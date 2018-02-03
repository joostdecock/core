<?php
/** Freesewing\Patterns\Beta\BlakeBlazer class */
namespace Freesewing\Patterns\Beta;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/**
 * A single-breasted jacket pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BlakeBlazer extends \Freesewing\Patterns\Beta\BentBodyBlock
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /** Lenght bonus below hips as factor as fraction of neck to hip = 13.33% */
    const LENGTHEN_FACTOR = 0.1333;

    /** Front extention as factor of chest circumference = 2% */
    const FRONT_EXTENSION = 0.02;

    /** Sleeve vent */
    const SLEEVE_VENT_LENGTH = 100;
    const SLEEVE_VENT_WIDTH = 40;

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
        parent::initialize($model);

        // Length bonus
        $this->setValueIfUnset('lengthBase', $model->m('centerBackNeckToWaist') + $model->m('naturalWaistToHip'));
        $this->setValueIfUnset('lengthBonus', $this->v('lengthBase') * self::LENGTHEN_FACTOR + $this->o('lengthBonus'));
        // Overwrite lengthBonus option with new value
        $this->setOptionIfUnset('lengthBonus', $this->v('lengthBonus'));

        // Front extension
        $this->setValueIfUnset('frontExtension', self::FRONT_EXTENSION);

        // Sleeve vent
        $this->setValueIfUnset('sleeveVentLength', self::SLEEVE_VENT_LENGTH);
        $this->setValueIfUnset('sleeveVentWidth', self::SLEEVE_VENT_WIDTH);
        
        // Make sure collar height makes sense
        if($this->o('collarHeight')*2 < $this->o('rollLineCollarHeight')) $this->setValue('collarHeight', $this->o('rollLineCollarHeight')/2);
        else $this->setValue('collarHeight', $this->o('collarHeight'));

        // Prevent chest shaping from being 0, because that will get read as 360 degrees
        if($this->o('chestShaping') == 0) $this->setOptionIfUnset('chestShaping', 0.0001);

        // We store all reduction in values to avoid option/value mix as side is not an option
        $this->setValueIfUnset('waistReductionRatioBack', $this->o('waistReductionRatioBack'));
        $this->setValueIfUnset('waistReductionRatioFront', $this->o('waistReductionRatioFront'));
        $sideReduction = 1 - ($this->o('waistReductionRatioFront') + $this->o('waistReductionRatioBack'));
        $this->setValueIfUnset('waistReductionRatioFrontSide', $sideReduction/2);
        $this->setValueIfUnset('waistReductionRatioBackSide', $sideReduction/2);

        $this->setValueIfUnset('hipsReductionRatioBack', $this->o('hipsReductionRatioBack'));
        $sideReduction = 1 - $this->o('hipsReductionRatioBack');
        $this->setValueIfUnset('hipsReductionRatioFrontSide', $sideReduction/2);
        $this->setValueIfUnset('hipsReductionRatioBackSide', $sideReduction/2);
        
        // Helper values
        $chest = $model->m('chestCircumference') + $this->o('chestEase');
        $waist = $model->m('naturalWaist') + $this->o('waistEase');
        $hips = $model->m('hipsCircumference') + $this->o('hipsEase');
        $this->setValueIfUnset('quarterChest', $chest/4);
        $this->setValueIfUnset('quarterWaist', $waist/4);
        $this->setValueIfUnset('quarterHips', $hips/4);

        // Actual reduction values
        $this->setValueIfUnset('waistReduction', ($chest - $waist));
        $this->setValueIfUnset('waistReductionBack',      $this->v('waistReduction') * $this->v('waistReductionRatioBack'));
        $this->setValueIfUnset('waistReductionFront',     $this->v('waistReduction') * $this->v('waistReductionRatioFront'));
        $this->setValueIfUnset('waistReductionFrontSide', $this->v('waistReduction') * $this->v('waistReductionRatioFrontSide'));
        $this->setValueIfUnset('waistReductionBackSide',  $this->v('waistReduction') * $this->v('waistReductionRatioBackSide'));
        $this->setValueIfUnset('hipsReduction', ($chest - $hips));
        $this->setValueIfUnset('hipsReductionBack',      $this->v('hipsReduction') * $this->v('hipsReductionRatioBack'));
        $this->setValueIfUnset('hipsReductionFrontSide', $this->v('hipsReduction') * $this->v('hipsReductionRatioFrontSide'));
        $this->setValueIfUnset('hipsReductionBackSide',  $this->v('hipsReduction') * $this->v('hipsReductionRatioBackSide'));

        // And now these values divided to make life simpler
        $this->setValueIfUnset('redBackWaist', $this->v('waistReductionBack')/2); // 50% coz cut twice. This is the full shaping on 1 back.
        $this->setValueIfUnset('redFrontWaist', $this->v('waistReductionFront')/2); // 50% coz cut twice. This is the full dart width on 1 front.
        $this->setValueIfUnset('redFrontSideWaist', $this->v('waistReductionFrontSide')/4); // 25% coz cut twice and divided between front and side. This is the full shaping on 1 [front/side].
        $this->setValueIfUnset('redBackSideWaist', $this->v('waistReductionBackSide')/4); // 25% coz cut twice and divided between back and side. This is the full shaping on 1 [back/side].
        $this->setValueIfUnset('redBackHips', $this->v('hipsReductionBack')/2); // 50% coz cut twice. This is the full shaping on 1 back.
        $this->setValueIfUnset('redFrontSideHips', $this->v('hipsReductionFrontSide')/4); // 25% coz cut twice and divided between front and side. This is the full shaping on 1 [front/side].
        $this->setValueIfUnset('redBackSideHips', $this->v('hipsReductionBackSide')/4); // 25% coz cut twice and divided between back and side. This is the full shaping on 1 [back/side].

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
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->sample($model);

        // Finalize pattern parts
        $this->finalizeBack($model);
        $this->finalizeFront($model);
        $this->finalizeSide($model);
        $this->finalizeTopsleeve($model);
        $this->finalizeUndersleeve($model);
        $this->finalizeUndercollar($model);
        $this->finalizeCollar($model);
        $this->finalizeCollarstand($model);

        // Should we draw zebra stripes?
        if ($this->o('zebra') == 1) {
            $this->zebraBack($model);
            $this->zebraSide($model);
            $this->zebraFront($model);
            $this->zebraTopsleeve($model);
            $this->zebraUndersleeve($model);
        }

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to all parts
            $this->paperlessBack($model);
            $this->paperlessFront($model);
            $this->paperlessSide($model);
            $this->paperlessTopsleeve($model);
            $this->paperlessUndersleeve($model);
            $this->paperlessUndercollar($model);
            $this->paperlessCollar($model);
            $this->paperlessCollarstand($model);
        }
    }
    
    protected function armholeLen()
      {
         /** @var \Freesewing\Part $back */
          $back = $this->parts['back'];
         /** @var \Freesewing\Part $front */
          $front = $this->parts['front'];
         /** @var \Freesewing\Part $side */
          $side = $this->parts['side'];

          return (  
              $back->curveLen(12, 19, 17, 10) + $back->curveLen(10, 18, 15, 14) +
              $side->curveLen('side14', 'side16', 'side13',5) + $side->curveLen(5,'5CpLeft','slArmCpRight','slArm') +
              $front->curveLen(12, 19, 17, 10) + $front->curveLen(10, 18, 15, 14) + $front->curveLen(14, '14CpRight', 'slArmCpLeft', 'slArm')
          );
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

        // Draft front and back blocks
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        // Draft front and back parts
        $this->draftFront($model);
        $this->draftSide($model);
        $this->draftBack($model);
        $this->draftCollar($model);
        $this->draftCollarstand($model);
        $this->draftUndercollar($model);
        
        // Draft sleeve 
        // Tweak the sleeve until it fits the armhole
        do {
            $this->draftSleeveBlock($model);
        } while (abs($this->armholeDelta()) > 1 && $this->v('sleeveTweakRun') < 50);
        $this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta(),1).'mm off.');
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
        $this->draftTopsleeve($model);
        $this->draftUndersleeve($model);
        

        // Hide blocks
        $this->parts['backBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['sleeveBlock']->setRender(false);
        $this->parts['topsleeveBlock']->setRender(false);
        $this->parts['undersleeveBlock']->setRender(false);
    }

    /**
     * Drafts the back block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBackBlock($model)
    {
        parent::draftBackBlock($model);

        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        // Widest part is a bit above chest line, so let's add a point there
        $p->newPoint('chestCenter', 0, $p->y(18));

        // Center back neck reduction
        $p->newPoint('centerBackNeck', $model->m('chestCircumference') * $this->o('neckReduction')/2, $p->y(1));
        $p->addPoint('chestCenterCpTop', $p->shift('chestCenter',90, $p->deltaY('centerBackNeck', 'chestCenter')/2));

        // Draw style line (sl) seperating the side panel
        $p->clonePoint(14, 'slArm');
        $p->addPoint('slArmCpBottom', $p->rotate(15,14,90));
        $p->addPoint('slChestCpTop', $p->shift('slArmCpBottom',-90, 30));
        $p->addPoint('slChest', $p->shift('slArmCpBottom',-90, 60));
        $p->newPoint('slWaist', $p->x('slChest'), $p->y(3));
        $p->newPoint('slHips', $p->x('slChest'), $p->y(3)+$model->m('naturalWaistToHip'));
        $p->newPoint('slHem', $p->x('slChest'), $p->y(4));

        // Shaping at back seam
        $p->addPoint('waistCenter', $p->shift(3, 0, $this->v('redBackWaist')));
        $p->newPoint('hipsCenter', $this->v('redBackHips'), $p->y(3) + $model->m('naturalWaistToHip'));
        $p->newPoint('hemCenter', $p->x('hipsCenter'), $p->y(4));
        $p->addPoint('chestCenterCpBottom', $p->shift('chestCenter', -90, $p->deltaY('chestCenter',3)/3));
        $p->addPoint('waistCenterCpTop', $p->shift('waistCenter', 90, $p->deltaY('chestCenter',3)/3));
        $p->addPoint('waistCenterCpBottom', $p->shift('waistCenter', -90, $p->deltaY(3,'hipsCenter')/3));
        $p->addPoint('hipsCenterCpTop', $p->shift('hipsCenter', 90, $p->deltaY(3,'hipsCenter')/3));

        // Shaping at back/side seam
        $p->addPoint('waistBackSide', $p->shift('slWaist', 180, $this->v('redBackSideWaist')));
        $p->addPoint('waistSideBack', $p->shift('slWaist', 0, $this->v('redBackSideWaist')));
        $p->addPoint('hipsBackSide', $p->shift('slHips',  180, $this->v('redBackSideHips')));
        $p->addPoint('hipsSideBack', $p->shift('slHips',  0, $this->v('redBackSideHips')));
        $p->newPoint('hemBackSide', $p->x('hipsBackSide'), $p->y(4));
        $p->newPoint('hemSideBack', $p->x('hipsSideBack'), $p->y(4));
        $p->newPoint('hipsBackSideCpTop', $p->x('hipsBackSide'), $p->y('hipsCenterCpTop'));
        $p->newPoint('hipsSideBackCpTop', $p->x('hipsSideBack'), $p->y('hipsCenterCpTop'));
        $p->newPoint('waistBackSideCpBottom', $p->x('waistBackSide'), $p->y('waistCenterCpBottom'));
        $p->newPoint('waistSideBackCpBottom', $p->x('waistSideBack'), $p->y('waistCenterCpBottom'));
        $p->addPoint('waistBackSideCpTop', $p->shift('waistBackSide', 90, $p->deltaY('slArm','waistSideBack')/3));
        $p->addPoint('waistSideBackCpTop', $p->shift('waistSideBack', 90, $p->deltaY('slArm','waistSideBack')/3));


        // Paths
        $path = 'M centerBackNeck C centerBackNeck chestCenterCpTop chestCenter L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 1 1 z';
        $p->newPath('seamline', $path, ['class' => 'hint']);
        $p->newPath('styleline', 'M slArm C slArmCpBottom slChestCpTop slChest L slHem', ['class' => 'hint']);
        $p->newPath('back','
            M centerBackNeck 
            C centerBackNeck 20 8
            L 12
            C 19 17 10
            C 18 15 14
            C slArmCpBottom waistBackSideCpTop waistBackSide
            C waistBackSideCpBottom hipsBackSideCpTop hipsBackSide
            L hemBackSide
            L hemCenter
            L hipsCenter 
            C hipsCenterCpTop waistCenterCpBottom waistCenter 
            C waistCenterCpTop chestCenterCpBottom chestCenter
            C chestCenterCpTop centerBackNeck centerBackNeck
            z
            ', ['class' => 'fabric']);
        $p->newPath('side','
            M hemSideBack
            L hipsSideBack
            C hipsSideBackCpTop waistSideBackCpBottom waistSideBack
            C waistSideBackCpTop slArmCpBottom slArm
            C 16 13 5
            L 6
            L hemSideBack 
            z
            ', ['class' => 'help']);

        // Mark path for sample service
        $p->paths['back']->setSample(true);

        // Store length of the collar
        $this->setValue('backCollarLength', $p->curveLen(8,20,'centerBackNeck','centerBackNeck'));
    }

    /**
     * Drafts the front block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontBlock($model)
    {
        // Note: The parent method called below will start by cloning all point from the backBlock
        parent::draftFrontBlock($model);
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontBlock'];
        
        // Draw style line (sl) seperating the side panel
        $p->curveCrossesX(5,13,16,14,$p->x(13), '.tmp');
        $p->clonePoint('.tmp1','slArm');
        // Need control points for this splitted curve
        $p->splitCurve(5,13,16,14,'slArm','.tmp');
        $p->clonePoint('.tmp7', 'slArmCpLeft');
        $p->clonePoint('.tmp3', 'slArmCpRight');
        $p->clonePoint('.tmp2', '5CpLeft');
        $p->clonePoint('.tmp6', '14CpRight');

        $p->newPoint('slWaist', $p->x(5) - $model->m('chestCircumference') * $this->o('sideFrontPlacement'), $p->y(3));
        $p->addPoint('slWaistCpTop', $p->shift('slWaist', 90, $p->deltaY(5,3)/2));
        $p->newPoint('slHips', $p->x('slWaist'), $p->y(3)+$model->m('naturalWaistToHip'));
        $p->newPoint('slHem', $p->x('slWaist'), $p->y(4));

        // Shift sideseam shaping to adapt for different in location of style line
        $shiftThese = [
            'hemBackSide', 
            'hemSideBack', 
            'hipsBackSide', 
            'hipsSideBack',
            'hipsBackSideCpTop', 
            'hipsSideBackCpTop',
            'waistBackSideCpBottom', 
            'waistSideBackCpBottom',
            'waistBackSide', 
            'waistSideBack',
            'waistBackSideCpTop', 
            'waistSideBackCpTop',
        ];
        $shiftDistance = $p->deltaX('slChest', 'slWaist');
        foreach($shiftThese as $pid) $p->addPoint($pid, $p->shift($pid, 0, $shiftDistance));
    
        // Bring over side panel points from back block
        $b =  $this->parts['backBlock'];
        $transferThese = [13,16,14,'slArmCpBottom','waistSideBackCpTop','waistSideBack','waistSideBackCpBottom','hipsSideBackCpTop','hipsSideBack','hemSideBack'];
        foreach($transferThese as $pid) $p->newPoint('side'.ucfirst($pid), $b->x(5) + $b->deltaX($pid,5), $b->y(5) + $b->deltaY(5,$pid));

        // Front dart
        $p->addPoint('frontDartMid', $p->shift(3,0,$model->m('chestCircumference') * $this->o('frontDartPlacement')));
        $p->addPoint('frontDartTop', $p->shift('frontDartMid', 90, $p->deltaY(5,3)/1.5));
        $p->addPoint('frontDartBottom', $p->shift('frontDartMid', -90, $p->deltaY(3,'hipsCenter')/2));
        $p->addPoint('frontDartRight', $p->shift('frontDartMid', 0, $this->v('redFrontWaist')/2));
        $p->addPoint('frontDartLeft', $p->shift('frontDartMid', 180, $this->v('redFrontWaist')/2));
        $p->addPoint('frontDartRightCpTop', $p->shift('frontDartRight', 90, $p->deltaY('frontDartTop','frontDartMid')/3));
        $p->addPoint('frontDartLeftCpTop', $p->shift('frontDartLeft', 90, $p->deltaY('frontDartTop','frontDartMid')/3));
        $p->addPoint('frontDartRightCpBottom', $p->shift('frontDartRight', -90, $p->deltaY('frontDartMid','frontDartBottom')/3));
        $p->addPoint('frontDartLeftCpBottom', $p->shift('frontDartLeft', -90, $p->deltaY('frontDartMid','frontDartBottom')/3));

        // Drop hem center front
        $p->newPoint('cfHem', $p->x(4), $p->y(4) + ($model->m('centerBackNeckToWaist') + $model->m('naturalWaistToHip')) * $this->o('centerFrontHemDrop'));
        $p->addPoint('frontSideHem', $p->beamsCross('hipsBackSide','hemBackSide','cfHem','sideHemSideBack'));
        $p->newPoint('sideFrontHem', $p->x('hemSideBack'), $p->y('frontSideHem'));



        $path = 'M 9 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 9 z';
        $p->newPath('seamline', $path, ['class' => 'hint']);
        $p->newPath('styleline', 'M slArm C slArm slWaistCpTop slWaist L slHem', ['class' => 'hint']);
        $p->newPath('front','
            M 9 
            L cfHem
            L frontSideHem
            L hipsBackSide
            C hipsBackSideCpTop waistBackSideCpBottom waistBackSide
            C waistBackSideCpTop slArm slArm
            C slArmCpLeft 14CpRight 14
            C 15 18 10
            C 17 19 12
            L 8
            C 20 21 9
            z
            M frontDartBottom 
            C frontDartBottom frontDartRightCpBottom frontDartRight
            C frontDartRightCpTop frontDartTop frontDartTop
            C frontDartTop frontDartLeftCpTop frontDartLeft
            C frontDartLeftCpBottom frontDartBottom frontDartBottom
            z
            ', ['class' => 'fabric']);
        $p->newPath('side','
            M sideFrontHem
            L hipsSideBack
            C hipsSideBackCpTop waistSideBackCpBottom waistSideBack
            C waistSideBackCpTop slArm slArm
            C slArmCpRight 5CpLeft 5
            C side13 side16 side14
            C sideSlArmCpBottom sideWaistSideBackCpTop sideWaistSideBack
            C sideWaistSideBackCpBottom sideHipsSideBackCpTop sideHipsSideBack
            L sideHemSideBack
            L sideFrontHem
            z
            ', ['class' => 'fabric']);
        
        // Mark paths for sample service
        $p->paths['front']->setSample(true);
        $p->paths['side']->setSample(true);
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
        $this->clonePoints('frontBlock', 'front');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Front extension (fe)
        $p->newPoint('feTop', -1 * ($this->v('frontExtension') * $model->m('chestCircumference')), $p->y(9));
        $p->newPoint('feBottom', $p->x('feTop'), $p->y('cfHem'));

        // Chest pocket (cp)
        $width = $this->o('chestPocketWidth');
        $p->newPoint('cpBottomLeft', $model->m('chestCircumference')/4 * $this->o('chestPocketPlacement') - ($width/2), $p->y(5));
        $p->addPoint('cpTopLeft', $p->shift('cpBottomLeft', 90, $this->o('chestPocketWeltSize')));
        $p->addPoint('cpBottomRight', $p->shift('cpBottomLeft', 0, $width));
        $p->addPoint('cpTopRight', $p->shift('cpTopLeft', 0, $width));
        if($this->o('chestPocketAngle') > 0) {
            $p->addPoint('cpBottomRight', $p->rotate('cpBottomRight', 'cpBottomLeft', $this->o('chestPocketAngle')));
            $p->addPoint('cpTopRight', $p->rotate('cpTopRight', 'cpBottomLeft', $this->o('chestPocketAngle')));
            $p->addPoint('cpTopLeft', $p->rotate('cpTopLeft', 'cpBottomLeft', $this->o('chestPocketAngle')));
        }

        // Front pocket (fp)
        $width = $model->m('chestCircumference') * $this->o('frontPocketWidth');
        $p->newPoint('fpTopLeft', $model->m('chestCircumference') * $this->o('frontPocketPlacement') - ($width/2), $p->y('frontDartMid') + ($model->m('naturalWaistToHip') * $this->o('frontPocketHeight')));
        $p->addPoint('fpBottomLeft', $p->shift('fpTopLeft', -90, ($p->deltaY('frontDartMid', 'slHem') * $this->o('frontPocketDepth'))));
        $p->addPoint('fpTopRight', $p->shift('fpTopLeft', 0, $width));
        $p->addPoint('fpBottomRight', $p->shift('fpBottomLeft', 0, $width));
        // Adapt width according to dart
        if($p->y('fpTopLeft') < $p->y('frontDartBottom')) {
            $p->curveCrossesY('frontDartLeft', 'frontDartLeftCpBottom', 'frontDartBottom', 'frontDartBottom', $p->y('fpTopLeft'), 'dartPocketLeft');
            $p->curveCrossesY('frontDartRight', 'frontDartRightCpBottom', 'frontDartBottom', 'frontDartBottom', $p->y('fpTopLeft'), 'dartPocketRight');
            $delta = $p->distance('dartPocketLeft1', 'dartPocketRight1');
            $p->addPoint('fpTopRight', $p->shift('fpTopRight', 0, $delta));
            $p->newPath('frontPocket', 'M dartPocketRight1 L fpTopRight M fpBottomRight L fpBottomLeft L fpTopLeft L dartPocketLeft1', ['class' => 'help']);

        } else {
            $p->newPath('frontPocket', 'M fpTopLeft L fpTopRight M fpBottomRight L fpBottomLeft L fpTopLeft', ['class' => 'help']);
        }



        /*
         * Slash & spread chest.  This is one of those things that's simpler on paper
         *
         * We could slash this part into a limited number of strips here (say 5 or so)
         * and then rotate them all. However, that would require us to split the curves
         * into 5 parts, which is particularly challenging for the armhole that itself is
         * made up of 3 curves strung together.
         *
         * So, to simplify things, we won't be using curves, but only straight lines.
         * On the other hand, racking up the number of slashlines is relatively easy,
         * so we cut this into tiny little slices, so that the straight lines aren't that
         * big a deal
         */
        $steps = 100;
        $distance = $p->deltaY(12,5)/($steps+1);
        $left = $p->x('feTop');
        $right = $p->x('slArm');
        $bottom = $p->y(5);
        for($i=1; $i<=$steps; $i++) {

            // Slash line coordinates
            $lpid = "leftStep$i";
            $rpid = "rightStep$i";
            $p->newPoint($lpid, $left, $bottom - $i*$distance);
            $p->newPoint($rpid, $right, $bottom - $i*$distance);

            // Find left intersection points
            if($p->y($lpid) < $p->y('feTop')) { // In neck curve
                $p->curveCrossesY(8,20,21,9,$p->y($lpid),'.isect');
                // Overwrite point
                $p->clonePoint('.isect1', $lpid);
            } else { 
                // Store id of last point to fall on center front
                $this->setValue('cfTipPoint', $lpid);
                $this->setValue('cfTipPointNext', 'leftStep'.($i+1));
            }
            
            // Find right intersection points
            if($p->y($rpid) > $p->y(14)) { // In first curve of the armhole
                $p->curveCrossesY(14, '14CpRight', 'slArmCpLeft','slArm',$p->y($rpid),'.isect');
                $p->clonePoint('.isect1', $rpid); // Overwrite point
            }
            else if($p->y($rpid) > $p->y(10)) { // In second curve of the armhole
                $p->curveCrossesY(10,18,15,14,$p->y($rpid),'.isect');
                $p->clonePoint('.isect1', $rpid); // Overwrite point
            } else { // In third and final curve of the armhole
                $p->curveCrossesY(12,19,17,10,$p->y($rpid),'.isect');
                $p->clonePoint('.isect1', $rpid); // Overwrite point
            }
        }

        // Add start and end line
        $p->clonePoint('slArm', 'rightStep0');
        $p->newPoint('leftStep0', $p->x('feTop'), $p->y('rightStep0'));
        $p->clonePoint(12, 'rightStep'.($steps+1));
        $p->clonePoint(8, 'leftStep'.($steps+1));

        // Figure out how much we need to rotate
        $distance = $model->m('chestCircumference') * $this->o('chestShaping');
        $p->newPoint('.helper', $p->x('feTop'), $p->y(10) - $distance);
        $angle = -1*(360-$p->angle(10, '.helper'))/$steps;
        
        // Rotate points in second loop, because we need them all before we can do this
        $steps++;
        $pathDown = [];
        for($i=1; $i<=$steps; $i++) {
            for($j=$i; $j<=$steps; $j++) {
                $rotateThese[] = "leftStep$j";
                $rotateThese[] = "rightStep$j";
            }
            foreach($rotateThese as $pid) $p->addPoint($pid, $p->rotate($pid, "rightStep$i", $angle));
            unset($rotateThese);
            $pathDown[] = "L rightStep$i ";
        }
        // Clone endpoints to avoid breaking things when the nr of steps change
        $p->clonePoint('rightStep'.$steps, 'shoulderLineRight');
        $p->clonePoint('leftStep'.$steps, 'shoulderLineLeft');

        // Now reconstruct the armhole in a proper curve
        $p->clonePoint('shoulderLineRight', 12);
        $p->addPoint(19, $p->shift(12, $p->angle('shoulderLineLeft','shoulderLineRight')+90, 15));
        $p->addPoint(10, $p->shift(10, 0, 3));
        $p->addPoint(17, $p->shift(17, 0, 3));
        $p->addPoint(18, $p->shift(18, 0, 3));
        $p->newPath('test', 'M 12 C 19 17 10 C 18 15 14', ['class' => 'debug']);




        // Lapel break point and roll line
        $p->newPoint('breakPoint', $p->x('feBottom'), $p->y(3) - ($p->distance(2,3) * $this->o('lapelStart')));
        $p->addPoint('cutawayPoint', $p->shift('breakPoint',-90,$p->distance(2,3) * $this->o('lapelStart') + 10/$this->o('lapelStart')));
        $p->newPath('sdfsdss', 'M breakPoint L cutawayPoint', ['class' => 'debug']);
        $p->addPoint('shoulderRoll', $p->shiftOutwards('shoulderLineRight','shoulderLineLeft', $this->o('rollLineCollarHeight')));
        $p->addPoint('shoulderRollCb', $p->shiftOutwards('breakPoint','shoulderRoll', $this->v('backCollarLength')));
        $p->addPoint('collarCbHelp', $p->shift('shoulderRollCb', $p->angle('shoulderRoll','shoulderRollCb')+90, $this->o('rollLineCollarHeight')));
        $p->addPoint('collarCbBottom', $p->shift('collarCbHelp', $p->angle('shoulderRoll','collarCbHelp')+90, $this->o('rollLineCollarHeight')));
        $p->addPoint('collarCbTop',    $p->shift('collarCbHelp', $p->angle('shoulderRoll','collarCbHelp')-90,  $this->v('collarHeight')*2 - $this->o('rollLineCollarHeight')));
        
        // Notch (prevent it from getting too deep)
        $maxNotch = $p->distance($this->v('cfTipPoint'), $this->v('cfTipPointNext'));
        if($this->o('collarNotchDepth') > $maxNotch) $this->setValue('collarNotchDepth', $maxNotch);
        else $this->setValue('collarNotchDepth', $this->o('collarNotchDepth'));
        $p->addPoint('notchPoint', $p->shiftTowards($this->v('cfTipPoint'), $this->v('cfTipPointNext'), $this->v('collarNotchDepth')));
        $p->addPoint('notchTip', $p->rotate($this->v('cfTipPoint'), 'notchPoint', -1 * $this->o('collarNotchAngle')));
        $p->addPoint('notchTip', $p->shiftTowards('notchPoint', 'notchTip', $this->v('collarNotchDepth') * $this->o('collarNotchReturn')));
        $p->addPoint('notchTipCp', $p->shift('notchTip', $p->angle('notchPoint','notchTip')+90, $p->distance('notchTip', 'collarCbTop')/4));
        $p->addPoint('collarCbTopCp', $p->shift('collarCbTop', $p->angle('collarCbBottom','collarCbTop')-90, $p->distance('notchTip', 'collarCbTop')/4));

        // Redraw front neck line
        $p->clonePoint($this->v('cfTipPoint'), 'cfRealTop');
        $p->clonePoint('leftStep1', 'breakPointCp');
        $p->addPoint('.cpHelper1', $p->rotate('collarCbHelp','collarCbBottom',90));
        $p->addPoint('.cpHelper2', $p->beamsCross('cfRealTop', 'notchPoint', 'collarCbBottom', '.cpHelper1'));
        $p->addPoint('notchPointCp', $p->shiftFractionTowards('notchPoint', '.cpHelper2', 0.75));
        $p->addPoint('shoulderLineRealLeft', $p->beamsCross('shoulderLineRight', 'shoulderLineLeft', 'collarCbBottom', '.cpHelper1'));
        $p->addPoint('shoulderLineRealLeftCp', $p->shiftFractionTowards('shoulderLineRealLeft', '.cpHelper2', 0.75));

        // Now adapt to fit the back neck curve length
        if($p->distance('shoulderLineRealLeft', 'collarCbBottom') != $this->v('backCollarLength')) {
            $delta = $p->distance('shoulderLineRealLeft', 'collarCbBottom') - $this->v('backCollarLength');
            $angle = $p->angle('shoulderLineRealLeft', 'collarCbBottom');
            $shiftThese = ['collarCbBottom','collarCbTop', 'collarCbTopCp'];
            foreach($shiftThese as $pid) $p->addPoint($pid, $p->shift($pid, $angle, $delta));
        }
    
        // Seperation between front and collar
        $p->addPoint('shiftedNotchPoint', $p->shift('notchPoint',0,10));
        $p->addPoint('shiftedNotchPoint', $p->rotate('shiftedNotchPoint','notchPoint',30)); 
        $p->addPoint('foldNotchHeight', $p->beamsCross('breakPoint', 'shoulderRoll', 'notchPoint', 'shiftedNotchPoint'));
        $p->addPoint('collarCorner', $p->shift('foldNotchHeight', 0, $p->distance('shoulderRoll', 'shoulderLineRealLeft')/2));
        $p->addPoint('rollLineTop', $p->beamsCross('breakPoint', 'shoulderRoll', 'notchPoint', 'collarCorner'));


        // Store pocket info for side panel
        $p->curveCrossesY('waistBackSide','waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide', $p->y('fpTopRight'), 'fpHelpTop');
        $this->setValue('fpTopWidth', $p->distance('fpHelpTop1', 'fpTopRight'));
        $this->setValue('fpBottomWidth', $p->deltaX('hipsBackSide', 'fpBottomRight'));
        $this->setValue('fpHeight', $p->deltaY('fpTopRight', 'fpBottomRight'));
        $this->setValue('fpStartY', $p->y('fpTopRight'));
        // Move top and bottom right to edge
        $p->clonePoint('fpHelpTop1', 'fpTopRight');
        $p->newPoint('fpBottomRight', $p->x('hipsBackSide'), $p->y('fpBottomLeft'));

        // Add extra hem allowance (3cm)
        $p->addPoint('frontSideHemEdge', $p->shift('frontSideHem', -90, $this->o('sa')*3));
        $p->addPoint('cfHemEdge', $p->shift('cfHem', -90, $this->o('sa')*3));
        $p->addPoint('feBottomHemEdge', $p->shift('feBottom', -90, $this->o('sa')*3));

        // Round the front at hem
        $p->addPoint('roundTop', $p->shiftFractionTowards('cutawayPoint','feBottomHemEdge', $this->o('frontCutawayStart')));
        $p->addPoint('roundTop', $p->rotate('roundTop', 'cutawayPoint', $this->o('frontCutawayAngle')));
        $p->addPoint('roundTopCp', $p->shiftFractionTowards('feTop','feBottom', 0.95));
        $p->newPoint('roundTopCp', $p->x('roundTop'), $p->y('roundTopCp'));
        $p->addPoint('roundTopCp', $p->rotate('roundTopCp', 'roundTop', $this->o('frontCutawayAngle')));
        $p->addPoint('roundRight', $p->shiftFractionTowards('cfHem','frontSideHem', 0.3));
        $p->addPoint('roundRightCp', $p->shiftFractionTowards('cfHem','frontSideHem', 0.05));

        // Smooth out curve
        $p->addPoint('roundTop',$p->shift('roundTop',180,$p->deltaX('cutawayPoint','roundTop')/2));
        $p->addPoint('roundTopCpTop', $p->beamsCross('breakPoint','cutawayPoint','roundTop','roundTopCp'));
        $p->addPoint('cutawayPointCp', $p->shiftFractionTowards('cutawayPoint','roundTopCpTop',0.5));

        // Facing/lining boundary
        $p->addPoint('facingTop', $p->shiftFractionTowards('shoulderLineRealLeft','shoulderLineRight',0.2));    

        // Paths
        $p->newPath('front', '
            M breakPoint 
            C breakPointCp cfRealTop cfRealTop 
            L notchPoint 
            L collarCorner
            L shoulderLineRealLeft 
            L shoulderLineRight
            C 19 17 10
            C 18 15 14
            C 14CpRight slArmCpLeft slArm
            C slArm waistBackSideCpTop waistBackSide
            C waistBackSideCpBottom hipsBackSideCpTop hipsBackSide
            L frontSideHem
            L roundRight
            C roundRightCp roundTopCp roundTop
            C roundTopCpTop cutawayPointCp cutawayPoint
            L breakPoint
            z
            M frontDartBottom 
            C frontDartBottom frontDartRightCpBottom frontDartRight
            C frontDartRightCpTop frontDartTop frontDartTop
            C frontDartTop frontDartLeftCpTop frontDartLeft
            C frontDartLeftCpBottom frontDartBottom frontDartBottom
            z
             
            ', ['class' => 'fabric']);

        $p->newPath('cf', 'M 9 L cfHem', ['class' => 'help']);
        $p->newPath('chestPocket', ' M cpBottomLeft L cpTopLeft L cpTopRight L cpBottomRight L cpBottomLeft z', ['class' => 'help']);
        $p->newPath('rolline', 'M breakPoint L rollLineTop', ['class' => 'help']);
        $p->newPath('lining', 'M facingTop L roundRight', ['class' => 'lining']);
        $p->newPath('facing', 'M facingTop L roundRight', ['class' => 'fabric', 'stroke-dasharray' => '10,10']);

        // 3cm extra hem allowance
        $p->addPoint('roundedHem', $p->shift('roundRight',-90, $this->o('sa')*3));

        // Mark path for sample service
        $p->paths['front']->setSample(true);
        $p->paths['chestPocket']->setSample(true);
        $p->paths['frontPocket']->setSample(true);

        // Store lenght of the sleeve cap to the pitch point notch
        $this->setValue('frontSleevecapToNotch', $p->curveLen('shoulderLineRight', 19, 17, 10)); 
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
        $this->clonePoints('frontBlock', 'side');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];
        
        /*
        $this->setValue('fpTopWidth', $p->distance('fpHelpTop1', 'fpTopRight'));
        $this->setValue('fpBottomWidth', $p->deltaX('hipsBackSide', 'fpBottomRight'));
        $this->setValue('fpHeight', $p->deltaY('fpTopRight', 'fpBottomRight'));
        $this->setValue('fpStartY', $p->y('fpTopRight'));
        */

        // Front pocket
        $p->curveCrossesY('waistSideBack','waistSideBackCpBottom','hipsSideBackCpTop','hipsSideBack',$this->v('fpStartY'), 'fpHelp');
        $p->clonePoint('fpHelp1','fpTopLeft');
        $p->addPoint('fpTopRight', $p->shift('fpTopLeft', 0, $this->v('fpTopWidth')));
        $p->newPoint('fpBottomLeft', $p->x('sideFrontHem'), $p->y('fpTopLeft') + $this->v('fpHeight'));
        $p->addPoint('fpBottomRight', $p->shift('fpBottomLeft', 0, $this->v('fpBottomWidth')));

        // Add extra hem allowance (3cm)
        $p->addPoint('hemEdgeBackSide', $p->shift('sideHemSideBack', -90, $this->o('sa')*3));
        $p->addPoint('hemEdgeFrontSide', $p->shift('sideFrontHem', -90, $this->o('sa')*3));
        

        $p->newPath('pocket', 'M fpTopLeft L fpTopRight L fpBottomRight L fpBottomLeft', ['class' => 'help']);

        $p->newPath('side','
            M sideFrontHem
            L hipsSideBack
            C hipsSideBackCpTop waistSideBackCpBottom waistSideBack
            C waistSideBackCpTop slArm slArm
            C slArmCpRight 5CpLeft 5
            C side13 side16 side14
            C sideSlArmCpBottom sideWaistSideBackCpTop sideWaistSideBack
            C sideWaistSideBackCpBottom sideHipsSideBackCpTop sideHipsSideBack
            L sideHemSideBack
            L sideFrontHem
            z
            ', ['class' => 'fabric']);

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
        $this->clonePoints('backBlock', 'back');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Back vent
        if($this->o('backVent') == 1) {
            // Vent tip
            $p->curveCrossesY('hipsCenter','hipsCenterCpTop','waistCenterCpBottom','waistCenter',$p->y('hipsCenter') - $p->deltaY('waistCenter','hipsCenter') * $this->o('backVentLength'), 'vent');
            $p->clonePoint('vent1', 'ventTip');
            // Vent facing
            $p->splitCurve('hipsCenter','hipsCenterCpTop','waistCenterCpBottom','waistCenter','ventTip','ventSplit');
            $p->addPoint('ventFacingBase', $p->shiftAlong('ventTip','ventSplit3','ventSplit2','hipsCenter', 15));
            $p->splitCurve('hipsCenter','hipsCenterCpTop','waistCenterCpBottom','waistCenter','ventFacingBase','ventFacingSplit');
            $p->offsetPathString('ventFacing', 'M hipsCenter C ventFacingSplit2 ventFacingSplit3 ventFacingBase', -40);
            $p->addPoint('ventFacingBottomLeft', $p->shift('hemCenter', 180, 40));

            $p->newPath('tmp', 'M hemCenter L hipsCenter C hipsCenterCpTop waistCenterCpBottom waistCenter', ['class' => 'hint']);
            
            $path = 'L ventFacingBottomLeft 
                    L ventFacing-startPoint 
                    C ventFacing-cp1--hipsCenter.ventFacingSplit2.ventFacingSplit3.ventFacingBase ventFacing-cp2--hipsCenter.ventFacingSplit2.ventFacingSplit3.ventFacingBase ventFacing-endPoint 
                    L ventTip
                    C ventSplit7 ventSplit6 waistCenter';
        } else {
            $path = 'L hipsCenter C hipsCenterCpTop waistCenterCpBottom waistCenter';
        }

        // Add extra hem allowance (3*SA)
        $p->addPoint('hemEdgeBackSide', $p->shift('hemBackSide', -90, $this->o('sa')*3));
        $p->addPoint('hemEdgeCenter', $p->shift('hemCenter', -90, $this->o('sa')*3));
        if($this->o('backVent') == 1) $p->addPoint('hemEdgeVent', $p->shift('ventFacingBottomLeft',-90,$this->o('sa')*3));

        $p->newPath('back','
            M centerBackNeck 
            C centerBackNeck 20 8
            L 12
            C 19 17 10
            C 18 15 14
            C slArmCpBottom waistBackSideCpTop waistBackSide
            C waistBackSideCpBottom hipsBackSideCpTop hipsBackSide
            L hemBackSide
            L hemCenter
            '.$path.'
            C waistCenterCpTop chestCenterCpBottom chestCenter
            C chestCenterCpTop centerBackNeck centerBackNeck
            z
            ', ['class' => 'fabric']);

        // Store lenght of the sleeve cap to the pitch point notch
        $this->setValue('backSleevecapToNotch', $p->curveLen(12, 19, 17, 10)); 
    }


    /**
     * Drafts the collar
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftCollar($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collar'];

        /** @var \Freesewing\Part $p */
        $front = $this->parts['front'];

        // Cloning all points from front seems like overkill
        $cloneThese = [
            'collarCbBottom',
            'collarCbTop',
            'collarCbTopCp',
            'collarCorner',
            'notchPoint',
            'notchTip',
            'notchTipCp',
            'shoulderLineRealLeft',
        ];
        foreach($cloneThese as $pid) $p->newPoint($pid, $front->x($pid), $front->y($pid));
        
        // Rotate entire part
        $angle = $front->angle('collarCbTop','collarCbBottom');
        foreach($cloneThese as $pid) $p->addPoint($pid, $p->rotate($pid, 'collarCbTop', $angle*-1+90));

        // Tweak bottom shape
        $p->addPoint('shoulderLineRealLeft', $p->shiftFractionTowards('collarCbBottom','shoulderLineRealLeft', 1.4));

        // Bend the collar
        $angle = 5;
        $p->addPoint('bottomLeft', $p->rotate('collarCorner', 'collarCbBottom', $angle));
        $p->addPoint('helper', $p->shiftAlong('bottomLeft','bottomLeft','shoulderLineRealLeft','collarCbBottom', 1));
        $delta = $this->bendedCollarDelta();
        $tweaks = 0;
        while(abs($delta) > 1 && $tweaks < 50) {
            $p->addPoint('bottomLeft', $p->shiftTowards('helper','bottomLeft', 1-$delta));
            $tweaks++;
            $delta = $this->bendedCollarDelta();
        }
        $this->msg("After $tweaks attemps, the collar length is ".round($delta).'mm off.');
        $rotateThese = ['notchPoint','notchTip','notchTipCp'];
        foreach($rotateThese as $pid) $p->addPoint($pid, $p->rotate($pid, 'collarCbBottom', $angle));

        // Tweak top shape
        $p->newPoint('collarCbTopCp', $p->x('shoulderLineRealLeft'), $p->y('collarCbTopCp'));
        $p->addPoint('collarCbTopCp', $p->shiftOutwards('collarCbTop', 'collarCbTopCp', $p->distance('collarCbTop', 'collarCbTopCp')/10));
        $p->addPoint('notchTipCp', $p->shiftFractionTowards('notchTip','collarCbTop', 0.25));

        // Undercollar line
        $p->addPoint('ucTop', $p->shift('collarCbBottom', 90, 20));
        $p->addPoint('ucTopCpLeft', $p->shift('shoulderLineRealLeft', 90, 20));
        $p->addPoint('ucTopCpRight', $p->flipX('ucTopCpLeft', $p->x('ucTop')));
        $p->addPoint('ucTipLeft', $p->shiftTowards('bottomLeft', 'notchPoint', 20));
        $p->addPoint('ucTipRight', $p->flipX('ucTipLeft', $p->x('ucTop')));

        // End undercollar before end of collar
        $p->addPoint('ucBottomLeft', $p->shiftAlong('bottomLeft','bottomLeft','shoulderLineRealLeft','collarCbBottom', $p->distance('bottomLeft','collarCbBottom')/5));
        // Split curve
        $p->splitCurve('bottomLeft','bottomLeft','shoulderLineRealLeft','collarCbBottom','ucBottomLeft','ucBottomCurve');

        // Mirror what we need on the other side
        $mirrorThese = [
            'collarCbTopCp',
            'notchTipCp',
            'notchTip',
            'notchPoint',
            'collarCorner',
            'shoulderLineRealLeft',
            'bottomLeft',
            'ucBottomLeft',
            'ucBottomCurve3',
            'ucBottomCurve6',
            'ucBottomCurve7',
        ];
        foreach($mirrorThese as $pid) $p->addPoint("m.$pid", $p->flipX($pid, $p->x('collarCbTop')));

        $p->newPath('outline', '
            M notchPoint 
            L bottomLeft 
            C bottomLeft ucBottomCurve3 ucBottomLeft
            C ucBottomLeft ucTopCpLeft ucTop
            C ucTopCpRight m.ucBottomLeft m.ucBottomLeft            
            C m.ucBottomCurve3 m.bottomLeft m.bottomLeft
            L m.notchPoint 
            L m.notchTip
            C m.notchTipCp m.collarCbTopCp collarCbTop
            C collarCbTopCp notchTipCp notchTip
            L notchPoint
            z
            ', ['class' => 'fabric']);
    }

    /** 
     * Checks the difference in length between the original straight collar and bended collar
     */
    protected function bendedCollarDelta() 
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collar'];

        $straightLen = $p->distance('collarCorner','shoulderLineRealLeft') + $p->distance('shoulderLineRealLeft','collarCbBottom');
        $bendedLen = $p->curveLen('collarCbBottom','shoulderLineRealLeft','bottomLeft','bottomLeft');

        return $bendedLen - $straightLen;
    }

    /**
     * Drafts the collar stand
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftCollarstand($model)
    {
        $this->clonePoints('collar', 'collarstand');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['collarstand'];

        $p->newPath('outline', '
            M ucBottomLeft 
            C ucBottomLeft ucTopCpLeft ucTop
            C ucTopCpRight m.ucBottomLeft m.ucBottomLeft
            C m.ucBottomCurve7 m.ucBottomCurve6 collarCbBottom
            C ucBottomCurve6 ucBottomCurve7 ucBottomLeft
            z
            ', ['class' => 'fabric']);
    }

    /**
     * Drafts the undercollar
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftUndercollar($model)
    {
        $this->clonePoints('collar', 'undercollar');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['undercollar'];

        $p->newPath('outline', '
            M notchPoint 
            L bottomLeft
            C bottomLeft shoulderLineRealLeft collarCbBottom
            C m.shoulderLineRealLeft m.bottomLeft m.bottomLeft
            L m.notchPoint 
            L m.notchTip
            C m.notchTipCp m.collarCbTopCp collarCbTop
            C collarCbTopCp notchTipCp notchTip
            L notchPoint
            z
            ', ['class' => 'various']);
        $p->newPath('undercollarLine', '
            M ucBottomLeft 
            C ucBottomLeft ucTopCpLeft ucTop
            C ucTopCpRight m.ucBottomLeft m.ucBottomLeft
            ', ['class' => 'hint']);

    }

    /**
     * Drafts the topsleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTopsleeve($model)
    {
        $this->clonePoints('topsleeveBlock', 'topsleeve');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeve'];

        // Vent 
        $p->addPoint('ventBottomRight', $p->shiftOutwards('topsleeveWristLeft','topsleeveWristRight',$this->v('sleeveVentWidth')));
        $p->addPoint('ventTopLeft', $p->shiftTowards('topsleeveWristRight','elbowRight', $this->v('sleeveVentLength')));
        $p->addPoint('ventTopRight', $p->shiftTowards('topsleeveWristRight','elbowRight', $this->v('sleeveVentLength')-$this->v('sleeveVentWidth')));
        $p->addPoint('ventTopRight', $p->rotate('ventTopRight','ventTopLeft',90));
        $p->addPoint('ventTopRight', $p->shiftTowards('ventTopRight','ventBottomRight',$this->v('sleeveVentWidth')/2));

        // Paths
        $p->newPath('outline', 'M elbowRight C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint C backPitchPoint sleeveTopCpRight sleeveTop C sleeveTopCpLeft frontPitchPointCpTop frontPitchPoint C frontPitchPointCpBottom topsleeveLeftEdgeCpRight topsleeveLeftEdge C topsleeveLeftEdge topsleeveElbowLeftCpTop topsleeveElbowLeft L topsleeveWristLeft L ventBottomRight L ventTopRight L ventTopLeft L elbowRight z', ['class' => 'fabric']);
        $p->newPath('ventHint', 'M ventTopLeft L topsleeveWristRight', ['class' => 'help']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);

        // Store length of the front and back sleevecap for the topsleeve
        $this->setValue('topsleevecapFrontLength', $p->curveLen('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint') + $p->curveLen('frontPitchPoint','frontPitchPointCpBottom','topsleeveLeftEdge','topsleeveLeftEdge'));
        $this->setValue('topsleevecapBackLength', $p->curveLen('sleeveTop','sleeveTopCpRight','backPitchPoint','backPitchPoint'));

    }

    /**
     * Drafts the undersleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftUndersleeve($model)
    {
        $this->clonePoints('undersleeveBlock', 'undersleeve');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeve'];

        // Vent 
        $p->addPoint('ventBottomRight', $p->shiftOutwards('undersleeveWristLeft','undersleeveWristRight',$this->v('sleeveVentWidth')));
        $p->addPoint('ventTopLeft', $p->shiftTowards('undersleeveWristRight','elbowRight', $this->v('sleeveVentLength')));
        $p->addPoint('ventTopRight', $p->shiftTowards('undersleeveWristRight','elbowRight', $this->v('sleeveVentLength')-$this->v('sleeveVentWidth')));
        $p->addPoint('ventTopRight', $p->rotate('ventTopRight','ventTopLeft',90));
        $p->addPoint('ventTopRight', $p->shiftTowards('ventTopRight','ventBottomRight',$this->v('sleeveVentWidth')/2));

        // Paths
        $p->newPath('undersleeve', 'M undersleeveWristRight L ventBottomRight L ventTopRight L ventTopLeft L elbowRight C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip C undersleeveTipCpBottom undersleeveLeftEdgeCpRight undersleeveLeftEdgeRight L undersleeveLeftEdge C undersleeveLeftEdge undersleeveElbowLeftCpTop undersleeveElbowLeft L undersleeveWristLeft L undersleeveWristRight z', ['class' => 'fabric']);
        $p->newPath('ventHint', 'M ventTopLeft L undersleeveWristRight', ['class' => 'help']);
        
       
        // Mark path for sample service
        $p->paths['undersleeve']->setSample(true);

    }

    /**
     * Draws zebra stripes on the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function zebraBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        if(1 || $this->o('zebra') == 0) {
            $shiftThese = [
                'centerBackNeck',
                'chestCenterCpTop',
                'chestCenter',
                'chestCenterCpBottom',
                'waistCenterCpTop',
                'waistCenter',
                'waistCenterCpBottom',
                'hipsCenterCpTop',
                'hipsCenter',
                'hemCenter'
            ];
        }
        // Shift center back
        foreach($shiftThese as $pid) $p->addPoint('zCb'.ucfirst($pid), $p->shift($pid,0,$this->o('zebraCenterBackClearance')));

        // Stripe marks 

        // Neck opening, stripe 1
        $len1 = $p->curveLen('centerBackNeck', 20, 8, 8);
        $p->addPoint('z.1.Top', $p->shiftAlong('centerBackNeck', 20, 8, 8, $len1/3));
        $p->addPoint('z.1.Bot', $p->shiftAlong('centerBackNeck', 20, 8, 8, $len1*0.9));

        // Shoulder seam, stripes 2,3 
        $p->addPoint('z.2.Top', $p->shiftFractionTowards(8,12,0.15));
        $p->addPoint('z.2.Bot', $p->shiftFractionTowards(8,12,0.5));
        $p->addPoint('z.3.Top', $p->shiftFractionTowards(8,12,0.7));

        // Armhole, stripes 3,4,5 
        $len1 = $p->curveLen(12, 19, 17, 10);
        $p->addPoint('z.3.Bot', $p->shiftAlong(12, 19, 17, 10, $len1*0.3));
        $p->addPoint('z.4.Top', $p->shiftAlong(12, 19, 17, 10, $len1*0.5));
        $p->clonePoint(10,'z.4.Bot');
        $len2 = $p->curveLen(10, 18, 15, 14);
        $p->addPoint('z.5.Top', $p->shiftAlong(10, 18, 15, 14, $len2*0.2));
        $p->addPoint('z.5.Bot', $p->shiftAlong(10, 18, 15, 14, $len2*0.9));
        
        // Side seam part 1, stripes 6,7,8 Rhytm = 0.1, 0.3, 0.4, 0.6, 0.7, 0.9
        $len1 = $p->curveLen(14, 'slArmCpBottom', 'waistBackSideCpTop', 'waistBackSide');
        $p->addPoint('z.6.Top', $p->shiftAlong(14, 'slArmCpBottom', 'waistBackSideCpTop', 'waistBackSide', $len1*0.1));
        $p->addPoint('z.6.Bot', $p->shiftAlong(14, 'slArmCpBottom', 'waistBackSideCpTop', 'waistBackSide', $len1*0.3));
        $p->addPoint('z.7.Top', $p->shiftAlong(14, 'slArmCpBottom', 'waistBackSideCpTop', 'waistBackSide', $len1*0.4));
        $p->addPoint('z.7.Bot', $p->shiftAlong(14, 'slArmCpBottom', 'waistBackSideCpTop', 'waistBackSide', $len1*0.6));
        $p->addPoint('z.8.Top', $p->shiftAlong(14, 'slArmCpBottom', 'waistBackSideCpTop', 'waistBackSide', $len1*0.7));
        $p->addPoint('z.8.Bot', $p->shiftAlong(14, 'slArmCpBottom', 'waistBackSideCpTop', 'waistBackSide', $len1*0.9));
        // Side seam part 2, stripes 9,10 Rhytm = 0.3, 0.5, 0.9
        $len2 = $p->curveLen('waistBackSide', 'waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide');
        $p->clonePoint('waistBackSide', 'z.9.Top');
        $p->addPoint('z.9.Bot', $p->shiftAlong('waistBackSide', 'waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide', $len2*0.3));
        $p->addPoint('z.10.Top', $p->shiftAlong('waistBackSide', 'waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide', $len2*0.5));
        $p->addPoint('z.10.Bot', $p->shiftAlong('waistBackSide', 'waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide', $len2*0.9));
        // Side seam part 3, stripe 11 Rhytm = 0.5
        $p->addPoint('z.11.Top', $p->shiftFractionTowards('hipsBackSide','hemBackSide', 0.5));
        // Hem, stripes 11,12 Rhytm = 0.2, 0.4, 0.7
        $p->addPoint('z.11.Bot', $p->shiftFractionTowards('hemEdgeBackSide', 'hemEdgeCenter',0.2));
        $p->addPoint('z.12.Top', $p->shiftFractionTowards('hemEdgeBackSide', 'hemEdgeCenter',0.4));
        $p->addPoint('z.12.Bot', $p->shiftFractionTowards('hemEdgeBackSide', 'hemEdgeCenter',0.7));

        // Stripe tips
        $p->clonePoint('zCbChestCenterCpTop', 'z.1.Tip');
        $p->addPoint('z.2.Tip', $p->shiftFractionTowards('zCbChestCenter', 'zCbChestCenterCpTop', 0.5));
        $p->clonePoint('zCbChestCenter', 'z.3.Tip');
        $len1 = $p->curveLen('zCbChestCenter','zCbChestCenterCpBottom','zCbWaistCenterCpTop','zCbWaistCenter');
        $p->addPoint('z.4.Tip', $p->shiftAlong('zCbChestCenter','zCbChestCenterCpBottom','zCbWaistCenterCpTop','zCbWaistCenter', $len1*0.15));
        $p->addPoint('z.5.Tip', $p->shiftAlong('zCbChestCenter','zCbChestCenterCpBottom','zCbWaistCenterCpTop','zCbWaistCenter', $len1*0.35));
        $p->addPoint('z.6.Tip', $p->shiftAlong('zCbChestCenter','zCbChestCenterCpBottom','zCbWaistCenterCpTop','zCbWaistCenter', $len1*0.51));
        $p->addPoint('z.7.Tip', $p->shiftAlong('zCbChestCenter','zCbChestCenterCpBottom','zCbWaistCenterCpTop','zCbWaistCenter', $len1*0.67));
        $p->addPoint('z.8.Tip', $p->shiftAlong('zCbChestCenter','zCbChestCenterCpBottom','zCbWaistCenterCpTop','zCbWaistCenter', $len1*0.83));
        $p->addPoint('z.9.Tip', $p->shiftAlong('zCbChestCenter','zCbChestCenterCpBottom','zCbWaistCenterCpTop','zCbWaistCenter', $len1*0.99));
        $len2 = $p->curveLen('zCbWaistCenter','zCbWaistCenterCpBottom','zCbHipsCenterCpTop','zCbHipsCenter');
        $p->addPoint('z.10.Tip', $p->shiftAlong('zCbWaistCenter','zCbWaistCenterCpBottom','zCbHipsCenterCpTop','zCbHipsCenter', $len2*0.3));
        $p->addPoint('z.11.Tip', $p->shiftAlong('zCbWaistCenter','zCbWaistCenterCpBottom','zCbHipsCenterCpTop','zCbHipsCenter', $len2*0.6));
        $p->addPoint('z.12.Tip', $p->shiftAlong('zCbWaistCenter','zCbWaistCenterCpBottom','zCbHipsCenterCpTop','zCbHipsCenter', $len2*0.9));


        // Make stripes a bit more organic
        for($s=1;$s<=12;$s++) {
            if($s == 1) $p->addPoint('.helper', $p->shiftTowards("z.$s.Tip", "z.$s.Top", 20));
            else $p->addPoint('.helper', $p->shiftTowards("z.$s.Tip", "z.$s.Top", 30));
            $p->addPoint("z.$s.TipCpTop", $p->rotate('.helper', "z.$s.Tip", 40));
            $p->addPoint("z.$s.TipCpBot", $p->rotate("z.$s.TipCpTop", "z.$s.Tip", -90));
            
            $p->addPoint("z.$s.TopA", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.3));
            $p->addPoint("z.$s.TopACpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.2));
            $p->addPoint("z.$s.TopACpTip", $p->rotate("z.$s.TopACpTip", "z.$s.TopA", -15));
            $p->addPoint("z.$s.TopACpTop", $p->rotate("z.$s.TopACpTip", "z.$s.TopA", 180));
            $p->addPoint("z.$s.TopB", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.7));
            $p->addPoint("z.$s.TopBCpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.6));
            $p->addPoint("z.$s.TopBCpTip", $p->rotate("z.$s.TopBCpTip", "z.$s.TopB", -15));
            $p->addPoint("z.$s.TopBCpTop", $p->rotate("z.$s.TopBCpTip", "z.$s.TopB", 180));

            $p->addPoint("z.$s.BotA", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.4));
            $p->addPoint("z.$s.BotA", $p->shift("z.$s.BotA",-90,15));
            $p->addPoint("z.$s.BotACpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.3));
            if($s == 1) $p->addPoint("z.$s.BotACpTip", $p->rotate("z.$s.BotACpTip", "z.$s.BotA", 80));
            else $p->addPoint("z.$s.BotACpTip", $p->rotate("z.$s.BotACpTip", "z.$s.BotA", 20));
            $p->addPoint("z.$s.BotACpBot", $p->rotate("z.$s.BotACpTip", "z.$s.BotA", 180));
            $p->addPoint("z.$s.BotB", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.7));
            $p->addPoint("z.$s.BotB", $p->shift("z.$s.BotB",-90,15));
            $p->addPoint("z.$s.BotBCpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.6));
            if($s == 1) $p->addPoint("z.$s.BotBCpTip", $p->rotate("z.$s.BotBCpTip", "z.$s.BotB", 65));
            else $p->addPoint("z.$s.BotBCpTip", $p->rotate("z.$s.BotBCpTip", "z.$s.BotB", 25));
            $p->addPoint("z.$s.BotBCpBot", $p->rotate("z.$s.BotBCpTip", "z.$s.BotB", 180));

            $p->clonePoint("z.$s.Top", "z.$s.TopCp");
            $p->clonePoint("z.$s.Bot", "z.$s.BotCp");

            $p->newPath("stripe$s", "
                M z.$s.Tip 
                C z.$s.TipCpTop z.$s.TopACpTip z.$s.TopA
                C z.$s.TopACpTop z.$s.TopBCpTip z.$s.TopB
                C z.$s.TopBCpTop z.$s.TopCp z.$s.Top 
                M z.$s.Bot 
                C z.$s.BotCp z.$s.BotBCpBot z.$s.BotB
                C z.$s.BotBCpTip z.$s.BotACpBot z.$s.BotA
                C z.$s.BotACpTip z.$s.TipCpBot z.$s.Tip 
            ", ['class' => 'fabric hint zebra']);

            // Basic triangles
            //$p->newPath("stripe$s", "M z.$s.Tip L z.$s.Top L z.$s.Bot z", ['style' => 'stroke: #fff']);
        }

        // Some manual tweaks
        $p->addPoint('z.2.TopCp', $p->shift('z.2.TopCp', $p->angle(8,12)+90, 15));
        $p->addPoint('z.2.BotCp', $p->shift('z.2.BotCp', $p->angle(8,12)+90, 10));
        $p->addPoint('z.3.TopCp', $p->shift('z.3.TopCp', $p->angle(8,12)+90, 15));
        $p->addPoint('z.3.BotCp', $p->shift('z.3.BotCp', $p->angle(12,19)+90, 10));
        $p->addPoint('z.4.TopCp', $p->shift('z.4.TopCp', $p->angle(12,19)+90, 15));
        $p->addPoint('z.4.BotCp', $p->shift('z.4.BotCp', 180, 10));
        $p->addPoint('z.5.TopCp', $p->shift('z.5.TopCp', 180, 15));
        $p->addPoint('z.5.BotCp', $p->shift('z.5.BotCp', $p->angle('z.5.BotCp',14)+90, 15));
        $p->addPoint('z.6.TopCp', $p->shift('z.6.TopCp', $p->angle(14,'z.6.TopCp')+90, 15));
        $p->addPoint('z.6.BotCp', $p->shift('z.6.BotCp', $p->angle('z.6.TopCp','z.6.BotCp')+90, 20));
        $p->addPoint('z.7.TopCp', $p->shift('z.7.TopCp', $p->angle('z.6.Bot','z.7.Top')+90, 15));
        $p->addPoint('z.7.BotCp', $p->shift('z.7.BotCp', $p->angle('z.7.Top','z.7.Bot')+90, 20));
        $p->addPoint('z.8.TopCp', $p->shift('z.8.TopCp', $p->angle('z.7.Bot','z.8.Top')+90, 15));
        $p->addPoint('z.8.BotCp', $p->shift('z.8.BotCp', $p->angle('z.8.Top','z.8.Bot')+90, 20));
        $p->addPoint('z.9.TopCp', $p->shift('z.9.TopCp', $p->angle('z.8.Bot','z.9.Top')+90, 15));
        $p->addPoint('z.9.BotCp', $p->shift('z.9.BotCp', $p->angle('z.9.Top','z.9.Bot')+90, 20));
        $p->addPoint('z.10.TopCp', $p->shift('z.10.TopCp', $p->angle('z.9.Bot','z.10.Top')+90, 15));
        $p->addPoint('z.10.BotCp', $p->shift('z.10.BotCp', $p->angle('z.10.Top','z.10.Bot')+90, 20));
        $p->addPoint('z.11.TopCp', $p->shift('z.11.TopCp', 180, 15));

        // Store this for the undersleeve stripe
        $p->splitCurve(10,18,15,14,'z.5.Top','tmp');
        $this->setValue('backSleevePitchToJoint', $p->distance(10, 'sleeveJoint'));
        $this->setValue('backSleeveJointToStartStripe', $p->curveLen('tmp1','tmp2','tmp3','tmp4') - $p->distance(10, 'sleeveJoint'));
        $this->setValue('backSleeveJointToEndStripe', $p->curveLen('tmp5','tmp6','tmp7','tmp8') - $p->distance(14, 'z.5.Bot') + $this->v('backSleeveJointToStartStripe'));
    }

    /**
     * Draws zebra stripes on the side
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function zebraSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];

        // Right side
        // Side seam part 1, stripes 6,7,8 Rhytm = 0.1, 0.3, 0.4, 0.6, 0.7, 0.9
        $len = $p->curveLen('side14','sideSlArmCpBottom','sideWaistSideBackCpTop','sideWaistSideBack');
        $p->addPoint('rz.6.Top', $p->shiftAlong('side14','sideSlArmCpBottom','sideWaistSideBackCpTop','sideWaistSideBack', $len*0.1)); 
        $p->addPoint('rz.6.Bot', $p->shiftAlong('side14','sideSlArmCpBottom','sideWaistSideBackCpTop','sideWaistSideBack', $len*0.3)); 
        $p->addPoint('rz.7.Top', $p->shiftAlong('side14','sideSlArmCpBottom','sideWaistSideBackCpTop','sideWaistSideBack', $len*0.4)); 
        $p->addPoint('rz.7.Bot', $p->shiftAlong('side14','sideSlArmCpBottom','sideWaistSideBackCpTop','sideWaistSideBack', $len*0.6)); 
        $p->addPoint('rz.8.Top', $p->shiftAlong('side14','sideSlArmCpBottom','sideWaistSideBackCpTop','sideWaistSideBack', $len*0.7)); 
        $p->addPoint('rz.8.Bot', $p->shiftAlong('side14','sideSlArmCpBottom','sideWaistSideBackCpTop','sideWaistSideBack', $len*0.9)); 
        // Side seam part 2, stripes 9,10 Rhytm = 0.3, 0.5, 0.9
        $len = $p->curveLen('sideWaistSideBack','sideWaistSideBackCpBottom','sideHipsSideBackCpTop','sideHipsSideBack');
        $p->clonePoint('sideWaistSideBack','rz.9.Top');
        $p->addPoint('rz.9.Bot', $p->shiftAlong('sideWaistSideBack','sideWaistSideBackCpBottom','sideHipsSideBackCpTop','sideHipsSideBack', $len*0.3)); 
        $p->addPoint('rz.10.Top', $p->shiftAlong('sideWaistSideBack','sideWaistSideBackCpBottom','sideHipsSideBackCpTop','sideHipsSideBack', $len*0.5)); 
        $p->addPoint('rz.10.Bot', $p->shiftAlong('sideWaistSideBack','sideWaistSideBackCpBottom','sideHipsSideBackCpTop','sideHipsSideBack', $len*0.9)); 
        // Side seam part 3, stripe 11 Rhytm = 0.5
        $p->addPoint('rz.11.Top', $p->shiftFractionTowards('sideHipsSideBack','sideHemSideBack', 0.5)); 

        // Left side
        // Side seam part 1, stripes 6,7,8 Rhytm = 0.1, 0.3, 0.4, 0.6, 0.7, 0.9
        $len = $p->curveLen('slArm','slArm','waistSideBackCpTop','waistSideBack');
        $p->addPoint('lz.6.Top', $p->shiftAlong('slArm','slArm','waistSideBackCpTop','waistSideBack', $len*0.1)); 
        $p->addPoint('lz.6.Bot', $p->shiftAlong('slArm','slArm','waistSideBackCpTop','waistSideBack', $len*0.3)); 
        $p->addPoint('lz.7.Top', $p->shiftAlong('slArm','slArm','waistSideBackCpTop','waistSideBack', $len*0.4)); 
        $p->addPoint('lz.7.Bot', $p->shiftAlong('slArm','slArm','waistSideBackCpTop','waistSideBack', $len*0.6)); 
        $p->addPoint('lz.8.Top', $p->shiftAlong('slArm','slArm','waistSideBackCpTop','waistSideBack', $len*0.7)); 
        $p->addPoint('lz.8.Bot', $p->shiftAlong('slArm','slArm','waistSideBackCpTop','waistSideBack', $len*0.9)); 
        // Side seam part 2, stripes 9,10 Rhytm = 0.3, 0.5, 0.9
        $len = $p->curveLen('waistSideBack','waistSideBackCpBottom','hipsSideBackCpTop','hipsSideBack');
        $p->clonePoint('waistSideBack','lz.9.Top');
        $p->addPoint('lz.9.Bot',  $p->shiftAlong('waistSideBack','waistSideBackCpBottom','hipsSideBackCpTop','hipsSideBack', $len*0.3)); 
        $p->addPoint('lz.10.Top', $p->shiftAlong('waistSideBack','waistSideBackCpBottom','hipsSideBackCpTop','hipsSideBack', $len*0.5)); 
        $p->addPoint('lz.10.Bot', $p->shiftAlong('waistSideBack','waistSideBackCpBottom','hipsSideBackCpTop','hipsSideBack', $len*0.9)); 
        // Side seam part 3, stripe 11 Rhytm = 0.5
        $p->addPoint('lz.11.Top', $p->shiftFractionTowards('hipsSideBack','hemSideBack', 0.5)); 

        // Make stripes a bit more organic
        for($s=6;$s<=10;$s++) {
            $p->addPoint("rz.$s.TopCp", $p->shift("rz.$s.Top", 180, 15));
            $p->addPoint("rz.$s.BotCp", $p->shift("rz.$s.Bot", 180, 15));
            $p->addPoint("lz.$s.TopCp", $p->shift("lz.$s.Top", 0, 15));
            $p->addPoint("lz.$s.BotCp", $p->shift("lz.$s.Bot", 0, 15));

            $p->addPoint("z.$s.TopMid", $p->shiftFractionTowards("rz.$s.Top","lz.$s.Top", 0.5));
            $p->addPoint("z.$s.TopMid", $p->shift("z.$s.TopMid", -90, 10));
            $p->addPoint("z.$s.TopMidCpR", $p->shiftFractionTowards("rz.$s.Top","lz.$s.Top", 0.35));
            $p->addPoint("z.$s.TopMidCpR", $p->shift("z.$s.TopMidCpR", -90, 10));
            $p->addPoint("z.$s.TopMidCpR", $p->rotate("z.$s.TopMidCpR", "z.$s.TopMid", 10));
            $p->addPoint("z.$s.TopMidCpL", $p->rotate("z.$s.TopMidCpR", "z.$s.TopMid", 180));

            $p->addPoint("z.$s.BotMid", $p->shiftFractionTowards("rz.$s.Bot","lz.$s.Bot", 0.5));
            $p->addPoint("z.$s.BotMid", $p->shift("z.$s.BotMid", -90, 5));
            $p->addPoint("z.$s.BotMidCpR", $p->shiftFractionTowards("rz.$s.Bot","lz.$s.Bot", 0.35));
            $p->addPoint("z.$s.BotMidCpR", $p->shift("z.$s.BotMidCpR", -90, 10));
            $p->addPoint("z.$s.BotMidCpR", $p->rotate("z.$s.BotMidCpR", "z.$s.BotMid", 10));
            $p->addPoint("z.$s.BotMidCpL", $p->rotate("z.$s.BotMidCpR", "z.$s.BotMid", 180));

            $p->newPath("stripe$s", "
                M lz.$s.Top 
                C lz.$s.TopCp z.$s.TopMidCpL z.$s.TopMid 
                C z.$s.TopMidCpR rz.$s.TopCp rz.$s.Top
                M rz.$s.Bot 
                C rz.$s.BotCp z.$s.BotMidCpR z.$s.BotMid
                C z.$s.BotMidCpL lz.$s.BotCp lz.$s.Bot
            ", ['class' => 'fabric hint zebra']);
        }
        // Stripe11 only has a top
        $s = 11;
        $p->addPoint("rz.$s.TopCp", $p->shift("rz.$s.Top", 180, 15));
        $p->addPoint("lz.$s.TopCp", $p->shift("lz.$s.Top", 0, 15));
        $p->addPoint("z.$s.TopMid", $p->shiftFractionTowards("rz.$s.Top","lz.$s.Top", 0.5));
        $p->addPoint("z.$s.TopMid", $p->shift("z.$s.TopMid", -90, 5));
        $p->addPoint("z.$s.TopMidCpR", $p->shiftFractionTowards("rz.$s.Top","lz.$s.Top", 0.35));
        $p->addPoint("z.$s.TopMidCpR", $p->shift("z.$s.TopMidCpR", -90, 10));
        $p->addPoint("z.$s.TopMidCpR", $p->rotate("z.$s.TopMidCpR", "z.$s.TopMid", 10));
        $p->addPoint("z.$s.TopMidCpL", $p->rotate("z.$s.TopMidCpR", "z.$s.TopMid", 180));

        $p->newPath("stripe$s", "
            M lz.$s.Top 
            C lz.$s.TopCp z.$s.TopMidCpL z.$s.TopMid 
            C z.$s.TopMidCpR rz.$s.TopCp rz.$s.Top
        ", ['class' => 'fabric hint zebra']);
    }

    /**
     * Draws zebra stripes on the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function zebraFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];
        /** @var \Freesewing\Part $p */
        $side = $this->parts['side'];

        /** @var \Freesewing\Part $p */
        $back = $this->parts['back'];

        // Shoulder seam, stripes 2,3 
        $p->addPoint('z.2.Top', $p->shiftTowards('shoulderLineRight', 'shoulderLineRealLeft', $back->distance(12, 'z.2.Top')));   
        $p->addPoint('z.2.Bot', $p->shiftTowards('shoulderLineRight', 'shoulderLineRealLeft', $back->distance(12, 'z.2.Bot')));   
        $p->addPoint('z.3.Top', $p->shiftTowards('shoulderLineRight', 'shoulderLineRealLeft', $back->distance(12, 'z.3.Top')));   
        // Armhole, stripes 3,4,5 
        $len1 = $p->curveLen(12, 19, 17, 10);
        $p->addPoint('z.3.Bot', $p->shiftAlong(12, 19, 17, 10, $len1*0.3));
        $p->addPoint('z.4.Top', $p->shiftAlong(12, 19, 17, 10, $len1*0.5));
        $p->clonePoint(10,'z.4.Bot');
        $len2 = $p->curveLen(10, 18, 15, 14);
        $p->addPoint('z.5.Top', $p->shiftAlong(10, 18, 15, 14, $len2*0.3));
        $p->clonePoint(14,'z.5.Bot');
        // Side seam part 1, stripes 6,7,8 Rhytm = 0.1, 0.3, 0.4, 0.6, 0.7, 0.9
        $len = $side->curveLen('slArm','slArm','waistSideBackCpTop','waistSideBack');
        $p->addPoint('z.6.Top', $p->shiftAlong('slArm','slArm','waistBackSideCpTop','waistBackSide', $len*0.1)); 
        $p->addPoint('z.6.Bot', $p->shiftAlong('slArm','slArm','waistBackSideCpTop','waistBackSide', $len*0.3)); 
        $p->addPoint('z.7.Top', $p->shiftAlong('slArm','slArm','waistBackSideCpTop','waistBackSide', $len*0.4)); 
        $p->addPoint('z.7.Bot', $p->shiftAlong('slArm','slArm','waistBackSideCpTop','waistBackSide', $len*0.6)); 
        $p->addPoint('z.8.Top', $p->shiftAlong('slArm','slArm','waistBackSideCpTop','waistBackSide', $len*0.7)); 
        $p->addPoint('z.8.Bot', $p->shiftAlong('slArm','slArm','waistBackSideCpTop','waistBackSide', $len*0.9)); 
        // Side seam part 2, stripes 9,10 Rhytm = 0.3, 0.5, 0.9
        $len = $side->curveLen('sideWaistSideBack','sideWaistSideBackCpBottom','sideHipsSideBackCpTop','sideHipsSideBack');
        $p->clonePoint('waistBackSide','z.9.Top');
        $p->addPoint('z.9.Bot', $p->shiftAlong('waistBackSide','waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide', $len*0.3)); 
        $p->addPoint('z.10.Top', $p->shiftAlong('waistBackSide','waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide', $len*0.5)); 
        $p->addPoint('z.10.Bot', $p->shiftAlong('waistBackSide','waistBackSideCpBottom','hipsBackSideCpTop','hipsBackSide', $len*0.9)); 
        // Side seam part 3, stripe 11 Rhytm = 0.5
        $p->addPoint('z.11.Top', $p->shiftFractionTowards('hipsBackSide','hemBackSide', 0.5)); 
        // Hem, stripes 11,12 Rhytm = 0.2, 0.4, 0.7
        $p->addPoint('z.11.Bot', $p->shiftFractionTowards('frontSideHemEdge', 'cfHemEdge',0.2));
        $p->addPoint('z.12.Top', $p->shiftFractionTowards('frontSideHemEdge', 'cfHemEdge',0.4));
        $p->addPoint('z.12.Bot', $p->shiftFractionTowards('frontSideHemEdge', 'cfHemEdge',0.7));

        // Stripe tips
        // Zebra edge line
        $p->addPoint('zlineBot', $p->shift('cfHem',0,10));
        $p->addPoint('zlineTop', $p->shift('centerBackNeck',0,30));
        $p->addPoint('z.2.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.18));
        $p->addPoint('z.3.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.26));
        $p->addPoint('z.4.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.35));
        $p->addPoint('z.5.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.44));
        $p->addPoint('z.6.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.5));
        $p->addPoint('z.7.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.57));
        $p->addPoint('z.8.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.64));
        $p->addPoint('z.9.Tip',  $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.7));
        $p->addPoint('z.10.Tip', $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.76));
        $p->addPoint('z.11.Tip', $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.82));
        $p->addPoint('z.12.Tip', $p->shiftFractionTowards('zlineTop', 'zlineBot', 0.9));

        // Make stripes a bit more organic
        for($s=2;$s<=12;$s++) {
            $p->addPoint('.helper', $p->shiftTowards("z.$s.Tip", "z.$s.Top", 30));
            $p->addPoint("z.$s.TipCpTop", $p->rotate('.helper', "z.$s.Tip", 40));
            $p->addPoint("z.$s.TipCpBot", $p->rotate("z.$s.TipCpTop", "z.$s.Tip", -90));
            $p->addPoint("z.$s.TopA", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.3));
            $p->addPoint("z.$s.TopACpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.2));
            $p->addPoint("z.$s.TopACpTip", $p->rotate("z.$s.TopACpTip", "z.$s.TopA", -15));
            $p->addPoint("z.$s.TopACpTop", $p->rotate("z.$s.TopACpTip", "z.$s.TopA", 180));
            $p->addPoint("z.$s.TopB", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.7));
            $p->addPoint("z.$s.TopBCpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Top", 0.6));
            $p->addPoint("z.$s.TopBCpTip", $p->rotate("z.$s.TopBCpTip", "z.$s.TopB", -15));
            $p->addPoint("z.$s.TopBCpTop", $p->rotate("z.$s.TopBCpTip", "z.$s.TopB", 180));

            $p->addPoint("z.$s.BotA", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.4));
            $p->addPoint("z.$s.BotA", $p->shift("z.$s.BotA",-90,15));
            $p->addPoint("z.$s.BotACpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.3));
            $p->addPoint("z.$s.BotACpTip", $p->rotate("z.$s.BotACpTip", "z.$s.BotA", 20));
            $p->addPoint("z.$s.BotACpBot", $p->rotate("z.$s.BotACpTip", "z.$s.BotA", 180));
            $p->addPoint("z.$s.BotB", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.7));
            $p->addPoint("z.$s.BotB", $p->shift("z.$s.BotB",-90,15));
            $p->addPoint("z.$s.BotBCpTip", $p->shiftFractionTowards("z.$s.Tip", "z.$s.Bot", 0.6));
            $p->addPoint("z.$s.BotBCpTip", $p->rotate("z.$s.BotBCpTip", "z.$s.BotB", 25));
            $p->addPoint("z.$s.BotBCpBot", $p->rotate("z.$s.BotBCpTip", "z.$s.BotB", 180));

            $p->clonePoint("z.$s.Top", "z.$s.TopCp");
            $p->clonePoint("z.$s.Bot", "z.$s.BotCp");

            $p->newPath("stripe$s", "
                M z.$s.Tip 
                C z.$s.TipCpTop z.$s.TopACpTip z.$s.TopA
                C z.$s.TopACpTop z.$s.TopBCpTip z.$s.TopB
                C z.$s.TopBCpTop z.$s.TopCp z.$s.Top 
                M z.$s.Bot 
                C z.$s.BotCp z.$s.BotBCpBot z.$s.BotB
                C z.$s.BotBCpTip z.$s.BotACpBot z.$s.BotA
                C z.$s.BotACpTip z.$s.TipCpBot z.$s.Tip 
            ", ['class' => 'fabric hint zebra']);
            
            // Basic triangles
            //$p->newPath("stripe$s", "M z.$s.Tip L z.$s.Top L z.$s.Bot z", ['style' => 'stroke: #fff']);
        }
        
        // Some manual tweaks
        $p->addPoint('z.2.TopCp', $p->shift('z.2.TopCp', $p->angle('shoulderLineRealLeft','shoulderLineRight')+90, 15));
        $p->addPoint('z.2.BotCp', $p->shift('z.2.BotCp', $p->angle('shoulderLineRealLeft','shoulderLineRight')+90, 10));
        $p->addPoint('z.3.TopCp', $p->shift('z.3.TopCp', $p->angle('shoulderLineRealLeft','shoulderLineRight')+90, 15));
        $p->addPoint('z.3.BotCp', $p->shift('z.3.BotCp', $p->angle('shoulderLineRight','z.3.Bot')+90, 10));
        $p->addPoint('z.4.TopCp', $p->shift('z.4.TopCp', $p->angle('z.3.Bot','z.4.Top')+90, 15));
        $p->addPoint('z.4.BotCp', $p->shift('z.4.BotCp', $p->angle('z.4.Top','z.4.Bot')+90, 10));
        $p->addPoint('z.5.TopCp', $p->shift('z.5.TopCp', $p->angle('z.4.Bot','z.5.Top')+90, 15));
        $p->addPoint('z.5.BotCp', $p->shift('z.5.BotCp', $p->angle('z.5.Top','z.5.Bot')+90, 10));
        $p->addPoint('z.6.TopCp', $p->shift('z.6.TopCp', $p->angle('slArm','z.6.Top')+90, 35));
        $p->addPoint('z.6.TopB', $p->shift('z.6.TopB',90,20));
        $p->addPoint('z.6.TopBCpTop', $p->shift('z.6.TopBCpTop',90,20));
        $p->addPoint('z.6.TopBCpTip', $p->shift('z.6.TopBCpTip',90,20));
        $p->addPoint('z.6.BotCp', $p->shift('z.6.BotCp', $p->angle('z.6.Top','z.6.Bot')+90, 10));
        $p->addPoint('z.7.TopCp', $p->shift('z.7.TopCp', $p->angle('z.6.Bot','z.7.Top')+90, 15));
        $p->addPoint('z.7.BotCp', $p->shift('z.7.BotCp', $p->angle('z.7.Top','z.7.Bot')+90, 10));
        $p->addPoint('z.8.TopCp', $p->shift('z.8.TopCp', $p->angle('z.7.Bot','z.8.Top')+90, 15));
        $p->addPoint('z.8.BotCp', $p->shift('z.8.BotCp', $p->angle('z.8.Top','z.8.Bot')+90, 10));
        $p->addPoint('z.9.TopCp', $p->shift('z.9.TopCp', $p->angle('z.8.Bot','z.9.Top')+90, 15));
        $p->addPoint('z.9.BotCp', $p->shift('z.9.BotCp', $p->angle('z.9.Top','z.9.Bot')+90, 10));
        $p->addPoint('z.10.TopCp', $p->shift('z.10.TopCp', $p->angle('z.9.Bot','z.10.Top')+90, 15));
        $p->addPoint('z.10.BotCp', $p->shift('z.10.BotCp', $p->angle('z.10.Top','z.10.Bot')+90, 10));
        $p->addPoint('z.11.TopCp', $p->shift('z.11.TopCp', $p->angle('z.10.Bot','z.11.Top')+90, 15));
        
        // Handle dart in stripe 6
        $p->newPoint('z.6.BotA', $p->x('frontDartTop'), $p->y('z.6.BotA'));
        $p->addPoint('z.6.BotACpBot', $p->shift('z.6.BotA', 0, 20));

        // Handle dart in stripe 7, top part
        $p->curvesCross('z.7.TopB','z.7.TopBCpTip','z.7.TopACpTop','z.7.TopA','frontDartTop','frontDartTop', 'frontDartRightCpTop','frontDartRight','stripe7x');
        $p->splitCurve('z.7.TopB','z.7.TopBCpTip','z.7.TopACpTop','z.7.TopA','stripe7x1','topSplit7');
        $p->addPoint('topSplit78', $p->flipX('topSplit78', $p->x('frontDartTop')));
        $p->addPoint('topSplit77', $p->shift('topSplit77', 180, $p->distance('topSplit78','topSplit74')));
        // Handle dart in stripe 7, bottom part
        $p->curvesCross('z.7.BotB','z.7.BotBCpTip','z.7.BotACpBot','z.7.BotA','frontDartTop','frontDartTop', 'frontDartRightCpTop','frontDartRight','stripe7x');
        $p->splitCurve( 'z.7.BotB','z.7.BotBCpTip','z.7.BotACpBot','z.7.BotA','stripe7x1','botSplit7');
        $p->addPoint('botSplit78', $p->flipX('botSplit78', $p->x('frontDartTop')));
        $p->addPoint('botSplit77', $p->shift('botSplit77', 180, $p->distance('botSplit78','botSplit74')));
        // Redraw stripe 7 
        $p->newPath("stripe7", "
            M z.7.Tip 
            C z.7.TipCpTop z.7.TopACpTip z.7.TopA
            C topSplit76 topSplit77 topSplit78
            L topSplit74
            C topSplit73 topSplit72 topSplit71 
            C z.7.TopBCpTop z.7.TopCp z.7.Top 
            M z.7.Bot 
            C z.7.BotCp z.7.BotBCpBot z.7.BotB
            C botSplit72 botSplit73 botSplit74
            L botSplit78
            C botSplit77 botSplit76 botSplit75
            C z.7.BotACpTip z.7.TipCpBot z.7.Tip 
        ", ['class' => 'fabric hint zebra']);

        // Handle dart in stripe 8, top part
        $p->curvesCross('z.8.TopB','z.8.TopBCpTip','z.8.TopACpTop','z.8.TopA','frontDartTop','frontDartTop', 'frontDartRightCpTop','frontDartRight','stripe8x');
        $p->splitCurve('z.8.TopB','z.8.TopBCpTip','z.8.TopACpTop','z.8.TopA','stripe8x1','topSplit8');
        $p->addPoint('topSplit88', $p->flipX('topSplit88', $p->x('frontDartTop')));
        $p->addPoint('topSplit87', $p->shift('topSplit87', 180, $p->distance('topSplit88','topSplit84')));
        // Handle dart in stripe 8, bottom part
        $p->curvesCross('z.8.BotB','z.8.BotBCpTip','z.8.BotACpBot','z.8.BotA','frontDartTop','frontDartTop', 'frontDartRightCpTop','frontDartRight','stripe8x');
        $p->splitCurve( 'z.8.BotB','z.8.BotBCpTip','z.8.BotACpBot','z.8.BotA','stripe8x1','botSplit8');
        $p->addPoint('botSplit88', $p->flipX('botSplit88', $p->x('frontDartTop')));
        $p->addPoint('botSplit87', $p->shift('botSplit87', 180, $p->distance('botSplit88','botSplit84')));
        // Redraw stripe 8 
        $p->newPath("stripe8", "
            M z.8.Tip 
            C z.8.TipCpTop z.8.TopACpTip z.8.TopA
            C topSplit86 topSplit87 topSplit88
            L topSplit84
            C topSplit83 topSplit82 topSplit81 
            C z.8.TopBCpTop z.8.TopCp z.8.Top 
            M z.8.Bot 
            C z.8.BotCp z.8.BotBCpBot z.8.BotB
            C botSplit82 botSplit83 botSplit84
            L botSplit88
            C botSplit87 botSplit86 botSplit85
            C z.8.BotACpTip z.8.TipCpBot z.8.Tip 
        ", ['class' => 'fabric hint zebra']);
        
        // Handle dart in stripe 9
        $p->addPoint('z.9.TopA', $p->shift('z.9.TopA',90,6));
        $p->addPoint('z.9.TopACpTop', $p->shift('z.9.TopACpTop',90,6));
        $p->addPoint('z.9.TopACpTip', $p->shift('z.9.TopACpTip',90,6));
    }

    /**
     * Draws zebra stripes on the topsleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function zebraTopsleeve($model)
    {
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeve'];

        /** @var \Freesewing\Part $front */
        $front = $this->parts['front'];

        /** @var \Freesewing\Part $back */
        $back = $this->parts['back'];

        // Along sleevecap front
        $len1 = $front->curveLen(12,19,17,10);
        $full1 = $len1 + $this->o('sleevecapEase')/2;
        $factor = $full1/$len1;
        $p->addPoint('z.3.BotIn', $p->shiftalong('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint', ($len1*0.3)*$factor));
        $p->addPoint('z.4.TopIn', $p->shiftalong('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint', ($len1*0.5)*$factor));
        $p->clonePoint('frontSleeveNotch','z.4.BotIn');

        $len2 = $front->curveLen(10, 18, 15, 14);
        $limit1 = $p->curveLen('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint');
        $limit2 = $p->curveLen('frontPitchPoint','frontPitchPointCpBottom','topsleeveLeftEdgeCpRight','topsleeveLeftEdge');

        // Does this fall in the first or second curve?
        if($len2*0.3 + $full1 == $limit1) $p->clonePoint('frontPitchPoint', 'z.5.TopIn');
        elseif ($len2*0.3 + $full1 < $limit1) $p->addPoint('z.5.TopIn', $p->shiftalong('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint', $len2*0.3 + $full1));
        else $p->addPoint('z.5.TopIn', $p->shiftalong('frontPitchPoint','frontPitchPointCpBottom','topsleeveLeftEdgeCpRight','topsleeveLeftEdge', ($len2*0.3 + $full1)-$limit1));

        if($len2 + $full1 == $limit1) $p->clonePoint('frontPitchPoint', 'z.5.BotIn');
        elseif ($len2 + $full1 < $limit1) $p->addPoint('z.5.BotIn', $p->shiftalong('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint', $len2 + $full1));
        else $p->addPoint('z.5.BotIn', $p->shiftalong('frontPitchPoint','frontPitchPointCpBottom','topsleeveLeftEdgeCpRight','topsleeveLeftEdge', ($len2 + $full1)-$limit1));


        // Along sleevecap back
        $len1 = $back->curveLen(12,19,17,10);
        $full1 = $len1 + $this->o('sleevecapEase')/2;
        $factor = $full1/$len1;
        $p->addPoint('z.3.BotOut', $p->shiftalong('sleeveTop','sleeveTopCpRight','backPitchPoint','backPitchPoint', ($len1*0.3)*$factor));
        $p->addPoint('z.4.TopOut', $p->shiftalong('sleeveTop','sleeveTopCpRight','backPitchPoint','backPitchPoint', ($len1*0.5)*$factor));


        if($p->isPoint('backSleeveNotch')) {
            $p->clonePoint('backSleeveNotch','z.4.BotOut');
            $p->newPath('stripe4', 'M z.4.TopIn L z.4.TopOut L  z.4.BotOut L z.4.BotIn z', ['class' => 'fabric;']);
        }

        $p->curveCrossesY('topsleeveRightEdge', 'topsleeveRightEdgeCpTop', 'backPitchPoint', 'backPitchPoint', $p->y('z.5.TopIn')-5, 'z.5.TopOut');
        $p->clonePoint('z.5.TopOut1','z.5.TopOut');
        $p->curveCrossesY('topsleeveRightEdge', 'topsleeveRightEdgeCpTop', 'backPitchPoint', 'backPitchPoint', $p->y('z.5.BotIn')-5, 'z.5.BotOut');
        $p->clonePoint('z.5.BotOut1','z.5.BotOut');
        
        $p->newPath('stripe5', 'M z.5.TopIn L z.5.TopOut L  z.5.BotOut L z.5.BotIn z', ['class' => 'fabric']);

        // straight down, left side
        $p->addPoint('z.6.TopIn', $p->shiftFractionAlong('topsleeveLeftEdge', 'topsleeveLeftEdge', 'topsleeveElbowLeftCpTop','topsleeveElbowLeft', 0.13));
        $p->addPoint('z.6.BotIn', $p->shiftFractionAlong('topsleeveLeftEdge', 'topsleeveLeftEdge', 'topsleeveElbowLeftCpTop','topsleeveElbowLeft', 0.37));
        $p->addPoint('z.7.TopIn', $p->shiftFractionAlong('topsleeveLeftEdge', 'topsleeveLeftEdge', 'topsleeveElbowLeftCpTop','topsleeveElbowLeft', 0.48));
        $p->addPoint('z.7.BotIn', $p->shiftFractionAlong('topsleeveLeftEdge', 'topsleeveLeftEdge', 'topsleeveElbowLeftCpTop','topsleeveElbowLeft', 0.67));
        $p->addPoint('z.8.TopIn', $p->shiftFractionAlong('topsleeveLeftEdge', 'topsleeveLeftEdge', 'topsleeveElbowLeftCpTop','topsleeveElbowLeft', 0.78));
        $p->clonePoint('topsleeveElbowLeft', 'z.8.BotIn');
        $p->addPoint('z.9.TopIn', $p->shiftFractionTowards('topsleeveElbowLeft', 'topsleeveWristLeft', 0.13));
        $p->addPoint('z.9.BotIn', $p->shiftFractionTowards('topsleeveElbowLeft', 'topsleeveWristLeft', 0.37));
        $p->addPoint('z.10.TopIn', $p->shiftFractionTowards('topsleeveElbowLeft', 'topsleeveWristLeft', 0.48));
        $p->addPoint('z.10.BotIn', $p->shiftFractionTowards('topsleeveElbowLeft', 'topsleeveWristLeft', 0.72));
        $p->addPoint('z.11.TopIn', $p->shiftFractionTowards('topsleeveElbowLeft', 'topsleeveWristLeft', 0.82));
        $p->clonePoint('topsleeveWristLeft', 'z.11.BotIn');
        
        // straight down, right side
        $p->addPoint('z.6.TopOut', $p->shiftFractionAlong('topsleeveRightEdge','topsleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.17));
        $p->addPoint('z.6.BotOut', $p->shiftFractionAlong('topsleeveRightEdge','topsleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.33));
        $p->addPoint('z.7.TopOut', $p->shiftFractionAlong('topsleeveRightEdge','topsleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.52));
        $p->addPoint('z.7.BotOut', $p->shiftFractionAlong('topsleeveRightEdge','topsleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.63));
        $p->addPoint('z.8.TopOut', $p->shiftFractionAlong('topsleeveRightEdge','topsleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.82));
        $p->clonePoint('elbowRight', 'z.8.BotOut');
        $p->addPoint('z.9.TopOut', $p->shiftFractionTowards('elbowRight', 'topsleeveWristRight', 0.17));
        $p->addPoint('z.9.BotOut', $p->shiftFractionTowards('elbowRight', 'topsleeveWristRight', 0.33));
        $p->addPoint('z.10.TopOut', $p->shiftFractionTowards('elbowRight', 'topsleeveWristRight', 0.52));
        $p->addPoint('z.10.BotOut', $p->shiftFractionTowards('elbowRight', 'topsleeveWristRight', 0.68));
        $p->addPoint('z.11.TopOut', $p->shiftFractionTowards('elbowRight', 'topsleeveWristRight', 0.87));
        $p->clonePoint('topsleeveWristRight', 'z.11.BotOut');

        // Simple stripes
        //for($s=6;$s<=11;$s++) $p->newPath("stripe$s", "M z.$s.TopIn L z.$s.TopOut L  z.$s.BotOut L z.$s.BotIn z", ['style' => 'fill: #000;']);

        // Make stripes more organic
        for($s=4;$s<=11;$s++) {
            // Control points for tweaking later
            $p->clonePoint("z.$s.TopIn", "z.$s.TopInCp");
            $p->clonePoint("z.$s.BotIn", "z.$s.BotInCp");
            $p->clonePoint("z.$s.TopOut", "z.$s.TopOutCp");
            $p->clonePoint("z.$s.BotOut", "z.$s.BotOutCp");
            
            // Tweak control points
            if ($s<8) $outAngle = 180;
            else $outAngle = $p->angle('elbowRight','topsleeveWristRight')+90;
            $p->addPoint("z.$s.TopInCp", $p->shift("z.$s.TopInCp", $p->angle('topsleeveElbowLeft','topsleeveWristLeft')-90, 15));
            $p->addPoint("z.$s.BotInCp", $p->shift("z.$s.BotInCp", $p->angle('topsleeveElbowLeft','topsleeveWristLeft')-90, 10));
            $p->addPoint("z.$s.TopOutCp", $p->shift("z.$s.TopOutCp", $outAngle, 25));
            $p->addPoint("z.$s.BotOutCp", $p->shift("z.$s.BotOutCp", $outAngle, 25));

            $p->addPoint("z.$s.TopA", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.3));
            $p->addPoint("z.$s.TopACpIn", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.1));
            $p->addPoint("z.$s.TopB", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.7));
            $p->addPoint("z.$s.TopB", $p->shift("z.$s.TopB", -90, 10));
            $p->addPoint("z.$s.TopBCpIn", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.6));

            $p->addPoint("z.$s.BotA", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.3));
            $p->addPoint("z.$s.BotACpIn", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.2));
            $p->addPoint("z.$s.BotB", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.7));
            $p->addPoint("z.$s.BotB", $p->shift("z.$s.BotB", -90, 10));
            $p->addPoint("z.$s.BotBCpIn", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.6));

            $p->addPoint("z.$s.TopACpIn", $p->rotate("z.$s.TopACpIn", "z.$s.TopA", 10));
            $p->addPoint("z.$s.TopACpOut", $p->rotate("z.$s.TopACpIn", "z.$s.TopA", 180));
            $p->addPoint("z.$s.BotACpIn", $p->rotate("z.$s.BotACpIn", "z.$s.BotA", -15));
            $p->addPoint("z.$s.BotACpOut", $p->rotate("z.$s.BotACpIn", "z.$s.BotA", 180));

            $p->addPoint("z.$s.TopBCpIn", $p->rotate("z.$s.TopBCpIn", "z.$s.TopB", 10));
            $p->addPoint("z.$s.TopBCpOut", $p->rotate("z.$s.TopBCpIn", "z.$s.TopB", 180));
            $p->addPoint("z.$s.BotBCpIn", $p->rotate("z.$s.BotBCpIn", "z.$s.BotB", 15));
            $p->addPoint("z.$s.BotBCpOut", $p->rotate("z.$s.BotBCpIn", "z.$s.BotB", 180));


            $p->newPath("stripe$s", "
                M z.$s.TopIn 
                C z.$s.TopInCp z.$s.TopACpIn z.$s.TopA
                C z.$s.TopACpOut z.$s.TopBCpIn z.$s.TopB
                C z.$s.TopBCpOut z.$s.TopOutCp z.$s.TopOut
                M z.$s.BotOut
                C z.$s.BotOutCp z.$s.BotBCpOut z.$s.BotB
                C z.$s.BotBCpIn z.$s.BotACpOut z.$s.BotA
                C z.$s.BotACpIn z.$s.BotInCp z.$s.BotIn
            ", ['class' => 'fabric hint zebra']);
        }

        // Manually tweak top and bottom stripes
        $p->addPoint('z.4.TopInCp', $p->shift('z.4.TopIn', $p->angle('z.3.BotIn','z.4.TopIn')-90, 5));
        $p->addPoint('z.4.BotInCp', $p->shift('z.4.BotIn', $p->angle('z.4.BotIn','z.5.TopIn')-90, 15));
        $p->addPoint('z.4.TopOutCp', $p->shift('z.4.TopOut', $p->angle('z.3.BotOut','z.4.TopOut')+90, 5));
        $p->addPoint('z.4.BotOutCp', $p->shift('z.4.BotOut', $p->angle('z.4.BotOut','z.5.TopOut')+90, 15));
        $p->addPoint('z.4.TopBCpIn', $p->rotate('z.4.TopBCpIn','z.4.TopB',35));
        $p->addPoint('z.4.TopBCpOut', $p->rotate('z.4.TopBCpIn','z.4.TopB',180));
        $shiftThese = ['z.4.TopACpIn','z.4.TopA','z.4.TopACpOut','z.4.TopBCpIn','z.4.TopB','z.4.TopBCpOut'];
        foreach($shiftThese as $pid) $p->addPoint($pid, $p->shift($pid, -90, 10));

        $p->addPoint('z.3.BotA', $p->shiftFractionTowards('z.3.BotIn','z.3.BotOut', 0.7));
        $p->addPoint('z.3.BotACpIn', $p->shiftFractionTowards('z.3.BotIn','z.3.BotOut', 0.5));
        $p->addPoint('z.3.BotACpOut', $p->shiftFractionTowards('z.3.BotIn','z.3.BotOut', 0.8));
        $p->addPoint('z.3.BotA', $p->shift('z.3.BotA', -90, 10));
        $p->addPoint('z.3.BotACpIn', $p->shift('z.3.BotACpIn', -90, 10));
        $p->addPoint('z.3.BotACpIn', $p->rotate('z.3.BotACpIn','z.3.BotA',-15));
        $p->addPoint('z.3.BotACpOut', $p->rotate('z.3.BotACpIn','z.3.BotA',180));
        $p->addPoint('z.3.BotInCp', $p->shift('z.3.BotIn', $p->angle('z.3.BotIn','sleeveTop')+90, 10));
        $p->addPoint('z.3.BotOutCp', $p->shift('z.3.BotOut', $p->angle('sleeveTop','z.3.BotOut')+90, 10));
        
        $p->newPath('stripe3', '
            M z.3.BotIn 
            C z.3.BotInCp z.3.BotACpIn z.3.BotA
            C z.3.BotACpOut z.3.BotOutCp z.3.BotOut
        ', ['class' => 'fabric hint zebra']);
        
        $p->newPath('stripe11', '
            M z.11.TopIn 
            C z.11.TopInCp z.11.TopACpIn z.11.TopA
            C z.11.TopACpOut z.11.TopBCpIn z.11.TopB
            C z.11.TopBCpOut z.11.TopOutCp z.11.TopOut
        ', ['class' => 'fabric hint zebra']);

    }

    /**
     * Draws zebra stripes on the undersleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function zebraUndersleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeve'];

        if($this->v('stripe5InUndersleeve') !== false) {
            // FIXME: Handle this
        }

        // straight down, left side
        $p->addPoint('z.6.TopIn', $p->shiftFractionAlong('undersleeveLeftEdge', 'undersleeveLeftEdge', 'undersleeveElbowLeftCpTop','undersleeveElbowLeft', 0.13));
        $p->addPoint('z.6.BotIn', $p->shiftFractionAlong('undersleeveLeftEdge', 'undersleeveLeftEdge', 'undersleeveElbowLeftCpTop','undersleeveElbowLeft', 0.37));
        $p->addPoint('z.7.TopIn', $p->shiftFractionAlong('undersleeveLeftEdge', 'undersleeveLeftEdge', 'undersleeveElbowLeftCpTop','undersleeveElbowLeft', 0.48));
        $p->addPoint('z.7.BotIn', $p->shiftFractionAlong('undersleeveLeftEdge', 'undersleeveLeftEdge', 'undersleeveElbowLeftCpTop','undersleeveElbowLeft', 0.67));
        $p->addPoint('z.8.TopIn', $p->shiftFractionAlong('undersleeveLeftEdge', 'undersleeveLeftEdge', 'undersleeveElbowLeftCpTop','undersleeveElbowLeft', 0.78));
        $p->clonePoint('undersleeveElbowLeft', 'z.8.BotIn');
        $p->addPoint('z.9.TopIn', $p->shiftFractionTowards('undersleeveElbowLeft', 'undersleeveWristLeft', 0.13));
        $p->addPoint('z.9.BotIn', $p->shiftFractionTowards('undersleeveElbowLeft', 'undersleeveWristLeft', 0.37));
        $p->addPoint('z.10.TopIn', $p->shiftFractionTowards('undersleeveElbowLeft', 'undersleeveWristLeft', 0.48));
        $p->addPoint('z.10.BotIn', $p->shiftFractionTowards('undersleeveElbowLeft', 'undersleeveWristLeft', 0.72));
        $p->addPoint('z.11.TopIn', $p->shiftFractionTowards('undersleeveElbowLeft', 'undersleeveWristLeft', 0.82));
        $p->clonePoint('undersleeveWristLeft', 'z.11.BotIn');

        // straight down, right side
        $p->addPoint('z.6.TopOut', $p->shiftFractionAlong('undersleeveRightEdge','undersleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.17));
        $p->addPoint('z.6.BotOut', $p->shiftFractionAlong('undersleeveRightEdge','undersleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.33));
        $p->addPoint('z.7.TopOut', $p->shiftFractionAlong('undersleeveRightEdge','undersleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.52));
        $p->addPoint('z.7.BotOut', $p->shiftFractionAlong('undersleeveRightEdge','undersleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.63));
        $p->addPoint('z.8.TopOut', $p->shiftFractionAlong('undersleeveRightEdge','undersleeveRightEdgeCpBottom','elbowRightCpTop','elbowRight', 0.82));
        $p->clonePoint('elbowRight', 'z.8.BotOut');
        $p->addPoint('z.9.TopOut', $p->shiftFractionTowards('elbowRight', 'undersleeveWristRight', 0.17));
        $p->addPoint('z.9.BotOut', $p->shiftFractionTowards('elbowRight', 'undersleeveWristRight', 0.33));
        $p->addPoint('z.10.TopOut', $p->shiftFractionTowards('elbowRight', 'undersleeveWristRight', 0.52));
        $p->addPoint('z.10.BotOut', $p->shiftFractionTowards('elbowRight', 'undersleeveWristRight', 0.68));
        $p->addPoint('z.11.TopOut', $p->shiftFractionTowards('elbowRight', 'undersleeveWristRight', 0.87));
        $p->clonePoint('undersleeveWristRight', 'z.11.BotOut');

        // Simple stripes
        //for($s=6;$s<=11;$s++) $p->newPath("stripe$s", "M z.$s.TopIn L z.$s.TopOut L  z.$s.BotOut L z.$s.BotIn z", ['style' => 'fill: #000;']);

        // Make stripes more organic
        for($s=6;$s<=11;$s++) {
            // Control points for tweaking later
            $p->clonePoint("z.$s.TopIn", "z.$s.TopInCp");
            $p->clonePoint("z.$s.BotIn", "z.$s.BotInCp");
            $p->clonePoint("z.$s.TopOut", "z.$s.TopOutCp");
            $p->clonePoint("z.$s.BotOut", "z.$s.BotOutCp");
            
            // Tweak control points
            if ($s<8) $leftAngle = $p->angle('undersleeveRightEdge','z.7.BotOut')+90;
            else $leftAngle = $p->angle('elbowRight','undersleeveWristRight')+90;
            $p->addPoint("z.$s.TopInCp", $p->shift("z.$s.TopInCp",   $p->angle('undersleeveLeftEdge','undersleeveWristLeft')-90, 15));
            $p->addPoint("z.$s.BotInCp", $p->shift("z.$s.BotInCp",   $p->angle('undersleeveLeftEdge','undersleeveWristLeft')-90, 10));
            $p->addPoint("z.$s.TopOutCp", $p->shift("z.$s.TopOutCp", $leftAngle, 25));
            $p->addPoint("z.$s.BotOutCp", $p->shift("z.$s.BotOutCp", $leftAngle, 25));

            $p->addPoint("z.$s.TopA", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.3));
            $p->addPoint("z.$s.TopACpIn", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.1));
            $p->addPoint("z.$s.TopB", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.7));
            $p->addPoint("z.$s.TopB", $p->shift("z.$s.TopB", -90, 10));
            $p->addPoint("z.$s.TopBCpIn", $p->shiftFractionTowards("z.$s.TopIn", "z.$s.TopOut", 0.6));

            $p->addPoint("z.$s.BotA", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.3));
            $p->addPoint("z.$s.BotACpIn", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.2));
            $p->addPoint("z.$s.BotB", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.7));
            $p->addPoint("z.$s.BotB", $p->shift("z.$s.BotB", -90, 10));
            $p->addPoint("z.$s.BotBCpIn", $p->shiftFractionTowards("z.$s.BotIn", "z.$s.BotOut", 0.6));

            $p->addPoint("z.$s.TopACpIn", $p->rotate("z.$s.TopACpIn", "z.$s.TopA", 10));
            $p->addPoint("z.$s.TopACpOut", $p->rotate("z.$s.TopACpIn", "z.$s.TopA", 180));
            $p->addPoint("z.$s.BotACpIn", $p->rotate("z.$s.BotACpIn", "z.$s.BotA", -15));
            $p->addPoint("z.$s.BotACpOut", $p->rotate("z.$s.BotACpIn", "z.$s.BotA", 180));

            $p->addPoint("z.$s.TopBCpIn", $p->rotate("z.$s.TopBCpIn", "z.$s.TopB", 10));
            $p->addPoint("z.$s.TopBCpOut", $p->rotate("z.$s.TopBCpIn", "z.$s.TopB", 180));
            $p->addPoint("z.$s.BotBCpIn", $p->rotate("z.$s.BotBCpIn", "z.$s.BotB", 15));
            $p->addPoint("z.$s.BotBCpOut", $p->rotate("z.$s.BotBCpIn", "z.$s.BotB", 180));


            $p->newPath("stripe$s", "
                M z.$s.TopIn 
                C z.$s.TopInCp z.$s.TopACpIn z.$s.TopA
                C z.$s.TopACpOut z.$s.TopBCpIn z.$s.TopB
                C z.$s.TopBCpOut z.$s.TopOutCp z.$s.TopOut
                M z.$s.BotOut
                C z.$s.BotOutCp z.$s.BotBCpOut z.$s.BotB
                C z.$s.BotBCpIn z.$s.BotACpOut z.$s.BotA
                C z.$s.BotACpIn z.$s.BotInCp z.$s.BotIn
            ", ['class' => 'fabric hint zebra']);
        }

        // Now that one that's partial
        $p->addPoint("z.5.TopIn", $p->shiftAlong('undersleeveTip', 'undersleeveTipCpBottom', 'undersleeveLeftEdgeCpRight', 'undersleeveLeftEdgeRight', $this->v('backSleeveJointToStartStripe')));
        $p->addPoint("z.5.BotIn", $p->shiftAlong('undersleeveTip', 'undersleeveTipCpBottom', 'undersleeveLeftEdgeCpRight', 'undersleeveLeftEdgeRight', $this->v('backSleeveJointToEndStripe')));
        $top = $this->parts['topsleeve'];
        $p->addPoint("z.5.TopOut", $p->shiftAlong('undersleeveTip','undersleeveTip','undersleeveRightEdgeCpTop','undersleeveRightEdge', $top->distance('backPitchPoint','z.5.TopOut')));  
        $p->addPoint("z.5.BotOut", $p->shiftAlong('undersleeveTip','undersleeveTip','undersleeveRightEdgeCpTop','undersleeveRightEdge', $top->distance('backPitchPoint','z.5.TopOut') + $top->distance('z.5.TopOut','z.5.BotOut')));  
        $p->addPoint('z.5.TopInCp', $p->shift('z.5.TopIn', -10, 10));
        $p->addPoint('z.5.TopOutCp', $p->shift('z.5.TopOut', 190, 10));
        $p->addPoint('z.5.BotInCp', $p->shift('z.5.BotIn', -45, 30));
        $p->addPoint('z.5.BotOutCp', $p->shift('z.5.BotOut', 190, 40));
        $p->newPath("stripe5", "M z.5.TopIn C z.5.TopInCp z.5.TopOutCp z.5.TopOut M z.5.BotOut C z.5.BotOutCp z.5.BotInCp z.5.BotIn", ['class' => 'fabric hint zebra']);

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

        // Notches
        $p->notch([10,'chestCenter','waistCenter', 'waistBackSide']);
        
        // Sleeve notch for top/under sleeve seam. But in what curve should it go?
        $len1 = $p->curveLen(12,19,17,10);
        $len2 = $len1 + $p->curveLen(10,18,15,'slArm');
        $lenx = $this->v('topsleevecapBackLength') - $this->o('sleevecapEase')/2;
        
        if($lenx == $len1) $p->clonePoint(10, 'sleeveJoint');
        elseif($lenx == $len2) $p->clonePoint('slArm', 'sleeveJoint');
        elseif($lenx < $len1) $p->addPoint('sleeveJoint', $p->shiftAlong(12,19,17,10,$lenx));
        elseif($lenx < $len2) $p->addPoint('sleeveJoint', $p->shiftAlong(10,18,15,'slArm',$lenx-$len1));
        else die('oh boy');
        $p->notch(['sleeveJoint']);

        // Grainline
        $p->newPoint('grainlineTop', $p->x(8), $p->y(8)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('hemCenter')-10);
        $p->newGrainline('grainlineBottom','grainlineTop', $this->t('Grainline'));

        // Sleevehead SA foldback notch
        $p->addPoint('foldBack', $p->shiftAlong(12,19,17,10,30));
        $p->notch(['foldBack']);

        // Seam allowance
        if($this->o('sa')) {
            if($this->o('backVent') == 1) {
                $start = '
                M ventFacingBottomLeft 
                L ventFacing-startPoint 
                C ventFacing-cp1--hipsCenter.ventFacingSplit2.ventFacingSplit3.ventFacingBase ventFacing-cp2--hipsCenter.ventFacingSplit2.ventFacingSplit3.ventFacingBase ventFacing-endPoint
                L ventTip 
                C ventSplit7 ventSplit6 waistCenter
                ';
            } else {
                $start = '
                M hemCenter 
                L hipsCenter 
                C hipsCenterCpTop waistCenterCpBottom waistCenter 
                ';
            }
            $p->offsetPathString('sa1', $start.' 
                C waistCenterCpTop chestCenterCpBottom chestCenter 
                C 9 centerBackNeck  centerBackNeck 
                C centerBackNeck 20 8
                L 12
                C 19 17 10
                C 18 15 slArm 
                C slArmCpBottom waistBackSideCpTop waistBackSide 
                C waistBackSideCpBottom hipsBackSideCpTop hipsBackSide 
                L hemBackSide 
            ', $this->o('sa')*-1, 1, ['class' => 'fabric sa']);
            $p->newPoint('hemSaLeft', $p->x('sa1-startPoint'), $p->y('hemEdgeCenter'));
            $p->newPoint('hemSaRight', $p->x('sa1-endPoint'), $p->y('hemEdgeCenter'));
            $p->newPath('sa2', 'M sa1-startPoint L hemSaLeft L hemEdgeCenter L hemEdgeBackSide L hemSaRight L sa1-endPoint', ['class' => 'fabric sa']);
        }

        $p->newPath('waistLine', 'M waistCenter L waistBackSide',['class' => 'help']);

        // Title & logo
        $p->newPoint('titleAnchor', $p->x('slArm')*0.6, $p->y('slArm'));
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '2x '.$this->t('from fabric')."\n".'2x '.$this->t('from lining'));
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', -90, 100));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

        // Notes
        $p->newNote( $p->newId(), 'foldBack', $this->t("Fold back seam allowance\nfrom here to shoulder seam"), 8, 20, 5);
        $p->newNote( $p->newId(), 10, $this->t("Work in sleevecap ease\nfrom this point onwards"), 10, 20, 5);
        $p->newNote( $p->newId(), 'sleeveJoint', $this->t("Topsleeve/Undersleeve joint point"), 8, 20, 5);
        if($this->o('sa')) {
            $p->newNote( $p->newId(), 'hipsBackSideCpTop', $this->t("Standard seam allowance")."\n(".$p->unit($this->o('sa')).')', 8, 20, -3);
            $p->newNote( $p->newId(), 'grainlineBottom', $this->t("Extra hem allowance")."\n(".$p->unit($this->o('sa')*3).')', 11, 20, -23);
        }

        // Text on paths
        $p->newTextOnPath('waistLine', 'M waistCenter L waistBackSide', 'Waistline', false, false);
    }

    /**
     * Finalizes the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Sleeve notch for start sleevecap ease
        $p->notch([10]);

        // Sleeve notch for top/under sleeve seam. But in what curve should it go?
        $len1 = $p->curveLen(12,19,17,10);
        $len2 = $len1 + $p->curveLen(10,18,15,14);
        $len3 = $len2 + $p->curveLen(14,'14CpRight','slArmCpLeft','slArm');
        $lenx = $this->v('topsleevecapFrontLength') - $this->o('sleevecapEase')/2;
        
        if($lenx == $len1) $p->clonePoint(10, 'sleeveJoint');
        elseif($lenx == $len2) $p->clonePoint(14, 'sleeveJoint');
        elseif($lenx == $len3) $p->clonePoint('slArm', 'sleeveJoint');
        elseif($lenx < $len1) $p->addPoint('sleeveJoint', $p->shiftAlong(12,19,17,10,$lenx));
        elseif($lenx < $len2) $p->addPoint('sleeveJoint', $p->shiftAlong(10,18,15,14,$lenx-$len1));
        elseif($lenx < $len3) $p->addPoint('sleeveJoint', $p->shiftAlong(14,'14CpRight','slArmCpLeft','slArm',$lenx-$len2));
        $p->notch(['sleeveJoint']);

        if($this->o('sa')) {
            // Seam allowance
            $p->offsetPathstring('sa1','
                M roundRight
                C roundRightCp roundTopCp roundTop
                L breakPoint
                C breakPointCp cfRealTop cfRealTop 
                L notchPoint
                L collarCorner 
                L shoulderLineRealLeft 
                L 12
                C 19 17 10
                C 18 15 14
                C 14CpRight slArmCpLeft slArm 
                C slArm waistBackSideCpTop waistBackSide 
                C waistBackSideCpBottom hipsBackSideCpTop hipsBackSide 
                L frontSideHem', $this->o('sa')*-1,1, ['class' => 'fabric sa']);
            $p->newPoint('hemRight', $p->x('sa1-endPoint'), $p->y('frontSideHemEdge'));
            $p->newPath('sa2', 'M sa1-startPoint L roundedHem L frontSideHemEdge L hemRight L sa1-endPoint', ['class' => 'fabric sa']);
        }

        $p->newTextOnPath('facing', 'M roundRight L facingTop', 'Facing/Lining boundary, facing side', ['dy' => -3, 'class' => 'fill-fabric'], false);
        $p->newTextOnPath('lining', 'M roundRight L facingTop', 'Facing/Lining boundary, lining side', ['dy' => 6, 'class' => 'fill-lining'], false);
        $p->newPath('waistLine', 'M 3 L waistBackSide', ['class' => 'help']);

        // Text on paths
        $p->newTextOnPath('waistLine', 'M 3 L waistBackSide', $this->t('Waistline'), false, false);
        $p->newTextOnPath('centerFront', 'M cfHem L chestCenterCpTop', $this->t('Center front').' - '.$this->t('Grainline'), false, false);
        $p->newTextOnPath('rollLine', 'M breakPoint L rollLineTop', $this->t('Roll line'), false, false);

        // Title and logo
        $p->addPoint('titleAnchor', $p->shift('frontDartLeftCpTop', 180, 40));
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Lining part').' 2x '.$this->t('from lining')."\n".$this->t('Facing part').' 2x '.$this->t('from fabric'));
        $p->addPoint('logoAnchor', $p->shift('frontDartBottom', -90, 40));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
        
        // Notes
        $p->addPoint('foldBack', $p->shiftAlong('shoulderLineRight',19,17,10,30));
        $p->newNote( $p->newId(), 'foldBack', $this->t("Fold back seam allowance\nfrom here to shoulder seam"), 8, 20, 5);
        $p->newNote( $p->newId(), 10, $this->t("Work in sleevecap ease\nfrom this point onwards"), 10, 20, 5);
        $p->newNote( $p->newId(), 'sleeveJoint', $this->t("Topsleeve/Undersleeve joint point"), 8, 20, 5);
        if($this->o('sa')) {
            $p->newNote( $p->newId(), 'hipsBackSideCpTop', $this->t("Standard seam allowance")."\n(".$p->unit(10).')', 8, 20, -3);
            $p->newNote( $p->newId(), 'frontSideHemEdge', $this->t("Extra hem allowance")."\n(".$p->unit(30).')', 11, 50, 23);
        }

        // Grainline
        $p->addPoint('grainlineTop', $p->shiftFractionTowards('shoulderLineRealLeft','shoulderLineRight', 0.5));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('hemCenter')-10);
        $p->newGrainline('grainlineBottom','grainlineTop', $this->t('Grainline'));

        // Notches
        $p->notch(['foldBack','waistBackSide',3]);

        // Buttons
        $p->newPoint('topButton', $p->x(3), $p->y('breakPoint'));
        $p->newPoint('bottomButton', $p->x('topButton'), $p->y('cutawayPoint'));
        $p->newSnippet('topButton','button-lg','topButton');
        $p->newSnippet('bottomButton','button-lg','bottomButton');

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

        if($this->o('sa')) {
            // Seam allowance
            $p->offsetPathstring('sa1','
                M sideFrontHem
                L hipsSideBack
                C hipsSideBackCpTop waistSideBackCpBottom waistSideBack
                C waistSideBackCpTop slArm slArm
                C slArmCpRight 5CpLeft 5
                C side13 side16 side14
                C sideSlArmCpBottom sideWaistSideBackCpTop sideWaistSideBack
                C sideWaistSideBackCpBottom sideHipsSideBackCpTop sideHipsSideBack
                L sideHemSideBack
            ', $this->o('sa')*-1,1, ['class' => 'fabric sa']);
            $p->newPoint('hemLeft', $p->x('sa1-startPoint'), $p->y('hemEdgeFrontSide'));
            $p->newPoint('hemRight', $p->x('sa1-endPoint'), $p->y('hemEdgeBackSide'));
            $p->newPath('sa2', 'M sa1-startPoint L hemLeft L hemRight L sa1-endPoint', ['class' => 'fabric sa']);
        }
        $p->newPath('waistLine', 'M sideWaistSideBack L waistSideBack', ['class' => 'help']);
        $p->newTextOnPath('waistLine', 'M waistSideBack L sideWaistSideBack', $this->t('Waistline'), false, false);

        // Grainline
        $p->addPoint('grainlineBottom', $p->shiftFractionTowards('sideFrontHem','sideHemSideBack',0.5));
        $p->addPoint('grainlineBottom', $p->shift('grainlineBottom',90,10));
        $p->newPoint('grainlineTop', $p->x('grainlineBottom'), $p->y(5)+10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title and logo
        $p->addPoint('titleAnchor', $p->shiftFractionTowards('grainlineBottom', 'grainlineTop',0.2));
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '2x '.$this->t('from fabric')."\n".' 2x '.$this->t('from lining'));
        $p->addPoint('logoAnchor', $p->shiftFractionTowards('grainlineBottom', 'grainlineTop',0.8));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');

        // Notes
        if($this->o('sa')) {
            $p->newNote( $p->newId(), 'sideWaistSideBackCpTop', $this->t("Standard seam allowance")."\n(".$p->unit($this->o('sa')).')', 9, 10, -5);
            $p->newNote( $p->newId(), 'grainlineBottom', $this->t("Extra hem allowance")."\n(".$p->unit($this->o('sa')*3).')', 11, 30, -23);
        }

        // Notches
        $p->notch(['sideWaistSideBack','waistSideBack']);
    }
    /**
     * Finalizes the topsleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeTopsleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeve'];

        // Sleeve front notch
        $len = $p->curveLen('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint');
        if($len == $this->v('frontSleevecapToNotch') + $this->o('sleevecapEase')/2) $p->clonePoint('frontPitchPoint', 'frontSleeveNotch');
        elseif ($len > $this->v('frontSleevecapToNotch') + $this->o('sleevecapEase')/2) $p->addPoint('frontSleeveNotch', $p->shiftAlong('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint',  $this->v('frontSleevecapToNotch') + $this->o('sleevecapEase')/2));
        else $p->addPoint('frontSleeveNotch', $p->shiftAlong('frontPitchPoint', 'frontPitchPointCpBottom','topsleeveLeftEdge', 'topsleeveLeftEdge', ($this->v('frontSleevecapToNotch') + $this->o('sleevecapEase')/2)-$len));
        $p->notch(['frontSleeveNotch']);

        // Sleeve back notch
        $this->setValue('backSleevecapPithToNotch', false);
        $len = $p->curveLen('sleeveTop','sleeveTopCpRight','backPitchPoint','backPitchPoint');
        if($len == $this->v('backSleevecapToNotch') + $this->o('sleevecapEase')/2) $p->clonePoint('backPitchPoint', 'backSleeveNotch');
        elseif ($len > $this->v('backSleevecapToNotch') + $this->o('sleevecapEase')/2)  $p->addPoint('backSleeveNotch', $p->shiftAlong('sleeveTop','sleeveTopCpRight','backPitchPoint','backPitchPoint',  $this->v('backSleevecapToNotch') + $this->o('sleevecapEase')/2));
        else $this->setValue('backSleevecapPithToNotch', ($this->v('backSleevecapToNotch') + $this->o('sleevecapEase')/2) - $len);
        if($this->v('backSleevecapPithToNotch') === false) $p->notch(['backSleeveNotch']);
        
        if($this->o('sa')) {
            // 4cm extra hem allowance
            $p->offsetPathString('hemsa','M topsleeveWristLeft L topsleeveWristRight',$this->o('sa')*-4,0);
            $p->addPoint('hemSaLeftIn', $p->beamsCross('topsleeveWristLeft','topsleeveElbowLeft', 'hemsa-startPoint', 'hemsa-endPoint'));
            $angleLeft = $p->angle('topsleeveWristLeft', 'hemSaLeftIn') - $p->angle('topsleeveWristLeft', 'topsleeveWristRight');
            $p->addPoint('hemSaLeft', $p->rotate('hemSaLeftIn', 'topsleeveWristLeft', $angleLeft*-2));
            $p->addPoint('hemSaRightIn', $p->beamsCross('ventBottomRight','ventTopRight', 'hemsa-startPoint', 'hemsa-endPoint'));
            $angleRight = $p->angle('ventBottomRight', 'hemSaRightIn') - $p->angle('topsleeveWristLeft', 'topsleeveWristRight');
            $p->addPoint('hemSaRight', $p->rotate('hemSaRightIn', 'ventBottomRight', $angleRight*-2));
            
            // Seam allowance
            $p->offsetPathString('sa1', 'M elbowRight C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint C backPitchPoint sleeveTopCpRight sleeveTop C sleeveTopCpLeft frontPitchPointCpTop frontPitchPoint C frontPitchPointCpBottom topsleeveLeftEdgeCpRight topsleeveLeftEdge C topsleeveLeftEdge topsleeveElbowLeftCpTop topsleeveElbowLeft L topsleeveWristLeft L hemSaLeft L hemSaRight L ventBottomRight L ventTopRight L ventTopLeft L elbowRight z', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->newPath('hemHint', 'M topsleeveWristLeft L hemSaLeft L hemSaRight L ventBottomRight', ['class' => 'hint']);
        }

        // Notes
        $p->newNote( $p->newId(), 'frontSleeveNotch', $this->t("Work in sleevecap ease\nfrom this point onwards"), 4, 20, 5);
        if($p->isPoint('backSleeveNotch')) $p->newNote( $p->newId(), 'backSleeveNotch', $this->t("Work in sleevecap ease\nfrom this point onwards"), 8, 20, 5);
        else $p->newNote( $p->newId(), 'backPitchPoint', $this->t("Work in sleevecap ease\nfrom this point onwards"), 8, 20, 5);
        if($this->o('sa')) {
            $p->newNote( $p->newId(), 'topsleeveRightEdgeCpBottom', $this->t("Standard seam allowance")."\n(".$p->unit($this->o('sa')).')', 8, 20, -3);
            $p->newNote( $p->newId(), 'topsleeveWristLeftHelperBottom', $this->t("Extra hem allowance")."\n(".$p->unit($this->o('sa')*4).')', 12, 40, -20);
        }
        // Title and logo
        $p->addTitle('underarmCenter', 4, $this->t($p->title), '2x '.$this->t('from fabric')."\n".' 2x '.$this->t('from lining'));
        $p->newSnippet('logo', 'logo-sm', 'elbowCenter');

        // Grainline 
        $p->newPoint('grainlineBottom', $p->x('sleeveTop'), $p->y('topsleeveWristLeft'));
        $p->newGrainline('grainlineBottom','sleeveTop', $this->t('Grainline'));
    }

    /**
     * Finalizes the undersleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeUndersleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeve'];

        // Should we notch the sleevecap?
        if($this->v('backSleevecapPithToNotch') !== false) {
            $p->addPoint('backSleeveNotch', $p->shiftAlong('undersleeveTip','undersleeveTipCpBottom','undersleeveLeftEdgeCpRight','undersleeveLeftEdgeRight', $this->v('backSleevecapPithToNotch')));
            $p->notch(['backSleeveNotch']);
        }

        if($this->o('sa')) {
            // 4cm extra hem allowance
            $p->offsetPathString('hemsa','M undersleeveWristLeft L undersleeveWristRight',$this->o('sa')*-4,0);
            $p->addPoint('hemSaLeftIn', $p->beamsCross('undersleeveWristLeft','undersleeveElbowLeft', 'hemsa-startPoint', 'hemsa-endPoint'));
            $p->addPoint('hemSaRightIn', $p->beamsCross('ventBottomRight','ventTopRight', 'hemsa-startPoint', 'hemsa-endPoint'));
            $angleLeft = $p->angle('undersleeveWristLeft', 'hemSaLeftIn') - $p->angle('undersleeveWristLeft', 'undersleeveWristRight');
            $p->addPoint('hemSaLeft', $p->rotate('hemSaLeftIn', 'undersleeveWristLeft', $angleLeft*-2));
            $angleRight = $p->angle('undersleeveWristRight', 'hemSaRightIn') - $p->angle('undersleeveWristRight', 'undersleeveWristLeft');
            $p->addPoint('hemSaRight', $p->rotate('hemSaRightIn', 'undersleeveWristRight', $angleRight*-2));

            // Seam allowance
            $p->offsetPathString('sa1', 'M elbowRight C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip C undersleeveTipCpBottom undersleeveLeftEdgeCpRight undersleeveLeftEdgeRight L undersleeveLeftEdge C undersleeveLeftEdge undersleeveElbowLeftCpTop undersleeveElbowLeft L undersleeveWristLeft L hemSaLeft L hemSaRight L ventBottomRight L ventTopRight L ventTopLeft L elbowRight z', $this->o('sa'),1, ['class' => 'fabric sa']);
            $p->newPath('hemHint', 'M undersleeveWristLeft L hemSaLeft L hemSaRight L ventBottomRight', ['class' => 'hint']);
        } 

        // Grainline
        $p->newPoint('grainlineBottom', $p->x('undersleeveLeftEdgeCpRight'), $p->y('undersleeveWristLeft'));
        $p->newGrainline('grainlineBottom','undersleeveLeftEdgeCpRight', $this->t('Grainline'));
        
        // Title and logo
        $p->addPoint('titleAnchor', $p->shiftFractionTowards('grainlineBottom','undersleeveLeftEdgeCpRight',0.8));
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '2x '.$this->t('from fabric')."\n".' 2x '.$this->t('from lining'));
        $p->addPoint('logoAnchor', $p->shiftFractionTowards('grainlineBottom','undersleeveLeftEdgeCpRight',0.3));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
        
        // Notes
        if($this->o('sa')) {
            $p->newNote( $p->newId(), 'elbowRight', $this->t("Standard seam allowance")."\n(".$p->unit($this->o('sa')).')', 8, 20, -3);
            $p->newNote( $p->newId(), 'grainlineBottom', $this->t("Extra hem allowance")."\n(".$p->unit($this->o('sa')*4).')', 1, 40, -20);
        }
    }

    /**
     * Finalizes the undercollar
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeUndercollar($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['undercollar'];

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa1', 'M bottomLeft L notchPoint L notchTip', $this->o('sa')*-1, 1, ['class' => 'various sa']);
            $p->offsetPathString('sa2', 'M m.bottomLeft L m.notchPoint L m.notchTip', $this->o('sa'), 1, ['class' => 'various sa']);
            $p->newPath('sa3', 'M notchTip L sa1-endPoint M bottomLeft L sa1-startPoint M m.bottomLeft L sa2-startPoint M m.notchTip L sa2-endPoint', ['class' => 'various sa']);
        }

        // Grainline
        $p->newGrainline('collarCbBottom','collarCbTop', $this->t('Grainline'));

        // Title
        $p->addTitle('ucTop', 6, $this->t($p->title), '1x '.$this->t('from fixme'));
    }

    /**
     * Finalizes the collar
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeCollar($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collar'];

        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'outline', $this->o('sa'), 1, ['class' => 'fabric sa']);
    
        // Grainline
        $p->newGrainline('ucTop','collarCbTop', $this->t('Grainline'));
        
        // Title
        $p->addPoint('titleAnchor', $p->shiftFractionTowards('ucTop','collarCbTop', 0.4));
        $p->addTitle('titleAnchor', 7, $this->t($p->title), '1x '.$this->t('from fabric'),'horizontal-small');
    }

    /**
     * Finalizes the collarstand
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeCollarstand($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collarstand'];

        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'outline', $this->o('sa')*-1, 1, ['class' => 'fabric sa']);
    
        // Grainline
        $p->newGrainline('collarCbBottom','ucTop', $this->t('Grainline'));
        
        // Title
        $p->addTitle('collarCbBottom', 7, $this->t($p->title), '1x '.$this->t('from fabric'),'horizontal-small');
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

        // Height on the left
        $xBase = $p->x('chestCenter');
        if($this->o('sa')) $xBase -= $this->o('sa');
        $p->newHeightDimension('chestCenter', 'centerBackNeck', $xBase-15);
        $p->newHeightDimension('chestCenter', 8, $xBase-30);
        $p->newHeightDimension('waistCenter','chestCenter', $xBase-15);
        if($this->o('backVent') == 1) {
            $p->newHeightDimension('ventTip','chestCenter', $xBase-30);
            $p->newHeightDimension('ventFacing-endPoint','chestCenter', $xBase-45);
            $p->newHeightDimension('ventFacingBottomLeft','chestCenter', $xBase-60);
        } else {
            $p->newHeightDimension('hemCenter','chestCenter', $xBase-30);
        }

        // Heights on the right
        $xBase = $p->x('slArm');
        if($this->o('sa')) $xBase += $this->o('sa');
        $p->newHeightDimension('slArm','chestCenter',$xBase+15);
        $p->newHeightDimension('slArm',10,$xBase+30);
        $p->newHeightDimension('slArm',12,$xBase+45);
        $p->newHeightDimension('slArm',8,$xBase+60);
        $p->newHeightDimension('waistBackSide','slArm',$xBase+15);
        $p->newHeightDimension('hemBackSide','slArm',$xBase+30);

        // Widths
        $yBase = $p->y(8);
        if($this->o('sa')) $yBase -= $this->o('sa');
        $p->newWidthDimensionSm('chestCenter','centerBackNeck', $yBase-15);
        $p->newWidthDimension('chestCenter',8, $yBase-30);
        $p->newWidthDimension('chestCenter',10, $yBase-45);
        $p->newWidthDimension('chestCenter',12, $yBase-60);
        $offset = 15;
        if($this->o('sa')) $offset += $this->o('sa');
        $p->newLinearDimension(8,12,$offset*-1);
        $p->newLinearDimension('waistCenter','waistBackSide', -15);
        $hemOffset = 15;
        if($this->o('sa')) $hemOffset += $this->o('sa')*3;
        $p->newLinearDimension('hemCenter','hemBackSide', $hemOffset);
        if($this->o('backVent') == 1) $p->newWidthDimension('ventFacingBottomLeft', 'hemCenter', $p->y('hemCenter')+$hemOffset);
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
        
        // Height on the left
        $xBase = $p->x('breakPoint');
        if($this->o('sa')) $xBase -= $this->o('sa');
        $p->newHeightDimension(3, 'breakPoint',$xBase-15);
        $p->newHeightDimension(3,'cpBottomLeft',$xBase-30);
        $p->newHeightDimension(3,'cpBottomRight',$xBase-45);
        $p->newHeightDimension(3,'cfRealTop',$xBase-60);
        $p->newHeightDimension(3,'collarCorner',$xBase-75);
        $p->newHeightDimension(3,'shoulderLineRealLeft',$xBase-90);
        $p->newHeightDimension('roundRight', 3,$xBase-15);
        
        // Height on the right
        $xBase = $p->x('slArm');
        if($this->o('sa')) $xBase += $this->o('sa');
        $p->newHeightDimension('slArm', 10,$xBase+15);
        $p->newHeightDimension('slArm', 'shoulderLineRight',$xBase+30);
        $p->newHeightDimension('slArm', 'shoulderLineRealLeft',$xBase+45);
        $p->newHeightDimension('waistBackSide','slArm',$xBase+15);
        $p->newHeightDimension('frontSideHem','waistBackSide',$xBase+15);
        
        // Widths
        $yBase = $p->y('shoulderLineRealLeft');
        if($this->o('sa')) $yBase -= $this->o('sa');
        $p->newWidthDimensionSm('cfRealTop',9, $yBase+35);
        $p->newWidthDimensionSm(9,'notchPoint', $yBase+35);
        $p->newWidthDimension(9,'shoulderLineRealLeft', $yBase-15);
        $p->newWidthDimension(9,10, $yBase-30);
        $p->newWidthDimension(9,'shoulderLineRight', $yBase-45);
        $p->newWidthDimension(9,'slArm', $yBase-60);
        $p->newLinearDimension('shoulderLineRealLeft','shoulderLineRight', -20);
        $p->newLinearDimensionSm('shoulderLineRealLeft','facingTop', 15);
        $p->newLinearDimension(3,'waistBackSide',-5);
        $p->newWidthDimensionSm('breakPoint',9, $p->y(3)-5);
        $p->newWidthDimension('cfHem','roundRight', $p->y('frontSideHem')-10);
        $hemOffset = 20;
        if($this->o('sa')) $hemOffset += $this->o('sa')*3;
        $p->newWidthDimension('cfHem','frontSideHem', $p->y('roundRight')+$hemOffset);

        // Pocket and dart
        $p->newWidthDimension('cfHem','fpBottomLeft', $p->y('fpBottomLeft')+10);
        $p->newWidthDimension(9,'frontDartBottom', $p->y('frontDartBottom')+10);
        $p->newHeightDimension('frontDartBottom','frontDartRight', $p->x('frontDartRight')+15);
        $p->newHeightDimension('frontDartRight', 'frontDartTop', $p->x('frontDartRight')+15);
        $p->newWidthDimensionSm('frontDartLeft','frontDartRight', $p->y('frontDartTop')-10);
        $p->newHeightDimensionSm('fpTopRight', 'waistBackSide', $xBase);
        $p->newHeightDimension('fpBottomRight','fpTopRight', $xBase);
        $p->newLinearDimension('cpTopLeft','cpTopRight', -10);
        $p->newLinearDimensionSm('cpBottomRight','cpTopRight', 10);
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
        $p = $this->parts[''];
    }

    /**
     * Adds paperless info for the topsleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessTopsleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts[''];
    }

    /**
     * Adds paperless info for the undersleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessUndersleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts[''];
    }

    /**
     * Adds paperless info for the undercollar
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessUndercollar($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts[''];
    }

    /**
     * Adds paperless info for the collar
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCollar($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts[''];
    }

    /**
     * Adds paperless info for the collarstand
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCollarstand($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts[''];
    }
}
