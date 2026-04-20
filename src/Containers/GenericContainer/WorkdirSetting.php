<?php

namespace Testcontainers\Containers\GenericContainer;

use function Testcontainers\ensure;

/**
 * WorkdirSetting is a trait that provides the ability to set the working directory for a container.
 *
 * Two formats are supported:
 * 1. static variable `$WORKDIR`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $WORKDIR = '/path/to/working/directory';
 * }
 * </code>
 *
 * 2. method `withWorkingDirectory`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withWorkingDirectory('/path/to/working/directory');
 * </code>
 */
trait WorkdirSetting
{
    /**
     * Define the default working directory to be used for the container.
     *
     * @var null|string
     */
    protected static $WORKDIR;

    /**
     * The working directory to be used for the container.
     *
     * @var null|string
     */
    private $workDir;

    /**
     * Set the working directory that the container should use on startup.
     *
     * @param string $workDir the path to the working directory inside the container
     *
     * @return self
     */
    public function withWorkingDirectory($workDir)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($workDir), '$workDir must be string');

        $this->workDir = $workDir;

        return $this;
    }

    /**
     * Set the working directory that the container should use on startup. Alias for `withWorkingDirectory`.
     *
     * @param string $workDir the path to the working directory inside the container
     *
     * @return self
     */
    public function withWorkDir($workDir)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($workDir), '$workDir must be string');

        return $this->withWorkingDirectory($workDir);
    }

    /**
     * Retrieve the working directory for the container.
     *
     * @return null|string
     */
    protected function workDir()
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(static::$WORKDIR === null || is_string(static::$WORKDIR), 'static::$WORKDIR must be null|string');

        if (static::$WORKDIR !== null) {
            return static::$WORKDIR;
        }

        return $this->workDir;
    }
}
