<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\LayoutTest class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class LayoutTest extends Pattern
{
    public $parts = array();
    private $numberOfParts = 20;
    private $minSize = 20;
    private $maxSize = 200;

    public function draft($model)
    {
        for($i=1; $i<=$this->numberOfParts; $i++) {
            $this->addPart($i);
            $this->parts[$i]->setTitle("Part #$i");

            $w = $this->getSize();
            $h = $this->getSize();
            $p = $this->parts[$i];
            
            $p->newPoint(1,  0,  0);
            $p->newPoint(2, $w,  0);
            $p->newPoint(3, $w, $h);
            $p->newPoint(4,  0, $h);
            
            $p->newPath('box', 'M 1 L 2 L 3 L 4 z');
        }
    }

    private function getSize() {
        return round(rand($this->minSize, $this->maxSize));
    }
}
