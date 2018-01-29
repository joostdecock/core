<?php
/** Freesewing\Patterns\Beta\CarltonCoat class */
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
class CarltonCoat extends BentBodyBlock
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /** Length bonus is irrelevant */
    const LENGTH_BONUS = 0;

    /** Hem from waist factor is always 69% */
    const HEM_FROM_WAIST_FACTOR = 0.69;

    /** Tailfold width is 11.36% of waist */
    const TAILFOLD_WAIST_FACTOR = 0.1136;

    /** Collar mid height is 9.6% of the chest circumferce */
    const COLLAR_MID_HEIGHT_FACTOR = 0.096;

    /** Collar edge height is 8.6% of the chest circumferce */
    const COLLAR_EDGE_HEIGHT_FACTOR = 0.086;

    /**Cuff height is 15% of sleeve length */
    const CUFF_LENGTH_FACTOR = 0.15;
    
    /** Belt height is 7cm */
    const BELT_HEIGHT = 70;

    /** Vertical button distance = 5.425% of chest circumference */
    const VERTICAL_BUTTON_DIST = 0.05425;

    /** Horizontal button distance = 9.43% of chest circumference */
    const HORIZONTAL_BUTTON_DIST = 0.0943;

    /** Belt width = 7cm */
    const BELT_WIDTH = 70;

    /** Main pocket width = 18.85% of chest circumference */
    const MAIN_POCKET_WIDTH = 0.1885;

    /** Main pocket height = 21.5% of chest circumference */
    const MAIN_POCKET_HEIGHT = 0.215;

    /** Main pocket radius = 5cm */
    const MAIN_POCKET_RADIUS = 50;

    /** Main pocket flap height = 7.78% of chest circumference */
    const MAIN_POCKET_FLAP_HEIGHT = 0.0778;

    /** Main pocket flap radius = 3cm */
    const MAIN_POCKET_FLAP_RADIUS = 30;

    /** Chest pocket width = 3.9% of chest circumference */
    const CHEST_POCKET_WIDTH = 0.039;

    /** Chest pocket height = 17.36% of chest circumference */
    const CHEST_POCKET_HEIGHT = 0.1736;

    /** Chest pocket rotation = 4 degrees */
    const CHEST_POCKET_ROTATION = 4;

    /* Distance between pocket and chest pocket = 6.6% of chest */
    const INTER_POCKET_DISTANCE = 0.066;

    /**
     * Sets up options and values for our draft
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
        // The (grand)parent pattern's lengthBonus is irrelevant here
        $this->setOptionIfUnset('lengthBonus', self::LENGTH_BONUS);
        
        // Fix the hemFromWaistFactor to 69%
        $this->setOptionIfUnset('hemFromWaistFactor', self::HEM_FROM_WAIST_FACTOR);

        // Make collarMidHeightFactor 9.6% of chest circumference
        $this->setOptionIfUnset('collarMidHeightFactor', self::COLLAR_MID_HEIGHT_FACTOR);
        
        // Make collarEdgeHeightFactor 8.6% of chest circumference
        $this->setOptionIfUnset('collarEdgeHeightFactor', self::COLLAR_EDGE_HEIGHT_FACTOR);
        
        // Make shoulderToShoulder measurement larger because coat
        $model->setMeasurement('shoulderToShoulder', $model->m('shoulderToShoulder') + $this->o('shoulderEase'));
        
        // Make acrossBack measurement larger because coat
        $model->setMeasurement('acrossBack', $model->m('acrossBack') + $this->o('shoulderEase'));

        // Waist shaping
        $this->setValueIfUnset('waistReduction', 
            ( $model->m('chestCircumference') + $this->o('chestEase') ) - 
            ( $model->m('naturalWaist') + $this->o('waistEase') ) 
        );
        // Percentage of the waistReduction that's handled in the side seams
        $this->setValueIfUnset('waistSideShapeFactor', 0.5);
        $this->setValueIfUnset('waistReductionSide', $this->v('waistReduction') * $this->v('waistSideShapeFactor') / 8);
        $this->setValueIfUnset('waistReductionBack', $this->v('waistReduction') * (1-$this->v('waistSideShapeFactor')) / 8);

        // Distance between buttons
        $this->setValueIfUnset('buttonDistHor', ($model->m('chestCircumference') * self::VERTICAL_BUTTON_DIST));
        $this->setValueIfUnset('buttonDistVer', ($model->m('chestCircumference') * self::HORIZONTAL_BUTTON_DIST));

        // Fix the tailfoldWaistFactor to 11.36%
        $this->setValueIfUnset('tailfoldWaistFactor', self::TAILFOLD_WAIST_FACTOR);

        // Width of the belt
        $this->setValueIfUnset('beltWidth', self::BELT_WIDTH);

        // Width of the main pockets = 18.85% of chest
        $this->setValueIfUnset('pocketWidth', $model->m('chestCircumference') * self::MAIN_POCKET_WIDTH);
        
        // Height of the main pockets = 21.5% of chest
        $this->setValueIfUnset('pocketHeight', $model->m('chestCircumference') * self::MAIN_POCKET_HEIGHT);
        
        // Radius of the pockets at the bottom
        $this->setValueIfUnset('pocketRadius', self::MAIN_POCKET_RADIUS);
        
        // Height of the pocket flap = 7.78% of chest
        $this->setValueIfUnset('pocketFlapHeight', $model->m('chestCircumference') * self::MAIN_POCKET_FLAP_HEIGHT);
        
        // Radius of the pocket flap at the bottom
        $this->setValueIfUnset('pocketFlapRadius', self::MAIN_POCKET_FLAP_RADIUS);
        
        // Width of the chest pocket = 3.9% of chest
        $this->setValueIfUnset('chestPocketWidth', $model->m('chestCircumference') * self::CHEST_POCKET_WIDTH);
        
        // Height of the chest pocket = 17.36% of chest
        $this->setValueIfUnset('chestPocketHeight', $model->m('chestCircumference') * self::CHEST_POCKET_HEIGHT);
        
        // Rotation of the chest pocket = 4 degrees
        $this->setValueIfUnset('chestPocketRotation', self::CHEST_POCKET_ROTATION);

        // Distance between pocket and chest pocket = 6.6% of chest
        $this->setValueIfUnset('pocketDistance', $model->m('chestCircumference') * self::INTER_POCKET_DISTANCE);

        // Cuff length
        $this->setValueIfUnset('cuffLengthFactor' , self::CUFF_LENGTH_FACTOR);
        $this->setValueIfUnset('cuffLength' , $model->m('shoulderToWrist') * $this->v('cuffLengthFactor'));
        
        // Belt height
        $this->setValueIfUnset('beltHeight' , self::BELT_HEIGHT);

        // Add ease to the accross back measurement
        $model->setMeasurement('acrossBack', $model->m('acrossBack') + $this->o('chestEase')/6);
        
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

        // Get to work
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        $this->draftSleeveBlock($model);
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
         
        $this->draftFront($model);
        $this->draftBack($model);
        $this->draftTail($model);

        $this->draftCollar($model);
        $this->draftCollarStand($model);

        $this->draftTopsleeve($model);
        $this->draftUndersleeve($model);
        $this->draftCuffFacing($model);
        $this->draftBelt($model);

        $this->draftPocket($model);
        $this->draftPocketFlap($model);
        $this->draftChestPocketWelt($model);
        
        // Hide the sleeveBlocks, frontBlock, and backBlock
        $this->parts['sleeveBlock']->setRender(false);
        $this->parts['topsleeveBlock']->setRender(false);
        $this->parts['undersleeveBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
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
        
        $this->finalizeFront($model);
        $this->finalizeBack($model);
        $this->finalizeTail($model);
        
        $this->finalizeTopsleeve($model);
        $this->finalizeUndersleeve($model);
        
        $this->finalizeBelt($model);
        $this->finalizeCollarStand($model);
        $this->finalizeCollar($model);
        $this->finalizeCuffFacing($model);
        $this->finalizePocket($model);
        $this->finalizePocketFlap($model);
        $this->finalizeChestPocketWelt($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            $this->paperlessFront($model);
            $this->paperlessBack($model);
            $this->paperlessTail($model);
            $this->paperlessTopsleeve($model);
            $this->paperlessUndersleeve($model);
            $this->paperlessBelt($model);
            $this->paperlessCollarStand($model);
            $this->paperlessCollar($model);
            $this->paperlessCuffFacing($model);
            $this->paperlessPocket($model);
            $this->paperlessPocketFlap($model);
            $this->paperlessChestPocketWelt($model);
        }
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
        $this->clonePoints('frontBlock','front');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Hem length
        $p->newPoint('hemMiddle', $p->x(4), $p->y(3) + $model->m('naturalWaistToFloor') * $this->o('hemFromWaistFactor'));
        $p->newPoint('hemSide', $p->x(5), $p->y('hemMiddle'));

        // Waist shaping
        $p->newPoint('waistSide', $p->x(5) - $this->v('waistReductionSide'), $p->y(3));
        $p->addPoint('waistSideCpTop', $p->shift('waistSide', 90, $p->deltaY(5,3)/2));
        $p->addPoint('waistSideCpBottom', $p->flipY('waistSideCpTop', $p->y('waistSide')));
        $p->addPoint('chestSideCp', $p->shift(5,-90,$p->deltaY(5,'waistSideCpTop')/8));

        // Seat
        $p->newPoint('seatSide', $p->x(3) + ($model->m('seatCircumference') + $this->o('seatEase'))/4, $p->y(4) + $model->m('naturalWaistToSeat') );
        $p->addPoint('seatSideCpTop', $p->shift('seatSide', 90, $p->deltaY(4,'seatSide')/2));

        // Buttonline
        //$this->setValue('buttonDistVer', $p->deltaY(4,5)/2.5);
        $p->newPoint('button1Left', $p->x(4) - $this->v('buttonDistHor'), $p->y(4));
        $p->addPoint('button2Left', $p->shift('button1Left',90,$this->v('buttonDistVer')*1));
        $p->addPoint('button3Left', $p->shift('button1Left',90,$this->v('buttonDistVer')*2));
        $p->addPoint('button1Right', $p->flipX('button1Left',$p->x(4)));
        $p->addPoint('button2Right', $p->flipX('button2Left',$p->x(4)));
        $p->addPoint('button3Right', $p->flipX('button3Left',$p->x(4)));

        // Front center edge
        $p->addPoint('frontEdge', $p->shift('button1Left',180,25));

        // Hem
        $p->newPoint('hemSide', $p->x('seatSide'), $p->y('hemMiddle')); 
        $p->newPoint('hemFrontEdge', $p->x('frontEdge'), $p->y('hemMiddle')); 

        // Collar
        $p->newPoint('collarEdge', $p->x('frontEdge'), $p->y(9));
        $p->addPoint('collarTip', $p->shift('collarEdge',0,$this->v('buttonDistHor')/11.5));
        $p->newPoint('collarBendPoint', $p->x('collarEdge'), $p->y(5));
        $p->addPoint('collarBendPointCpTop', $p->shift('collarBendPoint',90,$p->deltaY('collarEdge','collarBendPoint')*0.8));

        // Pocket
        $p->newPoint('pocketTopLeft', $p->x('button1Right')+25, $p->y('button1Right')-12.5);
        $p->addPoint('pocketTopRight', $p->shift('pocketTopLeft', 0, $this->v('pocketWidth')));
        $p->addPoint('pocketBottomLeft', $p->shift('pocketTopLeft', -90, $this->v('pocketHeight')));
        $p->addPoint('pocketBottomRight', $p->shift('pocketTopRight', -90, $this->v('pocketHeight')));
        $p->addPoint('pocketBottomLeftTop', $p->shift('pocketBottomLeft', 90, $this->v('pocketRadius')));
        $p->addPoint('pocketBottomRightTop', $p->shift('pocketBottomRight', 90, $this->v('pocketRadius')));
        $p->addPoint('pocketBottomLeftRight', $p->shift('pocketBottomLeft', 0, $this->v('pocketRadius')));
        $p->addPoint('pocketBottomRightLeft', $p->shift('pocketBottomRight', 180, $this->v('pocketRadius')));
        $p->addPoint('pocketBottomLeftTopCp', $p->shift('pocketBottomLeftTop', -90, \Freesewing\BezierToolbox::bezierCircle($this->v('pocketRadius'))));
        $p->addPoint('pocketBottomLeftRightCp', $p->shift('pocketBottomLeftRight', 180, \Freesewing\BezierToolbox::bezierCircle($this->v('pocketRadius'))));
        $p->addPoint('pocketBottomRightTopCp', $p->shift('pocketBottomRightTop', -90, \Freesewing\BezierToolbox::bezierCircle($this->v('pocketRadius'))));
        $p->addPoint('pocketBottomRightLeftCp', $p->shift('pocketBottomRightLeft', 0, \Freesewing\BezierToolbox::bezierCircle($this->v('pocketRadius'))));

        // Pocket flap
        $rise = 12.5;
        $round = \Freesewing\BezierToolbox::bezierCircle($this->v('pocketFlapRadius'));
        $p->newPoint('pocketFlapTopLeft', $p->x('pocketTopLeft'), $p->y('pocketTopLeft')-$rise);
        $p->addPoint('pocketFlapTopRight', $p->shift('pocketFlapTopLeft', 0, $this->v('pocketWidth')));
        $p->addPoint('pocketFlapBottomLeft', $p->shift('pocketFlapTopLeft', -90, $this->v('pocketFlapHeight')));
        $p->addPoint('pocketFlapBottomRight', $p->shift('pocketFlapTopRight', -90, $this->v('pocketFlapHeight')));
        $p->addPoint('pocketFlapBottomLeftTop', $p->shift('pocketFlapBottomLeft', 90, $this->v('pocketFlapRadius')));
        $p->addPoint('pocketFlapBottomRightTop', $p->shift('pocketFlapBottomRight', 90, $this->v('pocketFlapRadius')));
        $p->addPoint('pocketFlapBottomLeftRight', $p->shift('pocketFlapBottomLeft', 0, $this->v('pocketFlapRadius')));
        $p->addPoint('pocketFlapBottomRightLeft', $p->shift('pocketFlapBottomRight', 180, $this->v('pocketFlapRadius')));
        $p->addPoint('pocketFlapBottomLeftTopCp', $p->shift('pocketFlapBottomLeftTop', -90,     $round));
        $p->addPoint('pocketFlapBottomLeftRightCp', $p->shift('pocketFlapBottomLeftRight', 180, $round));
        $p->addPoint('pocketFlapBottomRightTopCp', $p->shift('pocketFlapBottomRightTop', -90,   $round));
        $p->addPoint('pocketFlapBottomRightLeftCp', $p->shift('pocketFlapBottomRightLeft', 0,   $round));
        
        // Make flap taper inwards a bit
        $p->addPoint('pocketFlapBottomLeftTop', $p->shift('pocketFlapBottomLeftTop', 0, 2));
        $p->addPoint('pocketFlapBottomRightTop', $p->shift('pocketFlapBottomRightTop', 180, 2));
        $p->addPoint('pocketFlapTopLeft', $p->shiftOutwards('pocketFlapBottomLeftTop','pocketTopLeft', $rise));
        $p->addPoint('pocketFlapTopRight', $p->shiftOutwards('pocketFlapBottomRightTop','pocketTopRight', $rise));
        $p->addPoint('pocketFlapBottomLeftTopCp', $p->shiftOutwards('pocketFlapTopLeft','pocketFlapBottomLeftTop', $round));
        $p->addPoint('pocketFlapBottomRightTopCp', $p->shiftOutwards('pocketFlapTopRight','pocketFlapBottomRightTop', $round));
        $p->addPoint('pocketFlapBottomLeftRight', $p->shift('pocketFlapBottomLeftRight', 0, 2));
        $p->addPoint('pocketFlapBottomLeftRightCp', $p->shift('pocketFlapBottomLeftRightCp', 0, 2));
        $p->addPoint('pocketFlapBottomRightLeft', $p->shift('pocketFlapBottomRightLeft', 180, 2));
        $p->addPoint('pocketFlapBottomRightLeftCp', $p->shift('pocketFlapBottomRightLeftCp', 180, 2));
        

        // Pocket path
        $p->newPath('pocket', 'M pocketTopLeft L pocketBottomLeftTop 
            C pocketBottomLeftTopCp pocketBottomLeftRightCp pocketBottomLeftRight
            L pocketBottomRightLeft
            C pocketBottomRightLeftCp pocketBottomRightTopCp pocketBottomRightTop
            L pocketTopRight
            z', ['class' => 'help']);
        $p->newPath('pocketFlap', 'M pocketFlapTopLeft L pocketFlapBottomLeftTop 
            C pocketFlapBottomLeftTopCp pocketFlapBottomLeftRightCp pocketFlapBottomLeftRight
            L pocketFlapBottomRightLeft
            C pocketFlapBottomRightLeftCp pocketFlapBottomRightTopCp pocketFlapBottomRightTop
            L pocketFlapTopRight
            z', ['class' => 'help']);

        // Chest pocket
        $p->newPoint('chestPocketBottomLeft', $p->x('button2Right')+ 2*$this->v('chestPocketWidth'), $p->y('button2Right')-5);
        $p->addPoint('chestPocketBottomRight', $p->shift('chestPocketBottomLeft', 0, $this->v('chestPocketWidth')));
        $p->addPoint('chestPocketTopLeft', $p->shift('chestPocketBottomLeft', 90, $this->v('chestPocketHeight')));
        $p->addPoint('chestPocketTopRight', $p->shift('chestPocketBottomRight', 90, $this->v('chestPocketHeight')));
        // Slightly rotate chest pocket
        $angle = $this->v('chestPocketRotation');
        $p->addPoint('chestPocketBottomRight', $p->rotate('chestPocketBottomRight','chestPocketBottomLeft',$angle));
        $p->addPoint('chestPocketTopLeft', $p->rotate('chestPocketTopLeft','chestPocketBottomLeft',$angle));
        $p->addPoint('chestPocketTopRight', $p->rotate('chestPocketTopRight','chestPocketBottomLeft',$angle));

        $p->newPath('chestPocket', 'M chestPocketTopLeft L chestPocketTopRight L chestPocketBottomRight L chestPocketBottomLeft z', ['class' => 'help']);



        // Paths 
        $path = 'M 9 L collarTip 
            C collarTip collarBendPointCpTop collarBendPoint
            L hemFrontEdge L hemSide L seatSide 
            C seatSideCpTop waistSideCpBottom waistSide 
            C waistSideCpTop chestSideCp 5 
            C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 9 z';
        $p->newPath('outline', $path);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->clonePoint('frontEdge', 'gridAnchor');


        // Calculate collar length
        $this->setValue('frontCollarLength', $p->curveLen(9, 21, 20, 8));
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
        $this->clonePoints('backBlock','back');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Box pleat
        $p->newPoint('bpTop', $p->x(1) - $model->m('chestCircumference') * 0.048, $p->y(10));
        $p->newPoint('bpTopIn', $p->x(1), $p->y(10));
        $p->newPoint('bpBottom', $p->x('bpTop'), $p->y(3) - $this->v('beltWidth')/2);
         
        // Waist shaping
        $p->newPoint('waistSide', $p->x(5) - $this->v('waistReductionSide'), $p->y(3) - $this->v('beltWidth')/2);
        $p->addPoint('waistSideCpTop', $p->shift('waistSide', 90, ($p->deltaY(5,3)/2) - ($this->v('beltWidth')/2)));
        $p->addPoint('chestSideCp', $p->shift(5,-90,$p->deltaY(5,'waistSideCpTop')/8));

        // Darts
        $p->newPoint('dartCenter', $p->x('waistSide') * 0.4, $p->y('waistSide'));
        $p->addPoint('dartRight', $p->shift('dartCenter', 0, $this->v('waistReductionBack')));
        $p->addPoint('dartLeft', $p->shift('dartCenter', 180, $this->v('waistReductionBack')));
        $p->newPoint('dartTip', $p->x('dartCenter'), $p->y(5));
        $p->addPoint('dartRightCp', $p->shift('dartRight', 90, $p->deltaY(5,'dartCenter')/2));
        $p->addPoint('dartLeftCp', $p->shift('dartLeft', 90, $p->deltaY(5,'dartCenter')/2));
        // Paths
        $path = 'M 1 L bpTopIn L bpTop L bpBottom L dartLeft C dartLeftCp dartTip dartTip C dartTip dartRightCp dartRight L waistSide C waistSideCpTop chestSideCp 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 1 1 z';
        $p->newPath('outline', $path, ['class' => 'fabric']);

        $p->newPath('boxPleat', 'M bpTopIn L bpTop L bpBottom'); 
        
        $this->setValue('backCollarLength', $p->curveLen(1, 1, 20, 8));

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->clonePoint('bpBottom', 'gridAnchor');

    }

    /**
     * Drafts the Tail
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTail($model)
    {
        /** @var \Freesewing\Part $p */
        $b = $this->parts['back'];
        $waist = $b->x('waistSide') - $this->v('waistReductionBack')*2;

        /** @var \Freesewing\Part $p */
        $f = $this->parts['front'];
        $length = $f->y('hemSide') - $f->y('waistSide');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['tail'];

        $p->newPoint('cbTop', 0, 0);
        $p->newPoint('waistTop', $waist, 0);
        $p->newPoint('leftTop', $model->m('naturalWaist') * $this->v('tailfoldWaistFactor') * -2, 0);
        $p->newPoint('leftPleat1', $model->m('naturalWaist') * $this->v('tailfoldWaistFactor') * -1.5, 0);
        $p->newPoint('leftPleat2', $model->m('naturalWaist') * $this->v('tailfoldWaistFactor') * -1.0, 0);
        $p->newPoint('leftPleat3', $model->m('naturalWaist') * $this->v('tailfoldWaistFactor') * 0.5, 0);

        foreach($p->points as $id => $point) {
            $p->addPoint("$id-1", $p->shift($id,-90,50));
            $p->addPoint("$id-2", $p->shift($id,-90,80));
            $p->addPoint("$id-3", $p->shift($id,-90,130));
        }

        $p->addPoint('dimTop', $p->shift('waistTop', 180, 70));
        $p->addPoint('dimBottom', $p->shift('waistTop-3', 180, 70));

        // Store belt length
        $this->setValue('halfBackWidth', $p->distance('leftPleat2', 'waistTop') - (2 * $p->distance('cbTop','leftPleat3'))); 
        $this->setValue('beltLength', $this->v('halfBackWidth') + $p->distance('leftPleat2', 'cbTop') + 20);
        $this->dbg('belt length  is '.$this->v('beltLength'));

        $p->newLinearDimension('dimBottom', 'dimTop', 0, $p->unit($length));

        // Paths
        $p->newPath('seamline1', 'M leftTop-1 leftTop L cbTop L waistTop L waistTop-1', ['class' => 'fabric']);
        $p->newPath('seamline2', 'M leftTop-2 leftTop-3 L cbTop-3 L waistTop-3 L waistTop-2', ['class' => 'fabric']);
        $p->newPath('folds', '
            M leftPleat1 L leftPleat1-3
            M leftPleat2 L leftPleat2-3
            M cbTop L cbTop-3
            M leftPleat3 L leftPleat3-3
        ', ['class' => 'dashed']);
        $p->newPath('dots', 'M leftTop-1 L leftTop-2 M waistTop-1 L waistTop-2', ['class' => 'help sa']);

        // Mark path for sample service
        $p->paths['seamline1']->setSample(true);
        $p->paths['seamline2']->setSample(true);
        $p->clonePoint('leftTop', 'gridAnchor');
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


        $p->newPoint('centerTop', 0, 0);
        $p->newPoint('centerBottom', 0, $model->m('chestCircumference') * $this->o('collarMidHeightFactor'));
        $p->newPoint('rightTop', $this->v('frontCollarLength') + $this->v('backCollarLength'), 0);
        $p->newPoint('rightCorner', $p->x('rightTop'), $p->y('centerBottom'));
        $p->addPoint('leftCorner', $p->flipX('rightCorner'));
        $p->newPoint('rightBottom', $p->x('rightTop')*0.76, $p->y('centerBottom') + $p->x('rightTop')/8.5);
        $p->addPoint('leftTop', $p->flipX('rightTop'));
        $p->addPoint('leftBottom', $p->flipX('rightBottom'));

        // Control points
        $p->newPoint('centerBottomCpRight', $p->x('rightBottom')/2, $p->y('centerBottom'));
        $p->addPoint('centerBottomCpLeft', $p->flipX('centerBottomCpRight'));
        $p->newPoint('rightBottomCpRight', $p->x('rightBottom') + $p->deltaX('rightBottom', 'rightCorner')/2, $p->y('rightBottom'));
        $p->addPoint('rightBottomCpLeft', $p->flipX('rightBottomCpRight', $p->x('rightBottom')));
        $p->addPoint('leftBottomCpRight', $p->flipX('rightBottomCpRight'));
        $p->addPoint('leftBottomCpLeft', $p->flipX('rightBottomCpLeft'));

        // Collar stand points
        $p->addPoint('standCenterTop', $p->shift('centerBottom',90,25));
        $p->newPoint('standCenterTopCpRight', $p->x('rightBottom')*0.9, $p->y('standCenterTop'));
        $p->addPoint('standCenterTopCpLeft', $p->flipX('standCenterTopCpRight'));
        
        // Divide top in 5 parts
        $p->addPoint('cutTop1', $p->shift('centerTop',0,$p->x('rightTop')*1/5));
        $p->addPoint('cutTop2', $p->shift('centerTop',0,$p->x('rightTop')*2/5));
        $p->addPoint('cutTop3', $p->shift('centerTop',0,$p->x('rightTop')*3/5));
        $p->addPoint('cutTop4', $p->shift('centerTop',0,$p->x('rightTop')*4/5));

        // Divide bottom in 4 parts
        $len = $p->curveLen('standCenterTop','standCenterTopCpRight', 'rightBottom','rightBottom');
        $p->addPoint('cutBottom1', $p->shiftAlong('standCenterTop','standCenterTopCpRight', 'rightBottom','rightBottom',$len*1/4));
        $p->addPoint('cutBottom2', $p->shiftAlong('standCenterTop','standCenterTopCpRight', 'rightBottom','rightBottom',$len*2/4));
        $p->addPoint('cutBottom3', $p->shiftAlong('standCenterTop','standCenterTopCpRight', 'rightBottom','rightBottom',$len*3/4));

        // Split bottom curve in half
        $p->splitCurve('standCenterTop','standCenterTopCpRight', 'rightBottom','rightBottom','cutBottom2','.helpa');
        $p->splitCurve('standCenterTop','.helpa2', '.helpa3','cutBottom2','cutBottom1','.helpb');
        $p->splitCurve('cutBottom2','.helpa7', 'rightBottom','rightBottom','cutBottom3','.helpc');

        $p->clonePoint('.helpb2','curve1Cp1');
        $p->clonePoint('.helpb3','curve1Cp2');
        $p->clonePoint('.helpb7','curve2Cp1');
        $p->clonePoint('.helpb6','curve2Cp2');
        $p->clonePoint('.helpc2','curve3Cp1');
        $p->clonePoint('.helpc3','curve3Cp2');
        $p->clonePoint('.helpc7','curve4Cp1');

        /*
        $p->newPath('panels','
            M cutBottom1 L cutTop1
            M cutBottom2 L cutTop2
            M cutBottom3 L cutTop3
            M rightBottom L cutTop4
            ', ['class' => 'help']);
        */
        
        // Clone points for collar stand before we fuck shit up
        $this->clonePoints('collar','collarStand');

        // Slash and rotate
        $rotate = [
            1 => [
                'origin' => 'rightBottom',
                'points' => ['rightCorner', 'rightTop', 'cutTop4', 'rightBottomCpRight'],
            ],
            2 => [
                'origin' => 'cutBottom3',
                'points' => ['rightBottom', 'cutTop4', 'cutTop3', 'curve4Cp1'],
            ],
        3 => [
                'origin' => 'cutBottom2',
                'points' => ['cutBottom3', 'cutTop3', 'cutTop2', 'curve3Cp1', 'curve3Cp2'],
            ],
            4 => [
                'origin' => 'cutBottom1',
                'points' => ['cutBottom2', 'cutTop2', 'cutTop1', 'curve2Cp1', 'curve2Cp2'],
            ],
        ];
         
        $angle = -5;
        $alsoRotate=[];
        $prevorg = false;
        foreach($rotate as $nr => $step) {
            $org= $step['origin'];
            // $path="M $org ";
            $first = false;
            foreach($step['points'] as $pnt) {
                if($first === false) $first = $pnt;
                $id = "rot-$nr-$pnt";
                $p->addPoint($id, $p->rotate($pnt, $org, $angle));
                $alsoRotate[]=$id;
                // $path .= "L $id ";
                // $p->newPath("rot-$nr", $path.'z', ['class' => 'hint']); 
            }
            if($nr <4) foreach($alsoRotate as $pnt) $p->addPoint($pnt, $p->rotate($pnt, $org, $angle));
        }

        // Shift panel 2 in place
        $ang = $p->angle('cutBottom2', 'rot-4-cutBottom2')+180; 
        $len = -1*$p->distance('cutBottom2', 'rot-4-cutBottom2'); 
        foreach(['cutBottom2', 'rot-3-cutTop2', 'rot-3-cutTop3', 'rot-3-cutBottom3','rot-3-curve3Cp1', 'rot-3-curve3Cp2'] as $pnt) $p->addPoint($pnt, $p->shift($pnt, $ang, $len));
        
        // Shift panel 3 in place
        $ang = $p->angle('cutBottom3', 'rot-3-cutBottom3')+180; 
        $len = -1*$p->distance('cutBottom3', 'rot-3-cutBottom3'); 
        foreach(['cutBottom3', 'rot-2-cutTop3', 'rot-2-cutTop4', 'rot-2-rightBottom', 'rot-2-curve4Cp1'] as $pnt) $p->addPoint($pnt, $p->shift($pnt, $ang, $len));
        
        // Shift panel 4 in place
        $ang = $p->angle('rightBottom', 'rot-2-rightBottom')+180; 
        $len = -1*$p->distance('rightBottom', 'rot-2-rightBottom'); 
        foreach(['rightBottom', 'rot-1-cutTop4', 'rot-1-rightTop', 'rot-1-rightCorner','rot-1-rightBottomCpRight'] as $pnt) $p->addPoint($pnt, $p->shift($pnt, $ang, $len));

        // Add 2cm collar shaping
        $p->addPoint('shapedTip', $p->shiftTowards('rot-2-cutTop3', 'rot-1-rightTop', $p->distance('rot-2-cutTop3', 'rot-1-rightTop')+20)); 
         
        $p->newPath("outline", "
            M standCenterTop C curve1Cp1 curve1Cp2 cutBottom1
            C rot-4-curve2Cp1 rot-4-curve2Cp2 rot-4-cutBottom2
            C rot-3-curve3Cp1 rot-3-curve3Cp2 rot-3-cutBottom3
            C rot-2-curve4Cp1 rot-2-rightBottom rot-2-rightBottom
            L rot-1-rightCorner
            L shapedTip
            C rot-2-cutTop4 cutTop3 centerTop
            z 
        "); 

        $p->addPoint('tmp', $p->shift('standCenterTopCpRight', 90, 35));
        /*
        $p->newPath('acrSegments','
            M standCenterTop
            C curve1Cp1 curve1Cp2 cutBottom1 
            C curve2Cp1 curve2Cp2 cutBottom2 
            C curve3Cp1 curve3Cp2 cutBottom3 
            C curve4Cp1 rightBottom rightBottom
        ', ['class' => 'debug']);
         
        //$p->splitCurve('standCenterTop','standCenterTopCpRight', 'rightBottom','rightBottom', 0.5, 'cutBottom', true);

        $p->newPath('outline', '
            M centerTop 
            L rightTop 
            L rightCorner 
            C rightCorner rightBottomCpRight rightBottom 
            C rightBottom standCenterTopCpRight standCenterTop
            C standCenterTopCpLeft leftBottom leftBottom
            C leftBottomCpRight leftCorner leftCorner L leftTop z');

        //$p->newPath('stand', 'M leftBottom C leftBottom standCenterTopCpLeft standCenterTop C standCenterTopCpRight rightBottom rightBottom', ['class' => 'debug']);
         */
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->clonePoint('centerTop', 'gridAnchor');
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collarStand'];
        
        $p->newPath('outline', '
            M leftBottom 
            C leftBottom standCenterTopCpLeft standCenterTop 
            C standCenterTopCpRight rightBottom rightBottom
            C rightBottomCpLeft centerBottomCpRight centerBottom 
            C centerBottomCpLeft leftBottomCpLeft leftBottom 
            z
        ');
        //$p->notch(['centerTop', 'centerBottom']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->clonePoint('standCenterTop', 'gridAnchor');
    }

    protected function collarDelta() {
        /** @var \Freesewing\Part $s */
        $s = $this->parts['collarStand'];
        // Collar stand length
        $standLen = $s->curveLen('centerTop', 'rightTopCp', 'rightTop', 'rightBottom');
            
        /** @var \Freesewing\Part $c */
        $c = $this->parts['collar'];
        // Collar length
        $collarLen = $c->curveLen('bottom', 'rightTopCp', 'rightBottom', 'rightBottom');

        return $collarLen - $standLen;
    }

    /**
     * Drafts the topssleeve 
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTopsleeve($model)
    {
        $this->clonePoints('topsleeveBlock','topsleeve');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeve'];
        
        // Add cuff
        $p->addPoint('cuffBottomRight', $p->shift('topsleeveWristRight', $p->angle('topsleeveWristLeft', 'topsleeveWristRight')-90, $this->v('cuffLength')));
        $p->addPoint('cuffBottomLeft', $p->shift('topsleeveWristLeft', $p->angle('topsleeveWristLeft', 'topsleeveWristRight')-90, $this->v('cuffLength')));
        $p->addPoint('cuffBottomRightTop', $p->shiftTowards('cuffBottomRight', 'topsleeveWristRight', $this->v('cuffLength') *0.25));
        $p->addPoint('cuffBottomRightCpTop', $p->shiftTowards('cuffBottomRightTop', 'cuffBottomRight', \Freesewing\BezierToolbox::bezierCircle($p->distance('cuffBottomRightTop', 'cuffBottomRight'))));
        $p->addPoint('cuffBottomRightLeft', $p->rotate('cuffBottomRightTop', 'cuffBottomRight', 90));
        $p->addPoint('cuffBottomRightCpLeft', $p->rotate('cuffBottomRightCpTop', 'cuffBottomRight', 90));

        // Store cuff width
        $this->setValue('topCuffWidth', $p->distance('topsleeveWristRight','topsleeveWristLeft'));

        // Paths
        $p->newPath('prollem', '
            M elbowRight 
            C elbowRightCpTop topsleeveRightEdgeCpBottom topsleeveRightEdge 
            C topsleeveRightEdgeCpTop backPitchPoint backPitchPoint 
            C backPitchPoint sleeveTopCpRight sleeveTop 
            C sleeveTopCpLeft frontPitchPointCpTop frontPitchPoint 
            C frontPitchPointCpBottom topsleeveLeftEdgeCpRight topsleeveLeftEdge 
            C topsleeveLeftEdge topsleeveElbowLeftCpTop topsleeveElbowLeft 
            L topsleeveWristLeft 
            L cuffBottomLeft 
            L cuffBottomRightLeft
            C cuffBottomRightCpLeft cuffBottomRightCpTop cuffBottomRightTop
            L topsleeveWristRight
            z
        ', ['class' => 'fabric', 'flag' => 'prollem']);
        $p->newPath('tmp', 'M topsleeveWristLeft L topsleeveWristRight ', ['class' => 'hint']); 

        // Mark path for sample service
        $p->paths['prollem']->setSample(true);
        $p->clonePoint('topsleeveWristRight', 'gridAnchor');
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
        $this->clonePoints('undersleeveBlock','undersleeve');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeve'];
        
        // Add cuff
        $p->addPoint('cuffBottomRight', $p->shift('undersleeveWristRight', $p->angle('undersleeveWristLeft', 'undersleeveWristRight')-90, $this->v('cuffLength')));
        $p->addPoint('cuffBottomLeft', $p->shift('undersleeveWristLeft', $p->angle('undersleeveWristLeft', 'undersleeveWristRight')-90, $this->v('cuffLength')));
        $p->addPoint('cuffBottomRightTop', $p->shiftTowards('cuffBottomRight', 'undersleeveWristRight', $this->v('cuffLength') *0.25));
        $p->addPoint('cuffBottomRightCpTop', $p->shiftTowards('cuffBottomRightTop', 'cuffBottomRight', \Freesewing\BezierToolbox::bezierCircle($p->distance('cuffBottomRightTop', 'cuffBottomRight'))));
        $p->addPoint('cuffBottomRightLeft', $p->rotate('cuffBottomRightTop', 'cuffBottomRight', 90));
        $p->addPoint('cuffBottomRightCpLeft', $p->rotate('cuffBottomRightCpTop', 'cuffBottomRight', 90));
        
        // Store cuff width
        $this->setValue('underCuffWidth', $p->distance('undersleeveWristRight','undersleeveWristLeft'));


        $p->newPath('outline', '
            M elbowRight 
            C elbowRightCpTop undersleeveRightEdgeCpBottom undersleeveRightEdge 
            C undersleeveRightEdgeCpTop undersleeveTip undersleeveTip 
            C undersleeveTipCpBottom undersleeveLeftEdgeCpRight undersleeveLeftEdgeRight 
            L undersleeveLeftEdge 
            C undersleeveLeftEdge undersleeveElbowLeftCpTop undersleeveElbowLeft 
            L undersleeveWristLeft 
            L cuffBottomLeft
            L cuffBottomRightLeft
            C cuffBottomRightCpLeft cuffBottomRightCpTop cuffBottomRightTop
            L undersleeveWristRight
            z
        ', ['class' => 'fabric']);
        $p->newPath('tmp', 'M undersleeveWristLeft L undersleeveWristRight ', ['class' => 'hint']); 

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts the cuff facing 
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftCuffFacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['cuffFacing'];

        // Make facing 1.5 times the lenght of the cuff
        $factor = 1.5;

        $width = $this->v('underCuffWidth') + $this->v('topCuffWidth');
        $p->newPoint('topLeft', 0, 0);
        $p->newPoint('topRight', $width, 0);
        $p->newPoint('bottomLeft', 0, $this->v('cuffLength') * $factor);
        $p->newPoint('bottomRight', $width, $p->y('bottomLeft'));
        
        $p->addPoint('bottomLeftTop', $p->shiftTowards('bottomLeft', 'topLeft', $this->v('cuffLength') *0.25));
        $p->addPoint('bottomLeftCpTop', $p->shiftTowards('bottomLeftTop', 'bottomLeft', \Freesewing\BezierToolbox::bezierCircle($p->distance('bottomLeftTop', 'bottomLeft'))));
        $p->addPoint('bottomLeftRight', $p->rotate('bottomLeftTop', 'bottomLeft', -90));
        $p->addPoint('bottomLeftCpRight', $p->rotate('bottomLeftCpTop', 'bottomLeft', -90));
        $rotateThese = ['bottomLeftTop','bottomLeftCpTop','bottomLeftRight','bottomLeftCpRight'];
        foreach($rotateThese as $pid) {
            $p->addPoint($pid.'-r', $p->flipX($pid, $width/2));
        }
    
        // Mark edge of cuff
        $p->addPoint('notchLeft', $p->shift('bottomLeft',90,$this->v('cuffLength')));
        $p->addPoint('notchRight', $p->shift('bottomRight',90,$this->v('cuffLength')));

        $p->newPath('outline', 'M topLeft L bottomLeftTop C bottomLeftCpTop bottomLeftCpRight bottomLeftRight 
            L bottomLeftRight-r C bottomLeftCpRight-r bottomLeftCpTop-r bottomLeftTop-r L topRight z'); 

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->addPoint('gridAnchor', $p->shiftFractionTowards('topLeft','topRight', 0.5));
    }

    /**
     * Drafts the belt 
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['belt'];

        $p->newPoint('topLeft', 0, 0);
        $p->newPoint('topRight', $this->v('beltLength'), 0);
        $p->newPoint('bottomLeft', 0, $this->v('beltHeight'));
        $p->newPoint('bottomRight', $p->x('topRight'), $p->y('bottomLeft'));

        // Round the edges
        $radius = 10;
        $p->newPoint('topLeftRight', $radius, 0);
        $p->newPoint('topLeftRightCp', $radius - \Freesewing\BezierToolbox::bezierCircle($radius), 0);
        $p->newPoint('topLeftBottom', 0, $radius);
        $p->newPoint('topLeftBottomCp', 0, $radius - \Freesewing\BezierToolbox::bezierCircle($radius));
        
        $flipThese = ['topLeftRight','topLeftRightCp','topLeftBottom','topLeftBottomCp','topLeftBottomCp'];
        foreach($flipThese as $pid) {
            $p->addPoint($pid.'-b', $p->flipY($pid, $this->v('beltHeight')/2));
        }

        // Buttonholes
        $p->newPoint('button', 30, $this->v('beltHeight')/2);

        $p->newPath('outline', '
            M topRight 
            L topLeftRight
            C topLeftRightCp topLeftBottomCp topLeftBottom
            L topLeftBottom-b 
            C topLeftBottomCp-b topLeftRightCp-b topLeftRight-b
            
            L bottomRight L topRight  z', ['class' => 'fabric']); 

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts the pocket 
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocket($model)
    {
        $this->clonePoints('front', 'pocket');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocket'];

        // Path
        $p->newPath('outline', '
            M pocketTopLeft 
            L pocketBottomLeftTop
            C pocketBottomLeftTopCp pocketBottomLeftRightCp pocketBottomLeftRight
            L pocketBottomRightLeft
            C pocketBottomRightLeftCp pocketBottomRightTopCp pocketBottomRightTop
            L pocketTopRight
            L pocketTopLeft 
            z', ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->addPoint('gridAnchor', $p->shiftFractionTowards('pocketTopLeft','pocketTopRight', 0.5));
    }

    /**
     * Drafts the pocket flap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocketFlap($model)
    {
        $this->clonePoints('front', 'pocketFlap');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketFlap'];

        // Path
        $p->newPath('outline', 'M pocketFlapTopLeft L pocketFlapBottomLeftTop 
            C pocketFlapBottomLeftTopCp pocketFlapBottomLeftRightCp pocketFlapBottomLeftRight
            L pocketFlapBottomRightLeft
            C pocketFlapBottomRightLeftCp pocketFlapBottomRightTopCp pocketFlapBottomRightTop
            L pocketFlapTopRight
            z', ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->addPoint('gridAnchor', $p->shiftFractionTowards('pocketFlapTopLeft','pocketFlapTopRight', 0.5));
    }

    /**
     * Drafts the chest pocket welt
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftChestPocketWelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['chestPocketWelt'];

        $p->newPoint('topLeft', 0, 0);
        $p->newPoint('topRight', $this->v('chestPocketWidth')*2, 0);
        $p->newPoint('bottomRight', $p->x('topRight'), $this->v('chestPocketHeight'));
        $p->newPoint('bottomLeft', 0, $p->y('bottomRight'));
        $p->newPoint('midTop', $p->x('topRight')/2, 0);
        $p->newPoint('midBottom', $p->x('midTop'), $p->y('bottomRight'));


        // Path
        $p->newPath('outline', 'M topLeft L topRight L bottomRight L bottomLeft L topLeft z', ['class' => 'fabric']);
        $p->newPath('foldline', 'M midTop L midBottom', ['class' => 'hint']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
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

        // Title
        $p->newPoint('titleAnchor', $p->x(21), $p->y(10));
        $p->addTitle('titleAnchor', 1, $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('facing from fabric').
            "\n2x ".
            $this->t('non-facing from lining')
        );

        // Scalebox
        $p->newPoint('scaleboxAnchor', $p->x(21), $p->y(5));
        $p->newSnippet('scalebox','scalebox','scaleboxAnchor');

        // Logo
        $p->addPoint('logoAnchor', $p->shiftFractionTowards('titleAnchor','scaleboxAnchor',0.5));
        $p->newSnippet('logo','logo','logoAnchor');

        // Grainline
        $p->newPoint('grainlineTop', $p->x('collarTip')+30, $p->y('collarTip')+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('hemFrontEdge')-20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Buttons
        $p->newSnippet('button1Left','button-lg','button1Left');
        $p->newSnippet('button2Left','button-lg','button2Left');
        $p->newSnippet('button3Left','button-lg','button3Left');
        $p->newSnippet('button1Right','button-lg','button1Right');
        $p->newSnippet('button2Right','button-lg','button2Right');
        $p->newSnippet('button3Right','button-lg','button3Right');

        // Center front helpline
        $p->newPath('cf', 'M hemMiddle L 9', ['class' => 'help']);
        $p->newTextOnPath('cf1', 'M hemMiddle L 4', $this->t('Center front'), ['dy' => -2], false);
        $p->newTextOnPath('cf2', 'M 4 L 3', $this->t('Center front'), ['dy' => -2], false);
        $p->newTextOnPath('cf3', 'M 3 L 2', $this->t('Center front'), ['dy' => -2], false);
        $p->newTextOnPath('cf4', 'M 2 L 9', $this->t('Center front'), ['dy' => -2], false);

        // Facing lining border (flb)
        $p->newPoint('flbBottom', $p->x('pocketTopLeft')-12.5, $p->y('hemFrontEdge'));
        $p->curveCrossesX(9, 21, 20, 8, $p->x('flbBottom'), 'flb');
        $p->clonePoint('flb1', 'flbTop');
        $p->newPath('fldFacing', 'M flbTop L flbBottom',['class' => 'fabric']);
        $p->newPath('fldLining', 'M flbTop L flbBottom',['class' => 'lining lashed']);
        $p->newTextOnPath(1, 'M flbTop L flbBottom', $this->t('Facing/Lining boundary - Lining side'), ['dy' => -2, 'class' => 'fill-lining'], false);
        $p->newTextOnPath(2, 'M flbBottom L flbTop', $this->t('Facing/Lining boundary - Facing side'), ['dy' => -2, 'class' => 'fill-fabric'], false);
        $p->newPoint('facingNoteAnchor', $p->x('flbBottom'), $p->y('seatSide'));
        $p->newNote('flb', 'facingNoteAnchor', $this->t('Add seam allowance at the facing/lining border'), 4, 40 );

        // Notches
        $p->notch(['collarBendPoint', 10, 'waistSide']);

        // Waistline
        $p->newPath('waistline', 'M 3 L waistSide', ['class' => 'help']);

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline',$this->o('sa')*-1,1,['class' => 'sa fabric']);
            // Extra hem SA
            $p->addPoint('sa-line-hemFrontEdgeTOhemSide', $p->shift('sa-line-hemFrontEdgeTOhemSide',-90,$this->o('sa')*4));
            $p->addPoint('sa-line-hemSideTOhemFrontEdge', $p->shift('sa-line-hemSideTOhemFrontEdge',-90,$this->o('sa')*4));
            // Notes
            $p->addPoint('noteAnchor1', $p->shift('hemSide', 90, 30));
            $p->addPoint('noteAnchor2', $p->shift('hemSide', 180, 120));
            $p->newNote('sa1', 'noteAnchor1', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 10, 40, $this->o('sa')*-0.5);
            $p->newNote('sa2', 'noteAnchor2', $this->t('Hem allowance')."\n(".$p->unit($this->o('sa')*5).')', 12, 30, $this->o('sa')*-2.5);
            // Straighten hem
            $p->newPoint('sa-line-hemFrontEdgeTOhemSide', $p->x('sa-line-hemFrontEdgeTOcollarBendPoint'), $p->y('sa-line-hemFrontEdgeTOhemSide'));
            $p->newPoint('sa-line-hemSideTOhemFrontEdge', $p->x('sa-line-hemSideTOseatSide'), $p->y('sa-line-hemSideTOhemFrontEdge'));
        }

        // Store length to sleeve notch
        $this->setValue('toFrontSleeveNotch', $p->curveLen(12,19,17,10));
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Title
        $p->newPoint('titleAnchor', $p->x(8), $p->y(18));
        $p->addTitle('titleAnchor', 2, $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('from lining')
        );

        // Logo
        $p->newSnippet('logo','logo', 21);
        
        // Grainline
        $p->newPoint('grainlineTop', $p->x(1)+20, $p->y(1)+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('waistSide')-20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Enforcement triangle
        $p->addPoint('triangleRight', $p->shift('bpTopIn', 0, $p->distance('bpTopIn','bpTop')*0.6));
        $p->addPoint('triangleTop', $p->rotate('triangleRight','bpTopIn',90));
        $p->newPath('triangle', 'M bpTopIn L triangleRight L triangleTop z', ['class' => 'help']);

        // Notches
        $p->notch([10]);

        // Seam allowance
        if($this->o('sa')) {
            // FIXME : This is a hack because path offset is tricky at this neck curve
            $p->addPoint('split', $p->shiftAlong(8, 20, 1, 1, 10));
            $p->splitCurve(8,20,1,1,'split','split');
            $path = 'M split C split7 1 1 L bpTopIn L bpTop L bpBottom L waistSide C waistSideCpTop chestSideCp 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8';
            $p->offsetPathString('sa', $path,$this->o('sa')*-1,1,['class' => 'sa fabric']);
            $p->newPath('closeSa', 'M sa-endPoint L sa-startPoint',['class' => 'sa fabric']);
            $p->newNote('sa', 'chestSideCp', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 9, 30, $this->o('sa')*-0.4);
        }
        
        // Store length to sleeve notch
        $this->setValue('toBackSleeveNotch', $p->curveLen(12,19,17,10));
    }

    /**
     * Finalizes the tail
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeTail($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['tail'];

        // Title
        $p->newPoint('titleAnchor', $p->x('waistTop')/2, $p->y('leftTop-3')/2);
        $p->addTitle('titleAnchor', 3, $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('from lining')
        );

        // Logo
        $p->newPoint('logoAnchor', $p->x('leftPleat2')/2, $p->y('leftTop-3')/2);
        $p->newSnippet('logo','logo', 'logoAnchor');
        
        // Grainline
        $p->newPoint('grainlineTop', $p->x('leftTop')+20, $p->y('leftTop')+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('leftTop-3')-10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));
        
        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa','M leftTop L leftTop-3 L waistTop-3 L waistTop L leftTop z',$this->o('sa')*-1,1,['class' => 'sa fabric']);
            $p->addPoint('sa-line-leftTop-3TOwaistTop-3', $p->shift('sa-line-leftTop-3TOwaistTop-3',-90,$this->o('sa')*4));
            $p->addPoint('sa-line-waistTop-3TOleftTop-3', $p->shift('sa-line-waistTop-3TOleftTop-3',-90,$this->o('sa')*4));
            $p->addPoint('noteAnchor1', $p->shift('waistTop', -90, 20));
            $p->addPoint('noteAnchor2', $p->shift('leftPleat3-3', -90, 12));
            $p->newNote('sa1', 'noteAnchor1', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 9, 10, $this->o('sa')*-0.5);
            $p->newNote('sa2', 'noteAnchor2', $this->t('Hem allowance')."\n(".$p->unit($this->o('sa')*5).')', 2, 40);
            // Straighten hem
            $p->newPoint('sa-line-waistTop-3TOleftTop-3', $p->x('sa-line-waistTop-3TOwaistTop'), $p->y('sa-line-waistTop-3TOleftTop-3'));
            $p->newPoint('sa-line-leftTop-3TOwaistTop-3', $p->x('sa-line-leftTop-3TOleftTop'), $p->y('sa-line-leftTop-3TOwaistTop-3'));
        }
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
        
        // Title
        $p->newPoint('titleAnchor', $p->x('sleeveTop'), $p->y('topsleeveRightEdge')+80);
        $p->addTitle('titleAnchor', 4, $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('from lining')
        );

        // Logo
        $p->newPoint('logoAnchor', $p->x('sleeveTop'), $p->y('elbowRight')-25);
        $p->newSnippet('logo','logo', 'logoAnchor');

        // Grainline
        $p->newPoint('grainlineTop', $p->x('sleeveTop'), $p->y('sleeveTop')+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('topsleeveWristLeft')-20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Front sleeve notch
        $curveLen = $p->curveLen('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint');
        $target = $this->v('toFrontSleeveNotch') + $this->o('sleevecapEase')/2;
        if($target > $curveLen) $p->addPoint('frontSleeveNotch', $p->shiftAlong('frontPitchPoint','frontPitchPointCpBottom','topsleeveLeftEdgeCpRight','topsleeveLeftEdge', $target));
        else $p->addPoint('frontSleeveNotch', $p->shiftAlong('sleeveTop','sleeveTopCpLeft','frontPitchPointCpTop','frontPitchPoint', $target));
        $p->notch(['sleeveTop', 'frontSleeveNotch']);

        // Back sleeve notch
        $curveLen = $p->curveLen('sleeveTop','sleeveTopCpRight','backPitchPoint','backPitchPoint');
        $target = $this->v('toBackSleeveNotch') + $this->o('sleevecapEase')/2;
        if($target > $curveLen) {
            $this->setValue('backSleeveNotchInUndersleeve', true);
            $this->setValue('backSleeveNotchTopsleeveLen', $curveLen);
        } else {
            $this->setValue('backSleeveNotchInUndersleeve', false);
            $p->addPoint('backSleeveNotch', $p->shiftAlong('sleeveTop','sleeveTopCpRight','backPitchPoint','backPitchPoint', $target));
            $p->notch(['backSleeveNotch']);
        }

        // Seam allowance
        if($this->o('sa')) {
            // FIXME : This is a hack because path offset is tricky at this sleeve curve
            $p->addPoint('split', $p->shiftAlong('topsleeveLeftEdge' ,'topsleeveLeftEdgeCpRight', 'frontPitchPointCpBottom', 'frontPitchPoint', 10));
            $p->splitCurve('topsleeveLeftEdge' ,'topsleeveLeftEdgeCpRight', 'frontPitchPointCpBottom', 'frontPitchPoint','split','split');
            $p->offsetPathString('sa1', 'M split 
                C split7 split6 frontPitchPoint 
                C frontPitchPointCpTop sleeveTopCpLeft sleeveTop 
                C sleeveTopCpRight backPitchPoint backPitchPoint', $this->o('sa'), 1,['class' => 'sa fabric']);
            $p->offsetPathString('sa2', 'M backPitchPoint
                C backPitchPoint topsleeveRightEdgeCpTop topsleeveRightEdge 
                C topsleeveRightEdgeCpBottom elbowRightCpTop elbowRight 
                L topsleeveWristRight 
                L cuffBottomRightTop
                C cuffBottomRightCpTop cuffBottomRightCpLeft cuffBottomRightLeft 
                L cuffBottomLeft
                L topsleeveWristLeft 
                L topsleeveElbowLeft  
                C topsleeveElbowLeftCpTop topsleeveLeftEdge topsleeveLeftEdge 
                ', $this->o('sa')/2, 1,['class' => 'sa fabric']);
            $p->newPath('sa3', 'M sa1-startPoint L sa2-endPoint M sa1-endPoint L sa2-startPoint',['class' => 'sa fabric']);
            $p->newNote('sa1', 'frontPitchPointCpTop', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 3, 30);
            $p->newNote('sa2', 'elbowRightCpTop', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 9, 30);
            
        }
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
        
        // Title
        $p->addTitle('undersleeveWristLeftHelperTop', 5, $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('from lining')
        );

        // Logo
        $p->addPoint('logoAnchor', $p->shift('undersleeveWristLeftHelperTop', 90, 100));
        $p->newSnippet('logo','logo-sm', 'logoAnchor');

        // Grainline
        $p->newPoint('grainlineTop', $p->x('logoAnchor'), $p->y('undersleeveLeftEdge')+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('undersleeveWristLeft')-20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notches
        if($this->v('backSleeveNotchInUndersleeve')) {
            $p->addPoint('backSleeveNotch', $p->shiftAlong('undersleeveTip','undersleeveTipCpBottom','undersleeveLeftEdgeCpRight','undersleeveLeftEdgeRight', $this->v('toBackSleeveNotch') - $this->v('backSleeveNotchTopsleeveLen')));
            $p->notch(['backSleeveNotch']);
        }
        
        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa1', 'M undersleeveLeftEdge 
                L undersleeveLeftEdgeRight 
                C undersleeveLeftEdgeCpRight undersleeveTipCpBottom undersleeveTip 
                ', $this->o('sa'), 1,['class' => 'sa fabric']);
            $p->offsetPathString('sa2', 'M undersleeveTip 
                C undersleeveTip undersleeveRightEdgeCpTop undersleeveRightEdge 
                C undersleeveRightEdgeCpBottom elbowRightCpTop elbowRight 
                L undersleeveWristRight 
                L cuffBottomRightTop 
                C cuffBottomRightCpTop cuffBottomRightCpLeft cuffBottomRightLeft 
                L cuffBottomLeft 
                L undersleeveWristLeft 
                L undersleeveElbowLeft 
                C undersleeveElbowLeftCpTop undersleeveLeftEdge undersleeveLeftEdge 
            ', $this->o('sa')/2, 1,['class' => 'sa fabric']);
            $p->newPoint('sa-join', $p->x('undersleeveLeftEdge') - 0.5*$this->o('sa'), $p->y('undersleeveLeftEdge') - $this->o('sa'));
            $p->addPoint('sa-tipjoin1', $p->shiftOutwards('undersleeveRightEdgeCpTop','undersleeveTip', $this->o('sa')));
            $p->addPoint('sa-tipjoin1', $p->shift('sa-tipjoin1', $p->angle('undersleeveRightEdgeCpTop','undersleeveTip')-90, $this->o('sa')/2));
            $p->newPath('sa3', 'M sa1-startPoint L sa-join L sa2-endPoint M sa1-endPoint L sa-tipjoin1 L sa2-startPoint',['class' => 'sa fabric']);
            $p->newNote('sa1', 'undersleeveLeftEdgeRight', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 5, 30, $this->o('sa')/-2);
            $p->newNote('sa2', 'undersleeveRightEdge', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 8, 30, $this->o('sa')/-4);
        }
    }

    /**
     * Finalizes the belt
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['belt'];
        
        // Title
        $p->newPoint('titleAnchor', $p->x('bottomRight')/2, $p->y('bottomRight')/2);
        $p->addTitle('titleAnchor', 6, $this->t($p->title), '4x '.$this->t('from fabric'), 'small');

        // Button
        $p->newSnippet('button','button-lg','button');

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline', $this->o('sa')*-0.5,1,['class' => 'sa fabric']);
            $p->addPoint('sa-line-topRightTObottomRight', $p->shift('sa-line-topRightTObottomRight',0,$this->o('sa')*0.5));
            $p->addPoint('sa-line-bottomRightTOtopRight', $p->shift('sa-line-bottomRightTOtopRight',0,$this->o('sa')*0.5));
            $p->addPoint('noteanchor1', $p->shift('bottomRight', 90, 40));
            $p->addPoint('noteanchor2', $p->shift('bottomRight', 180, 50));
            $p->newNote('sa1', 'noteanchor1', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 9, 30, $this->o('sa')/-2);
            $p->newNote('sa2', 'noteanchor2', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 11, 20, $this->o('sa')/-4);
        }
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collarStand'];
        
        // Title
        $p->addPoint('titleAnchor', $p->shiftFractionTowards('standCenterTop','centerBottom',0.5));
        $p->addTitle('titleAnchor', 7, $this->t($p->title), '2x '.$this->t('from fabric'), ['scale' => 40]);

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline', $this->o('sa')/2,1,['class' => 'sa fabric']);
            $p->newNote('sa', 'centerBottom', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 6, 20, $this->o('sa')/4);
        }
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
        
        // Title
        $p->newPoint('titleAnchor', $p->x('rot-4-cutBottom2'),$p->y('shapedTip')-20);
        $p->addTitle('titleAnchor', 8, $this->t($p->title), '2x '.$this->t('from fabric'), ['scale' => 75, 'align'=>'left']);

        // Cut on fold
        $p->newPoint('cofTop', $p->x('centerTop'), $p->y('centerTop')+10);
        $p->newPoint('cofBottom', $p->x('standCenterTop'), $p->y('standCenterTop')-10);
        $p->newCutOnFold('cofBottom','cofTop',$this->t('cut on fold'));
        
        // Grainline
        $p->newPoint('grainlineTop', $p->x('centerTop')+30, $p->y('centerTop')+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('standCenterTop')-10);
        $p->newGrainline('grainlineBottom','grainlineTop', $this->t('grainline'));

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathString('sa','
            M standCenterTop C curve1Cp1 curve1Cp2 cutBottom1
            C rot-4-curve2Cp1 rot-4-curve2Cp2 rot-4-cutBottom2
            C rot-3-curve3Cp1 rot-3-curve3Cp2 rot-3-cutBottom3
            C rot-2-curve4Cp1 rot-2-rightBottom rot-2-rightBottom
            L rot-1-rightCorner
            L shapedTip
            C rot-2-cutTop4 cutTop3 centerTop
            ', $this->o('sa')*-0.5,1,['class' => 'sa fabric']);
            $p->newPath('sa-close', 'M sa-endPoint L standCenterTop M sa-startPoint L centerTop', ['class' => 'sa fabric']);
            $p->newNote('sa', 'rot-3-cutBottom3', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 9, 30, $this->o('sa')/4);
        }
    }

    /**
     * Finalizes the cuff facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeCuffFacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['cuffFacing'];
        
        // Title
        $p->newPoint('titleAnchor', $p->x('bottomRight')/2, $p->y('bottomRight')/2);
        $p->addTitle('titleAnchor', 9, $this->t($p->title), '2x '.$this->t('from fabric'));

        // Grainline
        $p->newPoint('grainlineTop', $p->x('topLeft')+50, $p->y('topLeft')+5);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('bottomLeft')-5);
        $p->newGrainline('grainlineBottom','grainlineTop', $this->t('grainline'));

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline', $this->o('sa')*-0.5,1,['class' => 'sa fabric']);
            $p->newNote('sa', 'notchRight', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 9, 30, $this->o('sa')/-4);
        }
    }

    /**
     * Finalizes the pocket
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePocket($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocket'];
        
        // Title
        $p->addPoint('titleAnchor', $p->shift('pocketTopLeft',-40,120));
        $p->addTitle('titleAnchor', 10, $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('from lining')
        );

        // Grainline
        $p->newPoint('grainlineTop', $p->x('pocketTopLeft')+50, $p->y('pocketTopLeft')+5);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('pocketBottomLeftRight')-5);
        $p->newGrainline('grainlineBottom','grainlineTop', $this->t('grainline'));

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline', $this->o('sa')*-0.5,1,['class' => 'sa fabric']);
            $p->addPoint('sa-line-pocketTopLeftTOpocketTopRight', $p->shift('sa-line-pocketTopLeftTOpocketTopRight', 90, $this->o('sa')*1.5));
            $p->addPoint('sa-line-pocketTopRightTOpocketTopLeft', $p->shift('sa-line-pocketTopRightTOpocketTopLeft', 90, $this->o('sa')*1.5));
            $p->addPoint('note1anchor', $p->shift('pocketTopRight', 180, 60));
            $p->newNote('sa1', 'note1anchor', $this->t('Twice the standard seam allowance')."\n(".$p->unit($this->o('sa')*2).')', 6, 20, $this->o('sa')*-1);
            $p->newNote('sa2', 'pocketBottomRightTop', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 9, 20, $this->o('sa')/-4);
            // Straighten HEM 
            $p->newPoint('sa-line-pocketTopLeftTOpocketTopRight', $p->x('sa-endPoint'), $p->y('sa-line-pocketTopLeftTOpocketTopRight'));
            $p->newPoint('sa-line-pocketTopRightTOpocketTopLeft', $p->x('sa-line-pocketTopRightTOpocketBottomRightTop'), $p->y('sa-line-pocketTopRightTOpocketTopLeft'));
        }
    }

    /**
     * Finalizes the pocket flap
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePocketFlap($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketFlap'];
        
        // Title
        $p->addPoint('titleAnchor', $p->shift('pocketTopLeft',-10,100));
        $p->addTitle('titleAnchor', 11, $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('from lining')
        , ['scale' => 50, 'align'=>'left']);

        // Grainline
        $p->newPoint('grainlineTop', $p->x('pocketTopLeft')+50, $p->y('pocketTopLeft')-5);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('pocketFlapBottomLeftRight')-5);
        $p->newGrainline('grainlineBottom','grainlineTop', $this->t('grainline'));

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline', $this->o('sa')*-0.5,1,['class' => 'sa fabric']);
            $p->newNote('sa', 'pocketFlapBottomRightTop', $this->t('Half of the standard seam allowance')."\n(".$p->unit($this->o('sa')/2).')', 9, 20, $this->o('sa')/-4);
        }
    }

    /**
     * Finalizes the chest pocket welt
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeChestPocketWelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['chestPocketWelt'];
        
        // Title
        $p->newPoint('titleAnchor', $p->x('bottomRight')/2, $p->y('bottomRight')/2);
        $p->addTitle('titleAnchor', 12, $this->t($p->title), '2x '.$this->t('from fabric'), 'vertical-small');

        // Grainline
        $p->newPoint('grainlineTop', $p->x('topLeft')+10, $p->y('topLeft')+5);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('bottomLeft')-5);
        $p->newGrainline('grainlineBottom','grainlineTop', $this->t('grainline'));

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline', $this->o('sa')/2,1,['class' => 'sa fabric']);
        }
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
     * Adds paperless info for the front part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];
        
        // Heigh left side
        $xBase = $p->x('hemFrontEdge') - 15;
        if($this->o('sa')) {
            $xBase -= $this->o('sa');
            $sa = $this->o('sa');
        } else $sa = 0;
        $p->newHeightDimension('collarBendPoint', 'collarTip', $xBase);
        $p->newHeightDimension('button1Left', 'button2Left', $xBase-15);
        $p->newHeightDimension('button2Left', 'button3Left', $xBase-15);
        $p->newHeightDimension('button3Left', 'collarTip', $xBase-15);
        $p->newHeightDimension('hemFrontEdge', 'collarTip', $xBase-45);
        $p->newHeightDimension('hemFrontEdge', 8, $xBase-60);
        $p->newHeightDimension('hemFrontEdge', 3, $xBase-30);
        $p->newHeightDimension(3, 'collarTip', $xBase-30);
        $p->newHeightDimension('flbTop', 8, $p->x('flbTop')-15-$sa);
        $p->newHeightDimension(3, 'button3Right', $p->x(3)+20);

        // Heigh right side
        $xBase = $p->x('hemSide') + 15;
        if($this->o('sa')) $xBase += $this->o('sa');
        $p->newHeightDimension('flbTop', 8, $p->x('flbTop')-15-$sa);
        $p->newHeightDimension('waistSide', 5, $xBase);
        $p->newHeightDimension('waistSide', 10, $xBase+15);
        $p->newHeightDimension('waistSide', 12, $xBase+30);
        $p->newHeightDimension('waistSide', 8, $xBase+45);
        $p->newHeightDimension('pocketFlapTopRight', 'waistSide', $xBase);
        $p->newHeightDimension('pocketTopRight', 'waistSide', $xBase+15);
        $p->newHeightDimension('hemSide', 'waistSide', $xBase+30);

        // Width top
        $p->newWidthDimensionSm('collarBendPoint','collarTip', $p->y('collarTip')-15-$sa);
        $p->newWidthDimension('collarBendPoint',9, $p->y('collarTip')-30-$sa);
        $p->newWidthDimension('collarBendPoint','flbTop', $p->y(8)-15-$sa);
        $p->newWidthDimension('collarBendPoint',8, $p->y(8)-30-$sa);
        $p->newWidthDimension('collarBendPoint',10, $p->y(8)-45-$sa);
        $p->newWidthDimension('collarBendPoint',12, $p->y(8)-60-$sa);
        $p->newWidthDimension('collarBendPoint',5, $p->y(8)-75-$sa);
        $p->newLinearDimension(3, 'waistSide');

        // Width bottom
        $p->newWidthDimension('hemFrontEdge','hemMiddle', $p->y('hemFrontEdge')+15+$sa*5);
        $p->newWidthDimension('hemFrontEdge','flbBottom', $p->y('hemFrontEdge')+30+$sa*5);
        $p->newWidthDimension('hemFrontEdge','hemSide', $p->y('hemFrontEdge')+45+$sa*5);

        // Main pocket
        $p->newHeightDimension('pocketBottomRightLeft', 'pocketTopRight', $p->x('pocketBottomRightLeft')-10);
        $p->newHeightDimension('pocketFlapBottomLeftRight', 'pocketFlapTopLeft', $p->x('pocketFlapBottomLeftRight')+10);
        $p->newWidthDimension('pocketBottomLeftTop','pocketBottomRightTop', $p->y('pocketBottomLeft')+15);
        $p->newWidthDimension('pocketFlapTopLeft','pocketFlapTopRight', $p->y('pocketFlapTopLeft')-15);
        $p->newWidthDimension(4,'pocketFlapBottomLeft', $p->y('pocketFlapBottomLeft'));

        // Chest pocket
        $p->newLinearDimension('chestPocketTopLeft','chestPocketTopRight', -15);
        $p->newLinearDimension('chestPocketBottomRight','chestPocketTopRight', 15);
        $p->newNote(1, 'chestPocketTopRight', $this->v('chestPocketRotation').' '.$this->t('degree').' '.$this->t('slant'), 1, 30, -20);
        $p->newWidthDimension(3, 'chestPocketTopLeft', $p->y('chestPocketTopLeft')+15);
    }
    
    /**
     * Adds paperless info for the back part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];
        
        // Heigh left side
        $xBase = $p->x('bpTop') - 15;
        if($this->o('sa')) {
            $xBase -= $this->o('sa');
            $sa = $this->o('sa');
        } else $sa = 0;
        // Height left
        $p->newHeightDimension('bpBottom', 'bpTop', $xBase);
        $p->newHeightDimension('bpBottom', 1, $xBase-15);
        $p->newHeightDimension('bpBottom', 8, $xBase-30);
        // Height dart
        $p->newHeightDimension('dartLeft', 'dartTip', $p->x('dartLeft')-15);
        // Height right
        $p->newHeightDimension('waistSide', 5, $p->x(5)+15+$sa);
        $p->newHeightDimension('waistSide', 10, $p->x(5)+30+$sa);
        $p->newHeightDimension('waistSide', 12, $p->x(5)+45+$sa);
        // Width top
        $p->newWidthDimension('bpTop', 1, $p->y(8)-15-$sa);
        $p->newWidthDimension(1, 8, $p->y(8)-15-$sa);
        $p->newWidthDimension(1, 10, $p->y(8)-30-$sa);
        $p->newWidthDimension(1, 12, $p->y(8)-45-$sa);
        $p->newWidthDimension(1, 5, $p->y(8)-60-$sa);
        // Width bottom
        $p->newWidthDimension('bpBottom', 'dartLeft', $p->y('dartLeft')+15+$sa);
        $p->newWidthDimensionSm('dartLeft', 'dartRight', $p->y('dartLeft')+15+$sa);
        $p->newWidthDimension('bpBottom', 'waistSide', $p->y('dartLeft')+30+$sa);
        $p->newWidthDimension('bpBottom', 5, $p->y('dartLeft')+45+$sa);
        // Triangle
        $p->newWidthDimensionSm('bpTopIn', 'triangleRight', $p->y('bpTopIn')+15);
        $p->newHeightDimensionSm('triangleRight', 'triangleTop', $p->x('triangleRight')+15);
    }
    
    /**
     * Adds paperless info for the tail part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessTail($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['tail'];
        
        // Width
        $yBase = $p->y('leftTop-3') + 15;
        if($this->o('sa')) {
            $yBase += $this->o('sa');
            $sa = $this->o('sa');
        } else $sa = 0;
        $p->newWidthDimension('leftTop-3', 'leftPleat1-3', $yBase);
        $p->newWidthDimension('leftPleat1-3', 'leftPleat2-3', $yBase);
        $p->newWidthDimension('leftPleat2-3', 'cbTop-3', $yBase);
        $p->newWidthDimension('cbTop-3', 'leftPleat3-3', $yBase);
        $p->newWidthDimension('leftPleat3-3', 'dimBottom', $yBase);
        $p->newWidthDimension('dimBottom', 'waistTop-3', $yBase);
        $p->newWidthDimension('leftTop-3', 'waistTop-3', $yBase+15);

    }

    /**
     * Adds paperless info for the topsleeve part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessTopsleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['topsleeve'];
        
        // Heigh left side
        $xBase = $p->x('topsleeveLeftEdge') - 15;
        if($this->o('sa')) {
            $xBase -= $this->o('sa');
            $sa = $this->o('sa');
        } else $sa = 0;
        // Height left
        $p->newHeightDimension('topsleeveWristLeft', 'topsleeveLeftEdge', $xBase);
        $p->newHeightDimension('cuffBottomLeft', 'topsleeveLeftEdge', $xBase-15);
        $p->newHeightDimension('topsleeveLeftEdge', 'sleeveTop', $xBase);
        $p->newHeightDimension('topsleeveRightEdge', 'sleeveTop', $p->x('topsleeveRightEdge')+15+$sa);
        $p->newHeightDimension('elbowRight', 'topsleeveRightEdge', $p->x('topsleeveRightEdge')+15+$sa);

        $p->newWidthDimension('topsleeveLeftEdge','sleeveTop', $p->y('sleeveTop')-15-$sa);
        $p->newWidthDimension('sleeveTop', 'backPitchPoint', $p->y('sleeveTop')-15-$sa);
        $p->newWidthDimension('sleeveTop', 'topsleeveRightEdge', $p->y('sleeveTop')-30-$sa);
        $p->newWidthDimension('topsleeveLeftEdge', 'topsleeveRightEdge', $p->y('sleeveTop')-45-$sa);

        // Linear
        $p->newLinearDimension('topsleeveLeftEdge', 'topsleeveRightEdge');
        $p->newLinearDimension('cuffBottomLeft', 'topsleeveWristLeft', 15);
        $p->newLinearDimension('topsleeveElbowLeft', 'elbowRight');
        $p->newLinearDimension('undersleeveWristRight', 'elbowRight', 15+$sa);
        $p->newLinearDimension('topsleeveWristLeft', 'topsleeveElbowLeft', -15-$sa);
        $p->newLinearDimension('topsleeveWristLeft', 'topsleeveWristRight', -15);

        // Note
        $p->newNote(1, 'topsleeveWristLeftHelperBottom', $this->o('sleeveBend').' '.$this->t('degree').' '.$this->t('slant'), 6, 30);

    }

    /**
     * Adds paperless info for the undersleeve part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessUndersleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['undersleeve'];
        
        // Heigh left side
        $xBase = $p->x('cuffBottomLeft') - 15;
        if($this->o('sa')) {
            $xBase -= $this->o('sa');
            $sa = $this->o('sa');
        } else $sa = 0;
        // Height left
        $p->newHeightDimension('undersleeveLeftEdge', 'undersleeveTip', $xBase-15);
        $p->newHeightDimension('undersleeveElbowLeft', 'undersleeveLeftEdge', $xBase);
        $p->newHeightDimension('cuffBottomLeft', 'undersleeveLeftEdge', $xBase-15);
        $p->newHeightDimension('cuffBottomLeft', 'undersleeveTip', $xBase-30);

        $p->newWidthDimension('undersleeveLeftEdge','grainlineTop', $p->y('undersleeveTip')-$sa-15);
        $p->newWidthDimension('grainlineTop','undersleeveTip', $p->y('undersleeveTip')-$sa-15);
        $p->newWidthDimension('undersleeveLeftEdge','undersleeveTip', $p->y('undersleeveTip')-$sa-30);
        $p->newLinearDimension('undersleeveLeftEdge','undersleeveRightEdge');
        $p->addPoint('rightEdge', $p->curveEdge('elbowRight','elbowRightCpTop','undersleeveRightEdgeCpBottom','undersleeveRightEdge','right'));
        $p->newWidthDimension('undersleeveLeftEdge','rightEdge', $p->y('undersleeveTip')-$sa-45);
        $p->newLinearDimension('undersleeveElbowLeft','elbowRight');
        $p->newLinearDimension('undersleeveWristLeft','undersleeveWristRight', -15);
        $p->newLinearDimension('cuffBottomLeft', 'undersleeveWristLeft', 15);
        $p->newLinearDimension('undersleeveWristLeft', 'undersleeveElbowLeft', -15-$sa/2);
        $p->newLinearDimension('undersleeveWristRight', 'elbowRight', 15+$sa/2);
        
        // Note
        $p->newNote(1, 'topsleeveWristLeftHelperBottom', $this->o('sleeveBend').' '.$this->t('degree').' '.$this->t('slant'), 6, 30);
    }

    /**
     * Adds paperless info for the belt part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['belt'];

        $p->newWidthDimension('topLeftBottom-b','bottomRight', $p->y('bottomRight')+$this->o('sa')/2+15);
        $p->newHeightDimension('bottomRight', 'topRight', $p->x('bottomRight')+$this->o('sa')+15);
                    
    }
    
    /**
     * Adds paperless info for the collar stand part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCollarStand($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collarStand'];

        $p->newLinearDimensionSm('centerBottom','standCenterTop');
        $p->newWidthDimension('leftBottom','rightBottom', $p->y('rightBottom')+15+$this->o('sa')/2);
        $p->newHeightDimension('rightBottom','standCenterTop', $p->x('rightBottom')+15+$this->o('sa')/2);
                    
    }
    
    /**
     * Adds paperless info for the collar part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCollar($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['collar'];

        $p->newHeightDimension('standCenterTop','centerTop', $p->x('standCenterTop')-15);
        $p->newHeightDimension('rot-2-rightBottom','centerTop', $p->x('standCenterTop')-30);
        $p->newHeightDimension('rot-1-rightCorner','centerTop', $p->x('standCenterTop')-45);
        $p->newHeightDimension('shapedTip','centerTop', $p->x('shapedTip')+15+$this->o('sa')/2);
        $p->newWidthDimension('standCenterTop','rot-2-rightBottom', $p->y('rot-1-rightCorner')+15+$this->o('sa')/2);
        $p->newWidthDimension('standCenterTop','rot-1-rightCorner', $p->y('rot-1-rightCorner')+30+$this->o('sa')/2);
        $p->newWidthDimension('standCenterTop','shapedTip', $p->y('rot-1-rightCorner')+45+$this->o('sa')/2);
                    
    }
    
    /**
     * Adds paperless info for the cuff facing part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCuffFacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['cuffFacing'];

        $p->newHeightDimension('bottomLeftRight-r','topRight', $p->x('topRight')+15+$this->o('sa')/2);
        $p->newWidthDimension('bottomLeftTop','bottomLeftTop-r', $p->y('bottomLeftRight')+15+$this->o('sa')/2);
                    
    }
    
    /**
     * Adds paperless info for the pocket part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPocket($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocket'];

        $p->newHeightDimension('pocketBottomRightLeft','pocketTopRight', $p->x('pocketTopRight')+15+$this->o('sa')/2);
        $p->newWidthDimension('pocketBottomLeftTop','pocketBottomRightTop', $p->y('pocketBottomLeftRight')+15+$this->o('sa')/2);
                    
    }
    
    /**
     * Adds paperless info for the pocket flap part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPocketFlap($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketFlap'];

        $p->newHeightDimension('pocketFlapBottomRightLeft','pocketFlapTopRight', $p->x('pocketFlapTopRight')+30+$this->o('sa')/2);
        $p->newHeightDimension('pocketFlapBottomRightLeft','pocketFlapBottomRightTop', $p->x('pocketFlapTopRight')+15+$this->o('sa')/2);
        $p->newWidthDimension('pocketFlapTopLeft','pocketFlapTopRight', $p->y('pocketFlapTopRight')-15-$this->o('sa')/2);
        $p->newWidthDimension('pocketFlapBottomLeftTop','pocketFlapBottomRightTop', $p->y('pocketFlapBottomLeftRight')+15+$this->o('sa')/2);
                    
    }
    
    /**
     * Adds paperless info for the chest pocket welt part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessChestPocketWelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['chestPocketWelt'];
        $p->newHeightDimension('bottomRight','topRight', $p->x('topRight')+15+$this->o('sa')/2);
        $p->newWidthDimension('bottomLeft','bottomRight', $p->y('bottomLeft')+15+$this->o('sa')/2);
    }
}
