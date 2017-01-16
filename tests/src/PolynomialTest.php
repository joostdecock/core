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
