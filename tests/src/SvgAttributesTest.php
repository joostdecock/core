<?php

namespace Freesewing\Tests;

class SvgAttributesTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadAfterAdd()
    {
        $data = 'sorcha';
        $expectedResult = "\n<svg\n    sorcha\n    \n>\n";
        
        $object = new \Freesewing\SvgAttributes();
        $object->add($data);

        $this->assertEquals($expectedResult, $object->load());
    }
}
