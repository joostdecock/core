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

        $this->draftSleeveBlock($model);
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
        //$this->finalizeSleeveBlock($model);
        
        // Hide the sleeveBlock
        $this->parts['sleeveBlock']->setRender(false);
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
        
        $this->draftSleeveBlock($model);
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

        $this->setValue('sleevecapSeamLength', ($this->armholeLen() + $this->o('sleevecapEase'))*$this->v('sleeveTweakFactor'));

        // Sleeve frame
        $p->newPoint('sleeveTop', 0, 0, 'Top of the sleeve');
        $p->newPoint('sleeveRightTop', $this->v('sleevecapSeamLength')/6 + 10, 0, 'Right top of the sleeve frame');
        $p->addPoint('sleeveLeftTop', $p->flipX('sleeveRightTop'), 'Left top of the sleeve frame');
        $p->newPoint('sleeveBottom', 0, $model->m('sleeveLengthToWrist'), 'Center-cuff of the sleeve');
        $p->newPoint('sleeveRightBottom', $p->x('sleeveRightTop'), $p->y('sleeveBottom'), 'Right bottom of the sleeve frame');
        $p->addPoint('sleeveLeftBottom', $p->flipX('sleeveRightBottom'), 'Left bottom of the sleeve frame');
        $p->newPoint('underarmCenter', 0, $this->v('sleevecapHeight'),'Height of the underarm line');
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
        $p->addPoint('sleeveWristLeft', $p->shift('sleeveLeftBottom',90,$factor/5), 'Wrist left');
        $p->addPoint('topsleeveWristLeft', $p->shift('sleeveWristLeft',180,$factor/4.5), 'Topsleeve wrist left');
        $p->addPoint('undersleeveWristLeft', $p->shift('sleeveWristLeft',0,$factor/4.5), 'Undersleeve wrist left');
        
        $wristWidth = $model->getMeasurement('wristCircumference') + $this->getOption('cuffEase');
        $topWrist = $wristWidth/2 + $factor/5;
        $underWrist = $wristWidth/2 - $factor/5;
        $p->newPoint('topsleeveWristRight', $p->x('topsleeveWristLeft')+ sqrt(pow($topWrist,2)-pow($p->deltaY('sleeveLeftBottom','sleeveWristLeft'),2)), $p->y('sleeveLeftBottom'));
        $p->newPoint('undersleeveWristRight', $p->x('undersleeveWristLeft')+ sqrt(pow($underWrist,2)-pow($p->deltaY('sleeveLeftBottom','sleeveWristLeft'),2)), $p->y('sleeveLeftBottom'));
        // Force right edge of top and undersleeve to fall on the same point
        $delta = $p->deltaX('topsleeveWristRight','undersleeveWristRight');
        $p->addPoint('topsleeveWristRight', $p->shift('topsleeveWristRight',0,$delta/2));
        $p->addPoint('undersleeveWristRight', $p->shift('undersleeveWristRight',180,$delta/2));
        
        // Control points topsleeve
        $p->addPoint('topsleeveRightEdgeCpTop', $p->shift('topsleeveRightEdge',90,$p->deltaY('backPitchPoint','topsleeveRightEdge')/2));
        $p->addPoint('topsleeveRightEdgeCpBottom', $p->flipY('topsleeveRightEdgeCpTop',$p->y('topsleeveRightEdge')));
        $p->addPoint('elbowRightCpTop', $p->beamsCross('topsleeveWristRight','elbowRight','topsleeveRightEdgeCpTop','topsleeveRightEdge'));
        $p->addPoint('sleeveTopCpRight', $p->shift('sleeveTop',0,$factor/1.6));
        $p->addPoint('sleeveTopCpLeft', $p->flipX('sleeveTopCpRight',0));
        $p->addPoint('topsleeveLeftEdgeCpRight', $p->shift('topsleeveLeftEdge',0,$p->distance('topsleeveLeftEdge','underarmLeft')/2));
        $p->addPoint('frontPitchPointCpBottom', $p->shiftTowards('frontPitchPoint','topsleeveLeftEdgeCpRight', $p->distance('frontPitchPoint','topsleeveLeftEdgeCpRight')/1.5)); 
        $p->addPoint('frontPitchPointCpTop', $p->rotate('frontPitchPointCpBottom','frontPitchPoint', 180));
        $p->addPoint('topsleeveElbowLeftCpTop', $p->shiftTowards('topsleeveWristLeft', 'topsleeveElbowLeft', $p->distance('topsleeveWristLeft', 'topsleeveElbowLeft')*1.2));

        // Control points undersleeve
        $p->addPoint('undersleeveRightEdgeCpBottom', $p->shift('undersleeveRightEdge', $p->angle('elbowRight', 'undersleeveTip'), $p->deltaY('undersleeveTip', 'undersleeveRightEdge')/2));
        $p->addPoint('undersleeveRightEdgeCpTop', $p->rotate('undersleeveRightEdgeCpBottom', 'undersleeveRightEdge', 180));
        $p->addPoint('.helper1', $p->shiftAlong('backPitchPoint','backPitchPoint','sleeveTopCpRight', 'sleeveTop', 5));
        $p->addPoint('.helper2', $p->shiftAlong('backPitchPoint','backPitchPoint','topsleeveRightEdgeCpTop', 'topsleeveRightEdge', 5));
        $p->addPoint('undersleeveLeftEdgeRight', $p->shift('undersleeveLeftEdge',0,$p->distance('undersleeveLeftEdge', 'underarmCenter')/3));
        $p->addPoint('undersleeveLeftEdgeCpRight', $p->shift('undersleeveLeftEdge',0,$p->distance('undersleeveLeftEdge', 'underarmCenter')/1.2));

        // Angle of the undersleeveTip
        $angle = $p->angle('.helper1', 'backPitchPoint') - $p->angle('backPitchPoint','.helper2');
        $this->msg("Angle is $angle");

        $p->addPoint('undersleeveTipCpBottom', $p->rotate('undersleeveRightEdgeCpTop','undersleeveTip',-$angle));
        $p->addPoint('undersleeveElbowLeftCpTop', $p->shiftTowards('undersleeveWristLeft', 'undersleeveElbowLeft', $p->distance('undersleeveWristLeft', 'undersleeveElbowLeft')*1.2));

        // Paths
        //$p->newPath('topsleeve', 'M topsleeveWristRight L elbowRight C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint C backPitchPoint sleeveTopCpRight sleeveTop C sleeveTopCpLeft frontPitchPointCpTop frontPitchPoint C frontPitchPointCpBottom topsleeveLeftEdgeCpRight topsleeveLeftEdge C topsleeveLeftEdge topsleeveElbowLeftCpTop topsleeveElbowLeft L topsleeveWristLeft z');

        //$p->newPath('undersleeve', 'M undersleeveWristRight elbowRight C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip C undersleeveTipCpBottom undersleeveLeftEdgeCpRight undersleeveLeftEdgeRight L undersleeveLeftEdge C undersleeveLeftEdge undersleeveElbowLeftCpTop undersleeveElbowLeft L undersleeveWristLeft z');
        
        
        
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
        $p->newPath('topsleeve', 'M topsleeveWristRight L elbowRight C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint C backPitchPoint sleeveTopCpRight sleeveTop C sleeveTopCpLeft frontPitchPointCpTop frontPitchPoint C frontPitchPointCpBottom topsleeveLeftEdgeCpRight topsleeveLeftEdge C topsleeveLeftEdge topsleeveElbowLeftCpTop topsleeveElbowLeft L topsleeveWristLeft z');
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
        $p->newPath('undersleeve', 'M undersleeveWristRight elbowRight C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip C undersleeveTipCpBottom undersleeveLeftEdgeCpRight undersleeveLeftEdgeRight L undersleeveLeftEdge C undersleeveLeftEdge undersleeveElbowLeftCpTop undersleeveElbowLeft L undersleeveWristLeft z');
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
