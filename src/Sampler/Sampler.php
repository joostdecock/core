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

    public $partContainer = array();
    public $anchors = array();
    public $boundaries = array();

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

    public function sampleParts($step, $steps, $pattern, $theme, $renderBot)
    {
        foreach($pattern->parts as $partKey => $part) {
            if($part->render) {
                if (!@is_object($this->anchors[$partKey])) {
                    $this->anchors[$partKey] = $this->getSamplerAnchor($part);
                    $deltaX = 0;
                    $deltaY = 0;
                    $transform = "translate( 0, 0 )"; 
                } else {
                    $anchor = $this->getSamplerAnchor($part);
                    $deltaX = $this->anchors[$partKey]->getX() - $anchor->getX();
                    $deltaY = $this->anchors[$partKey]->getY() - $anchor->getY();
                    $transform = "translate( $deltaX, $deltaY )"; 
                }
                foreach($part->paths as $pathKey => $path) {
                    if($path->sampler) {
                        $path->boundary = $path->findBoundary($part);
                        if (!@is_object($this->boundaries[$partKey]['topLeft'])) {
                            $this->boundaries[$partKey]['topLeft'] = new \Freesewing\Point();
                            $this->boundaries[$partKey]['topLeft']->setX($path->boundary->topLeft->x);
                            $this->boundaries[$partKey]['topLeft']->setY($path->boundary->topLeft->y);
                            $this->boundaries[$partKey]['bottomRight'] = new \Freesewing\Point();
                            $this->boundaries[$partKey]['bottomRight']->setX($path->boundary->bottomRight->x);
                            $this->boundaries[$partKey]['bottomRight']->setY($path->boundary->bottomRight->y);
                        } else {
                            if (($path->boundary->topLeft->x + $deltaX) < $this->boundaries[$partKey]['topLeft']->x) $this->boundaries[$partKey]['topLeft']->setX($path->boundary->topLeft->x + $deltaX);
                            if (($path->boundary->topLeft->y + $deltaY) < $this->boundaries[$partKey]['topLeft']->y) $this->boundaries[$partKey]['topLeft']->setY($path->boundary->topLeft->y + $deltaY);
                            if ($path->boundary->bottomRight->x+$deltaX > $this->boundaries[$partKey]['bottomRight']->x)  $this->boundaries[$partKey]['bottomRight']->setX($path->boundary->bottomRight->x + $deltaX);
                            if ($path->boundary->bottomRight->y+$deltaY > $this->boundaries[$partKey]['bottomRight']->y)  $this->boundaries[$partKey]['bottomRight']->setY($path->boundary->bottomRight->y + $deltaY);
                        }
                        $path->setAttributes(['transform' => $transform, 'style' => $theme->samplerPathStyle($step, $steps)]);
                        $this->partContainer[$partKey]['includes']["$step-$pathKey"] = $renderBot->renderPath($path, $part);
                        $this->partContainer[$partKey]['topLeft'] = $this->boundaries[$partKey]['topLeft'];
                        $this->partContainer[$partKey]['bottomRight'] = $this->boundaries[$partKey]['bottomRight'];
                    }
                }
            }
        }
    }
    
    public function addPartBorder()
    {
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
    }

}
