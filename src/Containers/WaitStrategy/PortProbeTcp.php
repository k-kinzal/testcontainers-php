<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Utility\Ensure;

/**
 * PortProbeTcp checks the availability of a specific port on a given host using `fsockopen`.
 * A TCP self-connect is not treated as available.
 * @api
 */
class PortProbeTcp implements PortProbe
{
    use Ensure;

    /**
     * Check whether the endpoint is available.
     *
     * @param string     $host the host
     * @param int|string $port the port
     * @psalm-param mixed $host
     * @psalm-param mixed $port
     */
    public function available($host, $port)
    {
        self::ensureString($host);
        self::ensureIntOrDigitStringRange($port, 1, 65535);

        $port = (int) $port;

        $fp = @fsockopen($host, $port);
        if ($fp === false) {
            return false;
        }
        $selfConnected = stream_socket_get_name($fp, false) === stream_socket_get_name($fp, true);
        fclose($fp);

        return !$selfConnected;
    }
}
