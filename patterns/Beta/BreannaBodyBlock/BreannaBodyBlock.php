<?php
/** Freesewing\Patterns\Beta\BreannaBodyBlock class */
namespace Freesewing\Patterns\Beta;

/**
 * A pattern template
 *
 * If you'd like to add you own pattern, you can copy this class/directory.
 * It's an empty skeleton for you to start working with
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2018 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BreannaBodyBlock extends \Freesewing\Patterns\Core\Pattern
{
    /**
     * Fix collar ease to 1.5cm
     */
    const COLLAR_EASE = 15;

    /**
     * Fix back neck cutout to 2cm
     */
    const NECK_CUTOUT = 20;

    /**
     * Fix sleevecap ease to 0.5cm
     */
    const SLEEVECAP_EASE = 5;

    /**
     * Fix biceps ease to 5cm
     */
    const BICEPS_EASE = 50;

    /**
     * Cut front armhole a bit deeper
     */
    const FRONT_ARMHOLE_EXTRA = 5;
    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
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
        /** 
         * Calling setOptionIfUnset and setValueIfUnset so that child patterns can 
         * set the options they need and call this method for the rest
         */
        $this->setOptionIfUnset('collarEase', self::COLLAR_EASE);
        $this->setOptionIfUnset('backNeckCutout', self::NECK_CUTOUT);
        $this->setOptionIfUnset('sleevecapEase', self::SLEEVECAP_EASE);
        $this->setOptionIfUnset('bicepsEase', self::BICEPS_EASE);

        // Make shoulderslope configurable (for shoulder pads in jackets and so on)
        //$this->setOptionIfUnset('shoulderSlopeReduction', 0); // Make sure option is set
        //$this->setValueIfUnset('shoulderSlope', $model->m('shoulderSlope') - $this->o('shoulderSlopeReduction'));
        
        // Depth of the armhole
        //$this->setValueIfUnset('armholeDepth', $this->v('shoulderSlope') / 2 + ( $model->m('bicepsCircumference') + $this->o('bicepsEase') ) * $this->o('armholeDepthFactor'));

        // Heigth of the sleevecap
        //$this->setValueIfUnset('sleevecapHeight', ($model->m('bicepsCircumference') + $this->o('bicepsEase')) * $this->o('sleevecapHeightFactor'));
        
        // Collar width and depth
        //$this->setValueIfUnset('collarWidth', ($model->getMeasurement('neckCircumference') / 2.42) / 2);
        //$this->setValueIfUnset('collarDepth', ($model->getMeasurement('neckCircumference') + $this->getOption('collarEase')) / 5 - 8);

        // Cut front armhole a bit deeper
        //$this->setValueIfUnset('frontArmholeExtra', self::FRONT_ARMHOLE_EXTRA);

        // Tweak factors
        //$this->setValueIfUnset('frontCollarTweakFactor', 1); 
        //$this->setValueIfUnset('frontCollarTweakRun', 0); 
        //$this->setValueIfUnset('sleeveTweakFactor', 1); 
        //$this->setValueIfUnset('sleeveTweakRun', 0); 
    }

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
        //$this->finalizeBackBlock($model);
        
        $this->draftFrontBlock($model);
        //$this->finalizeFrontBlock($model);

        // Tweak the sleeve until it fits the armhole
        //do {
        //    $this->draftSleeveBlock($model);
        //} while (abs($this->armholeDelta()) > 1 && $this->v('sleeveTweakRun') < 50);
        //$this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta(),1).'mm off.');
        //$this->draftTopsleeveBlock($model);
        //$this->draftUndersleeveBlock($model);
        
        //$this->finalizeTopsleeveBlock($model);
        //$this->finalizeUndersleeveBlock($model);
        
        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to all parts
            //$this->paperlessFrontBlock($model);
            //$this->paperlessBackBlock($model);
            //$this->paperlessTopsleeveBlock($model);
            //$this->paperlessUndersleeveBlock($model);
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
        
        //$this->draftFrontBlock($model);
        //$this->finalizeFrontBlock($model);

        // Tweak the sleeve until it fits the armhole
        //do {
        //    $this->draftSleeveBlock($model);
        //} while (abs($this->armholeDelta()) > 1 && $this->v('sleeveTweakRun') < 50);
        //$this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta(),1).'mm off.');
        //$this->draftTopsleeveBlock($model);
        //$this->draftUndersleeveBlock($model);
    }

    /**
     * Drafts the front block
     *
     * I'm using a draft[part name] scheme here but
     * don't let that think that this is something specific
     * to the draft service.
     *
     * This draft method does the basic drafting and is
     * called by both the draft AND sample methods.
     *
     * The difference starts after this method is done.
     * For sample, this is all we need, but draft calls
     * the finalize[part name] method after this.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontBlock($model)
    {

        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontBlock'];

        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 100, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 0, 100);

        $p->newPath('test', 'M 1 L 2 L 3 L 4 z');
    }

    /**
     * Drafts the back block
     *
     * I'm using a draft[part name] scheme here but
     * don't let that think that this is something specific
     * to the draft service.
     *
     * This draft method does the basic drafting and is
     * called by both the draft AND sample methods.
     *
     * The difference starts after this method is done.
     * For sample, this is all we need, but draft calls
     * the finalize[part name] method after this.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBackBlock($model)
    {

        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 100, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 0, 100);

        $p->newPath('test', 'M 1 L 2 L 3 L 4 z');
    }

    /**
     * Drafts the sleeve block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSleeveBlock($model, $noTweak = false)
    {

    }

    protected function armholeLen()
    {
         /** @var \Freesewing\Part $back */
          $back = $this->parts['backBlock'];
         /** @var \Freesewing\Part $front */
          $front = $this->parts['frontBlock'];
  
          return ($back->curveLen(12, 19, 17, 10) + $back->curveLen(10, 18, 15, 14) + $back->curveLen(14, 16, 13,
                     5)) + ($front->curveLen(12, 19, 17, 10) + $front->curveLen(10, 18, 15, 14) + $front->curveLen(14, 16, 13, 5));
    }

    protected function sleevecapLen()
    {
         /** @var \Freesewing\Part $back */
         $p = $this->parts['sleeveBlock'];

          return (  
              $p->curveLen('topsleeveLeftEdge','topsleeveLeftEdge','frontPitchPointCpBottom','frontPitchPoint') + 
              $p->curveLen('frontPitchPoint','frontPitchPointCpTop','sleeveTopCpLeft','sleeveTop') +
              $p->curveLen('sleeveTop', 'sleeveTopCpRight', 'backPitchPoint', 'backPitchPoint') + 
              $p->curveLen('undersleeveTip','undersleeveTipCpBottom','undersleeveLeftEdgeCpRight','undersleeveLeftEdgeRight') +
              $p->distance('undersleeveLeftEdge','undersleeveLeftEdgeRight')
          );
    }

    protected function armholeDelta()
    {
        $this->setValue('armholeLength', $this->armholeLen());
        $this->setValue('sleevecapLength', $this->sleevecapLen());

        return $this->v('armholeLength') + $this->o('sleevecapEase') - $this->v('sleevecapLength');
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
