<?php

namespace Freesewing\Tests;

class DimensionTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Dimension');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['label'],
            ['leaders'],
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
        $point = new \Freesewing\Dimension();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $point->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $point->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        $label = new \Freesewing\TextOnPath();
        $path = new \Freesewing\Path();
        $path->setPath('M 1 L 2 L 3 z');
        $label->setPath($path);
        $label->setText('foo bar');
        return [
            ['Label', $label],
            ['Leaders', [1,2,'foo']],
        ];
    }
    
    /*
     * Tests the getPath method
     */
    public function testGetPath()
    {
        $label = new \Freesewing\TextOnPath();
        $path = new \Freesewing\Path();
        $path->setPath('M 1 L 2 L 3 z');
        $label->setPath($path);
        $label->setText('foo bar');
        $dimension = new \Freesewing\Dimension();
        $dimension->setLabel($label);
        $this->assertEquals($dimension->getPath(), $path);
    }

    /*
     * Tests the addLeader method
     */
    public function testAddLeader()
    {
        $path = new \Freesewing\Path();
        $path->setPath('M 1 L 2 L 3 z');
        $dimension = new \Freesewing\Dimension();
        $dimension->addLeader($path);
        $this->assertEquals($dimension->getLeaders(), [$path]);
    }
}
