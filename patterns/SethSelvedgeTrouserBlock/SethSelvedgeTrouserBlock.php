<?php
/** Freesewing\Patterns\SethSelvedgeTrouserBlock class */
namespace Freesewing\Patterns;

/**
 * A selvedge trouser block based on the Trent Trouser Block
 *
 * Selvedge trousers have one side of the pattern (the outseam)
 * completely straight, so you can place them on the selvedge
 * of the fabric. This is common in selvedge jeans patterns
 * but can also be used for patterns where front and back leg
 * are in one piece (like leggings) because the straigh edge
 * means you can simply join them together.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SethSelvedgeTrouserBlock extends TrentTrouserBlock
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
        // Call TrentTrouserBlock::initialize()
        parent::initialize($model);
        
        /**
         * A bit of background on this shiftWaist info
         *
         * Shifting the waist so that it falls on the selfedge line means we have
         * to do no alterations to the top part of the fly, which is nice for
         * things like legging patterns.
         *
         * However, for selvedge jeans patterns, we don't really need to shift the waist
         * because the pocket opening will be cut out of the front panel.
         * This means that we don't  have to strictly keep it on the selvedge line, as
         * it will be cut off anyway.
         *
         * To allow this block to accomodate for both scenarios, you can set this value
         * to either true or false, depending on whether you want a shifted front waist or not
         */ 
        // Shall we shift the front waist or not?
        $this->setValue('shiftFrontWaist', true); 
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

        // Draft the parts of the parent pattern
        $this->draftFrame($model);
        $this->draftFrontBlock($model);
        $this->draftBackBlock($model);

        // Draft the selvedge parts
        $this->draftSelvedgeFront($model);
        $this->draftSelvedgeBack($model);

        // Don't render the frame
        $this->parts['.frame']->setRender(false);

        // Don't render the front and back blocks
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
        $this->sample($model);

        // Finalize front and back block
        //$this->finalizeFrontBlock($model);
        //$this->finalizeBackBlock($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to our example part
            //$this->paperlessExamplePart($model);
        }
    }

    /**
     * Drafts the selvedge front
     *
     * We are using a draft[part name] scheme here but
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
    public function draftSelvedgeFront($model)
    {
        // Clone front points
        $this->clonePoints('frontBlock', 'frontSelvedgeBlock');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontSelvedgeBlock'];
        
        // Should we shift the front waist?
        if($this->v('shiftFrontWaist')) {
            // Yes, we should. How much?
            $delta = $p->deltaX('frontHipsOut','frameHipsOut');
            // Move hips line
            $p->addPoint('frontHipsIn', $p->shift('frontHipsIn',0,$delta));
            $p->addPoint('frontHipsOut', $p->shift('frontHipsOut',0,$delta));
            // Adapt frontCrotchVerticalControlPoint
            $p->addPoint(
                'frontCrotchVerticalControlPoint', 
                $p->shiftTowards(
                    'frontHipsIn',
                    'frameSeatIn',
                    $p->distance('frontHipsIn','frameSeatIn') + 
                    $p->distance('frameSeatIn','frontCrotchVerticalControlPoint')
                )
            );
        }

        // Correction at crotch line
        // This adds correctionCrotchLine1
        $p->curveCrossesY('frameSeatOut','frontCpSeatDown','frontCpKneeOut','frontKneeOut',$p->y('frameCrotchLineOut'),'correctionCrotchLine');
        // Calculate correction
        $correction = $p->distance('correctionCrotchLine1','frameCrotchLineOut');
        // Move points
        foreach(['frameCrotchEdge','frontCpCrotchEdge'] as $point) {
            $p->addPoint($point, $p->shift($point,0,$correction));
        }
        
        // Correction at the knee - Calculate correction
        $correction = $p->distance('frontKneeOut','frameKneeOut');
        // Move points
        foreach(['frontKneeIn','frontCpKneeIn'] as $point) {
            $p->addPoint($point, $p->shift($point,0,$correction));
        }
        
        // Correction at the hem - Calculate correction
        $correction = $p->distance('frontHemOut','frameHemOut');
        // Move point
        $p->addPoint('frontHemIn', $p->shift('frontHemIn',0,$correction));
        
        // Paths
        $p->newPath('seamLine', 'M frontHemIn L frontKneeIn C frontCpKneeIn frameCrotchEdge frameCrotchEdge C frontCpCrotchEdge frontCrotchVerticalControlPoint frameSeatIn L frontHipsIn L frontHipsOut L frameHemOut z');
    }

    /**
     * Drafts the selvedge back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSelvedgeBack($model)
    {
        // Clone back points
        $this->clonePoints('backBlock', 'backSelvedgeBlock');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['backSelvedgeBlock'];

        // Shift the waistline to match the selvedge
        $p->addPoint('backSeatOutVerticalHelper', $p->shift('backSeatOut',90,20)); // Helper for beamsCross() below
        $p->addPoint('backHipsOut',$p->beamsCross('backSeatOut', 'backSeatOutVerticalHelper', 'backHipsOut', 'backHipsIn'));
        $p->addPoint('backHipsIn', $p->shiftTowards('backHipsOut','backHipsIn',$this->v('backHips')));
        // Adapt backSeamVerticalControlPoint
        $p->addPoint(
            'backSeamVerticalControlPoint', 
            $p->shiftTowards(
                'backHipsIn',
                'backSeamTiltPoint',
                $p->distance('backHipsIn','backSeamTiltPoint') + 
                $p->distance('backSeamTiltPoint','backSeamVerticalControlPoint')
            )
        );
        
        // Correction at crotch line
        // This adds correctionCrotchLine1
        $p->curveCrossesY('backSeatOut','backCpSeatOutDown','backCpKneeOut','backKneeOut',$p->y('backCrotchLineOut'),'correctionCrotchLine');
        // Calculate correction
        $correction = $p->deltaX('correctionCrotchLine1','backSeatOut');
        // Move point
        $p->addPoint('backCrotchEdge', $p->shift('backCrotchEdge',0,$correction));

        // Correction at the knee - Calculate correction
        $correction = $p->deltaX('backKneeOut','backSeatOut');
        // Move points
        foreach(['backKneeIn','backCpKneeIn'] as $point) {
            $p->addPoint($point, $p->shift($point,0,$correction));
        }
        
        // Correction at the hem - Calculate correction
        $correction = $p->deltaX('backHemOut','backSeatOut');
        // Move point
        $p->addPoint('backHemIn', $p->shift('backHemIn',0,$correction));
        
        // Create new hem point
        $p->newPoint('selvedgeHem', $p->x('backSeatOut'), $p->y('backHemOut')); 
        
        // Adjust inseam to match the front
        $inseamDelta = $this->inseamDelta();
        while(abs($inseamDelta)>1) { // bring delta below 1mm
            $id = $p->newId('inseamTweak');
            $p->clonePoint('backCrotchEdge',$id);
            $p->addPoint('backCrotchEdge',$p->shift('backCrotchEdge',90,$inseamDelta));
            $inseamDelta = $this->inseamDelta();
        }
        $this->msg('Inseam delta is '.$inseamDelta);
        
        // Adjust selvedge seam to match the front
        $selvedgeDelta = $this->selvedgeDelta();
        $count = 1;
        while(abs($selvedgeDelta)>1 & $count < 20) { // bring delta below 1mm
            $id = $p->newId('outseamTweak');
            $p->clonePoint('backHipsOut',$id);
            $p->addPoint('backHipsOut',$p->shift('backHipsOut',90,$selvedgeDelta));
            $selvedgeDelta = $this->selvedgeDelta();
            $count++;
        }
        $this->msg('Selvedge delta is '.$selvedgeDelta);
        
        
        // Paths
        $p->newPath('seamLine', 'M backHemIn L backKneeIn C backCpKneeIn backCrotchEdge backCrotchEdge C backCrotchEdge backSeamVerticalControlPoint backSeamTiltPoint L backHipsIn L backHipsOut L selvedgeHem z');

        $this->msg('Inseam delta is '.$this->unit($this->inseamDelta()));
        $this->msg('Outseam delta is '.$this->unit($this->selvedgeDelta()));
    }
        
    /**
     * Returns the difference between the front and back selvedge (outseam)
     *
     * Positive values mean the front outseam is longer
     *
     * @return float The outseam delta
     */
    protected function selvedgeDelta()
    {
        /** @var \Freesewing\Part $p */
        $front = $this->parts['frontSelvedgeBlock'];
        $frontLen = $front->deltaY('frontHipsOut', 'frameHemOut'); 
        
        /** @var \Freesewing\Part $p */
        $back = $this->parts['backSelvedgeBlock'];
        $backLen = $back->deltaY('backHipsOut', 'selvedgeHem');

        return ($frontLen - $backLen);
        $this->setValue('frontOutseam', $p->deltaY('frontHipsOut', 'frameHemOut'));
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
