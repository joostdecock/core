<?php

namespace Freesewing\Tests;

use \Freesewing\Output;
require_once __DIR__.'/../src/assets/testFunctions.php';

class DesignerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
       Output::reset();
    }

    public function tearDown()
    {
       Output::reset();
    }

    /**
     * Tests the loadTemplates method
     *
     */
    public function testLoadTemplates()
    {
        $svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        $theme = new \Freesewing\Themes\Designer();
        $this->assertFalse($svgDocument->script->load());
        $theme->loadTemplates($svgDocument);
        $this->assertContains('ecmascript',$svgDocument->script->load());
    }
}
