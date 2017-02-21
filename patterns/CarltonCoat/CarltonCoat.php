<?php
/** Freesewing\Patterns\PatternTemplate class */
namespace Freesewing\Patterns;

/**
 * A pattern template
 *
 * If you'd like to add you own pattern, you can copy this class/directory.
 * It's an empty skeleton for you to start working with
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class CarltonCoat extends BentBodyBlock
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /** Length bonus is irrelevant */
    const LENGTH_BONUS = 0;

    /** Armhole depth factor is always 67% */
    const ARMHOLE_DEPTH_FACTOR = 0.67;

    /** Sleevecap height factor is always 50% */
    const SLEEVECAP_HEIGHT_FACTOR = 0.5;

    /** Hem from waist factor is always 69% */
    const HEM_FROM_WAIST_FACTOR = 0.69;

    /**
     * Sets up options and values for our draft
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
        // The (grand)parent pattern's lengthBonus is irrelevant here
        $this->setOption('lengthBonus', self::LENGTH_BONUS);
        
        // Fix the armholeDepthFactor to 67%
        $this->setOption('armholeDepthFactor', self::ARMHOLE_DEPTH_FACTOR);
        
        // Fix the sleevecapHeightFactor to 50%
        $this->setOption('sleevecapHeightFactor', self::SLEEVECAP_HEIGHT_FACTOR);
        
        // Fix the hemFromWaistFactor to 69%
        $this->setOption('hemFromWaistFactor', self::HEM_FROM_WAIST_FACTOR);

        // Make shoulderToShoulder measurement 106.38% of original because coat
        $model->setMeasurement('shoulderToShoulder', $model->getMeasurement('shoulderToShoulder')*1.0638);
        
        // Make acrossBack measurement 106.38% of original because coat
        $model->setMeasurement('acrossBack', $model->getMeasurement('acrossBack')*1.0638);
        
        parent::initialize($model);
    }

    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
    */

    /**
     * Generates a sample of the pattern
     *
     * Here, you create a sample of the pattern for a given model
     * and set of options. You should get a barebones pattern with only
     * what it takes to illustrate the effect of changes in
     * the sampled option or measurement.
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        // Setup all options and values we need
        $this->initialize($model);

        // Get to work
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        $this->draftSleeveBlock($model);
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
        
        $this->draftFrontCoatBlock($model);

        // Hide the sleeveBlock, frontBlock, and backBlock
        $this->parts['sleeveBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
    }

    /**
     * Generates a draft of the pattern
     *
     * Here, you create the full draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        // Continue from sample
        //$this->sample($model);
        $this->initialize($model);
        
        $this->draftBackBlock($model);
        //$this->finalizeBackBlock($model);
        
        $this->draftFrontBlock($model);
        //$this->finalizeFrontBlock($model);

        $this->draftSleeveBlock($model);
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
        //$this->finalizeSleeveBlock($model);
        
        $this->draftFrontCoatBlock($model);
        
        // Hide the sleeveBlock, frontBlock, and backBlock
        $this->parts['sleeveBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
        
        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to our example part
            //$this->paperlessExamplePart($model);
        }
    }

    /**
     * Drafts the frontCoatBlock
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontCoatBlock($model)
    {
        $this->clonePoints('frontBlock','frontCoatBlock');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontCoatBlock'];

        // Hem length
        $p->newPoint('hemMiddle', $p->x(4), $p->y(3) + $model->m('naturalWaistToFloor') * $this->o('hemFromWaistFactor'));
        $p->newPoint('hemSide', $p->x(5), $p->y('hemMiddle'));


        // Paths 
        $path = 'M 9 L 2 L 3 L 4 L hemMiddle hemSide L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 9 z';
        $p->newPath('seamline', $path);
        $p->newPath('hipLine', 'M 4 L 6', ['class' => 'helpline']);
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
     * Finalizes the example part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeExamplePart($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['examplePart'];
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
     * Adds paperless info for the example part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessExamplePart($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['examplePart'];
    }
}
