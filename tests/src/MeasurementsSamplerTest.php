<?php

namespace Freesewing\Tests;

class MeasurementsSamplerTest extends \PHPUnit\Framework\TestCase
{


    /**
     * Tests the loadPatternModels method
     */
    public function testLoadPatternModels()
    {
        $config = [
            'default' => [
                'group' => 'maleStandardUsSizes',
            ],
            'groups' => [
                'realMen' => ['joost'],
                'maleStandardUsSizes' => [
                    'usSize34',
                    'usSize36',
                    'usSize38',
                    'usSize40',
                    'usSize42',
                    'usSize44',
                ],
            ],
            'measurements' => [
                'acrossBack' => [
                    'joost' => 450,
                    'usSize34' => 380,
                    'usSize36' => 390,
                    'usSize38' => 400,
                    'usSize40' => 410,
                    'usSize42' => 420,
                    'usSize44' => 430,
                ]
            ]
        ];

        $sampler = new \Freesewing\MeasurementsSampler();
        $sampler->setModelConfig($config);
        $models = $sampler->loadPatternModels('maleStandardUsSizes');

        foreach($models as $key => $model) {
            foreach($config['measurements'] as $m => $ignoreMe) {
                $this->assertEquals($model->getMeasurement($m), $config['measurements'][$m][$key]);
            }
        }
        
        $models = $sampler->loadPatternModels('nonexisting');
        
        foreach($models as $key => $model) {
            foreach($config['measurements'] as $m => $ignoreMe) {
                $this->assertEquals($model->getMeasurement($m), $config['measurements'][$m][$key]);
            }
        }

        $config['default']['group'] = [0 => 'maleStandardUsSizes', 1 => 'otherGroup'];
        $sampler->setModelConfig($config);
        $models = $sampler->loadPatternModels('nonexisting');
        foreach($models as $key => $model) {
            $this->assertEquals($model->getMeasurement('acrossBack'), $config['measurements']['acrossBack'][$key]);
        }

    }
}
