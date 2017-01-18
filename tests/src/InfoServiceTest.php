<?php

namespace Freesewing\Tests;

use \Freesewing\Services\InfoService;

class InfoServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the getServiceName method
     */
    public function testGetServiceName()
    {
        $service = new InfoService();
        $this->assertEquals($service->getServiceName(),'info');
    }

    /**
     * Tests the run method for base info service
     */
    public function testRunBasicInfo()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info']));
        $context->configure();

        $service = new InfoService();
        ob_start();
        $service->run($context);
        $json = json_decode(ob_get_clean(),1);
        
        $this->assertEquals(is_array($json), true);
        $this->assertEquals(is_array($json['services']), true);
        $this->assertEquals(is_array($json['patterns']), true);
        $this->assertEquals(is_array($json['themes']), true);
        $this->assertEquals(is_array($json['channels']), true);
    }

    /**
     * Tests the run method for pattern info
     */
    public function testRunPatternInfo()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info', 'pattern' => 'AaronAshirt']));
        $context->configure();

        $service = new InfoService();
        ob_start();
        $service->run($context);
        $json = json_decode(ob_get_clean(),1);

        $this->assertEquals(is_array($json), true);
        $this->assertEquals(is_array($json['info']), true);
        $this->assertEquals(isset($json['info']['version']), true);
        $this->assertEquals(is_array($json['parts']), true);
        $this->assertEquals(isset($json['pattern']), true);
    }
}
