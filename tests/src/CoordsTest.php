<?php

namespace Freesewing\Tests;

class CoordsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Coords');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['x'],
            ['y'],
        ];
    }

}
