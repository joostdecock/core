<?php

namespace Freesewing\Tests;

use \Freesewing\Services\SampleService;

class SampleServiceTest extends \PHPUnit\Framework\TestCase
{
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
     *
     * @runInSeparateProcess
     */
    public function testOptionSampling()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'sample', 
            'mode' => 'options', 
            'option' => 'necklineDrop', 
            'steps' => 3, 
            'pattern' => 'AaronAshirt'
        ]));
        $context->configure();

        $service = new SampleService();
        ob_start();
        $service->run($context);
        $svg = ob_get_contents();

        $this->assertEquals(substr($svg,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
        ob_end_clean();
    }

    /**
     * Tests the run method for measurements sampling
     *
     * @runInSeparateProcess
     */
    public function testMeasurementsSampling()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'sample', 
            'mode' => 'measurements', 
            'samplerGroup' => 'realMen', 
            'pattern' => 'AaronAshirt'
        ]));
        $context->configure();

        $service = new SampleService();
        ob_start();
        $service->run($context);
        $svg = ob_get_contents();

        $this->assertEquals(substr($svg,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
        ob_end_clean();
    }
    /**
     * Tests the run method for invalid request
     *
     * @runInSeparateProcess
     */
    public function testInvalidRequest()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'sample']));
        $context->configure();

        $service = new SampleService();
        ob_start();
        $service->run($context);
        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'Location: /docs/');
        ob_end_clean();
    }
}
