<?php

namespace Freesewing\Tests;

class TestPatternTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Patterns\Core\Pattern');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['height'],
            ['messages'],
            ['options'],
            ['isPaperless'],
            ['partMargin'],
            ['parts'],
            ['replacements'],
            ['units'],
            ['width'],
        ];
    }

    public function providerGettersReturnWhatSettersSet()
    {
        return [
            ['Units', ['in' => 'metric', 'out' => 'imperial']],
            ['Width', 5269],
            ['Height', 6952],
            ['PartMargin', 13],
        ];
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $key The key in the array
     * @param $value The value to set and test for
     *
     * @dataProvider providerGettersReturnWhatSettersSetForArrays
     */
    public function testGettersReturnWhatSettersSetForArrays($methodSuffix, $key, $value)
    {
        $object = new \Freesewing\Patterns\Tests\TestPattern();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($key,$value);
        $this->assertEquals($value, $object->{$getMethod}($key));
    }

    public function providerGettersReturnWhatSettersSetForArrays()
    {
        return [
            ['Option', 'testOption', 123],
            ['Value', 'testValue', 312],
        ];
    }

    /** 
     * Tests the getOption and o methods
     */
    public function testGetOption()
    {
        $p = new \Freesewing\Patterns\Tests\TestPattern();
        $p->setOption('test', 'something');
        $this->assertEquals('something', $p->getOption('test'));
        $this->assertEquals('something', $p->o('test'));
        
    }

    /** 
     * Tests messaging and debug
     */
    public function testMessagesAndDebug()
    {
        $p = new \Freesewing\Patterns\Tests\TestPattern();
        $this->assertEquals($p->getMessages(), false);
        $this->assertEquals($p->getDebug(), false);
        $p->msg('This is a test message');
        $p->dbg('This is a debug test');
        $this->assertEquals($p->getMessages(), "This is a test message");
        $this->assertEquals($p->getDebug(), "This is a debug test");
        $p->msg('This is a another test message');
        $p->dbg('This is a another debug test');
        $this->assertEquals($p->getMessages(), "This is a test message\nThis is a another test message");
        $this->assertEquals($p->getDebug(), "This is a debug test\nThis is a another debug test");
    }

    /** 
     * Tests draft and sample methods
     */
    public function testDraftAndSample()
    {
        $p1 = new \Freesewing\Patterns\Tests\TestPattern();
        $p2 = new \Freesewing\Patterns\Tests\TestPattern();
        $model = new \Freesewing\Model();
        $p2->sample($model);
        $this->assertEquals($p1,$p2);
        $p2->draft($model);
        $this->assertEquals($p1,$p2);
    }
}
