<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Output;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Output\DockerEventsOutput;

/**
 * @internal
 *
 * @coversNothing
 */
class DockerEventsOutputTest extends TestCase
{
    public function testGetIterator()
    {
        $output = new DockerEventsOutput(self::startProcess(
            self::event('create', 1700000000000000001)."\n"
            .self::event('start', 1700000000000000002)
        ), false);

        $actions = [];
        foreach ($output->getIterator() as $event) {
            if ($event !== null) {
                $actions[] = $event->action->toString();
            }
        }

        $this->assertSame(['create', 'start'], $actions);
    }

    public function testGetIteratorYieldsNullWithoutEvent()
    {
        $process = new Process(['sleep', '30']);
        $process->start();
        $output = new DockerEventsOutput($process, true);

        $this->assertNull($output->getIterator()->current());
    }

    public function testGetIteratorRestartsFromLastEvent()
    {
        $output = new DockerEventsOutput(self::startProcess(
            self::event('create', 1700000000000000001)."\n"
            .self::event('start', 1700000000000000002)."\n"
        ), true);

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

    public function testGetIteratorThrowsInvalidValueException()
    {
        $output = new DockerEventsOutput(self::startProcess("invalid\n"), false);

        $this->expectException(InvalidValueException::class);

        foreach ($output->getIterator() as $event) {
        }
    }

    public function testReleaseStopsProcess()
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

    /**
     * @param string $action
     * @param int    $timeNano
     *
     * @return string
     */
    private static function event($action, $timeNano)
    {
        return json_encode([
            'Type' => 'container',
            'Action' => $action,
            'Actor' => ['ID' => 'abc', 'Attributes' => null],
            'scope' => 'local',
            'time' => (int) ($timeNano / 1000000000),
            'timeNano' => $timeNano,
        ]);
    }

    /**
     * @param string $output
     *
     * @return Process
     */
    private static function startProcess($output)
    {
        $process = new Process([PHP_BINARY, '-r', 'echo '.var_export($output, true).';']);
        $process->start();

        return $process;
    }
}
