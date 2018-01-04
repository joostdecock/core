<?php

namespace Freesewing\Tests;

class InfoTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests the themePatternInfo method
     */
    public function testThemePatternInfo()
    {
        $theme = new \Freesewing\Themes\Core\Info();
        $pattern = new \Freesewing\Patterns\Tests\TestPattern();
        $data = $pattern->getConfig();
        $data['models'] = $pattern->getSamplerModelConfig();
        $data['pattern'] = basename(\Freesewing\Utils::getClassDir($pattern));
        $response = $theme->themePatternInfo($data, 'php');
        $this->saveFixture('patternInfoPhp', $response->getBody());
        $this->assertEquals($response->getBody(),$this->loadFixture('patternInfoPhp'));
        
        $response = $theme->themePatternInfo($data, 'html');
        $this->saveFixture('patternInfoHtml', $response->getBody());
        $this->assertEquals($response->getBody(),$this->loadFixture('patternInfoHtml'));
        
        $response = $theme->themePatternInfo($data, 'other');
        $this->saveFixture('patternInfoOther', serialize($response->getBody()));
        $this->assertEquals(serialize($response->getBody()),$this->loadFixture('patternInfoOther'));
    }
    
    /**
     * Tests the themeInfo method
     */
    public function testThemeInfo()
    {
        $theme = new \Freesewing\Themes\Core\Info();
        $data = [
            'version' => 'phpunit',
            'services' => ['service 1','service 2'],
            'patterns' => ['Core' => ['pattern 1','pattern 2']],
            'channels' => ['Core' => ['channel 1','channel 2']],
            'themes' => ['Core' => ['theme 1','theme 2']],
        ];
        $response = $theme->themeInfo($data, 'php');
        $this->assertEquals($response->getBody(),serialize($data));
        
        $response = $theme->themeInfo('something', 'yadayada');
        $this->assertEquals($response->getBody(),'something');
        
        $response = $theme->themeInfo($data, 'html');
        $this->saveFixture('infoHtml', $response->getBody());
        $this->assertEquals($response->getBody(),$this->loadFixture('infoHtml'));

    }
    
    /**
     * Tests the cleanUp method does noting
     */
    public function testCleanUp()
    {
        $theme1 = new \Freesewing\Themes\Core\Info();
        $theme2 = new \Freesewing\Themes\Core\Info();
        $theme1->cleanUp();
        $this->assertEquals($theme1,$theme2);
    }

    private function loadFixture($fixture)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
        $file = "$dir/Info.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        return true;
        $dir = \Freesewing\Utils::getApiDir().'/tests/themes/fixtures';
        $file = "$dir/Info.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
