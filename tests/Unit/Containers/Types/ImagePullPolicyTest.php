<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Exceptions\InvalidFormatException;

final class ImagePullPolicyTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructAlways()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$ALWAYS);

        self::assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testConstructMissing()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$MISSING);

        self::assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testConstructNever()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$NEVER);

        self::assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testAlways()
    {
        $imagePullPolicy = ImagePullPolicy::ALWAYS();

        self::assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testMissing()
    {
        $imagePullPolicy = ImagePullPolicy::MISSING();

        self::assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testNever()
    {
        $imagePullPolicy = ImagePullPolicy::NEVER();

        self::assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithAlways()
    {
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$ALWAYS);

        self::assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithMissing()
    {
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$MISSING);

        self::assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithNever()
    {
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$NEVER);

        self::assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidPolicy()
    {
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('Invalid format: `"invalid"`, expects: `always`, `missing`, `never`');
        ImagePullPolicy::fromString('invalid');
    }

    /**
     * @return void
     */
    public function testIsAlways()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$ALWAYS);

        self::assertTrue($imagePullPolicy->isAlways());
    }

    /**
     * @return void
     */
    public function testIsMissing()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$MISSING);

        self::assertTrue($imagePullPolicy->isMissing());
    }

    /**
     * @return void
     */
    public function testIsNever()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$NEVER);

        self::assertTrue($imagePullPolicy->isNever());
    }
}
