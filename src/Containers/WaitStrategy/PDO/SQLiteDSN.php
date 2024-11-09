<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

class SQLiteDSN implements DSN
{
    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toString()
    {
        // TODO: support file path
        return 'sqlite::memory:';
    }

    public function __toString()
    {
        return $this->toString();
    }
}
