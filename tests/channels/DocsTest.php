<?php

namespace Freesewing\Tests;

class DocsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Channels\Channel');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['options'],
            ['config'],
        ];
    }

    public function testIsValidRequest()
    {
        $context = new \Freesewing\Context(['pattern' => 'TestPattern']);
        $pattern = new \Freesewing\Patterns\TestPattern();
        $context->setPattern($pattern);
        $channel = new \Freesewing\Channels\Docs();
        $this->assertEquals($channel->isValidrequest($context), true);
    }

    public function testCleanUp()
    {
        $channel1 = new \Freesewing\Channels\Docs();
        $channel2 = new \Freesewing\Channels\Docs();
        $channel1->cleanUp();
        $this->assertEquals($channel1, $channel2);
    }

    public function testStandardizeModelMeasurements()
    {
        $channel = new \Freesewing\Channels\Docs();
        $data = $this->getSampleData();
        $measurements = $channel->standardizeModelMeasurements($data['in']['measurements']);
        foreach($data['out']['measurements'] as $key => $m) {
            $this->assertEquals($measurements[$m], $data['in']['measurements'][$key]*10);
        }
    }

    public function testStandardizePatternOptions()
    {

        $channel = new \Freesewing\Channels\Docs();
        $data = $this->getSampleData();
        $options = $channel->standardizePatternOptions(array_merge($data['in']['cmoptions'], $data['in']['percentoptions']));
        foreach($data['out']['cmoptions'] as $key => $o) {
            $this->assertEquals($options[$o], $data['in']['cmoptions'][$key]*10);
        }
        foreach($data['out']['percentoptions'] as $key => $o) {
            $this->assertEquals($options[$o], $data['in']['percentoptions'][$key]/100);
        }
    }

    public function getSampleData() {
        $data = [
            'in' => [
                'measurements' => [
                    'wc'    => 101,
                    'cc'    => 102,
                    'cbntw' => 103,
                    'ab'    => 104,
                    'nc'    => 105,
                    'hc'    => 106,
                    'nwttw' => 107,
                    'sl'    => 108,
                    'ss'    => 109,
                    'ubc'   => 110,
                    'slw'   => 111,
                ],
                'cmoptions' => [
                    'opt_ssw' => 112,
                    'opt_nd'  => 114,
                    'opt_lb'  => 115,
                    'opt_ad'  => 116,
                    'opt_se'  => 117,
                    'opt_ce'  => 118,
                    'opt_cfe' => 119,
                    'opt_che' => 120,
                    'opt_be'  => 121,
                    'opt_bnc' => 122,
                ],
                'percentoptions' => [
                    'opt_sf'  => 123,
                    'opt_ssp' => 124,
                    'opt_nb'  => 125,
                    'opt_bb'  => 126,
                ],
            ],
            'out' => [
                'measurements' => [
                    'wc'    => 'wristCircumference',
                    'cc'    => 'chestCircumference',
                    'cbntw' => 'centerBackNeckToWaist',
                    'ab'    => 'acrossBack',
                    'nc'    => 'neckCircumference',
                    'hc'    => 'hipsCircumference',
                    'nwttw' => 'naturalWaistToTrouserWaist',
                    'sl'    => 'shoulderLength',
                    'ss'    => 'shoulderSlope',
                    'ubc'   => 'upperBicepsCircumference',
                    'slw'   => 'sleeveLengthToWrist',
                ],
                'cmoptions' => [
                    'opt_ssw' => 'shoulderStrapWidth',
                    'opt_nd'  => 'necklineDrop',
                    'opt_lb'  => 'lengthBonus',
                    'opt_ad'  => 'armholeDrop',
                    'opt_se'  => 'sleevecapEase',
                    'opt_ce'  => 'collarEase',
                    'opt_cfe' => 'cuffEase',
                    'opt_che' => 'chestEase',
                    'opt_be'  => 'bicepsEase',
                    'opt_bnc' => 'backNeckCutout',
                ],
                'percentoptions' => [
                    'opt_sf'  => 'stretchFactor',
                    'opt_ssp' => 'shoulderStrapPlacement',
                    'opt_nb'  => 'necklineBend',
                    'opt_bb'  => 'backlineBend',
                ],
            ],
        ];

        return $data;
    }

}
