<?php

namespace Freesewing\Tests;

class YamlrTest extends \PHPUnit\Framework\TestCase
{
    private function loadTemplate($template)
    {
        $dir = 'tests/src/fixtures';
        return "$dir/YamlrTest.$template.yml";
    }

    public function testYamlFileLoadAndParsing()
    {
        $data = [
            'fruit' => [
                'banana',
                'kiwi',
            ],
            'colors' => [
                'purple',
                'blue',
                'pink'
            ]
        ];
        $yamlr = new \Freesewing\Yamlr();
        $config = $yamlr->loadYamlFile($this->loadTemplate('correct'));

        $this->assertEquals($data, $config);
    }

    /**
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     * @expectedExceptionMessage Unable to parse
     */
    public function testFaultyYamlFile()
    {
        $yamlr = new \Freesewing\Yamlr();
        $config = $yamlr->loadYamlFile($this->loadTemplate('incorrect'));
    }
}
