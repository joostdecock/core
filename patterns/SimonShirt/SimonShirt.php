<?php
/** Freesewing\Patterns\SimonShirt class */
namespace Freesewing\Patterns;

use Freesewing\Part;

/**
 * The Simon Shirt  pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SimonShirt extends JoostBodyBlock
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
        /**
         *  Armhole Depth 
         *
         *  You can lower the armhole depth with the armholeDrop option
         */
        $this->setValue('armholeDepth',
            200
            + ( $model->m('shoulderSlope') / 2 -27.5 ) 
            + ( $model->m('bicepsCircumference') / 10 )
            + $this->o('armholeDrop')
        );

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
        $this->draftSleeveBlock($model);

        // Don't render blocks from parent pattern
        $this->parts['backBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['sleeveBlock']->setRender(false);

        // Now let's make that into a shirt
        $this->draftFrontRight($model);

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
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontRight($model)
    {
        $this->clonePoints('frontBlock', 'frontRight');
        
        /** @var Part $p */
        $p = $this->parts['frontRight'];

        // Front block is drafted as the left half, so every point needs to be mirrored
        foreach($p->points as $i => $point) $p->addPoint($i, $p->flipX($i,0));

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
        $p->addSplitCurve(8,20,21,9,2041,'splitNeckCurve');
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
        if($this->o('buttonPlacketType')==1) $p->newSnippet($p->newId('button'), 'button', 3000);
        // Next buttons
        for($i=1;$i<$this->o('buttons');$i++) {
          $pid = 3000+$i;
          $p->addPoint($pid, $p->shift(3000, 90, $buttonSpacing * $i), 'Button');
          if($this->o('buttonPlacketType')==1) $p->newSnippet($p->newId('button'), 'button', $pid);
        }
        // Extra top button
        if($this->o('extraTopButton')) {
          $extrapid = $pid +1;
          $p->addPoint($extrapid, $p->shift($pid,90,$buttonSpacing/2), 'Extra button');
          if($this->o('buttonPlacketType')==1) $p->newSnippet($p->newId('button'), 'button', $extrapid);
        }
        // Shaping of side seam
        $p->addPoint(8000, $p->shift(4,90,$this->o('lengthBonus')), 'Hips height');        
        $p->newPoint(8001, $p->x(6), $p->y(8000), 'Hips height');        
        if ($this->v('waistReduction') <= 100) { 
            // Only shape side seams if we're reducing less than 10cm
            $in = $this->v('waistReduction')/4;
            $hin = ($this->v('hipsReduction'))/4;
        } else { 
            // Also add cack darts if we're reducing 10cm or more
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
        $p->newPoint( 6663 , $p->x(8001)-$this->o('hipFlare')/4,$p->y(8001)+$this->o('lengthBonus')-$this->o('hemCurve'));
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

        // Construct paths
        if($this->o('buttonPlacketType') == 2) $seamline = 'M 2041 L 2040 '; // Separate button placket
        else { // Cut-on button placket
            switch($this->o('buttonPlacketStyle')) {
                case 1: 
                    // Classic style placket
                    $seamline = 'M 9 L -2017 C -2051 -2052 -2053 L 2043 L 2044 ';
                    $plackethelp = 'M 2153 2040';
                    $p->newPath('placketHelp', 'M 2153 L 2040', ['class' => 'helpline']);
                    break;
                case 2: 
                    // Seamless or French style placket
                    $seamline = 'M 9 L -2017 C -2051 -2052 -2053 C -2055 -2054 -2056 L 2043 L 2044 ';
                    $p->newPath($p->newId('buttonPlacketFold'), 'M -2053 L 2046', ['class' => 'foldline']);
                    break;
            }
        }

        $seamline .= 'L 6669 C 6668 6667 6666 C 6665 6664 6663 C 6662 6661 8001 C 8002 6031 6021 C 6011 5001 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 ';
        
        if($this->o('buttonPlacketType') == 2) $seamline .= 'C 2051 2052 2041 z'; // Separate button placket
        else { // Cut-on button placket
            $seamline .= 'C 20 21 9 z'; 
            $p->newPath($p->newId('buttonPlacketHelp'),'M 4 L 9', ['class' => 'helpline']);
            $p->newPath($p->newId('buttonPlacketFold'),'M 2042 L 2045', ['class' => 'foldline']);
        }
        
        // Add paths to part
        
        // Helplines
        $p->newPoint('acrossBackHeight', $p->x(9), $p->y(10), 'Across back height at button line');
        $p->newPath('acrossBackLine', 'M acrossBackHeight L 10', ['class' => 'helpline']);
        $p->newPath('chestLine', 'M 5 L 2', ['class' => 'helpline']);
        $p->newPath('waistLine', 'M 6021 L 3', ['class' => 'helpline']);
        $p->newPath('hipsLine', 'M 8001 L 8003', ['class' => 'helpline']);
        
        // Seamline path 
        $p->newPath('seamline',$seamline);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);

        // FIXME Handle this later with SA
        if($this->o('hemStyle') == 1) $flatFelledSideSeam = 'M 8001 M 5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663 L 6667';
        else $flatFelledSideSeam = 'M 8001 M 5 C 5001 6011 6021 C 6031 8002 8001 C 6661 6662 6663';
        $p->newPath('flatFelledSideSeam',$flatFelledSideSeam, ['class' => 'debug']);
/*
        Legacy points conversion
        -4 = 4
        -17 = 9
        -501 = 13
        -9 = 20
        -171 = 21
 */
    }

    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/measurements and so on
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
    }
}
