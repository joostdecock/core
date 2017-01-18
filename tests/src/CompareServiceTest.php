<?php

namespace Freesewing\Tests;

use \Freesewing\Services\CompareService;

class CompareServiceTest extends \PHPUnit\Framework\TestCase
{
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
     *
     * @runInSeparateProcess
     */
    public function testRunBasic()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request([
            'service' => 'compare', 
            'pattern' => 'AaronAshirt',
            'samplerGroup' => 'realMen'
        ]));
        $context->configure();

        $service = new CompareService();
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
        $context->setRequest(new \Freesewing\Request([
            'service' => 'compare'
        ]));
        $context->configure();

        $service = new CompareService();
        ob_start();
        $service->run($context);
        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'Location: /docs/');
        ob_end_clean();
    }
}
