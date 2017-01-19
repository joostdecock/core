<?php

namespace Freesewing\Tests;

class BasicTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests the isPaperless method
     */
    public function testIsPaperless()
    {
        $theme = new \Freesewing\Themes\Basic();

    }

    /**
     * Tests the getThemeName method
     */
    public function testGetThemeName()
    {
        $theme = new \Freesewing\Themes\Basic();
        $this->assertEquals($theme->getThemeName(), 'Basic');
    }

    /**
     * Tests the themeResponse method
     */
    public function testThemeResponse() {
        $svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        $theme = new \Freesewing\Themes\Basic();
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'TestPattern', 'parts' => 'testPart', 'forceParts' => true]));
        $response = $theme->themeResponse($context);
        $this->assertEquals($response->getFormat(), 'svg');
    }
}
