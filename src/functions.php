<?php

namespace Testcontainers;

use Testcontainers\Exceptions\AssertException;

/**
 * Convert a string to kebab-case.
 *
 * @param string $str
 *
 * @return string
 */
function kebab($str)
{
    $s = preg_replace('/(?<!^)[A-Z]/', '-$0', $str);
    if ($s === null) {
        return $str;
    }

    return strtolower($s);
}

/**
 * Assert a type or invariant. Throws AssertException on failure.
 *
 * @param bool   $condition
 * @param string $message
 *
 * @throws AssertException
 *
 * @psalm-assert true $condition
 */
function ensure($condition, $message)
{
    if ($condition !== true) {
        throw new AssertException($message);
    }
}
