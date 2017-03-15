<?php

namespace Freesewing\Tests;

use \Freesewing\Sampler;

class SamplerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Sampler');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['partContainer'],
            ['boundaries'],
            ['modelConfig'],
            ['models'],
            ['pattern'],
        ];
    }

    public function providerGettersReturnWhatSettersSet()
    {
        $pattern = new \Freesewing\Patterns\Tests\TestPattern();
        return [
            ['Pattern',  new \Freesewing\Patterns\Tests\TestPattern()],
        ];
    }

    /**
     * Tests the loadPatternOptions method
     */
    public function testLoadPatternOptions()
    {
        $in = [
            'options' => [
                'option1' => [
                    'type' => 'measure',
                    'default' => 52,
                ],
                'option2' => [
                    'type' => 'percent',
                    'default' => 80,
                ],
            ],
        ];

        $out = ['option1' => 52, 'option2' => 0.8];
        // Mock the pattern
        $pattern = $this->getMockBuilder('\freesewing\patterns\Tests\TestPattern')->getMock();
        $pattern->method('getConfig')->willReturn($in);
        
        $object = new \Freesewing\Sampler();
        $object->setPattern($pattern);

        $this->assertEquals($object->loadPatternOptions(), $out);
    }

    private function loadFixture($fixture)
    {
        $dir = 'tests/src/fixtures';
        $file = "$dir/Sampler.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        $dir = 'tests/src/fixtures';
        $file = "$dir/Sampler.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
