<?php

namespace Testcontainers;

use Testcontainers\Exceptions\EnsureException;

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
 * Assert a type or invariant. Throws EnsureException on failure.
 *
 * @param bool   $condition
 * @param string $message
 *
 * @throws EnsureException
 *
 * @psalm-assert true $condition
 */
function ensure($condition, $message)
{
    if ($condition !== true) {
        throw new EnsureException($message);
    }
}
