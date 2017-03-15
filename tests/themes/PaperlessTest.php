<?php

namespace Freesewing\Tests;

use \Freesewing\Output;
require_once __DIR__.'/../src/assets/testFunctions.php';

class PaperlessTest extends \PHPUnit\Framework\TestCase
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
     * Tests the isPaperless method
     *
     */
    public function testIsPaperless()
    {
        $theme = new \Freesewing\Themes\Core\Paperless();
        $this->assertTrue($theme->isPaperless());
    }

    /**
     * Tests the themeSvg method
     */
    public function testThemeSvg()
    {
        $theme = new \Freesewing\Themes\Core\Paperless();
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
        $this->saveFixture('themeSvgDocument', serialize($svgDocument));
        $this->assertEquals(serialize($svgDocument), $this->loadFixture('themeSvgDocument'));
    }

    /**
     * Tests the themePattern method
     */
    public function testThemePattern()
    {
        $context = new \Freesewing\Context();
        $context->setRequest(
            new \Freesewing\Request([
                'service' => 'draft', 
                'pattern' => 'TestPattern', 
                'theme' => 'Paperless',
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
        $p->newPath('test', 'M 1 L 2');

        $context->getPattern()->newPart('part2');
        $p = $context->getPattern()->parts['part2'];
        $p->newPoint(1,0,0); 
        $p->newPoint(2,100,100); 
        $p->newPath('test', 'M 1 L 2');
        $p->newPoint('gridAnchor',50,50); 

        $context->getPattern()->setPartMargin($context->getTheme()->config['settings']['partMargin']);
        $context->getTheme()->applyRenderMask($context->getPattern());
        $context->getPattern()->layout();
        $context->getTheme()->themePattern($context->getPattern());

        $this->saveFixture('themePattern', serialize($context->getPattern()));
        $this->assertEquals(serialize($context->getPattern()), $this->loadFixture('themePattern'));
    }

    private function loadFixture($fixture)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
        $file = "$dir/Paperless.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        return true;
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
        $file = "$dir/Paperless.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
