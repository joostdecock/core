<?php
/** Freesewing\Patterns\TrentTrouserBlock class */
namespace Freesewing\Patterns;

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
    
        // Keep crotchCurveFactor at 30 at least
        if($this->o('crotchCurveFactor') <= 0.3) $this->setValue('crotchCurveFactor', 0.3);
        else $this->setValue('crotchCurveFactor', $this->o('crotchCurveFactor'));
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

        // Finalize our example part
        $this->finalizeExamplePart($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to our example part
            $this->paperlessExamplePart($model);
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
        $p->addPoint('frameHemOut',$p->shift('frameWaistOut',-90,$model->m('outSeam')),'Hem at side seam');

        // Points at center/inseam
        $p->addPoint('frameWaistIn',$p->shift('frameWaistOut',180,$this->v('frontSeat')),'Waist center');
        $p->newPoint('frameHipsIn',$p->x('frameWaistIn'), $p->y('frameHipsOut'),'Hips center');
        $p->newPoint('frameSeatIn',$p->x('frameWaistIn'), $p->y('frameSeatOut'),'Seat center');
        $p->newPoint('frameCrotchLineIn',$p->x('frameWaistIn'), $p->y('frameCrotchLineOut'),'Crotch line center');
        $p->newPoint('frameCrotchEdge',$p->x('frameCrotchLineIn') - $this->v('frontSeat')/4 + 1, $p->y('frameCrotchLineOut'),'Crotch line edge');
        $p->newPoint('frameHemIn',$p->x('frameCrotchEdge'),$p->y('frameHemOut'),'Hem at inseam');

        // Knee height
        $p->addPoint('frameKneeOut', $p->shift('frameCrotchLineOut',-90,$model->m('inSeam')*0.42),'Knee height at outseam');
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
        $p->newPoint('pleatWaist',$p->x('frameWaistIn')*0.65,$p->y('frameWaistOut'),'Pleat/Grainline at natural waist');
        $p->newPoint('pleatHem',$p->x('pleatWaist'),$p->y('frameHemOut'),'Pleat/Grainline at hem');

        // Hips width set out from center
        $p->clonePoint('frameHipsIn','hipsIn');
        $p->addPoint('hipsOut', $p->shift('hipsIn',0,$this->v('frontHips')), 'Hip size at hip');

        // Move waist to take in 60% at side seam and 40% at center
        $shift = $p->distance('hipsOut','frameHipsOut')*0.6;
        $p->addPoint('hipsIn', $p->shift('hipsIn',0,$shift));
        $p->addPoint('hipsOut', $p->shift('hipsOut',0,$shift));

        // Front crotch curve
        $p->addPoint('crotchVerticalControlEdge', $p->beamsCross('hipsIn','frameSeatIn','frameCrotchEdge','frameCrotchLineIn'));
        $p->addPoint('crotchVerticalControlPoint', 
            $p->shiftTowards(
                'frameSeatIn',
                'crotchVerticalControlEdge',
                $p->distance(
                    'frameSeatIn',
                    'crotchVerticalControlEdge'
                ) * $this->v('crotchCurveFactor')
            ), 'Crotch control point'
        );
        $p->addPoint('crotchHorizontalControlPoint', $p->shift( 'frameCrotchEdge', 0,$p->distance('frameCrotchEdge','crotchVerticalControlEdge')*0.3)); 

        // Knee
        $p->newPoint('pleatKnee', $p->x('pleatWaist'), $p->y('frameKneeOut'),'Pleat/Grainline at the knee');
        $p->addPoint('kneeIn', $p->shift('pleatKnee',180,$this->v('frontHips')*0.478));
        $p->addPoint('kneeOut', $p->shift('pleatKnee',0,$this->v('frontHips')*0.522));
        
        // Knee
        $p->addPoint('hemIn', $p->shift('pleatHem',180,$this->v('frontHips')*0.478));
        $p->addPoint('hemOut', $p->shift('pleatHem',0,$this->v('frontHips')*0.478));

        // Control points for seamline
        $kneeCpBase = $p->deltaY('kneeIn','frameCrotchLineIn');
        $p->addPoint('cpKneeIn', $p->shift('kneeIn',-90,$kneeCpBase/2),'Control point above knee, inseam');
        $p->addPoint('cpKneeOutBase', $p->shiftTowards('kneeOut','hemOut', $kneeCpBase/2));
        $p->addPoint('cpKneeOut', $p->rotate('cpKneeOutBase','kneeOut',180), 'Control point above knee, inseam');
        $p->addPoint('cpSeatDown', $p->shift('frameSeatOut',90,$kneeCpBase/3),'Control point down from seat');
        $p->addPoint('cpSeatUp', $p->shift('frameSeatOut',-90,$p->deltaY('frameSeatOut','frameHipsOut')/2),'Control point up from seat');
        $p->addPoint('cpCrotchEgdeBase', $p->shiftAlong('frameCrotchEdge','frameCrotchEdge','cpKneeIn','kneeIn',$p->distance('frameCrotchEdge','crotchHorizontalControlPoint')));
        $p->addPoint('cpCrotchEdge', $p->rotate('cpCrotchEgdeBase','frameCrotchEdge',90));

        // Paths
        $p->newPath('seamLine', 'M hemIn L kneeIn C cpKneeIn frameCrotchEdge frameCrotchEdge C cpCrotchEdge crotchVerticalControlPoint frameSeatIn L hipsIn L hipsOut C hipsOut cpSeatUp frameSeatOut C cpSeatDown cpKneeOut kneeOut L hemOut z');



        $p->newPath('frame', 'M frameCrotchLineIn L frameWaistIn L frameWaistOut L frameHemOut M frameCrotchLineOut L frameCrotchEdge L frameHemIn L frameHemOut', ['class' => 'debug']);
        $p->newPath('hipsLine', 'M frameHipsIn L frameHipsOut', ['class' => 'helpline']);
        $p->newPath('seatLine', 'M frameSeatIn L frameSeatOut', ['class' => 'helpline']);
        $p->newPath('grainLine', 'M pleatWaist L pleatHem', ['class' => 'helpline']);
        $p->newPath('grainLine', 'M hipsIn L frameSeatIn C crotchVerticalControlPoint crotchHorizontalControlPoint frameCrotchEdge', ['class' => 'helpline']);
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
