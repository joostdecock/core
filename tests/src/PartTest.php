<?php

namespace Freesewing\Tests;

class PartTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function estAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Part');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['points'],
            ['snippets'],
            ['texts'],
            ['textsOnPath'],
            ['paths'],
            ['transforms'],
            ['dimensions'],
            ['tmp'],
            ['notes'],
            ['title'],
            ['boundary'],
            ['maxOffsetTolerance'],
            ['steps'],
            ['render'],
        ];
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerGettersReturnWhatSettersSet
     */
    public function estGettersReturnWhatSettersSet($methodSuffix, $expectedResult)
    {
        $part = new \Freesewing\Part();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $part->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $part->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        return [
            ['Render', true],
            ['Render', false],
            ['Title', 'I am tired. Does thing unit testing ever end?'],
        ];
    }

    /** 
     * Tests the unit method
     */
    public function estUnit()
    {
        $p = new \Freesewing\Part();
        $p->setUnits('metric');
        $this->assertEquals($p->unit(20),'2cm');
        $this->assertEquals($p->unit(12.3456),'1.23cm');
        $p->setUnits('imperial');
        $this->assertEquals($p->unit(25.4),'1"');
        $this->assertEquals($p->unit(12.3456),'0.49"');
    }


    /** 
     * Tests the newNote method for edge cases not covered by other tests
     *
     * Specifically, an orientation that is not between 1 and 12
     * and a missing class attribute
     */
    public function estNewNoteEdgeCase()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newNote('test',1,'Test note', 123, 20, 2, ['dx' => 2]);
        $this->assertEquals(serialize($p->notes['test']),$this->loadFixture('note'));
    }

    /** 
     * Tests the addTitle method
     */
    public function estAddTitle()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);

        $p->addTitle(1,1,'Test title', 'Message', 'default');
        $this->assertEquals(serialize($p->texts),$this->loadFixture('title.default'));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'vertical');
        $this->assertEquals(serialize($p->texts),$this->loadFixture('title.vertical'));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'vertical-small');
        $this->assertEquals(serialize($p->texts),$this->loadFixture('title.verticalSmall'));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'horizontal');
        $this->assertEquals(serialize($p->texts),$this->loadFixture('title.horizontal'));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'horizontal-small');
        $this->assertEquals(serialize($p->texts),$this->loadFixture('title.horizontalSmall'));
    }

    /** 
     * Tests the hasPathToRender method
     */
    public function estHasPathToRender()
    {
        $p = new \Freesewing\Part();
        $this->assertFalse($p->hasPathToRender());
        $p->newPath('test', 'M 1 L 2');
        $this->assertTrue($p->hasPathToRender());
    }

    /** 
     * Tests that offsetPath throws exception whnen path object is missing
     *
     * @expectedException InvalidArgumentException
     s @expectedExceptionMessage offsetPath requires a valid path object
     */
    public function estOffsetPathException()
    {
        $p = new \Freesewing\Part();
        $p->offsetPath(1,2);
    }
    
    /** 
     * Tests the path offset code
     */
    public function estPathOffsetCode()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->newPoint(4,100,100);
        $p->newPoint(5,150,100);
        $p->newPoint(6,200,100);
        $p->newPoint(7,200,150);
        $p->newPoint(8,100,150);
        $p->newPoint(9,95,55);
        $p->newPath('original', 'M 1 L 2 L 3 C 4 4 5 C 6 6 7 L 8 L 9 z');
        $p->offsetPath('offset','original', 25);

        $this->assertEquals(serialize($p->paths),$this->loadFixture('offset.1'));
    }
    
    
    /** 
     * Tests the curvesCross method
     */
    public function testNewWidthDimension()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->newPoint(4,150,100);
        $this->assertEquals($p->curvesCross(1,2,3,4,3,4,1,2), null);
//        $this->assertEquals($p->curvedimensions),$this->loadFixture('widthDimensions'));
    }
    
    /** 
     * Tests the newWidhtDimension method
     */
    public function estNewWidthDimension()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->newPoint(4,150,100);
        $p->newWidthDimension(1,2);
        $p->newWidthDimensionSm(3,4,150,'Test msg');
        $this->assertEquals(serialize($p->dimensions),$this->loadFixture('widthDimensions'));
    }
    
    /** 
     * Tests the newHeightDimension method
     */
    public function estNewHeightDimension()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->newPoint(4,150,100);
        $p->newHeightDimension(1,2);
        $p->newHeightDimensionSm(3,4,150,'Test msg');
        $this->assertEquals(serialize($p->dimensions),$this->loadFixture('heightDimensions'));
    }
    
    /** 
     * Tests the newLinearDimension method
     */
    public function estNewLinearDimension()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->newPoint(4,100,100);
        $p->newLinearDimension(1,2);
        $p->newLinearDimensionSm(3,4,25,'Test msg');
        $this->assertEquals(serialize($p->dimensions),$this->loadFixture('linearDimensions'));
    }
    
    /** 
     * Tests the newCurvedDimension method
     */
    public function estNewCurvedDimension()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->newPoint(4,100,100);
        $p->newCurvedDimension('M 1 C 2 3 4');
        $p->newCurvedDimension('M 2 C 1 3 4', 25, 'Test msg');
        $this->assertEquals(serialize($p->dimensions),$this->loadFixture('curvedDimensions'));
    }
    
    /** 
     * Tests the notch method
     */
    public function estNotch()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->notch([1,2,3]);

        $this->saveFixture('notch', serialize($p->snippets));
        $this->assertEquals(serialize($p->snippets),$this->loadFixture('notch'));
    }
    
    private function loadFixture($fixture)
    {
        $dir = 'tests/src/fixtures';
        $file = "$dir/Part.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        $dir = 'tests/src/fixtures';
        $file = "$dir/Part.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
