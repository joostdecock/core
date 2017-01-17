<?php

namespace Freesewing\Tests;

class SvgIncludeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that get gets what set sets
     * @param string $data Data to set
     * @dataProvider providerGetGetsWhatSetSets
     */
    public function testGetGetsWhatSetSets($data)
    {
        $object = new \Freesewing\SvgInclude();
        $object->set($data);
        $this->assertEquals($object->get(), $data);
    }

    public function providerGetGetsWhatSetSets()
    {
        return [
            ['X'],
            [69],
            ['A description'],
            ['<path id="168"  d=" M  0,0  L  106.25,-0  L  122.5,16.25  L  122.5,65  L  -122.5,65  L  -122.5,16.25  L  -106.25,0  z " />'],
        ];
    }
}
