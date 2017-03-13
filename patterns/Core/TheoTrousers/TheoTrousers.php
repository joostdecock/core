<?php
/** Freesewing\Patterns\Core\TheoTrousers class */
namespace Freesewing\Patterns\Core;

use Freesewing\Part;

/**
 * The Theo Trousers  pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TheoTrousers extends TheodoreTrousers
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /**
     * Sets up options and values for our draft
     *
     * By branching this out of the sample/draft methods, we can
     * set a bunch of options and values the influence the draft
     * without having to touch the sample/draft methods
     * When extending this pattern so we can just implement the
     * initialize() method and re-use the other methods.
     *
     * Good to know: 
     * Options are typically provided by the user, but sometimes they are fixed
     * Values are calculated for re-use later
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
        // This option is fixed in the legacy code
        $this->setOption('trouserBottomWidth', 226);   
        
        // Specific to the Theo
        $this->setValue('legReduction', 30);   
        $this->setValue('legExtension', 40);   
        $this->setValue('frontReduction', 10);   
        $this->setValue('backReduction', 15);   
    }
}
