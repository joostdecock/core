<?php
/** Freesewing\Patterns\Core\BruceBoxerBriefs class */
namespace Freesewing\Patterns\Core;

/**
 * The Bruce Boxer Briefs pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BruceBoxerBriefs extends Pattern
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
     * Ratio of different parts at the hips 
     * 
     * Take care to keep this sum of this = 1 (side counts double)
     */
    const HIP_RATIO_FRONT = 0.245;
    const HIP_RATIO_SIDE = 0.22;
    const HIP_RATIO_BACK = 0.315;

    /** Ratio of different parts at the legs 
     * 
     * Take care to keep this sum of this = 1 
     */
    const LEG_RATIO_INSET = 0.3;
    const LEG_RATIO_SIDE = 0.38;
    const LEG_RATIO_BACK = 0.32;

    /** Gusset widht in relation to waist = 6.66% */
    const GUSSET_RATIO = 0.0666;

    /** Part of crotch seam length that is attached to the inset (rest goes in the tusks) */
    const GUSSET_INSET_RATIO = 0.6;

    /** Height distribution between inset and front */
    const HEIGHT_RATIO_INSET = 0.65;
    const HEIGHT_RATIO_FRONT = 0.35;

    /**
     * Sets up options and values for our draft
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
        /* Set vertical scale to 1 (no stretch) */
        $this->setValueIfUnset('yScale', 1);   
        
        /* Set horizontal scale based on stretch */
        $this->setValueIfUnset('xScale', $this->stretchToScale($this->o('stretch')));   
        $this->setValueIfUnset('xScaleLegs', $this->stretchToScale($this->o('legStretch')));   
        
        /* Ratio of parts at the hips*/
        $this->setValueIfUnset('hips', $model->m('hipsCircumference') * $this->v('xScale'));
        $this->setValueIfUnset('hipsFront', $this->v('hips') * self::HIP_RATIO_FRONT); 
        $this->setValueIfUnset('hipsSide', $this->v('hips') * self::HIP_RATIO_SIDE);
        $this->setValueIfUnset('hipsBack', $this->v('hips') * self::HIP_RATIO_BACK);

        /* Ratio of parts at the legs*/
        $this->setValueIfUnset('leg', $model->m('upperLegCircumference') * $this->v('xScaleLegs'));
        $this->setValueIfUnset('legInset',$this->v('leg') * self::LEG_RATIO_INSET); 
        $this->setValueIfUnset('legSide', $this->v('leg') * self::LEG_RATIO_SIDE);
        $this->setValueIfUnset('legBack', $this->v('leg') * self::LEG_RATIO_BACK);

        /* Gusset */
        $this->setValueIfUnset('gusset', $model->m('hipsCircumference') * self::GUSSET_RATIO);
        $this->setValueIfUnset('gussetInsetRatio', self::GUSSET_INSET_RATIO);

        /* Length helper */
        $this->setValueIfUnset('length', $model->m('hipsToUpperLeg') * $this->v('yScale'));
        $this->setValueIfUnset('riseLength', ($model->m('hipsToUpperLeg') + $this->o('rise')) * $this->v('yScale'));
        $this->setValueIfUnset('fullLength', ($model->m('hipsToUpperLeg') + $this->o('rise') + $this->o('legBonus')) * $this->v('yScale'));
        
        /* Height ration front/inset */
        $this->setValueIfUnset('heightInset', $this->v('fullLength') * self::HEIGHT_RATIO_INSET);
        $this->setValueIfUnset('heightFront', $this->v('fullLength') * self::HEIGHT_RATIO_FRONT);

        /* Absolute amount to raise the back */
        $this->setValueIfUnset('backRise', $model->m('hipsCircumference') * $this->o('backRise'));
        $this->setValueIfUnset('sideRise', $this->v('backRise') * 0.75);;
        $this->setValueIfUnset('frontRise', $this->v('backRise') * 0.25);;
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
     * Generates a draft of the pattern
     *
     * This creates a draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bells and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->sample($model);

        //$this->finalizeBack($model);
        //$this->finalizeSide($model);
        //$this->finalizeFront($model);
        //$this->finalizeInset($model);

        if (false && $this->isPaperless) {
            $this->paperlessBack($model);
            $this->paperlessSide($model);
            $this->paperlessFront($model);
            $this->paperlessInset($model);
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
        //$this->draftBlock($model);
        $this->draftBack($model);
        $this->draftSide($model);
        $this->draftInset($model);
        $this->draftFront($model);
        // Don't render the block
        //$this->parts['block']->setRender(false);
    }

    /**
     * Drafts the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Center back 
        $p->newPoint('defaultCenter', 0, 0);
        $p->newPoint('elasticCenter', 0, $this->o('rise')*-1*$this->v('yScale'));
        $p->newPoint('center', 0, $p->y('elasticCenter') + $this->o('elasticWidth'));

        // Sides top
        $p->newPoint('sideRight', $this->v('hipsBack')/2, $p->y('center'));
        $p->addPoint('sideLeft', $p->flipX('sideRight'));

        // Gusset
        $p->newPoint('gussetTop', 0, $this->v('riseLength'));
        $p->newPoint('gussetBottom', 0, $p->y('gussetTop') + $this->v('gusset'));
        $p->newPoint('gussetRight', $this->v('gusset')/2 , $p->y('gussetBottom'));
        $p->addPoint('gussetLeft', $p->flipX('gussetRight'));
        $p->newPoint('gussetCpRight', $p->x('gussetRight'), $p->y('gussetTop'));
        $p->newPoint('gussetCpLeft', $p->x('gussetLeft'), $p->y('gussetTop'));

        // Find leg edge
        $p->circlesCross('gussetRight', $this->v('legBack'), 'sideRight', $this->v('fullLength'), 'isect');
        $p->clonePoint('isect2', 'legRight');
        $p->addPoint('legLeft', $p->flipX('legRight'));

        // Store back seam length and (half of the) crotch seam length
        $this->setValue('backSeamLength', $p->distance('sideRight','legRight'));
        $this->setValue('crotchSeamLength', $p->curveLen('gussetTop','gussetCpRight', 'gussetRight', 'gussetRight'));

        // Handle back rise
        $p->addPoint('center', $p->shift('center',90, $this->v('backRise')));
        $p->addPoint('sideRight', $p->shift('sideRight',90, $this->v('sideRise')));
        $p->addPoint('sideLeft', $p->shift('sideLeft',90, $this->v('sideRise')));
        $p->newPoint('centerCpRight', $p->x('sideRight')/2, $p->y('center'));
        $p->addPoint('centerCpLeft', $p->flipX('centerCpRight'));
        $this->dbg('back rise is '.$this->v('backRise'));
        $this->dbg('ratio is '.$this->v('hipRatioFront'));

        $p->newPath('outline', '
            M gussetLeft 
            C gussetLeft gussetCpLeft gussetTop 
            C gussetCpRight gussetRight gussetRight
            L legRight 
            L sideRight
            C sideRight centerCpRight center
            C centerCpLeft sideLeft sideLeft
            L legLeft
            L gussetLeft
            z');

        // Mark path for sample service
        $p->paths['outline']->setSample(true);

        $p->newPoint('scaleboxAnchor', 0,$p->y('center') + 20);
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        $p->newSnippet('logo', 'logo', 'isect1');
    }

    /**
     * Drafts the side
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];
        
        // Top left 
        $p->newPoint('defaultTopLeft', 0, 0);
        $p->newPoint('elasticTopLeft', 0, $this->o('rise')*-1*$this->v('yScale'));
        $p->newPoint('topLeft', 0, $p->y('elasticTopLeft') + $this->o('elasticWidth'));

        // Top right
        $p->newPoint('topRight', $this->v('hipsSide'), $p->y('topLeft'));

        // Bottom right
        $p->newPoint('bottomRight', $p->x('topRight'), $p->y('topRight') + $this->v('fullLength'));

        // Find bottom left
        $p->circlesCross('bottomRight', $this->v('legSide'), 'topLeft', $this->v('backSeamLength'), 'isect');
        $p->clonePoint('isect1', 'bottomLeft');

        // Store side seam length
        $this->setValue('sideSeamLength', $p->distance('topRight','bottomRight'));
        
        // Handle back rise
        $p->addPoint('topLeft', $p->shift('topLeft',90, $this->v('sideRise')));
        $p->addPoint('topRight', $p->shift('topRight',90, $this->v('frontRise')));


        $p->newPath('outline', '
            M topLeft 
            L topRight
            L bottomRight
            L bottomLeft
            z
        ');

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts the inset
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftInset($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['inset'];

        // height is 73.5%
        $p->newPoint('topLeft', 0,0);
        $p->newPoint('bottomLeft', 0, $this->v('heightInset'));
        $p->newPoint('bottomRight', $this->v('legInset'), $p->y('bottomLeft'));
        $p->newPoint('tip', $p->x('bottomRight') * 1.111, $p->y('bottomRight') - $this->v('gusset'));
        $p->addPoint('tip', $p->shiftTowards('bottomRight', 'tip', $this->v('crotchSeamLength') - $this->v('gusset') * (1-$this->v('gussetInsetRatio'))));
        $p->newPoint('tipCpTop', $this->v('gusset')*1.2, 0);
        $p->addPoint('tipCpBottom', $p->shift('tip', $p->angle('bottomRight', 'tip')-90, $this->v('gusset')*1.5));

        // Store cuve length
        $this->setValue('curve', $p->curveLen('tip', 'tipCpBottom', 'tipCpTop', 'topLeft'));

        $p->newPath('outline', '
            M topLeft 
            L bottomLeft
            L bottomRight
            L tip
            C tipCpBottom tipCpTop topLeft z
        ');
    
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }


    /**
     * Drafts the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        $p->newPoint('topRight', $this->v('hipsFront')/2, 0);
        $p->addPoint('topLeft', $p->flipX('topRight'));
        $p->newPoint('midMid', 0,$this->v('heightFront'));
        $p->newPoint('midRight', $p->x('topRight') + $this->v('heightFront')*0.05, $p->y('midMid'));
        $p->addPoint('midLeft', $p->flipX('midRight'));


        $p->newPoint('bottomMid', 0, $this->v('riseLength'));
        $p->newPoint('rightTuskRight', $this->v('gusset') * (1-$this->v('gussetInsetRatio')),  $p->y('bottomMid'));
        $p->clonePoint('bottomMid', 'rightTuskLeft');
        $p->newPoint('curveRightCpTop', $p->x('midRight') - $this->v('gusset')*1.3, $p->y('midRight'));
        $p->newPoint('curveRightCpBottom', $p->x('rightTuskRight'), $p->y('rightTuskRight') - $this->v('gusset')*1.3);

        // Adjust tusk length to fit inset curve
        $delta = $this->tuskDelta();
        $count = 0;
        while (abs($delta) > 1) { // Below 1mm is good enough
            $this->tweakTusk($delta);
            $delta = $this->tuskDelta();
            $count++;
            if($count>150) die("We got stuck trying to calculate an optimal tusk length. Please report this.");
            $this->dbg("Tusk tweak $count, delta is $delta");
        }
        $this->msg("After $count iterations, tusk curve length is ".round($delta,2)."mm off.");

        // Adjust midMid to new length
        $p->newPoint('bottomMid', 0, $p->y('rightTuskLeft'));

        // Front dart only if bulge > 0
        if($this->o('bulge') > 0) {
            
            // Rotate tusk according to bulge option
            foreach(['curveRightCpTop', 'curveRightCpBottom', 'rightTuskRight', 'rightTuskLeft'] as $pid) {
                $p->addPoint($pid, $p->rotate($pid, 'midRight', $this->o('bulge')));
            }

            // Dart join point
            $p->newPoint('dartJoin', 0, $p->y('midMid') + 0.65 * $p->distance('midMid', 'bottomMid'));

            // Dart control point
            $p->newPoint('dartCpRight', 0, $p->y('dartJoin') + ($p->distance('dartJoin', 'bottomMid') * ($this->o('bulge')/30)));
            $p->addPoint('dartCpRight', $p->rotate('dartCpRight', 'dartJoin', $this->o('bulge')/1));

            // Flip control point to left side
            $p->addPoint('dartCpLeft', $p->flipx('dartCpRight'));
        }

        // Flip points to left side
        $p->addPoint('leftTuskRight', $p->flipx('rightTuskLeft'));
        $p->addPoint('leftTuskLeft', $p->flipx('rightTuskRight'));
        $p->addPoint('curveLeftCpBottom', $p->flipx('curveRightCpBottom'));
        $p->addPoint('curveLeftCpTop', $p->flipx('curveRightCpTop'));

        // Handle back rise
        $p->newPoint('topMid', 0, $p->y('topLeft'));
        $p->addPoint('topLeft', $p->shift('topLeft',90, $this->v('frontRise')));
        $p->addPoint('topRight', $p->shift('topRight',90, $this->v('frontRise')));
        $p->newPoint('topMidCpRight', $p->x('topRight')/2, $p->y('topMid'));
        $p->addPoint('topMidCpLeft', $p->flipX('topMidCpRight'));

        if($this->o('bulge') > 0) { 
            $p->newPath('outline', '
                M midLeft
                L topLeft 
                C topLeft topMidCpLeft topMid
                C topMidCpRight topRight topRight
                L midRight
                C curveRightCpTop curveRightCpBottom rightTuskRight 
                L rightTuskLeft
                C rightTuskLeft dartCpRight dartJoin
                C dartCpLeft leftTuskRight leftTuskRight
                L leftTuskLeft
                C curveLeftCpBottom curveLeftCpTop midLeft
                z
            ');
        } else {
            $p->newPath('outline', '
                M midLeft
                L topLeft 
                L topRight
                L midRight
                C curveRightCpTop curveRightCpBottom rightTuskRight 
                L leftTuskLeft
                C curveLeftCpBottom curveLeftCpTop midLeft
                z
            ');
        }
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
   }

    protected function tweakTusk($delta)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        if (abs($delta)>2) $factor = 3;
        else $factor = 5;

        $p->addPoint('rightTuskRight', $p->shift('rightTuskRight', 90, $delta/$factor));
        $p->addPoint('rightTuskLeft', $p->shift('rightTuskLeft', 90, $delta/$factor));
        $p->addPoint('curveRightCpBottom', $p->shift('curveRightCpBottom', 90, $delta/$factor));
    }

    protected function tuskDelta()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];
        $len = $p->curveLen('midRight','curveRightCpTop','curveRightCpBottom','rightTuskRight');

        return $len - $this->v('curve');
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
     * Finalizes the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Seam allowance
        $p->offsetPathString('sa1','M cbXseam C cbXseamCp xseamLegCpRot xseamLeg L xseamHemRot', 10, 1, ['class' => 'fabric sa']);
        $p->offsetPathString('sa2','M xseamHemRot L hemBackSide', 20, 1, ['class' => 'fabric sa']);
        $p->offsetPathString('sa3','M hemBackSide L backSplit1 C backTopCurve3 backTopCurve2 cbTop', 10, 1, ['class' => 'fabric sa']);
        // Join sa parts
        $p->newPath('sa4', '
            M cbXseam L sa1-startPoint
            M sa1-endPoint sa2-startPoint 
            M sa2-endPoint L sa3-startPoint
            M sa3-endPoint L cbTop
        ', ['class' => 'fabric sa']);

        // Cut on fold
        $p->newPoint('cofTop', 0, $p->y('cbTop') + 20);
        $p->newPoint('cofBottom', 0, $p->y('cbXseam') - 20);
        $p->newCutonfold('cofBottom','cofTop',$this->t('Cut on fold').'  -  '.$this->t('Grainline'));

        // Scale box
        $p->newPoint('sbAnchor', 80, 80);
        $p->newSnippet('scalebox','scalebox','sbAnchor');

        // Title
        $p->newPoint('titleAnchor', 80, 180);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from fabric'));

        // Notches
        $p->notch(['cbTop', 'cbXseam']);

        // Notes
        $p->addPoint('noteAnchor1', $p->shiftTowards('hemBackSide','backSplit1', 100));
        $p->newNote($p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit(10).')', 8, 10, -5);
        $p->addPoint('noteAnchor2', $p->shiftTowards('xseamHemRot','hemBackSide', 50));
        $p->newNote($p->newId(), 'noteAnchor2', $this->t("Hem allowance")." (".$p->unit(20).')', 12, 25, -10);

        // Logo
        $p->newPoint('logoAnchor', $p->x('titleAnchor'), $p->y('cofTop')+40);
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
    }

    /**
     * Finalizes the side
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];

        // Seam allowance
        $p->offsetPathString('sa1', 'M backSplit1 C backSplit1 sideRealLeftCp sideLeftCorner', 10, 1, ['class' => 'fabric sa']);
        $p->offsetPathString('sa2', 'M sideLeftCorner C sideLeftCorner sideRightCp sideRightRot', 20, 1, ['class' => 'fabric sa']);
        $p->offsetPathString('sa3', 'M sideRightRot L frontSplit1 C sideTopCurve3 sideTopCurve2 backSplit1', 10, 1, ['class' => 'fabric sa']);
        // Join sa parts
        $p->newPath('sa4', '
            M sa3-endPoint L sa1-startPoint
            M sa1-endPoint L sa2-startPoint
            M sa2-endPoint L sa3-startPoint
        ', ['class' => 'fabric sa']); 
        
        // Title
        $p->newPoint('titleAnchor', $p->x('backSplit1') + $p->deltaX('backSplit1','frontSplit1')/2, $p->y('sideRightRot')/3);
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-90,100));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

        // Grainline
        $p->newPoint('glTop', $p->x('backSplit1')+20, $p->y('backSplit1')+20);
        $p->newPoint('glBottom', $p->x('glTop'), $p->y('sideLeftCorner')-40);
        $p->newGrainline('glBottom','glTop', $this->t('Grainline'));

        // Notes
        $p->addPoint('noteAnchor1', $p->shiftTowards('sideRightRot', 'frontSplit1', 60));
        $p->newNote( $p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit(10).')', 8, 10, -5);
        $p->addPoint('noteAnchor2', $p->shiftTowards('sideLeftCorner', 'sideRightRot', 80));
        $p->newNote( $p->newId(), 'noteAnchor2', $this->t("Hem allowance")."\n(".$p->unit(20).')', 12, 25, -3,['class' => 'note', 'dy' => -13, 'line-height' => 7]);
    }

    /**
     * Finzalizes the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Seam allowance
        $p->offsetPathString('sa1','
            M cfTop
            C sideTopCurve6 sideTopCurve7 frontSplit1
            L frontInset
            C frontInsetCp gussetTipCp gussetTip
            L cfXseam
        ', 10, 1, ['class' => 'fabric sa']);
        // This seam allowance on fold is a bit tricky. 
        $p->curveCrossesX('cfXseam','cfXseam','cfDartTopCp','cfDartTop',$p->x('cfDartTop')-10, 'sa-dart');
        $p->splitCurve('cfXseam','cfXseam','cfDartTopCp','cfDartTop','sa-dart1', 'sa-curve');
        $p->offsetPathString('sa2','M cfXseam C cfXseam sa-curve3 sa-dart1', 10, 1, ['class' => 'fabric sa']);
        // Joining SA parts
        $p->newPath('sa3', 'M sa2-endPoint L cfDartTop M sa2-startPoint L sa1-endPoint M sa1-startPoint L cfTop', ['class' => 'fabric sa']);

        // Cut on fold
        $p->newPoint('cofTop', $p->x('cfTop'), $p->y('cfTop')+10);
        $p->newPoint('cofBottom', $p->x('cfTop'), $p->y('cfDartTop')-10);
        $p->newCutonfold('cofBottom','cofTop',$this->t('Cut on fold').'  -  '.$this->t('Grainline'), -20);

        // Title
        $p->newPoint('titleAnchor', $p->x('frontSplit1')+15, $p->y('frontSplit1')+30);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '2x '.$this->t('from fabric'),'horizontal-small');

        // Notches
        $p->notch(['cfTop']);
        
        // Notes
        $p->addPoint('noteAnchor1', $p->shift('frontInset', 90, 30));
        $p->newNote( $p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit(10).')', 3, 10, -5);
        
        // Logo
        $p->newPoint('logoAnchor', $p->x('cofTop')-50, $p->y('cofTop')+40);
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
    }

    /**
     * Finalizes the inset
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeInset($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['inset'];
        
        // Seam allowance
        $p->offsetPathString('sa1', '
            M sideRight L frontInset
            C insetCpTop insetCpBottom insetCurveEnd
            L insetBottomRight
            ', -10, 1, ['class' => 'fabric sa']);
        $p->offsetPathString('sa2', 'M sideRight L insetBottomRight', 20, 1, ['class' => 'fabric sa']);
        // Joint SA parts
        $p->newPath('sa3', 'M sa1-startPoint L sa2-startPoint M sa1-endPoint L sa2-endPoint', ['class' => 'fabric sa']);

        // Gainline
        $p->newPoint('glTop', $p->x('frontInset')+15, $p->y('frontInset')+15);
        $p->newPoint('glBottom', $p->x('glTop'), $p->y('sideRight')-15);
        $p->newGrainline('glBottom', 'glTop', $this->t('Grainline'));
        
        // Title
        $p->newPoint('titleAnchor', $p->x('glBottom')+10, $p->y('glBottom')-30);
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'), 'horizontal-small');

        // Notes
        $p->addPoint('noteAnchor1', $p->shiftTowards('insetBottomRight', 'insetCurveEnd', 30));
        $p->newNote( $p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit(10).')', 9, 10, -5);
        $p->addPoint('noteAnchor2', $p->shift('sideRight', 0, 80));
        $p->newNote( $p->newId(), 'noteAnchor2', $this->t("Hem allowance")."\n(".$p->unit(20).')', 12, 45, -13,['class' => 'note', 'dy' => -13, 'line-height' => 7]);
        
        // Logo
        $p->newPoint('logoAnchor', $p->x('glTop')+15, $p->y('glTop')+20);
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
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
     * Adds paperless info for the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Heights on the left
        $xBase = 0;
        $p->newHeightDimension('cbXseam','cbTop',$xBase-15);
        $p->newHeightDimension('xseamHemRot','cbTop',$xBase-30);
        
        // Height on the right
        $xBase = $p->x('hemBackSide');
        $p->newHeightDimension('hemBackSide','backSplit1',$xBase+15);
        $p->newHeightDimension('hemBackSide','cbTop',$xBase+30);

        // Widht at the top
        $yBase = $p->y('cbTop');
        $p->newWidthDimension('cbTop', 'backSplit1',$yBase-25);

        // Widhts at the botom
        $p->newLinearDimension('xseamHemRot','hemBackSide', 35);
        $p->newWidthDimension('cbXseam','xseamHemRot', $p->y('xseamHemRot')+25);
        $p->newWidthDimension('cbXseam','hemBackSide', $p->y('xseamHemRot')+40);
    }

    /**
     * Adds paperless info for the side
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];

        // Heights on the left
        $p->newHeightDimension('sideLeftCorner', 'backSplit1', $p->x('sideLeftCorner')-30);

        // Heights on the right
        $xBase = $p->x('sideRightRot');
        $p->newHeightDimension('sideRightRot', 'frontSplit1', $xBase+20);
        $p->newHeightDimension('sideRightRot', 'backSplit1', $xBase+35);
        $p->newHeightDimension('sideLeftCorner', 'sideRightRot', $xBase+20);

        // Width at the top
        $p->newWidthDimension('backSplit1','frontSplit1', $p->y('backSplit1')-20);
        
        // Width at the bottom
        $p->addPoint('leftEdge', $p->curveEdge('backSplit1','backSplit1','sideRealLeftCp','sideLeftCorner','left'));
        $yBase = $p->y('sideLeftCorner');
        $p->newWidthDimension('sideLeftCorner', 'sideRightRot', $yBase+25);
        $p->newWidthDimension('leftEdge', 'sideRightRot', $yBase+40);
    }

    /**
     * Adds paperless info for the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Heights at the left
        $xBase = $p->x('frontSplit1');
        $p->newHeightDimension('frontInset', 'frontSplit1',$xBase-20);
        $p->newHeightDimension('gussetTip', 'frontSplit1',$xBase-35);
        $p->newHeightDimension('cfXseam', 'frontSplit1',$xBase-50);
        
        // Heights at the right
        $xBase = $p->x('cfTop');
        $p->newHeightDimension('cfXseam', 'cfDartTop',$xBase+15);
        $p->newHeightDimension('cfXseam', 'cfTop',$xBase+30);

        // Widths at the bottom
        $yBase = $p->y('cfXseam');
        $p->newWidthDimension('cfXseam','cfDartTop',$yBase+25);
        $p->newLinearDimension('gussetTip','cfXseam', 25);

        // Curve 
        $p->newCurvedDimension('M frontInset C frontInsetCp gussetTipCp gussetTip', 20);

        // Width at the top
        $p->newWidthDimension('frontSplit1', 'cfTop', $p->y('frontSplit1')-20);
    }

    /**
     * Adds paperless info for the inset
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessInset($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['inset'];

        // Height at the left
        $p->newHeightDimension('sideRight', 'frontInset', $p->x('sideRight')-20);

        // Height at the right
        $p->newHeightDimension('insetBottomRight', 'insetCurveEnd', $p->x('insetCurveEnd')+20);

        // Widths at the bottom
        $yBase = $p->y('sideRight');
        $p->newWidthDimension('sideRight','insetBottomRight', $yBase+35);
        $p->newWidthDimension('sideRight','insetCurveEnd', $yBase+50);
        
        // Curve
        $p->newCurvedDimension('M frontInset C insetCpTop insetCpBottom insetCurveEnd', -20);
    }
}
