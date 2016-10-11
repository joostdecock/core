<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\JoostBodyBlock class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ExamplePattern extends Pattern
{
    public function draft($model)
    {
        $p = $this->parts['test'];

        // Center vertical axis
        $p->newPoint( 1 ,   0,  0);
        $p->newPoint( 2 ,   0,  100);
        $p->newPoint( 3 ,   100,  100);
        $p->newPoint( 4 ,   100,  50);
        $p->newPoint( 5 ,   100,  50);
        $p->newPoint( 6 ,   75,  50);
        $p->newPoint( 7 ,   50,  25);
        $p->newPoint( 8 ,   50,  0);
        // Paths
        $path = 'M 1 L 2 L 3 L 4 L 5 C 6 7 8 ';
        //$path = 'M 1 L 2 L 3 L 4 L 5 L 6 L 7 L 8 z';
        $p->newPath('outline', $path);
        $p->offsetPath('sa', 'outline', -10, 1);
    }

}
