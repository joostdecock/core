<?php

namespace Freesewing\Tests;

class FreesewingTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValidRequest()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info']));
        $context->configure();
        $channel = new \Freesewing\Channels\Core\Freesewing();
        $this->assertEquals($channel->isValidrequest($context), true);
        
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info', 'pattern' => 'TestPattern']));
        $context->configure();
        $this->assertEquals($channel->isValidrequest($context), true);

    }

    public function testCleanUp()
    {
        $channel1 = new \Freesewing\Channels\Core\Freesewing();
        $channel2 = new \Freesewing\Channels\Core\Freesewing();
        $channel1->cleanUp();
        $this->assertEquals($channel1, $channel2);
    }

    public function testStandardizeModelMeasurements()
    {
        $channel = new \Freesewing\Channels\Core\Freesewing();
        $pattern = new \Freesewing\Patterns\Tests\TestPattern();
        $pattern->setUnits(['in' => 'metric']);
        $request = new \Freesewing\Request(['someMeasurement' => 97]);
        $measurements = $channel->standardizeModelMeasurements($request,$pattern);
        $this->assertEquals($measurements['someMeasurement'], 970);
        unset($pattern->config);
        $this->assertEquals($channel->standardizeModelMeasurements($request,$pattern),null);
    }

    public function testStandardizePatternOptions()
    {
        $channel = new \Freesewing\Channels\Core\Freesewing();
        $pattern = new \Freesewing\Patterns\Tests\TestPattern();
        $pattern->setUnits(['in' => 'metric']);
        $request = new \Freesewing\Request(['measureOption' => 4, 'chooseOneOption' => 2]);
        $options = $channel->standardizePatternOptions($request,$pattern);
        $this->assertEquals($options['measureOption'], 40);
        $this->assertEquals($options['chooseOneOption'], 2);

        $pattern = new \Freesewing\Patterns\Tests\TestPattern();
        $pattern->setUnits(['in' => 'metric']);
        $request = new \Freesewing\Request(['percentOption' => 95]);
        $options = $channel->standardizePatternOptions($request,$pattern);
        $this->assertEquals($options['percentOption'], 0.95);
        unset($pattern->config);
        $this->assertEquals($channel->standardizePatternOptions($request,$pattern),null);
    }
}
