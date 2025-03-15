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
     * @var int
     */
    private $port;

    /**
     * @param int $port The port to return.
     */
    public function __construct($port)
    {
        $this->port = $port;
    }

    /**
     * @inheritDoc
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function conflictBehavior()
    {
        return ConflictBehavior::FAIL();
    }
}
