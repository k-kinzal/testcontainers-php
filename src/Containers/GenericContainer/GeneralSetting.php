<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * GeneralSetting is a trait that provides the ability to set general container options.
 *
 * Two formats are supported:
 * 1. static variable `$IMAGE` and `$NAME`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $IMAGE = 'image';
 *
 *     protected static $NAME = 'your-container';
 * }
 * </code>
 *
 * 2. method `withName`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withName('your-container');
 * </code>
 */
trait GeneralSetting
{
    /**
     * Define the default image to be used for the container.
     *
     * @var null|string
     */
    protected static $IMAGE;

    /**
     * The commands to be executed in the container.
     *
     * @var null|string|string[]
     */
    protected static $COMMANDS;

    /**
     * Define the default name to be used for the container.
     *
     * @var null|string
     */
    protected static $NAME;

    /**
     * The image to be used for the container.
     *
     * @var string
     */
    private $image;

    /**
     * The commands to be executed in the container.
     *
     * @var string[]
     */
    private $commands = [];

    /**
     * The name to be used for the container.
     *
     * @var null|string
     */
    private $name;

    public function withCommand($cmd)
    {
        $this->commands = [$cmd];

        return $this;
    }

    public function withCommands($commandParts)
    {
        $this->commands = $commandParts;

        return $this;
    }

    /**
     * Set the name for this container, similar to the `--name <name>` option on the Docker CLI.
     *
     * @param string $name the name to set
     *
     * @return self
     */
    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retrieve the image to be used for the container.
     *
     * @return string
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * Retrieve the command to be executed in the container.
     *
     * This method returns the command that should be executed in the container.
     * If a specific command is set, it will return that. Otherwise, it will
     * attempt to retrieve the default command from the provider.
     *
     * @return null|string|string[]
     */
    protected function commands()
    {
        if (static::$COMMANDS) {
            return static::$COMMANDS;
        }
        if ($this->commands) {
            return $this->commands;
        }

        return null;
    }

    /**
     * Retrieve the command to be executed.
     *
     * @return null|string
     */
    protected function command()
    {
        $commands = $this->commands();
        if (is_string($commands)) {
            $commands = [$commands];
        }
        if (is_array($commands) && count($commands) > 0) {
            return $commands[0];
        }

        return null;
    }

    /**
     * Retrieve the arguments to be passed to the command.
     *
     * @return string[]
     */
    protected function args()
    {
        $commands = $this->commands();
        if (is_string($commands)) {
            $commands = [$commands];
        }
        if (is_array($commands) && count($commands) > 1) {
            return array_slice($commands, 1);
        }

        return [];
    }

    /**
     * Retrieve the name to be used for the container.
     *
     * @return null|string
     */
    protected function name()
    {
        if (static::$NAME) {
            return static::$NAME;
        }
        if ($this->name) {
            return $this->name;
        }

        return null;
    }
}
