<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PullPolicySetting;
use Testcontainers\Containers\Types\ImagePullPolicy;
use UnexpectedValueException;

final class PullPolicySettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasPullPolicySettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(PullPolicySetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticPullPolicy()
    {
        $container = new PullPolicySettingWithStaticPullPolicyContainer('alpine:latest');
        $instance = $container->start();

        $pullPolicy = $instance->getImagePullPolicy();

        $this->assertNotNull($pullPolicy);
        $this->assertSame(ImagePullPolicy::$ALWAYS, $pullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testStartWithImagePullPolicy()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withImagePullPolicy(ImagePullPolicy::MISSING())
        ;
        $instance = $container->start();

        $pullPolicy = $instance->getImagePullPolicy();

        $this->assertNotNull($pullPolicy);
        $this->assertSame(ImagePullPolicy::$MISSING, $pullPolicy->toString());
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidPullPolicy()
    {
        $container = new PullPolicySettingWithInvalidPullPolicyContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class PullPolicySettingWithStaticPullPolicyContainer extends GenericContainer
{
    protected static $PULL_POLICY = 'always';
}

final class PullPolicySettingWithInvalidPullPolicyContainer extends GenericContainer
{
    protected static $PULL_POLICY = [];
}
