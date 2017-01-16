<?php

namespace Freesewing\Tests;

class NoteTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Note');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['path'],
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
        $note = new \Freesewing\Note();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $note->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $note->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        $path = new \Freesewing\Path();
        $path->setPath('M 1 L 2 L 3 z');
        return [
            ['Path', $path],
        ];
    }
}
