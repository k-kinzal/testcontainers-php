<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * PullPolicySetting is a trait that provides the ability to set the image pull policy for a container.
 *
 * Two formats are supported:
 * 1. static variable `$PULL_POLICY`:
 *
 * <code>
 *     class YourContainer extends GenericContainer
 *     {
 *         protected static $PULL_POLICY = 'always';
 *     }
 * </code>
 *
 * 2. method `withImagePullPolicy`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *         ->withImagePullPolicy(ImagePullPolicy::ALWAYS());
 * </code>
 */
trait PullPolicySetting
{
    /**
     * Define the default image pull policy to be used for the container.
     *
     * @var null|string
     */
    protected static $PULL_POLICY;

    /**
     * The image pull policy to be used for the container.
     *
     * @var null|ImagePullPolicy
     */
    private $pullPolicy;

    /**
     * Set the image pull policy of the container.
     *
     * @param ImagePullPolicy $policy the image pull policy to set
     *
     * @return self
     */
    public function withImagePullPolicy($policy)
    {
        $this->pullPolicy = $policy;

        return $this;
    }

    /**
     * Set the image pull policy of the container. (Alias for `withImagePullPolicy`).
     *
     * @param ImagePullPolicy $policy the image pull policy to set
     *
     * @return self
     */
    public function withPullPolicy($policy)
    {
        return $this->withImagePullPolicy($policy);
    }

    /**
     * Retrieve the image pull policy for the container.
     *
     * This method returns the image pull policy that should be used for the container.
     * If a specific image pull policy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default image pull policy from the provider.
     *
     * @throws InvalidFormatException if the image pull policy is not valid
     *
     * @return null|ImagePullPolicy the image pull policy to be used, or null if none is set
     */
    protected function pullPolicy()
    {
        if (static::$PULL_POLICY) {
            return ImagePullPolicy::fromString(static::$PULL_POLICY);
        }

        return $this->pullPolicy;
    }
}
