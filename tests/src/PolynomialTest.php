<?php

namespace Freesewing\Tests;

class PolynomialTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Polynomial');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['precision'],
            ['tolerance'],
            ['coefs'],
        ];
    }

    /**
     * Tests the getRoots method
     */
    public function testGetRoots()
    {
        $p = new \Freesewing\Polynomial([]);
        $this->assertEquals($p->getRoots(), []);
        
        $p = new \Freesewing\Polynomial([1]);
        $this->assertEquals($p->getRoots(), []);
        
        $p = new \Freesewing\Polynomial([1,2]);
        $this->assertEquals($p->getRoots(), [-2]);
        
        $p = new \Freesewing\Polynomial([1,2,-3]);
        $this->assertEquals($p->getRoots(), [1,-3]);
        
        $p = new \Freesewing\Polynomial([1,2,-3,4]);
        $this->assertEquals($p->getRoots(), [-3.284277537307]);
    }

    /**
     * Tests exception thrown in evalu method
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Polinomial::Eval() : Parameter must be numeric
     */
    public function testEvaluException()
    {
        $poly = new \Freesewing\Polynomial([1,2,3]);
        $poly->evalu('boom');
    }
}
