<?php
/** Freesewing\Patterns\TestPattern class */
namespace Freesewing\Patterns;

/**
 * The Test pattern
 *
 * This pattern is used in unit testing. You can safely ignore it.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TestPattern extends Pattern
{
    /**
     * Generates a draft of the pattern
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->sample($model);

        $this->finalizeTest($model);

        if ($this->isPaperless) {
            $this->paperlessTest($model);
        }

    }

    /**
     * Generates a sample of the pattern
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        $this->draftTest($model);
    }

    /**
     * Drafts the Test
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTest($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['test'];

        $p->newPoint(     1, 0, 0);
        $p->newPoint(     2, 100, 0);
        
        // Paths
        $p->newPath('line', 'M 1 L 2');

        // Mark paths for sample service
        $p->paths['line']->setSample(true);
    }

    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/grainline and so on
    */

    /**
     * Finalizes the test
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     *
     * @return void
     */
    public function finalizeTop($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['top'];

        // Seam allowance
        $p->offsetPath('sa', 'line', 10, 1, ['class' => 'seam-allowance']);
    }


    /*
        ____                       _
       |  _ \ __ _ _ __   ___ _ __| | ___  ___ ___
       | |_) / _` | '_ \ / _ \ '__| |/ _ \/ __/ __|
       |  __/ (_| | |_) |  __/ |  | |  __/\__ \__ \
       |_|   \__,_| .__/ \___|_|  |_|\___||___/___/
                  |_|

      Instructions for paperless patterns
    */

    /**
     * Adds paperless info for the test
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessTest($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['test'];

        // Width
        $yBase = $p->y(2);
        $p->newWidthDimension(1,2,$p->y(1)+25);
    }
}
