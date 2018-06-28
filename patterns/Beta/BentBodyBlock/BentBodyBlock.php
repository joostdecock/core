<?php
/** Freesewing\Patterns\Beta\BentBodyBlock class */
namespace Freesewing\Patterns\Beta;

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
class BentBodyBlock extends \Freesewing\Patterns\Core\BrianBodyBlock
{
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
        $this->finalizeBackBlock($model);
        
        $this->draftFrontBlock($model);
        $this->finalizeFrontBlock($model);

        // Tweak the sleeve until it fits the armhole
        do {
            $this->draftSleeveBlock($model);
        } while (abs($this->armholeDelta()) > 1 && $this->v('sleeveTweakRun') < 50);
        $this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta(),1).'mm off.');
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
        
        $this->finalizeTopsleeveBlock($model);
        $this->finalizeUndersleeveBlock($model);
        
        // Hide the sleeveBlock
        $this->parts['sleeveBlock']->setRender(false);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to all parts
            $this->paperlessFrontBlock($model);
            $this->paperlessBackBlock($model);
            $this->paperlessTopsleeveBlock($model);
            $this->paperlessUndersleeveBlock($model);
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
        
        $this->finalizeBackBlock($model);
        $this->finalizeFrontBlock($model);

        // Tweak the sleeve until it fits the armhole
        do {
            $this->draftSleeveBlock($model);
        } while (abs($this->armholeDelta()) > 1 && $this->v('sleeveTweakRun') < 50);
        $this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta(),1).'mm off.');
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
        
        // Hide the sleeveBlock
        $this->parts['sleeveBlock']->setRender(false);
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeveBlock'];

        if($noTweak === false) {
            // Is this the first time we're calling draftSleeveBlock() ?
            if($this->v('sleeveTweakRun') > 0) {
                // No, this will be a tweaked draft. So let's tweak
                if($this->armholeDelta() > 0) {
                    //  Armhole is larger than sleeve head + sleevecap ease. Increase tweak factor
                    $this->setValue('sleeveTweakFactor', $this->v('sleeveTweakFactor')*1.01);
                } else {
                    //  Armhole is smaller than sleeve head + sleevecap ease. Decrease tweak factor
                    $this->setValue('sleeveTweakFactor', $this->v('sleeveTweakFactor')*0.97);
                }
                // Include debug message
                $this->dbg('Sleeve tweak run '.$this->v('sleeveTweakRun').'. Sleeve head is '.$this->armholeDelta().'mm off');
            }
            // Keep track of tweak runs because why not
            $this->setValue('sleeveTweakRun', $this->v('sleeveTweakRun')+1);
        }


        $this->setValue('sleevecapSeamLength', ($this->armholeLen() + $this->o('sleevecapEase'))*$this->v('sleeveTweakFactor'));

        // Sleeve frame
        $p->newPoint('sleeveTop', 0, 0, 'Top of the sleeve');
        $p->clonePoint('sleeveTop','gridAnchor');
        $p->newPoint('sleeveRightTop', $this->v('sleevecapSeamLength')/6 + 10, 0, 'Right top of the sleeve frame');
        $p->addPoint('sleeveLeftTop', $p->flipX('sleeveRightTop'), 'Left top of the sleeve frame');
        $p->newPoint('sleeveBottom', 0, $model->m('shoulderToWrist') + $this->o('sleeveLengthBonus'), 'Center-cuff of the sleeve');
        $p->newPoint('sleeveRightBottom', $p->x('sleeveRightTop'), $p->y('sleeveBottom'), 'Right bottom of the sleeve frame');
        $p->addPoint('sleeveLeftBottom', $p->flipX('sleeveRightBottom'), 'Left bottom of the sleeve frame');
        $p->newPoint('underarmCenter', 0, $this->v('sleevecapHeight') * $this->v('sleeveTweakFactor'),'Height of the underarm line');
        $p->newPoint('underarmRight', $p->x('sleeveRightTop'), $p->y('underarmCenter'),'Height of the underarm line, right');
        $p->newPoint('underarmLeft', $p->x('sleeveLeftTop'), $p->y('underarmCenter'),'Height of the underarm line, left');
        $p->newPoint('elbowCenter', 0, $model->m('shoulderToElbow'),'Height of the elbow line');
        $p->newPoint('elbowRight', $p->x('sleeveRightTop'), $p->y('elbowCenter'),'Height of the elbow line, right');
        $p->newPoint('elbowLeft', $p->x('sleeveLeftTop'), $p->y('elbowCenter'),'Height of the elbow line, left');
        
        // Using sleeve width to adapt other values
        $factor = $p->x('sleeveRightTop');
        $p->newPoint('backPitchPoint', $factor, $p->y('underarmCenter')/3, 'Back picth point');
        $p->addPoint('undersleeveTip', $p->shift('backPitchPoint',180,$factor/4), 'Tip of the undersleeve');
        $p->addPoint('topsleeveLeftEdge', $p->shift('underarmLeft',180,$factor/4), 'Left edge of the topsleeve');
        $p->addPoint('undersleeveLeftEdge', $p->shift('underarmLeft',0,$factor/4), 'Left edge of the undersleeve');
        $p->addPoint('topsleeveRightEdge', $p->shift('underarmRight',0,$factor/9), 'Right edge of the topsleeve');
        $p->addPoint('undersleeveRightEdge', $p->shift('underarmRight',180,$factor/9), 'Right edge of the undersleeve');
        $p->newPoint('frontPitchPoint', $p->x('sleeveLeftTop'), $p->y('underarmCenter')*0.6, 'Front picth point');
        $p->addPoint('topsleeveElbowLeft', $p->shift('elbowLeft',180,$factor/9), 'Topsleeve elbow left');
        $p->addPoint('undersleeveElbowLeft', $p->shift('elbowLeft',0,$factor/2.4), 'Undersleeve elbow left');
        
        // Different approach to sleeve bend, wrist right first
        $p->addPoint('.angleHelper', $p->rotate('sleeveRightBottom', 'elbowRight', $this->o('sleeveBend')*-1));
        $p->addpoint('topsleeveWristRight', $p->beamsCross('elbowRight','.angleHelper','sleeveLeftBottom','sleeveRightBottom'));
        $p->clonePoint('topsleeveWristRight', 'undersleeveWristRight');

        // Shift wrist left to the exact wrist width
        $wristWidth = $model->getMeasurement('wristCircumference') + $this->getOption('cuffEase');
        $topWrist = $wristWidth/2 + $factor/5;
        $underWrist = $wristWidth/2 - $factor/5;
        
        $p->newPoint('topsleeveWristLeftHelperBottom', $p->x('topsleeveWristRight')-$topWrist/2, $p->y('topsleeveWristRight'));
        $p->newPoint('undersleeveWristLeftHelperBottom', $p->x('undersleeveWristRight')-$underWrist/2, $p->y('undersleeveWristRight'));
        $p->addPoint('topsleeveWristLeftHelperTop', $p->shiftTowards('topsleeveElbowLeft', 'elbowRight', $p->distance('topsleeveElbowLeft', 'elbowRight')/2));
        $p->addPoint('undersleeveWristLeftHelperTop', $p->shiftTowards('undersleeveElbowLeft', 'elbowRight', $p->distance('undersleeveElbowLeft', 'elbowRight')/2));
        $topsleeveWristAngle = $p->angle('topsleeveWristLeftHelperBottom','topsleeveWristLeftHelperTop');
        $undersleeveWristAngle = $p->angle('undersleeveWristLeftHelperBottom','undersleeveWristLeftHelperTop');
        
        $p->addPoint('topsleeveWristLeft', $p->shift('topsleeveWristRight',$topsleeveWristAngle-90, $topWrist*-1)); 
        $p->addPoint('undersleeveWristLeft', $p->shift('undersleeveWristRight',$undersleeveWristAngle-90, $underWrist*-1)); 
         
        // Control points topsleeve
        $p->addPoint('topsleeveRightEdgeCpTop', $p->shift('topsleeveRightEdge',90,$p->deltaY('backPitchPoint','topsleeveRightEdge')/2));
        $p->addPoint('topsleeveRightEdgeCpBottom', $p->flipY('topsleeveRightEdgeCpTop',$p->y('topsleeveRightEdge')));
        $p->addPoint('elbowRightCpTop', $p->shiftTowards('topsleeveWristRight','elbowRight',$p->distance('topsleeveWristRight','elbowRight')*1.15));
        $p->addPoint('sleeveTopCpRight', $p->shift('sleeveTop',0,$factor/1.6));
        $p->addPoint('sleeveTopCpLeft', $p->flipX('sleeveTopCpRight',0));
        $p->addPoint('topsleeveLeftEdgeCpRight', $p->shift('topsleeveLeftEdge',0,$p->distance('topsleeveLeftEdge','underarmLeft')/2));
        $p->addPoint('frontPitchPointCpBottom', $p->shiftTowards('frontPitchPoint','topsleeveLeftEdgeCpRight', $p->distance('frontPitchPoint','topsleeveLeftEdgeCpRight')/1.5)); 
        $p->addPoint('frontPitchPointCpTop', $p->rotate('frontPitchPointCpBottom','frontPitchPoint', 180));
        $p->addPoint('topsleeveElbowLeftCpTop', $p->shiftTowards('topsleeveWristLeft', 'topsleeveElbowLeft', $p->distance('topsleeveWristLeft', 'topsleeveElbowLeft')*1.2));

        // Control points undersleeve
        $p->addPoint('undersleeveRightEdgeCpBottom', $p->shift('undersleeveRightEdge', $p->angle('undersleeveTip', 'elbowRight'), $p->deltaY('undersleeveTip', 'undersleeveRightEdge')/2));
        $p->addPoint('undersleeveRightEdgeCpTop', $p->rotate('undersleeveRightEdgeCpBottom', 'undersleeveRightEdge', 180));
        $p->addPoint('.helper1', $p->shiftAlong('backPitchPoint','backPitchPoint','sleeveTopCpRight', 'sleeveTop', 5));
        $p->addPoint('.helper2', $p->shiftAlong('backPitchPoint','backPitchPoint','topsleeveRightEdgeCpTop', 'topsleeveRightEdge', 5));
        $p->addPoint('undersleeveLeftEdgeRight', $p->shift('undersleeveLeftEdge',0,$p->distance('undersleeveLeftEdge', 'underarmCenter')/3));
        $p->addPoint('undersleeveLeftEdgeCpRight', $p->shift('undersleeveLeftEdge',0,$p->distance('undersleeveLeftEdge', 'underarmCenter')/1.2));

        // Angle of the undersleeveTip
        $angle = $p->angle('.helper1', 'backPitchPoint') - $p->angle('backPitchPoint','.helper2');

        $p->addPoint('undersleeveTipCpBottom', $p->rotate('undersleeveRightEdgeCpTop','undersleeveTip',-$angle));
        $p->addPoint('undersleeveElbowLeftCpTop', $p->shiftTowards('undersleeveWristLeft', 'undersleeveElbowLeft', $p->distance('undersleeveWristLeft', 'undersleeveElbowLeft')*1.2));

        // Paths
        $p->newPath('topsleeve', 'M topsleeveWristRight L elbowRight C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint C backPitchPoint sleeveTopCpRight sleeveTop C sleeveTopCpLeft frontPitchPointCpTop frontPitchPoint C frontPitchPointCpBottom topsleeveLeftEdgeCpRight topsleeveLeftEdge C topsleeveLeftEdge topsleeveElbowLeftCpTop topsleeveElbowLeft L topsleeveWristLeft z');

        $p->newPath('undersleeve', 'M undersleeveWristRight elbowRight C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip C undersleeveTipCpBottom undersleeveLeftEdgeCpRight undersleeveLeftEdgeRight L undersleeveLeftEdge C undersleeveLeftEdge undersleeveElbowLeftCpTop undersleeveElbowLeft L undersleeveWristLeft z');
        
        
        $p-> newPath('centerLine', 'M sleeveLeftTop L sleeveRightTop L sleeveRightBottom L sleeveLeftBottom z M sleeveTop L sleeveBottom M elbowLeft L elbowRight M underarmLeft L underarmRight', ['class' => 'helpline']);
    }

    /**
     * Drafts the topsleeve block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTopsleeveBlock($model)
    {
        $this->clonePoints('sleeveBlock','topsleeveBlock');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeveBlock'];

        // Paths
        $p->newPath('topsleeve', 'M topsleeveWristRight L elbowRight C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint C backPitchPoint sleeveTopCpRight sleeveTop C sleeveTopCpLeft frontPitchPointCpTop frontPitchPoint C frontPitchPointCpBottom topsleeveLeftEdgeCpRight topsleeveLeftEdge C topsleeveLeftEdge topsleeveElbowLeftCpTop topsleeveElbowLeft L topsleeveWristLeft z', ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['topsleeve']->setSample(true);
        $p->clonePoint('topsleeveWristRight', 'gridAnchor');

    }

    /**
     * Drafts the undersleeve block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftUndersleeveBlock($model)
    {
        $this->clonePoints('sleeveBlock','undersleeveBlock');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeveBlock'];
        
        // Paths
        $p->newPath('undersleeve', 'M undersleeveWristRight L elbowRight C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip C undersleeveTipCpBottom undersleeveLeftEdgeCpRight undersleeveLeftEdgeRight L undersleeveLeftEdge C undersleeveLeftEdge undersleeveElbowLeftCpTop undersleeveElbowLeft L undersleeveWristLeft L undersleeveWristRight z', ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['undersleeve']->setSample(true);
        $p->clonePoint('undersleeveWristRight', 'gridAnchor');
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


    /**
     * Finalizes the topsleeve block
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeTopsleeveBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeveBlock'];
        
        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'topsleeve', $this->o('sa')*-1, 1, ['class' => 'sa fabric']);

        // Title
        $p->clonePoint('elbowCenter','titleAnchor');
        $p->addTitle('titleAnchor',3,$this->t($p->getTitle()));

        // logo & scalebox
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-90,50));
        $p->newSnippet('logo','logo','logoAnchor');
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor',90,150));
        $p->newSnippet('scalebox','scalebox','scaleboxAnchor');
    }

    /**
     * Finalizes the undersleeve block
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeUndersleeveBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeveBlock'];
        
        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'undersleeve', $this->o('sa')*-1, 1, ['class' => 'sa fabric']);

        // Title
        $p->clonePoint('elbowCenter','titleAnchor');
        $p->addTitle('titleAnchor',4,$this->t($p->getTitle()),'','horizontal-small');

        // logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-90,50));
        $p->newSnippet('logo','logo-sm','logoAnchor');
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
     * Adds paperless info for the topsleeve block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessTopsleeveBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeveBlock'];

        // Add helpline
        $p->newPath('help', 'M topsleeveLeftEdge L topsleeveRightEdge', ['class' => 'fabric help']);

        // Heights on the right
        $xBase = $p->x('topsleeveRightEdge');
        $p->newHeightDimension('undersleeveWristRight', 'topsleeveRightEdge', $xBase+40); 
        $p->newHeightDimension('undersleeveWristRight', 'backPitchPoint', $xBase+55); 
        $p->newHeightDimension('undersleeveWristRight', 'sleeveTop', $xBase+70); 

        // Widhts at the bottom
        $yBase = $p->y('undersleeveWristRight');
        $p->newWidthDimension('topsleeveWristLeft', 'undersleeveWristRight', $yBase+25);
        $p->newWidthDimension('topsleeveLeftEdge', 'undersleeveWristRight', $yBase+40);

        // Height difference wrist
        $p->newHeightDimensionSm('undersleeveWristRight','topsleeveWristLeft', $p->x('topsleeveWristLeft')-25);

        // Lengths
        $p->newCurvedDimension('M undersleeveWristRight L elbowRight C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint', -25);
        $p->newCurvedDimension('M topsleeveWristLeft L topsleeveElbowLeft C topsleeveElbowLeftCpTop topsleeveLeftEdge topsleeveLeftEdge', 25);
        $p->newCurvedDimension('M topsleeveLeftEdge C topsleeveLeftEdgeCpRight frontPitchPointCpBottom frontPitchPoint C frontPitchPointCpTop sleeveTopCpLeft sleeveTop C sleeveTopCpRight backPitchPoint backPitchPoint', 25);

        $p->newLinearDimension('topsleeveLeftEdge', 'topsleeveRightEdge');
    }
    
    /**
     * Adds paperless info for the undersleeve block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessUndersleeveBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeveBlock'];

        // Lengths
        $p->newCurvedDimension('M undersleeveWristRight L elbowRight C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip', -25); 
        $p->newCurvedDimension('M undersleeveWristLeft L undersleeveElbowLeft C undersleeveElbowLeftCpTop undersleeveLeftEdge undersleeveLeftEdge', 25); 
        $p->newCurvedDimension('M undersleeveLeftEdge L undersleeveLeftEdgeRight C undersleeveLeftEdgeCpRight undersleeveTipCpBottom undersleeveTip', 25);

        // Height on the right
        $p->newHeightDimension('undersleeveWristRight','undersleeveTip', $p->x('elbowRightCpTop')+30);

        // Width
        $p->newWidthDimension('undersleeveLeftEdge','undersleeveTip', $p->y('undersleeveTip')-25);
        $p->newLinearDimension('undersleeveWristLeft','undersleeveWristRight', 25);
    }

}
