<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\ExamplePattern class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ExamplePattern extends Pattern
{
    private $config_file = __DIR__.'/config.yml';
    public $parts = array();

    public function __construct()
    {
        $this->config = \Freesewing\Yamlr::loadConfig($this->config_file);

        return $this;
    }

    public function draft($model)
    {
        foreach ($this->config['parts'] as $part => $title) {
            $this->addPart($part);
            $this->parts[$part]->setTitle($title);
            $method = 'draft'.ucwords($part);
            $this->$method($model);
        }
        //$this->draftSquare($model);
    }

    private function draftSquare()
    {
        $size = $this->config['options']['square_size'];
        $p = $this->parts['square'];
        
        $p->newPoint(1,   0,   0);
        $p->newPoint(2, 100,   0);
        $p->newPoint(3, $p->x(2), 100);
        $p->newPoint(4,   0, 100);
        $p->newPoint(5,  50,  50);

        $outline = new \Freesewing\Path();
        $outline->setPath('M 3 C 4 1 5 z');
        $outline->setOptions(['class' => 'cutline']);

        $p->addPath('outline', $outline);

        $p->newPoint('center', 150, 50); 
        $p->newPoint(120, $p->x('center'), $p->y('center') - 40);
        $angle = 0;
        $clockPath = 'M 120 L center ';
        for($i=10;$i<120;$i+=10) {
            $angle -= 30;
            $p->addPoint($i, $p->rotate(120, 'center', $angle));
            $clockPath .= "M $i L center ";
        }
        
        $clock = new \Freesewing\Path();
        $clock->setPath($clockPath);
        $clock->setOptions(['class' => 'cutline']);
        $p->addPath('clock', $clock);
        
        $clockpath2 = '';
        $p->addPoint('center2', $p->shift('center', 0, 50));
        for($i=10;$i<130;$i+=10) {
            
            $point = $p->rotate($i, 'center', -15);
            $point->setX($point->getX()+50);
            $p->addPoint($i.'.2', $point);
            $clockPath2 .= "M $i.2 L center2 ";
        }
        
        $clock2 = new \Freesewing\Path();
        $clock2->setPath($clockPath2);
        $clock2->setOptions(['class' => 'cutline']);
        $p->addPath('clock2', $clock2);
    }
}
