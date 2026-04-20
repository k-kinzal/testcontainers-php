<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\Types\HostToIp;
use Testcontainers\Exceptions\InvalidFormatException;

use function Testcontainers\ensure;

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
     */
    protected static $EXTRA_HOSTS = [];

    /**
     * Define the default extra hosts to be used for the container. (Alias for $EXTRA_HOSTS).
     *
     * @var array{
     *             hostname: string,
     *             ipAddress: string
     *             }[]|string[]
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
     *
     * @return self
     *
     * @see Container::withExtraHost()
     */
    public function withExtraHost($hostname, $ipAddress = null)
    {
        ensure(
            is_array($hostname) || is_string($hostname) || $hostname instanceof HostToIp,
            '$hostname must be array|HostToIp|string'
        );
        ensure($ipAddress === null || is_string($ipAddress), '$ipAddress must be null|string');

        if (($hostname instanceof HostToIp) && $ipAddress === null) {
            $hostToIp = $hostname;
        } elseif (is_array($hostname) && $ipAddress === null) {
            $hostToIp = HostToIp::fromArray($hostname);
        } elseif (is_string($hostname) && is_string($ipAddress)) {
            $hostToIp = new HostToIp($hostname, $ipAddress);
        } else {
            $hostnameJson = json_encode($hostname);
            $ipAddressJson = json_encode($ipAddress);
            throw new \InvalidArgumentException(
                'Invalid arguments: withExtraHost(`'
                .($hostnameJson !== false ? $hostnameJson : 'null').'`, `'
                .($ipAddressJson !== false ? $ipAddressJson : 'null').'`)'
            );
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
     *
     * @return self
     *
     * @see Container::withExtraHosts()
     */
    public function withExtraHosts($extraHosts)
    {
        ensure(is_array($extraHosts), '$extraHosts must be array');

        $this->extraHosts = [];
        foreach ($extraHosts as $extraHost) {
            $this->withExtraHost($extraHost);
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
        ensure(is_array(static::$EXTRA_HOSTS), 'static::$EXTRA_HOSTS must be array');
        ensure(is_array(static::$HOSTS), 'static::$HOSTS must be array');

        $static = static::$EXTRA_HOSTS;
        if (empty($static)) {
            $static = static::$HOSTS;
        }
        if (count($static) > 0) {
            $extraHosts = [];
            foreach ($static as $extraHost) {
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
