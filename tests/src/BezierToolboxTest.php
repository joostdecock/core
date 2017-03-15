<?php

namespace Freesewing\Tests;

use \Freesewing\BezierToolbox;
use \Freesewing\Point;
use \Freesewing\Boundary;

class BezierToolboxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the bezierBoundary method
     */
    public function testBezierBoundary()
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

        $this->assertEquals(BezierToolbox::bezierBoundary($start,$cp1,$cp2,$end), $expected);
    }

    /**
     * Tests the bezierEdge method
     */
    public function testBezierEdge()
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

        $this->assertEquals(BezierToolbox::bezierEdge($start,$cp1,$cp2,$end,'left'), $leftEdge);
    }

    /**
     * Tests the bezierLength method
     */
    public function testBezierLength()
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

        $this->assertEquals(BezierToolbox::bezierLength($start,$cp1,$cp2,$end), 151.80277303164098);
    }

    /**
     * Tests the bezierLineIntersections method
     */
    public function testBezierLineIntersections()
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
        $this->assertEquals(BezierToolbox::bezierLineIntersections($l1,$l2,$start,$cp1,$cp2,$end), [$i1,$i2]);
        
        $l1->setX(50);
        $l1->setY(0);
        $l2->setX(50);
        $l2->setY(100);
        
        $i1->setX(50);
        $i1->setY(89.204999999999998);
        $this->assertEquals(BezierToolbox::bezierLineIntersections($l1,$l2,$start,$cp1,$cp2,$end), [$i1]);

        $l2->setX(50);
        $l2->setY(-100);
        $this->assertEquals(BezierToolbox::bezierLineIntersections($l1,$l2,$start,$cp1,$cp2,$end), false);
    }
    
    /**
     * Tests the bezierPoint method
     */
    public function testBezierPoint()
    {
        $this->assertEquals(BezierToolbox::bezierPoint(0.7,10,20,80,90), 70.200000000000003);
    }
    
    /**
     * Tests the bezierCircle method
     */
    public function testBezierCircle()
    {
        $this->assertEquals(BezierToolbox::bezierCircle(100),55.228474983079359);
    }
    
    /**
     * Tests the bezierBezierIntersections method
     */
    public function testBezierBezierIntersections()
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

        $this->assertEquals(BezierToolbox::bezierBezierIntersections($start,$cp1,$cp2,$end,$cp1,$start,$end,$cp2),[$i1,$i2]);

        $startB = new Point();
        $endB = new Point();
        
        $startB->setX(-100);
        $startB->setY(0);
        $endB->setX(-100);
        $endB->setY(100);

        $this->assertEquals(BezierToolbox::bezierBezierIntersections($startB,$cp1,$endB,$endB,$start,$cp1,$cp2,$end),false);
        
    }
    
}
