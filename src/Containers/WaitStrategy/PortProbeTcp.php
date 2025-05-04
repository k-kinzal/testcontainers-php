<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * PortProbeTcp checks the availability of a specific port on a given host using `fsockopen`.
 */
class PortProbeTcp implements PortProbe
{
    /**
     * {@inheritDoc}
     */
    public function available($host, $port)
    {
        $fp = @fsockopen($host, $port);
        if (false === $fp) {
            return false;
        }
        fclose($fp);

        return true;
    }
}
