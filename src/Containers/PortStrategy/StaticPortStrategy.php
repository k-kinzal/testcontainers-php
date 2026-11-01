<?php

namespace Testcontainers\Containers\PortStrategy;

use Testcontainers\Utility\Ensure;

/**
 * A port strategy that returns a fixed port.
 * When a port conflict occurs, it fails (does not retry).
 * @api
 */
class StaticPortStrategy implements PortStrategy
{
    use Ensure;

    /**
     * The port to return.
     *
     * @var int
     */
    private $port;

    /**
     * @param int $port the port to return
     * @psalm-param mixed $port
     */
    public function __construct($port)
    {
        self::ensureIntOrDigitString($port);

        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535');
        }
        $port = (int) $port;

        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function conflictBehavior()
    {
        return ConflictBehavior::FAIL();
    }
}
