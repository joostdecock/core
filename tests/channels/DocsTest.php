<?php

namespace Freesewing\Tests;

class DocsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Channels\Docs');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['options'],
        ];
    }

    public function testIsValidRequest()
    {
        $context = new \Freesewing\Context();
        $channel = new \Freesewing\Channels\Docs();
        $this->assertEquals($channel->isValidrequest($context), false);
        
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info', 'pattern' => 'TestPattern']));
        $context->setPattern(new \Freesewing\Patterns\TestPattern()); 
        $this->assertEquals($channel->isValidrequest($context), true);

    }

    public function testCleanUp()
    {
        $channel1 = new \Freesewing\Channels\Docs();
        $channel2 = new \Freesewing\Channels\Docs();
        $channel1->cleanUp();
        $this->assertEquals($channel1, $channel2);
    }

    public function testStandardizeModelMeasurements()
    {
        $channel = new \Freesewing\Channels\Docs();
        $pattern = new \Freesewing\Patterns\TestPattern();
        $request = new \Freesewing\Request(['someMeasurement' => 97]);
        $measurements = $channel->standardizeModelMeasurements($request,$pattern);
        $this->assertEquals($measurements['someMeasurement'], 970);
        unset($pattern->config);
        $this->assertEquals($channel->standardizeModelMeasurements($request,$pattern),null);
    }

    public function testStandardizePatternOptions()
    {
        $channel = new \Freesewing\Channels\Docs();
        $pattern = new \Freesewing\Patterns\TestPattern();
        $request = new \Freesewing\Request(['measureOption' => 5, 'chooseOneOption' => 2]);
        $options = $channel->standardizePatternOptions($request,$pattern);
        $this->assertEquals($options['measureOption'], 50);
        $this->assertEquals($options['chooseOneOption'], 2);

        $pattern = new \Freesewing\Patterns\TestPattern();
        $request = new \Freesewing\Request(['percentOption' => 95]);
        $options = $channel->standardizePatternOptions($request,$pattern);
        $this->assertEquals($options['percentOption'], 0.95);
        unset($pattern->config);
        $this->assertEquals($channel->standardizePatternOptions($request,$pattern),null);
    }
}
