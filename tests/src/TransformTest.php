<?php

namespace Freesewing\Tests;

class TransformTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Transform');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['x'],
            ['y'],
            ['angle'],
            ['type'],
        ];
    }

    public function providerAsSvgTransformOrParameterReturnsCorrectTransform()
    {
        return [
            ['translate', 52, 69, 30, ' translate(52 69) '],
            ['translate', 52, 69, null, ' translate(52 69) '],
            ['translate', 52, null, null, ' translate(52 ) '],
            ['scale', 52, 69, 30, ' scale(52 69) '],
            ['scale', 52, 69, null, ' scale(52 69) '],
            ['scale', 52, null, null, ' scale(52 ) '],
            ['rotate', 52, 69, 30, ' rotate(30 52 69) '],
        ];
    }

    /**
     * @param string $type Type of transform
     * @param float $x X coordinate of deplacement
     * @param float $y Y coordinate of deplacement
     * @param float $angle Angle for rotation
     * @param string $expectedResult The result we should get
     *
     * @dataProvider providerAsSvgParameterReturnsCorrectParameter
     */
    public function estAsSvgParameterReturnsCorrectParameter($type, $x, $y, $angle, $expectedResult)
    {
        $transform = new \Freesewing\Transform($type, $x, $y, $angle);
        $this->assertEquals($expectedResult, $transform->asSvgParameter());
    }

    public function roviderAsSvgParameterReturnsCorrectParameter()
    {
        return [
            ['translate', 52, 69, 30, ' translate(52 69) '],
            ['translate', 52, 69, null, ' translate(52 69) '],
            ['translate', 52, null, null, ' translate(52 ) '],
            ['scale', 52, 69, 30, ' scale(52 69) '],
            ['scale', 52, 69, null, ' scale(52 69) '],
            ['scale', 52, null, null, ' scale(52 ) '],
            ['rotate', 52, 69, 30, ' rotate(30 52 69) '],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing parameter for rotate transform
     */
    public function testExceptionRotateWithXButNoY()
    {
        $transform = new \Freesewing\Transform('rotate', 52, null, 30);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing parameter x
     */
    public function testExceptionRotateWithYButNoX()
    {
        $transform = new \Freesewing\Transform('rotate', null, 69, 30);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing parameter for rotate transform
     */
    public function testExceptionRotateWithNoAngle()
    {
        $transform = new \Freesewing\Transform('rotate', 52, 69);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage not a supported transform type
     */
    public function testExceptionInvalidTransformType()
    {
        $transform = new \Freesewing\Transform('sorcha', 10);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be numeric
     */
    public function testExceptionNonNumericParameter()
    {
        $transform = new \Freesewing\Transform('rotate', 52, 'sorcha', 30);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing parameter for rotate transform
     */
    public function testExceptionRotateTransformNoY()
    {
        $transform = new \Freesewing\Transform('rotate', 52, null, 30);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing parameter for rotate transform
     */
    public function testExceptionRotateTransformNoAngle()
    {
        $transform = new \Freesewing\Transform('rotate', 52, 69, null);
    }
}
