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
    
    /**
     * Tests the themeSvg method
     */
    public function testThemeSvg()
    {
        $theme = new \Freesewing\Themes\Designer();
        $theme->messages = "message 1\nmessage2";
        $theme->debug = "debug line 1\ndebug line 2";
        $svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        $theme->themeSvg($svgDocument);
        $this->saveFixture('themeSvgDocument',serialize($svgDocument));
        $this->assertEquals(serialize($svgDocument), $this->loadFixture('themeSvgDocument'));
    }

    private function loadFixture($fixture)
    {
        $dir = 'tests/themes/fixtures';
        $file = "$dir/Designer.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        $dir = 'tests/themes/fixtures';
        $file = "$dir/Designer.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
