<?php

namespace Freesewing\Tests;

class PartOffsetCode extends \PHPUnit\Framework\TestCase
{
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
        $this->saveFixture('curveOffset', serialize($p->paths));
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
        $this->saveFixture('curveOffsetCp1IsStart', serialize($p->paths));
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
        $this->saveFixture('curveOffsetCp2IsEnd', serialize($p->paths));
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
        $this->saveFixture('lineOffsetClosedPath', serialize($p->paths));
        $this->assertEquals(serialize($p->paths),$this->loadFixture('lineOffsetClosedPath'));
    }

    /**
     * Tests line-line offset
     */
    public function testOffsetLineLine()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,50,0);
        $p->newPoint(3,100,0);
        $p->offsetPathString(1,'M 1 L 2 L 3 ', 10); // Lines are parallel, no gap, no intersections
        $this->saveFixture('offsetLineLine', serialize($p->paths));
        $this->assertEquals(serialize($p->paths),$this->loadFixture('offsetLineLine'));
    }

    /**
     * Tests line-curve offset
     */
    public function testOffsetLineCurve()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,100,100);
        $p->newPoint(4,0,100);
        $p->offsetPathString(1,'M 1 L 2 C 3 4 4', 10); // Inside offset, no gap
        $p->offsetPathString(2,'M 1 L 2 C 3 4 4', -10); // Outside offset, gap, cp2 = end
        $p->offsetPathString(3,'M 1 L 2 C 2 3 4', -10); // Outside offset, gap, start = cp1
        $this->saveFixture('offsetLineCurve', serialize($p->paths));
        $this->assertEquals(serialize($p->paths),$this->loadFixture('offsetLineCurve'));
    }

    /**
     * Tests curve-line offset
     */
    public function testOffsetCurveLine()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,100,100);
        $p->newPoint(4,0,100);
        $p->offsetPathString(1,'M 4 C 4 3 2 L 1', -10); // Inside offset, no gap
        $p->newPoint(1,0,100);
        $p->offsetPathString(2,'M 4 C 4 3 2 L 1', 10); // Outside offset, gap, cp2 = end
        $p->offsetPathString(3,'M 4 C 3 2 2 L 1', 10); // Outside offset, gap, start = cp1
        $this->saveFixture('offsetCurveLine', serialize($p->paths));
        $this->assertEquals(serialize($p->paths),$this->loadFixture('offsetCurveLine'));
    }

    /**
     * Tests curve-curve offset
     */
    public function testOffsetCurveCurve()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,100,100);
        $p->newPoint(4,200,100);
        $p->newPoint(5,200,200);
        $p->offsetPathString(1,'M 1 C 2 3 3 C 3 4 5', 10); 
        $this->saveFixture('offsetCurveCurve', serialize($p->paths));
        $this->assertEquals(serialize($p->paths),$this->loadFixture('offsetCurveCurve'));
    }

    /**
     * Tests offset with intersection of two curves
     */
    public function estOffsetWithTwoCurvesIntersecting()
    {
        $p = new \Freesewing\Part();
        $p->newPoint(1,0,0);
        $p->newPoint(2,100,0);
        $p->newPoint(3,100,100);
        $p->newPoint(4,0,100);
        $p->newPoint(5,0,50);
        $p->newPoint(6,100,50);
        $p->offsetPathString(1,'M 1 C 6 6 2 L 3 C 5 5 4 z', 10);
        $this->saveFixture('offsetWithTwoCurvesIntersecting', serialize($p->paths));
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
        return true;
        $dir = 'tests/src/fixtures';
        $file = "$dir/Part.offset.$fixture.data";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
