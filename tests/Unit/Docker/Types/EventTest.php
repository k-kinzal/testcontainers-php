<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Types\Event;

final class EventTest extends TestCase
{
    /**
     * @return void
     */
    public function testFromArray()
    {
        $event = Event::fromArray(json_decode('{"Type":"container","Action":"die","Actor":{"ID":"713445482cc85d55d1df8adb30ea097a5884727c52d893c7e08978be3315909c","Attributes":{"execDuration":"0","exitCode":"2","image":"alpine","name":"clever_moser"}},"scope":"local","time":1793107853,"timeNano":1793107853987226709}', true));

        self::assertTrue($event->type->isContainer());
        self::assertTrue($event->action->isDie());
        self::assertSame('713445482cc85d55d1df8adb30ea097a5884727c52d893c7e08978be3315909c', $event->actor->id);
        self::assertSame('2', $event->actor->getAttribute('exitCode'));
        self::assertSame('alpine', $event->actor->getAttribute('image'));
        self::assertNull($event->actor->getAttribute('not-exist'));
        self::assertNotNull($event->scope);
        self::assertTrue($event->scope->isLocal());
        self::assertSame(1793107853, $event->time);
        self::assertSame(1793107853987226709, $event->timeNano);
        self::assertNull($event->status);
        self::assertNull($event->id);
        self::assertNull($event->from);
    }

    /**
     * @return void
     */
    public function testDeserialize()
    {
        $event = Event::deserialize('{"Type":"container","Action":"start","Actor":{"ID":"8188d93d8a27","Attributes":{"image":"alpine"}},"scope":"local","time":1,"timeNano":1000000000}');

        self::assertTrue($event->type->isContainer());
        self::assertTrue($event->action->isStart());
        self::assertSame('8188d93d8a27', $event->actor->id);
        self::assertSame(1000000000, $event->timeNano);
    }

    /**
     * @return void
     */
    public function testDeserializeWithInvalidJson()
    {
        $this->expectException(InvalidValueException::class);

        Event::deserialize('invalid');
    }

    /**
     * @return void
     */
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

        self::assertSame('start', $event->status);
        self::assertSame('8188d93d8a27', $event->id);
        self::assertSame('alpine', $event->from);
    }

    /**
     * @return void
     */
    public function testFromArrayWithOmittedProperties()
    {
        $event = Event::fromArray([
            'Type' => 'daemon',
            'Action' => 'reload',
            'Actor' => ['ID' => 'daemon-id', 'Attributes' => null],
        ]);

        self::assertTrue($event->type->isDaemon());
        self::assertTrue($event->action->isReload());
        self::assertSame([], $event->actor->attributes);
        self::assertNull($event->scope);
        self::assertNull($event->time);
        self::assertNull($event->timeNano);
    }

    /**
     * @return void
     */
    public function testFromArrayWithUnknownType()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'unknown',
            'Action' => 'start',
            'Actor' => ['ID' => '8188d93d8a27'],
        ]);
    }

    /**
     * @return void
     */
    public function testFromArrayWithUnknownAction()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'container',
            'Action' => 'unknown',
            'Actor' => ['ID' => '8188d93d8a27'],
        ]);
    }

    /**
     * @return void
     */
    public function testFromArrayWithoutActor()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'container',
            'Action' => 'start',
        ]);
    }

    /**
     * @return void
     */
    public function testFromArrayWithNonStringAttribute()
    {
        $this->expectException(InvalidValueException::class);

        Event::fromArray([
            'Type' => 'container',
            'Action' => 'start',
            'Actor' => ['ID' => '8188d93d8a27', 'Attributes' => ['exitCode' => 1]],
        ]);
    }

    /**
     * @return void
     */
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
