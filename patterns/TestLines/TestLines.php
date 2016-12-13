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
        $p->newPoint(1, 100, 100);
        $p->newPoint(2, 100, 200);
        $p->newPoint(3, 200, 200);
        $p->newPoint(4, 200, 100);

        // Paths
        $path = 'M 1 L 2 L 3 L 4 z';
        $p->newPath('test', $path);

        // Mark path for sample service
        $p->paths['test']->setSample(true);
    }
}
