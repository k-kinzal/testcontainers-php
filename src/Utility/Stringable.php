<?php

namespace Testcontainers\Utility;

/**
 * Stringable interface for classes that can be converted to a string.
 */
interface Stringable
{
    /**
     * @return string
     */
    public function __toString();
}
