<?php

namespace Freesewing\Tests;

class PatternTemplateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\patterns\Pattern');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['height'],
            ['messages'],
            ['options'],
            ['isPaperless'],
            ['partMargin'],
            ['parts'],
            ['replacements'],
            ['units'],
            ['width'],
        ];
    }

    /**
     * Tests the initialize method
     */
    public function testInitialize()
    {
        $pattern = new \Freesewing\Patterns\PatternTemplate();
        $model = new \Freesewing\Model();
        $pattern->initialize($model);
        $this->assertEquals($pattern->o('percentOption'), 52);
        $this->assertTrue(is_numeric($pattern->v('exampleValue')));
    }

    /** 
     * Tests draft and sample methods
     */
    public function testDraftAndSample()
    {
        $p1 = new \Freesewing\Patterns\PatternTemplate();
        $p2 = new \Freesewing\Patterns\PatternTemplate();
        $model = new \Freesewing\Model();
        $p1->setPaperless(true);
        $p2->setPaperless(true);
        $p1->initialize($model);
        $p2->initialize($model);
        $p2->sample($model);
        $p2->draft($model);
        $this->assertEquals($p1,$p2);
    }
}
