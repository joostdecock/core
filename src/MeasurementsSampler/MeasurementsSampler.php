<?php

namespace Freesewing;

/**
 * Freesewing\Sampler class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class MeasurementsSampler extends Sampler
{
    public $options = array();

    public function loadPatternModels($requestData)
    {
        $config = $this->getSamplerConfigFile($this->pattern, 'measurements');
        if(is_readable($config)) {
            $this->measurementsConfig = \Freesewing\Yamlr::loadYamlFile($config);
            $this->models = $this->loadModelGroup($this->modelGroupToLoad($requestData)); 
            return $this->models;
        }
        else return false;
    }

    public function sampleMeasurements($theme) 
    {
        $renderBot = new \Freesewing\SvgRenderbot;
        $steps = count($this->models);
        $i = 0;
        foreach($this->models as $modelKey => $model) {
            $p = clone $this->pattern;
            $p->loadParts();
            $p->sample($model);
            foreach($p->parts as $partKey => $part) $this->sampleParts($i, $steps, $p, $theme, $renderBot);
            $i++;
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
