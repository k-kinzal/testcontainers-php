<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Types\Event;

/**
 * @internal
 *
 * @coversNothing
 */
class EventTest extends TestCase
{
    public function testFromArray()
    {
        $event = Event::fromArray(json_decode('{"Type":"container","Action":"die","Actor":{"ID":"713445482cc85d55d1df8adb30ea097a5884727c52d893c7e08978be3315909c","Attributes":{"execDuration":"0","exitCode":"2","image":"alpine","name":"clever_moser"}},"scope":"local","time":1793107853,"timeNano":1793107853987226709}', true));

        $this->assertTrue($event->type->isContainer());
        $this->assertTrue($event->action->isDie());
        $this->assertSame('713445482cc85d55d1df8adb30ea097a5884727c52d893c7e08978be3315909c', $event->actor->id);
        $this->assertSame('2', $event->actor->getAttribute('exitCode'));
        $this->assertSame('alpine', $event->actor->getAttribute('image'));
        $this->assertNull($event->actor->getAttribute('not-exist'));
        $this->assertTrue($event->scope->isLocal());
        $this->assertSame(1793107853, $event->time);
        $this->assertSame(1793107853987226709, $event->timeNano);
        $this->assertNull($event->status);
        $this->assertNull($event->id);
        $this->assertNull($event->from);
    }

    public function testDeserialize()
    {
        $event = Event::deserialize('{"Type":"container","Action":"start","Actor":{"ID":"8188d93d8a27","Attributes":{"image":"alpine"}},"scope":"local","time":1,"timeNano":1000000000}');

        $this->assertTrue($event->type->isContainer());
        $this->assertTrue($event->action->isStart());
        $this->assertSame('8188d93d8a27', $event->actor->id);
        $this->assertSame(1000000000, $event->timeNano);
    }

    public function testDeserializeWithInvalidJson()
    {
        $this->expectException(InvalidValueException::class);

        Event::deserialize('invalid');
    }

    public function testFromArrayWithDeprecatedProperties()
    {
        $event = Event::fromArray([
            'status' => 'start',
            'id' => '8188d93d8a27',
            'from' => 'alpine',
            'Type' => 'container',
            'Action' => 'start',
            'Actor' => ['ID' => '8188d93d8a27', 'Attributes' => ['image' => 'alpine']],
            'scope' => 'local',
            'time' => 1,
            'timeNano' => 1000000000,
        ]);

        $this->assertSame('start', $event->status);
        $this->assertSame('8188d93d8a27', $event->id);
        $this->assertSame('alpine', $event->from);
    }

    public function testFromArrayWithOmittedProperties()
    {
        $event = Event::fromArray([
            'Type' => 'daemon',
            'Action' => 'reload',
            'Actor' => ['ID' => 'daemon-id', 'Attributes' => null],
        ]);

        $this->assertTrue($event->type->isDaemon());
        $this->assertTrue($event->action->isReload());
        $this->assertSame([], $event->actor->attributes);
        $this->assertNull($event->scope);
        $this->assertNull($event->time);
        $this->assertNull($event->timeNano);
    }

    public function testFromArrayWithUnknownType()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'unknown',
            'Action' => 'start',
            'Actor' => ['ID' => '8188d93d8a27'],
        ]);
    }

    public function testFromArrayWithUnknownAction()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'container',
            'Action' => 'unknown',
            'Actor' => ['ID' => '8188d93d8a27'],
        ]);
    }

    public function testFromArrayWithoutActor()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'container',
            'Action' => 'start',
        ]);
    }

    public function testFromArrayWithNonStringAttribute()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'container',
            'Action' => 'start',
            'Actor' => ['ID' => '8188d93d8a27', 'Attributes' => ['exitCode' => 1]],
        ]);
    }

    public function testFromArrayWithNonIntegerTime()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'container',
            'Action' => 'start',
            'Actor' => ['ID' => '8188d93d8a27'],
            'time' => '1',
        ]);
    }
}
