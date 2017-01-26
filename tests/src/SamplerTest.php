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

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerGettersReturnWhatSettersSet
     */
    public function testGettersReturnWhatSettersSet($methodSuffix, $expectedResult)
    {
        $object = new \Freesewing\Sampler();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $object->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        $pattern = new \Freesewing\Patterns\TestPattern();
        return [
            ['Pattern',  new \Freesewing\Patterns\TestPattern()],
        ];
    }

    /**
     * Tests the getSamplerModelsFile method
     */
    public function testGetSamplerModelsFile()
    {
        $object = new \Freesewing\Sampler();
        $file = $object->getSamplerModelsFile(new \Freesewing\Patterns\TestPattern());
        $dir = dirname(dirname(__DIR__));
        $expect = "$dir/patterns/TestPattern/sampler/models.yml";
        $len = strlen($expect);
        $this->assertEquals(substr($file,-$len), $expect);
    }

    /**
     * Tests the getSamplerAnchor method without anchor
     */
    public function testGetSamplerAnchorNoAnchor()
    {
        $object = new \Freesewing\Sampler();
        
        $point = new \Freesewing\Point();
        $point->setX(0);
        $point->setX(0);
        $point->setDescription('Anchor point added by sampler');
        
        $part = new \Freesewing\Part();
        
        $this->assertEquals($object->getSamplerAnchor($part), $point);
    }
    
    /**
     * Tests the getSamplerAnchor method with gridAnchor
     */
    public function testGetSamplerAnchorWithGridAnchor()
    {
        $object = new \Freesewing\Sampler();
        
        $point = new \Freesewing\Point();
        $point->setX(52);
        $point->setX(69);
        
        $part = new \Freesewing\Part();
        $part->addPoint('gridAnchor',$point);
        
        $this->assertEquals($object->getSamplerAnchor($part), $point);
    }
    
    /**
     * Tests the getSamplerAnchor method with samplerAnchor
     */
    public function testGetSamplerAnchorWithSamplerAnchor()
    {
        $object = new \Freesewing\Sampler();
        
        $point = new \Freesewing\Point();
        $point->setX(69);
        $point->setX(52);
        
        $part = new \Freesewing\Part();
        $part->addPoint('samplerAnchor',$point);
        
        $this->assertEquals($object->getSamplerAnchor($part), $point);
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
        $pattern = $this->getMockBuilder('\freesewing\patterns\TestPattern')->getMock();
        $pattern->method('getConfig')->willReturn($in);
        
        $object = new \Freesewing\Sampler();
        $object->setPattern($pattern);

        $this->assertEquals($object->loadPatternOptions(), $out);
    }

    /**
     * Tests the samplerParts method
     */
    public function testSampleParts()
    {
        $object = new \Freesewing\Sampler();

        $pattern = new \Freesewing\Patterns\TestPattern();
        $pattern->addPart('test1');
        $pattern->addPart('test2');
        unset($pattern->parts['testPart']);
        
        $p1 = $pattern->parts['test1'];
        $p2 = $pattern->parts['test2'];

        $p1->newPoint(1,10,0);
        $p1->newPoint(2,10,2);
        $p1->newPoint(3,-100,-100);
        $p1->newPoint(4,100,100);
        $p1->newPath('test1', 'M 1 L 2');
        $p1->paths['test1']->setSample(true);
        
        $p2->newPoint(1,40,10);
        $p2->newPoint(2,5,2);
        $p2->newPoint('gridAnchor',5,5);
        $p2->newPath('test2', 'M 1 L 2');
        $p2->paths['test2']->setSample(true);
        $p2->paths['test2']->findBoundary($p2);
        
        $object->sampleParts(1,2, $pattern, new \Freesewing\Themes\Sampler(), new \Freesewing\SvgRenderbot()); 

        $p1->newPath('test3', 'M 3 L 4');
        $p1->paths['test3']->setSample(true);

        $object->sampleParts(2,1, $pattern, new \Freesewing\Themes\Sampler(), new \Freesewing\SvgRenderbot()); 

        $this->assertEquals(count($object->partContainer), 2);
        $this->assertTrue(isset($object->partContainer['test1']));
        $this->assertTrue(isset($object->partContainer['test2']));
        $this->assertEquals($object->partContainer['test1']['includes']['2-test3'], "\n".'<path transform="translate( 0, 0 )" style="stroke: hsl(269, 55%, 50%);" id="3"  d=" M  -100,-100  L  100,100 " />');

        $object->setPattern($pattern);
        $object->addSampledPartsToPattern();
        $this->assertEquals(serialize($object), $this->loadFixture('parts'));
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
