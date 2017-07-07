<?php
/** Freesewing\Patterns\Beta\TrentTrouserBlock class */
namespace Freesewing\Patterns\Beta;

use Freesewing\Patterns\Core\Pattern;

/**
 * A trouser bock inspired by the work of Gareth Kershaw
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TrentTrouserBlock extends Pattern
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /** How much is the back wider than the front of the trousers */
    const BACK_HIPS_FACTOR = 0.545;
    const BACK_SEAT_FACTOR = 0.545;
    const BACK_LEG_FACTOR = 0.545;

    /** Fork parameters */
    const FRONT_FORK = 1.2; // Front fork jumps out 20% 

    /** Pleat placement */
    const FRONT_PLEAT = 0.375; // 37.5% of seat from center towards side

    /** Knee placement */
    const KNEE = 0.4; // 40% down the inseam

    /** Back rise factor, raise by 5.25% of hipCircumference */
    const BACK_RISE_FACTOR = 0.0525;

    /** Back slant factor, move right by 4.75% of hipCircumference */
    const BACK_SLANT_FACTOR = 0.0475;

    /** Position of the back dart from center back towards side of hip: 55% */
    const BACK_DART_POSITION = 0.55;

    /** Length of the back dart as ratio of distance from hip line to seat line */
    const BACK_DART_LENGTH = 0.5;

    /** Back seam reach, move left by 8.5% of seatCircumference */
    const BACK_SEAM_REACH = 0.085;

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
        // Back factor determines how much more than half (0.5) we add to the back
        $this->setValueIfUnset('backHipsFactor', self::BACK_HIPS_FACTOR);
        $this->setValueIfUnset('backSeatFactor', self::BACK_SEAT_FACTOR);
        $this->setValueIfUnset('backLegFactor', self::BACK_LEG_FACTOR);

        // Front factors are what remains
        $this->setValueIfUnset('frontHipsFactor', (1-self::BACK_HIPS_FACTOR));
        $this->setValueIfUnset('frontSeatFactor', (1-self::BACK_SEAT_FACTOR));
        $this->setValueIfUnset('frontLegFactor', (1-self::BACK_LEG_FACTOR));

        // Set values for hips
        $hips = $model->m('hipsCircumference') + $this->o('hipsEase');
        $this->setValueIfUnset('frontHip', $hips * $this->v('frontHipsFactor') / 2);
        $this->setValueIfUnset('backHip', $hips * $this->v('backHipsFactor') / 2);
        
        // Set values for seat
        $seat = $model->m('seatCircumference') + $this->o('seatEase');
        $this->setValueIfUnset('frontSeat', $seat * $this->v('frontSeatFactor') /2);
        $this->setValueIfUnset('backSeat', $seat * $this->v('backSeatFactor') /2);

        // Fork parameters
        $this->setValueIfUnset('frontFork', self::FRONT_FORK);

        // Pleat lines
        $this->setValueIfUnset('frontPleat', self::FRONT_PLEAT);
        
        // Knee placement
        $this->setValueIfUnset('knee', self:: KNEE);

        // Back rise and slant
        $this->setValueIfUnset('backRiseFactor', self::BACK_RISE_FACTOR);
        $this->setValueIfUnset('backSlantFactor', self::BACK_SLANT_FACTOR);

        // Back dart
        $this->setValueIfUnset('backDartPosition', self::BACK_DART_POSITION);
        $this->setValueIfUnset('backDartLength', self::BACK_DART_LENGTH);

        // Back fork
        $this->setValueIfUnset('backSeamReach', self::BACK_SEAM_REACH);
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

        // Draft the parts
        $this->draftFrame($model);
        $this->draftFrontBlock($model);
        $this->draftBackBlock($model);

        // Don't render the frame
        $this->parts['frame']->setRender(false);
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
     * Drafts the frame
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
    public function draftFrame($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frame'];

        // Points at side seam
        $p->newPoint('waistSide', 0, -1 * $model->m('naturalWaistToHip'));
        $p->newPoint('hipSide', 0, 0);
        $p->newPoint('seatSide', 0, $model->m('naturalWaistToSeat') - $model->m('naturalWaistToHip'));
        $p->newPoint('crotchSide', 0, $model->m('seatDepth'));
        $p->newPoint('hemSide', 0, $p->y('crotchSide') + $model->m('inseam'));

        // Points at center seam
        $p->newPoint('hipCenter', -1 * $this->v('frontHip'), $p->y('hipSide'));
        $p->newPoint('seatCenter', -1 * $this->v('frontSeat'), $p->y('seatSide'));
        $p->newPoint('crotchCenter', -1 * $this->v('frontSeat') * $this->v('frontFork'), $p->y('crotchSide'));
        $p->newPoint('hemCenter', $p->x('crotchCenter'), $p->y('hemSide'));

        // Remaining points
        $p->newPoint('crotchSeat', $p->x('seatCenter'), $p->y('crotchCenter'));
        $p->newPoint('waistCenter', $p->x('crotchCenter'), $p->y('waistSide'));
        
        $p->newPath('frame', '
            M hipSide 
            L hemSide
            L hemCenter
            L crotchCenter
            L crotchSeat
            L seatCenter
            L hipCenter
            z
        ', ['class' => 'debug']);

        $p->newLinearDimension('waistCenter','waistSide', 0, 'Natural waist line');
        $p->newLinearDimension('hipCenter','hipSide', 0, 'Hip line');
        $p->newLinearDimension('seatCenter','seatSide', 0, 'Seat line');
        $p->newLinearDimension('crotchCenter','crotchSide', 0, 'Crotch line');
    }


    /**
     * Drafts the front block
     *
     * @param \Freesewing\Model $model The model to draft for
     */
    public function draftFrontBlock($model)
    {
        // Clone frame points
        $this->clonePoints('frame', 'frontBlock');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontBlock'];

        // Move hip line to center
        $shift = $p->x('hipCenter') - $p->x('seatCenter');
        $p->addPoint('hipCenter', $p->shift('hipCenter', 180, $shift));
        
        // Pleat line
        $p->addPoint('frontPleatTop', $p->shift('hipCenter',0,$this->v('frontSeat') * $this->v('frontPleat')));
        $p->newPoint('frontPleatBottom', $p->x('frontPleatTop'), $p->y('hemSide'));
        
        // Add front dart if needed
        if($shift < 20) {
            $p->addPoint('hipSide', $p->shift('hipSide', 180, $shift));
            $this->setValueIfUnset('frontDart', false);
            
        } else {
            $p->addPoint('hipSide', $p->shift('hipSide', 180, $shift/2));
            $p->addPoint('frontDartRight', $p->shift('frontPleatTop', 0, $shift/4));
            $p->addPoint('frontDartLeft', $p->shift('frontPleatTop', 180, $shift/4));
            $p->newPoint('frontDartTip', $p->x('frontPleatTop'), $p->y('seatCenter') * 0.75);
            $this->setValueIfUnset('frontDart', true);
        }

        // Crotch curve
        $p->addPoint('crotchCurveCp', $p->shiftTowards('seatCenter', 'crotchSeat', $p->distance('seatCenter', 'crotchSeat') * $this->o('crotchCurveFactor')));

        // Legs | FIXME: Make legs smart
        $p->newPoint('kneePleat', $p->x('frontPleatTop'), $p->y('crotchCenter') + $model->m('inseam') * $this->v('knee'));
        $p->newPoint('kneeSide', $p->x('frontPleatTop') + 120, $p->y('kneePleat'));
        $p->newPoint('kneeCenter', $p->x('frontPleatTop') - 110, $p->y('kneePleat'));
        $p->newPoint('hemSide', $p->x('frontPleatTop') + 110, $p->y('hemSide'));
        $p->newPoint('hemCenter', $p->x('frontPleatTop') - 110, $p->y('hemSide'));

        // Side control points
        $p->newPoint('seatSideCpTop', $p->x('seatSide'), 0.5 * $p->y('seatSide'));
        $p->addPoint('kneeSideCpTop', $p->shiftTowards('hemSide', 'kneeSide', $p->distance('hemSide', 'kneeSide') * 1.3));
        $p->newPoint('kneeCenterCpTop', $p->x('kneeCenter'), $p->y('crotchCenter') + 0.2 * $p->deltaY('crotchCenter', 'kneeCenter'));

        if($this->v('frontDart')) $dart = ' L frontDartLeft L frontDartTip L frontDartRight ';
        else $dart = '';
        $p->newPath('outline', "
            M hipSide 
            C hipSide seatSideCpTop seatSide
            C crotchSide kneeSideCpTop kneeSide
            L hemSide
            L hemCenter
            L kneeCenter
            C kneeCenterCpTop crotchCenter crotchCenter
            C crotchCenter crotchCurveCp seatCenter
            L hipCenter
            $dart
            z
            M frontPleatTop L frontPleatBottom
        ");
           
        // Store location of pleat line
        $this->setValue('pleatX', $p->x('frontPleatTop'));

        // Store location of knee line
        $this->setValue('kneeY', $p->y('kneeSide'));

        // Store inseam out outseam length
        $this->setValue('inseamFront', 
            $p->curveLen('crotchCenter', 'crotchCenter', 'kneeCenterCpTop', 'kneeCenter') + 
            $p->distance('kneeCenter', 'hemCenter')
        ); 
        $this->setValue('outseamFront', 
            $p->curveLen('hipSide', 'hipSide', 'seatSideCpTop', 'seatSide') + 
            $p->curveLen('seatSide', 'crotchSide','kneeSideCpTop','kneeSide') +
            $p->distance('kneeSide', 'hemSide')
        ); 
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        
        // Add grid anchor
        $p->clonePoint('hipCenter', 'gridAnchor');
    }

    /**
     * Drafts the back block
     *
     * @param \Freesewing\Model $model The model to draft for
     */
    public function draftBackBlock($model)
    {
        // Clone frame points
        $this->clonePoints('frame', 'backBlock');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        // Save hip line or later before moving points
        $p->clonePoint('hipCenter', 'hipLineLeft');
        $p->addPoint('hipLineRight', $p->flipX('hipLineLeft'));
        $p->newPoint('topCenter', $p->x('seatCenter'), -1 * $model->m('hipsCircumference') * $this->v('backRiseFactor'));
        $p->addPoint('hipCenter', $p->shift('topCenter', 0, $model->m('hipsCircumference') * $this->v('backSlantFactor')));

        // Calculate back dart
        $backDart =   ($this->v('backSeat') - $this->v('backHip')) /2;
        if($backDart > 10) {
            $p->circleCrossesLine('hipCenter', $this->v('backHip') + $backDart, 'hipLineLeft', 'hipLineRight', 'isect'); 
            $p->clonePoint('isect2', 'hipSide');
            $p->addPoint('backDartCenter', $p->shiftTowards('hipCenter', 'hipSide', $p->distance('hipCenter', 'hipSide') * $this->v('backDartPosition')));
            $p->addPoint('backDartRight', $p->shiftTowards('backDartCenter', 'hipSide', $backDart/2));
            $p->addPoint('backDartLeft', $p->shiftTowards('backDartCenter', 'hipCenter', $backDart/2));
            $p->addPoint('backDartTip', $p->shift('backDartCenter', $p->angle('hipCenter','seatCenter')+180, $p->distance('hipCenter','seatCenter') * $this->v('backDartLength')));
            $this->setValueIfUnset('backDart', true);
        } else {
            $p->circleCrossesLine('hipCenter', $this->v('backHip'), 'hipLineLeft', 'hipLineRight', 'isect'); 
            $p->clonePoint('isect2', 'hipSide');
        }

        // Pleat line
        $p->newPoint('pleatLineBottom', $this->v('pleatX'), $p->y('hemSide'));
        $p->newPoint('pleatLineTop', $p->x('pleatLineBottom'), $p->y('hipLineLeft'));
        $p->addPoint('pleatLineTop', $p->beamsCross('pleatLineBottom', 'pleatLineTop', 'hipCenter', 'hipSide'));

        // Seat
        $p->addPoint('seatSide', $p->shift('seatCenter', $p->angle('hipCenter', 'hipSide')+180, $this->v('backSeat')));
        $p->addPoint('.helper1', $p->shift('seatCenter', $p->angle('hipCenter', 'hipSide'), $model->m('seatCircumference') * $this->v('backSeamReach')));
        $p->addPoint('backFork', $p->shift('.helper1', -90, $p->distance('seatCenter', 'crotchSeat')));

        // Knee FIXME: Make legs smart
        $p->newPoint('kneeMid', $p->x('pleatLineTop'), $this->v('kneeY'));
        $p->addPoint('kneeSide', $p->shift('kneeMid', 0, 138));
        $p->addPoint('kneeCenter', $p->shift('kneeMid', 180, 135));

        // Hem FIXME: Make legs smart
        $p->addPoint('hemSide', $p->shift('pleatLineBottom', 0, 135));
        $p->addPoint('hemCenter', $p->shift('pleatLineBottom', 180, 125));

        // Seat CP side
        $p->addPoint('.helper2', $p->shiftTowards('seatSide', 'seatCenter', $p->distance('seatSide', 'hipSide')/2));
        $p->addPoint('seatSideCpTop', $p->rotate('.helper2', 'seatSide', -90));
        $p->addPoint('seatSideCpBottom', $p->rotate('.helper2', 'seatSide', 90));

        // Knee CP
        $p->addPoint('kneeSideCpTop', $p->shiftTowards('hemSide', 'kneeSide', $p->distance('hemSide', 'kneeSide') * 1.4));
        $p->addPoint('kneeCenterCpTop', $p->shiftTowards('hemCenter', 'kneeCenter', $p->distance('hemCenter', 'kneeCenter') * 1.4));

        // Fork CP
        $p->addPoint('.helper3', $p->shift('backFork', 0, 20));
        $p->addPoint('forkCpMax', $p->beamsCross('backFork','.helper3','hipCenter','seatCenter'));
        $p->addPoint('seatCenterCp', $p->shiftTowards('seatCenter', 'forkCpMax', $p->distance('seatCenter', 'forkCpMax') * $this->o('backCurveFactor')));

        // Adjust outseam to match front by shifting leg down
        $delta = $this->outseamDelta();
        $count = 1;
        while (abs($delta) > 1) {
            $this->tweakOutseam($delta);
            $delta = $this->outseamDelta();
            $this->dbg("Iteration $count, outseam delta is $delta");
            $count ++;
            if($count> 100) die('There was a problem mathing the outseam length. Please report this.');
        }
        $this->dbg("After $count iterations, outseam delta is ". round($delta,2).'mm');

        // Adjust inseam to match front by shifting fork
        $delta = $this->inseamDelta();
        $count = 1;
        $break = true;
        while (abs($delta) > 1 && $break) {
            $this->tweakInseam($delta);
            $delta = $this->inseamDelta();
            $this->dbg("Iteration $count, inseam delta is $delta");
            $count ++;
            if($count> 20) $break = false;
        }
        $this->dbg("After $count iterations, inseam delta is ". round($delta,2).'mm');

        if($this->v('backDart')) $dart = ' L backDartLeft L backDartTip L backDartRight ';
        else $dart = '';

        $p->newPath('outline', "
            M seatCenter 
            L hipCenter 
            $dart 
            L hipSide
            C hipSide seatSideCpTop seatSide
            C seatSideCpBottom kneeSideCpTop kneeSide
            L hemSide
            L hemCenter
            L kneeCenter
            C kneeCenterCpTop backFork backFork
            C backFork seatCenterCp seatCenter
            z
            M pleatLineTop L pleatLineBottom
            ");
         
        // Mark path for sample service
        $p->paths['outline']->setSample(true);

        // Add grid anchor
        $p->clonePoint('hipCenter', 'gridAnchor');
    }


    /**
     * Adapts the inseam of the back block to match the front block
     */
    protected function tweakOutseam($delta)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        $shiftThese = ['kneeSideCpTop', 'kneeCenterCpTop', 'kneeSide', 'kneeMid', 'kneeCenter', 'hemSide', 'pleatLineBottom', 'hemCenter'];

        foreach($shiftThese as $pid) $p->addPoint($pid, $p->shift($pid, -90, $delta));
    }

    /**
     * Adapts the outseam of the back block to match the front block
     */
    protected function tweakInseam($delta)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        $shiftThese = ['backFork', 'seatCenterCp', 'seatCenter'];
        $angle = $p->angle('seatCenter', 'hipCenter') + 180;

        foreach($shiftThese as $pid) {
            $p->addPoint($p->newId(), $p->shift($pid, $angle, $delta));
            $p->addPoint($pid, $p->shift($pid, $angle, $delta));
        }
    }

    /**
     * Returns the difference between the front inseam and back inseam
     *
     * Positive values mean the front inseam is longer
     *
     * @return float The inseam delta
     */
    protected function inseamDelta()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];
        $this->setValue('inseamBack', $p->curveLen('backFork','backFork','kneeCenterCpTop','kneeCenter') + $p->distance('kneeCenter','hemCenter'));

        return ($this->v('inseamFront') - $this->v('inseamBack'));
    }

    /**
     * Returns the difference between the front outseam and back outseam
     *
     * Positive values mean the front outseam is longer
     *
     * @return float The outseam delta
     */
    protected function outseamDelta()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];
        $this->setValue('outseamBack', $p->curveLen('hipSide','hipSide','seatSideCpTop','seatSide') + $p->curveLen('seatSide','seatSideCpBottom','kneeSideCpTop','kneeSide') + $p->distance('kneeSide', 'hemSide'));

        return ($this->v('outseamFront') - $this->v('outseamBack'));
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
     * Finalizes the front block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrontBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontBlock'];

        // Title
        $p->newPoint('titleAnchor', $p->x('frontPleatWaist')+60, $p->y('frameSeatIn')+60);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('With good sides together'));
        
        // Grainline
        $p->newPoint('grainlineTop', $p->x('frontPleatWaist'), $p->y('frontHipsIn')+10); 
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('frontKneeIn')-10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('grainline')); 
        
        // Seat helpline
        $p->newPath('seatLine', 'M frameSeatIn L frameSeatOut', ['class' => 'help fabric']);
        
        // Seam allowance
        $p->offsetPath('sa','saBase', -10, 1, ['class' => 'sa fabric']);
        $p->offsetPath('hemSa','hemBase', 30, 1, ['class' => 'sa fabric']);
        // Join sa ends
        $p->newPath('saJoints', 'M sa-startPoint L hemSa-startPoint M hemSa-endPoint L sa-endPoint', ['class' => 'sa fabric']);
    }


    /**
     * Finalizes the back block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBackBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        // Title
        $p->newPoint('titleAnchor', $p->x('frontPleatWaist')+60, $p->y('frameSeatIn')+60);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('With good sides together'));

        // Grainline
        $p->newPoint('grainlineTop', $p->x('frontPleatWaist'), $p->y('backHipsIn')+20); 
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('frontKneeIn')-10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('grainline')); 
        
        // Seat helpline
        $p->newPath('seatLine', 'M backSeamTiltPoint L backSeatOut', ['class' => 'help fabric']);
        
        // Seam allowance
        $p->offsetPath('sa','saBase', -10, 1, ['class' => 'sa fabric']);
        $p->offsetPath('hemSa','hemBase', 30, 1, ['class' => 'sa fabric']);
        // Join sa ends
        $p->newPath('saJoints', 'M sa-startPoint L hemSa-startPoint M hemSa-endPoint L sa-endPoint', ['class' => 'sa fabric']);
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
