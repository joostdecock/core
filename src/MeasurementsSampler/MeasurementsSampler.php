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
    private $options = array();

    public function loadPatternOptions()
    {
        $config = $this->getSamplerConfigFile($this->pattern, 'options');
        if(is_readable($config)) {
            $options = \Freesewing\Yamlr::loadYamlFile($config);
            foreach($options as $key => $option) 
                if($option['type'] == 'percent') $this->options[$key] = $option['default']/100;
                else $this->options[$key] = $option['default'];
            return $this->options;
        } 
        else return false;
    }

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

    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function sampleMeasurements($theme) 
    {
        $partContainer = array();
        $renderBot = new \Freesewing\SvgRenderbot;
        $modelCount = count($this->models);
        $mi = 0;
        foreach($this->models as $modelKey => $model) {
            $p = clone $this->pattern;
            $p->loadParts();
            $p->sample($model);
            foreach($p->parts as $partKey => $part) {
                if($part->render) {
                    $anchorKey = "anchor-$partKey";
                    if (!@is_object(${$anchorKey})) {
                        ${$anchorKey} = $this->getSamplerAnchor($part);
                        $deltaX = 0;
                        $deltaY = 0;
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
                            $path->setAttributes(['transform' => $transform, 'style' => $theme->samplerPathStyle($mi, $modelCount)]);
                            $partContainer[$partKey]['includes']["$modelKey-$pathKey"] = $renderBot->renderPath($path, $part);
                            $partContainer[$partKey]['topLeft'] = ${$tlKey};
                            $partContainer[$partKey]['bottomRight'] = ${$brKey};
                        }
            
                    }
                }
            }
            $mi++;
        }
        foreach($partContainer as $partKey => $part) {
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

    private function getSamplerAnchor($part)
    {
        if(isset($part->points['samplerAnchor'])) return $part->loadPoint('samplerAnchor');
        else if(isset($part->points['gridAnchor'])) return $part->loadPoint('gridAnchor');
        else {
            $part->newPoint('defaultAnchor', 0, 0, 'Anchor point added by sampler');
            return $part->loadPoint('samplerAnchor');
        }
    }

    private function getSamplerConfigFile($pattern, $mode)
    {
        if($mode == 'options') return $pattern->getPatternDir().'/sampler/options.yml';
        else return $pattern->getPatternDir().'/sampler/measurements.yml';

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
