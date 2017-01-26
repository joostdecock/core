<?php

namespace Freesewing\Tests;

use \Freesewing\Services\SampleService;
use \Freesewing\Output;
require_once __DIR__.'/assets/testFunctions.php';

class SampleServiceTest extends \PHPUnit\Framework\TestCase
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
     * Tests the getServiceName method
     */
    public function testGetServiceName()
    {
        $service = new SampleService();
        $this->assertEquals($service->getServiceName(),'sample');
    }

    /**
     * Tests the run method for option sampling
     */
    public function testOptionSamplingMeasure()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'sample', 
            'mode' => 'options', 
            'option' => 'measureOption', 
            'steps' => 3, 
            'pattern' => 'TestPattern'
        ]));
        $context->configure();

        $service = new SampleService();
        $service->run($context);

        $this->assertContains('Content-Type: image/svg+xml', Output::$headers);
        $this->assertEquals(substr(Output::$body,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
    }

    /**
     * Tests the run method for option sampling
     */
    public function testOptionSamplingPercent()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'sample', 
            'mode' => 'options', 
            'option' => 'percentOption', 
            'steps' => 30, 
            'pattern' => 'TestPattern'
        ]));
        $context->configure();

        $service = new SampleService();
        $service->run($context);

        $this->assertContains('Content-Type: image/svg+xml', Output::$headers);
        $this->assertEquals(substr(Output::$body,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
    }

    /**
     * Tests the run method for option sampling
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is not an option in the pattern configuration
     */
    public function testOptionSamplingNonExisting()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'sample', 
            'mode' => 'options', 
            'option' => 'thisDoesNotExist', 
            'steps' => 30, 
            'pattern' => 'TestPattern'
        ]));
        $context->configure();

        $service = new SampleService();
        $service->run($context);
    }

    /**
     * Tests the run method for measurements sampling
     */
    public function testMeasurementsSampling()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'sample', 
            'mode' => 'measurements', 
            'samplerGroup' => 'realMen', 
            'pattern' => 'TestPattern'
        ]));
        $context->configure();

        $service = new SampleService();
        $service->run($context);

        $this->assertContains('Content-Type: image/svg+xml', Output::$headers);
        $this->assertEquals(substr(Output::$body,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
    }
    /**
     * Tests the run method for invalid request
     */
    public function testInvalidRequest()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'sample']));
        $context->configure();

        $service = new SampleService();
        $service->run($context);
        
        $this->assertContains('Location: /docs/', Output::$headers);
    }
}
