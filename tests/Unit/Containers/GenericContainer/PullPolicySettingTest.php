<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\PullPolicySetting;
use Testcontainers\Containers\Types\ImagePullPolicy;

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

        $this->assertSame(ImagePullPolicy::$ALWAYS, $instance->getImagePullPolicy()->toString());
    }

    public function testStartWithImagePullPolicy()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withImagePullPolicy(ImagePullPolicy::MISSING());
        $instance = $container->start();

        $this->assertSame(ImagePullPolicy::$MISSING, $instance->getImagePullPolicy()->toString());
    }
}

class PullPolicySettingWithStaticPullPolicyContainer extends GenericContainer
{
    protected static $PULL_POLICY = 'always';
}
