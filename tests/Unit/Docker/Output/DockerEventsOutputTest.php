<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Output;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Output\DockerEventsOutput;

final class DockerEventsOutputTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetIterator()
    {
        $payload = '{"Type":"container","Action":"create","Actor":{"ID":"abc","Attributes":null},"scope":"local","time":1700000000,"timeNano":1700000000000000001}'."\n"
            .'{"Type":"container","Action":"start","Actor":{"ID":"abc","Attributes":null},"scope":"local","time":1700000000,"timeNano":1700000000000000002}';
        $process = new Process([PHP_BINARY, '-r', 'echo '.var_export($payload, true).';']);
        $process->start();
        $output = new DockerEventsOutput($process, false);

        $actions = [];
        foreach ($output->getIterator() as $event) {
            if ($event !== null) {
                $actions[] = $event->action->toString();
            }
        }

        $this->assertSame(['create', 'start'], $actions);
    }

    /**
     * @return void
     */
    public function testGetIteratorYieldsNullWithoutEvent()
    {
        $process = new Process(['sleep', '30']);
        $process->start();
        $output = new DockerEventsOutput($process, true);

        $this->assertNull($output->getIterator()->current());
    }

    /**
     * @return void
     */
    public function testGetIteratorRestartsFromLastEvent()
    {
        $payload = '{"Type":"container","Action":"create","Actor":{"ID":"abc","Attributes":null},"scope":"local","time":1700000000,"timeNano":1700000000000000001}'."\n"
            .'{"Type":"container","Action":"start","Actor":{"ID":"abc","Attributes":null},"scope":"local","time":1700000000,"timeNano":1700000000000000002}'."\n";
        $process = new Process([PHP_BINARY, '-r', 'echo '.var_export($payload, true).';']);
        $process->start();
        $output = new DockerEventsOutput($process, true);

        $actions = [];
        $deadline = time() + 10;
        foreach ($output->getIterator() as $event) {
            if ($event !== null) {
                $actions[] = $event->action->toString();
            }
            if (count($actions) === 3 || time() > $deadline) {
                break;
            }
        }

        $this->assertSame(['create', 'start', 'start'], $actions);
    }

    /**
     * @return void
     */
    public function testGetIteratorThrowsInvalidValueException()
    {
        $payload = "invalid\n";
        $process = new Process([PHP_BINARY, '-r', 'echo '.var_export($payload, true).';']);
        $process->start();
        $output = new DockerEventsOutput($process, false);

        $this->expectException(InvalidValueException::class);

        iterator_to_array($output->getIterator(), false);
    }

    /**
     * @return void
     */
    public function testDestructStopsProcess()
    {
        $process = new Process(['sleep', '30']);
        $process->start();
        $output = new DockerEventsOutput($process, true);
        $iterator = $output->getIterator();
        $iterator->current();

        $this->assertTrue($process->isRunning());

        unset($output, $iterator);

        $this->assertFalse($process->isRunning());
    }

}
