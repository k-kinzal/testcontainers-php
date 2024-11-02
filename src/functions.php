<?php

namespace Testcontainers;

/**
 * Flatten a multi-dimensional array into a single level.
 *
 * @param array $array The array to flatten.
 * @return array The flattened array.
 */
function array_flatten($array)
{
    $result = [];
    foreach ($array as $item) {
        if (is_array($item)) {
            $result = array_merge($result, array_flatten($item));
        } else {
            $result[] = $item;
        }
    }
    return $result;
}
