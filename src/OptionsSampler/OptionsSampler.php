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
            return array_combine($measurements['measurements'], $measurements['models'][$default]);
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
        $this->partContainer = array();
        $renderBot = new \Freesewing\SvgRenderbot;

        for($i=1;$i<=$steps;$i++) {
            $p = clone $this->pattern;
            $sampleValue = $this->getSampleValue($i,$steps,$option);
            $p->setOption($optionKey, $sampleValue);
            $p->loadParts();
            $p->sample($model);
            foreach($p->parts as $partKey => $part) {
        if($part->render) {
            $anchorKey = "anchor-$partKey";
            if (!@is_object(${$anchorKey})) {
                ${$anchorKey} = $this->getSamplerAnchor($part);
                $deltaX = 0;
                $deltaY = 0;
                $transform = "translate( 0, 0 )"; 
            } else {
                $thisAnchor = $this->getSamplerAnchor($part);
                $deltaX = ${$anchorKey}->getX() - $thisAnchor->getX();
                $deltaY = ${$anchorKey}->getY() - $thisAnchor->getY();
                $transform = "translate( $deltaX, $deltaY )"; 
            }
            $tlKey = "topLeft-$partKey";
            $brKey = "bottomRight-$partKey";
            foreach($part->paths as $pathKey => $path) {
                if($path->sampler) {
                    $path->boundary = $path->findBoundary($part);
                    if (!@is_object(${$tlKey})) {
                        ${$tlKey} = new \Freesewing\Point($tlKey);
                        ${$tlKey}->setX($path->boundary->topLeft->x);
                        ${$tlKey}->setY($path->boundary->topLeft->y);
                        ${$brKey} = new \Freesewing\Point($brKey);
                        ${$brKey}->setX($path->boundary->bottomRight->x);
                        ${$brKey}->setY($path->boundary->bottomRight->y);
                    } else {
                        if (($path->boundary->topLeft->x + $deltaX) < ${$tlKey}->x) ${$tlKey}->setX($path->boundary->topLeft->x + $deltaX);
                        if (($path->boundary->topLeft->y + $deltaY) < ${$tlKey}->y) ${$tlKey}->setY($path->boundary->topLeft->y + $deltaY);
                        if ($path->boundary->bottomRight->x+$deltaX > ${$brKey}->x) ${$brKey}->setX($path->boundary->bottomRight->x + $deltaX);
                        if ($path->boundary->bottomRight->y+$deltaY > ${$brKey}->y) ${$brKey}->setY($path->boundary->bottomRight->y + $deltaY);
                    }
                    $path->setAttributes(['transform' => $transform, 'style' => $theme->samplerPathStyle($i, $steps)]);
                    $this->partContainer[$partKey]['includes']["$i-$pathKey"] = $renderBot->renderPath($path, $part);
                    $this->partContainer[$partKey]['topLeft'] = ${$tlKey};
                    $this->partContainer[$partKey]['bottomRight'] = ${$brKey};
                }
            }
        }
            }
        }
        foreach($this->partContainer as $partKey => $part) {
            $this->pattern->addPart("sampler-$partKey");
            $p = $this->pattern->parts[$partKey];
            $p->newPoint( 1, $part['topLeft']->getX(), $part['topLeft']->getY(), 'Top left');
            $p->newPoint( 3, $part['bottomRight']->getX(), $part['bottomRight']->getY(), 'Bottom right');
            $p->newPoint( 2, $p->x(3), $p->y(1), 'Top right');
            $p->newPoint( 4, $p->x(1), $p->y(3), 'Bottom left');
            $p->newPath('border', 'M 1 L 2 L 3 L 4 z', ['class' => 'hidden']);
            foreach($part['includes'] as $pathKey => $include) {
                $p->newInclude($pathKey, $include);
            }
        }
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
