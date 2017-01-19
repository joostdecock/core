<?php

namespace Freesewing\Tests;

use \Freesewing\Context;
use \Freesewing\Output;
require_once __DIR__.'/assets/testFunctions.php';

class ContextTest extends \PHPUnit\Framework\TestCase
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
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Context');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['service'],
            ['pattern'],
            ['theme'],
            ['channel'],
            ['request'],
            ['response'],
            ['model'],
            ['translator'],
            ['optionsSampler'],
            ['measurementsSampler'],
            ['renderbot'],
            ['svgDocument'],
            ['locale'],
            ['config'],
            ['units'],
        ];
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerGettersReturnWhatSettersSet
     */
    public function testGettersReturnWhatSettersSet($methodSuffix, $expectedResult)
    {
        $context = new Context();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $context->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $context->{$getMethod}());
    }
    public function providerGettersReturnWhatSettersSet()
    {
        return [
            ['Response', new \Freesewing\Response()],
            ['Pattern', new \Freesewing\Patterns\AaronAshirt()],
            ['Theme', new \Freesewing\Themes\Svg()],
            ['Service', new \Freesewing\Services\DraftService()],
            ['Channel', new \Freesewing\Channels\Docs()],
            ['Locale', 'en'],
            ['Config', ['foo' => 'bar']],
            ['Model', new \Freesewing\Model()],
            ['Request', new \Freesewing\Request()],
            ['OptionsSampler', new \Freesewing\OptionsSampler()],
            ['MeasurementsSampler', new \Freesewing\MeasurementsSampler()],
            ['Renderbot', new \Freesewing\SvgRenderbot()],
            ['SvgDocument', new \Freesewing\SvgDocument(
                new \Freesewing\SvgComments,
                new \Freesewing\SvgAttributes,
                new \Freesewing\SvgCss,
                new \Freesewing\SvgScript,
                new \Freesewing\SvgDefs,
                new \Freesewing\SvgComments
                )
            ],
        ];
    }
    
    /**
     * Tests the addTheme() method
     */
    public function testAddTheme()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['theme' => 'Paperless']));
        $context->addTheme();
        $this->assertEquals($context->getTheme(), new \Freesewing\Themes\Paperless());
    }

    /**
     * Tests whether service gets loaded
     */
    public function testLoadService()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info']));
        $context->configure();
        $this->assertEquals($context->getService(), new \Freesewing\Services\InfoService());
    }

    /**
     * Tests fallback service loading
     */
    public function testFallbackServiceLoading()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'nonexisting']));
        $context->configure();
        $this->assertEquals($context->getService(), new \Freesewing\Services\DraftService());
    }

    /**
     * Tests fallback pattern loading
     */
    public function testFallbackPatternLoading()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft']));
        $context->configure();
        $context->addPattern();
        $this->assertEquals($context->getPattern(), new \Freesewing\Patterns\AaronAshirt());
    }

    /**
     * Tests nonexisting pattern exception
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot load pattern nonexisting, it does not exist
     */
    public function testNonexistingPattern()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'nonexisting']));
        $context->configure();
        $context->addPattern();
    }

    /**
     * Tests nonexisting theme exception
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot load theme nonexisting, it does not exist
     */
    public function testNonexistingTheme()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'theme' => 'nonexisting']));
        $context->configure();
    }

    /**
     * Tests local loading
     */
    public function testLoadLocale()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['lang' => 'nl']));
        $context->configure();
        $this->assertEquals($context->getLocale(), 'nl');
    }

    /**
     * Tests channel loading
     */
    public function testLoadChannel()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs']));
        $context->configure();
        $this->assertEquals($context->getChannel(), new \Freesewing\Channels\Docs());
    }

    /**
     * Tests non existing channel exception
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot load channel nonexisting, it does not exist
     */
    public function testNonExistingChannel()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'nonexisting']));
        $context->configure();
        
    }

    /**
     * Tests addMethods
     *
     * @dataProvider providerTestAddMethods
     */
    public function testAddMethods($object)
    {
        $addMethod = 'add'.$object;
        $getMethod = 'get'.$object;
        $class = "\\freesewing\\$object";
        
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Paperless']));
        $context->{$addMethod}();
        $this->assertEquals($context->{$getMethod}(), new $class());
    }

    public function providerTestAddMethods()
    {
        return [
            ['Model'],
            ['OptionsSampler'],
            ['MeasurementsSampler'],
            ];
    }   
       
    /**
     * Tests addPattern
     */
    public function testAddPattern()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Info', 'pattern' => 'AaronAshirt']));
        $context->configure();
        $context->addPattern();
        $this->assertEquals($context->getPattern(), new \Freesewing\patterns\AaronAshirt());
    }
    
    /**
     * Tests addRenderbot
     */
    public function testAddRenderbot()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Paperless']));
        $context->addRenderbot();
        $this->assertEquals($context->getRenderbot(), new \freesewing\SvgRenderbot());
    }
    /**
     * Tests addTranslator
     */
    public function testAddTranslator()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Svg', 'pattern' => 'AaronAshirt']));
        $context->configure();
        $context->addPattern();
        $context->addTranslator();
        $this->assertEquals(($context->getTranslator() instanceof \Symfony\Component\Translation\Translator), true);
    }

    /**
     * Tests addUnits
     */
    public function testAddUnits()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Svg', 'pattern' => 'AaronAshirt', 'unitsIn' => 'imperial', 'unitsOut' => 'metric']));
        $context->configure();
        $context->addUnits();
        $expected = [ 'in' => 'imperial', 'out' => 'metric', ];
        $this->assertEquals($context->getUnits(), $expected);
        
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Svg', 'pattern' => 'AaronAshirt', 'unitsIn' => 'metric', 'unitsOut' => 'imperial']));
        $context->configure();
        $context->addUnits();
        $expected = [ 'in' => 'metric', 'out' => 'imperial', ];
        $this->assertEquals($context->getUnits(), $expected);
    }

    /**
     * Tests addSvgDocument
     */
    public function testAddSvgDocument()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Svg', 'pattern' => 'AaronAshirt']));
        $context->configure();
        $context->addSvgDocument();
        $expected = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments,
            new \Freesewing\SvgAttributes,
            new \Freesewing\SvgCss,
            new \Freesewing\SvgScript,
            new \Freesewing\SvgDefs,
            new \Freesewing\SvgComments
        );

        $this->assertEquals($context->getSvgDocument(), $expected);
    }

    /**
     * Tests test cleanUp
     */
    public function testCleanUp()
    {
        // Mock pattern, theme, and channel classes
        $pattern = $this->getMockBuilder('\freesewing\patterns\AaronAshirt')->getMock();
        $theme = $this->getMockBuilder('\freesewing\themes\Svg')->getMock();
        $channel = $this->getMockBuilder('\freesewing\channels\Docs')->getMock();

        // We expect cleanUp() to be called once on pattern, theme, and channel
        $pattern->expects($this->once())->method('cleanUp');
        $theme->expects($this->once())->method('cleanUp');
        $channel->expects($this->once())->method('cleanUp');
        
        $context = new Context();
        $context->setPattern($pattern);
        $context->setChannel($channel);
        $context->setTheme($theme);
        $context->cleanUp();
    }


    /**
     * Tests runService
     */
    public function testRunService()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info', 'channel' => 'Docs']));
        $context->configure();

        $context->runService();
        $json = json_decode(Output::$body,1);

        $this->assertTrue(is_array($json));
        $this->assertTrue(is_array($json['services']));
        $this->assertTrue(is_array($json['patterns']));
        $this->assertTrue(is_array($json['themes']));
        $this->assertTrue(is_array($json['channels']));
    }
}
