<?php

namespace Freesewing\Tests;

use \Freesewing\Output;
use Symfony\Component\Translation\Translator;
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
        $this->assertEquals(serialize($svgDocument), $this->loadFixture('themeSvgDocument'));
    }

    /**
     * Tests the themePattern method
     */
    public function testThemePatternBasic()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(
            new \Freesewing\Request([
                'service' => 'draft', 
                'pattern' => 'TestPattern', 
                'theme' => 'Designer',
            ])
        );
        $context->configure();
        $context->addPattern();
        $context->addModel();
        $context->getModel()->addMeasurements($context->getChannel()->standardizeModelMeasurements($context->getRequest(), $context->getPattern()));
        $context->getPattern()->addOptions($context->getChannel()->standardizePatternOptions($context->getRequest(), $context->getPattern()));
        $context->addUnits();
        $context->getPattern()->setUnits($context->getUnits());
        $context->addTranslator();
        $context->getPattern()->setTranslator($context->getTranslator());
        $context->getTheme()->setOptions($context->getRequest());

        $p = $context->getPattern()->parts['testPart'];
        $p->newPoint(1,0,0); 
        $p->newPoint(2,100,100); 
        $p->newPoint(3,0,100); 
        $p->newPath('test1', 'M 1 L 2 C 3 3 1');
        $p->offsetPathString('test2', 'M 1 L 2 C 3 3 1', 10, 1);

        $context->getPattern()->setPartMargin($context->getTheme()->config['settings']['partMargin']);
        $context->getTheme()->applyRenderMask($context->getPattern());
        $context->getPattern()->layout();
        $context->getTheme()->themePattern($context->getPattern());

        $this->assertEquals(serialize($context->getPattern()), $this->loadFixture('themePatternBasic'));
    }

    /**
     * Tests the themePattern method
     */
    public function testThemePatternMarked()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(
            new \Freesewing\Request([
                'service' => 'draft', 
                'pattern' => 'TestPattern', 
                'theme' => 'Designer',
                'onlyPoints' => '3',
                'markPoints' => '3',
            ])
        );
        $context->configure();
        $context->addPattern();
        $context->addModel();
        $context->getModel()->addMeasurements($context->getChannel()->standardizeModelMeasurements($context->getRequest(), $context->getPattern()));
        $context->getPattern()->addOptions($context->getChannel()->standardizePatternOptions($context->getRequest(), $context->getPattern()));
        $context->addUnits();
        $context->getPattern()->setUnits($context->getUnits());
        $context->addTranslator();
        $context->getPattern()->setTranslator($context->getTranslator());
        $context->getTheme()->setOptions($context->getRequest());

        $p = $context->getPattern()->parts['testPart'];
        $p->newPoint(1,0,0); 
        $p->newPoint(2,100,100); 
        $p->newPoint(3,0,100); 
        $p->newPath('test', 'M 1 L 2 C 3 3 1');

        $context->getPattern()->setPartMargin($context->getTheme()->config['settings']['partMargin']);
        $context->getTheme()->applyRenderMask($context->getPattern());
        $context->getPattern()->layout();
        $context->getTheme()->themePattern($context->getPattern());

        $this->assertEquals(serialize($context->getPattern()), $this->loadFixture('themePatternMarked'));
    }

    private function loadFixture($fixture)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
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
