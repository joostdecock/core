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

        $this->finalizeBack($model);
        $this->finalizeSide($model);
        $this->finalizeFront($model);
        $this->finalizeInset($model);

        if ($this->isPaperless) {
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
        
        $this->draftBack($model);
        $this->draftSide($model);
        $this->draftInset($model);
        $this->draftFront($model);
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

        $p->newPath('outline', '
            M gussetTop
            C gussetCpRight gussetRight gussetRight
            L legRight 
            L sideRight
            C sideRight centerCpRight center
            L gussetTop
            z', ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
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
        ', ['class' => 'fabric']);

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
        ', ['class' => 'fabric']);
    
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

        // Store this length for a notch on the side part
        $this->setValue('frontNotch', $p->distance('topRight', 'midRight'));

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
            ', ['class' => 'fabric']);
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
            ', ['class' => 'fabric']);
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
        if($this->o('sa')) {
            $p->offsetPathString('sa1','M gussetTop C gussetCpRight gussetRight gussetRight', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->offsetPathString('sa2','M gussetRight L legRight', $this->o('sa')*2, 1, ['class' => 'fabric sa']);
            $p->offsetPathString('sa3','M legRight L sideRight C sideRight centerCpRight center', $this->o('sa'), 1, ['class' => 'fabric sa']);
            // Join sa parts
            $p->newPath('sa-join', 'M gussetTop L sa1-startPoint M sa1-endPoint L sa2-startPoint M sa2-endPoint L sa3-startPoint M sa3-endPoint L center', ['class' => 'fabric sa']);
        }
        
        // Cut on fold
        $p->addPoint('cofTop', $p->shift('center', -90, 20));
        $p->addPoint('cofBottom', $p->shift('gussetTop', 90, 20));
        $p->newCutonfold('cofBottom','cofTop',$this->t('Cut on fold').'  -  '.$this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x('centerCpRight'), $p->y('gussetTop') - 80);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));


        // Notes
        if($this->o('sa')) {
            $p->addPoint('noteAnchor1', $p->shiftTowards('legRight','sideRight', $p->distance('legRight','sideRight')/2));
            $p->newNote($p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 8, 10, -5);
            $p->addPoint('noteAnchor2', $p->shiftTowards('legRight','gussetRight', $p->distance('legRight','gussetRight')/2));
            $p->newNote($p->newId(), 'noteAnchor2', $this->t("Hem allowance")." (".$p->unit($this->o('sa')*2).')', 12, 25, -10);
        }

        // Logo
        $p->newPoint('logoAnchor', $p->x('titleAnchor'), $p->y('cofTop')+40);
        $p->newSnippet('logo', 'logo', 'logoAnchor');
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
        if($this->o('sa')) {
            $p->offsetPathString('sa1', 'M bottomRight L topRight L topLeft L bottomLeft', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->offsetPathString('sa2', 'M bottomLeft L bottomRight', $this->o('sa')*2, 1, ['class' => 'fabric sa']);
            // Join sa parts
            $p->newPath('sa-join', 'M sa1-endPoint L sa2-startPoint M sa2-endPoint L sa1-startPoint', ['class' => 'fabric sa']); 
        }
        
        // Title
        $p->newPoint('titleAnchor', $p->x('topRight')/2, $p->y('topRight')+120);
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-90,50));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');

        // Grainline
        $p->newPoint('glTop', $p->x('topRight')-20, $p->y('topRight')+20);
        $p->newPoint('glBottom', $p->x('glTop'), $p->y('bottomRight')-20);
        $p->newGrainline('glBottom','glTop', $this->t('Grainline'));

        // Notes
        if($this->o('sa')) {
            $p->addPoint('noteAnchor1', $p->shiftTowards('bottomRight', 'topRight', $p->distance('bottomRight', 'topRight')/3));
            $p->newNote( $p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 8, 10, -5);
            $p->addPoint('noteAnchor2', $p->shiftTowards('bottomRight', 'bottomLeft', $p->distance('bottomRight', 'bottomLeft')/2));
            $p->newNote( $p->newId(), 'noteAnchor2', $this->t("Hem allowance")."\n(".$p->unit(20).')', 12, 25, -3,['class' => 'note', 'dy' => -13, 'line-height' => 7]);
        }
        $p->addPoint('noteAnchor3', $p->shiftTowards('bottomRight', 'topRight', $p->distance('bottomRight', 'topRight')*0.6));
        $p->newNote( $p->newId(), 'noteAnchor3', $this->t("Front side"), 9, 25, 5);
        $p->addPoint('.helper', $p->shift('noteAnchor3', 180, 20));
        $p->addPoint('noteAnchor4', $p->beamsCross('noteAnchor3', '.helper', 'topLeft', 'bottomLeft'));
        $p->newNote( $p->newId(), 'noteAnchor4', $this->t("Back side"), 3, 25, 5);

        // Notches
        $p->addPoint('notch', $p->shiftTowards('topRight', 'bottomRight', $this->v('frontNotch')));
        $p->notch(['notch']);

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor',90,100));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
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
        if($this->o('sa')) {
            if($this->o('bulge') > 0) {
                $p->offsetPathString('sa1','
                    M topMid
                    C topMidCpRight topRight topRight
                    L midRight
                    C curveRightCpTop curveRightCpBottom rightTuskRight
                    L rightTuskLeft
                    C rightTuskLeft dartCpRight dartJoin
                    C dartCpLeft leftTuskRight leftTuskRight
                    L leftTuskLeft
                    C curveLeftCpBottom curveLeftCpTop midLeft 
                    L topLeft
                    C topLeft topMidCpLeft topMid
                    z
                ', $this->o('sa')*-1, 1, ['class' => 'fabric sa']);
            } else {
                $p->offsetPathString('sa1','
                    M midLeft
                    L topLeft 
                    L topRight
                    L midRight
                    C curveRightCpTop curveRightCpBottom rightTuskRight 
                    L leftTuskLeft
                    C curveLeftCpBottom curveLeftCpTop midLeft
                    z
                ', $this->o('sa')*-1, 1, ['class' => 'fabric sa']);
            }
        }
        
        // Grainline
        $p->addPoint('glTop',    $p->shift('topMid',-90,5));
        $p->addPoint('glBottom', $p->shift('midMid',-90,80));
        $p->newGrainline('glBottom', 'glTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', 0, $p->y('topMid')+70);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '2x '.$this->t('from fabric'));

        // Notches
        $p->notch(['midRight', 'midLeft']);
        
        // Notes
        if($this->o('sa')) {
            $p->addPoint('noteAnchor1', $p->shift('midLeft', 90, 20));
            $p->newNote( $p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 3, 10, -5);
        }

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-90,50));
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
        if($this->o('sa')) {
            $p->offsetPathString('sa1', ' M bottomLeft L topLeft C tipCpTop tipCpBottom tip L bottomRight ', $this->o('sa')*-1, 1, ['class' => 'fabric sa']);
            $p->offsetPathString('sa2', 'M bottomRight L bottomLeft', $this->o('sa')*-2, 1, ['class' => 'fabric sa']);
            // Joint SA parts
            $p->newPath('sa3', 'M sa1-startPoint L sa2-endPoint M sa1-endPoint L sa2-startPoint', ['class' => 'fabric sa']);
        }

        // Grainline
        $p->addPoint('glTop', $p->shift('topLeft', -45, 15));
        $p->addPoint('glBottom', $p->shift('bottomLeft', 45, 15));
        $p->newGrainline('glBottom', 'glTop', $this->t('Grainline'));
        
        // Title
        $p->newPoint('titleAnchor', $p->x('bottomLeft')+30, $p->y('bottomLeft')-30);
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'), 'horizontal-small');

        // Notes
        if($this->o('sa')) {
            $p->addPoint('noteAnchor1', $p->shiftTowards('bottomRight', 'tip', $p->distance('bottomRight', 'tip')/2));
            $p->newNote( $p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 9, 10, -5);
            $p->addPoint('noteAnchor2', $p->shiftTowards('bottomRight', 'bottomLeft', $p->distance('bottomRight', 'bottomLeft')/2));
            $p->newNote( $p->newId(), 'noteAnchor2', $this->t("Hem allowance")."\n(".$p->unit($this->o('sa')*2).')', 12, 45, -13,['class' => 'note', 'dy' => -13, 'line-height' => 7]);
        }

        // Logo
        $p->newPoint('logoAnchor', $p->x('topLeft')+25, $p->y('topLeft')+40);
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
        $p->newHeightDimension('gussetTop','center',$xBase-15);
        $p->newHeightDimension('gussetRight','center',$xBase-30);
        
        // Height on the right
        $xBase = $p->x('legRight');
        $p->newHeightDimension('legRight','center',$xBase+15);
        $p->newHeightDimensionSm('sideRight','center',$xBase);

        // Widht at the top
        $yBase = $p->y('center');
        $p->newWidthDimension('center', 'sideRight',$yBase-20);

        // Widhts at the botom
        $yBase = $p->y('gussetRight');
        $p->newWidthDimension('gussetTop','gussetRight', $yBase+30);
        $p->newWidthDimension('gussetTop', 'legRight',$yBase+45);
        $p->newLinearDimension('gussetRight','legRight', 30);
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

        // Height on the left
        $p->newHeightDimension('bottomLeft', 'topLeft', $p->x('topLeft')-25);

        // Height on the right
        $p->newHeightDimension('bottomRight', 'topRight', $p->x('topRight')+25);

        // Width at the top
        $p->newWidthDimension('topLeft','topRight', $p->y('topLeft')-20);
        
        // Width at the bottom
        $p->newWidthDimension('bottomLeft','bottomRight', $p->y('bottomLeft')+35);
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
        $xBase = $p->x('midLeft');
        $p->newHeightDimension('midLeft', 'topLeft',$xBase-20);
        if($this->o('bulge') > 0) {
            $p->newHeightDimension('dartJoin', 'topLeft',$xBase-35);
            $p->newHeightDimension('leftTuskLeft', 'topLeft',$xBase-50);
            $p->newHeightDimension('leftTuskRight', 'topLeft',$xBase-65);
        } 
        // Width at narrowest point
        $p->addPoint('curveRight', $p->curveEdge('midRight', 'curveRightCpTop', 'curveRightCpBottom', 'rightTuskRight', 'left'));
        $p->addPoint('curveLeft', $p->flipX('curveRight'));
        $p->newLinearDimension('curveLeft','curveRight');

        // Heights at the right
        $xBase = $p->x('midRight');
        $p->newHeightDimension('curveRight', 'topRight',$xBase+25);

        // Widths at the bottom
        $yBase = $p->y('rightTuskLeft');
        $p->newWidthDimension('leftTuskRight','rightTuskLeft',$yBase+25);
        $p->newWidthDimension('leftTuskLeft','rightTuskRight',$yBase+40);
        
        // Tusk width
        $p->newLineardimensionSm('rightTuskLeft','rightTuskRight',30);

        // Width at the top
        $yBase = $p->y('topLeft');
        $p->newWidthDimension('topLeft', 'topRight', $yBase-20);
        $p->newWidthDimension('midLeft', 'midRight', $yBase-35);

        // Front drop
        $p->newHeightDimensionSm('topMid', 'topLeft', 10);
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
        $p->newHeightDimension('bottomLeft', 'topLeft', $p->x('topLeft')-20);

        // Height at the right
        $p->newHeightDimension('bottomRight', 'tip', $p->x('tip')+20);

        // Widths at the bottom
        $yBase = $p->y('bottomLeft');
        $p->newWidthDimension('bottomLeft','bottomRight', $yBase+35);
        $p->newWidthDimension('bottomLeft','tip', $yBase+50);
    }
}
