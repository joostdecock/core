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
        $theme = new \Freesewing\Themes\Core\Designer();
        $this->assertFalse($svgDocument->script->load());
        $theme->themeSvg($svgDocument);
        $this->assertContains('ecmascript',$svgDocument->script->load());
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

        $context->getPattern()->setPartMargin($context->getTheme()->config['settings']['partMargin']);
        $context->getTheme()->applyRenderMask($context->getPattern());
        $context->getPattern()->layout();
        $context->getTheme()->themePattern($context->getPattern());

        $this->saveFixture('themePatternBasic', serialize($context->getPattern()));
        $this->assertEquals(serialize($context->getPattern()), $this->loadFixture('themePatternBasic'));
    }

    private function loadFixture($fixture)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
        $file = "$dir/Designer.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        return true;
        $dir = 'tests/themes/fixtures';
        $file = "$dir/Designer.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
