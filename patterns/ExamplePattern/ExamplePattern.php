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
        //$size = $this->config['options']['square_size'];
        $p = $this->parts['square'];

        $p->newPoint(1,   0,   0);
        $p->newPoint(2, 100,   0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 0, 100);
        $p->newPoint(5, 50, 50);

//        $p->newPath('outline', 'M 1 L 2 C 5 5 3 L 4 C 5 5 1 z', ['class' => 'cutline']);
        $p->newPath('outline', 'M 1 L 4 C 5 5 3 L 2 L 5 z ', ['class' => 'cutline']);
        //$p->newPath('outline', 'M 1 L 2 L 3 z', ['class' => 'cutline']);
        $p->offsetPath('offset', 'outline', 10);

    }
}
