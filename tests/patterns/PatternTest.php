<?php

namespace Freesewing\Tests;

class PatternTest extends \PHPUnit\Framework\TestCase
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
            ['paperless'],
            ['partMargin'],
            ['parts'],
            ['replacements'],
            ['units'],
            ['width'],
        ];
    }

}
