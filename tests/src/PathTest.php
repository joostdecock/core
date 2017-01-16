<?php

namespace Freesewing\Tests;

class PathTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Path');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['boundary'],
            ['sample'],
            ['render'],
            ['attributes'],
            ['path'],
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
        $path = new \Freesewing\Path();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $path->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $path->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        $boundary = new \Freesewing\Boundary();
        $topLeft = new \Freesewing\Point();
        $topLeft->setX(-10);
        $topLeft->setY(-20);
        $bottomRight = new \Freesewing\Point();
        $bottomRight->setX(310);
        $bottomRight->setY(420);
        $boundary->setTopLeft($topLeft);
        $boundary->setBottomRight($bottomRight);
        $path = 'M 1 L 2 L 3 C 4 5 6 z';
        $attr = ['class' => 'seam-allowance', 'transform' => 'inherit'];
        return [
            ['Sample', false],
            ['Sample', true],
            ['Render', false],
            ['Render', true],
            ['Boundary', $boundary],
            ['Path', $path],
            ['Attributes', $attr],
        ];
    }

    /**
     * Test the breakUp method
     */
    public function testBreakUp()
    {
        $path = new \Freesewing\Path();

        $expected = [
            ['type' => 'L', 'path' => 'M 1 L 2'],
            ['type' => 'L', 'path' => 'M 2 L 3'],
            ['type' => 'C', 'path' => 'M 3 C 4 5 6'],
            ['type' => 'L', 'path' => 'M 6 L 1'],
        ];
        
        $path->setPath(' M  1  L   2 L 3     C 4  5 6 z  ');
        $this->assertEquals($expected, $path->breakUp()); 
        
    }
    /**
     * Test the isClosed method
     */
    public function testIsClosed()
    {
        $path = new \Freesewing\Path();
        
        $path->setPath(' M  1  L   2 L 3     C 4  5 6 z  ');
        $this->assertEquals(true, $path->isClosed()); 
        
        $path->setPath(' M  1  L   2 L 3     C 4  5 6   ');
        $this->assertEquals(false, $path->isClosed()); 
    }
    
    /**
     * Test the getStartPoint and getEndPoint methods
     */
    public function testGetStartPointGetEndPoint()
    {
        $path = new \Freesewing\Path();
        $path->setPath('   M   1  L   2 L 3     C 4  5 6   ');
        $this->assertEquals(1, $path->getStartPoint()); 
        $this->assertEquals(6, $path->getEndPoint()); 

    }
    
    /**
     * Test the auto-cleanup of pathstrings
     */
    public function testPathstringAutoCleaneUp()
    {
        $path = new \Freesewing\Path();
        $path->setPath('   M   1  L   2 L 3     C 4  5 6  z  ');
        $this->assertEquals('M 1 L 2 L 3 C 4 5 6 z', $path->getPath()); 

    }
    
    /**
     * Test the setAttribute and getAttribute methods
     */
    public function testSetAttributeGetAttribute()
    {
        $path = new \Freesewing\Path();
        $path->setAttribute('class', 'seam-allowance');
        $this->assertEquals($path->getAttribute('class'), 'seam-allowance'); 
        $this->assertEquals($path->getAttribute('foo'), null); 

    }
    
    /**
     * Test the findBoundary method
     */
    public function testFindBoundary()
    {
        $p = new \Freesewing\Part();

        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 100, 100);
        $p->newPoint(3, -400, -20);
        $p->newPoint(4, 4320, 20);
        $p->newPoint(5, 540, 430);
        $p->newPoint(6, -540, -430);

        $path = new \Freesewing\Path();
     
        $path->setPath('M 1 L 3 L 4 C 4 5 6 C 1 5 3');

        $boundary = new \Freesewing\Boundary();
        $topLeft = new \Freesewing\Point();
        $topLeft->setX(-540);
        $topLeft->setY(-430);
        $bottomRight = new \Freesewing\Point();
        $bottomRight->setX(4320);
        $bottomRight->setY(171.16);
        $boundary->setTopLeft($topLeft);
        $boundary->setBottomRight($bottomRight);

        $this->assertEquals($path->findBoundary($p), $boundary); 
    }
}
