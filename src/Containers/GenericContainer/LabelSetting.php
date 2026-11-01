<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * LabelSetting is a trait that provides the ability to set labels for a container.
 *
 * Two formats are supported:
 * 1. static variable `$LABELS`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $LABELS = [
 *         'com.example.label' => 'value',
 *     ];
 * }
 * </code>
 *
 * 2. method `withLabel`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withLabel('com.example.label', 'value');
 * </code>
 */
trait LabelSetting
{
    /**
     * Define the default labels to be used for the container.
     *
     * @var null|array<string, string>
     * @psalm-var mixed
     */
    protected static $LABELS;

    /**
     * The labels to be used for the container.
     *
     * @var array<string, string>
     */
    private $labels = [];

    /**
     * Add a label to the container.
     *
     * @param string $key   the name of the label
     * @param string $value the value of the label
     * @psalm-param mixed $key
     * @psalm-param mixed $value
     *
     * @return self
     */
    public function withLabel($key, $value)
    {
        self::ensureString($key);

        self::ensureString($value);

        $this->labels[$key] = $value;

        return $this;
    }

    /**
     * Adds multiple labels to the container.
     *
     * @param array<string, string> $labels an associative array where the key is the label name and the value is the label value
     * @psalm-param mixed $labels
     *
     * @return self
     */
    public function withLabels($labels)
    {
        self::ensureStringMap($labels);

        $this->labels = $labels;

        return $this;
    }

    /**
     * Retrieve the labels for the container.
     *
     * This method returns the labels that should be used for the container.
     * If specific labels are set, it will return those. Otherwise, it will
     * attempt to retrieve the default labels from the provider.
     *
     * @return array<string, string> The labels to be used
     */
    protected function labels()
    {
        if (static::$LABELS !== null) {
            return self::ensureStringMap(static::$LABELS);
        }
        if ($this->labels) {
            return $this->labels;
        }

        return [];
    }
}
