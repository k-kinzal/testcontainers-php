<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * GeneralSetting is a trait that provides the ability to set the name for a container.
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
     * @var string|null
     */
    protected static $IMAGE;

    /**
     * The image to be used for the container.
     * @var string
     */
    private $image;

    /**
     * Define the default name to be used for the container.
     * @var string|null
     */
    protected static $NAME;

    /**
     * The name to be used for the container.
     * @var string|null
     */
    private $name;

    /**
     * Set the name for this container, similar to the `--name <name>` option on the Docker CLI.
     *
     * @param string $name The name to set.
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
     * Retrieve the name to be used for the container.
     *
     * @return string|null
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
