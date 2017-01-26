<?php

namespace Freesewing\Tests;

use \Freesewing\Services\CompareService;
use \Freesewing\Output;
require_once __DIR__.'/assets/testFunctions.php';

class CompareServiceTest extends \PHPUnit\Framework\TestCase
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
        $service = new CompareService();
        $this->assertEquals($service->getServiceName(),'compare');
    }

    /**
     * Tests the run method for basic draft
     */
    public function testRunBasic()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'compare', 
            'pattern' => 'TestPattern',
            'samplerGroup' => 'realMen'
        ]));
        $context->configure();

        $service = new CompareService();
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
        $context->setRequest(new \Freesewing\Request([
            'service' => 'compare'
        ]));
        $context->configure();

        $service = new CompareService();
        $service->run($context);
        
        $this->assertContains('Location: /docs/', Output::$headers);
    }
}
