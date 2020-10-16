<?php


namespace BR\Toolkit\Tests\Misc\Enum;

use BR\Toolkit\Misc\Enum\BaseEnum;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * Class TestEnumClass
 * @package Tests\Unit\Enum
 */
abstract class TestEnumClass extends BaseEnum
{
    public const TEST_0 = '0';
    public const TEST_A = 1.0;
    public const TEST_B = 'a';
    public const TEST_C = null;
    public const TEST_D = 0.01;
    public const TEST_F = ['a' => 'A'];
    public const TEST_G = false;
}


class EnumTest extends TestCase
{
    /**
     * @test
     */
    public function BasicEnumValidateTest()
    {
        $this->assertTrue(TestEnumClass::validate(1));
        $this->assertTrue(TestEnumClass::validate('a'));
        $this->assertTrue(TestEnumClass::validate(null));
        $this->assertTrue(TestEnumClass::validate(false));
        $this->assertTrue(TestEnumClass::validate(0));
        $this->assertTrue(TestEnumClass::validate(0.01));
        $this->assertTrue(TestEnumClass::validate(['a' => 'A']));
        $this->assertTrue(TestEnumClass::validate('1'));

        $this->assertFalse(TestEnumClass::validate(['B' => 'A']));
        $this->assertFalse(TestEnumClass::validate(['a' => 'B']));
        $this->assertFalse(TestEnumClass::validate([]));
        $this->assertFalse(TestEnumClass::validate(''));
    }

    /**
     * @test
     */
    public function BasicEnumValuesTest()
    {
        $this->assertSame([
            '0',1.0,'a',null,0.01,['a' => 'A'], false
        ], TestEnumClass::getValues());

        $this->assertNotSame([
            '',1.0,'a',null,0.01,['A'],true
        ], TestEnumClass::getValues());
    }

    /**
     *
     */
    public function testSanitize()
    {
        $this->assertTrue(is_float(TestEnumClass::sanitize('1', '0')));
        $this->assertSame(1.0, TestEnumClass::sanitize(1, '0'));
        $this->assertSame('0', TestEnumClass::sanitize('1.0', '0'));
        $this->assertSame(1.0, TestEnumClass::sanitize('1', '0'));
        $this->assertSame('0', TestEnumClass::sanitize('5', '0'));
        $this->assertSame(TestEnumClass::TEST_F, TestEnumClass::sanitize(TestEnumClass::TEST_F, '0'));
    }
}