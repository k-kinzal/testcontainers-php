<?php

namespace Testcontainers\Containers\Types;

use LogicException;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * Represents a host-to-IP mapping.
 *
 * @property-read string $host The host name.
 * @property-read string $ip The IP address.
 */
class HostToIp
{
    /**
     * The host name.
     * @var string
     */
    private $host;

    /**
     * The IP address.
     * @var string
     */
    private $ip;

    /**
     * @param string $host
     * @param string $ip
     */
    public function __construct($host, $ip)
    {
        assert(is_string($host));
        assert(is_string($ip));

        $this->host = $host;
        $this->ip = $ip;
    }

    /**
     * Create a HostToIp object from a string.
     *
     * @param string $v The host-to-IP mapping.
     * @return HostToIp The HostToIp object.
     *
     * @throws InvalidFormatException If the format is invalid.
     */
    public static function fromString($v)
    {
        $parts = explode(':', $v);
        if (count($parts) !== 2) {
            throw new InvalidFormatException($v, 'host:ip');
        }
        return new self($parts[0], $parts[1]);
    }

    /**
     * Create a HostToIp object from an array.
     *
     * @param array{
     *     hostname: string,
     *     ipAddress: string
     * } $v The host-to-IP mapping.
     * @return HostToIp The HostToIp object.
     *
     * @throws InvalidFormatException If the format is invalid.
     */
    public static function fromArray($v)
    {
        if (!isset($v['hostname']) || !isset($v['ipAddress'])) {
            throw new InvalidFormatException($v, '["hostname": string, "ipAddress": string]');
        }
        if (!is_string($v['hostname']) || !is_string($v['ipAddress'])) {
            throw new InvalidFormatException($v, '["hostname": string, "ipAddress": string]');
        }
        return new self($v['hostname'], $v['ipAddress']);
    }

    /**
     * Get the host-to-IP mapping.
     *
     * @return string The host-to-IP mapping.
     */
    public function toString()
    {
        return (string) $this;
    }

    /**
     * Get the host name.
     *
     * @return string The host name.
     */
    public function __toString()
    {
        return $this->host . ':' . $this->ip;
    }

    /**
     * Get the value of a property.
     *
     * @param string $name The name of the property.
     * @return mixed The value of the property.
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new LogicException('HostToIp::' . $name . ' does not exist');
        }
        return $this->$name;
    }
}
