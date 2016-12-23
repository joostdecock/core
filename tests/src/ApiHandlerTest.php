<?php

namespace Freesewing\Tests;

class ApiHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\ApiHandler');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['model'],
            ['svgDocument'],
            ['context'],
            ['requestData'],
        ];
    }

    public function testConstructorTest()
    {
       $api = new \Freesewing\ApiHandler(null);
       $context = $api->getContext();
       $this->assertEquals('Channel', $context['channel']);
       $this->assertEquals('Pattern', $context['pattern']);
       $this->assertEquals('Theme', $context['theme']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandle()
    {
       $api = new \Freesewing\ApiHandler(null);
       $expectedResult = file_get_contents(__DIR__.'/ApiHandler.output.svg');
       $api->handle();
       $this->expectOutputString($expectedResult);
    }

    /**
     * @runInSeparateProcess
     */
    public function testHandleWithNonExistingChannel()
    {
       $data = [
           'channel' => 'ThisChannelDoesNotExist',
           'pattern' => 'Sample',
           'theme' => 'Sample',
           'model' => 'joost',
       ];

       $api = new \Freesewing\ApiHandler($data);
       $expectedResult = '{"error":"Bad Request","info":"Channel ThisChannelDoesNotExist not found"}';
       $api->handle();
       $this->expectOutputString($expectedResult);
    }

}
