<?php
/** Freesewing\Patterns\TestPattern class */
namespace Freesewing\Patterns;

/**
 * The Test pattern
 *
 * This pattern is used in unit testing because we can't test the abstract
 * pattern class, and we can't be certain what other patterns are 
 * available (as the are options submodules)
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TestPattern extends Pattern
{
    /**
     * The mandatory draft method
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
    
    }

    /**
     * The mandatory sample method
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {

    }
}
