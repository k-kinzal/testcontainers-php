<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * PortProbeTcp checks the availability of a specific port on a given host using `fsockopen`.
 * A TCP self-connect is not treated as available.
 */
class PortProbeTcp implements PortProbe
{
    public function available($host, $port)
    {
        $fp = @fsockopen($host, $port);
        if ($fp === false) {
            return false;
        }
        $selfConnected = stream_socket_get_name($fp, false) === stream_socket_get_name($fp, true);
        fclose($fp);

        return !$selfConnected;
    }
}
