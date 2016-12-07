<?php
/** Freesewing\Patterns\TrayvonTie class */
namespace Freesewing\Patterns;

/**
 * The Trayvon Tie pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TrayvonTie extends Pattern
{
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
        
        foreach ($this->parts as $key => $part) {
            $method = 'finalize'.ucfirst($key);
            $this->$method($model, $part);
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
        $this->loadHelp($model);
        
        $this->draftTieShape($this->parts['interfacingTip'], $this->halfTip*2, $this->halfKnot*2);
        $this->draftTieShape($this->parts['interfacingTail'], $this->halfBackTip*2, $this->halfKnot*2);
        $this->draftTieShape($this->parts['fabricTip'], $this->halfTip*4+40, $this->halfKnot*4+40);
        $this->draftTieShape($this->parts['fabricTail'], $this->halfBackTip*4+40, $this->halfKnot*4+40);

        $this->draftLiningShape($this->parts['liningTip'], $this->halfTip*4+40, $this->halfKnot*4+40);
        $this->draftLiningShape($this->parts['liningTail'], $this->halfBackTip*4+40, $this->halfKnot*4+40);
        
        $this->draftLoop($model, $this->parts['loop']);
        
    }

    /**
     * Sets up some properties shared between methods
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function loadHelp($model)
    {
        $this->halfLength = ($model->getMeasurement('centerBackNeckToWaist') * 2 + $model->getMeasurement('neckCircumference') +150) / 2;
        $this->halfTip = $this->getOption('tipWidth') / 2;
        $this->halfKnot = $this->getOption('knotWidth') / 2;
        $this->halfBackTip = $this->halfKnot + ($this->halfTip - $this->halfKnot) / 2;
    }

    /**
     * Finalizes the Interfacing Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeInterfacingTip($model, $p)
    {
        /* Title */
        $p->addTitle('titleAnchor', 1, $this->t($p->title), $this->t('Cut 1 from tie interfacing'), 'vertical');

        /* Paperless instructions (or not) */
        if ($this->isPaperless) {
            $this->addInstructions($p, true);
        } else {
            $p->newPath('grainline', 'M 1 L 2', ['class' => 'grainline']); // Only add grainline on non-paperless
        }
    }


    /**
     * Finalizes the Interfacing Tail
     *
     * @see \Freesewing\Patterns\TrayvonTie::finalizeInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeInterfacingTail($model, $p)
    {
        /* Title */
        $p->addTitle('titleAnchor', 2, $this->t($p->title), $this->t('Cut 1 from tie interfacing'), 'vertical');
        
        /* Paperless instructions (or not) */
        if (!$this->isPaperless) {
            $p->newPath('grainline', 'M 1 L 2', ['class' => 'grainline']);
        }
        /* Paperless instructions (or not) */
        if ($this->isPaperless) {
            $this->addInstructions($p, true);
        } else {
            $p->newPath('grainline', 'M 1 L 2', ['class' => 'grainline']); // Only add grainline on non-paperless
        }
    }

    /**
     * Finalizes the Fabric Tip
     *
     * @see \Freesewing\Patterns\TrayvonTie::finalizeInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeFabricTip($model, $p)
    {
        /* Title */
        $p->addTitle('titleAnchor', 3, $this->t($p->title), $this->t('Cut 1 from fabric'));
        
        /* Scalebox */
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 50));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        
        /* Tip seam allowance */
        $p->offsetPathString('tipSA', 'M 4 L 1 L 3', -10, 0);
        $p->addPoint(10, $p->beamsCross('tipSA-line-1TO4', 'tipSA-line-4TO1', 6, 4), 'Left edge of tip SA');
        $p->addPoint(11, $p->flipX(10), 'Right edge of tip SA');
        $p->newPath('tipSA', 'M 4 L 10 L tipSA-line-1TO4XllXtipSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);
        
        /* Mid-tie seam allowance */
        $p->offsetPathString('midSA', 'M 5 L 6', -10, 0);
        $p->newPath('midSA', 'M 5 L midSA-line-5TO6 L midSA-line-6TO5 L 6', ['class' => 'seam-allowance']);
        
        /* Notch */
        $p->newSnippet('notch1', 'notch', 'notch1');
        $p->newSnippet('notch2', 'notch', 'notch2');
        
        /* Paperless instructions (or not) */
        if ($this->isPaperless) {
            $this->addInstructions($p);
        } else {
            $p->newPath('grainline', 'M 1 L 2', ['class' => 'grainline']); // Only add grainline on non-paperless
        }
    }
    /**
     * Finalizes the Fabric Tail
     *
     * @see \Freesewing\Patterns\TrayvonTie::finalizeInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeFabricTail($model, $p)
    {
        /* Title */
        $p->addTitle('titleAnchor', 4, $this->t($p->title), $this->t('Cut 1 from fabric'));

        /* Tail seam allowance */
        $p->offsetPathString('tailSA', 'M 4 L 1 L 3', -10, 0);
        $p->addPoint(10, $p->beamsCross('tailSA-line-1TO4', 'tailSA-line-4TO1', 6, 4), 'Left edge of tail SA');
        $p->addPoint(11, $p->flipX(10), 'Right edge of tail SA');
        $p->newPath('tailSA', 'M 4 L 10 L tailSA-line-1TO4XllXtailSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);
        
        /* Mid-tie seam allowance */
        $p->offsetPathString('midSA', 'M 5 L 6', -10, 0);
        $p->newPath('midSA', 'M 5 L midSA-line-5TO6 L midSA-line-6TO5 L 6', ['class' => 'seam-allowance']);
        
        /* Notch */
        $p->newSnippet('notch1', 'notch', 'notch1');
        $p->newSnippet('notch2', 'notch', 'notch2');
        
        /* Paperless instructions (or not) */
        if ($this->isPaperless) {
            $this->addInstructions($p);
        } else {
            $p->newPath('grainline', 'M 1 L 2', ['class' => 'grainline']); // Only add grainline on non-paperless
        }
    }

    /**
     * Finalizes the Lining Tip
     *
     * @see \Freesewing\Patterns\TrayvonTie::finalizeInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeLiningTip($model, $p)
    {
        /* Re-using the fabric tip points */
        $this->clonePoints('fabricTip', 'liningTip');
        
        /* Title */
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '1x '.$this->t('Cut 1 from lining'));
        
        /* Tip seam allowance */
        $p->newPath('tipSA', 'M 4 L 10 L tipSA-line-1TO4XllXtipSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);

        /* Notch */
        $p->newSnippet('notch', 'notch', 1);
        
        /* Paperless instructions */
        if ($this->isPaperless) {
            /* The length measure along the middle */
            $p->newPath('center1', 'M 7 L 89', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('length1', 'M 89 L 7', $this->unit($p->distance(7, 89)), ['class' => 'text-lg fill-note text-center', 'dy' => -3, 'dx' => 30]);
            $p->newPath('center2', 'M 7 L 1', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('length2', 'M 7 L 1', $this->unit($p->distance(7, 1)), ['class' => 'text-lg fill-note text-center', 'dy' => -3]);
                
            /* The width measure of the tip */
            $p->newPath('width', 'M 4 L 3', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('width', 'M 4 L 3', $this->unit($p->distance(3, 4)), ['class' => 'text-lg fill-note text-center', 'dy' => -3, 'dx' => 12]);
            
            /* Seam allowance note */
            $p->newNote(1, 1, $this->t("Standard  seam\nallowance")."(".$this->unit(10).')', 9, 25, 8, ['line-height' => 7, 'class' => 'text-lg']);
        }
    }

    /**
     * Finalizes the Lining Tail
     *
     * @see \Freesewing\Patterns\TrayvonTie::finalizeInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeLiningTail($model, $p)
    {
        /* Re-using the fabric tail points */
        $this->clonePoints('fabricTail', 'liningTail');

        /* Title */
        $p->addTitle('titleAnchor', 6, $this->t($p->title), $this->t('Cut 1 from lining'));
        
        /* Tip seam allowance */
        $p->newPath('tailSA', 'M 4 L 10 L tailSA-line-1TO4XllXtailSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);
        
        /* Notch */
        $p->newSnippet('notch', 'notch', 1);
        
        /* Paperless instructions */
        if ($this->isPaperless) {
            /* The length measure along the middle */
            $p->newPath('center1', 'M 7 L 89', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('length1', 'M 89 L 7', $this->unit($p->distance(7, 89)), ['class' => 'text-lg fill-note text-center', 'dy' => -3, 'dx' => 30]);
            $p->newPath('center2', 'M 7 L 1', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('length2', 'M 7 L 1', $this->unit($p->distance(7, 1)), ['class' => 'text-lg fill-note text-center', 'dy' => -3]);
                
            /* The width measure of the tip */
            $p->newPath('width', 'M 4 L 3', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('width', 'M 4 L 3', $this->unit($p->distance(3, 4)), ['class' => 'text-lg fill-note text-center', 'dy' => -3, 'dx' => 12]);

            /* Seam allowance note */
            $p->newNote(1, 1, $this->t("Standard  seam\nallowance")."(".$this->unit(10).')', 9, 25, 8, ['line-height' => 7, 'class' => 'text-lg']);
        }
    }

    /**
     * Drafts the Loop
     *
     * @see \Freesewing\Patterns\TrayvonTie::draftInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function draftLoop($model, $p)
    {
        $p->newPoint(1, 0, 0, 'Top left');
        $p->newPoint(2, $this->halfBackTip*4 + 40, 40, 'Bottom right');
        $p->newPoint(3, $p->x(2), 0, 'Top right');
        $p->newPoint(4, 0, $p->y(2), 'Bottom left');

        /* Paths */
        $path = 'M 1 L 3 L 2 L 4 z';
        $p->newPath('outline', $path, ['class' => 'seamline']);
        
        /* Anchors */
        $p->newPoint('titleAnchor', $p->x(2)/3, $p->y(2)/2, 'Title anchor point');
        $p->newPoint('gridAnchor', 0, $p->y(2), 'Grid anchor point');
    }

    /**
     * Finalizes the Loop
     *
     * @see \Freesewing\Patterns\TrayvonTie::finalizeInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeLoop($model, $p)
    {
        /* Title */
        $p->addTitle('titleAnchor', 7, $this->t($p->title), $this->t('Cut 1 from fabric'), 'horizontal');
    
        /* Paperless instructions */
        if ($this->isPaperless) {
            /* Height measure */
            $p->addPoint( 100, $p->shift(1, 0, 20));
            $p->addPoint( 101, $p->shift(4, 0, 20));
            $p->newPath('height', 'M 101 L 100', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('height', 'M 101 L 100', $this->unit($p->distance(1, 4)), ['class' => 'text-lg fill-note text-center', 'dy' => -3]);

            /* Width measure */
            $p->addPoint( 102, $p->shift(4, 90, 5));
            $p->addPoint( 103, $p->shift(2, 90, 5));
            $p->newPath('width', 'M 102 L 103', ['class' => 'double-arrow stroke-note stroke-lg']);
            $p->newTextOnPath('width', 'M 102 L 103', $this->unit($p->distance(2, 4)), ['class' => 'text-lg fill-note text-center', 'dy' => -3]);

        }
    
    }

    /**
     * Drafts a basic tie shape for a given tipWidth and knotWidth
     *
     * Because these different pattern parts are so similar, we can simply
     * reuse these steps.
     *
     * @param \Freesewing\Part $p The part to draft for
     * @param float $tipWidth The width of the tip
     * @param float $knotWidth The width of the knot
     *
     * @return void
     */
    public function draftTieShape($p, $tipWidth, $knotWidth)
    {
        $halfTip = $tipWidth/2;
        $halfKnot = $knotWidth/2;
        $p->newPoint(1, 0, 0, 'Tip');
        $p->newPoint(2, 0, $this->halfLength, 'Middle');
        $p->newPoint(3, $halfTip, $halfTip, 'Right tip corner');
        $p->addPoint(4, $p->flipX(3), 'Left tip corner');
        $p->addPoint('5a', $p->shift(2, 0, $halfKnot), 'Join right, 90 deg');
        $p->addPoint('5b', $p->rotate('5a', 2, 45), '45 degree helper');
        $p->addPoint(5, $p->beamsCross(2, '5b', '5a', 3), 'Join right, 45 deg');
        $p->addPoint('6a', $p->rotate('5a', 2, 180), 'Join left, 90deg');
        $p->addPoint(6, $p->beamsCross(2, '5b', '6a', 4), 'Join right, 45 deg');
        $p->newPoint(7, 0, $p->y(3));
        $p->addPoint('notch1', $p->shift(1, -45, 19));
        $p->addPoint('notch2', $p->flipX('notch1'));

        /** Outline */
        $p->newPath('outline', 'M 1 L 3 L 5 L 6 L 4 z', ['class' => 'seamline']);
        
        /** Mark for sampler */
        $p->paths['outline']->setSample(true);
        $p->paths['outline']->setSample(true);
        
        /** Anchors */
        $p->newPoint('titleAnchor', 0, $this->halfLength/4, 'Title anchor point');
        $p->newPoint('gridAnchor', 0, $p->y(7), 'Grid anchor point');
    }


    /**
     * Drafts a basic lining shape for a given tipWidth and knotWidth
     *
     * Because these different pattern parts are so similar, we can simply
     * reuse these steps.
     *
     * @param \Freesewing\Part $p The part to draft for
     * @param float $tipWidth The width of the tip
     * @param float $knotWidth The width of the knot
     *
     * @return void
     */
    public function draftLiningShape($p, $tipWidth, $knotWidth)
    {
        $this->draftTieShape($p, $tipWidth, $knotWidth);

        /** Cut lining short */
        $p->addPoint(8, $p->shiftTowards(3, 5, $p->distance(1, 3)*1.5), 'End of the lining, right side');
        $p->addPoint(9, $p->flipX(8), 'End of the lining, left side');
        $p->newPoint(89, 0, $p->y(8), 'End of the lining, center');

        /** Outline */
        $p->newPath('outline', 'M 1 L 3 L 8 L 9 L 4 z', ['class' => 'seamline']);
        
        /** Mark for sampler */
        $p->paths['outline']->setSample(true);
    }
    /**
     * Adds instructions for paperless
     *
     * Because these different pattern parts are so similar, we can simply
     * reuse these instructions.
     *
     * @param \Freesewing\Part $p The part to add instructions to
     * $param bool $interfacing Whether this is one of the interfacing pieces
     *
     * @return void
     */
    public function addInstructions($p, $interfacing = false)
    {
        if ($interfacing) {
            $size= 'sm';
        } else {
            $size='lg';
        }
        /* The big length measure along the middle */
        $p->newPath('center1', 'M 7 L 2', ['class' => 'double-arrow stroke-note stroke-lg']);
        $p->newTextOnPath('length1', 'M 2 L 1', $this->unit($p->distance(7, 2)), ['class' => 'text-lg fill-note text-center', 'dy' => -3]);
        $p->newPath('center2', 'M 7 L 1', ['class' => 'double-arrow stroke-note stroke-lg']);
        $p->newTextOnPath('length2', 'M 7 L 1', $this->unit($p->distance(7, 1)), ['class' => 'text-lg fill-note text-center', 'dy' => -3]);

        /* The measure for the knot width. Since the pattern is under 45 degrees I'm also adding a helpline for this */
        $p->newPath('knot1', 'M 5a L 5', ['class' => 'stroke-note dotted']);
        $p->newPath('knotWidth', 'M 6a 5a', ['class' => 'double-arrow stroke-note stroke-lg']);
        $p->newTextOnPath('knotWidth', 'M 6a L 5a', $this->unit($p->distance('6a', '5a')), ['class' => 'text-lg fill-note text-center', 'dy' => 7, 'dx' => 5]);
        
        /* * The 45 degree angle notation.  I'm adding some points to draw this curve */
        $p->addPoint(100, $p->shift('6a', -90, $p->distance('6a', 6)/4));
        $p->addPoint(101, $p->rotate('6a', 2, 45));
        $p->addPoint(102, $p->shift(101, 135, $p->distance('6a', 6)/4));
        $p->newPath('angle', 'M 101 C 102 100 6a', ['class' => "double-arrow stroke-note stroke-$size"]);
        $p->newTextOnPath('angle', 'M 101 C 102 100 6a', '45 &#176;', ['class' => "text-$size fill-note text-center", 'dy' => -4]);
        
        /* The measure for the tip width */
        $p->newPath('tip', 'M 3 L 4', ['class' => 'double-arrow stroke-note stroke-lg']);
        $p->newTextOnPath('tipWidth', 'M 4 L 3', $this->unit($p->distance(3, 4)), ['class' => 'fill-note text-center', 'dy' => -2, 'dx' => 12]);
    
        if (!$interfacing) {
            /* Seam allowance notes */
            $noteAttr = ['line-height' => 7, 'class' => 'text-lg'];
            $p->newNote(1, 101, $this->t("Standard  seam\nallowance")."(".$this->unit(10).')', 3, 20, 8, $noteAttr);
            $p->newNote(1, 1, $this->t("Standard  seam\nallowance")."(".$this->unit(10).')', 9, 25, 8, $noteAttr);
        }
    }
}
