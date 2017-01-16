<?php

namespace Freesewing\Tests;

class GrowingPackerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests the getFirstBlock method
     */
    public function testgetFirstBlock()
    {
        $block1 = new \Freesewing\LayoutBlock();
        $block2 = new \Freesewing\LayoutBlock();
        $block3 = new \Freesewing\LayoutBlock();
        $block4 = new \Freesewing\LayoutBlock();
        $block5 = new \Freesewing\LayoutBlock();

        $block1->setSize(100,100);
        $block2->setSize(500,250);
        $block3->setSize(80,20);
        $block4->setSize(300,600);
        $block5->setSize(21,19);
        
        $layout = [$block1,$block2,$block3,$block4,$block5];

        $packer = new \Freesewing\Growingpacker();
        $packer->fit($layout);

        $expected2 = new \Freesewing\LayoutBlock();
        $expected2->setSize(80,20);

        $expected2Right = new \Freesewing\LayoutBlock();
        $expected2Right->setPosition(100,0);
        $expected2Right->setSize(0,100);
        
        $expected2Fit = new \Freesewing\LayoutBlock();
        $expected2Fit->setPosition(100,0);
        $expected2Fit->setSize(80,100);
        $expected2Fit->setUsed(true);

        $expected2FitDown = new \Freesewing\LayoutBlock();
        $expected2FitDown->setPosition(100,20);
        $expected2FitDown->setSize(80,80);
        $expected2FitDown->setUsed(1);
        
        $expected2FitDownDown = new \Freesewing\LayoutBlock();
        $expected2FitDownDown->setPosition(100,39);
        $expected2FitDownDown->setSize(80,61);
        
        $expected2FitDownRight = new \Freesewing\LayoutBlock();
        $expected2FitDownRight->setPosition(121,20);
        $expected2FitDownRight->setSize(59,19);
        
        $expected2FitDown->down = $expected2FitDownDown;
        $expected2FitDown->right = $expected2FitDownRight;

        $expected2FitRight = new \Freesewing\LayoutBlock();
        $expected2FitRight->setPosition(180,0);
        $expected2FitRight->setSize(0,20);
        
        $expected2Fit->down = $expected2FitDown;
        $expected2Fit->right = $expected2FitRight;

        $expected2->fit = $expected2Fit;

        $this->assertEquals($layout[2],$expected2);
    }

}
