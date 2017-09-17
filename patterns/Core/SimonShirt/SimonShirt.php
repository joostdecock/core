<?php
/** Freesewing\Patterns\Core\SimonShirt class */
namespace Freesewing\Patterns\Core;

use Freesewing\Part;
use Freesewing\Utils;

/**
 * The Simon Shirt  pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SimonShirt extends BrianBodyBlock
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
        // Depth of the armhole
        $this->setValue('armholeDepth', $model->m('shoulderSlope') / 2 + $model->m('bicepsCircumference') * $this->o('armholeDepthFactor'));

        // Heigth of the sleevecap
        $this->setValue('sleevecapHeight', $model->m('bicepsCircumference') * $this->o('sleevecapHeightFactor'));
         
        // Collar widht and depth
        $this->setValue('collarWidth', ($model->getMeasurement('neckCircumference') / self::PI) / 2 + 5);
        $this->setValue('collarDepth', ($model->getMeasurement('neckCircumference') + $this->getOption('collarEase')) / 5 - 8);

        // Cut front armhole a bit deeper
        $this->setValue('frontArmholeExtra', 5);

        /**
         *  Number of cuff pleats depends on the cuffDrape options
         */
        if($this->o('cuffDrape') <= 30) $this->setValue('cuffPleats', 1);
        else $this->setValue('cuffPleats', 2);

        /**
         * Total garment length
         */
        $this->setValue('garmentLength', $this->o('lengthBonus') + $model->m('centerBackNeckToWaist') + $model->m('naturalWaistToHip'));
        
        /**
         *  Reduction in the waist and hips
         *
         *  We do not accomodate for a waist (or hip, but that seems even more rare) 
         *  measurement that is larger than the chest measurement.
         *  This means this pattern won't work for people with that shape.
         *
         *  A workaround that doesn't require pattern alterations would be to
         *  increase the chest ease to accomodate for the larger waist.
         *  You can just extend this pattern and handle that edge case in this initialize() method
         */
        $waistReduction = ( $model->m('chestCircumference') + $this->o('chestEase') ) - ( $model->m('naturalWaist') + $this->o('waistEase') );
        $hipsReduction = ( $model->m('chestCircumference') + $this->o('chestEase') ) - ( $model->m('hipsCircumference') + $this->o('hipsEase') );
        if($waistReduction < 0) $this->setValue('waistReduction', 0);
        else $this->setValue('waistReduction', $waistReduction);
        if($hipsReduction < 0) $this->setValue('hipsReduction', 0);
        else $this->setValue('hipsReduction', $hipsReduction);

        // Tweak factors
        // Front tweaking
        $this->setValue('frontCollarTweakFactor', 1); 
        $this->setValue('frontCollarTweakRun', 0); 
        // Sleave tweaking
        $this->setValue('sleeveTweakFactor', 1); 
        $this->setValue('sleeveTweakRun', 0); 
        // CollarStand tweaking
        $this->setValue('collarStandTweakFactor', 1); 
        $this->setValue('collarStandTweakRun', 0); 
        // Collar tweaking
        $this->setValue('collarTweakFactor', 1); 
        $this->setValue('collarTweakRun', 0); 
    
        // Calling parent pattern initialize
        parent::initialize($model);
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
        
        $this->finalizeFrontRight($model);
        $this->finalizeFrontLeft($model);

        // Front plackets, if needed
        if($this->o('buttonPlacketType') == 2) $this->finalizeButtonPlacket($model);
        if($this->o('buttonholePlacketType') == 2) $this->finalizeButtonholePlacket($model);

        // Yoke and back
        $this->finalizeYoke($model);
        $this->finalizeBack($model);

        // Sleeve
        $this->finalizeSleeve($model);
        $this->finalizeSleevePlacketUnderlap($model);
        $this->finalizeSleevePlacketOverlap($model);
        if($this->o('cuffStyle') > 3) $this->finalizeFrenchCuff($model);
        else $this->finalizeBarrelCuff($model);
        
        // Collar
        $this->finalizeCollarStand($model);
        $this->finalizeCollar($model);
        $this->finalizeUndercollar($model);

        if ($this->isPaperless) {
            // Add paperless info to all parts
            $this->paperlessFrontRight($model);
            $this->paperlessFrontLeft($model);
            if($this->o('buttonPlacketType') == 2) $this->paperlessButtonPlacket($model);
            if($this->o('buttonholePlacketType') == 2) $this->paperlessButtonholePlacket($model);
            $this->paperlessYoke($model);
            $this->paperlessBack($model);
            $this->paperlessSleeve($model);
            $this->paperlessCollarStand($model);
            $this->paperlessCollar($model);
            $this->paperlessUndercollar($model);
            $this->paperlessSleevePlacketUnderlap($model);
            $this->paperlessSleevePlacketOverlap($model);
            if($this->o('cuffStyle') > 3) $this->paperlessFrenchCuff($model);
            else $this->paperlessBarrelCuff($model);
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

        // Blocks from parent pattern
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        // note that the draftSleeveBlock() method is called in draftSleeve()

        // We need the yoke before we can fit the collar
        $this->draftYoke($model);

        // Tweak the collar opening until it fits around the model's neck
        do {
            $this->draftFrontRight($model);
        } while (abs($this->collarOpeningDelta($model)) > 0.5);
        $this->msg('After '.$this->v('frontCollarTweakRun').' attemps, collar opening is '.round($this->collarOpeningDelta($model),1).'mm off.');

        // Draft frontLeft based on frontRight
        $this->draftFrontLeft($model);

        // Front plackets, if needed
        if($this->o('buttonPlacketType') == 2) $this->draftButtonPlacket($model);
        if($this->o('buttonholePlacketType') == 2) $this->draftButtonholePlacket($model);

        // We need the back before we can fit the sleeve
        $this->draftBack($model);

        // Tweak the sleeve until it fits in our armhole
        do {
            $this->draftSleeve($model);
        } while (abs($this->armholeDelta($model)) > 1);
        $this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta($model),1).'mm off.');

        // Draft remaining sleeve parts
        $this->draftSleevePlacketUnderlap($model);
        $this->draftSleevePlacketOverlap($model);
        if($this->o('cuffStyle') > 3) $this->draftFrenchCuff($model);
        else $this->draftBarrelCuff($model);

        // Tweak the collar stand until it fits around the model's neck
        do {
            $this->draftCollarStand($model);
        } while (abs($this->collarStandDelta($model)) > 0.5);
        $this->msg('After '.$this->v('collarStandTweakRun').' attemps, the collar stand is '.round($this->collarStandDelta($model),1).'mm off.');

        // Tweak the collar until it's just right
        do {
            $this->draftCollar($model);
        } while (abs($this->collarDelta($model)) > 0.5);
        $this->msg('After '.$this->v('collarTweakRun').' attemps, the collar is '.round($this->collarDelta($model),1).'mm off.');

        // Draft undercollar
        $this->draftUndercollar($model);

        // Don't render blocks from parent pattern
        $this->parts['backBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['sleeveBlock']->setRender(false);

    }

    /**
     * Calculates the difference between the armhole and sleevehead length
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return float The difference between the armhole and sleevehead
     */
    private function armholeDelta($model) 
    {
        $this->setValue('armholeLength', $this->v('frontArmholeLength') + $this->v('yokeArmholeLength') + $this->v('backArmholeLength'));
        return ($this->v('armholeLength') + $this->o('sleevecapEase')) - $this->v('sleeveheadLength');
    }

    /**
     * Calculates the difference between collar+ease and the collar opening
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return float The difference between the collar+ease and the collar opening
     */
    private function collarOpeningDelta($model) 
    {
        $this->setValue('collarOpeningLength', $this->v('frontCollarOpeningLength') + $this->v('yokeCollarOpeningLength'));
        return $this->v('collarOpeningLength') - ( $model->m('neckCircumference') + $this->o('collarEase') );
    }

    /**
     * Calculates the difference between collar+ease and the collar stand
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return float The difference between the collar+ease and the collar stand
     */
    private function collarStandDelta($model) 
    {
        return $this->v('collarStandLength') - ( $model->m('neckCircumference') + $this->o('collarEase') );
    }

    /**
     * Calculates the difference between collar+ease-gap and the collar
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return float The difference between the collar+ease-gap and the collar
     */
    private function collarDelta($model) 
    {
        return $this->v('collarLength') - ( $model->m('neckCircumference') + $this->o('collarEase') - $this->o('collarGap'));
    }

    /**
     * Drafts the front right
     *
     * I'm using a draft[part name] scheme here but
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
     * 
     * Something that is particular about this method, and a few others in this pattern
     * is that it gets called more than once.
     * That is because it has parts that can be drafted theorethically, but may need some
     * tweaking to make sure things match in the real world.
     *
     * For example, the collar opening should be equal to neckCircumference = collarEase
     * By default, it will (hopefully) be close, but not perfect.
     * So, we call this method again until we get it right. Obvisouly doing the same thing
     * over and over again won't help us, we increase or decrease a 'tweak factor' that is 
     * taken into account in those points that influence the collar opening.
     * As such, we keep tweaking things until we are happy.
     *
     * You'll see similar tweaking in the sleeve and collar parts
     * 
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontRight($model)
    {
        /** @var Part $p */
        $p = $this->parts['frontRight'];
        
        // Is this the first time we're calling draftFrontRight() ?
        if($this->v('frontCollarTweakRun') == 0) {
            // Yes it is, this will be the initial draft
            // Cloning points from the frontBlock
            $this->clonePoints('frontBlock', 'frontRight');
            // Front block is drafted as the left half, so every point needs to be mirrored
            foreach($p->points as $i => $point) $p->addPoint($i, $p->flipX($i,0));
        } else {
            // No, this will be a tweaked draft. So let's tweak
            if($this->collarOpeningDelta($model) > 0) {
                //  Collar opening is too big. Decrease tweak factor 
                $this->setValue('frontCollarTweakFactor', $this->v('frontCollarTweakFactor')*0.99);
            } else {
                //  Collar opening is too small. Increase tweak factor 
                $this->setValue('frontCollarTweakFactor', $this->v('frontCollarTweakFactor')*1.01);
            }
            // Tweak points that determine the collar
            $p->clonePoint(9,$p->newId('collarTweakRun')); // Storing the original just because
            $p->newPoint(9, $p->x(9), $p->y(9)*$this->v('frontCollarTweakFactor')); // Overwriting point 9 with tweaked 9
            $p->newPoint(21, $p->x(21), $p->y(9)); // Updating 21 with tweaked Y value
            // Include debug message
            $this->dbg('Collar tweak run '.$this->v('frontCollarTweakRun').'. Collar opening is '.$this->collarOpeningDelta($model).'mm off');
        }

        // Keep track of tweak runs because why not
        $this->setValue('frontCollarTweakRun', $this->v('frontCollarTweakRun')+1);
        

        // Button placket
        $width = $this->o('buttonPlacketWidth')/2;
        if($this->o('buttonPlacketStyle') == 1) $width2 = $width*3; // Classic placket
        else $width2 = $width*5;
        $p->addPoint( 2040, $p->shift(4,180,$width));
        $p->curveCrossesX(8,20,21,9,$p->x(2040),204); // Creates point 2041
        $p->addPoint( 2042, $p->shift(9,0,$width));
        $p->addPoint( 2043, $p->shift(9,0,$width2));
        $p->addPoint( 2044, $p->shift(4,0,$width2));
        $p->addPoint( 2045, $p->shift(4,0,$width));
        if($this->o('buttonPlacketStyle') == 2) $p->addPoint( 2046, $p->shift(4,0,$width*3));
        // Need to split the neckcurve for a seperate placket or folding over
        $p->splitCurve(8,20,21,9,2041,'splitNeckCurve');
        $p->clonePoint('splitNeckCurve2', 2051);
        $p->clonePoint('splitNeckCurve3', 2052);
        $p->clonePoint(2041, 2053);
        $p->clonePoint('splitNeckCurve6', 2151);
        $p->clonePoint('splitNeckCurve7', 2152);
        $p->clonePoint(2041, 2153);
        // Fold over (mirror)
        $p->addPoint( -2051 , $p->flipX(2151,$p->x(2042)));
        $p->addPoint( -2052 , $p->flipX(2152,$p->x(2042)));
        $p->addPoint( -2053 , $p->flipX(2153,$p->x(2042)));
        $p->addPoint( -2017 , $p->flipX(9,$p->x(2042)));
        $p->addPoint( -2054,  $p->flipX(-2051,$p->x(-2053)));
        $p->addPoint( -2055,  $p->flipX(-2052,$p->x(-2053)));
        $p->addPoint( -2056,  $p->flipX(-2017,$p->x(-2053)));

        // Button placement
        $buttoningLength = $this->v('garmentLength') - $this->o('lengthBonus') - $this->o('buttonfreeLength') - $p->y(9);
        $buttonSpacing = $buttoningLength / ($this->o('buttons'));
        // First button
        $p->newPoint(3000 , $p->x(4), $buttoningLength+$p->y(9), 'button start');
        // Next buttons
        for($i=1;$i<$this->o('buttons');$i++) {
          $pid = 3000+$i;
          $p->addPoint($pid, $p->shift(3000, 90, $buttonSpacing * $i), 'Button');
        }
        // Extra top button
        if($this->o('extraTopButton')) {
          $extrapid = $pid +1;
          $p->addPoint($extrapid, $p->shift($pid,90,$buttonSpacing/2), 'Extra button');
        }
        // Shaping of side seam
        $p->addPoint(8000, $p->shift(4,90,$this->o('lengthBonus')), 'Hips height');        
        $p->newPoint(8001, $p->x(6), $p->y(8000), 'Hips height');        
        if ($this->v('waistReduction') <= 100) { 
            // Only shape side seams if we're reducing less than 10cm
            $in = $this->v('waistReduction')/4;
            $hin = ($this->v('hipsReduction'))/4;
        } else { 
            // Also add back darts if we're reducing 10cm or more
            $in = ($this->v('waistReduction')*0.6)/4;
            $hin = ($this->v('hipsReduction')*0.6)/4;
        }
        $p->addPoint( 6001, $p->shift(5,-90,$p->deltaY(5,3)*0.2));
        $p->newPoint( 6011, $p->x(5)+$in,$p->y(3)-$p->deltaY(5,3)/2);
        $p->newPoint( 6021, $p->x(5)+$in,$p->y(3));
        $p->newPoint( 6031, $p->x(5)+$in,$p->y(3)+$p->deltaY(3,8000)/2);
        $p->addPoint( 8002 , $p->shift(8001,90,$p->deltaY(6031,8000)/4));
        $p->newPoint( 8003 , $p->x(4),$p->y(8000));
        $p->addPoint( 5001 , $p->shift(5,-90,$p->deltaY(5,6011)/4));
        
        // Hem shape
        if($p->isPoint(2044)) $p->clonePoint(2044,6660);
        else $p->clonePoint(4,6660);
        $p->newPoint( 6663 , $p->x(8001)-$this->o('hipFlare')/4,$p->y(8001)+$this->o('lengthBonus')-$this->o('hemCurve')); // HERE
        $p->addPoint( 6662 , $p->shift(6663,90,$p->deltaY(8000,6663)*0.3));
        $p->addPoint( 6661 , $p->shift(8001,-90,$p->deltaY(8000,6663)*0.3));
        
        switch($this->o('hemStyle')) {
            case 1: // Straight hem
                $p->clonePoint(6663,6664);
                $p->newPoint(6665, $p->x(6663), $p->y(6663)+$this->o('hemCurve'));
                $p->clonePoint(6665,6666);
                $p->clonePoint(6666,6667);
                $p->addPoint(6668, $p->shift(6666,180,$p->deltaX(6660,6666)*0.1));
                $p->clonePoint(6668,6669);
                $hemLine = 'M 6665 L ';
                break;
            case 2: // Baseball hem
                $p->addPoint(6664, $p->shift(6663,180,$p->deltaX(6660,6663)*0.3));
                $p->addPoint(6665, $p->shift(6660,0,$p->deltaX(6660,6663)*0.7));
                $p->addPoint(6666, $p->shift(6660,0,$p->deltaX(6660,6663)*0.2));
                $p->clonePoint(6666,6667);
                $p->addPoint(6668, $p->shift(6666,180,$p->deltaX(6660,6666)*0.1));
                $p->clonePoint(6668,6669);
                $hemLine = 'M 6663 C 6664 6665 6666 L ';
                break;
            case 3: // Slashed hem
                $p->newPoint(6664, $p->x(6663), $p->y(6663)+$this->o('hemCurve'));
                $p->newPoint(6665, $p->x(6663), $p->y(6663)+$this->o('hemCurve'));
                $p->addPoint(6666, $p->shift(6664,180,$p->deltaX(6660,6663)*0.3));
                $p->clonePoint(6666,6667);
                $p->addPoint(6668, $p->shift(6666,180,$p->deltaX(6660,6666)*0.1));
                $p->clonePoint(6668,6669);
                $hemLine = 'M 6663 C 6664 6665 6666 L ';
                break;
        }
        
        // Smoothing out curve (overwriting points)
        $p->curveCrossesY(8001,8002,6031,6021,$p->y(8002),'curveSmooth');
        $p->clonePoint('curveSmooth1',8002);
        $p->addPoint(6661, $p->rotate(6661,8001,$p->angle(8001,8002)+90));

        // Construct paths
        $seamline = 'M 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 ';
        if($this->o('buttonPlacketType') == 2) {
            // Separate button placket
            $seamline .= 'C 2051 2052 2041 L 2040 '; 
            $this->setValue('frontRightSaBase', $seamline);
            $hemStart = 2040;
        } else { 
            // Cut-on button placket
            $seamline .= 'C 20 21 9 ';
            $hemStart = 2044; 
            $p->newPath('buttonPlacketHelp1','M 4 L 9', ['class' => 'help fabric']);
            $p->newPath('buttonPlacketFold1','M 2042 L 2045', ['class' => 'hint fabric']);
            switch($this->o('buttonPlacketStyle')) {
                case 1: 
                    // Classic style placket
                    $seamline .= 'L -2017 C -2051 -2052 -2053 L 2043 L 2044 ';
                    $this->setValue('frontRightSaBase', $seamline);
                    $plackethelp = 'M 2153 2040';
                    $p->newPath('buttonPlacketHelp2', 'M 2153 L 2040', ['class' => 'hint fabric']);
                    break;
                case 2: 
                    // Seamless or French style placket
                    $seamline .= 'L -2017 C -2051 -2052 -2053 C -2055 -2054 -2056 L 2043 ';
                    $this->setValue('frontRightSaBase', $seamline);
                    $seamline .= 'L 2044 '; 
                    $p->newPath('buttonPlacketFold2', 'M -2053 L 2046', ['class' => 'hint fabric']);
                    break;
            }
        }
        $this->setValue('frontRightHemBase', $hemLine.$hemStart);
        if($p->y(6664) > $p->y(8001)) $seamline .= 'L 6669 C 6668 6667 6666 C 6665 6664 6663 C 6662 6661 8001 C 8002 6031 6021 C 6011 5001 5 ';
        else $seamline .= 'L 6669 C 6668 6667 6666 L 8001 C 8002 6031 6021 C 6011 5001 5 ';

        // Add paths to part
        
        // Helplines
        $p->newPoint('acrossBackHeight', $p->x(9), $p->y(10), 'Across back height at button line');
        $p->newPath('acrossBackLine', 'M acrossBackHeight L 10', ['class' => 'help fabric']);
        $p->newPath('chestLine', 'M 5 L 2', ['class' => 'help fabric']);
        $p->newPath('waistLine', 'M 6021 L 3', ['class' => 'help fabric']);
        $p->newPath('hipsLine', 'M 8001 L 8003', ['class' => 'help fabric']);
        
        // Seamline path 
        $p->newPath('seamline',$seamline, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);

        // Store armhole curve length
        $this->setValue('frontArmholeLength', $p->curveLen(5,13,16,14) + $p->curveLen(14,15,18,10) + $p->curveLen(10,17,19,12));

        // Store collar opening length
        $this->setValue('frontCollarOpeningLength', $p->curveLen(8,20,21,9)*2);
        
        // Store flat felled seam path
        if($this->o('hemStyle') == 1) {
            if($p->Y(6663) > $p->y(8001)) $this->setValue('frontRightSideBase', 'M 5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663 L 6667');
            else $this->setValue('frontRightSideBase', 'M 5 C 5001 6011 6021 C 6031 8002 8001 L 6667');
        } else $this->setValue('frontRightSideBase','M 8001 M 5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663');
    }

    /**
     * Drafts the front left
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontLeft($model)
    {
        $this->clonePoints('frontRight', 'frontLeft');
        
        /** @var Part $p */
        $p = $this->parts['frontLeft'];

        // Since we're cloning frontLeft, every point needs to be mirrored
        foreach($p->points as $i => $point) $p->addPoint($i, $p->flipX($i,0));
        
        $width = $this->o('buttonholePlacketWidth');
        switch($this->o('buttonholePlacketStyle')) {
            case 1: // Classic placket
                $edge = $this->o('buttonholePlacketFoldWidth');
                $p->addPoint(4000, $p->shift(4,180,$edge*2), 'New center front');
                $p->addPoint(4001, $p->shift(4000,0,$width/2), 'Fold here');
                $p->addPoint(4002, $p->shift(4000,0,$width/2+$edge), 'Stitches');
                $p->addPoint(4003, $p->shift(4000,0,$width/2-$edge), 'Stitches');
                $p->addPoint(4004, $p->shift(4000,180,$width/2), 'Fold here');
                $p->addPoint(4005, $p->shift(4000,180,$width/2+$edge), 'Stitches');
                $p->addPoint(4006, $p->shift(4000,180,$width/2-$edge), 'Stitches');
                $p->addPoint(4007, $p->shift(4004,180,$width), 'Edge');

                for($i=0;$i<8;$i++){
                    $pid = 4100+$i;
                    $oid = 4000+$i;
                    $p->newPoint($pid,$p->x($oid),$p->y(9));
                }
                $p->curveCrossesX(8,20,21,9,$p->x(4102),'.4108'); // Creates helper point .41081
                $p->clonePoint('.41081', 4108); // Store .41081 in 4108
                $p->newPoint(4008, $p->x(4108),$p->y(4001));
                
                // Need to split the neckcurve for a seperate placket or folding over
                $p->splitCurve(8,20,21,9,4108,'splitNeckCurve');

                $p->clonepoint('splitNeckCurve7', 41081);
                $p->clonepoint('splitNeckCurve6', 41082);
                $p->clonepoint('splitNeckCurve2', 41091);
                $p->clonepoint('splitNeckCurve3', 41092);
                break;
            case 2: // Seamless
                $edge = $this->o('buttonholePlacketFoldWidth');
                $p->clonePoint(4,4000); // New center front
                $p->addPoint(4001, $p->shift(4000,180,$width/2), 'Fold here');
                $p->addPoint(4002, $p->shift(4000,180,$width*1.5), 'Fold again');
                $p->addPoint(4007, $p->shift(4000,180,$width*2.5), 'Edge');
                $p->newPoint(4100, $p->x(4000), $p->y(9), 'New center front');
                $p->newPoint(4101, $p->x(4001), $p->y(9), 'Fold here');
                $p->newPoint(4102, $p->x(4002), $p->y(9), 'Fold again');
                $p->newPoint(4107, $p->x(4007), $p->y(9), 'Edge');
                
                $p->addPoint('curveSplitHelper', $p->flipX(4101,$p->x(4100)));
                $p->curveCrossesX(8,20,21,9,$p->x('curveSplitHelper'),'.4108'); // Creates helper point .41081
                $p->clonePoint('.41081', 4108); // Store .41081 in 4108
                $p->newPoint(4008, $p->x(4108),$p->y(4001));
                
                // Need to split the neckcurve for a seperate placket or folding over
                $p->splitCurve(8,20,21,9,4108,'splitNeckCurve');
               
                $p->clonepoint('splitNeckCurve7', 41081);
                $p->clonepoint('splitNeckCurve6', 41082);
                $p->clonepoint('splitNeckCurve2', 41091);
                $p->clonepoint('splitNeckCurve3', 41092);
                $p->addPoint(41083, $p->flipX(4108,$p->x(4101)));
                $p->addPoint(41084, $p->flipX(41081,$p->x(4101)));
                $p->addPoint(41085, $p->flipX(41082,$p->x(4101)));
                $p->addPoint(41086, $p->flipX(4100,$p->x(4101)));
                $p->addPoint(41087, $p->flipX(41084,$p->x(4102)));
                $p->addPoint(41088, $p->flipX(41085,$p->x(4102)));
                $p->addPoint(41089, $p->flipX(41086,$p->x(4102)));
                break;
        }

        // First buttonhole
        $p->newPoint(3000 , $p->x(4100), $p->y(3000), 'button start');
        // Next buttonholes
        for($i=1;$i<$this->o('buttons');$i++) {
            $pid = 3000+$i;
            $p->newPoint($pid, $p->x(4100), $p->y($pid), 'Button');
        }
        // Extra top buttonhole
        if($this->o('extraTopButton')) {
            $extrapid = $pid +1;
            $p->newPoint($extrapid, $p->x(4100), $p->y($extrapid), 'Extra button');
        }

        // Construct paths

        $seamline = 'M 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 '; 
        
        if($this->o('buttonholePlacketType')==2) {
            // Sewn-on
            $seamline .= 'C 41091 41092 4108 L 4008 '; 
            $this->setValue('frontLeftSaBase', $seamline);
        } else {
            // Cut-on
            $seamline .= 'C 20 21 9 '; 
            if($this->o('buttonholePlacketStyle')==1) $seamline .= 'L 4107 '; // Classic style buttonhole placket
            else $seamline .= 'L 41086 C 41084 41085 41083 C 41088 41087 41089 L 4107 '; // Seamless/French style buttonhole placket
            $this->setValue('frontLeftSaBase', $seamline);
            $seamline .= 'L 4007 ';
        }

        if($this->o('buttonholePlacketType')==2 && $this->o('buttonholePlacketStyle')==2) { 
            if($this->o('hemStyle')) $seamline .= 'L 6669 C 6668 6667 6666 '; 
        } else {
            $seamline .= 'L 6669 C 6668 6667 6666 ';
        }
        if($this->o('buttonholePlacketType')==1) { // Cut-on
            switch($this->o('buttonholePlacketStyle')) {
                case 1: // Classic placket
                    $placketHelpLines = 'M 4100 L 4000';
                    $placketFoldLines = 'M 4104 L 4004 M 4101 L 4001 M 4105 L 4005 M 4106 L 4006 M 4103 L 4003 M 4102 L 4002';
                break;
                case 2: // Seamless
                    $placketHelpLines = 'M 4100 L 4000';
                    $placketFoldLines = 'M 4101 L 4001 M 4102 L 4002'; 
                break;
            }
            $p->newPath('placketHelpLines', $placketHelpLines, ['class' => 'help fabric']);
            $p->newPath('placketFoldLines', $placketFoldLines, ['class' => 'hint fabric']);
        }
        if($p->y(6663) > $p->y(8001)) $seamline .= ' C 6665 6664 6663 C 6662 6661 8001 C 8002 6031 6021 C 6011 5001 5';
        else $seamline .= ' L 8001 C 8002 6031 6021 C 6011 5001 5';

        // Add paths to part
        
        // Helplines
        $p->newPath('acrossBackLine', 'M acrossBackHeight L 10', ['class' => 'help fabric']);
        $p->newPath('chestLine', 'M 5 L 2', ['class' => 'help fabric']);
        $p->newPath('waistLine', 'M 6021 L 3', ['class' => 'help fabric']);
        $p->newPath('hipsLine', 'M 8001 L 8003', ['class' => 'help fabric']);
        
        // Seamline path 
        $p->newPath('seamline',$seamline, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        
        // Store flat felled seam path
        if($this->o('hemStyle') == 1) {
            if($p->y(6663) > $p->y(8001)) $this->setValue('frontLeftSideBase', 'M 5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663 L 6667');
            else $this->setValue('frontLeftSideBase', 'M 5 C 5001 6011 6021 C 6031 8002 8001 L 6667');
        } else $this->setValue('frontLeftSideBase', 'M 5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663');
        
        // Store hem path
        if($this->o('hemStyle') == 1) {
            if($this->o('buttonholePlacketType') == 1) $this->setValue('frontLeftHemBase', 'M 6666 L 4007');
            else $this->setValue('frontLeftHemBase', 'M 6666 L 4008');
        } else {
            if($this->o('buttonholePlacketType') == 1) $this->setValue('frontLeftHemBase', 'M 6663 C 6664 6665 6666 L 4007');
            else $this->setValue('frontLeftHemBase', 'M 6663 C 6664 6665 6666 L 4008');
        }
    }

    /**
     * Drafts the button placket
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftButtonPlacket($model)
    {
        $this->clonePoints('frontRight', 'buttonPlacket');
        
        /** @var Part $p */
        $p = $this->parts['buttonPlacket'];
        
        // Paths
        
        $foldline = 'M 2042 L 2045';
        $helpline = 'M 4 L 9';

        $outline = 'M 2044 L 2040 L 2153 C 2152 2151 9 L -2017 C -2051 -2052 -2053 ';
        if($this->o('buttonPlacketStyle') == 2) {
            $outline .= 'C -2055 -2054 -2056 ';
            $foldline .= ' M -2053 L 2046';
        }
        $outline .= 'L 2043 ';
        $this->setValue('buttonPlacketSeamlessSaBase', $outline);
        $outline .= 'z';

        $p->newPath('outline', $outline, ['class' => 'fabric']);
        $p->newPath('helpline', $helpline, ['class' => 'help fabric']);
        $p->newPath('foldline', $foldline, ['class' => 'foldline']);
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);

    }


    /**
     * Drafts the buttonhole placket
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftButtonholePlacket($model)
    {
        $this->clonePoints('frontLeft', 'buttonholePlacket');
        
        /** @var Part $p */
        $p = $this->parts['buttonholePlacket'];
        
        // Paths
        if($this->o('buttonholePlacketStyle') == 1) {
            $outline = 'M 4108 C 41082 41081 9 L 4107 L 4007 L 4008 z'; // Classic style
            $this->setValue('buttonholePlacketSaBase', 'M 4107 L 9 C 41081 41082 4108 L 4008 L 4007');
            $foldline = 'M 4104 L 4004 M 4101 L 4001';
            $helpline = 'M 4105 L 4005 M 4106 L 4006 M 4103 L 4003 M 4102 L 4002 M 4100 L 4000';
        } else {
            $outline = 'M 4108 C 41082 41081 4100 L 41086 C 41084 41085 41083 C 41088 41087 41089 L 4107 L 4007 L 4008 z'; // Seamless/French style
            // SA offset will go cray on these tiny curves, so stick to straight lines for SA base path
            $this->setValue('buttonholePlacketSaBase', 'M 4107 L 41089 L 41083 L 41086 L 4100 L 4108 L 4008 L 4007');
            $foldline = 'M 41083 L 4002 M 4101 L 4001';
            $helpline = 'M 4100 L 4000';
        }
        
        $p->newPath('outline', $outline, ['class' => 'fabric']);
        $p->newPath('helpline', $helpline, ['class' => 'helpline']);
        $p->newPath('foldline', $foldline, ['class' => 'foldline']);
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts the yoke
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftYoke($model)
    {
        $this->clonePoints('backBlock', 'yoke');

        /** @var Part $p */
        $p = $this->parts['yoke'];
    
        $mirrorThese = [8, 20, 12, 19, 17, 10];
        foreach($mirrorThese as $mirrorThis) $p->addPoint("-$mirrorThis", $p->flipX($mirrorThis,0)); 

        $p->newPoint('centerBottom', 0, $p->y(10)); 
        
        // Paths
        if($this->o('splitYoke') == 1) $outline = 'M 1 L centerBottom L 10 C 17 19 12 L 8 C 20 1 1 z'; // Split yoke
        else $outline = 'M 10 C 17 19 12 L 8 C 20 1 1 C 1 -20 -8 L -12 C -19 -17 -10 z';

        $p->newPath('seamline', $outline, ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        $p->clonePoint(1,'gridAnchor');
        
        // Store armhole curve length
        $this->setValue('yokeArmholeLength', $p->curveLen(10,17,19,12));
        
        // Store collar opening length
        $this->setValue('yokeCollarOpeningLength', $p->curveLen(8,20,1,1)*2);
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

        /** @var Part $p */
        $p = $this->parts['back'];

        $p->newPoint('centerTop', 0, $p->y(10));
        $p->addPoint(8000, $p->shift(4,90,$this->o('lengthBonus')), 'Hips height');        
        $p->newPoint(8001, $p->x(6), $p->y(8000), 'Hips height');        
        $hin = ($this->v('hipsReduction'))/4;
        if ($this->v('waistReduction') <= 100) { // Only shape side seams
          $in = $this->v('waistReduction')/4;
        } else { // Back darts too
          $in = ($this->v('waistReduction')*0.6)/4;
          $dart = ($this->v('waistReduction')*0.4)/4;
          $hdart = ($this->v('hipsReduction')*0.4)/4;
          $p->newPoint( 6100, ($p->x(5)-$in)*0.55, $p->y(3));
          $p->newPoint( 6300, $p->x(6100), $p->y(8000)-$p->deltaY(3,8000)*0.15);
          $p->addPoint( 6121, $p->shift(6100,0,$dart));
          $p->addPoint( 6122, $p->shift(6100,180,$dart));
          $p->newPoint( 6110, $p->x(6100),$p->y(3)-$p->deltaY(5,3)*0.75);
          $p->newPoint( 6111, $p->x(6121),$p->y(3)-$p->deltaY(5,3)*0.2);
          $p->newPoint( 6112, $p->x(6122),$p->y(3)-$p->deltaY(5,3)*0.2);
          $p->newPoint( 6113, $p->x(6121),$p->y(3)+$p->deltaY(5,3)*0.2);
          $p->newPoint( 6114, $p->x(6122),$p->y(3)+$p->deltaY(5,3)*0.2);
        }

        // Side shaping
        $p->addPoint( 6001, $p->shift(5,-90,$p->deltaY(5,3)*0.2));
        $p->newPoint( 6011, $p->x(5)-$in,$p->y(3)-$p->deltaY(5,3)/2);
        $p->newPoint( 6021, $p->x(5)-$in,$p->y(3));
        $p->newPoint( 6031, $p->x(5)-$in,$p->y(3)+$p->deltaY(3,8000)/2);

        // Hem shape
        $p->clonePoint(4, 6660);
        $p->newPoint( 6663 , $p->x(8001)+$this->o('hipFlare')/4,$p->y(8001)+$this->o('lengthBonus')-$this->o('hemCurve'));
        $p->addPoint( 6662 , $p->shift(6663,90,$p->deltaY(8001,6663)*0.3));
        $p->addPoint( 6661 , $p->shift(8001,-90,$p->deltaY(8001,6663)*0.3));
        $p->addPoint( 8002 , $p->shift(8001,90,$p->deltaY(6031,8001)/4));

        switch($this->o('hemStyle')) {
            case 1: // Straight hem
                $p->clonePoint(6663,6664);
                $p->newPoint(6665, $p->x(6663), $p->y(6663)+$this->o('hemCurve'));
                $p->clonePoint(6665,6666);
                $p->clonePoint(6666,6667);
                $p->addPoint(6668, $p->shift(6666,180,$p->deltaX(6660,6666)*0.1));
                $p->clonePoint(6668,6669);
                break;
            case 2: // Baseball hem
                $p->addPoint(6664, $p->shift(6663,180,$p->deltaX(6660,6663)*0.3));
                $p->addPoint(6665, $p->shift(6660,0,$p->deltaX(6660,6663)*0.7));
                $p->addPoint(6666, $p->shift(6660,0,$p->deltaX(6660,6663)*0.2));
                $p->clonePoint(6666,6667);
                $p->addPoint(6668, $p->shift(6666,180,$p->deltaX(6660,6666)*0.1));
                $p->clonePoint(6668,6669);
                break;
            case 3: // Slashed hem
                $p->newPoint(6664, $p->x(6663), $p->y(6663)+$this->o('hemCurve'));
                $p->newPoint(6665, $p->x(6663), $p->y(6663)+$this->o('hemCurve'));
                $p->addPoint(6666, $p->shift(6664,180,$p->deltaX(6660,6663)*0.3));
                $p->clonePoint(6666,6667);
                $p->addPoint(6668, $p->shift(6666,180,$p->deltaX(6660,6666)*0.1));
                $p->clonePoint(6668,6669);
                break;
        }

        // Smoothing out curve (overwriting points)
        $p->curveCrossesY(8001,8002,6031,6021,$p->y(8002),'curveSmooth');
        $p->clonePoint('curveSmooth1',8002);
        $p->addPoint(6661, $p->rotate(6661,8001,$p->angle(8001,8002)+90));

        // Yoke dart
        if($this->o('yokeDart') > 0) {
            $p->curveCrossesY(10,18,15,14,$p->y(10)+$this->o('yokeDart'),'yokeDart'); // Adds yokeDart1 point
            $p->newPoint('yokeDart2', ($p->x(5)-$this->v('waistReduction')/4)*0.55 , $p->y(10)); //HERE
            $p->newPoint('yokeDart3', $p->x(10)*0.8, $p->y(10));
        }

        // Paths
        $outline = 'M centerTop ';
        if($this->o('yokeDart') > 0) $outline .= ' L yokeDart2 C yokeDart3 yokeDart1 yokeDart1 ';
        else $outline .= 'L 10 ';

        $outline .= 'C 18 15 14 C 16 13 5 C 6001 6011 6021 C 6031 8002 8001 ';
        if($this->o('hemStyle') == 1) {
            if($p->y(6664) > $p->y(8001)) $outline .= 'C 6661 6662 6664 L 6665 ';
            else $outline .= 'L 6665 ';
            $this->setValue('backSaBase', $outline);
            $outline .= 'L 6660 ';
            $this->setValue('backHemBase', 'M 6660 L 6667');
        } else {
            $outline .= 'C 6661 6662 6663 ';
            $this->setValue('backSaBase', $outline);
            $outline .= 'C 6664 6665 6666 L 6660 ';
            $this->setValue('backHemBase', 'M 6660 L 6666 C 6665 6664 6663');
        }
        $outline .= 'L centerTop z';

        if ($this->v('waistReduction') > 100) { 
            $darts = 'M 6300 C 6300 6114 6122 C 6112 6110 6110 C 6110 6111 6121 C 6113 6300 6300 z ';
            $p->newPath('darts', $darts, ['class' => 'fabric']);
        }
        
        $p->newPath('outline', $outline, ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->newPoint('gridAnchor', 0, $p->y(10));
        
        // Store armhole curve length
        if($this->o('yokeDart') > 0) $this->setValue('backArmholeLength', $p->curveLen('yokeDart1',18,15,14) + $p->curveLen(14,16,13,5));
        else $this->setValue('backArmholeLength', $p->curveLen(10,18,15,14) + $p->curveLen(14,16,13,5));
    }

    /**
     * Drafts the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSleeve($model)
    {
        // Is this the first time we're calling draftSleeve() ?
        if($this->v('sleeveTweakRun') > 0) {
            // No, this will be a tweaked draft. So let's tweak
            if($this->armholeDelta($model) > 0) {
                //  Armhole is larger than sleeve head. Increase tweak factor 
                $this->setValue('sleeveTweakFactor', $this->v('sleeveTweakFactor')*1.01);
            } else {
                //  Armhole is smaller than sleeve head. Decrease tweak factor 
                $this->setValue('sleeveTweakFactor', $this->v('sleeveTweakFactor')*0.99);
            }
            // Include debug message
            $this->dbg('Sleeve tweak run '.$this->v('sleeveTweakRun').'. Sleeve head is '.$this->armholeDelta($model).'mm off');
        }
        // Keep track of tweak runs because why not
        $this->setValue('sleeveTweakRun', $this->v('sleeveTweakRun')+1);
        
        // (re-)Drafting sleeveBlock from parent pattern
        // The second parameter tells parent pattern not to tweak the sleeve
        $this->draftSleeveBlock($model, true);
        
        // Cloning points from the sleeveBlock
        $this->clonePoints('sleeveBlock', 'sleeve');

        /** @var Part $p */
        $p = $this->parts['sleeve'];
        
        // What is the usable cuff width?
        if($this->o('cuffStyle') < 4) $cuffwidth = $model->m('wristCircumference')+$this->o('cuffEase') + 20;
        else if($this->o('cuffStyle') == 6) $cuffwidth = $model->m('wristCircumference')+$this->o('cuffEase') + 30;
        else $cuffwidth = $model->m('wristCircumference')+$this->o('cuffEase') + 30 - $this->o('cuffLength')/2;
        
        // Shorten sleeve to take cuff into account
        $moveMe = [3, 9, -9, 6, -6, 31, 32];
        foreach($moveMe as $move) $p->addPoint($move, $p->shift($move,90,$this->o('cuffLength')));
        
        
        // Sleeve width 
        $width = $cuffwidth;
        $p->newPoint('cuffLeft', $width/-2, $p->y(3));
        $p->addPoint('cuffRight', $p->flipX('cuffLeft',0));
        $p->addPoint('elbowLeft', $p->beamsCross(-5,31,34,33));
        $p->addPoint('elbowRight', $p->flipX('elbowLeft',0));
        $p->newPoint('cuffOneQuarter', $p->x('cuffLeft')+$width/4, $p->y('cuffLeft')); 
        $p->newPoint('cuffThreeQuarters', $p->x('cuffLeft')+$width*0.75, $p->y('cuffLeft'));

        // Wavy sleeve hem
        $p->addPoint(411, $p->shift('cuffOneQuarter', 90 , 3)); 
        $p->addPoint(431, $p->shift('cuffThreeQuarters', -90 , 3)); 
        $waveLen = $width/8;
        $p->addPoint(412, $p->shift(411, 0 , $waveLen)); 
        $p->addPoint(413, $p->shift(411, 180 , $waveLen)); 
        $p->addPoint(432, $p->shift(431, 0 , $waveLen)); 
        $p->addPoint(433, $p->shift(431, 180 , $waveLen)); 
        
        $drape = $this->o('cuffDrape');
        if($this->v('cuffPleats') == 1) { // Single pleat
            // Create room for single
            $shiftLeft = ['cuffLeft', 413, 411, 412];
            foreach($shiftLeft as $pid) $p->addPoint($pid, $p->shift($pid, 180, $drape));
            // Helplines
            $p->addPoint('pleatLeft', $p->shift(3,180,$drape));
            $p->addPoint('pleatCenter', $p->shift(3,180,$drape/2));
            $p->clonePoint(3,'pleatRight');
            $p->addPoint('pleatLeftTop', $p->shift('pleatLeft',90,80));
            $p->addPoint('pleatCenterTop', $p->shift('pleatCenter',90,80));
            $p->addPoint('pleatRightTop', $p->shift('pleatRight',90,80));
            $this->setValue( 'cuffFoldlines', 'M pleatCenter L pleatCenterTop M pleatRight L pleatRightTop');
            $this->setValue('cuffHelplines', 'M pleatLeft L pleatLeftTop ');
            $this->setValue('cuffHemline',  'L cuffRight C cuffRight 432 431 C 433 pleatRight pleatRight L pleatLeft C pleatLeft 412 411 C 413 cuffLeft cuffLeft');
        } else { // Double pleat
            // Create room for first pleat
            $shiftLeft = ['cuffLeft', 413, 411, 412];
            foreach($shiftLeft as $pid) $p->addPoint($pid, $p->shift($pid, 180, $drape/2));
            $p->addPoint('pleatOneLeft', $p->shift(3,180,$drape/2));
            $p->addPoint('pleatOneCenter', $p->shift(3,180,$drape/4));
            $p->clonePoint(3,'pleatOneRight');
            // Split curve at spot for second pleat
            $p->curveCrossesX(3,3,433,432,$p->x(433),'pleatTwoX'); 
            $p->splitCurve(3,3,433,432,'pleatTwoX1', 'pleatTwo');
            // Create room for first pleat
            $p->clonePoint('pleatTwoX1','pleatTwoLeft');
            $shiftLeft = ['cuffRight', 433, 431, 432, 'pleatTwo7','pleatTwoX1'];
            foreach($shiftLeft as $pid) $p->addPoint($pid, $p->shift($pid, 0, $drape/2));
            // Helplines
            $p->clonePoint('pleatTwoX1','pleatTwoRight');
            $p->addPoint('pleatTwoCenter', $p->shift('pleatTwoLeft',0,$drape/4));
            $p->addPoint('pleatOneLeftTop', $p->shift('pleatOneLeft',90,80));
            $p->addPoint('pleatOneCenterTop', $p->shift('pleatOneCenter',90,80));
            $p->addPoint('pleatOneRightTop', $p->shift('pleatOneRight',90,80));
            $p->addPoint('pleatTwoLeftTop', $p->shift('pleatTwoLeft',90,80));
            $p->addPoint('pleatTwoCenterTop', $p->shift('pleatTwoCenter',90,80));
            $p->addPoint('pleatTwoRightTop', $p->shift('pleatTwoRight',90,80));
            $this->setValue(
                'cuffFoldlines', 
                'M pleatOneCenter L pleatOneCenterTop '.
                'M pleatOneRight L pleatOneRightTop '.
                'M pleatTwoCenter L pleatTwoCenterTop '.
                'M pleatTwoRight L pleatTwoRightTop '
            );
            $this->setValue(
                'cuffHelplines', 
                'M pleatOneLeft L pleatOneLeftTop '.
                'M pleatTwoLeft L pleatTwoLeftTop '
            );
            $this->setValue('cuffHemline', 'L cuffRight C cuffRight 432 431 C pleatTwo7 pleatTwoRight pleatTwoRight L pleatTwoLeft C pleatTwo3 pleatOneRight pleatOneRight L pleatOneLeft C pleatOneLeft 412 411 C 413 cuffLeft cuffLeft');
        }

        // Sleeve placket cut
        $p->addPoint('sleevePlacketCutTop', $p->shift(411,90,$this->o('sleevePlacketLength')+$this->o('sleevePlacketWidth')*0.35));
        $this->setValue('sleevePacketCut', 'M 411 L sleevePlacketCutTop');

        // Paths
        $p->newPath('cuffHelplines', $this->v('cuffHelplines'), ['class' => 'hint fabric']);   
        $p->newPath('cuffFoldlines', $this->v('cuffFoldlines'), ['class' => 'hint fabric']);   
        $p->newPath('sleevePacketCut', $this->v('sleevePacketCut'), ['class' => 'fabric']);   
        
        $outline = 'M -5 C -5 20 16 C 21 10 10 C 10 22 17 C 23 28 30 C 29 25 18 C 24 11 11 C 11 27 19 C 26 5 5  ';
        $this->setValue('sleeveFfsaBase', "$outline L cuffRight");
        $this->setValue('sleeveSaBase', 'M cuffRight '.$this->v('cuffHemline').' L -5');
        $outline .= $this->v('cuffHemline');
        $outline .= ' z'; 
        $p->newPath('seamline', $outline, ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    
        // Store sleevehead length
        $this->setValue('sleeveheadLength', $p->curveLen(-5,-5,20,16) + $p->curveLen(16,21,10,10) + $p->curveLen(10,10,22,17) + $p->curveLen(17,23,28,30) + $p->curveLen(30,29,25,18) + $p->curveLen(18,14,11,11) + $p->curveLen(11,11,27,19) + $p->curveLen(19,26,5,5));
    }

    /**
     * Drafts the collar stand
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftCollarStand($model)
    {
        // Is this the first time we're calling draftCollarStand() ?
        if($this->v('collarStandTweakRun') > 0) {
            // No, this will be a tweaked draft. So let's tweak
            if($this->collarStandDelta($model) > 0) {
                //  Collar stand is too big. Decrease tweak factor 
                $this->setValue('collarStandTweakFactor', $this->v('collarStandTweakFactor')*0.985);
            } else {
                //  Collar stand is too small. Increase tweak factor 
                $this->setValue('collarStandTweakFactor', $this->v('collarStandTweakFactor')*1.01);
            }
            // Include debug message
            $this->dbg('Collar stand tweak run '.$this->v('collarStandTweakRun').'. Collar stand is '.$this->collarStandDelta($model).'mm off');
        }

        // Keep track of tweak runs because why not
        $this->setValue('collarStandTweakRun', $this->v('collarStandTweakRun')+1);
        
        /** @var Part $p */
        $p = $this->parts['collarStand'];

        $base = ($model->m('neckCircumference')/2 + $this->o('collarEase')/2) * $this->v('collarStandTweakFactor');
        $extra = $this->o('buttonholePlacketWidth')/2 + $this->o('buttonPlacketWidth')/2;

        $extraBtnHoleSide = $this->o('buttonholePlacketWidth')/2;
        $extraBtnSide = $this->o('buttonPlacketWidth')/2;

        $p->newPoint( 1, 0,0);
        $p->newPoint( 2, $base,0);

        $p->newPoint('21BtnSide',$p->x(2)+$extraBtnSide,$p->y(2));
        $p->newPoint('21BtnHoleSide',$p->x(2)+$extraBtnHoleSide,$p->y(2));
        
        $p->newPoint( 3, $p->x(2),$this->o('collarStandWidth'));
        
        $p->newPoint('31BtnSide',$p->x(3)+$extraBtnSide,$p->y(3));
        $p->newPoint('31BtnHoleSide',$p->x(3)+$extraBtnHoleSide,$p->y(3));
        
        $p->newPoint( 4,$p->x(1),$p->y(3));
        $p->newPoint( 5,$p->x(2),$p->y(3)/2);

        $p->addPoint('21BtnSide',$p->rotate('21BtnSide',5,$this->o('collarStandCurve')));
        $p->addPoint('21BtnSide',$p->shift('21BtnSide',90,$this->o('collarStandBend')/2));
        $p->addPoint('21BtnHoleSide',$p->rotate('21BtnHoleSide',5,$this->o('collarStandCurve')));
        $p->addPoint('21BtnHoleSide',$p->shift('21BtnHoleSide',90,$this->o('collarStandBend')/2));
        
        $p->addPoint('31BtnSide',$p->rotate('31BtnSide',5,$this->o('collarStandCurve')));
        $p->addPoint('31BtnSide',$p->shift('31BtnSide',90,$this->o('collarStandBend')/2));
        $p->addPoint('31BtnHoleSide',$p->rotate('31BtnHoleSide',5,$this->o('collarStandCurve')));
        $p->addPoint('31BtnHoleSide',$p->shift('31BtnHoleSide',90,$this->o('collarStandBend')/2));

        // 21 and 31 are assymetric, can't be mirrored
        $p->addPoint(21, $p->loadPoint('21BtnHoleSide'));
        $p->addPoint(31, $p->loadPoint('31BtnHoleSide'));
        $p->addPoint(-21, $p->flipX('21BtnSide'));
        $p->addPoint(-31, $p->flipX('31BtnSide'));

        $p->addPoint(22,$p->rotate(2,5,$this->o('collarStandCurve')));
        $p->addPoint(22,$p->shift(22,90,$this->o('collarStandBend')/2));
        $p->addPoint(32,$p->rotate(3,5,$this->o('collarStandCurve')));
        $p->addPoint(32,$p->shift(32,90,$this->o('collarStandBend')/2));
        $p->addPoint(23,$p->shift(22,$this->o('collarStandCurve')+180,10));
        $p->addPoint(33,$p->shift(32,$this->o('collarStandCurve')+180,10));
        $p->addPoint(52,$p->shift(5,90,$this->o('collarStandBend')));
        $p->addPoint(12,$p->shift(1,90,$this->o('collarStandBend')));
        $p->addPoint(42,$p->shift(4,90,$this->o('collarStandBend')));
        $p->newPoint(43,$p->x(2)*0.4,$p->y(42));
        $p->newPoint( 6,$p->x(2)*0.6,$p->y(3));
        $p->newPoint(61,$p->x(2)*0.75,$p->y(3));
        $p->newPoint(62,$p->x(2)*0.95,$p->y(3));
        $p->newPoint( 7,$p->x(6),$p->y(1));
        $p->newPoint(71,$p->x(2)*0.75,$p->y(1));
        $p->newPoint(72,$p->x(2)*0.95,$p->y(1));
        $p->newPoint(13,$p->x(43),$p->y(12));

        // 51 is based on assymetric 21 & 31, can't be mirrored
        $p->addPoint(51,$p->shiftTowards(21,31,$p->distance(21,31)/2));
        $p->addPoint(-51,$p->shiftTowards(-21,-31,$p->distance(-21,-31)/2));
        
        $p->addPoint(55,$p->shiftTowards(22,32,$p->distance(22,32)/2));
        $p->newPoint(56,0,$p->y(3)/2);
        $p->newPoint(57,$p->x(61),$p->y(56));
        $flip = array(2,3,5,6,7,12,13,22,23,32,33,42,43,52,55,57,61,62,71,72);
        foreach($flip as $pf) {
            $id = $pf*-1;
            $p->addPoint($id,$p->flipX($pf));
        }
       
        // Move buttonhole to not be centered
        $p->addPoint(55,$p->shift(55,$this->o('collarStandCurve')+180,3));
        
        // Paths  
        $outline = 'M 42 C 43 6 61 C 62 33 32 L 31 C 51 21 22 C 23 72 71 C 7 13 12 C -13 -7 -71 C -72 -23 -22 C -21 -51 -31 L -32 C -33 -62 -61 C -6 -43 -42 z';
        $p->newPath('outline', $outline, ['class' => 'fabric']);
        $p->newPath('helpline', 'M 22 L 32 M -22 L -32', ['class' => 'help fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        
        // Store collar stand length
        $this->setValue('collarStandLength', 2 * ($p->curveLen(42,43,6,61) + $p->curveLen(61,62,33,32))); 
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
        // Is this the first time we're calling draftCollar() ?
        if($this->v('collarTweakRun') > 0) {
            // No, this will be a tweaked draft. So let's tweak
            if($this->collarDelta($model) > 0) {
                //  Collar is too big. Decrease tweak factor 
                $this->setValue('collarTweakFactor', $this->v('collarTweakFactor')*0.985);
            } else {
                //  Collar is too small. Increase tweak factor 
                $this->setValue('collarTweakFactor', $this->v('collarTweakFactor')*1.01);
            }
            // Include debug message
            $this->dbg('Collar tweak run '.$this->v('collarTweakRun').'. Collar is '.$this->collarDelta($model).'mm off');
        }

        // Keep track of tweak runs because why not
        $this->setValue('collarTweakRun', $this->v('collarTweakRun')+1);
        
        /** @var Part $p */
        $p = $this->parts['collar'];

        $base = ($model->m('neckCircumference')/2+$this->o('collarEase')/2) * $this->v('collarTweakFactor');
        $p->newPoint( 0 , 0, 0);
        $p->newPoint( 1 , $base,0);  
        $p->newPoint( 2 , $p->x(1),-1*($this->o('collarStandWidth')+$this->o('collarRoll')+$this->o('collarBend')));
        $p->newPoint( 3 , $p->x(0),$p->y(2));
        $p->newPoint( 4 , $p->x(1)-$this->o('collarGap')/2,0);
        $p->newPoint( 5 , 0,-1*$this->o('collarBend'));
        $p->newPoint( 6 , $p->x(1)*0.8,$p->y(5));
        $p->addPoint(401 , $p->shift(4,180,20));
        $p->addPoint(402 , $p->rotate(401,4,$this->o('collarAngle')));
        $p->newPoint( 7 , 0,$p->y(2)-$this->o('collarFlare'));
        $p->addPoint(701 , $p->shift(7,0,20));
        $p->addPoint( 8 , $p->beamsCross(7,701,4,402),'Collar tip');
        $p->newPoint( 9 , $p->x(1)*0.7,$p->y(3));
        $p->addPoint( 10, $p->shift(5,90,$p->distance(5,3)/2));
        $p->newPoint( 11, $p->x(6),$p->y(10));
        $p->newPoint( 12, $p->x(8)/2,$p->y(3));
        $flip = array(1,2,4,6,8,9,11,12);
        foreach($flip as $pf) {
          $id = $pf*-1;
          $p->addPoint($id,$p->flipX($pf));
        }
        
        // Paths
        $outline = 'M 5 C 6 4 4 L 8 C 8 9 12 L -12 C -9 -8 -8 L -4 C -4 -6 5 z';  
        $p->newPath('outline', $outline, ['class' => 'fabric']);
        $p->newPath('helpine', 'M 5 L 3', ['class' => 'helpline']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);

        // Store collar length
        $this->setValue('collarLength', 2 * ($p->curveLen(5,6,4,4))); 
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

        /** @var Part $p */
        $p = $this->parts['undercollar'];

        $p->addPoint(5,$p->shift(5,90,3));
        $p->addPoint(6,$p->shift(6,90,2));
        $p->addPoint(-6,$p->shift(-6,90,2));
        
        // Paths
        $outline = 'M 4 L 8 C 8 9 12 L -12 C -9 -8 -8 L -4 C -4 -6 5 C 6 4 4 z';  
        $p->newPath('outline', $outline, ['class' => 'fabric']);
        $p->newPath('helpine', 'M 5 L 3', ['class' => 'helpline']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts the sleeve placket underlap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSleevePlacketUnderlap($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleevePlacketUnderlap'];

        if ($this->o('sleevePlacketWidth')>=20) {
            $width = 10;
            $btndist = 5;
            $fold = 8;
        } else {
            $btndist = $this->o('sleevePlacketWidth')/4;
            $width = $this->o('sleevePlacketWidth')/2;
            $fold = $width-1.5;
        }
        
        // Handle possible lack of seam allowance value
        if($this->o('sa')) $sa = $this->o('sa');
        else $sa = 0;
        
        $p->newPoint(0,0,0);
        $p->newPoint(1,$this->o('sleevePlacketLength')+$sa+$this->o('sleevePlacketWidth')*0.35,0);
        $p->addPoint(2,$p->shift(1,-90,$fold));
        $p->addPoint(3,$p->shift(2,-90,$width));
        $p->addPoint(4,$p->shift(3,-90,$width));
        $p->addPoint(5,$p->shift(4,-90,$fold));
        $p->addPoint(6,$p->shift(0,-90,$fold));
        $p->addPoint(7,$p->shift(6,-90,$width));
        $p->addPoint(8,$p->shift(7,-90,$width));
        $p->addPoint(9,$p->shift(8,-90,$fold));
        $p->addPoint(10,$p->shift(0,0,$sa));
        $p->addPoint(11,$p->shift(9,0,$sa));
        $p->newPoint(12,$p->x(1)/2,$p->y(3)+$btndist);
        
        // Paths
        $p->newPath('outline', 'M 10 L 1 L 5 L 11 z', ['class' => 'fabric']);
        $p->newPath('foldline', 'M 2 L 6 M 7 L 3 M 4 L 8', ['class' => 'hint fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts the sleeve placket overlap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSleevePlacketOverlap($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleevePlacketOverlap'];

        if ($this->o('sleevePlacketWidth')>=20) {
            $width = 10;
            $btndist = 5;
            $fold = 8;
        } else {
            $btndist = $this->o('sleevePlacketWidth')/4;
            $width = $this->o('sleevePlacketWidth')/2;
            $fold = $width-1.5;
        }
        
        // Handle possible lack of seam allowance value
        if($this->o('sa')) $sa = $this->o('sa');
        else $sa = 0;
        
        $p->newPoint(0 , 0, 0);
        // 1.5 for fixed part, 0.7 for fold = 2.2 times 
        $p->newPoint(1,$this->o('sleevePlacketLength')+$sa+$this->o('sleevePlacketWidth')*2.2,0);
        $p->addPoint(2,$p->shift(1,-90,$fold));
        $p->addPoint(3,$p->shift(2,-90,$this->o('sleevePlacketWidth')));
        $p->addPoint(4,$p->shift(3,-90,$this->o('sleevePlacketWidth')+2));
        $p->addPoint(5,$p->shift(4,-90,$fold));
        $p->newPoint(6,$this->o('sleevePlacketLength')+$this->o('sleevePlacketWidth')*1.5+10,0);
        $p->addPoint(7,$p->shift(6,-90,$fold));
        $p->addPoint(8,$p->shift(7,-90,$this->o('sleevePlacketWidth')));
        $p->addPoint(9,$p->shift(8,-90,$this->o('sleevePlacketWidth')+2));
        $p->addPoint(10,$p->shift(9,-90,$fold));
        $p->newPoint(11,$this->o('sleevePlacketLength')+10,$fold);
        $p->addPoint(12,$p->shift(11,-90,$this->o('sleevePlacketWidth')));
        $p->addPoint(13,$p->shift(3,-90,$this->o('sleevePlacketWidth')/3));
        $p->addPoint(14,$p->shift(13,180,$this->o('sleevePlacketWidth')*0.75));
        $p->newPoint(15,$p->x(12)+$this->o('sleevePlacketWidth')*0.35,$p->y(3)+2*$this->o('sleevePlacketWidth')/3);
        $p->newPoint(16,$p->x(15),$p->y(5));
        $p->newPoint(17,$p->x(15)+$this->o('sleevePlacketWidth')*0.5,$p->y(15));
        $p->newPoint(18,$p->y(0),$p->y(16));
        $p->newPoint(19,$p->y(0),$p->y(2));
        $p->newPoint(20,$p->y(0),$p->y(3));
        $p->newPoint(21,$p->y(0),$p->y(4));
        $p->addPoint(22,$p->beamsCross(2,8,3,7));
        $p->newPoint(23,$p->x(15),$p->y(4));
        $p->newPoint(24,$p->x(0)+$sa,$p->y(0));
        $p->newPoint(25,$p->x(0)+$sa,$p->y(5));
        $p->addPoint(26,$p->flipY(22,$p->y(2)));
        $p->addPoint(27,$p->flipY(22,$p->y(3)));
        $p->addPoint(28,$p->beamsCross(6,1,7,26));
        $p->addPoint(29,$p->beamsCross(13,14,8,27));
        $p->newPoint(30,$p->x(11)/2+5,$p->y(22));
        
        // Paths
        $p->newPath('outline', 'M 24 L 1 L 13 L 14 L 17 L 15 L 16 L 25 z', ['class' => 'fabric']);
        $p->newPath('foldline', 'M 19 L 2 M 7 L 22 L 8 M 3 L 20 M 2 L 22 L 3 M 21 L 23 M 7 L 28 M 8 L 29', ['class' => 'hint fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts a barrel cuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBarrelCuff($model)
    {
        /** @var Part $p */
        $p = $this->parts['barrelCuff'];

        $p->newPoint(0,0,0);
        $p->newPoint(1,$model->m('wristCircumference')/2+$this->o('cuffEase')/2,0,'Button line');  
        $p->addPoint(2,$p->shift(1,0,10));
        $p->newPoint(3,$p->x(1),$this->o('cuffLength'));
        $p->newPoint(4,$p->x(2),$p->y(3));
        $p->newPoint(5,$model->m('wristCircumference')/2-$this->o('cuffEase')/2+20,0, 'Narrow button line');
        $p->newPoint(6, $p->x(1), $p->y(3)*0.45,'Single button');
        $p->newPoint(7, $p->x(1), $p->y(3)*0.3,'Double button, A');
        $p->newPoint(8, $p->x(1), $p->y(3)*0.7,'Double button, B');
        $p->newPoint(9, $p->x(5), $p->y(3)*0.45,'Single button');
        $p->newPoint(10, $p->x(5), $p->y(3)*0.3,'Double button, A');
        $p->newPoint(11, $p->x(5), $p->y(3)*0.7,'Double button, B');
        $p->newPoint(12, $p->x(2), $p->y(3)*0.25);
        $p->addPoint(13, $p->shift(2,180,$p->distance(2,12)));
        $p->addPoint(14, $p->shift(13,0,\Freesewing\BezierToolbox::bezierCircle($p->distance(2,12))));
        $p->addPoint(15, $p->shift(12,90,\Freesewing\BezierToolbox::bezierCircle($p->distance(2,12))));
        $flip = array(1,2,3,4,6,7,8,12,13,14,15);
        foreach($flip as $pf) {
          $id = $pf*-1;
          $p->addPoint($id,$p->flipX($pf));
        }
        // Shift for buttonholes
        $p->addPoint(-6,$p->shift(-6,0,4));
        $p->addPoint(-7,$p->shift(-7,0,4));
        $p->addPoint(-8,$p->shift(-8,0,4));
        if($this->o('cuffButtonRows') == 2) {
            $p->newSnippet($p->newId('button'), 'button', 7);
            $p->newSnippet($p->newId('button'), 'button', 8);
            $p->newSnippet($p->newId('buttonhole'), 'buttonhole', -7, ['transform' => 'rotate(90 '.$p->x(-7).' '.$p->y(-7).')']);
            $p->newSnippet($p->newId('buttonhole'), 'buttonhole', -8, ['transform' => 'rotate(90 '.$p->x(-8).' '.$p->y(-8).')']);
            if($this->o('barrelcuffNarrowButton') == 1) {
                $p->newSnippet($p->newId('button'), 'button', 10);
                $p->newSnippet($p->newId('button'), 'button', 11);
            }
        } else {
            $p->newSnippet($p->newId('button'), 'button', 6);
            $p->newSnippet($p->newId('buttonhole'), 'buttonhole', -6, ['transform' => 'rotate(90 '.$p->x(-6).' '.$p->y(-6).')']);
            if($this->o('barrelcuffNarrowButton') == 1) {
                $p->newSnippet($p->newId('button'), 'button', 9);
            }
        }
        
        // Paths
        if($this->o('cuffStyle') == 1) $outline = 'M 0 L 13 C 14 15 12 L 4 L -4 L -12 C -15 -14 -13 z';
        else if($this->o('cuffStyle') == 2) $outline = 'M 0 L 13 L 12 L 4 L -4 L -12 L -13 z';
        else $outline = 'M 0 L 2 L 4 L -4 L -2 z';

        $p->newPath('outline', $outline, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts a French cuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrenchCuff($model)
    {
        /** @var Part $p */
        $p = $this->parts['frenchCuff'];

        $p->newPoint(0,0,0);
        $p->newPoint(1,$model->m('wristCircumference')/2+$this->o('cuffEase')/2,0,'Button line');  
        $p->addPoint(2,$p->shift(1,0,15));
        $p->newPoint(3,$p->x(1),$this->o('cuffLength'));
        $p->newPoint(4,$p->x(2),$p->y(3));
        $p->newPoint(6, $p->x(1), $p->y(3)*0.45,'First buttonhole');
        $p->addPoint(7, $p->shift(6,-90,$p->distance(6,3)*2), 'Second buttonhole');
        // Shift buttonholes
        $p->addPoint(6,$p->shift(6,180,4));
        $p->addPoint(7,$p->shift(7,180,4));
        $p->newPoint(12, $p->x(2), $p->y(3)*0.25);
        $p->addPoint(13, $p->shift(2,180,$p->distance(2,12)));
        $p->addPoint(14, $p->shift(13,0,\Freesewing\BezierToolbox::bezierCircle($p->distance(2,12))));
        $p->addPoint(15, $p->shift(12,90,\Freesewing\BezierToolbox::bezierCircle($p->distance(2,12))));
        $p->newPoint(16,$p->x(2),$p->y(3)*2);
        $p->addPoint(17,$p->flipY(12,$p->y(4)));
        $p->addPoint(18,$p->flipY(13,$p->y(4)));
        $p->addPoint(19,$p->flipY(14,$p->y(4)));
        $p->addPoint(20,$p->flipY(15,$p->y(4)));
        $p->addPoint(21,$p->flipY(1,$p->y(4)));
        $flip = array(1,2,3,4,6,7,12,13,14,15,16,17,18,19,20,21);
        foreach($flip as $pf) {
          $id = $pf*-1;
          $p->addPoint($id,$p->flipX($pf));
        }
        // Need to move foldline a bit and buttonholes at back
        // This way first half coverd the seam
        $foldshift = 1.5;
        $p->addPoint(4, $p->shift(4,-90,$foldshift));
        $p->addPoint(-4, $p->shift(-4,-90,$foldshift));
        $p->addPoint(7, $p->shift(7,-90,$foldshift*2));
        $p->addPoint(-7, $p->shift(-7,-90,$foldshift*2));
        $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 6, ['transform' => 'rotate(90 '.$p->x(6).' '.$p->y(6).')']);
        $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 7, ['transform' => 'rotate(90 '.$p->x(7).' '.$p->y(7).')']);
        $p->newSnippet($p->newId('buttonhole'), 'buttonhole', -6, ['transform' => 'rotate(90 '.$p->x(-6).' '.$p->y(-6).')']);
        $p->newSnippet($p->newId('buttonhole'), 'buttonhole', -7, ['transform' => 'rotate(90 '.$p->x(-7).' '.$p->y(-7).')']);
        
        //Paths
        if($this->o('cuffStyle') == 4) $outline = 'M 0 L 13 C 14 15 12 L 17 C 20 19 18 L -18 C -19 -20 -17 L -12 C -15 -14 -13 z';
        else if($this->o('cuffStyle') == 5) $outline = 'M 0 L 13 L 12 L 17 L 18 L -18 L -17 L -12 L -13 z';
        else $outline = 'M 0 L 2 L 16 L -16 L -2 z';

        $p->newPath('outline', $outline, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }
    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/sa fabric/measurements and so on
    */

    /**
     * Finalizes the front right
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrontRight($model)
    {
        /** @var Part $p */
        $p = $this->parts['frontRight'];
        
        // First button
        if($this->o('buttonPlacketType')==1) $p->newSnippet($p->newId('button'), 'button', 3000); 
        
        // Next buttons
        for($i=1;$i<$this->o('buttons');$i++) {
          $pid = 3000+$i;
          if($this->o('buttonPlacketType')==1) $p->newSnippet($p->newId('button'), 'button', $pid); 
        }
        
        // Extra top button
        if($this->o('extraTopButton')) {
          $extrapid = $pid +1;
          if($this->o('buttonPlacketType')==1) $p->newSnippet($p->newId('button'), 'button', $extrapid); 
        }
        
        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa', $this->v('frontRightSaBase'), $this->o('sa')*-1, 1, ['class' => 'sa fabric']);
            // Flat felled seam allowance
            $p->offsetPathString('ffsa', $this->v('frontRightSideBase'), Utils::constraint($this->o('sa')*2,12,25), 1, ['class' => 'sa fabric']);
            $p->offsetPathString('ffsaHint', $this->v('frontRightSideBase'), Utils::constraint($this->o('sa')*2,12,25)/2, 1, ['class' => 'hint fabric']);
            // Hem seam allowance
            $p->offsetPathString('hemSa', $this->v('frontRightHemBase'), $this->o('sa')*3, 1, ['class' => 'sa fabric']);
            // Join different offsets
            $p->newPoint('joinArmhole', $p->x('ffsa-startPoint'), $p->y('sa-startPoint'));
            $p->newPoint('joinSideHem', $p->x('ffsa-endPoint'), $p->y('hemSa-startPoint'));
            $p->newPoint('joinHem', $p->x('sa-endPoint'), $p->y('hemSa-endPoint'));
            $p->newPath('joinSa', 'M ffsa-startPoint L joinArmhole L sa-startPoint M ffsa-endPoint L joinSideHem L hemSa-startPoint M hemSa-endPoint L joinHem L sa-endPoint', ['class' => 'sa fabric']);
        }

        // Title
        $p->newPoint('titleAnchor', $p->x(5)/2, $p->y(2)+50);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from fabric'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-90, 70));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('logoAnchor', -90, 40));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Grainline
        $p->addPoint('grainlineTop', $p->shift(9,180,50));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(6660)-10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notches
        $notchHere = [10, 6021, 8001];
        if($this->o('buttonPlacketType') == 1) {
            if($this->o('buttonPlacketStyle') == 1) $notchAlso = [2040, 4, 2045, 2041, 2042, 9];
            else if($this->o('buttonPlacketStyle') == 2) $notchAlso = [4, 2045, 2046, -2053, 2042, 9];
            $notchHere = array_merge($notchHere, $notchAlso);
        }
        $p->notch($notchHere);

        // Store front sleeveNotch distance
        $this->setValue('frontSleeveNotchDistance', $p->curveLen(5,13,16,14) + $p->curveLen(14,15,18,10));
    }
    
    /**
     * Finalizes the front left
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrontLeft($model)
    {
        /** @var Part $p */
        $p = $this->parts['frontLeft'];
        
        // First button
        if($this->o('buttonholePlacketType')==1) $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 3000); 
        // Next buttons
        for($i=1;$i<$this->o('buttons');$i++) {
          $pid = 3000+$i;
          if($this->o('buttonholePlacketType')==1) $p->newSnippet($p->newId('buttonhole'), 'buttonhole', $pid); 
        }
        
        // Extra top button
        if($this->o('extraTopButton')) {
          $extrapid = $pid +1;
          if($this->o('buttonholePlacketType')==1) $p->newSnippet($p->newId('buttonhole'), 'buttonhole', $extrapid); 
        }
        
        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa', $this->v('frontLeftSaBase'), $this->o('sa'), 1, ['class' => 'sa fabric']);
            // Flat felled seam allowance
            $p->offsetPathString('ffsa', $this->v('frontLeftSideBase'), Utils::constraint($this->o('sa')*2,12,25)*-1, 1, ['class' => 'sa fabric']);
            $p->offsetPathString('ffsaHint', $this->v('frontLeftSideBase'), Utils::constraint($this->o('sa')*2,12,25)*-0.5, 1, ['class' => 'sa hint']);
            // Hem seam allowance
            $p->offsetPathString('hemSa', $this->v('frontLeftHemBase'), $this->o('sa')*-3, 1, ['class' => 'sa fabric']);
            // Join different offsets
            if($this->o('buttonholePlacketType') == 1) {
                $joinStart = 4107;
                $joinEnd = 4007;
            } else {
                $joinStart = 4108;
                $joinEnd = 4008;
            }
            $p->newPoint('joinArmhole', $p->x('ffsa-startPoint'), $p->y('sa-startPoint'));
            $p->newPoint('joinSideHem', $p->x('ffsa-endPoint'), $p->y('hemSa-startPoint'));
            $p->newPoint('joinHem', $p->x('sa-endPoint'), $p->y('hemSa-endPoint'));
            $p->newPath('joinSa', 'M ffsa-startPoint L joinArmhole L sa-startPoint M ffsa-endPoint L joinSideHem L hemSa-startPoint M hemSa-endPoint L joinHem L sa-endPoint', ['class' => 'sa fabric']);
        }

        // Title
        $p->newPoint('titleAnchor', $p->x(5)/2, $p->y(2)+50);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('from fabric'));
        
        // Grainline
        $p->addPoint('grainlineTop', $p->shift(9,0,50));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(6660)-10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notches
        $notchHere = [10, 6021, 8001];
        if($this->o('buttonholePlacketType') == 1) {
            if($this->o('buttonholePlacketStyle') == 1) $notchAlso = [4005, 6660, 4006, 4000, 4003, 4001, 4002, 4105, 4104, 4106, 4100, 4103, 4101, 4102];
            else if($this->o('buttonholePlacketStyle') == 2) $notchAlso = [4002, 4001, 4, 41083, 4101, 9];
            $notchHere = array_merge($notchHere, $notchAlso);
        }
        $p->notch($notchHere);
    }

    /**
     * Finalizes the button placket
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeButtonPlacket($model)
    {
        /** @var Part $p */
        $p = $this->parts['buttonPlacket'];
        
        // First buttonhole
        $p->newSnippet($p->newId('button'), 'button', 3000);
        
        // Next buttonholes
        for($i=1;$i<$this->o('buttons');$i++) {
          $pid = 3000+$i;
          $p->newSnippet($p->newId('button'), 'button', $pid);
        }
        // Extra top buttonhole
        if($this->o('extraTopButton')) {
          $extrapid = $pid +1;
          $p->newSnippet($p->newId('button'), 'button', $extrapid);
        }
        // Seam allowance
        if($this->o('sa')) {
            if($this->o('buttonPlacketStyle') == 2) {
                $p->offsetPathString('sa', $this->v('buttonPlacketSeamlessSaBase'), $this->o('sa')*-1, 1, ['class' => 'sa fabric']);
                // Join seam allowance ends
                $p->newPath('joinSa', 'M 2043 L sa-endPoint M 2044 L sa-startPoint', ['class' => 'sa fabric']);
            } 
            else $p->offsetPath('sa', 'outline', $this->o('sa')*-1, 1, ['class' => 'sa fabric']);
            // Extra hem allowance
            $shiftThese = ['sa-line-2044TO2040', 'sa-line-2040TO2044', 'sa-startPoint'];
            foreach($shiftThese as $i) $p->addPoint($i, $p->shift($i,-90,20));
        }

        // Title
        $p->newPoint('titleAnchor', $p->x(2042), $p->y(2)+50);
        $p->addTitle('titleAnchor', '1b', $this->t($p->title), '1x '.$this->t('from fabric'), 'vertical-small');

        // Grainline
        $p->addPoint('grainlineTop', $p->shift(2042,-45,10));
        $p->addPoint('grainlineBottom', $p->shift(2045,45,10));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notches
        if($this->o('buttonPlacketStyle') == 1) $notchHere = [4, 2045, 2042, 9];
        else if($this->o('buttonPlacketStyle') == 2) $notchHere = [4, 2045, 2046, -2053, 2042, 9];
        $p->notch($notchHere);
    }


    /**
     * Finalizes the buttonhole placket
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeButtonholePlacket($model)
    {
        /** @var Part $p */
        $p = $this->parts['buttonholePlacket'];

        // First buttonhole
        $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 3000);
        
        // Next buttonholes
        for($i=1;$i<$this->o('buttons');$i++) {
          $pid = 3000+$i;
          $p->newSnippet($p->newId('buttonhole'), 'buttonhole', $pid);
        }
        // Extra top buttonhole
        if($this->o('extraTopButton')) {
          $extrapid = $pid +1;
          $p->newSnippet($p->newId('buttonhole'), 'buttonhole', $extrapid);
        }

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa', $this->v('buttonholePlacketSaBase'), $this->o('sa')*-1, 1, ['class' => 'sa fabric']);

            // Join seam allowance ends
            $p->newPath('joinSa', 'M 4107 L sa-startPoint M 4007 L sa-endPoint', ['class' => 'sa fabric']); 
            // Title
            if($this->o('buttonholePlacketStyle') == 2) $p->newPoint('titleAnchor', $p->x(41086), $p->y(2)+50);
            else $p->newPoint('titleAnchor', $p->x(4100), $p->y(2)+50);
            $p->addTitle('titleAnchor', '2b', $this->t($p->title), '1x '.$this->t('from fabric'), 'vertical-small');
            
            // Extra hem allowance
            if($this->o('buttonholePlacketStyle') == 1) $shiftThese = ['sa-endPoint', 'sa-line-4007TO4008', 'sa-line-4008TO4007', 'sa-line-4008TO4108XllXsa-line-4008TO4007'];
            else $shiftThese = ['sa-line-4007TO4008', 'sa-line-4008TO4007', 'sa-line-4008TO4108XllXsa-line-4008TO4007'];
        }

        // Notches
        $notchHere = [10, 6021, 8001];
        if($this->o('buttonholePlacketStyle') == 1) $notchHere = [4005, 6660, 4006, 4000, 4003, 4001, 4105, 4104, 4106, 4100, 4103, 4101];
        else if($this->o('buttonholePlacketStyle') == 2) $notchHere = [4002, 4001, 4, 41083, 4101, 9];
        $p->notch($notchHere);
    }

    /**
     * Finalizes the yoke
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeYoke($model)
    {
        /** @var Part $p */
        $p = $this->parts['yoke'];
        
        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'seamline', $this->o('sa'), 1, ['class' => 'sa fabric']);

        // Title
        if($this->o('splitYoke') == 1) {
            $p->newPoint('titleAnchor', $p->x(12)/2, $p->y(10)/2); 
            $p->addTitle('titleAnchor', '4', $this->t($p->title), '4x '.$this->t('from fabric'));
        } else {
            $p->newPoint('titleAnchor', $p->x(1), $p->y(1)+60); 
            $p->addTitle('titleAnchor', '4', $this->t($p->title), '2x '.$this->t('from fabric'));
        }

        // Grainline
        $p->addPoint('grainlineTop', $p->shift(8,-90,5));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('centerBottom')-5);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));
        
        // Notches
        $p->notch([10]);
        if($this->o('splitYoke') == 0) $p->notch([-10]);
    }

    /**
     * Finalizes the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBack($model)
    {
        /** @var Part $p */
        $p = $this->parts['back'];
        
        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa', $this->v('backSaBase'), $this->o('sa')*-1, 1, ['class' => 'sa fabric']);
            // Hem allowance
            $p->offsetPathString('hemSa', $this->v('backHemBase'), $this->o('sa')*3, 1, ['class' => 'sa fabric']);
            // Join SA
            $p->newPath('saJoin', 'M sa-endPoint L hemSa-endPoint M hemSa-startPoint L 6660 M centerTop L sa-startPoint', ['class' => 'sa fabric']);
        }

        // Helplines
        $p->newPath('chestLine', 'M 2 L 5', ['class' => 'help fabric']);
        $p->newPath('waistLine', 'M 3 L 6021', ['class' => 'help fabric']);
        $p->newPath('hipsLine', 'M 8000 L 8001', ['class' => 'help fabric']);

        // Title
        $p->addPoint('titleAnchor', $p->shift(11,160, 80)); 
        $p->addTitle('titleAnchor', '3', $this->t($p->title), '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));
        
        // Grainline
        $p->newPoint('grainlineTop', $p->x(2), $p->y(10)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4)-10);
        $p->newCutOnFold('grainlineBottom', 'grainlineTop', $this->t('Cut on fold').' - '.$this->t('Grainline'), 20);
        
        // Notches
        $p->notch([6021, 8001]);
        if($this->o('yokeDart') > 0) $p->notch(['yokeDart1']);
        else $p->notch([10]);
        
        // Store back sleeveNotch distance
        if($this->o('yokeDart') > 0) $this->setValue('backSleeveNotchDistance', $p->curveLen(5,13,16,14) + $p->curveLen(14,15,18,'yokeDart1'));
        else $this->setValue('backSleeveNotchDistance', $p->curveLen(5,13,16,14) + $p->curveLen(14,15,18,10));
    }

    /**
     * Finalizes the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSleeve($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleeve'];
       
        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa', $this->v('sleeveSaBase'), $this->o('sa')*-1, 1, ['class' => 'sa fabric']);
            // Flat felled seam allowance
            $p->offsetPathString('ffsa', $this->v('sleeveFfsaBase'), Utils::constraint($this->o('sa')*2,12,25)*-1, 1, ['class' => 'sa fabric']);
            $p->offsetPathString('ffsaHint', $this->v('sleeveFfsaBase'), Utils::constraint($this->o('sa')*2,12,25)*-0.5, 1, ['class' => 'sa hint']);
            // Join SA
            $p->newPoint('joinHemRight', $p->x('ffsa-endPoint'), $p->y('sa-startPoint'));
            $p->newPath('saJoin', 'M sa-endPoint L ffsa-startPoint M ffsa-endPoint L joinHemRight L sa-startPoint', ['class' => 'sa fabric']);
        }

        // Title
        $p->clonePoint(2,'titleAnchor');
        $p->addTitle('titleAnchor', '5', $this->t($p->title), '2x '.$this->t('from fabric'));
        
        // Grainline
        $p->newPoint('grainlineTop', $p->x(14), $p->y(14));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('cuffRight')-10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notches
        // Distance to curveJoint at front
        $frontSplit = $p->curveLen(5,5,26,19) + $p->curveLen(19,27,11,11);
        if($frontSplit == $this->v('frontSleeveNotchDistance')) {
            // Falls at curve joint, just clone joint point 11
            $p->clonePoint(11, 'frontSleeveNotch'); 
        }
        else if ($frontSplit > $this->v('frontSleeveNotchDistance')) {
            // Closer to armhole, shift along curve before joint point 11
            $shift = $this->v('frontSleeveNotchDistance') - $p->curveLen(5,5,26,19);
            $p->addPoint( 'frontSleeveNotch', $p->shiftAlong(19,27,11,11,$shift));
        }
        else {
            // Closer to shoulder, shift along curve after joint point
            $shift = $this->v('frontSleeveNotchDistance') - ($p->curveLen(5,5,26,19) + $p->curveLen(19,27,11,11));
            $p->addPoint('frontSleeveNotch', $p->shiftAlong(11,11,24,18,$shift));
        }
        // Distance to curveJoint at back
        $backSplit = $p->curveLen(-5,-5,20,16) + $p->curveLen(16,21,10,10);
        if($backSplit == $this->v('backSleeveNotchDistance')) {
            // Falls at curve joint, just clone joint point 10
            $p->clonePoint(10, 'backSleeveNotch'); 
            $p->addPoint('backSleeveNotch1', $p->shiftAlong(10,10,21,16,2.5));
            $p->addPoint('backSleeveNotch2', $p->shiftAlong(10,10,22,17,2.5));
        }
        else if ($backSplit > $this->v('backSleeveNotchDistance')) {
            // Closer to armhole, shift along curve before joint point 11
            $shift = $this->v('backSleeveNotchDistance') - $p->curveLen(-5,-5,20,16);
            $p->addPoint( 'backSleeveNotch', $p->shiftAlong(16,21,10,10,$shift));
            $p->addPoint( 'backSleeveNotch2', $p->shiftAlong(16,21,10,10,$shift-1.5));
            // It is possible our shift brings us closer than 1.5 mm to the edge of the curve
            $len = $p->curveLen(16,21,10,10);
            if($len > $shift + 1.5) {
                $p->addPoint( 'backSleeveNotch1', $p->shiftAlong(16,21,10,10,$shift+1.5));
            } else {
                $p->addPoint( 'backSleeveNotch1', $p->shiftAlong(10,10,22,17,$shift + 1.5 - $len));
            }
        }
        else {
            // Closer to shoulder, shift along curve after joint point
            $shift = $this->v('backSleeveNotchDistance') - ( $p->curveLen(-5,-5,20,16) + $p->curveLen(16,21,10,10) );
            $p->addPoint('backSleeveNotch', $p->shiftAlong(10,10,22,17,$shift));
            $p->addPoint('backSleeveNotch1', $p->shiftAlong(10,10,22,17,$shift+1.5));
            // It is possible our shift brings us closer than 1.5 mm to the edge of the curve
            if($shift > 1.5) {
                $p->addPoint('backSleeveNotch2', $p->shiftAlong(10,10,22,17,$shift-1.5));
            } else {
                $p->addPoint('backSleeveNotch2', $p->shiftAlong(16,21,10,10, 1.5 - $shift));
            }
        }

        $notchHere = [411, 'sleevePlacketCutTop', 30, 'frontSleeveNotch', 'backSleeveNotch1', 'backSleeveNotch2'];
        if($this->v('cuffPleats') == 1) $notchAlso = ['pleatLeft', 'pleatCenter', 'pleatRight'];
        else $notchAlso = ['pleatOneLeft', 'pleatOneCenter', 'pleatOneRight','pleatTwoLeft', 'pleatTwoCenter', 'pleatTwoRight'];
        $notchHere = array_merge($notchHere, $notchAlso);
        $p->notch($notchHere);
    }

    /**
     * Finalizes the collar stand
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeCollarStand($model)
    {
        /** @var Part $p */
        $p = $this->parts['collarStand'];
       
        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'outline', $this->o('sa'), 1, ['class' => 'sa fabric']);

        // Button and buttonhole
        $p->newSnippet($p->newId('button'), 'button', -55, ['transform' => 'rotate('.(90-$p->angle(-32,-31)).' '.$p->x(-55).' '.$p->y(-55).')']);
        $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 55, ['transform' => 'rotate('.(90-$p->angle(32,31)).' '.$p->x(55).' '.$p->y(55).')']);

        // Grainline
        $p->newGrainline(-57, 57, $this->t('Grainline'));
        
        // Title
        $p->addPoint('titleAnchor', $p->shift(56,0,30));
        $p->addTitle('titleAnchor', 6, $this->t($p->title), '2x '.$this->t('from fabric')." + ".'2x '.$this->t('from interfacing'), 'horizontal-small');

        // Notches
        $p->addPoint('collarStandNotch1', $p->shiftAlong(42,43,6,61,$this->v('yokeCollarOpeningLength')/2));
        $p->addPoint('collarStandNotch2', $p->flipX('collarStandNotch1',0));
        $p->notch(['collarStandNotch1', 'collarStandNotch2',-32,32]);
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
        /** @var Part $p */
        $p = $this->parts['collar'];
       
        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'outline', $this->o('sa'), 1, ['class' => 'sa fabric']);

        // Grainline
        $p->newGrainline(-11, 11, $this->t('Grainline'));
        
        // Title
        $p->addPoint('titleAnchor', $p->shift(10,0,40));
        $p->addTitle('titleAnchor', 7, $this->t($p->title), '1x '.$this->t('from fabric'),'small');
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
        /** @var Part $p */
        $p = $this->parts['undercollar'];
       
        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'outline', $this->o('sa'), 1, ['class' => 'sa fabric']);

        // Grainline
        $p->newGrainline(-11, 11, $this->t('Grainline'));
        
        // Title
        $p->addPoint('titleAnchor', $p->shift(10,0,40));
        $p->addTitle('titleAnchor', 8, $this->t($p->title), '1x '.$this->t('from fabric'),'small');
    }

    /**
     * Finalizes the sleevePlacketUnderlap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSleevePlacketUnderlap($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleevePlacketUnderlap'];
       
        // Button
        $p->newSnippet($p->newId('button'), 'button', 12);

        // Seam allowance
        $p->newPath('sa', 'M 10 L 0 L 9 L 11', ['class' => 'sa fabric']); 

        // Title
        $p->addPoint('titleAnchor', $p->shift(8,0,25));
        $p->addTitle('titleAnchor', 10, $this->t($p->title), '2x '.$this->t('from fabric'),'horizontal-small');
    }

    /**
     * Finalizes the sleevePlacketOverlap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSleevePlacketOverlap($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleevePlacketOverlap'];

        // Seam allowance
        $p->newPath('sa', 'M 24 L 0 L 18 L 25', ['class' => 'sa fabric']); 
        // Buttonhole
        $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 30, ['transform' => 'rotate(90 '.$p->x(30).' '.$p->y(30).')']);
        
        // Title
        $p->addPoint('titleAnchor', $p->shift(20,-35,30));
        $p->addTitle('titleAnchor', 11, $this->t($p->title), '2x '.$this->t('from fabric'),'horizontal-small');
    }


    /**
     * Finalizes the barrelCuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBarrelCuff($model)
    {
        /** @var Part $p */
        $p = $this->parts['barrelCuff'];

        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'outline', $this->o('sa')*-1, 1, ['class' => 'sa fabric']);

        // Title
        $p->newPoint('titleAnchor', 0, $p->y(-8));
        $p->addTitle('titleAnchor', 11, $this->t($p->title), '4x '.$this->t('from fabric').' + 4x '.$this->t('from interfacing'), 'small');
    }

    /**
     * Finalizes the frenchCuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrenchCuff($model)
    {
        /** @var Part $p */
        $p = $this->parts['frenchCuff'];

        // Foldline
        $p->newPath('foldline', 'M -4 L 4', ['class' => 'foldline']);

        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'outline', $this->o('sa')*-1, 1, ['class' => 'sa fabric']);

        // Title
        $p->newPoint('titleAnchor', 0, $p->y(-3));
        $p->addTitle('titleAnchor', 11, $this->t($p->title), '4x '.$this->t('from fabric').' + 4x '.$this->t('from interfacing'));
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
     * Adds paperless info for the front right
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFrontRight($model)
    {
        /** @var Part $p */
        $p = $this->parts['frontRight'];

        // Widths
        $yBase = $p->y(2045);
        if($this->o('buttonPlacketType') == 1) {
            if($this->o('buttonPlacketStyle') == 1) {
                $p->newWidthDimensionSm(2045, 2044, $yBase+45);  // To placket fold
                $p->newWidthDimensionSm(4, 2044, $yBase+55);  // To buttons
                $p->newWidthDimensionSm(2040, 2044, $yBase+65);  // To placket seam
            } else {
                $p->newWidthDimensionSm(2046, 2044, $yBase+45);  // To first placket fold
                $p->newWidthDimensionSm(2045, 2044, $yBase+55);  // To second placket fold
                $p->newWidthDimensionSm(4, 2044, $yBase+65);  // To buttons
            }
        }
        
        if($this->o('buttonPlacketType') == 1) $rightEdge = 2044;
        else $rightEdge = 2040;
    
        $p->newWidthDimension(6663, $rightEdge, $yBase+80);  // Width at hem
        $p->newWidthDimension(8001, $rightEdge, $p->y(8001)+15);  // Width at hips
        $p->newWidthDimension(6021, $rightEdge, $p->y(6021)+15);  // Width at waist
        $p->newWidthDimension(5   , $rightEdge, $p->y(5)+15);  // Width at arm hole bottom
        $p->newWidthDimension(10  , $rightEdge, $p->y(10)+15);  // Width at arm pitch point
        $p->newWidthDimension(8   , $rightEdge, $p->y(8)-25);  // Width at the neck opening
        $p->newWidthDimension(12  , $rightEdge, $p->y(8)-40);  // Width to the shoulder point

        // Heights on the left
        $xBase = $p->x(6663)-20;
        if($this->o('hemStyle') == 2 || $this->o('hemStyle') == 3) {
            $xBase -= 15;
            $p->newHeightDimension(2045,6663,$xBase);  // Height of baseball/slashed hem
        }
        $p->newHeightDimension(2045,8000, $xBase-15);  // Height of the hip line
        $p->newHeightDimension(2045,6021, $xBase-30);  // Height of the waist line
        $p->newHeightDimension(2045,5, $xBase-45);  // Height of the armhole
        $p->newHeightDimension(2045,10, $xBase-60);  // Height of arm pitch point
        $p->newHeightDimension(2045,12, $xBase-75);  // Height of the shoulder point
        $p->newHeightDimension(2045,8, $xBase-90);  // Height total

        // Button heighs, only if placket is attached
        if($this->o('buttonPlacketType') == 1) {
            // First button
            $p->newHeightDimension(2045,3000, $p->x(2044)+25);  // Distance to next button
            // Next buttons
            for($i=1;$i<$this->o('buttons');$i++) {
                $pid = 2999+$i;
                $nid = 3000+$i;
                $p->newHeightDimension($pid, $nid, $p->x(2044)+25);  // Distance to next button
            }
            // Extra top button
            if($this->o('extraTopButton')) {
                $pid++;
                $nid++;
                $p->newHeightDimension($pid, $nid, $p->x(2044)+25);  // Distance to next button
            }
        
        }

        // Linear dimensions
        $p->newLinearDimension(12,8,-25);  // Shoulder length

        // Curve lengths
        $p->newCurvedDimension('M 5 C 13 16 14 C 15 18 10 C 17 19 12', -25);  // Armhole length
        if($this->o('buttonPlacketType') == 1) $p->newCurvedDimension('M 8 C 20 21 9', -25);  // Neckopening length
        else $p->newCurvedDimension('M 8 C 2051 2052 2153', -25);

        // Notes
        if($this->o('sa')) {
            $p->addPoint('saNoteAnchor', $p->shift(10,180,5));
            $p->newNote('saNote', 'saNoteAnchor', $this->t("Standard\nseam\nallowance"), 2, 40, 0);
            $p->addPoint('ffsaNoteAnchor', $p->shift(6021,180,10));
            $p->newNote('ffsaNote', 'ffsaNoteAnchor', $this->t("Flat-felled\nseam\nallowance")."\n(".$p->unit(Utils::constraint($this->o('sa')*2,12,25)).')', 2, 40, 0);
            $p->addPoint('hemNoteAnchor', $p->shift(6668,-90,15));
            $p->newNote('hemNote', 'hemNoteAnchor', $this->t("Hem\nseam\nallowance")."\n(".$p->unit(30).')', 12, 40, 0);
        }
    }

    /**
     * Adds paperless info for the front left
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFrontLeft($model)
    {
        /** @var Part $p */
        $p = $this->parts['frontLeft'];

        // Widths
        $yBase = $p->y(6660);
        if($this->o('buttonholePlacketType') == 1) {
            if($this->o('buttonholePlacketStyle') == 1) {
                $p->newWidthDimensionSm(4007, 4004, $yBase+45);  // To first fold
                $p->newWidthDimensionSm(4007, 4000, $yBase+55);  // To buttonholes
                $p->newWidthDimensionSm(4007, 4001, $yBase+65);  // To second fold
                $p->newWidthDimensionSm(4005, 4006, $yBase-10);  // First fold width
                $p->newWidthDimensionSm(4003, 4002, $yBase-10);  // Second fold width
            } else {
                $p->newWidthDimensionSm(4007, 4002, $yBase+45);  // To first placket fold
                $p->newWidthDimensionSm(4007, 4001, $yBase+55);  // To second placket fold
                $p->newWidthDimensionSm(4007, 4000, $yBase+65);  // To buttonholes
            }
        }
        if($this->o('buttonholePlacketType') == 1) $leftEdge = 4007;
        else $leftEdge = 4008;
    
        $p->newWidthDimension($leftEdge, 6663,$yBase+80);  // Width at hem
        $p->newWidthDimension($leftEdge, 8001,$p->y(8001)+15);  // Width at hips
        $p->newWidthDimension($leftEdge, 6021,$p->y(6021)+15);  // Width at waist
        $p->newWidthDimension($leftEdge, 5,$p->y(5)+15);  // Width at arm hole bottom
        $p->newWidthDimension($leftEdge, 10,$p->y(10)+15);  // Width at arm pitch point
        $p->newWidthDimension($leftEdge, 8,$p->y(8)-25);  // Width at the neck opening
        $p->newWidthDimension($leftEdge, 12,$p->y(8)-40);  // Width to the shoulder point

        // Heights on the left
        $xBase = $p->x(6663)+20;
        if($this->o('hemStyle') == 2 || $this->o('hemStyle') == 3) {
            $xBase += 20;
            $p->newHeightDimension(2045,6663,$xBase);  // Height of baseball/slashed hem
        }
        $p->newHeightDimension(2045,8000, $xBase+15);  // Height of the hip line
        $p->newHeightDimension(2045,6021, $xBase+30);  // Height of the waist line
        $p->newHeightDimension(2045,5, $xBase+45);  // Height of the armhole
        $p->newHeightDimension(2045,10, $xBase+60);  // Height of arm pitch point
        $p->newHeightDimension(2045,12, $xBase+75);  // Height of the shoulder point
        $p->newHeightDimension(2045,8, $xBase+90);  // Height total

        // Button heighs, only if placket is attached
        if($this->o('buttonholePlacketType') == 1) {
            // First button
            $p->newHeightDimension(2045,3000, $p->x(4007)-25);  // Distance to next button
            // Next buttons
            for($i=1;$i<$this->o('buttons');$i++) {
                $pid = 2999+$i;
                $nid = 3000+$i;
                $p->newHeightDimension($pid, $nid, $p->x(4007)-25);  // Distance to next button
            }
            // Extra top button
            if($this->o('extraTopButton')) {
                $pid++;
                $nid++;
                $p->newHeightDimension($pid, $nid, $p->x(4007)-25);  // Distance to next button
            }
        
        }

        // Linear dimensions
        $p->newLinearDimension(8,12,-25);  // Shoulder length

        // Curve lengths
        $p->newCurvedDimension('M 5 C 13 16 14 C 15 18 10 C 17 19 12', 25);  // Armhole length
        if($this->o('buttonholePlacketType') == 1) $p->newCurvedDimension('M 4100 L 9 C 21 20 8', -25);  // Neckopening length
        else $p->newCurvedDimension('M 4108 C 41092 41091 8', -25);

        // Notes
        if($this->o('sa')) {
            $p->addPoint('saNoteAnchor', $p->shift(10,0,5));
            $p->newNote('saNote', 'saNoteAnchor', $this->t("Standard\nseam\nallowance"), 10, 40, 0);
            $p->addPoint('ffsaNoteAnchor', $p->shift(6021,0,10));
            $p->newNote('ffsaNote', 'ffsaNoteAnchor', $this->t("Flat-felled\nseam\nallowance")."\n(".$p->unit(Utils::constraint($this->o('sa')*2,12,25)).')', 10, 40, 0);
            $p->addPoint('hemNoteAnchor', $p->shift(6668,-90,15));
            $p->newNote('hemNote', 'hemNoteAnchor', $this->t("Hem\nseam\nallowance")."\n(".$p->unit(30).')', 12, 40, 0);
        }
    }
    
    /**
     * Adds paperless info for the button placket
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessButtonPlacket($model)
    {
        /** @var Part $p */
        $p = $this->parts['buttonPlacket'];
        // Widths
        $p->newWidthDimensionSm(2040, 4, $p->y(2040)+45);  // To buttons
        $p->newWidthDimensionSm(2040, 2045, $p->y(2040)+55);  // To fold
        if($this->o('buttonPlacketStyle') == 2) {
            $p->newWidthDimensionSm(2040, 2046, $p->y(2040)+65);  // To second fold
        }
        $p->newWidthDimension(2040, 2044, $p->y(2040)+75);  // To second fold
        
        // Button heighs
        // First button
        $p->newHeightDimension(2044,3000, $p->x(2044)+25);  // Distance to first button
        // Next buttons
        for($i=1;$i<$this->o('buttons');$i++) {
            $pid = 2999+$i;
            $nid = 3000+$i;
            $p->newHeightDimension($pid, $nid, $p->x(2044)+25);  // Distance to next button
        }
        // Extra top button
        if($this->o('extraTopButton')) {
            $pid++;
            $nid++;
            $p->newHeightDimension($pid, $nid, $p->x(2044)+25);  // Distance to next button
        }

        // Height
        $p->newHeightDimension(2044, 9, $p->x(2044)+40);  // Total height
    }
    
    /**
     * Adds paperless info for the buttonhole placket
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessButtonholePlacket($model)
    {
        /** @var Part $p */
        $p = $this->parts['buttonholePlacket'];
        
        // Widths
        if($this->o('buttonholePlacketStyle') == 2) {
            $p->newWidthDimensionSm(4007, 4002, $p->y(4002)+45);  // To buttonholes
            $p->newWidthDimensionSm(4007, 4001, $p->y(4002)+55);  // To fold
            $p->newWidthDimensionSm(4007, 4, $p->y(4002)+65);  // To second fold
            $p->newWidthDimension(4007, 4008, $p->y(4002)+80);  // To second fold
        } else {
            $p->newWidthDimensionSm(4007, 4004, $p->y(4007)+45);  // To first fold
            $p->newWidthDimensionSm(4007, 4000, $p->y(4007)+55);  // To buttonholes
            $p->newWidthDimensionSm(4007, 4003, $p->y(4007)+65);  // To second fold
            $p->newWidthDimension(4007, 4008, $p->y(4007)+80);  // To second fold

        }
        
        // Button heighs
        // First button
        $p->newHeightDimension(4008,3000, $p->x(4008)+25);  // Distance to first button
        // Next buttons
        for($i=1;$i<$this->o('buttons');$i++) {
            $pid = 2999+$i;
            $nid = 3000+$i;
            $p->newHeightDimension($pid, $nid, $p->x(4008)+25);  // Distance to next button
        }
        // Extra top button
        if($this->o('extraTopButton')) {
            $pid++;
            $nid++;
            $p->newHeightDimension($pid, $nid, $p->x(4008)+25);  // Distance to next button
        }

        // Height
        $p->newHeightDimension(4008, 9, $p->x(4008)+40);  // Total height
    }
    
    /**
     * Adds paperless info for the yoke
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessYoke($model)
    {
        /** @var Part $p */
        $p = $this->parts['yoke'];

        // Widths
        if($this->o('splitYoke') == 1) { // Split yoke
            $p->newWidthDimension('centerBottom', 10, $p->y(10)+25);  // Total width
            $p->newWidthDimension(1, 8, $p->y(8)-25);  // Neck cutout width
            $p->newWidthDimension(1, 12, $p->y(8)-40);  // Shoulder width
            $p->newHeightDimensionSm(1, 8, $p->x(1)-25);  // Neck cutout depth
        } else {
            $p->newWidthDimension(-10, 10, $p->y(10)+25);  // Total width
            $p->newWidthDimension(-8, 8, $p->y(8)-25);  // Neck cutout width
            $p->newWidthDimension(-12, 12, $p->y(8)-40);  // Shoulder width
            $p->newHeightDimensionSm(1, -8, $p->x(1));  // Neck cutout depth
        }

        // Heights
        $p->newHeightDimension(10, 12, $p->x(10)+40);  // Shoulder height
        $p->newHeightDimension(10, 8, $p->x(10)+55);  // Total height

        // Linear
        $p->newLinearDimension(8, 12, -15);  // Shoulder length

        // Curved
        $p->newCurvedDimension('M 10 C 17 19 12', 25);  // Armhole curve length

    }
    
    /**
     * Adds paperless info for the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBack($model)
    {
        /** @var Part $p */
        $p = $this->parts['back'];

        // Widths  
        $p->newWidthDimension(8000, 8001, $p->y(8001)+15);  // Hips width
        $p->newPoint('6663Center', 0, $p->y(6663));
        $p->newWidthDimension('6663Center', 6663, $p->y(4)+$this->o('sa')*3+15);  // Total width
        $p->newWidthDimension(3, 6021, $p->y(6021)-15);  // Waist width
        if($this->o('yokeDart') > 0) $p->newWidthDimension('centerTop', 'yokeDart1', $p->y(10)-25);  // Across back width
        else $p->newWidthDimension('centerTop',10, $p->y(10)-25);  // Across back width
        $p->newWidthDimension(2, 5, $p->y(10)-40);  // Underarm width

        // Heights
        if($this->o('hemStyle') == 3) $xBase = $p->x(6663) +45;
        else $xBase = $p->x(6663) +25;
        if($this->o('hemStyle') > 1) {
            $p->newHeightDimension(6666, 6663, $xBase);  // Hem curve height
            $xBase += 15;
        }
        $p->newHeightDimension(6666, 8001, $xBase);  // Hips height
        $p->newHeightDimension(6666, 6021, $xBase+15);  // Waist height
        $p->newHeightDimension(6666, 5, $xBase+30);  // Armhole height
        if($this->o('yokeDart') > 0) {
            $p->newHeightDimension(5,'yokeDart2', $xBase+30);  // Armhole height
            $p->newHeightDimension(6666,'yokeDart2', $xBase+45);  // Total height
            // Yoke dart
            $p->newHeightDimensionSm('yokeDart1','yokeDart2', $p->x('yokeDart2'));  // Yoke dart height
            $p->newWidthDimensionSm('yokeDart2','yokeDart1', $p->y('yokeDart1')+10);  // Yoke dart width
            $curveEnd = 'yokeDart1';
        } else {
            $p->newHeightDimension(5,10, $xBase+30);  // Armhole height
            $p->newHeightDimension(6666,10, $xBase+45);  // Total height
            $curveEnd = 10;
        }

        // Darts
        if($p->isPoint(6100)) { // Do we have darts?
            $p->newWidthDimension(8000, 6300, $p->y(6300)+15);  // Distance between darts
            $p->newLinearDimensionSm(6122, 6121);  // Right dart width
            $p->newHeightDimension(6300, 6121, $p->x(6121)+15);  // Dart bottom half height
            $p->newHeightDimension(6121, 6110, $p->x(6121)+15);  // Dart bottom half height
        }

        // Armhole curve
        $p->newCurvedDimension("M 5 C 13 16 14 C 15 18 $curveEnd", 25);  // Dart bottom half height

        // Notes
        if($this->o('sa')) {
            $p->addPoint('saNoteAnchor', $p->shift(6021,-65,10));
            $p->newNote('saNote', 'saNoteAnchor', $this->t("Standard\nseam\nallowance"), 8, 40, 0);
            $p->addPoint('hemNoteAnchor', $p->shift(6660,-155,30));
            $p->newNote('hemNote', 'hemNoteAnchor', $this->t("Hem\nseam\nallowance")."\n(".$p->unit(30).')', 10, 60, 0);
        }
    }

    /**
     * Adds paperless info for the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSleeve($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleeve'];

        // Helplines
        $p->newPath('sleeveHead1', 'M -5 L 5', ['class' => 'help fabric']);
        $p->newPath('sleeveHead2', 'M 1 L 2', ['class' => 'help fabric']);
        // Cuff
        $p->newWidthDimension('cuffLeft', 411, $p->y('cuffLeft')+25); // To placket cut
        $p->newHeightDimension(411, 'sleevePlacketCutTop', $p->x('cuffLeft')-35); // To placket cut
        
        if($this->v('cuffPleats') == 1) {
            $p->newWidthDimensionSm('pleatLeftTop', 'pleatRightTop', $p->y('pleatRightTop')-10); // Pleat width
            $p->newWidthDimension('cuffLeft', 'pleatLeft', $p->y('cuffLeft')+40); // To pleat
            $p->newWidthDimension('cuffLeft', 'cuffRight', $p->y('cuffLeft')+55); // Cuff width
        } else {
            $p->newWidthDimensionSm('pleatOneLeftTop', 'pleatOneRightTop', $p->y('pleatOneRightTop')-10); // Pleat width
            $p->newWidthDimensionSm('pleatTwoLeftTop', 'pleatTwoRightTop', $p->y('pleatTwoRightTop')-10); // Pleat width
            $p->newWidthDimension('cuffLeft', 'pleatOneLeft', $p->y('cuffLeft')+40); // To pleat 1
            $p->newWidthDimension('cuffLeft', 'pleatTwoLeft', $p->y('cuffLeft')+55); // To pleat 2
            $p->newWidthDimension('cuffLeft', 'cuffRight', $p->y('cuffLeft')+70); // Cuff width
        }

        // Heights
        $p->newHeightDimension('cuffRight', 5, $p->x(5)+35); // To sleevehead
        $p->newHeightDimension('cuffRight', 1, $p->x(5)+50); // Total height

        // Sleevehead
        $p->newWidthDimension(1,'frontSleeveNotch', $p->y('frontSleeveNotch')+15); // Front notch width
        $p->newWidthDimension('backSleeveNotch',1, $p->y('frontSleeveNotch')+15); // Back notch width
        $p->newHeightDimension('backSleeveNotch',1, $p->x(28)); // Back notch height
        $p->newHeightDimension('frontSleeveNotch',1, $p->x(29)); // Back notch height
        $p->newWidthDimensionSm(1,30, $p->y(1)-10); // Shoulder notch
        $p->newWidthDimension(-5,5, $p->y(1)-45); // Sleeve head width
        $p->newCurvedDimension('M -5 C -5 20 16 C 21 10 10 C 10 22 17 C 23 28 1', -35);
        $p->newCurvedDimension('M 1 C 29 25 18 C 24 11 11 C 11 27 19 C 26 5 5', -35);
        
        // Notes
        if($this->o('sa')) {
            $p->addPoint('saNoteAnchor', $p->shift(-5,-120,10));
            $p->newNote('saNote', 'saNoteAnchor', $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 4, 40, 0);
            $p->addPoint('ffsaNoteAnchor', $p->shift(5,-30,10));
            $p->newNote('ffsaNote', 'ffsaNoteAnchor', $this->t("Flat-felled\nseam\nallowance")."\n(".$p->unit(Utils::constraint($this->o('sa')*2,12,25)).')', 8, 40, 0);
        }
    }

    /**
     * Adds paperless info for the collar stand
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCollarStand($model)
    {
        /** @var Part $p */
        $p = $this->parts['collarStand'];

        // Length
        $p->newLinearDimensionSm(-31,-32,25);
        $p->newLinearDimensionSm(32,31,25);
        $p->newCurvedDimension('M -32 C -32 -62 -61 C -6 -43 -42 C 43 6 61 C 62 33 32', 55);
    
        $p->newWidthDimension('collarStandNotch2', 'collarStandNotch1', $p->y(61)+25);
        $p->newWidthDimension(-31, 31, $p->y(61)+40);
        $p->newHeightDimension(-42,12,$p->x(12));
        $p->newHeightDimensionSm(-61,42,$p->x(12));
        $p->newHeightDimensionSm(-61,-31,$p->x(-31)-15);

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
        /** @var Part $p */
        $p = $this->parts['collar'];

        $p->newHeightDimensionSm(5,3,$p->x(3));
        $p->newHeightDimensionSm(-4,5,$p->x(3));
        $p->newHeightDimensionSm(3,-8,$p->x(3));
        
        $p->newWidthDimension(-4,4,$p->y(-4)+25);
        $p->newWidthDimension(-8,8,$p->y(-8)-15);

        $p->newLinearDimension(4,8,25);
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
        /** @var Part $p */
        $p = $this->parts['undercollar'];

        $p->newHeightDimensionSm(5,3,$p->x(3));
        $p->newHeightDimensionSm(-4,5,$p->x(3));
        $p->newHeightDimensionSm(3,-8,$p->x(3));
        
        $p->newWidthDimension(-4,4,$p->y(-4)+25);
        $p->newWidthDimension(-8,8,$p->y(-8)-15);

        $p->newLinearDimension(4,8,25);
    }
    
    /**
     * Adds paperless info for the sleeve placket underlap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSleevePlacketUnderlap($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleevePlacketUnderlap'];

        $p->newWidthDimensionSm(9,11,$p->y(9)+15);
        $p->newWidthDimension(9,12,$p->y(9)+25);
        $p->newWidthDimension(9,5,$p->y(9)+40);
        $p->newHeightDimensionSm(11,12,$p->x(12)+15);
        $p->newHeightDimensionSm(5,1,$p->x(1)+25);
        $p->newHeightDimensionSm(5,4,$p->x(1)+15);
        $p->newHeightDimensionSm(4,3,$p->x(1)+15);
        $p->newHeightDimensionSm(3,2,$p->x(1)+15);
        $p->newHeightDimensionSm(2,1,$p->x(1)+15);

        $p->addPoint('noteAnchor', $p->shift(5,180,50));
        $p->newNote('saNote', 'noteAnchor', $this->t("No\nseam\nallowance"), 12, 20, 0);
    }
    
    /**
     * Adds paperless info for the sleeve placket overlap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSleevePlacketOverlap($model)
    {
        /** @var Part $p */
        $p = $this->parts['sleevePlacketOverlap'];

        $p->newWidthDimensionSm(18,25,$p->y(18)+10);
        $p->newWidthDimensionSm(14,13,$p->y(14)+15);
        $p->newWidthDimensionSm(17,13,$p->y(17)+15);
        $p->newWidthDimension(18,30,$p->y(16)+15);
        $p->newWidthDimension(16,13,$p->y(16)+15);
        $p->newWidthDimension(18,16,$p->y(16)+30);
        $p->newWidthDimension(18,13,$p->y(16)+45);
        
        $p->newHeightDimensionSm(13,3,$p->x(1)+15);
        $p->newHeightDimensionSm(3,2,$p->x(1)+15);
        $p->newHeightDimensionSm(2,1,$p->x(1)+15);
        $p->newHeightDimensionSm(17,13,$p->x(1)+15);
        $p->newHeightDimensionSm(16,17,$p->x(1)+15);
        $p->newHeightDimension(16,1,$p->x(1)+30);
        $p->newHeightDimensionSm(30,24,$p->x(30)+15);

        $p->addPoint('noteAnchor', $p->shift(16,180,50));
        $p->newNote('saNote', 'noteAnchor', $this->t("No\nseam\nallowance"), 12, 20, 0);

    }
    
    /**
     * Adds paperless info for the barrel cuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBarrelCuff($model)
    {
        /** @var Part $p */
        $p = $this->parts['barrelCuff'];
    
        if($this->o('cuffButtonRows') == 2) {
            $p->newWidthDimensionSm(-4,-8,$p->y(-4)+25);
            $p->newWidthDimensionSm(8,4,$p->y(-4)+25);
            $p->newHeightDimensionSm(4,8,$p->x(4)+20);
            $p->newHeightDimensionSm(8,7,$p->x(4)+20);
            if($this->o('barrelcuffNarrowButton') == 1) {
                $p->newWidthDimensionSm(11,8,$p->y(-4)+25);
            }
        } else {
            $p->newWidthDimensionSm(-4,-6,$p->y(-4)+25);
            $p->newWidthDimensionSm(6,4,$p->y(-4)+25);
            $p->newHeightDimensionSm(4,6,$p->x(4)+20);
            if($this->o('barrelcuffNarrowButton') == 1) {
                $p->newWidthDimensionSm(9,6,$p->y(-4)+25);
            }
        }
        $p->newWidthDimension(-4,4,$p->y(4)+40);
        
        if($this->o('cuffStyle') < 3) {
            $p->newWidthDimensionSm(-12,-13,$p->y(13)-20);
            $p->newHeightDimensionSm(-12,-13,$p->x(-12)-20);
            $p->newHeightDimension(4,13,$p->x(4)+35);
        } else {
            $p->newHeightDimension(4,2,$p->x(4)+35);
        }
    }
    
    /**
     * Adds paperless info for the French cuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFrenchCuff($model)
    {
        /** @var Part $p */
        $p = $this->parts['frenchCuff'];
    
        if($this->o('cuffStyle') < 6) {
            $p->newWidthDimensionSm(-17,-18,$p->y(-18)+20);
            $p->newHeightDimensionSm(-18,-17,$p->x(-17)-20);
        }
        
        $p->newWidthDimensionSm(7,17,$p->y(18)+20);
        $p->newHeightDimensionSm(18,7,$p->x(17)+20);
        $p->newHeightDimensionSm(6,13,$p->x(17)+20);
        
        $p->newWidthDimension(-17,17,$p->y(18)+35);
        $p->newHeightDimension(18,13,$p->x(17)+35);
    }
}

