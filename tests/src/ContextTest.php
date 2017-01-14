<?php

namespace Freesewing\Tests;

use \Freesewing\Context;

class ContextTest extends \PHPUnit\Framework\TestCase
{

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
            ['Pattern', new \Freesewing\Patterns\TestPattern()],
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
        $context->setRequest(new \Freesewing\Request(['channel' => 'Docs', 'theme' => 'Info', 'pattern' => 'TestPattern']));
        $context->configure();
        $context->addPattern();
        $this->assertEquals($context->getPattern(), new \Freesewing\patterns\TestPattern());
    }

    /**
     * Tests runService
     */
    public function testRunService()
    {
        $context = new Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'info', 'channel' => 'Docs']));
        $context->configure();
        $expected = '{"services":["info","draft","sample","compare"],"patterns":{"AaronAshirt":"Aaron A-Shirt","BruceBoxerBriefs":"Bruce Boxer Briefs","CathrinCorset":"Cathrin Corset","HugoHoodie":"Hugo Hoodie","JoostBodyBlock":"Joost Body Block","SimonShirt":"Simon Shirt","TamikoTop":"Tamiko Top","TestPattern":"Test pattern","TheoTrousers":"Theo trousers","TheodoreTrousers":"Theodore trousers","TrayvonTie":"Trayvon Tie","WahidWaistcoat":"Wahid Waistcoat"},"channels":["Docs"],"themes":["Compare","Designer","Developer","Paperless","Svg"]}';
        $context->runService();
        $this->expectOutputString($expected);
    }

       
}
