<?php

namespace Freesewing\Tests;

class PaperlessTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests the isPaperless method
     *
     */
    public function testIsPaperless()
    {
        $theme = new \Freesewing\Themes\Paperless();
        $this->assertTrue($theme->isPaperless());
    }

    public function estThemeResponse() {
        $svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        $pattern = new \Freesewing\Patterns\Pattern();
        $theme = new \Freesewing\Themes\Theme();
        $theme->themePattern($pattern, $svgDocument);
    }
}
