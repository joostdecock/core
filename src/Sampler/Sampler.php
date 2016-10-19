<?php

namespace Freesewing;

/**
 * Freesewing\Sampler class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Sampler
{

    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getSamplerConfigFile($pattern, $mode)
    {
        if($mode == 'options') return $pattern->getPatternDir().'/sampler/options.yml';
        else return $pattern->getPatternDir().'/sampler/measurements.yml';

    }
    
    public function getSamplerAnchor($part)
    {
        if(isset($part->points['samplerAnchor'])) return $part->loadPoint('samplerAnchor');
        else if(isset($part->points['gridAnchor'])) return $part->loadPoint('gridAnchor');
        else {
            $part->newPoint('defaultAnchor', 0, 0, 'Anchor point added by sampler');
            return $part->loadPoint('samplerAnchor');
        }
    }

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

    public function samplePart($step, $steps, $partKey, $part, $theme, $renderBot)
    {
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
                    $path->setAttributes(['transform' => $transform, 'style' => $theme->samplerPathStyle($step, $steps)]);
                    $this->partContainer[$partKey]['includes']["$step-$pathKey"] = $renderBot->renderPath($path, $part);
                    $this->partContainer[$partKey]['topLeft'] = ${$tlKey};
                    $this->partContainer[$partKey]['bottomRight'] = ${$brKey};
                }
            }
        }
    }

}
