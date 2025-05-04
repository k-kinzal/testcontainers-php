<?php

namespace Testcontainers\Containers\PortStrategy;

/**
 * A port strategy that returns a fixed port.
 * When a port conflict occurs, it fails (does not retry).
 */
class StaticPortStrategy implements PortStrategy
{
    /**
     * The port to return.
     *
     * @var int
     */
    private $port;

    /**
     * @param int $port the port to return
     */
    public function __construct($port)
    {
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
