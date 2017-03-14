<?php

namespace Freesewing\Tests;

use \Freesewing\Output;
require_once __DIR__.'/assets/testFunctions.php';

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
       Output::reset();
    }

    public function tearDown()
    {
       Output::reset();
    }

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Response');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['body'],
            ['format'],
            ['headers']
        ];
    }

    /**
     * Tests the addCacheHeaders method
     *
     * Needs to run in a seperate process, because we can't 
     * send headers after starting the output.
     * Also requires the xdebug extension
     */
    public function testaddCacheHeaders()
    {
        $response = new \Freesewing\Response();
        $request = new \Freesewing\Request(['cache'  => 'please']);
        $response->addCacheHeaders($request);

        $response->send();
        $this->assertContains('Cache-Control: public, max-age=15552000', Output::$headers);
        
        $response = new \Freesewing\Response();
        $request = new \Freesewing\Request();
        $response->addCacheHeaders($request);
        
        $response->send();
        $this->assertContains('Cache-Control: public, no-cache', Output::$headers);
    }

    /**
     * Tests the setBody and getBody methods
     */
    public function testSetBodyGetBody()
    {
        $response = new \Freesewing\Response();
        $response->setBody('This is expected.');

        $this->assertEquals($response->getBody(), 'This is expected.');
    }

    /**
     * Tests the setFormat and getformat methods
     */
    public function testSetFormatGetFormat()
    {
        $response = new \Freesewing\Response();

        $response->setFormat('json');
        $this->assertEquals($response->getFormat(), 'json');

        $response->setFormat('HTML');
        $this->assertEquals($response->getFormat(), 'html');
    }
    
    /**
     * Tests the send method with JSON output
     */
    public function testSendJson()
    {
        $response = new \Freesewing\Response();

        $response->setFormat('json');
        $response->setBody(['foo' => 'bar', 'gnoo' => 'jar']);
        $response->send();

        $this->assertEquals(Output::$body,'{"foo":"bar","gnoo":"jar"}');
    }
    
    /**
     * Tests the send method with default output
     */
    public function testSendDefault()
    {
        $response = new \Freesewing\Response();

        $response->setFormat('raw');
        $response->setBody('foobar gnoojar');
        $response->send();

        $this->assertEquals(Output::$body,'foobar gnoojar');
    }
}
