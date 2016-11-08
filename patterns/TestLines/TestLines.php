<?php
/** Freesewing\Patterns\TestLines class */

namespace Freesewing\Patterns;

/**
 * Used for testing Bezier curve intersection
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TestLines extends Pattern
{
    /**
     * Generates a draft 
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->test();
    }

    /**
     * Generates a sample 
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        $this->test();
    }

    /**
     * The actual testing
     *
     * @return void
     */
    public function test()
    {
        $p = $this->parts['test'];

        // Center vertical axis
        $p->newPoint(1,   0,  0);
        $p->newPoint(2,   30, 0);
        $p->newPoint(3,   50, 50);
        $p->newPoint(4,   70, 0);
        $p->newPoint(5,   100, 0);
        $p->newPoint(6,   100, 100);
        $p->newPoint(7,   0, 100);

        $p->curveCrossesLine(1,4,7,6,5,7,'test1'); 
        


        // Paths
        $path = 'M 1 C 4 7 6 M 5 L 7';
        $p->newPath('test', $path);
//        $p->offsetPathString('sa', $path, -10, 1, ['class' => 'seam-allowance']);

        // Mark path for sample service
        $p->paths['test']->setSample(true);
    }
}
