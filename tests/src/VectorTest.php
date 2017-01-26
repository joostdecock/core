<?php

namespace Freesewing\Tests;

use \Freesewing\Vector;

class VectorTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp()
    {
        $this->v1 = new Vector();
        $this->v1->setX(0);
        $this->v1->setY(0);

        $this->v2 = new Vector();
        $this->v2->setX(100);
        $this->v2->setY(100);

        $this->v3 = new Vector();
        $this->v3->setX(200);
        $this->v3->setY(200);

        $this->v4 = new Vector();
        $this->v4->setX(-123);
        $this->v4->setY(123);
    }

    /**
     * Tests the clone method
     */
    public function testClone()
    {
        $v = clone $this->v2;
        $this->assertEquals($this->v2->asPoint(), $v->asPoint());
    }

    /**
     * Tests the multiply method
     */
    public function testMultiply()
    {
        $this->assertEquals($this->v2->multiply(2), $this->v3);
    }

    /**
     * Tests the dot method
     */
    public function testDot()
    {
        $this->assertEquals($this->v2->dot($this->v3), 40000);
    }

    /**
     * Tests the add method
     */
    public function testAdd()
    {
        $v = new Vector();
        $v->setX(300);
        $v->setY(300);
        $this->assertEquals($this->v2->add($this->v3), $v);
    }

    /**
     * Tests the lerp method
     */
    public function testLerp()
    {
        $v = new Vector();
        $v->setX(170);
        $v->setY(170);
        $this->assertEquals($this->v2->lerp($this->v3,0.7), $v);
    }

    /**
     * Tests the min method
     */
    public function testMin()
    {
        $v = new Vector();
        $v->setX(-123);
        $v->setY(100);
        $this->assertEquals($this->v2->min($this->v4), $v);
    }

    /**
     * Tests the max method
     */
    public function testMax()
    {
        $v = new Vector();
        $v->setX(100);
        $v->setY(123);
        $this->assertEquals($this->v2->max($this->v4), $v);
    }

    /**
     * Tests the gte and lte methods
     */
    public function testGteLte()
    {
        $this->assertEquals($this->v2->gte($this->v1), true);
        $this->assertEquals($this->v2->lte($this->v1), false);
    }
}
