<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ImagePullPolicy;
use Testcontainers\Containers\InvalidImagePullPolicyException;

class ImagePullPolicyTest extends TestCase
{
    public function testImagePullPolicyAlways()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$ALWAYS);

        $this->assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    public function testImagePullPolicyMissing()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$MISSING);

        $this->assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    public function testImagePullPolicyNever()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$NEVER);

        $this->assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    public function testAlways()
    {
        $imagePullPolicy = ImagePullPolicy::ALWAYS();

        $this->assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    public function testMissing()
    {
        $imagePullPolicy = ImagePullPolicy::MISSING();

        $this->assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    public function testNever()
    {
        $imagePullPolicy = ImagePullPolicy::NEVER();

        $this->assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    public function testFromStringWithAlways()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$ALWAYS);

        $this->assertSame(ImagePullPolicy::$ALWAYS, $imagePullPolicy->toString());
    }

    public function testFromStringWithMissing()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$MISSING);

        $this->assertSame(ImagePullPolicy::$MISSING, $imagePullPolicy->toString());
    }

    public function testFromStringWithNever()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $imagePullPolicy = ImagePullPolicy::fromString(ImagePullPolicy::$NEVER);

        $this->assertSame(ImagePullPolicy::$NEVER, $imagePullPolicy->toString());
    }

    public function testFromStringWithInvalidPolicy()
    {
        $this->expectException(InvalidImagePullPolicyException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        ImagePullPolicy::fromString('invalid');
    }

    public function testIsAlways()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$ALWAYS);

        $this->assertTrue($imagePullPolicy->isAlways());
    }

    public function testIsMissing()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$MISSING);

        $this->assertTrue($imagePullPolicy->isMissing());
    }

    public function testIsNever()
    {
        $imagePullPolicy = new ImagePullPolicy(ImagePullPolicy::$NEVER);

        $this->assertTrue($imagePullPolicy->isNever());
    }
}
