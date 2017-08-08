<?php
/** Freesewing\Patterns\Core\AaronAshirt class */
namespace Freesewing\Patterns\Core;

use Freesewing\Part;

/**
 * The Aaron A-Shirt pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class AaronAshirt extends BrianBodyBlock
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
        // Options needed for BrianBodyBlock (but irrelevant here)
        $this->setOptionIfUnset('collarEase', self::COLLAR_EASE);
        $this->setOptionIfUnset('backNeckCutout', self::NECK_CUTOUT);
        $this->setOptionIfUnset('sleevecapEase', self::SLEEVECAP_EASE);

        // Values needed for BrianBodyBlock (but irrelevant here)
        $this->setValue('frontCollarTweakFactor', 1); 
        $this->setValue('shoulderSlope', $model->m('shoulderSlope')); 
        
        // shifTowards can't deal with 0, so shoulderStrapPlacement should be at least 0.001
        if ($this->getOption('shoulderStrapPlacement') == 0) {
            $this->setOptionIfUnset('shoulderStrapPlacement', 0.001);
        }

        // Depth of the armhole
        $this->setValue('armholeDepth', 200 + ($model->m('shoulderSlope') / 2 - 27.5) + ($model->m('bicepsCircumference') / 10));

        // Collar widht and depth
        $this->setValue('collarWidth', ($model->getMeasurement('neckCircumference') / self::PI) / 2 + 5);
        $this->setValue('collarDepth', ($model->getMeasurement('neckCircumference') + $this->getOption('collarEase')) / 5 - 8);

        // Cut front armhole a bit deeper
        $this->setValue('frontArmholeExtra', 5);

        // Avoid division by zero
        if($this->o('necklineBend') == 0) $this->setOptionIfUnset('necklineBend', 0.01);

        /* Set stretch factor. Don't use setOptionIfUnset here */
        $this->setOption('stretchFactor', $this->stretchToScale($this->o('stretchFactor')));   
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

        $this->finalizeFront($model);
        $this->finalizeBack($model);

        if ($this->isPaperless) {
            $this->paperlessFront($model);
            $this->paperlessBack($model);
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

        $this->draftFront($model);
        $this->draftBack($model);

        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
    }

    /**
     * Drafts the front
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
    public function draftFront($model)
    {
        $this->clonePoints('frontBlock', 'front');
        $p = $this->parts['front'];

        // Moving chest point because stretch
        $p->newPoint(5, ($model->getMeasurement('chestCircumference') + $this->getOption('chestEase')) / 4 * $this->getOption('stretchFactor'), $p->y(5), 'Quarter chest @ armhole depth');

        // Shoulders | Point indexes starting at 100
        $p->newPoint(100, $p->x(9), $p->y(1) + $this->getOption('necklineDrop'), 'Neck bottom @ CF');
        $p->clonePoint(100, 'gridAnchor');
        $p->addPoint(101, $p->shiftTowards(8, 12, $p->distance(8, 12) * $this->getOption('shoulderStrapPlacement') * $this->getOption('stretchFactor')), 'Center of shoulder strap');
        $p->addPoint(102, $p->shiftTowards(101, 12, $this->getOption('shoulderStrapWidth') / 2), 'Shoulder strap edge on the shoulder side');
        $p->addPoint(103, $p->shiftTowards(101, 8, $this->getOption('shoulderStrapWidth') / 2), 'Shoulder strap edge on the neck side');
        $p->addPoint('.help1', $p->shift(103, $p->angle(102, 103) - 90, 20), 'Helper point for 90 degree angle');
        $p->addPoint('.help2', $p->shift(100, 180, 20), 'Helper point to intersect with bottom of neckline');
        $p->addPoint(104, $p->beamsCross(103, '.help1', 100, '.help2'), 'Control point for 100');
        $p->addPoint(105, $p->shiftTowards(103, 104, $p->distance(103, 104) * $this->getOption('necklineBend')), 'Control point for 103');
        $p->addPoint(106, $p->shift(102, $p->angle(102, 103) - 90, $p->deltaY(102, 5) / 2), 'Control point for 102');
        $p->addPoint(107, $p->shift(5, 0, $p->deltaX(5, 102)), 'Control point for 5');

        // Hips
        $p->newPoint(110, ($model->getMeasurement('hipsCircumference') + $this->getOption('hipsEase')) / 4 * $this->getOption('stretchFactor'), $p->y(4) + $this->getOption('lengthBonus'), 'Hips @ trouser waist');
        $p->newPoint(111, $p->x(1), $p->y(110), 'Hips @ CF');

        // Waist -> Same as hips because stretch
        $p->newPoint(112, $p->x(110), $p->y(3), 'Side @ waist');
        $p->addPoint(113, $p->shift(112, 90, $p->deltaY(5, 112) / 3), 'Top control point for 112');

        // Armhole drop
        if ($this->getOption('armholeDrop') > 0) {
            // Move point 5 along curve
            $p->curveCrossesY(112, 112, 113, 5, $p->y(5) + $this->getOption('armholeDrop'), '.help-');
            $p->clonePoint('.help-1', 5);
            // Update other points accordingly
            $p->newPoint(107, $p->x(107), $p->y(5), 'Control point for 5');
            $p->newPoint(2, $p->x(2), $p->y(5), 'Center back @ armhole depth');
        }

        // Seamline
        $seamline = 'M 3 L 111 L 110 L 112 C 113 5 5 C 107 106 102 L 103 C 105 104 100 z';
        $p->newPath('seamline', $seamline, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Drafts the back
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
    public function draftBack($model)
    {
        $this->clonePoints('backBlock', 'back');
        $this->clonePoints('front', 'back');
        $p = $this->parts['back'];

        // Adjust neck
        $p->newPoint(100, $p->x(100), $p->y(1) + 12.5, 'Neck bottom @ CB');
        $p->clonePoint(100, 'gridAnchor');
        $p->newPoint(104, $p->deltaX(100, 103) / 2, $p->y(100), 'Control point for 100');
        $p->addPoint(105, $p->beamsCross(100, 104, 103, 105), 'Control point for 103');

        // Adjust armhole
        $p->addPoint('106max', $p->beamsCross(102, 106, 2, 5), 'Max CP for armhole');
        // backlineBend should stay between 0.5 and 0.9, so let's make sure of that.
        $backlineBend = 0.5 + $this->getOption('backlineBend') * 0.4;
        $p->addPoint(106, $p->shiftTowards(102, '106max', $p->distance(102, '106max') * $backlineBend), 'Control point for 102');
        $p->addPoint(107, $p->shiftTowards(5, '106max', $p->distance(5, '106max') * $backlineBend), 'Control point for 5');

        // Seamline
        $seamline = 'M 3 L 111 L 110 L 112 C 113 5 5 C 107 106 102 L 103 C 105 104 100 z';
        $p->newPath('seamline', $seamline, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['seamline']->setSample();
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
     * Finalizes the front
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFront($model)
    {
        /** @var Part $p */
        $p = $this->parts['front'];

        if($this->o('sa')) {
            // Seam allowance | Point indexes from 200 upward
            $p->offsetPathString('sa1', 'M 102 L 103', $this->o('sa'));
            $p->newPath('shoulderSA', 'M 102 L sa1-line-102TO103 L sa1-line-103TO102 L 103', ['class' => 'fabric sa']);
            $p->addPoint(200, $p->shift(111, -90, $this->o('sa')*2), 'Hem allowance @ CF');
            $p->addPoint(201, $p->shift(110, -90, $this->o('sa')*2), 'Hem allowance @ CF');
            $p->addPoint(201, $p->shift(201, 0, $this->o('sa')), 'Hem allowance @ side');
            $p->offsetPathString('sideSA', 'M 110 L 112 C 113 5 5', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->newPath('hemSA', 'M 111 L 200 L 201 L sideSA-line-110TO112 M 5 L sideSA-curve-5TO112', ['class' => 'fabric sa']);
        }

        // Instructions | Point indexes from 300 upward
        // Cut on fold line and grainline
        $p->newPoint('cofTop', 0, $p->y(100) + 20, 'Cut on fold top');
        $p->newPoint('cofBottom', 0, $p->y(111) - 20, 'Cut on fold bottom');
        $p->newPoint('grainlineTop', 35, $p->y('cofTop'));
        $p->newPoint('grainlineBottom', 35, $p->y('cofBottom'));

        $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on fold'));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(5) * 0.4, $p->x(5) + 40, 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('Cut on fold'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', -90, 115));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 190));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Notes
        if($this->o('sa')) {
            $noteAttr = ['line-height' => 7];
            $p->addPoint(306, $p->shift(101, 180, 3), 'Note 1 anchor');
            $p->newNote(1, 306, $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 6, 10, -5, $noteAttr);

            $p->addPoint('.help1', $p->shift(100, 90, 20));
            $p->curveCrossesY(100, 104, 105, 103, $p->y('.help1'), '.help-');
            $p->clonePoint('.help-1', 307);
            $p->newNote(2, 307, $this->t("No\nseam\nallowance"), 4, 15, 0, $noteAttr);

            $p->curveCrossesY(5, 107, 106, 102, $p->y('grainlineTop'), '.help-');
            $p->clonePoint('.help-1', 308);
            $p->newNote(3, 308, $this->t("No\nseam\nallowance"), 8, 15, 0, $noteAttr);

            $p->addPoint(309, $p->shift(112, -90, $p->distance(110, 112) / 2));
            $p->newNote(4, 309, $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 9, 15, -5, $noteAttr);

            $p->newPoint(310, $p->x(110) - 40, $p->y(110), 'Note 5 anchor');
            $p->newNote(5, 310, $this->t('Hem allowance')."\n(".$p->unit($this->o('sa')*2).')', 12, 15, -10, ['line-height' => 6, 'dy' => -4]);
        }
    }

    /**
     * Finalizes the back
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBack($model)
    {
        $p = $this->parts['back'];

        // Title
        $p->newPoint('titleAnchor', $p->x(5) * 0.4, $p->x(5) + 40, 'Title anchor');
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('Cut on fold'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', -90, 50));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');

        if($this->o('sa')) {
            // Seam allowance | Point indexes from 200 upward
            $p->offsetPathString('sa1', 'M 102 L 103', $this->o('sa'));
            $p->newPath('shoulderSA', 'M 102 L sa1-line-102TO103 L sa1-line-103TO102 L 103', ['class' => 'fabric sa']);
            $p->offsetPathString('sideSA', 'M 110 L 112 C 113 5 5', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->addPoint(200, $p->shift(111, -90, $this->o('sa')*2), 'Hem allowance @ CF');
            $p->addPoint(201, $p->shift(110, -90, $this->o('sa')*2), 'Hem allowance @ CF');
            $p->addPoint(201, $p->shift(201, 0, $this->o('sa')), 'Hem allowance @ side');
            $p->newPath('hemSA', 'M 111 L 200 L 201 L sideSA-line-110TO112 M 5 L sideSA-curve-5TO112', ['class' => 'fabric sa']);
        }

        // Instructions | Point indexes from 300 upward
        // Cut on fold line and grainline
        $p->newPoint('cofTop', 0, $p->y(100) + 20, 'Cut on fold top');
        $p->newPoint('cofBottom', 0, $p->y(111) - 20, 'Cut on fold bottom');
        $p->newPoint('grainlineTop', 35, $p->y('cofTop'));
        $p->newPoint('grainlineBottom', 35, $p->y('cofBottom'));

        $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on fold'));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notes
        if($this->o('sa')) {
            $p->addPoint(306, $p->shift(101, 180, 3), 'Note 1 anchor');
            $p->newNote(1, 306, $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 6, 10, -5);

            $p->addPoint('.help1', $p->shift(100, 90, 20));
            $p->newNote(2, 104, $this->t("No\nseam\nallowance"), 6, 15, 0);

            $p->curveCrossesY(5, 107, 106, 102, $p->y('106max') / 2, '.help-');
            $p->clonePoint('.help-1', 308);
            $p->newNote(3, 308, $this->t("No\nseam\nallowance"), 8, 15, 0);

            $p->addPoint(309, $p->shift(112, -90, $p->distance(110, 112) / 2));
            $p->newNote(4, 309, $this->t("Standard\nseam\nallowance")."\n(".$p->unit($this->o('sa')).')', 9, 15, -5);

            $p->newPoint(309, $p->x(110) - 40, $p->y(110), 'Note 5 anchor');
            $p->newNote(5, 309, $this->t('Hem allowance')."\n(".$p->unit($this->o('sa')*2).')', 12, 15, -10);
        }
        $armholeLen = $p->curveLen(102, 106, 107, 5) + $this->parts['front']->curveLen(102, 106, 107, 5);
        $neckholeLen = $p->curveLen(103, 105, 104, 100) + $this->parts['front']->curveLen(103, 105, 104, 100);

        $msg = $this->t('Cut two strips to finish the armholes').
            ":\n".
            $this->t('width').
            ': '.
            $p->unit(60).
            "\n".
            $this->t('length').
            ': '.
            $p->unit($armholeLen).
            "\n&#160;\n".
            $this->t('Cut one strip to finish the neck opening').
            ":\n".
            $this->t('width').
            ': '.
            $p->unit(60).
            "\n".
            $this->t('length').
            ': '.
            $p->unit($neckholeLen);

        $p->newPoint('msgAnchor', $p->x('grainlineTop') + 30, $p->y('logoAnchor')+50, 'Message anchor');
        $p->newText('binding', 'msgAnchor', $msg);
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

        // Widths at bottom
        $yBase = $p->y(110);
        $p->newWidthDimension(111,110, $yBase+35);
        $p->newWidthDimension(111,5, $yBase+50);

        // Heights left side
        $xBase = $p->x(111);
        $p->newHeightDimension(111,100, $xBase-10);
        $p->newHeightDimension(111,103, $xBase-25);

        // Heights right side
        $xBase = $p->x(5);
        $p->newCurvedDimension('M 110 L 112 C 113 5 5', 20);
        $p->newHeightDimension(110,5, $xBase+35);
        $p->newHeightDimension(110,102, $xBase+50);

        // Widths at top
        $yBase = $p->y(103);
        $p->newLinearDimension(103,102, -20);
        $p->newWidthDimension(100,103, $yBase-20);
        $p->newWidthDimension(100,102, $yBase-35);
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Widths at bottom
        $yBase = $p->y(110);
        $p->newWidthDimension(111,110, $yBase+35);
        $p->newWidthDimension(111,5, $yBase+50);

        // Heights left side
        $xBase = $p->x(111);
        $p->newHeightDimension(111,100, $xBase-10);
        $p->newHeightDimension(111,103, $xBase-25);

        // Heights right side
        $xBase = $p->x(5);
        $p->newCurvedDimension('M 110 L 112 C 113 5 5', 20);
        $p->newHeightDimension(110,5, $xBase+35);
        $p->newHeightDimension(110,102, $xBase+50);

        // Widths at top
        $yBase = $p->y(103);
        $p->newLinearDimension(103,102, -20);
        $p->newWidthDimension(100,103, $yBase-20);
        $p->newWidthDimension(100,102, $yBase-35);
    }

}
