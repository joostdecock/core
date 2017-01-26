<?php

namespace Freesewing\Tests;

use \Freesewing\Services\DraftService;
use \Freesewing\Output;
require_once __DIR__.'/assets/testFunctions.php';

class DraftServiceTest extends \PHPUnit\Framework\TestCase
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
        $service = new DraftService();
        $this->assertEquals($service->getServiceName(),'draft');
    }

    /**
     * Tests the run method for basic draft
     */
    public function testRunBasic()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'TestPattern']));
        $context->configure();

        $service = new DraftService();
        $service->run($context);

        $this->assertContains('Content-Type: image/svg+xml', Output::$headers);
        $this->assertEquals(substr(Output::$body,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
    }

    /**
     * Tests the run method for basic draft with viewbox
     */
    public function testRunBasicWithViewbox()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'TestPattern', 'viewbox' => '10,10,200,200', 'theme' => 'paperless']));
        $context->configure();

        $service = new DraftService();
        $service->run($context);

        $this->assertContains('Content-Type: image/svg+xml', Output::$headers);
        $this->assertEquals(substr(Output::$body,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
    }

    /**
     * Tests the run method for invalid request
     */
    public function testRunPatternInfo()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft']));
        $context->configure();

        $service = new DraftService();
        $service->run($context);
        
        $this->assertContains('Location: /docs/', Output::$headers);
    }
}
