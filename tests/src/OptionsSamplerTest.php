<?php

namespace Freesewing\Tests;

class OptionsSamplerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests the loadModelMeasurements method
     */
    public function testLoadModelMeasurements()
    {
        $measurements = [
            'someMeasurement' => 200,
        ];
        
        $sampler = new \Freesewing\OptionsSampler();
        $sampler->setPattern(new \Freesewing\Patterns\Tests\TestPattern());

        $this->assertEquals($sampler->loadModelMeasurements(), $measurements);
    }
}
