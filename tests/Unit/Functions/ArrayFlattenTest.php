<?php

namespace Tests\Unit\Functions;

use PHPUnit\Framework\TestCase;

use function Testcontainers\array_flatten;

class ArrayFlattenTest extends TestCase
{
    public function testArrayFlatten()
    {
        $actual = array_flatten([1, [2, [3, 4], 5]]);
        $expected = [1, 2, 3, 4, 5];

        $this->assertEquals($expected, $actual);
    }

    public function testArrayFlattenWithAlreadyFlatArray()
    {
        $actual = array_flatten([1, 2, 3, 4, 5]);
        $expected = [1, 2, 3, 4, 5];

        $this->assertEquals($expected, $actual);
    }

    public function testArrayFlattenWithEmptyArray()
    {
        $actual = array_flatten([]);
        $expected = [];

        $this->assertEquals($expected, $actual);
    }
}
