<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Types\Event;
use Testcontainers\Utility\LineIterator;
use Testcontainers\Utility\Ensure;

/**
 * Represents the output of a Docker `events` command executed via Symfony Process.
 *
 * The process is stopped when this output is released.
 * @api
 */
class DockerEventsOutput
{
    use Ensure;

    /**
     * The running Docker events process.
     *
     * @var Process
     */
    private $process;

    /**
     * Whether the process is restarted when it terminates.
     *
     * @var bool
     */
    private $reconnect;

    /**
     * The time of the last event in nanoseconds.
     *
     * @var null|int
     */
    private $timeNano;

    /**
     * @param Process $process   the Docker events process
     * @param bool    $reconnect whether the process is restarted when it terminates
     * @psalm-param mixed $process
     * @psalm-param mixed $reconnect
     */
    public function __construct($process, $reconnect)
    {
        self::ensureInstanceOf($process, Process::class);

        self::ensureBool($reconnect);

        $this->process = $process;
        $this->reconnect = $reconnect;
    }

    public function __destruct()
    {
        $this->process->stop(0);
    }

    /**
     * Returns an iterator for the Docker events output without blocking.
     *
     * `null` is yielded when no event is available at the moment.
     * When the process terminates, it is restarted and the events older than the last event are skipped.
     *
     * @throws InvalidValueException if the output contains a line that is not a valid event
     *
     * @return \Generator<null|Event> an iterator to traverse the events
     */
    public function getIterator()
    {
        while (true) {
            $lines = new LineIterator($this->process);
            foreach ($lines->getIterator(Process::ITER_NON_BLOCKING | Process::ITER_SKIP_ERR) as $line) {
                if ($line === null) {
                    yield null;

                    continue;
                }
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $event = Event::deserialize($line);
                if ($event->timeNano !== null) {
                    if ($this->timeNano !== null && $event->timeNano < $this->timeNano) {
                        continue;
                    }
                    $this->timeNano = $event->timeNano;
                }

                yield $event;
            }
            if (!$this->reconnect) {
                return;
            }

            yield null;
            $this->process = $this->process->restart();
        }
    }
}
