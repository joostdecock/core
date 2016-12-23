<?php

namespace Freesewing\Tests;

class YamlrTest extends \PHPUnit\Framework\TestCase
{
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
        $config = $yamlr->loadYamlFile(__DIR__.'/YamlrTestCorrect.yml');

        $this->assertEquals($data, $config);
    }

    /**
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     * @expectedExceptionMessage Unable to parse
     */
    public function testFaultyYamlFile()
    {
        $yamlr = new \Freesewing\Yamlr();
        $config = $yamlr->loadYamlFile(__DIR__.'/YamlrTestIncorrect.yml');
    }
}
