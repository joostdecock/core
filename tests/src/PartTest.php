<?php

namespace Freesewing\Tests;

class PartTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
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
    public function testGettersReturnWhatSettersSet($methodSuffix, $expectedResult)
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
    public function testUnit()
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
    public function testNewNoteEdgeCase()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newNote('test',1,'Test note', 123, 20, 2, ['dx' => 2]);
        $this->assertEquals(serialize($p->notes['test']),$this->loadFixture('note'));
    }

    /** 
     * Tests the addTitle method
     */
    public function testAddTitle()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);

        $p->addTitle(1,1,'Test title', 'Message', 'default');
        $this->assertEquals($this->loadFixture('title.default'),serialize($p->texts));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'vertical');
        $this->assertEquals($this->loadFixture('title.vertical'),serialize($p->texts));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'vertical-small');
        $this->assertEquals($this->loadFixture('title.verticalSmall'),serialize($p->texts));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'horizontal');
        $this->assertEquals($this->loadFixture('title.horizontal'),serialize($p->texts));

        unset($p->texts);
        $p->addTitle(1,1,'Test title', 'Message', 'horizontal-small');
        $this->assertEquals($this->loadFixture('title.horizontalSmall'),serialize($p->texts));
    }

    /** 
     * Tests the hasPathToRender method
     */
    public function testHasPathToRender()
    {
        $p = new \Freesewing\Part();
        $this->assertFalse($p->hasPathToRender());
        $p->newPath('test', 'M 1 L 2');
        $this->assertTrue($p->hasPathToRender());
    }

    /** 
     * Tests that loadPoint throws exception when point does not exist
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot load point 1, it does not exist
     */
    public function testLoadPointException()
    {
        $p = new \Freesewing\Part();
        $p->loadPoint(1);
    }
    
    /** 
     * Tests that offsetPath throws exception when path object is missing
     *
     * @expectedException InvalidArgumentException
     s @expectedExceptionMessage offsetPath requires a valid path object
     */
    public function testOffsetPathException()
    {
        $p = new \Freesewing\Part();
        $p->offsetPath(1,2);
    }
    
    /** 
     * Tests that shiftAlong throws exception when shift is longer than path
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Ran out of curve to move along
     */
    public function testShiftAlongException()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,10,0);
        $p->newPoint(3,10,10);
        $p->newPoint(4,20,20);
        $p->shiftAlong(1,2,3,4,500);
    }
    
    /** 
     * Tests the curveLe method
     */
    public function testCurveLen()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,10,0);
        $p->newPoint(3,10,10);
        $p->newPoint(4,20,20);
        $this->assertEquals($p->curveLen(1,2,3,4),29.451086905068575);
    }

    /** 
     * Tests the curvesCross method
     */
    public function testNewCurvedDimension()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,0,100);
        $p->newPoint(4,100,100);
        $p->newCurvedDimension('M 1 L 2', 10);
        $this->assertEquals(serialize($p->dimensions),$this->loadFixture('newCurvedDimension'));
    }

    /** 
     * Tests the curvesCross method
     */
    public function testCurvesCross()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,0,100);
        $p->newPoint(4,100,100);
        $p->curvesCross(1,2,3,4,2,1,3,4,'test');
        $p1 = new \Freesewing\Point();
        $p1->setX(43.75);
        $p1->setY(15.625);
        $this->assertEquals($p->points['test-1'],$p1);
    }
    
    /** 
     * Tests the curveCrossesLine method
     */
    public function testCurveCrossesLine()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,0,100);
        $p->newPoint(4,100,100);
        $p->curveCrossesLine(1,2,3,4,2,3,'test');
        $p1 = new \Freesewing\Point();
        $p1->setX(50);
        $p1->setY(50);
        $this->assertEquals($p->points['test1'],$p1);
    }
    
    /** 
     * Tests the curveEdge methods
     */
    public function testCurveEdge()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,50,50);
        $p->newPoint(2,40,0);
        $p->newPoint(3,80,100);
        $left = $p->curveEdge(1,2,3,1,'left');
        $right = $p->curveEdge(1,2,3,1,'right');
        $top = $p->curveEdge(1,2,3,1,'top');
        $bottom = $p->curveEdge(1,2,3,1,'bottom');
        
        $expect = new \Freesewing\Point();
        $expect->setX(48.353);
        $expect->setY(37.962);
        $this->assertEquals($left, $expect);
        
        $expect->setX(61.37);
        $expect->setY(63.306);
        $this->assertEquals($right, $expect);

        $expect->setX(49.204);
        $expect->setY(35.567);
        $this->assertEquals($top, $expect);

        $expect->setX(60.75);
        $expect->setY(64.433);
        $this->assertEquals($bottom, $expect);
    }

    /** 
     * Tests the curveCrossesXY methods
     */
    public function testCurveCrossesXY()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,50,50);
        $p->newPoint(2,40,0);
        $p->newPoint(3,80,100);
        $p->curveCrossesX(1,2,3,1,60,'testx');
        $px = new \Freesewing\Point();
        $px->setX(60);
        $px->setY(64.104);
        $this->assertEquals($p->points['testx1'],$px);
        $px->setY(56.943);
        $this->assertEquals($p->points['testx2'],$px);
        $p->curveCrossesY(1,2,3,1,55,'testy');
        $py = new \Freesewing\Point();
        $py->setX(53.081);
        $py->setY(55);
        $this->assertEquals($p->points['testy1'],$py);
        $py->setX(59.362);
        $this->assertEquals($p->points['testy2'],$py);
    }

    /** 
     * Tests the newWidhtDimension method
     */
    public function testNewWidthDimension()
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
    public function testNewHeightDimension()
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
    public function testNewLinearDimension()
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
     * Tests the pathLen method
     */
    public function testPathLen()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->newPoint(4,100,100);
        $p->newCurvedDimension('M 1 L 2 L 3 L 4');
        $label = $p->dimensions[0]->getLabel();
        $this->assertEquals($label->getText(),'17.07cm');
        $p->newCurvedDimension('M 1 C 2 3 4');
        $label = $p->dimensions[1]->getLabel();
        $this->assertEquals($label->getText(),'15.49cm');
    }
    /** 
     * Tests the notch method
     */
    public function testNotch()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,50);
        $p->notch([1,2,3]);

        $this->assertEquals(serialize($p->snippets),$this->loadFixture('notch'));
    }
    
    /** 
     * Tests the addBoundary method
     */
    public function testAddBoundary()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newWidthDimension(1,2,100);
        $p->newPath('test','M 1 L 2');
        $p->addBoundary();
        $boundary = new \Freesewing\Boundary();
        $topLeft = new \Freesewing\Point();
        $topLeft->setX(0);
        $topLeft->setY(0);
        $bottomRight = new \Freesewing\Point();
        $bottomRight->setX(50);
        $bottomRight->setY(100);
        $boundary->setTopLeft($topLeft);
        $boundary->setBottomRight($bottomRight);
        $this->assertEquals($p->boundary,$boundary);
    }
    
    /** 
     * Tests the newGrainline method
     */
    public function testNewGrainline()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newGrainline(1,2,'Test');
        $this->assertEquals(serialize($p->dimensions),$this->loadFixture('grainline'));
    }

    /** 
     * Tests the newCutOnFold method
     */
    public function testNewCutOnFold()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newCutOnFold(1,2,'Test');
        $this->assertEquals(serialize($p->dimensions),$this->loadFixture('cutOnFold'));
    }

    /** 
     * Tests the isPoint method
     */
    public function testIsPoint()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,20,30);
        $p->points[2] = 'test';
        $this->assertTrue($p->isPoint(1));
        $this->assertFalse($p->isPoint(2));
        $this->assertFalse($p->isPoint(3));
    }

    /** 
     * Tests the splitCurve method
     */
    public function testSpitCurve()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,100,100);
        $p->addPoint(4,$p->shiftAlong(1,2,2,3,50));

        $p->splitCurve(1,2,2,3,4);

        $xVals = [0,21,37.59,50.696,100,100,100,50.696];
        $yVals = [0,0,0,0.926,100,21,4.41,0.926];
        for($i=0;$i<8;$i++) {
            $this->assertEquals($xVals[$i], $p->points[$i+1]->getX());
            $this->assertEquals($yVals[$i], $p->points[$i+1]->getY());
        }

        $p->splitCurve(1,2,2,3,0.7,'test',true);
        $xVals = [0,70,91,97.3,100,100,100,97.3];
        $yVals = [0,0,0,34.3,100,70,49,34.3];
        for($i=0;$i<8;$i++) {
            $j = $i+1;
            $this->assertEquals($xVals[$i], $p->points["test$j"]->getX());
            $this->assertEquals($yVals[$i], $p->points["test$j"]->getY());
        }
    }

    /** 
     * Tests the beamsCross and linesCross method
     */
    public function testLinesCrossBeamsCross()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,25,25);
        $p->newPoint(3,0,60);
        $p->newPoint(4,25,35);
        $this->assertFalse($p->linesCross(1,2,3,4)); 
        $this->assertFalse($p->beamsCross(1,3,2,4)); 
        
        $expect = new \Freesewing\Point();
        $expect->setX(30);
        $expect->setY(30);
        $this->assertEquals($p->beamsCross(1,2,3,4), $expect);
        
        $p->newPoint(4,10,-10);
        $expect->setX(7.5);
        $expect->setY(7.5);
        $this->assertEquals($p->linesCross(1,2,3,4), $expect);
    }

    /** 
     * Tests the flipXY methods
     */
    public function testFlipXY()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,20,30);
        $expect = new \Freesewing\Point();
        $expect->setX(-20);
        $expect->setY(30);
        $flipped = $p->flipX(1);
        $flipped->setDescription('');
        $this->assertEquals($flipped,$expect);
        
        $flipped = $p->flipX(1,15);
        $flipped->setDescription('');
        $expect->setX(10);
        $this->assertEquals($flipped,$expect);
        
        $flipped = $p->flipY(1);
        $flipped->setDescription('');
        $expect->setX(20);
        $expect->setY(-30);
        $this->assertEquals($flipped,$expect);
        
        $flipped = $p->flipY(1,50);
        $flipped->setDescription('');
        $expect->setY(70);
        $this->assertEquals($flipped,$expect);
    }

    /** 
     * Tests the angle method
     */
    public function testAngle()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,10,0);
        $p->newPoint(3,0,10);
        $p->newPoint(4,-10,0);
        $p->newPoint(5,0,-10);
        $p->newPoint(6,10,10);
        $p->newPoint(7,-10,-10);

        $this->assertEquals($p->angle(1,1),0);
        $this->assertEquals($p->angle(2,1),0);
        $this->assertEquals($p->angle(1,3),90);
        $this->assertEquals($p->angle(1,4),0);
        $this->assertEquals($p->angle(1,5),270);
        $this->assertEquals($p->angle(1,6),135);
        $this->assertEquals($p->angle(1,7),315);
    }

    /** 
     * Tests the shiftTowards method
     */
    public function testShiftTowards()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(0,50,50);
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,100,100);
        $p->newPoint(4,0,100);

        $p->addPoint(10,$p->shiftTowards(0,1,40));
        $p->addPoint(11,$p->shiftTowards(0,2,40));
        $p->addPoint(12,$p->shiftTowards(0,3,40));
        $p->addPoint(13,$p->shiftTowards(0,4,40));
        $p->addPoint(14,$p->shiftTowards(1,2,40));
        $p->addPoint(15,$p->shiftTowards(2,1,40));
        $p->addPoint(16,$p->shiftTowards(1,4,40));
        $p->addPoint(17,$p->shiftTowards(4,1,40));
        
        $this->assertEquals($p->points[10]->getX(),21.716);
        $this->assertEquals($p->points[11]->getX(),78.284);
        $this->assertEquals($p->points[12]->getX(),78.284);
        $this->assertEquals($p->points[13]->getX(),21.716);
        $this->assertEquals($p->points[14]->getX(),40);
        $this->assertEquals($p->points[15]->getX(),60);
        $this->assertEquals($p->points[16]->getX(),0);
        $this->assertEquals($p->points[17]->getX(),0);
        
        $this->assertEquals($p->points[10]->getY(),21.716);
        $this->assertEquals($p->points[11]->getY(),21.716);
        $this->assertEquals($p->points[12]->getY(),78.284);
        $this->assertEquals($p->points[13]->getY(),78.284);
        $this->assertEquals($p->points[14]->getY(),0);
        $this->assertEquals($p->points[15]->getY(),0);
        $this->assertEquals($p->points[16]->getY(),40);
        $this->assertEquals($p->points[17]->getY(),60);
    }

    private function loadFixture($fixture)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/src/fixtures';
        $file = "$dir/Part.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        $dir = \Freesewing\Utils::getApiDir().'/tests/src/fixtures';
        $file = "$dir/Part.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
