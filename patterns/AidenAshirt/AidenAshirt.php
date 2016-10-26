<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\AidenAshirt class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class AidenAshirt extends JoostBodyBlock
{
    public function loadParts()
    {
        foreach ($this->config['parts'] as $part => $title) {
            $this->addPart($part);
            $this->parts[$part]->setTitle($title);
        }
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
    }
    
    public function draft($model)
    {
        $this->buildCore($model);

        $this->draftFront($model);
        $this->finalizeFront($model);
        $this->draftBack($model);
        $this->finalizeBack($model);
    }

    public function sample($model)
    {
        $this->buildCore($model);

        $this->draftFront($model);
        $this->draftBack($model);
    }

    public function buildCore($model)
    {
        $this->validateOptions();
        $this->loadHelp($model);
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
    }

    public function validateOptions()
    {
        // shifTowards can't deal with 0, so shoulderStrapPlacement should be at least 0.001
        if ($this->getOption('shoulderStrapPlacement') == 0) {
            $this->setOption('shoulderStrapPlacement', 0.001);
        }
        // a stretchFactor below 50% is obviously wrong
        if ($this->getOption('stretchFactor') < 0.5) {
            $this->setOption('stretchFactor', 0.5);
        }
        // These are irrelevant, but needed for JoostBodyBlock
        $this->setOption('collarEase', 15);
        $this->setOption('backNeckCutout', 20);
    }

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
        $p->addPoint(104, $p->linesCross(103, '.help1', 100, '.help2'), 'Control point for 100');
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
            $p->curveCrossesY(112, 112, 113, 5, $p->y(5) + $this->getOption('armholeDrop'), '.help');
            $p->clonePoint('.help-1', 5);
            // Update other points accordingly
            $p->newPoint(107, $p->x(107), $p->y(5), 'Control point for 5');
            $p->newPoint(2, $p->x(2), $p->y(5), 'Center back @ armhole depth');
        }

        // Seamline
        $seamline = 'M 3 L 111 L 110 L 112 C 113 5 5 C 107 106 102 L 103 C 105 104 100 z';
        $p->newPath('seamline', $seamline, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    public function draftBack($model)
    {
        $this->clonePoints('backBlock', 'back');
        $this->clonePoints('front', 'back');
        $p = $this->parts['back'];

        // Adjust neck
        $p->newPoint(100, $p->x(100), $p->y(1) + 12.5, 'Neck bottom @ CB');
        $p->clonePoint(100, 'gridAnchor');
        $p->newPoint(104, $p->deltaX(100, 103) / 2, $p->y(100), 'Control point for 100');
        $p->addPoint(105, $p->linesCross(100, 104, 103, 105), 'Control point for 103');

        // Adjust armhole
        $p->addPoint('106max', $p->linesCross(102, 106, 2, 5), 'Max CP for armhole');
        // backlineBend should stay between 0.5 and 0.9, so let's make sure of that.
        $backlineBend = 0.5 + $this->getOption('backlineBend') * 0.4;
        $p->addPoint(106, $p->shiftTowards(102, '106max', $p->distance(102, '106max') * $backlineBend), 'Control point for 102');
        $p->addPoint(107, $p->shiftTowards(5, '106max', $p->distance(5, '106max') * $backlineBend * -1), 'Control point for 5');

        // Seamline
        $seamline = 'M 3 L 111 L 110 L 112 C 113 5 5 C 107 106 102 L 103 C 105 104 100 z';
        $p->newPath('seamline', $seamline, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['seamline']->setSample();
    }

    public function finalizeFront($model)
    {
        $p = $this->parts['front'];

        // Seam allowance | Point indexes from 200 upward
        $p->offsetPathString('sa1', 'M 102 L 103', 10);
        $p->newPath('shoulderSA', 'M 102 L sa1-line-102TO103 L sa1-line-103TO102 L 103', ['class' => 'seam-allowance']);
        $p->addPoint(200, $p->shift(111, -90, 20), 'Hem allowance @ CF');
        $p->addPoint(201, $p->shift(110, -90, 20), 'Hem allowance @ CF');
        $p->addPoint(201, $p->shift(201, 0, 10), 'Hem allowance @ side');
        $p->offsetPathString('sideSA', 'M 110 L 112 C 113 5 5', 10, 1, ['class' => 'seam-allowance']);
        $p->newPath('hemSA', 'M 111 L 200 L 201 L sideSA-line-110TO112 M 5 L sideSA-curve-5TO112', ['class' => 'seam-allowance']);

        // Instructions | Point indexes from 300 upward
        // Cut on fold line and grainline
        $p->newPoint(300, 0, $p->y(100) + 20, 'Cut on fold endpoint top');
        $p->newPoint(301, 20, $p->y(300), 'Cut on fold corner top');
        $p->newPoint(302, 0, $p->y(111) - 20, 'Cut on fold endpoint bottom');
        $p->newPoint(303, 20, $p->y(302), 'Cut on fold corner bottom');
        $p->addPoint(304, $p->shift(301, 0, 15), 'Grainline top');
        $p->addPoint(305, $p->shift(303, 0, 15), 'Grainline bottom');

        $p->newPath('cutOnFold', 'M 300 L 301 L 303 L 302', ['class' => 'double-arrow stroke-note stroke-lg']);
        $p->newTextOnPath('cutonfold', 'M 303 L 301', $this->t('Cut on fold'), ['line-height' => 12, 'class' => 'text-lg fill-note', 'dy' => -2]);
        $p->newPath('grainline', 'M 304 L 305', ['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M 305 L 304', $this->t('Grainline'), ['line-height' => 12, 'class' => 'text-lg fill-gray1', 'dy' => -2]);

        // Title
        $p->newPoint('titleAnchor', $p->x(5) * 0.4, $p->x(5) + 40, 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title), $this->t('Cut 1 on fold'));
        $p->newSnippet('logo', 'logo', 'titleAnchor');

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 100));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Notes
        $noteAttr = ['line-height' => 7, 'class' => 'text-lg'];
        $p->addPoint(306, $p->shift(101, 180, 3), 'Note 1 anchor');
        $p->newNote(1, 306, $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 6, 10, -5, $noteAttr);

        $p->addPoint('.help1', $p->shift(100, 90, 20));
        $p->curveCrossesY(100, 104, 105, 103, $p->y('.help1'), '.help');
        $p->clonePoint('.help-1', 307);
        $p->newNote(2, 307, $this->t("No\nseam\nallowance"), 4, 15, 0, $noteAttr);

        $p->curveCrossesY(5, 107, 106, 102, $p->y(301), '.help');
        $p->clonePoint('.help-1', 308);
        $p->newNote(3, 308, $this->t("No\nseam\nallowance"), 8, 15, 0, $noteAttr);

        $p->addPoint(309, $p->shift(112, -90, $p->distance(110, 112) / 2));
        $p->newNote(4, 309, $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 15, -5, $noteAttr);

        $p->newPoint(310, $p->x(110) - 40, $p->y(110), 'Note 5 anchor');
        $p->newNote(5, 310, $this->t('Hem allowance')."\n(".$this->unit(20).')', 12, 15, -10, ['line-height' => 6, 'class' => 'text-lg', 'dy' => -4]);

        if ($this->theme == 'Paperless' || $this->theme == 'Designer' || $this->theme == 'Developer') {
            $pAttr = ['class' => 'measure-lg'];
            $tAttr = ['class' => 'text-lg fill-note text-center', 'dy' => -8];

            $key = 'armholeUnits';
            $path = 'M 102 C 106 107 5';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(5, 107, 106, 102)), $tAttr);

            $key = 'neckholeUnits';
            $path = 'M 100 C 104 105 103';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(100, 104, 105, 103)), $tAttr);

            $key = 'sideSeamUnits';
            $path = 'M 110 L 112 C 113 5 5';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Seam length').': '.$this->unit($p->curveLen(112, 113, 5, 5) + $p->distance(110, 112)), $tAttr);

            $key = 'hemUnits';
            $path = 'M 111 L 110';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(110, 111)), $tAttr);

            $key = 'CBUnits';
            $path = 'M 111 L 100';
            $p->offsetPathString($key, $path, -13, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(100, 111)), ['class' => 'text-lg fill-note text-center', 'dy' => -7]);

            $key = 'StrapUnits';
            $path = 'M 103 L 102';
            $p->offsetPathString($key, $path, -20, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(102, 103)), ['class' => 'text-lg fill-note text-center', 'dy' => -13]);
        }
    }

    public function finalizeBack($model)
    {
        $p = $this->parts['back'];

        // Title
        $p->newPoint('titleAnchor', $p->x(5) * 0.4, $p->x(5) + 40, 'Title anchor');
        $p->addTitle('titleAnchor', 2, $this->t($p->title), $this->t('Cut 1 on fold'));
        $p->newSnippet('logo', 'logo', 'titleAnchor');

        // Seam allowance | Point indexes from 200 upward
        $p->newPath('shoulderSA', 'M 102 L sa1-line-102TO103 L sa1-line-103TO102 L 103', ['class' => 'seam-allowance']);
        $p->offsetPathString('sideSA', 'M 110 L 112 C 113 5 5', 10, 1, ['class' => 'seam-allowance']);
        $p->newPath('hemSA', 'M 111 L 200 L 201 L sideSA-line-110TO112 M 5 L sideSA-curve-5TO112', ['class' => 'seam-allowance']);

        // Instructions | Point indexes from 300 upward
        // Cut on fold line and grainline
        $p->newPoint(300, 0, $p->y(100) + 20, 'Cut on fold endpoint top');
        $p->newPoint(301, 20, $p->y(300), 'Cut on fold corner top');
        $p->addPoint(304, $p->shift(301, 0, 15), 'Grainline top');

        $p->newPath('cutOnFold', 'M 300 L 301 L 303 L 302', ['class' => 'double-arrow stroke-note stroke-lg']);
        $p->newTextOnPath('cutonfold', 'M 303 L 301', $this->t('Cut on fold'), ['line-height' => 12, 'class' => 'text-lg fill-note', 'dy' => -2]);
        $p->newPath('grainline', 'M 304 L 305', ['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M 305 L 304', $this->t('Grainline'), ['line-height' => 12, 'class' => 'text-lg fill-gray1', 'dy' => -2]);

        // Notes
        $noteAttr = ['line-height' => 7, 'class' => 'text-lg'];
        $p->addPoint(306, $p->shift(101, 180, 3), 'Note 1 anchor');
        $p->newNote(1, 306,  $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 6, 10, -5, $noteAttr);

        $p->addPoint('.help1', $p->shift(100, 90, 20));
        $p->newNote(2, 104,  $this->t("No\nseam\nallowance"), 6, 15, 0, $noteAttr);

        $p->curveCrossesY(5, 107, 106, 102, $p->y('106max') / 2, '.help');
        $p->clonePoint('.help-1', 308);
        $p->newNote(3, 308,  $this->t("No\nseam\nallowance"), 8, 15, 0, $noteAttr);

        $p->newNote(4, 309, $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 15, -5, $noteAttr);

        $p->newPoint(309, $p->x(110) - 40, $p->y(110), 'Note 5 anchor');
        $p->newNote(5, 309,  $this->t('Hem allowance')."\n(".$this->unit(20).')', 12, 15, -10, ['line-height' => 6, 'class' => 'text-lg', 'dy' => -4]);

        $armholeLen = $p->curveLen(102, 106, 107, 5) + $this->parts['front']->curveLen(102, 106, 107, 5);
        $neckholeLen = $p->curveLen(103, 105, 104, 100) + $this->parts['front']->curveLen(103, 105, 104, 100);

        $msg = $this->t('Cut two trips to finish the armholes').
            ":\n".
            $this->t('width').
            ': '.
            $this->unit(60).
            "\n".
            $this->t('length').
            ': '.
            $this->unit($armholeLen).
            "\n&#160;\n".
            $this->t('Cut one strip to finish the neck opening').
            ":\n".
            $this->t('width').
            ': '.
            $this->unit(60).
            "\n".
            $this->t('length').
            ': '.
            $this->unit($neckholeLen);

        $p->newPoint('msgAnchor', $p->x(304) + 30, $p->y('scaleboxAnchor'), 'Message anchor');
        $p->newText('binding', 'msgAnchor', $msg, ['class' => 'text-lg fill-note', 'line-height' => 9]);

        if ($this->theme == 'Paperless' || $this->theme == 'Designer' || $this->theme == 'Developer') {
            $pAttr = ['class' => 'measure-lg'];
            $tAttr = ['class' => 'text-lg fill-note text-center', 'dy' => -8];

            $key = 'armholeUnits';
            $path = 'M 102 C 106 107 5';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(5, 107, 106, 102)), $tAttr);

            $key = 'neckholeUnits';
            $path = 'M 100 C 104 105 103';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(100, 104, 105, 103)), $tAttr);

            $key = 'sideSeamUnits';
            $path = 'M 110 L 112 C 113 5 5';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Seam length').': '.$this->unit($p->curveLen(112, 113, 5, 5) + $p->distance(110, 112)), $tAttr);

            $key = 'hemUnits';
            $path = 'M 111 L 110';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(110, 111)), $tAttr);

            $key = 'CBUnits';
            $path = 'M 111 L 100';
            $p->offsetPathString($key, $path, -13, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(100, 111)), ['class' => 'text-lg fill-note text-center', 'dy' => -7]);

            $key = 'StrapUnits';
            $path = 'M 103 L 102';
            $p->offsetPathString($key, $path, -20, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(102, 103)), ['class' => 'text-lg fill-note text-center', 'dy' => -13]);
        }
    }
}
