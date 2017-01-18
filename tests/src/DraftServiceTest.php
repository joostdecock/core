<?php

namespace Freesewing\Tests;

use \Freesewing\Services\DraftService;

class DraftServiceTest extends \PHPUnit\Framework\TestCase
{
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
     *
     * @runInSeparateProcess
     */
    public function testRunBasic()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'AaronAshirt']));
        $context->configure();

        $service = new DraftService();
        ob_start();
        $service->run($context);
        $svg = ob_get_contents();

        $this->assertEquals(substr($svg,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
        ob_end_clean();
    }

    /**
     * Tests the run method for basic draft with viewbox
     *
     * @runInSeparateProcess
     */
    public function testRunBasicWithViewbox()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'AaronAshirt', 'viewbox' => '10,10,200,200']));
        $context->configure();

        $service = new DraftService();
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
    public function testRunPatternInfo()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft']));
        $context->configure();

        $service = new DraftService();
        ob_start();
        $service->run($context);
        $headers = xdebug_get_headers();
        $this->assertEquals($headers[0], 'Location: /docs/');
        ob_end_clean();
    }
}
