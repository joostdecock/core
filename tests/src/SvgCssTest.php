<?php

namespace Freesewing\Tests;

class SvgCssTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadAfterAdd()
    {
        $data = 'sorcha';
        $expectedResult = "\n<style type=\"text/css\">\n    <![CDATA[\n\n    $data\n    ]]>\n</style>\n";

        $object = new \Freesewing\SvgCss();
        $object->add($data);
        $this->assertEquals($expectedResult, $object->load());
    }
    
    /* 
     * Tests return when there's no data
     */
    public function testFalseOnNoData()
    {
        $object = new \Freesewing\SvgCss();
        $this->assertEquals(false, $object->load());
    }
    
    /* 
     * Tests CSS soring
     */
    public function testCssSorting()
    {
        $data = "sorcha\n@line";
        $expectedResult = "\n<style type=\"text/css\">\n    <![CDATA[\n\n    @line\n    sorcha\n    ]]>\n</style>\n";

        $object = new \Freesewing\SvgCss();
        $object->add($data);

        $this->assertEquals($expectedResult, $object->load());
    }
}
