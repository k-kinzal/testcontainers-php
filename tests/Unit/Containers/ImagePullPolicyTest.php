<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * @internal
 *
 * @coversNothing
 */
final class ImagePullPolicyTest extends TestCase
{
    /**
     * @return void
     */
    public function testImagePullPolicyAlways()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$ALWAYS);

        $this->assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testImagePullPolicyMissing()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$MISSING);

        $this->assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testImagePullPolicyNever()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$NEVER);

        $this->assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testAlways()
    {
        $imagePullPolicy = ImagePullPolicy::ALWAYS();

        $this->assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testMissing()
    {
        $imagePullPolicy = ImagePullPolicy::MISSING();

        $this->assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testNever()
    {
        $imagePullPolicy = ImagePullPolicy::NEVER();

        $this->assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithAlways()
    {
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$ALWAYS);

        $this->assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithMissing()
    {
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$MISSING);

        $this->assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithNever()
    {
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$NEVER);

        $this->assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
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

        $this->assertTrue($imagePullPolicy->isAlways());
    }

    /**
     * @return void
     */
    public function testIsMissing()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$MISSING);

        $this->assertTrue($imagePullPolicy->isMissing());
    }

    /**
     * @return void
     */
    public function testIsNever()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$NEVER);

        $this->assertTrue($imagePullPolicy->isNever());
    }
}
