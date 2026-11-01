<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PullPolicySetting;
use Testcontainers\Containers\Types\ImagePullPolicy;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class PullPolicySettingTest extends TestCase
{
    public function testHasPullPolicySettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(PullPolicySetting::class, $uses);
    }

    public function testStaticPullPolicy()
    {
        $container = new PullPolicySettingWithStaticPullPolicyContainer('alpine:latest');
        $instance = $container->start();

        $pullPolicy = $instance->getImagePullPolicy();

        $this->assertNotNull($pullPolicy);
        $this->assertSame(ImagePullPolicy::$ALWAYS, $pullPolicy->toString());
    }

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

    public function testStartRejectsInvalidPullPolicy()
    {
        $container = new PullPolicySettingWithInvalidPullPolicyContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class PullPolicySettingWithStaticPullPolicyContainer extends GenericContainer
{
    protected static $PULL_POLICY = 'always';
}

class PullPolicySettingWithInvalidPullPolicyContainer extends GenericContainer
{
    protected static $PULL_POLICY = [];
}
