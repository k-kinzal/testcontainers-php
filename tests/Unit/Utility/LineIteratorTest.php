<?php

namespace Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Utility\LineIterator;

/**
 * @internal
 *
 * @coversNothing
 */
class LineIteratorTest extends TestCase
{
    public function testGetIterator()
    {
        $process = new Process([PHP_BINARY, '-r', 'echo "a\nb"; flush(); usleep(100000); echo "c\nd";']);
        $process->start();
        $lines = new LineIterator($process);

        $actual = [];
        foreach ($lines->getIterator() as $type => $line) {
            $actual[] = [$type, $line];
        }

        $this->assertSame([[Process::OUT, 'a'], [Process::OUT, 'bc'], [Process::OUT, 'd']], $actual);
    }

    public function testGetIteratorSeparatesOutputAndErrorOutput()
    {
        $process = new Process([PHP_BINARY, '-r', 'fwrite(STDOUT, "a"); fwrite(STDERR, "b\n"); fflush(STDOUT); usleep(100000); fwrite(STDOUT, "c\n");']);
        $process->start();
        $lines = new LineIterator($process);

        $actual = [];
        foreach ($lines->getIterator() as $type => $line) {
            $actual[] = [$type, $line];
        }

        $this->assertSame([[Process::ERR, 'b'], [Process::OUT, 'ac']], $actual);
    }

    public function testGetIteratorWithSkipErr()
    {
        $process = new Process([PHP_BINARY, '-r', 'fwrite(STDERR, "a\n"); fwrite(STDOUT, "b\n");']);
        $process->start();
        $lines = new LineIterator($process);

        $actual = [];
        foreach ($lines->getIterator(Process::ITER_SKIP_ERR) as $line) {
            $actual[] = $line;
        }

        $this->assertSame(['b'], $actual);
    }

    public function testGetIteratorYieldsNullWithoutCompleteLineInNonBlockingMode()
    {
        $process = new Process(['sleep', '30']);
        $process->start();
        $lines = new LineIterator($process);

        $this->assertNull($lines->getIterator(Process::ITER_NON_BLOCKING)->current());

        $process->stop(0);
    }
}
