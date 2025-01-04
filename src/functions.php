<?php

namespace Testcontainers;

/**
 * Convert a string to kebab-case.
 *
 * @param string $string
 * @return string
 */
function kebab($string)
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
}
