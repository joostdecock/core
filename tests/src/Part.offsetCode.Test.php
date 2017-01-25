<?php

namespace Freesewing\Tests;

class PartOffsetCode extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        //$this->startTime = time();
    }

    public function tearDown()
    {
        //$this->time('at teardown');
    }

    private function time($s='')
    {
        //$this->now = time();
        //echo "\n".(time() - $this->startTime)." seconds: ".$s;
    }
    
    /**
     * Test offset of a line
     */
    public function testLineOffset()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,10,0);
        $p->offsetPathString(1,'M 1 L 2', 10);
        $this->assertEquals(serialize($p->paths),$this->loadFixture('lineOffset'));
    }

    /** 
     * Tests that offsetPath throws exception when path id can't be found
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage offsetPath requires a valid path object
     */
    public function testOffsetPathException()
    {
        $p = new \Freesewing\Part();
        $p->offsetPath(1,2, 10);
    }

    /** 
     * Tests that offsetPathString throws exception for invalid path
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not offset pathstring: M 1 L 1
     */
    public function testOffsetPathStringException()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->offsetPathString(1,'M 1 L 1', 10);
    }

    /**
     * Test clonePoint returns false on non-existing point
     */
    public function testClonePointForNonExistingPoint()
    {
        $p = new \Freesewing\Part();
        $this->assertFalse($p->clonePoint(1,2));
    }

    /**
     * Test offset of a curve
     */
    public function testCurveOffset()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,0,100);
        $p->newPoint(4,100,100);
        $p->offsetPathString(1,'M 1 C 2 4 3', 10);
        $this->assertEquals(serialize($p->paths),$this->loadFixture('curveOffset'));
    }

    /**
     * Test offset of a curve where cp1 = Start
     */
    public function testCurveOffsetCp1IsStart()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,0,100);
        $p->offsetPathString(1,'M 1 C 1 2 3', 10);
        $this->assertEquals(serialize($p->paths),$this->loadFixture('curveOffsetCp1IsStart'));
    }

    /**
     * Test offset of a curve where cp2 = End
     */
    public function testCurveOffsetCp2IsEnd()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,0,100);
        $p->offsetPathString(1,'M 1 C 2 3 3', 10);
        $this->assertEquals(serialize($p->paths),$this->loadFixture('curveOffsetCp2IsEnd'));
    }

    /**
     * Test offset of a closed path with lines
     */
    public function testLineOffsetClosedPath()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,0,100);
        $p->offsetPathString(1,'M 1 L 2 L 3 z', 10);
        $this->assertEquals(serialize($p->paths),$this->loadFixture('lineOffsetClosedPath'));
    }

    /**
     * Tests offset with intersection of two curves
     */
    public function testOffsetWithTwoCurvesIntersecting()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,100,100);
        $p->newPoint(4,0,100);
        $p->newPoint(5,0,50);
        $p->newPoint(6,100,50);
        $p->offsetPathString(1,'M 1 C 6 6 2 L 3 C 5 5 4 z', 10);
        $this->saveFixture('offsetWithTwoCurvesIntersecting',serialize($p->paths));
        $this->assertEquals(serialize($p->paths),$this->loadFixture('offsetWithTwoCurvesIntersecting'));
    }


    private function loadFixture($fixture)
    {
        $dir = 'tests/src/fixtures';
        $file = "$dir/Part.offset.$fixture.data";
        return file_get_contents($file);
    }

    private function saveFixture($fixture, $data)
    {
        // use as 
        //$this->saveFixture('grainline',serialize($p->dimensions));
        $dir = 'tests/src/fixtures';
        $file = "$dir/Part.offset.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
