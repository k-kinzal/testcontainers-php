<?php

namespace Testcontainers;

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
    if (null === $s) {
        return $str;
    }

    return strtolower($s);
}
