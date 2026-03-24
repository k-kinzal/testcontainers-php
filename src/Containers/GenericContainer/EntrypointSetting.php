<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * EntrypointSetting is a trait that provides the ability to set the entrypoint for a container.
 *
 * Two formats are supported:
 * 1. static variable `$ENTRYPOINT`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $ENTRYPOINT = '/bin/sh';
 * }
 * </code>
 *
 * 2. method `withEntrypoint`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withEntrypoint('/bin/sh');
 * </code>
 */
trait EntrypointSetting
{
    /**
     * Define the default entrypoint to be used for the container.
     *
     * @var null|string
     */
    protected static $ENTRYPOINT;

    /**
     * The entrypoint to be used for the container.
     *
     * @var null|string
     */
    private $entrypoint;

    /**
     * Set the entrypoint for the container, overriding the default entrypoint defined in the Docker image.
     *
     * @param string $entrypoint the entrypoint to use (e.g., '/bin/sh', '/usr/local/bin/custom-entrypoint.sh')
     *
     * @return self
     */
    public function withEntrypoint($entrypoint)
    {
        $this->entrypoint = $entrypoint;

        return $this;
    }

    /**
     * Retrieve the entrypoint for the container.
     *
     * @return null|string
     */
    protected function entrypoint()
    {
        if (static::$ENTRYPOINT) {
            return static::$ENTRYPOINT;
        }

        return $this->entrypoint;
    }
}
