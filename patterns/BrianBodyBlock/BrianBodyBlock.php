<?php
/** Freesewing\Patterns\BrianBodyBlock class */
namespace Freesewing\Patterns;

/**
 * A male body block designed by Joost
 *
 * This is a basic body block that serves as a starting
 * point for other patterns. It is not complete in
 * this form, because the sleeve is not adapted to
 * the armhole. That's because this is not intended
 * to be used as a stand-along block, but to be
 * extended by other patterns.
 *
 * This block is based on the method of Gareth Kershaw
 * with tweaks and improvemens by Joost De Cock.
 * Most importanltly, this has a variable shoulder slope
 * based on the model's measurements.
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BrianBodyBlock extends Pattern
{

    /**
     * Fix collar ease to 1.5cm
     */
    const COLLAR_EASE = 15;

    /**
     * Fix back neck cutout to 2cm
     */
    const NECK_CUTOUT = 20;

    /**
     * Fix sleevecap ease to 1.5cm
     */
    const SLEEVECAP_EASE = 15;

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
        $this->setOption('collarEase', self::COLLAR_EASE);
        $this->setOption('backNeckCutout', self::NECK_CUTOUT);
        $this->setOption('sleevecapEase', self::SLEEVECAP_EASE);

        // Depth of the armhole
        $this->setValue('armholeDepth', 200 + ($model->m('shoulderSlope') / 2 - 27.5) + ($model->m('bicepsCircumference') / 10));

        // Collar widht and depth
        $this->setValue('collarWidth', ($model->getMeasurement('neckCircumference') / 2.42) / 2);
        $this->setValue('collarDepth', ($model->getMeasurement('neckCircumference') + $this->getOption('collarEase')) / 5 - 8);

        // Cut front armhole a bit deeper
        $this->setValue('frontArmholeExtra', 5);
        
        // Tweak factors
        $this->setValue('frontCollarTweakFactor', 1); 
        $this->setValue('frontCollarTweakRun', 0); 
        $this->setValue('sleeveTweakFactor', 1); 
        $this->setValue('sleeveTweakRun', 0); 
    }

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
        $this->finalizeSleeveBlock($model);
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
    }

    /**
     * Drafts the back block
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
    public function draftBackBlock($model)
    {

        /** @var \Freesewing\Part $p */
        $p = $this->parts['backBlock'];

        // Center vertical axis
        $p->newPoint(1, 0, $this->getOption('backNeckCutout'), 'Center back @ neck');
        $p->newPoint(2, 0, $p->y(1) + $this->v('armholeDepth'), 'Center back @ armhole depth');
        $p->newPoint(3, 0, $p->y(1) + $model->getMeasurement('centerBackNeckToWaist'), 'Center back @ waist');
        $p->newPoint(4, 0,
            $model->getMeasurement('centerBackNeckToWaist') + $model->getMeasurement('naturalWaistToHip') + $this->getOption('backNeckCutout') + $this->getOption('lengthBonus'),
            'Center back @ trouser waist');

        // Side vertical axis
        $p->newPoint(5, $model->getMeasurement('chestCircumference') / 4 + $this->getOption('chestEase') / 4, $p->y(2),
            'Quarter chest @ armhole depth');
        $p->clonePoint(5, 'gridAnchor');
        $p->newPoint(6, $p->x(5), $p->y(4), 'Quarter chest @ trouser waist');

        // Back collar
        $p->newPoint(7, $this->v('collarWidth'), $p->y(1), 'Half collar width @ center back');
        $p->newPoint(8, $p->x(7), $p->y(1) - $this->getOption('backNeckCutout'), 'Half collar width @ top of garment');

        // Front collar
        $p->newPoint(9, 0, $p->y(1) + $this->v('collarDepth') * $this->v('frontCollarTweakFactor'), 'Center front collar depth');

        // Armhole
        $p->newPoint(10, $model->getMeasurement('acrossBack') / 2, $p->y(1) + $p->deltaY(1, 2) / 2, 'Armhole pitch point');
        $p->newPoint(11, $p->x(10), $p->y(2), 'Armhole pitch width @ armhole depth');
        $p->newPoint(12,
            $p->x(7) + sqrt(pow($model->getMeasurement('shoulderLength'), 2) - pow($model->getMeasurement('shoulderSlope') / 2,
                    2)), $model->getMeasurement('shoulderSlope') / 2, 'Shoulder tip');
        $p->addPoint(13, $p->Shift(5, 180, $p->distance(11, 5) / 4), 'Left curve control point for 5');
        $p->addPoint('.help1', $p->shift(11, 45, 5), '45 degrees upwards');
        $p->addPoint('.help2', $p->beamsCross(11, '.help1', 5, 10), 'Intersection');
        $p->addPoint(14, $p->shiftTowards(11, '.help2', $p->distance(11, '.help2') / 2), 'Point on armhole curve');
        $p->addPoint(15, $p->shift(14, 135, 25), 'Top curve control point for 14');
        $p->addPoint(16, $p->Shift(14, -45, 25), 'Bottom control point for 14');
        $tmp = $p->deltaY(12, 10) / 3;
        $p->addPoint(17, $p->shift(10, 90, $tmp), 'Top curve control point for 10');
        $p->addPoint(18, $p->shift(10, -90, $tmp), 'Bottom curve control point for 10');
        $p->addPoint(19, $p->shift(12, $p->angle(8, 12) + 90, 10), 'Bottom control point for 12');

        // Control points for collar
        $p->addPoint(20, $p->shift(8, $p->angle(8, 12) + 90, $this->getOption('backNeckCutout')),
            'Curve control point for collar');
        $p->newPoint(21, $p->x(8), $p->y(9));

        // Paths
        $path = 'M 1 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 1 1 z';
        $p->newPath('seamline', $path);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Finalizes the back block
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBackBlock($model)
    {
        $p = $this->parts['backBlock'];
        // Title anchor
        $p->newPoint('titleAnchor', $p->x(10) / 2, $p->y(10), 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title));
        $p->newSnippet('logo', 'logo', 'titleAnchor');
    }

    /**
     * Drafts the front block
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
    public function draftFrontBlock($model)
    {
        $this->clonePoints('backBlock', 'frontBlock');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontBlock'];

        $frontExtra = 5; // Cutting out armhole a bit deeper at the front
        $p->addPoint(10, $p->shift(10, 180, $this->v('frontArmholeExtra')));
        $p->addPoint(17, $p->shift(17, 180, $this->v('frontArmholeExtra')));
        $p->addPoint(18, $p->shift(18, 180, $this->v('frontArmholeExtra')));

        $path = 'M 9 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 9 z';
        $p->newPath('seamline', $path);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Finalizes the front block
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrontBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontBlock'];
        $p->addTitle('titleAnchor', 1, $this->t($p->title));
        $p->newSnippet('logo', 'logo', 'titleAnchor');
    }

    /**
     * Drafts the sleeve block
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
    public function draftSleeveBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeveBlock'];

        $this->setValue('sleevecapSeamLength', ($this->armholeLen() + $this->o('sleevecapEase'))*$this->v('sleeveTweakFactor'));

        // Sleeve center
        $p->newPoint(1, 0, 0, 'Origin (Center sleeve @ shoulder)');
        $p->newPoint(2, 0, $this->v('sleevecapSeamLength') / 3, 'Center sleeve @ sleevecap start');
        $p->clonePoint(2, 'gridAnchor');
        $p->newPoint(3, 0, $model->getMeasurement('sleeveLengthToWrist'), 'Center sleeve @ wrist');

        // Sleeve half width
        $p->newPoint(4, $model->getMeasurement('bicepsCircumference') / 2 + $this->getOption('bicepsEase') / 2, 0,
            'Half width of sleeve @ shoulder');
        $p->newPoint(5, $p->x(4), $p->y(2), 'Half width of sleeve @ sleevecap start');
        $p->newPoint(6, $p->x(4), $p->y(3), 'Half width of sleeve @ wrist');

        // Sleeve quarter width
        $p->newPoint(7, $p->x(4) / 2, 0, 'Quarter width of sleeve @ shoulder');
        $p->newPoint(8, $p->x(7), $p->y(2), 'Quarter width of sleeve @ sleevecap start');
        $p->newPoint(9, $p->x(7), $p->y(3), 'Quarter width of sleeve @ wrist');

        // Mirror to get a full sleeve
        for ($i = 4; $i <= 9; ++$i) {
            $p->addPoint($i * -1, $p->flipX($i, 0));
        }

        // Back pitch point
        $p->newPoint(10, $p->x(-7), $this->v('sleevecapSeamLength') / 6 - 15, 'Back Pitch Point');

        // Front pitch point gets 5mm extra room
        $p->newPoint(11, $p->x(7) + 5, $p->y(10) + 15, 'Front Pitch Point');

        // Angles of the segments of the sleevecap
        $angleBackLow = $p->angle(-5, 10);
        $angleBackHigh = $p->angle(10, 1);
        $angleFrontLow = $p->angle(5, 11);
        $angleFrontHigh = $p->angle(11, 1);

        // The 4 quarter marks
        $p->addPoint(12, $p->shiftTowards(-5, 10, $p->distance(-5, 10) / 2), 'Back low quarter');
        $p->addPoint(13, $p->shiftTowards(10, 1, $p->distance(10, 1) / 2), 'Back high quarter');
        $p->addPoint(14, $p->shiftTowards(1, 11, $p->distance(1, 11) / 2), 'Front high quarter');
        $p->addPoint(15, $p->shiftTowards(11, 5, $p->distance(11, 5) / 2), 'Front low quarter');

        // Bulge out or in at quarter marks
        $p->addPoint(16, $p->shift(12, $angleBackLow + 90, 5), 'Back low valley');
        $p->addPoint(17, $p->shift(13, $angleBackHigh - 90, 15), 'Back high peak');
        $p->addPoint(18, $p->shift(14, $angleFrontHigh + 90, 23), 'Front high peak');
        $p->addPoint(19, $p->shift(15, $angleFrontLow - 90, 15), 'Front low valley');

        // Control points for bulges
        // Make control point offset relative to sleeve width
        $cpOffset = $p->x(7) * 0.27;
        $p->addPoint(20, $p->shift(16, $angleBackLow, $cpOffset), 'Bottom control point for 16');
        $p->addPoint(21, $p->shift(16, $angleBackLow, -1 * $cpOffset), 'Top control point for 16');
        $p->addPoint(22, $p->shift(17, $angleBackHigh, $cpOffset), 'Bottom control point for 17');
        $p->addPoint(23, $p->shift(17, $angleBackHigh, -1 * $cpOffset), 'Top control point for 17');
        $p->addPoint(24, $p->shift(18, $angleFrontHigh, $cpOffset), 'Bottom control point for 18');
        $p->addPoint(25, $p->shift(18, $angleFrontHigh, -1 * $cpOffset), 'Top control point for 18');
        $p->addPoint(26, $p->shift(19, $angleFrontLow, $cpOffset), 'Bottom control point for 19');
        $p->addPoint(27, $p->shift(19, $angleFrontLow, -1 * $cpOffset), 'Top control point for 19');

        // Sleeve crown
        $p->addPoint(28, $p->shift(1, 180, $cpOffset), 'Back control point for crown point');
        $p->addPoint(29, $p->shift(1, 0, $cpOffset), 'Front control point for crown point');
        // Shift crown point to the front by 0.5cm
        $p->addPoint(30, $p->shift(1, 0, 5), 'Sleeve crown point');

        // Wrist
        $wristWidth = $model->getMeasurement('wristCircumference') + $this->getOption('cuffEase');
        $p->newPoint(31, $wristWidth / -2, $p->y(3), 'Wrist point back');
        $p->newPoint(32, $wristWidth / 2, $p->y(3), 'Wrist point front');

        // Elbow location
        $p->newPoint(33, 0, $p->y(2) + $p->distance(2, 3) / 2 - 25, 'Elbow point');
        $p->addPoint('.help1', $p->shift(33, 0, 10));
        $p->addPoint(34, $p->beamsCross(-5, 31, 33, '.help1'), 'Elbow point back side');
        $p->addPoint(35, $p->beamsCross(5, 32, 33, 34), 'Elbow point front side');

        $path = 'M 31 L -5 C -5 20 16 C 21 10 10 C 10 22 17 C 23 28 30 C 29 25 18 C 24 11 11 C 11 27 19 C 26 5 5 L 32 z';
        $p->newPath('seamline', $path);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Finalizes the front block
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSleeveBlock($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeveBlock'];
        $p->newPoint('titleAnchor', $p->x(2), $this->parts['frontBlock']->y('titleAnchor'));
        $p->addTitle('titleAnchor', 3, $this->t($p->title));

        $p->newText('test', 33, $this->t('warning'), ['line-height' => 10, 'class' => 'text-center text-xl']);

        $p->newSnippet('logo', 'logo', 'titleAnchor');
    }

    /**
     * Calculates the armhole length
     *
     * @return float The armhole length
     */
    private function armholeLen()
    {
        /** @var \Freesewing\Part $back */
        $back = $this->parts['backBlock'];
        /** @var \Freesewing\Part $front */
        $front = $this->parts['frontBlock'];

        return ($back->curveLen(12, 19, 17, 10) + $back->curveLen(10, 18, 15, 14) + $back->curveLen(14, 16, 13,
                    5)) + ($front->curveLen(12, 19, 17, 10) + $front->curveLen(10, 18, 15, 14) + $front->curveLen(14, 16, 13, 5));
    }
}
