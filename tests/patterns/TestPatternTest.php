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
        $this->assertClassHasAttribute($attribute, '\Freesewing\patterns\Pattern');
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

    /**
     * Tests the getTranslationsDir method
     */
    public function testGetTranslationsDir()
    {
        $pattern = new \Freesewing\Patterns\TestPattern();
        $dir = dirname(dirname(__DIR__)).'/patterns/TestPattern/translations';
        $this->assertEquals($pattern->getTranslationsDir(), $dir);
    }

    /**
     * Tests the getSamplerModelFile method
     */
    public function testGetSamplerModelFile()
    {
        $pattern = new \Freesewing\Patterns\TestPattern();
        $file = dirname(dirname(__DIR__)).'/patterns/TestPattern/sampler/models.yml';
        $this->assertEquals($pattern->getSamplerModelFile(), $file);
    }

    /**
     * Tests the getSamplerModels method
     */
    public function testGetSamplerModels()
    {
        $pattern = new \Freesewing\Patterns\TestPattern();
        $data = [
            'default' => [
                'group' => 'someGroup',
            ],
            'groups' => [
                'someGroup' => ['someModel'],
                'anotherGroup' => ['anotherModel'],
            ],
            'measurements' => [
                'someMeasurement' => [
                    'someModel' => 123,
                    'anotherModel' => 321,
                ],
            ],
        ];
        $this->assertEquals($pattern->getSamplerModels(), $data);
    }
    
    /** 
     * Tests the unit method
     */
    public function testUnit()
    {
        $p = new \Freesewing\Patterns\TestPattern();
        $p->setUnits(['out' => 'metric']);
        $this->assertEquals($p->unit(20),'2cm');
        $this->assertEquals($p->unit(12.3456),'1.23cm');
        $p->setUnits(['out' => 'imperial']);
        $this->assertEquals($p->unit(25.4),'1"');
        $this->assertEquals($p->unit(12.3456),'0.49"');
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $value The value to set and test for
     *
     * @dataProvider providerGettersReturnWhatSettersSet
     */
    public function testGettersReturnWhatSettersSet($methodSuffix, $value)
    {
        $object = new \Freesewing\Patterns\TestPattern();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($value);
        $this->assertEquals($value, $object->{$getMethod}());
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
        $object = new \Freesewing\Patterns\TestPattern();
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
        $p = new \Freesewing\Patterns\TestPattern();
        $p->setOption('test', 'something');
        $this->assertEquals('something', $p->getOption('test'));
        $this->assertEquals('something', $p->o('test'));
        
    }

    /** 
     * Tests messaging and debug
     */
    public function testMessagesAndDebug()
    {
        $p = new \Freesewing\Patterns\TestPattern();
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
        $p1 = new \Freesewing\Patterns\TestPattern();
        $p2 = new \Freesewing\Patterns\TestPattern();
        $model = new \Freesewing\Model();
        $p2->sample($model);
        $this->assertEquals($p1,$p2);
        $p2->draft($model);
        $this->assertEquals($p1,$p2);
    }
}
