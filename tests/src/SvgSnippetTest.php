<?php

namespace Freesewing\Tests;

class SvgSnippetTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\SvgSnippet');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['anchor'],
            ['reference'],
            ['description'],
            ['attributes'],
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
        $object = new \Freesewing\SvgSnippet();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $object->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        $anchor = new \Freesewing\Point();
        $anchor->setX(52);
        $anchor->setY(69);
        return [
            ['Anchor', $anchor],
            ['Reference', 69],
            ['Description', 'The description'],
            ['Attributes', ['class' => 'seam-allowance', 'dx' => 12]],
        ];
    }
}
