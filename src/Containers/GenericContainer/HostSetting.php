<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\Types\HostToIp;
use Testcontainers\Exceptions\InvalidFormatException;
use UnexpectedValueException;

/**
 * HostSetting is a trait that provides the ability to add extra hosts to a container.
 *
 * Two formats are supported:
 * 1. static variable `$EXTRA_HOSTS` or `$HOSTS`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *      protected static $EXTRA_HOSTS = [
 *         'hostname1:ipAddress1',
 *         'hostname2:ipAddress2',
 *     ];
 * }
 * </code>
 *
 * 2. method `withExtraHost` or `withExtraHosts`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *    ->withExtraHost('hostname3', 'ipAddress3')
 *    ->withExtraHost(['hostname4:ipAddress4', 'hostname5:ipAddress5'])
 *    ->withExtraHost(HostToIp::formString('hostname6:ipAddress6'));
 * </code>
 */
trait HostSetting
{
    /**
     * Define the default extra hosts to be used for the container.
     *
     * @var array{
     *             hostname: string,
     *             ipAddress: string
     *             }[]|string[]
     * @psalm-var mixed
     */
    protected static $EXTRA_HOSTS = [];

    /**
     * Define the default extra hosts to be used for the container. (Alias for $EXTRA_HOSTS).
     *
     * @var array{
     *             hostname: string,
     *             ipAddress: string
     *             }[]|string[]
     * @psalm-var mixed
     */
    protected static $HOSTS = [];

    /**
     * The extra hosts to be used for the container.
     *
     * @var HostToIp[]
     */
    private $extraHosts = [];

    /**
     * Add an extra host entry to be passed to the container.
     *
     * @param array{
     *      hostname: string,
     *      ipAddress: string
     *  }|HostToIp|string $hostname The hostname to add
     * @param null|string $ipAddress the IP address associated with the hostname
     * @psalm-param mixed $hostname
     * @psalm-param mixed $ipAddress
     *
     * @throws UnexpectedValueException if the arguments are invalid
     *
     * @return self
     *
     * @see Container::withExtraHost()
     */
    public function withExtraHost($hostname, $ipAddress = null)
    {
        self::ensureStringOrArrayOrInstanceOf($hostname, HostToIp::class);
        self::ensureNullableString($ipAddress);

        if ($hostname instanceof HostToIp) {
            self::ensureNull($ipAddress);

            $hostToIp = $hostname;
        } elseif (is_array($hostname)) {
            self::ensureNull($ipAddress);

            $hostToIp = HostToIp::fromArray($hostname);
        } else {
            self::ensureNonEmptyString($ipAddress);

            $hostToIp = new HostToIp($hostname, $ipAddress);
        }

        $this->extraHosts[] = $hostToIp;

        return $this;
    }

    /**
     * Add multiple extra host entries to be passed to the container.
     *
     * @param array{
     *      hostname: string,
     *      ipAddress: string
     *  }[]|HostToIp[]|string[] $extraHosts The extra hosts to add
     * @psalm-param mixed $extraHosts
     *
     * @throws UnexpectedValueException if the arguments are invalid
     *
     * @return self
     *
     * @see Container::withExtraHosts()
     */
    public function withExtraHosts($extraHosts)
    {
        self::ensureArray($extraHosts);

        $this->extraHosts = [];
        foreach (array_keys($extraHosts) as $key) {
            $this->withExtraHost($extraHosts[$key]);
        }

        return $this;
    }

    /**
     * Retrieve the extra hosts to be used for the container.
     *
     * @throws InvalidFormatException if the format is invalid
     *
     * @return HostToIp[]
     */
    protected function extraHosts()
    {
        $static = static::$EXTRA_HOSTS;
        self::ensureNullableArray($static);

        if ($static === null || $static === []) {
            $fallback = static::$HOSTS;
            self::ensureNullableArray($fallback);

            $static = $fallback;
        }
        if ($static !== null && count($static) > 0) {
            $extraHosts = [];
            foreach (array_keys($static) as $key) {
                self::ensureStringOrArray($static[$key]);

                $extraHost = $static[$key];

                if (is_string($extraHost)) {
                    $extraHosts[] = HostToIp::fromString($extraHost);
                } else {
                    $extraHosts[] = HostToIp::fromArray($extraHost);
                }
            }

            return $extraHosts;
        }
        if ($this->extraHosts) {
            return $this->extraHosts;
        }

        return [];
    }
}
