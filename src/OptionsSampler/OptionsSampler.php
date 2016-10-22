<?php

namespace Freesewing;

/**
 * Freesewing\Sampler class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class OptionsSampler extends Sampler
{
    
    public function loadModelMeasurements()
    {
        $config = $this->getSamplerConfigFile($this->pattern, 'measurements');
        if(is_readable($config)) {
            $measurements = \Freesewing\Yamlr::loadYamlFile($config);
            $default = $measurements['default']['model'];
            return array_combine($this->pattern->config['measurements'], $measurements['models'][$default]);
        } 
        else return false;
    }

    private function loadOptionToSample($option)
    {
        $config = $this->getSamplerConfigFile($this->pattern, 'options');
        if(is_readable($config)) {
            $options = \Freesewing\Yamlr::loadYamlFile($config);
            if(isset($options[$option])) return $options[$option];
        } 
        throw new \InvalidArgumentException("Cannot sample option $option, it does not exist");
    }

    private function getSampleValue($step, $steps, $option)
    {
        $gaps = $steps-1;
        if($option['type'] == 'percent') return (100/$gaps) * ($step-1) / 100;
        else return $option['min']+((($option['max']-$option['min'])/$gaps)*($step-1));
    }

    public function sampleOptions($model, $theme, $optionKey, $steps=11) 
    {
        $option = $this->loadOptionToSample($optionKey);
        if(!is_int(intval($steps)) or $steps<=1 or $steps > 25) $steps = 11;
        $renderBot = new \Freesewing\SvgRenderbot;
        for($i=1;$i<=$steps;$i++) {
            $p = clone $this->pattern;
            $sampleValue = $this->getSampleValue($i,$steps,$option);
            $p->setOption($optionKey, $sampleValue);
            $p->loadParts();
            $p->sample($model);
            foreach($p->parts as $partKey => $part) $this->sampleParts($i, $steps, $p, $theme, $renderBot);
        }
        $this->addPartBorder();
        $this->pattern->layout();
        return $this->pattern;
    } 

    private function loadModelGroup($group)
    {
        foreach($this->measurementsConfig['groups'][$group] as $member) {
            $model = new \Freesewing\Model;
            $model->setName($member);
            $measurements = array_combine($this->measurementsConfig['measurements'], $this->measurementsConfig['models'][$member]);
            $model->addMeasurements($measurements);
            $models[$member] = $model;
        }
        return $models;
    }

    private function modelGroupToLoad($requestData)
    {
        if(
            isset($requestData['samplerGroup']) 
            && 
            is_array($this->measurementsConfig['groups'][$requestData['samplerGroup']])
        ) {
           return $requestData['samplerGroup']; 
        } 
        else return $this->measurementsConfig['default']['group'];
    }
}
