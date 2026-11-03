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

final class EventsCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testEventsIsProvidedByEventsCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);

        $this->assertContains(EventsCommand::class, $uses);
    }

    /**
     * @return void
     */
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
            'filter' => ['container='.$containerId->toString()],
            'since' => '0',
            'until' => (string) (time() + 3),
        ]);

        $this->assertInstanceOf(DockerEventsOutput::class, $eventsOutput);

        $events = array_values(array_filter(iterator_to_array($eventsOutput->getIterator(), false)));
        $containerIds = array_unique(array_map(function (Event $event) {
            return $event->actor->id;
        }, $events));
        $this->assertSame([$containerId->toString()], array_values($containerIds));
        $actions = array_map(function (Event $event) {
            return $event->action->toString();
        }, $events);
        $deaths = array_values(array_filter($events, function (Event $event) {
            return $event->action->isDie();
        }));
        $this->assertNotEmpty($deaths);
        $this->assertSame('3', $deaths[0]->actor->getAttribute('exitCode'));

        $this->assertSame(['create', 'start', 'die'], array_values(array_intersect($actions, ['create', 'start', 'die'])));
    }

    /**
     * @return void
     */
    public function testEventsWithTimeout()
    {
        $client = new DockerClient();

        $eventsOutput = $client->events([
            'filter' => ['container=not-exist'],
            'timeout' => 1,
        ]);

        try {
            iterator_to_array($eventsOutput->getIterator(), false);
            $this->fail('Expected ProcessTimedOutException');
        } catch (ProcessTimedOutException $e) {
            $this->assertSame(1.0, $e->getExceededTimeout());
        }

        $this->assertSame(60, $client->getTimeout());
    }
}
