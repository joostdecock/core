<?php

namespace Freesewing\Tests;

class SampleTest extends \PHPUnit\Framework\TestCase
{

    public function estThemeResponse() {
        $svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        $pattern = new \Freesewing\Patterns\Pattern();
        $theme = new \Freesewing\Themes\Theme();
        $theme->themePattern($pattern, $svgDocument);
        print_r($svgDocument->css);
    }
}
