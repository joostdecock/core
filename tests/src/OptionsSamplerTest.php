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
            'acrossBack' => 450,
            'bicepsCircumference' => 335,
            'centerBackNeckToWaist' => 480,
            'chestCircumference' => 1080,
            'naturalWaistToHip' => 120,
            'neckCircumference' => 420,
            'shoulderLength' => 160,
            'shoulderSlope' => 40,
            'hipsCircumference' => 950,
        ];
        
        $sampler = new \Freesewing\OptionsSampler();
        $sampler->setPattern(new \Freesewing\Patterns\AaronAshirt());

        $this->assertEquals($sampler->loadModelMeasurements(), $measurements);
    }
}
