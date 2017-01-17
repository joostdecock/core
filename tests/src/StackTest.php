<?php

namespace Freesewing\Tests;

use \Freesewing\Stack;

class StackTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->s = new Stack();
    }

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Stack');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['items'],
            ['intersections'],
        ];
    }

    /**
     * Tests the push method
     */
    public function testPush()
    {
        $this->s->push(['test']);
        $this->assertEquals($this->s->items[0], 'test');
        $this->s->push([2,3,4,5,6]);
        $this->assertEquals(count($this->s->items), 6);
    }

    /**
     * Tests the addIntersection method
     */
    public function testAddIntersection()
    {
        $this->s->addIntersection('test');
        $this->assertEquals($this->s->intersections[0], 'test');
        $this->s->addIntersection(2);
        $this->s->addIntersection(3);
        $this->s->addIntersection(4);
        $this->assertEquals(count($this->s->intersections), 4);
    }

    /**
     * Tests the replace method
     */
    public function testReplace()
    {
        $this->s->push(['Quick','yellow','jumps']);
        $this->s->replace('yellow', ['brown', 'fox']);
        $this->assertEquals($this->s->items, ['Quick','brown', 'fox','jumps']);
    }
}
