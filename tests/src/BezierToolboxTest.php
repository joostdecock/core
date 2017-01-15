<?php

namespace Freesewing\Tests;

use \Freesewing\BezierToolbox;

class BezierToolboxTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\BezierToolbox');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['steps'],
        ];
    }

}
