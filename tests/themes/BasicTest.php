<?php

namespace Freesewing\Tests;

class BasicTest extends \PHPUnit\Framework\TestCase
{

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
        $theme->applyRenderMask($pattern);
        
        $this->assertTrue($pattern->parts['part1']->getRender());
        $this->assertTrue($pattern->parts['part2']->getRender());
        $this->assertFalse($pattern->parts['part3']->getRender());
    }

    /**
     * Tests the ithemeSvg method
     */
    public function testThemeSvg()
    {
        $svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        
        $theme = new \Freesewing\Themes\Core\Basic();
        $theme->messages = 'message 1';
        $theme->debug = 'debug 1';

        $theme->themeSvg($svgDocument);

        $comments = $svgDocument->footerComments;
        $this->assertTrue(is_int(strpos("$comments", 'message 1')));
        $this->assertTrue(is_int(strpos("$comments", 'debug 1')));
    }
}
