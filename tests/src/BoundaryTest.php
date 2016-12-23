<?php

namespace Freesewing\Tests;

class BoundaryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Boundary');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['topLeft'],
            ['bottomRight'],
            ['width'],
            ['height'],
        ];
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerGettersReturnWhatSettersSet
     */
    public function testGettersReturnWhatSettersSet($methodSuffix, $expectedResult)
    {
        $object = new \Freesewing\Boundary();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $object->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        $point = new \Freesewing\Point();
        $point->setX(52);
        $point->setY(69);
        return [
            ['TopLeft', $point],
            ['BottomRight', $point],
        ];
    }

    public function testUpdateDimensions() {
        $topLeft = new \Freesewing\Point();
        $topLeft->setX(52);
        $topLeft->setY(69);

        $bottomRight = new \Freesewing\Point();
        $bottomRight->setX(69);
        $bottomRight->setY(5252);

        $object = new \Freesewing\Boundary();
        $object->setTopLeft($topLeft);
        $object->setBottomRight($bottomRight);

        $this->assertEquals(17, $object->width);
        $this->assertEquals(5183, $object->height);


    }
}
