<?php

namespace Freesewing\Tests;

class BasicTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests the isPaperless method
     */
    public function testIsPaperless()
    {
        $theme = new \Freesewing\Themes\Core\Basic();

    }

    /**
     * Tests the getThemeName method
     */
    public function testGetThemeName()
    {
        $this->markTestSkipped ( "method does not exit anymore" );
        $theme = new \Freesewing\Themes\Core\Basic();
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
        $theme = new \Freesewing\Themes\Core\Basic();
        $context = new \Freesewing\Context();
        $context->setRequest(new \Freesewing\Request(['service' => 'draft', 'pattern' => 'TestPattern', 'parts' => 'testPart', 'forceParts' => true]));
        $response = $theme->themeResponse($context);
        $this->assertEquals($response->getFormat(), 'svg');
    }
    
    /**
     * Tests the applyRenderMaskOnParts method
     */
    public function testapplyRenderMaskOnParts()
    {
        $theme = new \Freesewing\Themes\Core\Basic();
        $pattern = new \Freesewing\Patterns\Tests\TestPattern();
        $pattern->newPart('part1');
        $pattern->parts['part1']->setRender(false);
        $pattern->newPart('part2');
        $pattern->newPart('part3');
        
        $theme->setOptions(new \Freesewing\Request(['parts' => 'part1,part2']));
        $theme->applyRenderMask($pattern);
        
        $this->assertFalse($pattern->parts['part1']->getRender());
        $this->assertTrue($pattern->parts['part2']->getRender());
        $this->assertFalse($pattern->parts['part3']->getRender());
        
        $theme->setOptions(new \Freesewing\Request(['parts' => 'part1,part2', 'forceParts' => true]));
        $theme->applyRenderMaskOnParts($pattern);
        
        $this->assertTrue($pattern->parts['part1']->getRender());
        $this->assertTrue($pattern->parts['part2']->getRender());
        $this->assertFalse($pattern->parts['part3']->getRender());
    }
}
