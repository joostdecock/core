<?php
/** Freesewing\Patterns\Beta\BreannaBodyBlock class */
namespace Freesewing\Patterns\Beta;

/**
 * Breanna bodice for womenswear
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2018 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BreannaBodyBlock extends \Freesewing\Patterns\Core\BrianBodyBlock
{
    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
    */
    
    /**
     * Generates a draft of the pattern
     *
     * This creates a draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->initialize($model);
        
        $this->draftBackBlock($model);
        $this->finalizeBackBlock($model);
        
        $this->draftFrontBlock($model);
        $this->finalizeFrontBlock($model);

        // Tweak the sleeve until it fits the armhole
        do {
            $this->draftSleeveBlock($model);
        } while (abs($this->armholeDelta()) > 1 && $this->v('sleeveTweakRun') < 50);
        $this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta(),1).'mm off.');
        $this->finalizeSleeveBlock($model);
        
        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to all parts
            $this->paperlessFrontBlock($model);
            $this->paperlessBackBlock($model);
            $this->paperlessSleeveBlock($model);
        }
    }

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
        $this->initialize($model);

        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        $this->finalizeBackBlock($model);
        $this->finalizeFrontBlock($model);

        // Tweak the sleeve until it fits the armhole
        do {
            $this->draftSleeveBlock($model);
        } while (abs($this->armholeDelta()) > 1 && $this->v('sleeveTweakRun') < 50);
        $this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta(),1).'mm off.');
    }

    
    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/grainline and so on
    */



    /*
        ____                       _
       |  _ \ __ _ _ __   ___ _ __| | ___  ___ ___
       | |_) / _` | '_ \ / _ \ '__| |/ _ \/ __/ __|
       |  __/ (_| | |_) |  __/ |  | |  __/\__ \__ \
       |_|   \__,_| .__/ \___|_|  |_|\___||___/___/
                  |_|

      Instructions for paperless patterns
    */
}
