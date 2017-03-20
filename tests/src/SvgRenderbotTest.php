<?php

namespace Freesewing\Tests;

class SvgRenderbotTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp()
    {
        $this->pattern = new \Freesewing\Patterns\Tests\TestPattern();
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
        $svg = $bot->render($this->pattern);
        $this->saveFixture('pattern', $svg);
        $this->assertEquals($svg, $this->loadFixture('pattern'));
    }
    
    /**
     * Tests the renderPath method
     */
    public function testRenderPath()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $path = new \Freesewing\Path();
        $path->setPathstring('M 1 L 2');
        $svg = $bot->renderPath($path,$p);
        $this->assertEquals($svg, $this->loadFixture('path'));
        $path->setRender(false);
        $this->assertEquals($bot->renderPath($path,$p), '');
    }
    
    /**
     * Tests the renderPath method for a path that's part of a Part
     */
    public function testRenderPathFromPart()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $p->newPath('test', 'M 1 L 2');
        $svg = $bot->render($this->pattern);
        $this->saveFixture('pathFromPart', $svg);
        $this->assertEquals($svg, $this->loadFixture('pathFromPart'));
    }

    /**
     * Tests the renderSnippet method
     */
    public function testRenderSnippet()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newPoint(1,0,0);
        $p->newSnippet('button', 'button', 1);
        $svg = $bot->render($this->pattern);
        $this->saveFixture('snippet', $svg);
        $this->assertEquals($svg, $this->loadFixture('snippet'));
    }

    /**
     * Tests the renderText method
     */
    public function testRenderText()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newPoint(1,0,0);
        $p->newText('text', 1, 'This is an example');
        $svg = $bot->render($this->pattern);
        $this->saveFixture('text', $svg);
        $this->assertEquals($svg, $this->loadFixture('text'));
    }

    /**
     * Tests the renderNote method
     */
    public function testRenderNote()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newPoint(1,0,0);
        $p->newNote('note', 1, 'This is an example', 4, 40, 0);
        $svg = $bot->render($this->pattern);
        $this->saveFixture('note', $svg);
        $this->assertEquals($svg, $this->loadFixture('note'));
    }

    /**
     * Tests the renderDimension method
     */
    public function testRenderDimension()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $p->newHeightDimension(1,2,115);
        $p->newWidthDimension(1,2,115);
        $p->newLinearDimension(1,2,15);
        $svg = $bot->render($this->pattern);
        $this->saveFixture('dimension', $svg);
        $this->assertEquals($svg, $this->loadFixture('dimension'));
    }

    /**
     * Tests the renderTextOnPath method
     */
    public function testRenderTextOnPath()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,100);
        $p->newTextOnPath('test', 'M 1 L 2', 'This is an example', ['line-height' => 6]);
        $svg = $bot->render($this->pattern);
        $this->saveFixture('textOnPath', $svg);
        $this->assertEquals($svg, $this->loadFixture('textOnPath'));
    }

    /**
     * Tests the rendering of includes
     */
    public function testRenderInclude()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->newInclude('test', '<!-- This is an example -->');
        $svg = $bot->render($this->pattern);
        $this->saveFixture('include', $svg);
        $this->assertEquals($svg, $this->loadFixture('include'));
    }

    /**
     * Tests the rendering of transforms
     */
    public function testRenderTransform()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $p = $this->pattern->parts['testPart'];
        $p->addTransform('test', new \Freesewing\Transform('translate',10,20));
        $svg = $bot->render($this->pattern);
        $this->saveFixture('transform', $svg);
        $this->assertEquals($svg, $this->loadFixture('transform'));
    }
    
    private function loadFixture($fixture)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/src/fixtures';
        $file = "$dir/SvgRenderbot.$fixture.svg";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        return true;
        $dir = \Freesewing\Utils::getApiDir().'/tests/src/fixtures';
        $file = "$dir/SvgRenderbot.$fixture.svg";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
