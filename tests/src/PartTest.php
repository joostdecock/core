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
            ['paths'],
            ['transforms'],
            ['title'],
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
        $object = new \Freesewing\Part();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $object->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $object->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet() 
    {
        return [
            ['Title', 'Some example title'],
        ];
    }
    
    /**
     * @param string $key Point key
     * @param string $point Point object
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerAddPoint
     */
    public function testAddPoint($key, $point, $expectedResult)
    {
        $part = new \Freesewing\Part();
        $part->addPoint($key, $point);
        if(isset($expectedResult[0])) $this->assertEquals($expectedResult[0], $part->points[$key]->getX());
        if(isset($expectedResult[1])) $this->assertEquals($expectedResult[1], $part->points[$key]->getY());
        if(isset($expectedResult[2])) $this->assertEquals($expectedResult[2], $part->points[$key]->getDescription());
    }

    public function providerAddPoint() 
    {
        $p1 = new \Freesewing\Point();
        $p1->setX(52);
        $p1->setY(69);
        $p1->setDescription('The description');

        $p2 = new \Freesewing\Point();
        $p2->setX(-123152);
        $p2->setY(69.94328523);
        $p2->setDescription('The description is longer');

        $p3 = new \Freesewing\Point();
        $p3->setX(52);
        $p3->setY(69);

        return [
            [ 
                1,
                $p1,
                [52, 69, 'The description'], 
            ],
            [ 
                'some key',
                $p2,
                [-123152, 69.94328523, 'The description is longer'], 
            ],
            [ 
                -1,
                $p3,
                [52, 69], 
            ],
        ];
    }
    
    /**
     * @param array $keys Array of point key
     * @param array $points Array of point objects
     * @param array $expectedResults Array of results to check for
     *
     * @dataProvider providerAddPoints
     */
    public function testAddPoints($data)
    {
        $points = $data[0];
        $part = new \Freesewing\Part();
        $part->addPoints($points);

        foreach($data[1] as $key => $expectedResult) {
            if(isset($expectedResult[0])) $this->assertEquals($expectedResult[0], $part->points[$key]->getX());
        }
    }

    public function providerAddPoints() 
    {
        $p1 = new \Freesewing\Point();
        $p1->setX(52);
        $p1->setY(69);
        $p1->setDescription('The description');

        $p2 = new \Freesewing\Point();
        $p2->setX(-123152);
        $p2->setY(69.94328523);
        $p2->setDescription('The description is longer');

        $p3 = new \Freesewing\Point();
        $p3->setX(52);
        $p3->setY(69);

        $test1 = [
            [ 
                1 => $p1, 
                2 => $p2, 
                3 => $p3
            ],
            [
                1 => [52, 69, 'The description'],
                2 => [-123152, 69.94328523, 'The description is longer'],
                3 => [52, 69],
            ]
        ];
        return array(array($test1));
    }
    
    /**
     * @param string $key Path key
     * @param string $point Path object
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerAddPath
     */
    public function testAddPath($key, $path, $expectedResult)
    {
        $part = new \Freesewing\Part();
        $part->addPath($key, $path);
        if(isset($expectedResult[0])) $this->assertEquals($expectedResult[0], $part->paths[$key]->getPath());
        if(isset($expectedResult[1])) $this->assertEquals($expectedResult[1], $part->paths[$key]->getOptions());
    }

    public function providerAddPath() 
    {
        $p1 = new \Freesewing\Path();
        $p1->setPath('M 0 L 12 L 23 z');
        $p1->setOptions(['class' => 'sorcha']);

        $p2 = new \Freesewing\Path();
        $p2->setPath('M 0 L 12 C 11 23 23 z');
        $p2->setOptions(['class' => 'sorcha']);

        $p3 = new \Freesewing\Path();
        $p3->setPath('');
        $p3->setOptions(['class' => 'sorcha']);

        return [
            [ 
                1,
                $p1,
                ['M 0 L 12 L 23 z', ['class' => 'sorcha']], 
            ],
            [ 
                2,
                $p2,
                ['M 0 L 12 C 11 23 23 z', ['class' => 'sorcha']], 
            ],
            [ 
                3,
                $p3,
                ['', ['class' => 'sorcha']], 
            ],
        ];
    }
    
    /**
     * @param array $keys Array of path keys
     * @param array $points Array of path objects
     * @param array $expectedResults Array of results to check for
     *
     * @dataProvider providerAddPaths
     */
    public function testAddPaths($data)
    {
        $paths = $data[0];
        $part = new \Freesewing\Part();
        $part->addPaths($paths);

        foreach($data[1] as $key => $expectedResult) {
            if(isset($expectedResult[0])) $this->assertEquals($expectedResult[0], $part->paths[$key]->getPath());
            if(isset($expectedResult[1])) $this->assertEquals($expectedResult[1], $part->paths[$key]->getOptions());
        }
    }

    public function providerAddPaths() 
    {
        $p1 = new \Freesewing\Path();
        $p1->setPath('M 0 L 12 L 23 z');
        $p1->setOptions(['class' => 'sorcha']);

        $p2 = new \Freesewing\Path();
        $p2->setPath('M 0 L 12 C 11 23 23 z');
        $p2->setOptions(['class' => 'sorcha']);

        $p3 = new \Freesewing\Path();
        $p3->setPath('');
        $p3->setOptions(['class' => 'sorcha']);

        $test1 = [
            [ 
                1 => $p1, 
                2 => $p2, 
                3 => $p3
            ],
            [
                1 => ['M 0 L 12 L 23 z', ['class' => 'sorcha']],
                2 => ['M 0 L 12 C 11 23 23 z', ['class' => 'sorcha']],
                3 => ['', ['class' => 'sorcha']],
            ]
        ];
        return array(array($test1));
    }
    
    /**
     * @param string $key Path key
     * @param string $point Path object
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerAddTransform
     */
    public function testAddTransform($key, $transform, $expectedResult)
    {
        $part = new \Freesewing\Part();
        $part->addTransform($key, $transform);
        if(isset($expectedResult[0])) $this->assertEquals($expectedResult[0], $part->transforms[$key]->getType());
        if(isset($expectedResult[1])) $this->assertEquals($expectedResult[1], $part->transforms[$key]->getX());
        if(isset($expectedResult[2])) $this->assertEquals($expectedResult[2], $part->transforms[$key]->getY());
        if(isset($expectedResult[3])) $this->assertEquals($expectedResult[3], $part->transforms[$key]->getAngle());
    }

    public function providerAddTransform() 
    {
        $t1 = new \Freesewing\Transform('translate', 52, 69);
        $t2 = new \Freesewing\Transform('scale', 2);
        $t3 = new \Freesewing\Transform('rotate', 52, 69, 45);


        return [
            [ 
                1,
                $t1,
                ['translate', 52, 69], 
            ],
            [ 
                2,
                $t2,
                ['scale', 2], 
            ],
            [ 
                3,
                $t3,
                ['rotate', 52, 69, 45], 
            ],
        ];
    }
    
    /**
     * @param string $key Path key
     * @param string $point Path object
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerAddBoundary
     */
    public function testAddBoundary($part, $topLeft, $bottomRight) {
        $part->addBoundary();
        $this->assertEquals($topLeft, $part->boundary->topLeft);
        $this->assertEquals($bottomRight, $part->boundary->bottomRight);
    }

    public function providerAddBoundary() {
        
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
        
        $part  = new \Freesewing\Part('Test');
        
        $part->addPoints([ 
            1 => $p1, 
            2 => $p2, 
            3 => $p3, 
            4 => $p4, 
            5 => $p5,
        ]);

        $topLeft = new \Freesewing\Point();
        $topLeft->setX(0);
        $topLeft->setY(0);
        $bottomRight = new \Freesewing\Point();
        $bottomRight->setX(100);
        $bottomRight->setY(100);

        $path = new \Freesewing\Path();
        $path->setPath('M 2 L 3');
        $part->addPath('path1', $path);
        
        $path = new \Freesewing\Path();
        $path->setPath('M 3 C 4 1 5 z');
        $part->addPath('path2', $path);
        
        $path = new \Freesewing\Path();
        $path->setPath('M 3 L 1');
        $part->addPath('path3', $path);
        
        $tests[] = array($part, $topLeft, $bottomRight);
        return $tests;
    }

    public function testAddBoundaryOnPartWithoutPath() {
        $part  = new \Freesewing\Part('Test');
        $this->assertEquals(false,$part->addBoundary());
    }
}
