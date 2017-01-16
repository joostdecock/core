<?php

namespace Freesewing\Tests;

class LayoutBlockTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\LayoutBlock');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['x'],
            ['y'],
            ['w'],
            ['h'],
            ['used'],
        ];
    }

    /**
     * Tests the setPosition method
     */
    public function testSetPosition()
    {
        $block = new \Freesewing\LayoutBlock();
        $block->setPosition(10,20);
        $this->assertEquals($block->x, 10);
        $this->assertEquals($block->y, 20);
    }

    /**
     * Tests the setSize method
     */
    public function testSetSize()
    {
        $block = new \Freesewing\LayoutBlock();
        $block->setSize(10,20);
        $this->assertEquals($block->w, 10);
        $this->assertEquals($block->h, 20);
    }

    /**
     * Tests the setUsed and isUsed method
     */
    public function testSetUsedIsUsed()
    {
        $block = new \Freesewing\LayoutBlock();
        $block->setUsed(true);
        $this->assertEquals($block->isUsed(), true);
        $block->setUsed(false);
        $this->assertEquals($block->isUsed(), false);
    }
}
