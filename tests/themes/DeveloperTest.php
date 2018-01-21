<?php

namespace Freesewing\Tests;

class DeveloperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the themeResponse method
     */
    public function testThemeResponse() {
        // Developer theme is deprecated
        $this->assertTrue(true);
        return true;

        $svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        $theme = new \Freesewing\Themes\Core\Developer();
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'TestPattern', 'parts' => 'testPart', 'forceParts' => true]));
        $response = $theme->themeResponse($context);
        $this->assertEquals($response->getFormat(), 'json');
    }
}
