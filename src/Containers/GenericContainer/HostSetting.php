<?php

namespace Testcontainers\Containers\GenericContainer;

use InvalidArgumentException;
use Testcontainers\Containers\Types\HostToIp;
use Testcontainers\Exceptions\InvalidFormatException;

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
     * @var string[]|array{
     *      hostname: string,
     *      ipAddress: string
     *  }[]
     */
    protected static $EXTRA_HOSTS = [];

    /**
     * Define the default extra hosts to be used for the container. (Alias for $EXTRA_HOSTS)
     *
     * @var string[]|array{
     *      hostname: string,
     *      ipAddress: string
     *  }[]
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
     * @param HostToIp|string|array $hostname The hostname to add.
     * @param null|string $ipAddress The IP address associated with the hostname.
     * @return self
     *
     * @throws InvalidArgumentException If the arguments are invalid.
     *
     * @see Container::withExtraHost()
     */
    public function withExtraHost($hostname, $ipAddress = null)
    {
        if (($hostname instanceof HostToIp) && $ipAddress === null) {
            $hostToIp = $hostname;
        } elseif (is_array($hostname) && $ipAddress === null) {
            $hostToIp = HostToIp::fromArray($hostname);
        } elseif (is_string($hostname) && is_string($ipAddress)) {
            $hostToIp = new HostToIp($hostname, $ipAddress);
        } else {
            throw new InvalidArgumentException(
                'Invalid arguments: withExtraHost(`' . json_encode($hostname) . '`, `' . json_encode($ipAddress) . '`)'
            );
        }

        $this->extraHosts[] = $hostToIp;

        return $this;
    }

    /**
     * Add multiple extra host entries to be passed to the container.
     *
     * @param HostToIp[]|string[]|array{
     *      hostname: string,
     *      ipAddress: string
     *  }[] $extraHosts The extra hosts to add.
     * @return self
     *
     * @throws InvalidArgumentException If the arguments are invalid.
     *
     * @see Container::withExtraHosts()
     */
    public function withExtraHosts($extraHosts)
    {
        $this->extraHosts = [];
        foreach ($extraHosts as $extraHost) {
            $this->withExtraHost($extraHost);
        }

        return $this;
    }

    /**
     * Retrieve the extra hosts to be used for the container.
     *
     * @return HostToIp[]
     *
     * @throws InvalidFormatException If the format is invalid.
     */
    protected function extraHosts()
    {
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
