<?php

namespace Freesewing\Tests;

class SvgCommentsTest extends \PHPUnit\Framework\TestCase
{
    /* 
     * Tests display of data after call add method
     */
    public function testLoadAfterAdd()
    {
        $data = 'sorcha';
        $expectedResult = "\n<!--\n\n    $data\n    \n\n  -->\n";
        $object = new \Freesewing\SvgComments();
        $object->add($data);

        $this->assertEquals($expectedResult, $object->load());
    }
    
    /* 
     * Tests return when there's no data
     */
    public function testFalseOnNoData()
    {
        $object = new \Freesewing\SvgComments();
        $this->assertEquals(false, $object->load());
    }
    
    /* 
     * Tests replacement of data of data after call add method
     */
    public function testReplacements()
    {
        $data = 'sorcha';
        $replace = ['sorcha' => 'scorch'];
        $expectedResult = "\n<!--\n\n    scorch\n    \n\n  -->\n";
        $object = new \Freesewing\SvgComments();
        $object->add($data, $replace);

        $this->assertEquals($expectedResult, $object->load());
    }
    
}
