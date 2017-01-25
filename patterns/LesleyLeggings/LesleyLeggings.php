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
class LesleyLeggings extends SethSelvedgeTrouserBlock
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
        // Call SethSelvedgeTrouserBlock::initialize()
        parent::initialize($model);

        // Bolt down options available in parent patterns
        // hipsEase
        $this->setOption('hipsEase', 0);

        // seatEase
        $this->setOption('seatEase', 0);
        
        // crotchCurveFactor:
        $this->setOption('crotchCurveFactor', 0.4);
    
        // backSeamCurveFactor
        $this->setOption('backSeamCurveFactor', 0.4);
        
        // stretchFactor
        $this->setOption('stretchFactor', 1);
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

        // Draft the leggings
        $this->draftLeggings($model);
        
        // Don't render the frame
        $this->parts['.frame']->setRender(false);

        // Don't render the front and back blocks
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
        
        // Don't render the selvedge blocks
        $this->parts['frontSelvedgeBlock']->setRender(false);
        $this->parts['backSelvedgeBlock']->setRender(false);
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

        // Finalize the legggings
        $this->finalizeLeggings($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to our example part
            //$this->paperlessExamplePart($model);
        }
    }

    /**
     * Drafts the leggings
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
    public function draftLeggings($model)
    {
        // Clone front and back points
        $this->clonePoints('frontSelvedgeBlock', 'leggings');
        $this->clonePoints('backSelvedgeBlock', 'leggings');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['leggings'];

        // Find front mirror point
        $p->addPoint('mirrorPoint', $p->shift('frameHemOut', 0, $p->deltaX('frameHemOut', 'selvedgeHem')/2));
        // Mirror front points
        $toMirror = [
            'frontHemIn',
            'frontKneeIn',
            'frontCpKneeIn',
            'frameCrotchEdge',
            'frontCpCrotchEdge',
            'frontCrotchVerticalControlPoint',
            'frameSeatIn',
            'frontHipsIn',
            'frameHipsOut',
            'frameHemOut',
        ];
        $x = $p->x('mirrorPoint');
        foreach($toMirror as $id) {
            $p->addPoint($id,$p->flipX($id,$x));
        }

        // Shape leg
        $backFactor = self::BACK_LEG_FACTOR * $this->o('stretchFactor');
        $frontFactor = (1-$backFactor) * $this->o('stretchFactor');

        // Ankle width
        $p->addPoint('ankleFront', $p->shift('selvedgeHem',0,$model->m('ankleCircumference')*$frontFactor));
        $p->addPoint('ankleBack', $p->shift('selvedgeHem',180,$model->m('ankleCircumference')*$backFactor));

        // Knee width
        $p->newPoint('kneeBase', $p->x('selvedgeHem'), $p->y('frontKneeIn'));
        $p->addPoint('kneeFront',  $p->shift('kneeBase',0,$model->m('kneeCircumference')*$frontFactor));
        $p->addPoint('kneeBack', $p->shift('kneeBase',  180,$model->m('kneeCircumference')*$backFactor));

        // Upper leg width
        $p->newPoint('upperLegBase', $p->x('selvedgeHem'), $p->y('backCrotchEdge')+50);
        $p->addPoint('upperLegFront',  $p->shift('upperLegBase',0,$model->m('upperLegCircumference')*$frontFactor));
        $p->addPoint('upperLegBack', $p->shift('upperLegBase',180,$model->m('upperLegCircumference')*$backFactor));

        // Length bonus
        $p->addPoint('leggingHemBase', $p->shift('selvedgeHem',-90,$this->o('lengthBonus')));

        // Find new hem
        if($this->o('lengthBonus') > 0) {
            $p->addPoint('hemBack', $p->shift('ankleBack',-90,$this->o('lengthBonus')));
            $p->addPoint('hemFront', $p->shift('ankleFront',-90,$this->o('lengthBonus')));
        } else if($this->o('lengthBonus') < 0) {
            $p->addPoint('.hemHelper',$p->shift('leggingHemBase',0,20));
            $p->addPoint('hemBack', $p->beamsCross('ankleBack','kneeBack','leggingHemBase','.hemHelper'));
            $p->addPoint('hemFront', $p->beamsCross('ankleFront','kneeFront','leggingHemBase','.hemHelper'));
        } else if($this->o('lengthBonus') == 0) {
            $p->clonePoint('ankleBack','hemBack');
            $p->clonePoint('ankleFront','hemFront');
        }

        // Knee control points
        $p->addPoint('kneeCpBackDown', $p->shiftTowards('kneeBack','ankleBack',$p->distance('kneeBack','ankleBack')/2));
        $p->addPoint('kneeCpBack', $p->rotate('kneeCpBackDown','kneeBack',180));
        $p->addPoint('kneeCpFrontDown', $p->shiftTowards('kneeFront','ankleFront',$p->distance('kneeFront','ankleFront')/2));
        $p->addPoint('kneeCpFront', $p->rotate('kneeCpFrontDown','kneeFront',180));

        // Adjust back outseam to match the front
        $seamDelta = $this->seamDelta();
        $count = 1;
        while(abs($seamDelta)>1 && $count < 20) { // bring delta below 1mm
            $id = $p->newId('seamTweak');
            $p->clonePoint('backCrotchEdge',$id);
            $p->addPoint('backCrotchEdge',$p->shift('backCrotchEdge',90,$seamDelta));
            $seamDelta = $this->seamDelta();
            $count++;
        $this->msg("Run $count, Inseam delta is ".$this->unit($seamDelta));
        }
        
        // Adjust waistline to be more horizontal
        $delta = $p->deltaY('backHipsIn','frontHipsIn')/2-5;
        $p->addPoint('frontHipsIn', $p->shift('frontHipsIn',90,$delta));
        $p->addPoint('backHipsIn',$p->shiftTowards('backHipsIn', 'backSeamTiltPoint', $delta));

        // Waistline control points
        $delta = $p->deltaX('backHipsIn','frontHipsIn')/4;
        $p->addPoint('frontHipsInCp', $p->shift('frontHipsIn',180,$delta));
        $p->addPoint('backHipsInCp', $p->shift('backHipsIn',0,$delta));

        // Paths
        $seamLineFromKnees = 'L kneeFront C kneeCpFront frameCrotchEdge frameCrotchEdge C frontCpCrotchEdge frontCrotchVerticalControlPoint frameSeatIn L frontHipsIn C frontHipsInCp backHipsInCp backHipsIn L backSeamTiltPoint C backSeamVerticalControlPoint backCrotchEdge backCrotchEdge C backCrotchEdge kneeCpBack kneeBack';
        
        if($this->o('lengthBonus') > 0) {
            $seamLine = "M hemFront C ankleFront ankleFront kneeCpFrontDown $seamLineFromKnees L kneeCpBackDown C ankleBack ankleBack hemBack z";
        } else {
            $seamLine = "M hemFront $seamLineFromKnees L hemBack z";
        }
        $p->newPath('seamline', $seamLine);

    }

    /**
     * Returns the difference between the front outseam and back outseam
     *
     * Positive values mean the front outseam is longer
     *
     * @return float The outseam delta
     */
    protected function seamDelta()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['leggings'];
        if($this->o('lengthBonus') > 0) {
            $frontLen = $p->curveLen('hemFront','ankleFront','ankleFront','kneeCpFrontDown') + $p->distance('kneeCpFrontDown','kneeFront');
            $backLen = $p->curveLen('hemBack','ankleBack','ankleBack','kneeCpBackDown') + $p->distance('kneeCpBackDown','kneeBack'); 
        } else {
            $frontLen = $p->distance('hemFront','kneeFront');
            $backLen = $p->distance('hemBack','kneeBack');
        }
        $frontLen += $p->curveLen('kneeFront','kneeCpFront','frameCrotchEdge','frameCrotchEdge');
        $backLen += $p->curveLen('kneeBack','kneeCpBack','backCrotchEdge','backCrotchEdge');

        return ($frontLen - $backLen);
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
     * Finalizes the leggings
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeLeggings($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['leggings'];

        // Title
        $p->newPoint('titleAnchor',0,400);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('With good sides together'));

        // Logo
        $p->newPoint('logoAnchor', $p->x('titleAnchor'), $p->y('titleAnchor')+60);
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        $p->newSnippet('cc', 'cc', 'logoAnchor');
        
        // Seam allowance
        $p->offsetPath('sa','seamline',10,1,['class' => 'seam-allowance']);
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
