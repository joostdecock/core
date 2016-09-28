<?php

namespace Freesewing\Tests;

class SvgRenderbotTest extends \PHPUnit\Framework\TestCase
{

    public function testRenderPattern()
    {
        $bot = new \Freesewing\SvgRenderbot();
        $pattern = new \Freesewing\Patterns\Pattern();
        $part  = new \Freesewing\Part('Test');
        $pattern->addPart('test', $part);
        
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
        
        
        $pattern->parts['test']->addPoints([ 
            1 => $p1, 
            2 => $p2, 
            3 => $p3, 
            4 => $p4, 
            5 => $p5,
        ]);

        $path = new \Freesewing\Path();
        $path->setPath('M 2 L 3');
        $pattern->parts['test']->addPath('path1', $path);
        
        $path = new \Freesewing\Path();
        $path->setPath('M 3 C 4 1 5 z');
        $pattern->parts['test']->addPath('path2', $path);
        
        $path = new \Freesewing\Path();
        $path->setPath('M 3 L 1');
        $pattern->parts['test']->addPath('path3', $path);
        
        $transform = new \Freesewing\Transform('translate', 52, 69);
        $pattern->parts['test']->addTransform('moveabit', $transform);


        $svg = trim(file_get_contents(__DIR__.'/SvgRenderbot.output.svg'));
        $render = trim($bot->render($pattern));
            $this->assertEquals($svg, $render);
    }

}
