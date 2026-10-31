<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Testcontainers\Docker\Command\EventsCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerEventsOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Docker\Types\Event;

/**
 * @internal
 *
 * @coversNothing
 */
class EventsCommandTest extends TestCase
{
    public function testHasEventsCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(EventsCommand::class, $uses);
    }

    public function testEvents()
    {
        $client = new DockerClient();

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('alpine:latest', 'sh', ['-c', 'exit 3'], [
            'detach' => true,
            'rm' => true,
        ]);
        $containerId = $output->getContainerId();

        $eventsOutput = $client->events([
            'filter' => ['container='.$containerId],
            'since' => '0',
            'until' => (string) (time() + 3),
        ]);

        $this->assertInstanceOf(DockerEventsOutput::class, $eventsOutput);

        $actions = [];
        foreach ($eventsOutput->getIterator() as $event) {
            if ($event === null) {
                continue;
            }
            $this->assertInstanceOf(Event::class, $event);
            $this->assertSame((string) $containerId, $event->actor->id);
            $actions[] = $event->action->toString();
            if ($event->action->isDie()) {
                $this->assertSame('3', $event->actor->getAttribute('exitCode'));
            }
        }

        $this->assertSame(['create', 'start', 'die'], array_values(array_intersect($actions, ['create', 'start', 'die'])));
    }

    public function testEventsWithTimeout()
    {
        $client = new DockerClient();

        $eventsOutput = $client->events([
            'filter' => ['container=not-exist'],
            'timeout' => 1,
        ]);

        try {
            foreach ($eventsOutput->getIterator() as $event) {
            }
            $this->fail('Expected ProcessTimedOutException');
        } catch (ProcessTimedOutException $e) {
            $this->assertSame(1.0, $e->getExceededTimeout());
        }

        $this->assertSame(60, $client->getTimeout());
    }
}
