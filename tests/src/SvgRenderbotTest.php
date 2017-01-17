<?php

namespace Freesewing\Tests;

class SvgRenderbotTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp()
    {
        $this->pattern = new \Freesewing\Patterns\AaronAshirt();
    }

    private function loadTemplate($template)
    {
        $dir = 'tests/src/fixtures';
        return file_get_contents("$dir/SvgRenderbot.$template.svg");
    }
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\SvgRenderbot');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['tabs'],
            ['freeId'],
            ['openGroups'],
        ];
    }

    /**
     * Tests the render method
     */
    public function testRender()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('pattern'));
    }
    
    /**
     * Tests the renderPath method
     */
    public function testRenderPath()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $path = new \Freesewing\Path();
        $path->setPath('M 1 L 2');
        $this->assertEquals($bot->renderPath($path,$p), $this->loadTemplate('path'));
        $path->setRender(false);
        $this->assertEquals($bot->renderPath($path,$p), '');
    }
    
    /**
     * Tests the renderPath method for a path that's part of a Part
     */
    public function testRenderPathFromPart()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $p->newPath('test', 'M 1 L 2');
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('pathFromPart'));
    }

    /**
     * Tests the renderSnippet method
     */
    public function testRenderSnippet()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newPoint(1,0,0);
        $p->newSnippet('button', 'button', 1);
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('snippet'));
    }

    /**
     * Tests the renderText method
     */
    public function testRenderText()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newPoint(1,0,0);
        $p->newText('text', 1, 'This is an example');
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('text'));
    }

    /**
     * Tests the renderNote method
     */
    public function testRenderNote()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newPoint(1,0,0);
        $p->newNote('note', 1, 'This is an example', 4, 40, 0);
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('note'));
    }

    /**
     * Tests the renderDimension method
     */
    public function testRenderDimension()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $p->newHeightDimension(1,2,115);
        $p->newWidthDimension(1,2,115);
        $p->newLinearDimension(1,2,15);
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('dimension'));
    }

    /**
     * Tests the renderTextOnPath method
     */
    public function testRenderTextOnPath()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $p->newTextOnPath('test', 'M 1 L 2', 'This is an example', ['line-height' => 6]);
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('textOnPath'));
    }

    /**
     * Tests the rendering of includes
     */
    public function testRenderInclude()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->newInclude('test', '<!-- This is an example -->');
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('include'));
    }

    /**
     * Tests the rendering of transforms
     */
    public function testRenderTransform()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['front'];
        $p->addTransform('test', new \Freesewing\Transform('translate',10,20));
        $this->assertEquals($bot->render($this->pattern), $this->loadTemplate('transform'));
    }
}
