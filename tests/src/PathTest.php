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
            ['path'],
            ['options'],
            ['boundary'],
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
        $object = new \Freesewing\Path();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $object->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet() 
    {
        return [
            ['Path', 'M 52 L 69 L sorcha z'],
            ['Options', ['class' => ['freesewing', 'sorcha'] ] ],
        ];
    }
    
    /**
     * @param \Freesewing\Path $path The path for which we are calculating the boundary
     * @param \Freesewing\Part $part The part to which the path belongs 
     * @param \Freesewing\Boundary $expectedresult Result to check for, a boundary object
     *
     * @dataProvider providerFindBoundary
     */
    public function testFindBoundary($path, $part, $expectedResult)
    {
        $boundary = $path->findBoundary($part);
        $this->assertEquals($expectedResult, $boundary);
    }

    public function providerFindBoundary() 
    {
        $tests = [
            1 => [
                'path' => 'M 1 L 2 L 3 L 4 z',
                'topLeft' => [0, 0],
                'bottomRight' => [100, 100],
            ],
            2 => [
                'path' => 'M 1 L 5',
                'topLeft' => [0, 0],
                'bottomRight' => [50, 50],
            ],
            3 => [
                'path' => 'M 1 C 2 3 3 L 4 z',
                'topLeft' => [0, 0],
                'bottomRight' => [100, 100],
            ],
            4 => [
                'path' => 'M 3 C 4 1 5 z',
                'topLeft' => [17.161049999999999, 36.0],
                'bottomRight' => [100, 100],
            ],
        ];

        $return = array();
        foreach($tests as $index => $test) {
            $part = new \Freesewing\Part('Test 1');
            
            $p1 = new \Freesewing\Point();
            $p2 = new \Freesewing\Point();
            $p3 = new \Freesewing\Point();
            $p4 = new \Freesewing\Point();
            $p5 = new \Freesewing\Point();

            $p1->setX(0);
            $p1->setY(0);
            $p1->setDescription('Top left');
            $p2->setX(100);
            $p2->setY(0);
            $p2->setDescription('Top right');
            $p3->setX(100);
            $p3->setY(100);
            $p3->setDescription('Bottom right');
            $p4->setX(0);
            $p4->setY(100);
            $p4->setDescription('Bottom left');
            $p5->setX(50);
            $p5->setY(50);
            $p5->setDescription('center');

            $part->addPoints([ 
                1 => $p1, 
                2 => $p2, 
                3 => $p3, 
                4 => $p4, 
                5 => $p5,
            ]);

            $path = new \Freesewing\Path();
            $path->setPath($test['path']);
            $part->addPath($index, $path);

            $topLeft = new \Freesewing\Point();
            $topLeft->setX($test['topLeft'][0]);
            $topLeft->setY($test['topLeft'][1]);
            $bottomRight = new \Freesewing\Point();
            $bottomRight->setX($test['bottomRight'][0]);
            $bottomRight->setY($test['bottomRight'][1]);

            $boundary = new \Freesewing\Boundary();
            $boundary->setTopLeft($topLeft);
            $boundary->setBottomRight($bottomRight);
            array_push($return, [$path, $part, $boundary]);
        }
        return $return;
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage SVG path references non-existing point
     */
    public function testExceptionUnsupportedSvgPathCommand()
    {
        $tests = [
            1 => [
                'path' => 'M 1 L 2 J 3 L 4 z',
                'topLeft' => [0, 0],
                'bottomRight' => [100, 100],
            ],
        ];

        foreach($tests as $index => $test) {
            $part = new \Freesewing\Part('Test 1');
            
            $p1 = new \Freesewing\Point();
            $p2 = new \Freesewing\Point();
            $p3 = new \Freesewing\Point();
            $p4 = new \Freesewing\Point();
            $p5 = new \Freesewing\Point();

            $p1->setX(0);
            $p1->setY(0);
            $p1->setDescription('Top left');
            $p2->setX(100);
            $p2->setY(0);
            $p2->setDescription('Top right');
            $p3->setX(100);
            $p3->setY(100);
            $p3->setDescription('Bottom right');
            $p4->setX(0);
            $p4->setY(100);
            $p4->setDescription('Bottom left');
            $p5->setX(50);
            $p5->setY(50);
            $p5->setDescription('center');
            
            $part->addPoints([ 
                1 => $p1, 
                2 => $p2, 
                3 => $p3, 
                4 => $p4, 
                5 => $p5,
            ]);

            $path = new \Freesewing\Path();
            $path->setPath($test['path']);
            $part->addPath($index, $path);

            $topLeft = new \Freesewing\Point();
            $topLeft->setX($test['topLeft'][0]);
            $topLeft->setY($test['topLeft'][1]);
            $bottomRight = new \Freesewing\Point();
            $bottomRight->setX($test['bottomRight'][0]);
            $bottomRight->setY($test['bottomRight'][1]);
            $boundary = new \Freesewing\Boundary();
            $boundary->setTopLeft($topLeft);
            $boundary->setBottomRight($bottomRight);
            $path->findBoundary($part);
        }
    }

}
