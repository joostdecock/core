<?php

namespace Freesewing\Tests;

use \Freesewing\Output;
require_once __DIR__.'/../src/assets/testFunctions.php';

class PaperlessTest extends \PHPUnit\Framework\TestCase
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
     * Tests the isPaperless method
     *
     */
    public function testIsPaperless()
    {
        $theme = new \Freesewing\Themes\Paperless();
        $this->assertTrue($theme->isPaperless());
    }

    /**
     * Tests the themeSvg method
     */
    public function testThemeSvg()
    {
        $theme = new \Freesewing\Themes\Paperless();
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

    public function estThemePattern() {
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'AaronAshirt', 'theme'=>'paperless']));
        $context->configure();

        $pattern = $context->pattern;
        $part = $pattern->parts['front'];
        $part2 = clone $part1;
        unset($part2->points['gridAnchhor']);

        $service = new \Freesewing\Services\DraftService();
        $service->run($context);

        $this->assertContains('Content-Type: image/svg+xml', Output::$headers);
        $this->assertEquals(substr(Output::$body,0,54), '<?xml version="1.0" encoding="UTF-8" standalone="no"?>');
    }

    private function loadFixture($fixture)
    {
        $dir = 'tests/themes/fixtures';
        $file = "$dir/Paperless.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        $dir = 'tests/themes/fixtures';
        $file = "$dir/Paperless.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
