<?php

namespace Freesewing\Tests;

use \Freesewing\Output;
use Symfony\Component\Translation\Translator;
require_once __DIR__.'/../src/assets/testFunctions.php';

class DesignerTest extends \PHPUnit\Framework\TestCase
{
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
        $theme = new \Freesewing\Themes\Core\Designer();
        $this->assertFalse($svgDocument->script->load());
        $theme->themeSvg($svgDocument);
        $this->assertContains('ecmascript',$svgDocument->script->load());
    }
}
