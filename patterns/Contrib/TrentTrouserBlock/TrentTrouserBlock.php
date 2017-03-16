<?php
/** Freesewing\Patterns\Contrib\TrentTrouserBlock class */
namespace Freesewing\Patterns\Contrib;

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

    /**
     * How much is the back wider than the front of the trousers
     */
    const BACK_HIPS_FACTOR = 0.545;
    const BACK_SEAT_FACTOR = 0.545;
    const BACK_LEG_FACTOR = 0.545;

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
        $this->setValue('backHipsFactor', self::BACK_HIPS_FACTOR);
        $this->setValue('backSeatFactor', self::BACK_SEAT_FACTOR);
        $this->setValue('backLegFactor', self::BACK_LEG_FACTOR);

        // Front factors are what remains
        $this->setValue('frontHipsFactor', (1-self::BACK_HIPS_FACTOR));
        $this->setValue('frontSeatFactor', (1-self::BACK_SEAT_FACTOR));
        $this->setValue('frontLegFactor', (1-self::BACK_LEG_FACTOR));

        // Set values for hips
        $hips = $model->m('hipsCircumference') + $this->o('hipsEase');
        $this->setValue('frontHips', $hips * $this->v('frontHipsFactor') / 2);
        $this->setValue('backHips', $hips * $this->v('backHipsFactor') / 2);
        
        // Set values for seat
        $seat = $model->m('seatCircumference') + $this->o('seatEase');
        $this->setValue('frontSeat', $seat * $this->v('frontSeatFactor') /2);
        $this->setValue('backSeat', $seat * $this->v('backSeatFactor') /2);
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
        $this->parts['.frame']->setRender(false);
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
        $this->finalizeFrontBlock($model);
        $this->finalizeBackBlock($model);

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
        $p = $this->parts['.frame'];

        // Points at side seam
        $p->newPoint('frameWaistOut',0,0, 'Natural waist at side seam');
        $p->addPoint('frameHipsOut',$p->shift('frameWaistOut',-90,$model->m('naturalWaistToHip')),'Hips at side seam');
        $p->addPoint('frameSeatOut',$p->shift('frameWaistOut',-90,$model->m('naturalWaistToSeat')),'Seat at side seam');
        $p->addPoint('frameCrotchLineOut',$p->shift('frameWaistOut',-90,$model->m('bodyRise')),'Crotch line at side seam');
        $p->addPoint('frameHemOut',$p->shift('frameHipsOut',-90,$model->m('outseam')),'Hem at side seam');

        // Points at center/inseam
        $p->addPoint('frameWaistIn',$p->shift('frameWaistOut',180,$this->v('frontSeat')),'Waist center');
        $p->newPoint('frameHipsIn',$p->x('frameWaistIn'), $p->y('frameHipsOut'),'Hips center');
        $p->newPoint('frameSeatIn',$p->x('frameWaistIn'), $p->y('frameSeatOut'),'Seat center');
        $p->newPoint('frameCrotchLineIn',$p->x('frameWaistIn'), $p->y('frameCrotchLineOut'),'Crotch line center');
        $p->newPoint('frameCrotchEdge',$p->x('frameCrotchLineIn') - $this->v('frontSeat')/4 + 1, $p->y('frameCrotchLineOut'),'Crotch line edge');
        $p->newPoint('frameHemIn',$p->x('frameCrotchEdge'),$p->y('frameHemOut'),'Hem at inseam');

        // Knee height
        $p->addPoint('frameKneeOut', $p->shift('frameCrotchLineOut',-90,$model->m('inseam')*0.42),'Knee height at outseam');
        $p->newPoint('frameKneeIn', $p->x('frameCrotchEdge'), $p->y('frameKneeOut'), 'Knee height at inseam');
        
        // Paths
        $p->newPath('frame', 'M frameCrotchLineIn L frameWaistIn L frameWaistOut L frameHemOut M frameCrotchLineOut L frameCrotchEdge L frameHemIn L frameHemOut', ['class' => 'debug']);
        $p->newPath('hipsLine', 'M frameHipsIn L frameHipsOut', ['class' => 'helpline']);
        $p->newPath('seatLine', 'M frameSeatIn L frameSeatOut', ['class' => 'helpline']);
    }


    /**
     * Drafts the front block
     *
     * @param \Freesewing\Model $model The model to draft for
     */
    public function draftFrontBlock($model)
    {
        // Clone frame points
        $this->clonePoints('.frame', 'frontBlock');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontBlock'];

        // Pleatline sits 65% of the seat
        $p->newPoint('frontPleatWaist',$p->x('frameWaistIn')*0.65,$p->y('frameWaistOut'),'Pleat/Grainline at natural waist');
        $p->newPoint('frontPleatHem',$p->x('frontPleatWaist'),$p->y('frameHemOut'),'Pleat/Grainline at hem');

        // Hips width set out from center
        $p->clonePoint('frameHipsIn','frontHipsIn');
        $p->addPoint('frontHipsOut', $p->shift('frontHipsIn',0,$this->v('frontHips')), 'Hip size at hip');

        // Move waist to take in 60% at side seam and 40% at center
        $shift = $p->distance('frontHipsOut','frameHipsOut')*0.6;
        $p->addPoint('frontHipsIn', $p->shift('frontHipsIn',0,$shift));
        $p->addPoint('frontHipsOut', $p->shift('frontHipsOut',0,$shift));

        // Front crotch curve
        $p->addPoint('frontCrotchVerticalControlEdge', $p->beamsCross('frontHipsIn','frameSeatIn','frameCrotchEdge','frameCrotchLineIn'));
        // Assure shift is at least 50%, max 100% of delta 
        $delta = $p->distance('frameSeatIn','frontCrotchVerticalControlEdge');
        $shift = ($delta * 0.5) + ($delta * 0.5 * $this->o('crotchCurveFactor'));
        $p->addPoint('frontCrotchVerticalControlPoint', $p->shiftTowards( 'frameSeatIn', 'frontCrotchVerticalControlEdge', $shift), 'Crotch control point');
        $p->addPoint('frontCrotchHorizontalControlPoint', $p->shift( 'frameCrotchEdge', 0,$p->distance('frameCrotchEdge','frontCrotchVerticalControlEdge')*0.3)); 

        // Knee
        $p->newPoint('frontPleatKnee', $p->x('frontPleatWaist'), $p->y('frameKneeOut'),'Pleat/Grainline at the knee');
        $p->addPoint('frontKneeIn', $p->shift('frontPleatKnee',180,$this->v('frontHips')*0.478));
        $p->addPoint('frontKneeOut', $p->shift('frontPleatKnee',0,$this->v('frontHips')*0.522));
        
        // Hem
        $p->addPoint('frontHemIn', $p->shift('frontPleatHem',180,$this->v('frontHips')*0.478));
        $p->addPoint('frontHemOut', $p->shift('frontPleatHem',0,$this->v('frontHips')*0.478));

        // Control points for seamline
        $kneeCpBase = $p->deltaY('frontKneeIn','frameCrotchLineIn');
        $p->addPoint('frontCpKneeIn', $p->shift('frontKneeIn',-90,$kneeCpBase/2),'Control point above knee, inseam');
        $p->addPoint('frontCpKneeOutBase', $p->shiftTowards('frontKneeOut','frontHemOut', $kneeCpBase/2));
        $p->addPoint('frontCpKneeOut', $p->rotate('frontCpKneeOutBase','frontKneeOut',180), 'Control point above knee, outseam');
        $p->addPoint('frontCpSeatDown', $p->shift('frameSeatOut',90,$kneeCpBase/3),'Control point down from seat');
        $p->addPoint('frontCpSeatUp', $p->shift('frameSeatOut',-90,$p->deltaY('frameSeatOut','frameHipsOut')/2),'Control point up from seat');
        $p->addPoint('frontCpCrotchEgdeBase', $p->shiftAlong('frameCrotchEdge','frameCrotchEdge','frontCpKneeIn','frontKneeIn',$p->distance('frameCrotchEdge','frontCrotchHorizontalControlPoint')));
        $p->addPoint('frontCpCrotchEdge', $p->rotate('frontCpCrotchEgdeBase','frameCrotchEdge',90));

        // Paths
        $p->newPath('seamLine', 'M frontHemIn L frontKneeIn C frontCpKneeIn frameCrotchEdge frameCrotchEdge C frontCpCrotchEdge frontCrotchVerticalControlPoint frameSeatIn L frontHipsIn L frontHipsOut C frontHipsOut frontCpSeatUp frameSeatOut C frontCpSeatDown frontCpKneeOut frontKneeOut L frontHemOut z');
        // To knees only for muslin test
        $p->newPath('seamLine', 'M frontKneeIn C frontCpKneeIn frameCrotchEdge frameCrotchEdge C frontCpCrotchEdge frontCrotchVerticalControlPoint frameSeatIn L frontHipsIn L frontHipsOut C frontHipsOut frontCpSeatUp frameSeatOut C frontCpSeatDown frontCpKneeOut frontKneeOut z');

        /**
         * If you are studying this block, uncomment the paths below
         * they will help you understand its construction
         */
        //$p->newPath('frame', 'M frameCrotchLineIn L frameWaistIn L frameWaistOut L frameHemOut M frameCrotchLineOut L frameCrotchEdge L frameHemIn L frameHemOut', ['class' => 'debug']);
        //$p->newPath('hipsLine', 'M frameHipsIn L frameHipsOut', ['class' => 'helpline']);
        //$p->newPath('grainLine', 'M frontPleatWaist L frontPleatHem', ['class' => 'helpline']);
    }

    /**
     * Drafts the back block
     *
     * @param \Freesewing\Model $model The model to draft for
     */
    public function draftBackBlock($model)
    {
        // Clone frame points
        $this->clonePoints('frontBlock', 'backBlock');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        // Raise center back by 30% of frontHips
        $riseFactor = 0.3;
        $p->addPoint('backHeightIn', $p->shift('frameHipsIn',90,$this->v('frontHips')*$riseFactor),'Height of center back Inside');
        $p->newPoint('backHeightOut', $p->x('frameHipsOut'), $p->y('backHeightIn'),'Height of center back Outside');
        
        // Move center back inwards by 22.5% of frontHips
        $p->addPoint('backHipsIn', $p->shift('backHeightIn',0,$this->v('frontHips')*0.225), 'Center back');

        // Bring out outside by same factor (in theory, exact point follows below)
        $p->addPoint('backHipsOutInTheory', $p->shift('frameHipsOut',0,$this->v('frontHips')*$riseFactor),'Back hips outside');
        
        // Finding backHipsOut
        // ♬  Don't know much about history ♬  Don't know much trigonometry ♬ 
        $p->newPoint('.backHipsOutHelper', $p->x('backHipsIn'), $p->y('frameHipsIn'), 'Helper to find backHipsOut');
        $shift = sqrt($this->v('backHips')**2 - $p->deltaY('backHeightIn','frameHipsIn')**2);
        $p->addPoint('backHipsOutWithoutDart', $p->shift('.backHipsOutHelper',0,$shift),'Back hips outside witout dart');
        // Add back dart of 9.5%
        $this->setValue('backDart',$p->distance('backHipsIn','backHipsOutWithoutDart')*0.095);
        $shift = sqrt(($this->v('backDart')+$p->distance('backHipsIn','backHipsOutWithoutDart'))**2 - $p->deltaY('backHeightIn','frameHipsIn')**2);
        $p->addPoint('backHipsOut', $p->shift('.backHipsOutHelper',0,$shift),'Back hips outside with dart of '.$p->unit($this->v('backDart')));

        // Lower crotch line by 30% of seat/crotch vertical delta
        $p->addPoint('backCrotchLineHeight', $p->shift('frameCrotchEdge',-90,$p->deltaY('frameSeatIn','frameCrotchEdge')*0.3), 'Back crotch height');
        // Place same height on the frame, outseam side. 
        // Note that the real 'backCrotchLineOut point is added after we know the offset at the knee (because we reuse it)
        $p->newPoint('backCrotchLineOutFrame',$p->x('frameCrotchLineOut'), $p->y('backCrotchLineHeight'),'Back crotch height'); 

        // Raise back seam tilt point: 60% of seat/crotch vertical delta
        $p->addPoint('backSeamTiltPoint', $p->shift('frameSeatIn',90,$p->deltaY('frameSeatIn','frameCrotchEdge')*0.6), 'Back seam tilt point');

        // Move crotch point out 25% of backSeat
        $p->newPoint('backCrotchEdge', $p->x('frameCrotchEdge')-$this->v('backSeat')*0.25, $p->y('backCrotchLineHeight'), 'Back crotch edge');

        // Back seam curve
        $p->addPoint('backSeamVerticalControlEdge', $p->beamsCross('backHipsIn','backSeamTiltPoint','backCrotchEdge','backCrotchLineHeight'));
        // Assure shift is at least 60%, max 100% of delta 
        $delta = $p->distance('backSeamTiltPoint','backSeamVerticalControlEdge');
        $shift = (0.6 * $delta) + (0.4 * $delta * $this->o('backSeamCurveFactor'));
        $p->addPoint('backSeamVerticalControlPoint',$p->shiftTowards('backSeamTiltPoint','backSeamVerticalControlEdge',$shift), 'Back seam control point');
        
        // Knee
        $p->addPoint('backKneeIn', $p->shift('frontPleatKnee',180,$this->v('backHips')*0.478));
        $p->addPoint('backKneeOut', $p->shift('frontPleatKnee',0,$this->v('backHips')*0.522));

        // With the knee done, let's add the real 'backCrotchLineOut' point
        $p->addPoint('backCrotchLineOut',$p->shift('backCrotchLineOutFrame',0,$p->deltaX('backKneeIn','frontKneeIn')),'Back crotch height'); 
        
        // Hem
        $p->newPoint('backHemIn', $p->x('backKneeIn'), $p->y('frontHemIn'), 'Back inside hem'); 
        $p->addPoint('backHemOut', $p->flipX('backHemIn',$p->x('frontPleatHem')), 'Back outside hem'); 

        // Seat width
        $p->addPoint('backSeatOut', $p->shift('backSeamTiltPoint',$p->angle('backHipsIn','backHipsOut'),-1*$this->v('backSeat')));

        // Control points for seamline
        $kneeCpBase = $p->deltaY('backKneeIn','backCrotchEdge');
        $p->addPoint('backCpKneeIn', $p->shift('backKneeIn',-90,$kneeCpBase/2),'Control point above knee, inseam');
        $p->addPoint('backCpKneeOutBase', $p->shiftTowards('backKneeOut','backHemOut', $kneeCpBase/2));
        $p->addPoint('backCpKneeOut', $p->rotate('backCpKneeOutBase','backKneeOut',180), 'Control point above knee, outseam');
        $p->addPoint('backCpSeatOutUp', $p->shift('backSeatOut',$p->angle('backHipsIn','backSeamTiltPoint'), $p->distance('backHipsIn','backSeamTiltPoint')/2), 'Control point above seat, outseam');
        $p->addPoint('backCpSeatOutDown', $p->rotate('backCpSeatOutUp','backSeatOut',180), 'Control point below seat, outseam');

        // Adjust inseam to match the front
        $inseamDelta = $this->inseamDelta();
        while(abs($inseamDelta)>1) { // bring delta below 1mm
            $id = $p->newId('inseamTweak');
            $p->clonePoint('backCrotchEdge',$id);
            $p->addPoint('backCrotchEdge',$p->shift('backCrotchEdge',90,$inseamDelta));
            $inseamDelta = $this->inseamDelta();
        }
        $this->msg('Inseam delta is '.$inseamDelta);

        // Adjust outseam to match the front
        $outseamDelta = $this->outseamDelta();
        $count = 1;
        while(abs($outseamDelta)>1 & $count < 20) { // bring delta below 1mm
            $id = $p->newId('outseamTweak');
            $p->clonePoint('backHipsOut',$id);
            $p->addPoint('backHipsOut',$p->shift('backHipsOut',90,$outseamDelta));
            $outseamDelta = $this->outseamDelta();
            $count++;
        }
        $this->msg('Outseam delta is '.$outseamDelta);
        
        // Construct back dart, at 55% from center back to side, and 55% of hips to seat deep
        $angle = $p->angle('backHipsIn','backHipsOut');
        $p->addPoint('backDartMidPoint', $p->shiftTowards('backHipsIn','backHipsOut',$p->distance('backHipsIn','backHipsOut')*0.55),'Dart midpoint');
        $p->addPoint('backDartTip', $p->shift('backDartMidPoint',$angle+90,$p->distance('backSeamTiltPoint','backHipsIn')*0.55),'Dart tip');
        $p->addPoint('backDartLeftOnWaistline', $p->shift('backDartMidPoint',$angle,$this->getValue('backDart')/2),'Dart start on the left (on waistline)'); 
        $p->addPoint('backDartRightOnWaistline', $p->shift('backDartMidPoint',$angle,$this->getValue('backDart')/-2),'Dart start on the right (on waistline)'); 

        // Extend darts a bit to have a straight waistline after it's been closed
        $shift = $p->distance('backDartTip','backDartLeftOnWaistline') + 4;
        $p->addPoint('backDartLeft', $p->shiftTowards('backDartTip','backDartLeftOnWaistline',$shift),'Dart start on the left'); 
        $p->addPoint('backDartRight', $p->shiftTowards('backDartTip','backDartRightOnWaistline',$shift),'Dart start on the right'); 


        // Paths
        $p->newPath('seamLine', 'M backHemIn L backKneeIn C backCpKneeIn backCrotchEdge backCrotchEdge C backCrotchEdge backSeamVerticalControlPoint backSeamTiltPoint L backHipsIn L backDartLeft L backDartTip L backDartRight L backHipsOut C backHipsOut backCpSeatOutUp backSeatOut C backCpSeatOutDown backCpKneeOut backKneeOut L backHemOut z');
        
        // To knees only for muslin test
        $p->newPath('seamLine', 'M backKneeIn C backCpKneeIn backCrotchEdge backCrotchEdge C backCrotchEdge backSeamVerticalControlPoint backSeamTiltPoint L backHipsIn L backDartLeft L backDartTip L backDartRight L backHipsOut C backHipsOut backCpSeatOutUp backSeatOut C backCpSeatOutDown backCpKneeOut backKneeOut z');
        
        // Same but without darts to use as basis for seam allowance
        $p->newPath('seamLineNoDarts', 'M backKneeIn C backCpKneeIn backCrotchEdge backCrotchEdge C backCrotchEdge backSeamVerticalControlPoint backSeamTiltPoint L backHipsIn L backDartLeft L backDartRight L backHipsOut C backHipsOut backCpSeatOutUp backSeatOut C backCpSeatOutDown backCpKneeOut backKneeOut z');
        $p->paths['seamLineNoDarts']->setRender(false);

        /**
         * If you are studying this block, uncomment the paths below
         * they will help you understand its construction
         */
        //$p->newPath('frontseamLine', 'M frontHemIn L frontKneeIn C frontCpKneeIn frameCrotchEdge frameCrotchEdge C frontCpCrotchEdge frontCrotchVerticalControlPoint frameSeatIn L frontHipsIn L frontHipsOut C frontHipsOut frontCpSeatUp frameSeatOut C frontCpSeatDown frontCpKneeOut frontKneeOut L frontHemOut z', ['class' => 'helpline']);
        //$p->newPath('frame', 'M frameCrotchLineIn L frameWaistIn L frameWaistOut L frameHemOut M frameCrotchLineOut L frameCrotchEdge L frameHemIn L frameHemOut', ['class' => 'debug']);
        //$p->newPath('hipsLine', 'M frameHipsIn L frameHipsOut', ['class' => 'helpline']);
        //$p->newPath('frontSeatLine', 'M frameSeatIn L frameSeatOut', ['class' => 'helpline']);
        //$p->newPath('backHeight', 'M backHeightIn L backHeightOut', ['class' => 'helpline']);
        //$p->newPath('grainLine', 'M frontPleatWaist L frontPleatHem', ['class' => 'helpline']);
        //$p->newPath('crotchLine', 'M backCrotchEdge L backCrotchLineOut', ['class' => 'helpline']);
        //$p->newPath('hipsLine', 'M backHipsIn L backHipsOut', ['class' => 'helpline']);
    
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
        $front = $this->parts['frontBlock'];
        $frontLen = $front->curveLen('frameCrotchEdge','frameCrotchEdge','frontCpKneeIn','frontKneeIn') + $front->distance('frontKneeIn','frontHemIn');

        /** @var \Freesewing\Part $p */
        $back = $this->parts['backBlock'];
        $backLen = $back->curveLen('backCrotchEdge','backCrotchEdge','backCpKneeIn','backKneeIn') + $back->distance('backKneeIn','backHemIn');

        return ($frontLen - $backLen);
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
        $front = $this->parts['frontBlock'];
        $frontLen =  $front->curveLen('frontHipsOut','frontHipsOut','frontCpSeatUp','frameSeatOut') + $front->curveLen('frameSeatOut','frontCpSeatDown','frontCpKneeOut','frontKneeOut') + $front->distance('frontKneeOut', 'frontHemOut');
        
        /** @var \Freesewing\Part $p */
        $back = $this->parts['backBlock'];
        $backLen = $back->curveLen('backHipsOut','backHipsOut','backCpSeatOutUp','backSeatOut') + $back->curveLen('backSeatOut','backCpSeatOutDown','backCpKneeOut','backKneeOut') + $back->distance('backKneeOut', 'backHemOut');

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
        $p->newPath('seatLine', 'M frameSeatIn L frameSeatOut', ['class' => 'helpline']);
        
        // Seam allowance
        $p->offsetPath('sa','seamLine', -10, 1, ['class' => 'seam-allowance']);
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
        $p->newPath('seatLine', 'M backSeamTiltPoint L backSeatOut', ['class' => 'helpline']);
        
        // Seam allowance - we need to exclude the dart from it
        $p->offsetPath('sa','seamLineNoDarts', -10, 1, ['class' => 'seam-allowance']);
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
