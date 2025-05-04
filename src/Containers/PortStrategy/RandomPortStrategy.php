<?php

namespace Testcontainers\Containers\PortStrategy;

use RuntimeException;

/**
 * A port strategy that selects a random port from the ephemeral port range.
 */
class RandomPortStrategy implements PortStrategy
{
    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        for ($i = 0; $i < 65535 - 49152; ++$i) {
            $port = mt_rand(49152, 65535);
            if (false === @fsockopen('localhost', $port)) {
                return $port;
            }
        }

        throw new RuntimeException('Could not find an available port');
    }

    /**
     * {@inheritDoc}
     */
    public function conflictBehavior()
    {
        return ConflictBehavior::RETRY();
    }
}
