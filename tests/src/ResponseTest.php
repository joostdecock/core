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
            ['status'],
            ['body'],
            ['format'],
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
        $object = new \Freesewing\Response();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $object->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet() 
    {
        return [
            ['Status', 'bad_request'],
            ['Body', 'sorcha'],
            ['Format', 'raw'],
        ];
    }

    /**
     * @param string $status The response status property
     * @param string $format The response format property
     * @param string $body The response body property
     * @param $expectedResult Result to check for
     * @param $expectedResult Result to check for
     * @dataProvider providerSend
     * @runInSeparateProcess
     */
    public function testSend($status, $format, $body, $expectedResult)
    {
        $object = new \Freesewing\Response();
        $object->setStatus($status);
        $object->setFormat($format);
        $object->setBody($body);

        $this->expectOutputString($expectedResult); 
        $object->send();
    }

    public function providerSend()
    {
        return [
            ['ok', 'json', ['sorcha' => 'eyeballs', 'joost' => 'sewing machines'] , '{"sorcha":"eyeballs","joost":"sewing machines"}'],
            ['bad_request', 'raw', 'sorcha', 'sorcha'],
            ['unauthorized', 'raw', 'sorcha', 'sorcha'],
            ['forbidden', 'raw', 'sorcha', 'sorcha'],
            ['not_found', 'raw', 'sorcha', 'sorcha'],
            ['not_acceptable', 'raw', 'sorcha', 'sorcha'],
            ['api_down', 'raw', 'sorcha', 'sorcha'],
            ['server_error', 'raw', 'sorcha', 'sorcha'],
        ];
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is not a supported response status
     */
    public function testExceptionInvalidStatus()
    {
        $object = new \Freesewing\Response();
        $object->setStatus('sorcha');
    }
}
