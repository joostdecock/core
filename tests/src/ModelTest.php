<?php

namespace Freesewing\Tests;

class ModelTest extends \PHPUnit\Framework\TestCase
{
    public function testAttributeMeasurements()
    {
        $this->assertClassHasAttribute('measurements', '\Freesewing\Model');
    }

    public function testSetMeasurement()
    {
        $model = new \Freesewing\Model();
        $model->setMeasurement('Shoe size', 52);
        $this->assertEquals(52, $model->getMeasurement('Shoe size'));
    }

    public function testAddMeasurements()
    {
        $model = new \Freesewing\Model();
        $model->addMeasurements([
            'Shoe size' => 52,
            'Toe length' => 6.3,
            'Height' => 198,
        ]);

        $this->assertEquals(52, $model->getMeasurement('Shoe size'));
        $this->assertEquals(6.3, $model->getMeasurement('Toe length'));
        $this->assertEquals(198, $model->getMeasurement('Height'));
    }
}
