<?php

namespace Freesewing\Tests;

class ResponseTest extends \PHPUnit\Framework\TestCase
{

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
            ['headers'],
            ['cacheTime'],
        ];
    }

    /**
     * Tests the addCacheHeaders method
     *
     * Needs to run in a seperate process, because we can't 
     * send headers after starting the output.
     * Also requires the xdebug extension
     *
     * @runInSeparateProcess
     * @requires extension xdebug
     */
    public function testaddCacheHeaders()
    {
        $response = new \Freesewing\Response();
        $request = new \Freesewing\Request(['cache'  => 'please']);
        $response->addCacheHeaders($request);

        ob_start();
        $response->send();
        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'Cache-Control: public, max-age=15552000');
        ob_clean();
        
        $response = new \Freesewing\Response();
        $request = new \Freesewing\Request();
        var_dump($request->getData('cache'));
        $response->addCacheHeaders($request);
        
        $response->send();
        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'Cache-Control: public, no-cache');
        ob_end_clean();
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

        $this->expectOutputString('{"foo":"bar","gnoo":"jar"}');
        $response->send();
    }
    
    /**
     * Tests the send method with default output
     */
    public function testSendDefault()
    {
        $response = new \Freesewing\Response();

        $response->setFormat('raw');
        $response->setBody('foobar gnoojar');

        $this->expectOutputString('foobar gnoojar');
        $response->send();
    }
}
