<?php

namespace Freesewing\Tests;

class SvgCssTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadAfterAdd()
    {
        $data = 'sorcha';
        $expectedResult = "\n<style type=\"text/css\">\n    <![CDATA[\n    sorcha\n    ]]>\n</style>\n";

        $object = new \Freesewing\SvgCss();
        $object->add($data);
        $this->assertEquals($expectedResult, $object->load());
    }
}
