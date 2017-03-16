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
        /* Ratio of waist between parts */
        $this->setValue('waistRatioFront', 0.28);
        $this->setValue('waistRatioBack', 0.34);
        
        /* Ration of leg between parts */
        $this->setValue('legRatioInset', 0.26);
        $this->setValue('legRatioBack', 0.38);
        $this->setValue('legRatioSide', 1 - ( $this->v('legRatioInset') + $this->v('legRatioBack') ));
        
        /* Set vertical stretch factor to 90% */
        $this->setOption('verticalStretchFactor', 0.9);   
        
        /* Gusset width is 9% of the hips circumference */
        $this->setValue('gussetWidth', $model->m('hipsCircumference') * 0.09 * $this->getOption('horizontalStretchFactor'));

        /* Side length is 70% of half of the cross seam length */
        $this->setValue('sideLength', (($model->m('crossseamLength')/2) * 0.7 + $this->o('legBonus')) * $this->getOption('verticalStretchFactor'));
        
        
        
        $this->setValue('waistRatioSide', (1 - ($this->v('waistRatioFront') + $this->v('waistRatioBack'))) / 2);


        /* Helpers */
        $this->setValue('halfCross', $model->getMeasurement('crossseamLength') /2 * $this->getOption('verticalStretchFactor'));
        $this->setValue('sideWaist', $model->getMeasurement('hipsCircumference') * $this->v('waistRatioSide') * $this->getOption('horizontalStretchFactor'));
        $this->setValue('sideLeg', $model->getMeasurement('upperLegCircumference') * $this->v('legRatioSide') * $this->getOption('horizontalStretchFactor'));
        

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

        $this->draftBlock($model);
        $this->draftBack($model);
        $this->draftSide($model);
        $this->draftFront($model);
        $this->draftInset($model);

        // Don't render the block
        $this->parts['block']->setRender(false);
    }

    /**
     * Drafts the base block
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['block'];

        // Center back
        $p->newPoint('cbHips', 0, 0, 'Hipline @ center back');
        $p->addPoint('cbRise',$p->shift('cbHips',90,$this->o('rise')*$this->o('verticalStretchFactor')));
        $p->addPoint('cbBackRise',$p->shift('cbRise',90,($this->o('backRise')*$model->m('hipsCircumference'))*$this->o('verticalStretchFactor')));

        // Center front
        $p->newPoint('cfHips', $model->m('hipsCircumference')*$this->o('horizontalStretchFactor')/2, $p->y('cbHips'));
        $p->newPoint('cfRise', $p->x('cfHips'), $p->y('cbRise'));

        // Sloped hipline
        $p->newPoint('cfCpHipline', $p->x('cfRise')*0.8, $p->y('cfRise'));
        $p->newPoint('cbCpHipline', $p->x('cfRise')*0.3, $p->y('cbBackRise'));

        // Move elasticWidth down
        $shift = $this->o('elasticWidth')*$this->o('verticalStretchFactor');
        $p->addPoint('cbTop', $p->shift('cbBackRise',-90,$shift));
        $p->addPoint('cbTopCp', $p->shift('cbCpHipline',-90,$shift));
        $p->addPoint('cfTop', $p->shift('cfRise',-90,$shift));
        $p->addPoint('cfTopCp', $p->shift('cfCpHipline',-90,$shift));

        // Divide into back/side/front
        $p->curveCrossesX('cbTop','cbTopCp', 'cfTopCp', 'cfTop', $p->x('cfTop') * $this->v('waistRatioBack'), 'backSplit');
        $p->curveCrossesX('cbTop','cbTopCp', 'cfTopCp', 'cfTop', $p->x('cfTop') * (1 - $this->v('waistRatioFront')), 'frontSplit');

        // Split top curve
        $p->splitCurve('cbTop','cbTopCp', 'cfTopCp', 'cfTop', 'backSplit1', 'backTopCurve');
        $p->splitCurve('backSplit1', 'backTopCurve7', 'backTopCurve6', 'cfTop', 'frontSplit1', 'sideTopCurve');

        // Crossseam
        $p->newPoint(   'cbXseam', 0, $this->v('halfCross') * $this->getOption('verticalStretchFactor'), 'Crossseam point');
        $p->newPoint(   'cfXseam', $p->x('cfTop'), $p->y('cbXseam'));

        // Front shape
        $p->addPoint('gussetTip', $p->shift('cfXseam',180,$this->v('gussetWidth')/2));
        $p->addPoint('gussetTip', $p->rotate('gussetTip','cfXseam',$this->o('bulge')/-3));

        $p->newPoint('frontInset', $p->x('frontSplit1'), $p->y('cfXseam')/2.5 - $this->o('bulge'));
        $p->addPoint('frontInsetCp', $p->shift('frontInset',0,$p->deltaX('frontInset','gussetTip')*0.5 + $this->o('bulge')*1));
        $p->addPoint('gussetTipCp', $p->shift('gussetTip',$p->angle('cfXseam','gussetTip')+90,$p->deltaY('frontInset','gussetTip')*0.5));

        // Back shape
        $p->newPoint('xseamLeg', $this->v('gussetWidth')/2, $p->y('cbXseam') + $this->v('gussetWidth')/2);
        $p->addPoint('xseamLegCp', $p->shift('xseamLeg', 90, $this->v('gussetWidth')/4));
        $p->addPoint('cbXseamCp', $p->shift('cbXseam', 0, $this->v('gussetWidth')/4));
        $p->addPoint('xseamHem', $p->shift('xseamLeg', -90, $this->o('legBonus')));

        // Side shape
        $p->newPoint('sideLength', $p->x('cfTop')/2, $this->v('sideLength')); 
        $p->newPoint('sideLeft', $p->x('backSplit1'), $p->y('sideLength')); 
        $p->newPoint('sideRight', $p->x('frontSplit1'), $p->y('sideLength')); 

        // Leg width back+side
        $target = $model->m('upperLegCircumference') * ($this->v('legRatioBack')+$this->v('legRatioSide')) * $this->o('horizontalStretchFactor');
        // First run for hem slope
        $p->newPoint('sideRealRight', $p->x('xseamLeg') + sqrt(pow($target,2) - pow($p->deltaY('sideLength','xseamHem'),2)), $p->y('sideLength'));
        // Rotate leg inseam according to hem slope
        $angle = $p->angle('xseamHem','sideRealRight');
        $p->addpoint('xseamLegCpRot', $p->rotate('xseamLegCp','xseamLeg',$angle+180));
        $p->addpoint('xseamHemRot', $p->rotate('xseamHem','xseamLeg',$angle+180));
        // Second run for edge of side
        $p->newPoint('sideRealRight', $p->x('xseamHemRot') + sqrt(pow($target,2) - pow($p->deltaY('sideLength','xseamHemRot'),2)), $p->y('sideLength'));
        $p->newLinearDimension('xseamHemRot','sideRealRight');
        
        // Split back/side on leg hem
        $p->addPoint('hemBackSide', $p->shiftTowards('xseamHemRot','sideRealRight', $model->m('upperLegCircumference') * $this->v('legRatioBack') * $this->o('horizontalStretchFactor')));
        $p->newLinearDimension('hemBackSide','sideRealRight');
        $p->newLinearDimension('sideLeft','sideRight');

        // Side bottom width
        // First, rotate right corner a bit to make side more symmetric
        $p->addPoint('sideRightRot', $p->rotate('sideRight','frontSplit1', 5));
        $p->addPoint('sideRealLeft', $p->shift('sideRightRot',180,$model->m('upperLegCircumference') * $this->v('legRatioBack') * $this->o('horizontalStretchFactor')));
        $seamLength = $p->distance('backSplit1', 'hemBackSide');
        while($p->distance('sideRealLeft','backSplit1') < $seamLength) {
            $p->addPoint('sideRealLeft', $p->rotate('sideRealLeft', 'sideRightRot', 0.5));
        }
        // Curve side seam
        $p->addPoint('sideRealLeftCp', $p->shiftTowards('sideRealLeft','sideRightRot',$this->v('gussetWidth')));
        $p->addPoint('sideRealLeftCp', $p->rotate('sideRealLeftCp','sideRealLeft',90));
        // Adjust length
        $p->addPoint('sideLeftCorner', $p->shiftAlong('backSplit1','backSplit1','sideRealLeftCp','sideRealLeft',$seamLength));

        // Curve hem
        $p->addPoint('sideRightCp', $p->shiftTowards('sideRightRot','frontSplit1',$this->v('gussetWidth')/-1.2));
        $p->addPoint('sideRightCp', $p->rotate('sideRightCp','sideRightRot', 90));

        // Open up front to create some space
        $p->newPoint('insetRotCenter', $p->x('cfTop'), $p->y('frontInset'));
        $rotateThese = ['gussetTipCp','gussetTip','cfXseam'];
        foreach($rotateThese as $id) {
            $p->addPoint($id, $p->rotate($id, 'insetRotCenter', $this->o('bulge')*-1));
        }
        $p->newPoint('cfDartTop', $p->x('cfTop'), $p->y('frontInset')+$this->v('gussetWidth')/2);
        $p->addPoint('cfDartTopCp', $p->shiftTowards('cfTop','cfDartTop', $p->distance('cfTop','cfDartTop')*1.3+$this->o('bulge')*2));


        // Inset
        $p->newPoint('insetBottomRight', $p->x('sideRight') + $model->m('upperLegCircumference') * $this->v('legRatioInset') * $this->o('horizontalStretchFactor'), $p->y('sideRight'));
        $shortSeam = ($p->curveLen('cbXseam','cbXseamCp','xseamLegCpRot','xseamLeg') + $p->distance('xseamLeg','xseamHemRot')) -  $this->v('gussetWidth')/2;
        $curveSeam = $p->curveLen('frontInset','frontInsetCp','gussetTipCp','gussetTip'); 
        $p->addPoint('insetCurveEnd', $p->shift('insetBottomRight', 80+$this->o('bulge')/5, $shortSeam));
        $p->addPoint('insetCpTop', $p->shift('frontInset',0,10));
        $p->addPoint('insetCpBottom', $p->shift('insetCurveEnd',$p->angle('insetCurveEnd','insetBottomRight',10)+90, 10));
        $target = $p->curveLen('frontInset','frontInsetCp','gussetTipCp','gussetTip');
        while ($p->curveLen('frontInset','insetCpTop','insetCpBottom','insetCurveEnd') < $target) {
            $p->addPoint('insetCpTop', $p->shiftTowards('frontInset','insetCpTop', $p->distance('frontInset','insetCpTop')+1));
            $p->addPoint('insetCpBottom', $p->shiftTowards('insetCurveEnd','insetCpBottom', $p->distance('insetCurveEnd','insetCpBottom')+1));
        }

        // Measuring
        $p->newCurvedDimension('M frontInset C  frontInsetCp gussetTipCp gussetTip');
        $p->newCurvedDimension('M frontInset C  insetCpTop insetCpBottom insetCurveEnd');

        // Some help paths
        $p->newPath('elasticline','M cbBackRise C cbCpHipline cfCpHipline cfRise', ['class' => 'helpline']);
        $p->newPath('help1', 'M xseamHemRot L xseamLeg C xseamLegCpRot cbXseamCp cbXseam L cbTop M cfTop L cfDartTop L cfXseam L gussetTip C gussetTipCp frontInsetCp frontInset L frontSplit1');
        $p->newPath('help2', 'M backSplit1 L sideLeft L sideRight L frontSplit1 M hemBackSide L backSplit1');
        $p->newPath('help3', 'M sideRight L insetBottomRight L insetCurveEnd');
        $p->newPath('topline','M cbTop C cbTopCp cfTopCp cfTop');

        // Paths of the different parts
        $p->newPath('back', '
            M cbTop L cbXseam
            C cbXseamCp xseamLegCpRot xseamLeg 
            L xseamHemRot
            L hemBackSide
            L backSplit1
            C backTopCurve3 backTopCurve2 cbTop 
            z
            ', ['class' => 'debug']);
        $p->newPath('side', '
            M backSplit1 
            C backSplit1 sideRealLeftCp sideLeftCorner
            C sideLeftCorner sideRightCp sideRightRot
            L frontSplit1
            C sideTopCurve3 sideTopCurve2 backSplit1
            ', ['class' => 'debug']);
        $p->newPath('inset', '
            M frontInset
            C insetCpTop insetCpBottom insetCurveEnd
            L insetBottomRight
            L sideRight
            z
            ', ['class' => 'debug']);
        $p->newPath('front', '
            M frontSplit1
            L frontInset
            C frontInsetCp gussetTipCp gussetTip
            L cfXseam
            C cfXseam cfDartTopCp cfDartTop 
            L cfTop
            C sideTopCurve6 sideTopCurve7 frontSplit1
            z
            ', ['class' => 'debug']);
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
        $this->clonePoints('block','back');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Outline
        $p->newPath('seamline', '
            M cbTop L cbXseam
            C cbXseamCp xseamLegCpRot xseamLeg 
            L xseamHemRot
            L hemBackSide
            L backSplit1
            C backTopCurve3 backTopCurve2 cbTop 
            z
        ', ['class' => 'fabric']);

        // Grid anchor
        $p->clonePoint('cbTop','gridAnchor');

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
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

        $this->clonePoints('block','side');

        // Outline
        $p->newPath('seamline', '
            M backSplit1 
            C backSplit1 sideRealLeftCp sideLeftCorner
            C sideLeftCorner sideRightCp sideRightRot
            L frontSplit1
            C sideTopCurve3 sideTopCurve2 backSplit1
            z
        ', ['class' => 'fabric']);

        // Grid anchor
        $p->clonePoint('backSplit1','gridAnchor');

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
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
        $this->clonePoints('block','front');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Outline
        $p->newPath('seamline', '
            M frontSplit1
            L frontInset
            C frontInsetCp gussetTipCp gussetTip
            L cfXseam
            C cfXseam cfDartTopCp cfDartTop 
            L cfTop
            C sideTopCurve6 sideTopCurve7 frontSplit1
            z
        ', ['class' => 'fabric']);
        
        // Grid anchor
        $p->clonePoint('cfTop','gridAnchor');

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
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
        $this->clonePoints('block','inset');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['inset'];

        // Outline
        $p->newPath('seamline', '
            M frontInset
            C insetCpTop insetCpBottom insetCurveEnd
            L insetBottomRight
            L sideRight
            z
        ', ['class' => 'fabric']);

        // Grid anchor
        $p->clonePoint('sideRight','gridAnchor');

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
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
