<?php

namespace Freesewing\Tests;

class SvgCommentsTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadAfterAdd()
    {
        $data = 'sorcha';
        $expectedResult = "\n<!--sorcha\n  -->\n";

        $object = new \Freesewing\SvgComments();
        $object->add($data);
        $this->assertEquals($expectedResult, $object->load());
    }
}
