<?php

namespace Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use Testcontainers\Utility\Ensure;

class EnsureTest extends TestCase
{
    public function testRejectsInvalidValuesWithoutCoercion()
    {
        $cases = [
            ['ensureString', 1],
            ['ensureNonEmptyString', ''],
            ['ensureNullableString', false],
            ['ensureBool', 'false'],
            ['ensureBool', 1],
            ['ensureNullableBool', 'false'],
            ['ensureNullableBoolOrString', 1],
            ['ensureNullableStringOrStringList', ['ok', false]],
            ['ensureInt', '1'],
            ['ensureInt', 1.5],
            ['ensureIntOrDigitString', true],
            ['ensureIntOrDigitString', 1.5],
            ['ensureIntOrDigitString', '1e3'],
            ['ensureDigitString', ''],
            ['ensureDigitString', '80oops'],
            ['ensureNonNegativeNumber', -1],
            ['ensureNonNegativeNumber', INF],
            ['ensureNonNegativeNumber', NAN],
            ['ensureArray', new \stdClass()],
            ['ensureStringList', ['ok', false]],
            ['ensureStringMap', ['key' => []]],
            ['ensureStringMap', [0 => 'value']],
        ];
        foreach ($cases as $case) {
            try {
                EnsureHarness::check($case[0], $case[1]);
                $this->fail($case[0].' accepted invalid input');
            } catch (\InvalidArgumentException $e) {
                $this->assertNotSame('', $e->getMessage());
            }
        }
    }

    public function testOptionalMessageIsPreservedByNestedChecks()
    {
        foreach ([['ensureBool', 'false'], ['ensureStringList', ['ok', false]], ['ensureDigitString', 'invalid']] as $case) {
            try {
                EnsureHarness::check($case[0], $case[1], 'Custom validation message');
                $this->fail('Invalid input was accepted');
            } catch (\InvalidArgumentException $e) {
                $this->assertSame('Custom validation message', $e->getMessage());
            }
        }
    }

    public function testPreservesValidValuesAndNormalizesLists()
    {
        $this->assertSame(false, EnsureHarness::check('ensureBool', false));
        $this->assertNull(EnsureHarness::check('ensureNullableBool', null));
        $this->assertSame('echo', EnsureHarness::check('ensureNullableStringOrStringList', 'echo'));
        $this->assertNull(EnsureHarness::check('ensureNullableStringOrStringList', null));
        $this->assertSame(['a', 'b'], EnsureHarness::check('ensureNullableStringOrStringList', [2 => 'a', 8 => 'b']));
        $this->assertNull(EnsureHarness::check('ensureNullableString', null));
        $this->assertSame(0.5, EnsureHarness::check('ensureNonNegativeNumber', 0.5));
        $this->assertSame(['a', 'b'], EnsureHarness::check('ensureStringList', [2 => 'a', 8 => 'b']));
        $this->assertSame(['key' => ''], EnsureHarness::check('ensureStringMap', ['key' => '']));
        $this->assertSame('080', EnsureHarness::check('ensureIntOrDigitString', '080'));
        $this->assertSame(80, EnsureHarness::check('ensureIntOrDigitString', 80));
    }
}

class EnsureHarness
{
    use Ensure;

    public static function check($method, $value, $message = null)
    {
        return self::$method($value, $message);
    }
}
