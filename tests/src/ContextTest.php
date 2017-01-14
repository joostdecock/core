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
     * @param string $methodSuffix The part of the method to call without 'set'
     *
     * @dataProvider providerSetXAndSetYSetNonNumericValuesToZero
     */
 /*   public function testSetXAndSetYSetNonNumericValuesToZero($methodSuffix)
    {
        $point = new \Freesewing\Point();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $point->{$setMethod}('sorcha');
        $this->assertEquals(0, $point->{$getMethod}());
    }

    public function providerSetXAndSetYSetNonNumericValuesToZero()
    {
        return [
            ['X'],
            ['Y'],
        ];
    }
  */
}
