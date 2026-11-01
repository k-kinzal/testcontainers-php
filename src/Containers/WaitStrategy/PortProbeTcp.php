<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Utility\Ensure;

/**
 * PortProbeTcp checks the availability of a specific port on a given host using `fsockopen`.
 * A TCP self-connect is not treated as available.
 */
class PortProbeTcp implements PortProbe
{
    use Ensure;

    public function available($host, $port)
    {
        self::ensureString($host);

        self::ensureIntOrDigitString($port);

        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535');
        }
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
