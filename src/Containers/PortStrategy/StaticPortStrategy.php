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
     * Create a StaticPortStrategy instance.
     *
     * @param int $port the port to return
     * @psalm-param mixed $port
     */
    public function __construct($port)
    {
        self::ensureIntOrDigitStringRange($port, 1, 65535);

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
