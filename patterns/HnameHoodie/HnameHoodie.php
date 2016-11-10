<?php
/** Freesewing\Patterns\JoostBodyBlock class */
namespace Freesewing\Patterns;

/**
 * The Hname Hoodie pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class HnameHoodie extends JoostBodyBlock
{
    /**
     * Generates a sample of the pattern
     *
     * This creates a sample of this pattern for a given model
     * and set of options. You get a barebones pattern with only 
     * what it takes to illustrate the effect of changes in
     * the sampled option or measurement.
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        $this->loadHelp($model);

        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        $this->draftSleeveBlock($model);
    }
}
