<?php

namespace Freesewing\Tests;

class SvgScriptTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadAfterAdd()
    {
        $data = 'sorcha';
        $expectedResult = "\n<script type=\"application/ecmascript\">\n    <![CDATA[\n    $data \n    ]]>\n</script>\n";

        $object = new \Freesewing\SvgScript();
        $object->add($data);
        $this->assertEquals($expectedResult, $object->load());
    }
    
    /* 
     * Tests return when there's no data
     */
    public function testFalseOnNoData()
    {
        $object = new \Freesewing\SvgScript();
        $this->assertEquals(false, $object->load());
    }
}
