<?php

namespace Testcontainers\Containers\WaitStrategy;

use function Testcontainers\ensure;

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
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($host), '$host must be string');
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_int($port), '$port must be int');

        $fp = @fsockopen($host, $port);
        if ($fp === false) {
            return false;
        }
        fclose($fp);

        return true;
    }
}
