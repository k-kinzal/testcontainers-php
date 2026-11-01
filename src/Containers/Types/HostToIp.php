<?php

namespace Testcontainers\Containers\Types;

use LogicException;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Ensure;
use Testcontainers\Utility\Stringable;

/**
 * Represents a host-to-IP mapping.
 *
 * @property string $host The host name.
 * @property string $ip   The IP address.
 * @api
 */
class HostToIp implements Stringable
{
    use Ensure;

    /**
     * The host name.
     *
     * @var string
     */
    private $host;

    /**
     * The IP address.
     *
     * @var string
     */
    private $ip;

    /**
     * Create a HostToIp instance.
     *
     * @param string $host
     * @param string $ip
     * @psalm-param mixed $host
     * @psalm-param mixed $ip
     */
    public function __construct($host, $ip)
    {
        self::ensureNonEmptyString($host);
        self::ensureNonEmptyString($ip);

        $this->host = $host;
        $this->ip = $ip;
    }

    public function __toString()
    {
        return $this->host.':'.$this->ip;
    }

    /**
     * Get the value of a property.
     *
     * @param string $name the name of the property
     *
     * @return mixed the value of the property
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new LogicException('HostToIp::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Create a HostToIp object from a string.
     *
     * @param string $v the host-to-IP mapping
     * @psalm-param mixed $v
     *
     * @throws InvalidFormatException if the format is invalid
     *
     * @return HostToIp the HostToIp object
     */
    public static function fromString($v)
    {
        self::ensureMatches($v, '/^[^:]+:[^:]+$/D', 'host:ip');

        $parts = explode(':', $v);

        return new self($parts[0], $parts[1]);
    }

    /**
     * Create a HostToIp object from an array.
     *
     * @param array{
     *     hostname: string,
     *     ipAddress: string
     * } $v The host-to-IP mapping
     * @psalm-param mixed $v
     *
     * @return HostToIp the HostToIp object
     */
    public static function fromArray($v)
    {
        self::ensureArray($v);
        self::ensureArrayHasKey($v, 'hostname');
        self::ensureArrayHasKey($v, 'ipAddress');

        return new self($v['hostname'], $v['ipAddress']);
    }

    /**
     * Get the host-to-IP mapping.
     *
     * @return string the host-to-IP mapping
     */
    public function toString()
    {
        return (string) $this;
    }
}
