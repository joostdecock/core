<?php

namespace Freesewing\Tests;

class SvgDefsTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadAfterAdd()
    {
        $data = 'sorcha';
        $expectedResult = "\n<defs id=\"defs\">\n    sorcha \n</defs>\n";

        $object = new \Freesewing\SvgDefs();
        $object->add($data);
        $this->assertEquals($expectedResult, $object->load());
    }
    
    /* 
     * Tests return when there's no data
     */
    public function testFalseOnNoData()
    {
        $object = new \Freesewing\SvgDefs();
        $this->assertEquals(false, $object->load());
    }
}
