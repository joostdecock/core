<?php

namespace Freesewing\Tests;

class TestPatternTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\patterns\Pattern');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['height'],
            ['messages'],
            ['options'],
            ['paperless'],
            ['partMargin'],
            ['parts'],
            ['replacements'],
            ['units'],
            ['width'],
        ];
    }

    public function testConstructor()
    {
        $p = new \Freesewing\patterns\TestPattern;
        $this->assertEquals(count($p->parts), 1);
    }

    public function testGetTranslationsDir()
    {
        $p = new \Freesewing\patterns\TestPattern;
        $dir = $p->getPatternDir();
        $this->assertEquals($p->getTranslationsDir(),"$dir/translations");

    }

    public function testUnits()
    {
        $p = new \Freesewing\patterns\TestPattern;
        $p->setUnits(['in' => 'metric', 'out' => 'imperial']);
        $this->assertEquals($p->getUnits(),['in' => 'metric', 'out' => 'imperial']);
        $this->assertEquals($p->unit(200), '7.87"');
        $p->setUnits(['in' => 'metric', 'out' => 'metric']);
        $this->assertEquals($p->unit(123), '12.3cm');
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerGettersReturnWhatSettersSet
     */
    public function testGettersReturnWhatSettersSet($methodSuffix, $expectedResult)
    {
        $p = new \Freesewing\patterns\TestPattern;
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $p->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $p->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        return [
            ['Width', 52],
            ['Height', 69],
            ['PartMargin', 4],
        ];
    }

    public function testGetSetOptions()
    {
        $p = new \Freesewing\patterns\TestPattern;
        $p->setOption('sorcha', 10);
        $this->assertEquals($p->getOption('sorcha'), 10);
    }

    public function testClonePoints()
    {
        $pattern = new \Freesewing\patterns\TestPattern;
        $part = $pattern->parts['testpart'];
        $part->newPoint(1, 10, 10);
        $part->newPoint(2, 10, 30);
        $part->newPoint('test', 52, 69, 'Test description');
        $pattern->addPart('clone');
        $pattern->clonePoints('testpart', 'clone');
        $clone = $pattern->parts['clone'];

        $this->assertEquals($part->x(1), $clone->x(1));
        $this->assertEquals($part->y(2), $clone->y(2));
        $this->assertEquals($part->points['test']->getDescription(), $clone->points['test']->getDescription());
    }

    public function testPileParts()
    {
        $pattern = new \Freesewing\patterns\TestPattern;
        $part1 = $pattern->parts['testpart'];
        $part1->newPoint(1, 52, 69);
        $part1->newPoint(2, 100, 300);
        $part1->newPath(1, 'M 1 L 2');

        $pattern->addPart('part2');
        $part2 = $pattern->parts['part2'];
        $part2->newPoint(1, 10, 30);
        $part2->newPoint(2, 40, 70);
        $part2->newPath(1, 'M 2 L 1');

        $pattern->addPartBoundaries();
        $pattern->pileParts();
        $transform1 = new \Freesewing\Transform('translate', round(-52+$pattern->getPartMargin(),1), round(-69+$pattern->getPartMargin(),1));
        $transform2 = new \Freesewing\Transform('translate', round(-10+$pattern->getPartMargin(),1), round(-30+$pattern->getPartMargin(),1));
        $this->assertEquals($part1->transforms['#pileParts'], $transform1);
    }

    public function testAddBoundary()
    {//HERE
        $pattern = new \Freesewing\patterns\TestPattern;
        $part1 = $pattern->parts['testpart'];
        $part1->newPoint(1, 52, 69);
        $part1->newPoint(2, 100, 300);
        $part1->newPath(1, 'M 1 L 2');

        $pattern->addPart('part2');
        $part2 = $pattern->parts['part2'];
        $part2->newPoint(1, 10, 30);
        $part2->newPoint(2, 40, 70);
        $part2->newPath(1, 'M 2 L 1');

        $pattern->addPartBoundaries();
        $pattern->pileParts();
        $transform1 = new \Freesewing\Transform('translate', round(-52+$pattern->getPartMargin(),1), round(-69+$pattern->getPartMargin(),1));
        $transform2 = new \Freesewing\Transform('translate', round(-10+$pattern->getPartMargin(),1), round(-30+$pattern->getPartMargin(),1));
        $this->assertEquals($part1->transforms['#pileParts'], $transform1);

    }



 }
