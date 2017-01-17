<?php

namespace Freesewing\Tests;

use \Freesewing\BezierToolbox;
use \Freesewing\Point;
use \Freesewing\Boundary;

class BezierToolboxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the findBezierBoundary method
     */
    public function testFindBezierBoundary()
    {
        $start = new Point();
        $cp1 = new Point();
        $cp2 = new Point();
        $end = new Point();
        $topLeft = new Point();
        $bottomRight = new Point();

        $start->setX(50);
        $start->setY(50);

        $cp1->setX(0);
        $cp1->setY(0);

        $cp2->setX(0);
        $cp2->setY(100);

        $end->setX(100);
        $end->setY(100);

        $topLeft->setX(17.161000000000001);
        $topLeft->setY(36);

        $expected = new Boundary(); 
        $expected->setTopLeft($topLeft);
        $expected->setBottomRight($end);

        $this->assertEquals(BezierToolbox::findBezierBoundary($start,$cp1,$cp2,$end), $expected);
    }

    /**
     * Tests the findBezierEdge method
     */
    public function testFindBezierEdge()
    {
        $start = new Point();
        $cp1 = new Point();
        $cp2 = new Point();
        $end = new Point();

        $start->setX(50);
        $start->setY(50);

        $cp1->setX(0);
        $cp1->setY(0);

        $cp2->setX(0);
        $cp2->setY(100);

        $end->setX(100);
        $end->setY(100);

        $leftEdge = new Point();
        $rightEdge = new Point();
        $topEdge = new Point();
        $bottomEdge = new Point();
        
        $leftEdge->setX(17.161000000000001);
        $leftEdge->setY(46.914999999999999);

        $rightEdge->setX(100);
        $rightEdge->setY(100);

        $topEdge->setX(100);
        $topEdge->setY(100);

        $bottomEdge->setX(100);
        $bottomEdge->setY(100);

        $this->assertEquals(BezierToolbox::findBezierEdge($start,$cp1,$cp2,$end,'left'), $leftEdge);
    }

    /**
     * Tests the cubicBezierLength method
     */
    public function testCubicBezierLength()
    {
        $start = new Point();
        $cp1 = new Point();
        $cp2 = new Point();
        $end = new Point();

        $start->setX(50);
        $start->setY(50);

        $cp1->setX(0);
        $cp1->setY(0);

        $cp2->setX(0);
        $cp2->setY(100);

        $end->setX(100);
        $end->setY(100);

        $this->assertEquals(BezierToolbox::cubicBezierLength($start,$cp1,$cp2,$end), 151.80277303164098);
    }

    /**
     * Tests the findLineCurveIntersections method
     */
    public function testFindLineCurveIntersections()
    {
        $start = new Point();
        $cp1 = new Point();
        $cp2 = new Point();
        $end = new Point();
        $l1 = new Point();
        $l2 = new Point();
        $i1 = new Point();
        $i2 = new Point();

        $start->setX(50);
        $start->setY(50);
        $cp1->setX(0);
        $cp1->setY(0);
        $cp2->setX(0);
        $cp2->setY(100);
        $end->setX(100);
        $end->setY(100);
        
        $l1->setX(0);
        $l1->setY(50);
        $l2->setX(100);
        $l2->setY(50);

        $i1->setX(50);
        $i1->setY(50);
        $i2->setX(17.318999999999999);
        $i2->setY(50);
        $this->assertEquals(BezierToolbox::findLineCurveIntersections($l1,$l2,$start,$cp1,$cp2,$end), [$i1,$i2]);
        
        $l1->setX(50);
        $l1->setY(0);
        $l2->setX(50);
        $l2->setY(100);
        
        $i1->setX(50);
        $i1->setY(89.204999999999998);
        $this->assertEquals(BezierToolbox::findLineCurveIntersections($l1,$l2,$start,$cp1,$cp2,$end), [$i1]);

        $l2->setX(50);
        $l2->setY(-100);
        $this->assertEquals(BezierToolbox::findLineCurveIntersections($l1,$l2,$start,$cp1,$cp2,$end), false);
    }
    
    /**
     * Tests the findBezierCoeffs method
     */
    public function testFindBezierCoeffs()
    {
        $this->assertEquals(BezierToolbox::bezierCoeffs(10,20,30,40), [0,0,30,10]);
    }

    /**
     * Tests the cubicRoots method
     */
    public function testCubicRoots()
    {
        $this->assertEquals(BezierToolbox::cubicRoots([0.1,2.3,3.4,1.2]), [-1,-1,-1]);
        
        $this->assertEquals(BezierToolbox::cubicRoots([1,1,1,4]), [-1,-1,-1]);
    }

    /**
     * Tests the sgn method
     */
    public function testSngRoots()
    {
        $this->assertEquals(BezierToolbox::sgn(-12), -1);
        $this->assertEquals(BezierToolbox::sgn(52), 1);
    }

    /**
     * Tests the sortSpecial method
     */
    public function testSortSpecial()
    {
        $this->assertEquals(BezierToolbox::sortSpecial([12,2,-1,5,1]), [1,2,5,12,-1]);
    }
    
    /**
     * Tests the cubicBezierDelta method
     */
    public function testCubicBezierDelta()
    {
        $start = new Point();
        $cp1 = new Point();
        $cp2 = new Point();
        $end = new Point();
        $i1 = new Point();

        $start->setX(50);
        $start->setY(50);
        $cp1->setX(0);
        $cp1->setY(0);
        $cp2->setX(0);
        $cp2->setY(100);
        $end->setX(100);
        $end->setY(100);

        $i1->setX(17.318999999999999);
        $i1->setY(50);
        $this->assertEquals(BezierToolbox::cubicBezierDelta($start,$cp1,$cp2,$end,$i1), 0.44);
        
    }
    
    /**
     * Tests the calculateSplitCurvePoints method
     */
    public function testCalculateSplitCurvePoints()
    {
        $start = new Point();
        $cp1 = new Point();
        $cp2 = new Point();
        $end = new Point();

        $start->setX(50);
        $start->setY(50);
        $cp1->setX(0);
        $cp1->setY(0);
        $cp2->setX(0);
        $cp2->setY(100);
        $end->setX(100);
        $end->setY(100);

        $p1 = new Point();
        $p2 = new Point();
        $p3 = new Point();

        $p1->setX(28);
        $p1->setY(28);
        $p2->setX(15.68);
        $p2->setY(35.039999999999999);
        $p3->setX(17.298999999999999);
        $p3->setY(49.823999999999998);

        $this->assertEquals(BezierToolbox::calculateSplitCurvePoints($start,$cp1,$cp2,$end,0.44),[$start,$p1,$p2,$p3]);
        
    }
    
    /**
     * Tests the bezierCircle method
     */
    public function testBezierCircle()
    {
        $this->assertEquals(BezierToolbox::bezierCircle(100),55.228474983079359);
    }
    
    /**
     * Tests the findCurveCurveIntersections method
     */
    public function testFindCurveCurveIntersections()
    {
        $start = new Point();
        $cp1 = new Point();
        $cp2 = new Point();
        $end = new Point();

        $start->setX(50);
        $start->setY(50);
        $cp1->setX(0);
        $cp1->setY(0);
        $cp2->setX(0);
        $cp2->setY(100);
        $end->setX(100);
        $end->setY(100);

        $i1 = new Point();
        $i2 = new Point();

        $i1->setX(38.110999999999997);
        $i1->setY(40.186999999999998);
        $i2->setX(51.357999999999997);
        $i2->setY(89.882999999999996);

        $this->assertEquals(BezierToolbox::findCurveCurveIntersections($start,$cp1,$cp2,$end,$cp1,$start,$end,$cp2),[$i1,$i2]);

        $startB = new Point();
        $endB = new Point();
        
        $startB->setX(-100);
        $startB->setY(0);
        $endB->setX(-100);
        $endB->setY(100);

        $this->assertEquals(BezierToolbox::findCurveCurveIntersections($startB,$cp1,$endB,$endB,$start,$cp1,$cp2,$end),false);
        
    }
    
}
