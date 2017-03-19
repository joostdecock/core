<?php

namespace Freesewing\Tests;

class RequestTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Request');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['data'],
            ['info'],
        ];
    }

    /**
     * Tests the constructor
     */
    public function testConstructor()
    {
        $request = new \Freesewing\Request();
        $info = $request->getInfo();
        
        $this->assertEquals(isset($info['time']), true);
        unset($info['time']);
    }
    
    /**
     * Tests the getData method
     */
    public function testGetData()
    {
        $data = ['foo' => 'bar', 'moo' => 'gnar'];

        $request = new \Freesewing\Request($data);
        
        $this->assertEquals($request->getData('foo'), 'bar');
        $this->assertEquals($request->getData('moo'), 'gnar');
        $this->assertEquals($request->getData('zoo'), null);
    }
    
    /**
     * Tests the getAllData method
     */
    public function testGetAllData()
    {
        $data = ['foo' => 'bar', 'moo' => 'gnar'];

        $request = new \Freesewing\Request($data);
        
        $this->assertEquals($request->getAllData(), $data);
    }
}
