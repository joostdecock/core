<?php

namespace Freesewing\Tests;

class GrowingPackerTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp()
    {
        $this->block1 = new \Freesewing\LayoutBlock();
        $this->block2 = new \Freesewing\LayoutBlock();
        $this->block3 = new \Freesewing\LayoutBlock();
        $this->block4 = new \Freesewing\LayoutBlock();
        $this->block5 = new \Freesewing\LayoutBlock();
        $this->block6 = new \Freesewing\LayoutBlock();
        $this->block7 = new \Freesewing\LayoutBlock();
        $this->block8 = new \Freesewing\LayoutBlock();
        

        $this->block1->setSize(10,10);
        $this->block2->setSize(10,10);
        $this->block3->setSize(10,10);
        $this->block4->setSize(10,10);
        $this->block5->setSize(10,100);
        $this->block6->setSize(10,100);
        $this->block7->setSize(10,100);
        $this->block8->setSize(10,100);
        
    }

    public function test1Block()
    { 
        $layout = [
            $this->block1,
        ];
        $packer = new \Freesewing\Growingpacker();
        $packer->fit($layout); 
        $this->assertEquals(serialize($layout), $this->loadTemplate(1));
    }

    public function test2Blocks()
    { 
        $layout = [
            $this->block1,
            $this->block2,
        ];
        $packer = new \Freesewing\Growingpacker();
        $packer->fit($layout); 
        $this->assertEquals(serialize($layout), $this->loadTemplate(2));
    }

    public function test4Blocks()
    { 
        $layout = [
            $this->block1,
            $this->block2,
            $this->block3,
            $this->block4,
        ];
        $packer = new \Freesewing\Growingpacker();
        $packer->fit($layout); 
        $this->assertEquals(serialize($layout), $this->loadTemplate(4));
    }

    public function test8Blocks()
    { 
        $layout = [
            $this->block1,
            $this->block2,
            $this->block3,
            $this->block4,
            $this->block5,
            $this->block6,
            $this->block7,
            $this->block8,
        ];
        $packer = new \Freesewing\Growingpacker();
        $packer->fit($layout); 
        $this->assertEquals(serialize($layout), $this->loadTemplate(8));
    }

    public function test1011Blocks()
    { 
        $block1 = new \Freesewing\LayoutBlock();
        $block2 = new \Freesewing\LayoutBlock();
        $block1->setSize(10,10);
        $block2->setSize(1000,1000);
        $layout = [ $block1, $block2 ];

        $packer = new \Freesewing\Growingpacker();
        $packer->fit($layout); 
        $this->assertEquals(serialize($layout), $this->loadTemplate(12));
    }

    private function loadTemplate($template)
    {
        $dir = 'tests/src/fixtures';
        $file = "$dir/GrowingPacker.$template.layout";
        return file_get_contents($file);
    }

    private function saveTemplate($template, $data)
    {
        $dir = 'tests/src/fixtures';
        $file = "$dir/GrowingPacker.$template.layout";
        $f = fopen($file,'w');
        fwrite($f,$data);
        fclose($f);
    }
}
