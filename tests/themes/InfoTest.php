<?php

namespace Freesewing\Tests;

class InfoTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests the themePatternInfo method
     */
    public function estThemePatternInfo()
    {
        $theme = new \Freesewing\Themes\Info();
        $pattern = new \Freesewing\Patterns\TestPattern();
        $response = $theme->themePatternInfo($pattern, 'php');
        $this->saveFixture('patternInfoPhp',$response->getBody());
        $this->assertEquals($response->getBody(),$this->loadFixture('patternInfoPhp'));
        
        $response = $theme->themePatternInfo($pattern, 'html');
        $this->saveFixture('patternInfoHtml',$response->getBody());
        $this->assertEquals($response->getBody(),$this->loadFixture('patternInfoHtml'));
        
        $response = $theme->themePatternInfo($pattern, 'other');
        $this->saveFixture('patternInfoOther',$response->getBody());
        $this->assertEquals($response->getBody(),$this->loadFixture('patternInfoOther'));
    }
    /**
     * Tests the themeInfo method
     */
    public function testThemeInfo()
    {
        $theme = new \Freesewing\Themes\Info();
        $data = [
            'services' => ['service 1','service 2'],
            'patterns' => ['pattern 1','pattern 2'],
            'channels' => ['channel 1','channel 2'],
            'themes' => ['theme 1','theme 2'],
        ];
        $response = $theme->themeInfo($data, 'php');
        $this->assertEquals($response->getBody(),serialize($data));
        
        $response = $theme->themeInfo('something', 'yadayada');
        $this->assertEquals($response->getBody(),'something');
        
        $response = $theme->themeInfo($data, 'html');
        $this->saveFixture('infoHtml',$response->getBody());
        $this->assertEquals($response->getBody(),$this->loadFixture('infoHtml'));

    }
    
    private function loadFixture($fixture)
    {
        $dir = 'tests/themes/fixtures';
        $file = "$dir/Info.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        $dir = 'tests/themes/fixtures';
        $file = "$dir/Info.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
