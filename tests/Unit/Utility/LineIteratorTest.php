<?php

namespace Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use Testcontainers\Utility\LineIterator;

final class LineIteratorTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetIterator()
    {
        $input = new InputStream();
        $process = new Process([PHP_BINARY, '-r', 'echo "a\nb"; flush(); fgets(STDIN); echo "c\nd";']);
        $process->setInput($input);
        $process->start();
        $lines = new LineIterator($process);

        $iterator = $lines->getIterator();
        self::assertSame(Process::OUT, $iterator->key());
        self::assertSame('a', $iterator->current());
        $input->close();
        $iterator->next();
        self::assertSame(Process::OUT, $iterator->key());
        self::assertSame('bc', $iterator->current());
        $iterator->next();
        self::assertSame(Process::OUT, $iterator->key());
        self::assertSame('d', $iterator->current());
        $iterator->next();
        self::assertFalse($iterator->valid());

    }

    /**
     * @return void
     */
    public function testGetIteratorSeparatesOutputAndErrorOutput()
    {
        $input = new InputStream();
        $process = new Process([PHP_BINARY, '-r', 'fwrite(STDOUT, "a"); fwrite(STDERR, "b\n"); fflush(STDOUT); fgets(STDIN); fwrite(STDOUT, "c\n");']);
        $process->setInput($input);
        $process->start();
        $lines = new LineIterator($process);

        $iterator = $lines->getIterator();
        self::assertSame(Process::ERR, $iterator->key());
        self::assertSame('b', $iterator->current());
        $input->close();
        $iterator->next();
        self::assertSame(Process::OUT, $iterator->key());
        self::assertSame('ac', $iterator->current());
        $iterator->next();
        self::assertFalse($iterator->valid());

    }

    /**
     * @return void
     */
    public function testGetIteratorWithSkipErr()
    {
        $process = new Process([PHP_BINARY, '-r', 'fwrite(STDERR, "a\n"); fwrite(STDOUT, "b\n");']);
        $process->start();
        $lines = new LineIterator($process);

        $actual = iterator_to_array($lines->getIterator(Process::ITER_SKIP_ERR), false);

        self::assertSame(['b'], $actual);
    }

    /**
     * @return void
     */
    public function testGetIteratorYieldsNullWithoutCompleteLineInNonBlockingMode()
    {
        $process = new Process(['sleep', '30']);
        $process->start();
        $lines = new LineIterator($process);

        self::assertNull($lines->getIterator(Process::ITER_NON_BLOCKING)->current());

        $process->stop(0);
    }
}
