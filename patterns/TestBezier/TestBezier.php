<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\TestBezier class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TestBezier extends Pattern
{
    public function draft($model)
    {
        $this->test();
    }

    public function sample($model)
    {
        $this->test();
    }

    public function test()
    {
        $p = $this->parts['test'];

        // Center vertical axis
        $p->newPoint(1,   0,  75);
        $p->newPoint(2,   80, 100);
        $p->newPoint(3,   10, 0);
        $p->newPoint(4,   100, 25);
        $p->newPoint(5,   0, 50);
        $p->newPoint(6,   100, 40);

        $p->lineCrossesCurve(5,6,1,3,2,4,'test'); 
        
        // Paths
        $path = 'M 1 C 3 2 4 M 5 L 6';
        $p->newPath('test', $path);

        // Mark path for sample service
        $p->paths['test']->setSample(true);
    }
}
