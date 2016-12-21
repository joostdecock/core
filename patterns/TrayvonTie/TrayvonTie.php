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
        // Some helper vars
        $this->setValue('halfLength',($model->getMeasurement('centerBackNeckToWaist') * 2 + $model->getMeasurement('neckCircumference') +150) / 2);
        $this->setValue('halfTip', $this->getOption('tipWidth') / 2);
        $this->setValue('halfKnot', $this->getOption('knotWidth') / 2);
        $this->setValue('halfBackTip', $this->v('halfKnot') + ($this->v('halfTip') - $this->v('halfKnot')) / 2);
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
        
        // Reusing code for similar shapes
        $this->draftTieShape($this->parts['interfacingTip'], $this->v('halfTip')*2, $this->v('halfKnot')*2);
        $this->draftTieShape($this->parts['interfacingTail'], $this->v('halfBackTip')*2, $this->v('halfKnot')*2);
        $this->draftTieShape($this->parts['fabricTip'], $this->v('halfTip')*4+40, $this->v('halfKnot')*4+40);
        $this->draftTieShape($this->parts['fabricTail'], $this->v('halfBackTip')*4+40, $this->v('halfKnot')*4+40);

        // Reusing code for similar shapes
        $this->draftLiningShape($this->parts['liningTip'], $this->v('halfTip')*4+40, $this->v('halfKnot')*4+40);
        $this->draftLiningShape($this->parts['liningTail'], $this->v('halfBackTip')*4+40, $this->v('halfKnot')*4+40);
        
        // Drafting the loop
        $this->draftLoop($model, $this->parts['loop']);
    }

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

        // Finalize all parts 
        foreach ($this->parts as $key => $part) {
            $method = 'finalize'.ucfirst($key);
            $this->$method($model, $part);
        }
        
        // Is this a paperless pattern?
        if ($this->isPaperless) {
            foreach ($this->parts as $key => $part) {
                $method = 'paperless'.ucfirst($key);
                $this->$method($model, $part);
            }
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
        $p->newPoint(2, 0, $this->v('halfLength'), 'Middle');
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

        // Outline 
        $p->newPath('outline', 'M 1 L 3 L 5 L 6 L 4 z', ['class' => 'seamline']);
        
        // Mark for sampler 
        $p->paths['outline']->setSample(true);
        
        // Anchors 
        $p->newPoint('titleAnchor', 0, $this->v('halfLength')/4, 'Title anchor point');
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

        // Cut lining short 
        $p->addPoint(8, $p->shiftTowards(3, 5, $p->distance(1, 3)*1.5), 'End of the lining, right side');
        $p->addPoint(9, $p->flipX(8), 'End of the lining, left side');
        $p->newPoint(89, 0, $p->y(8), 'End of the lining, center');

        // Outline 
        $p->newPath('outline', 'M 1 L 3 L 8 L 9 L 4 z', ['class' => 'seamline']);
        
        // Mark for sampler 
        $p->paths['outline']->setSample(true);
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
        $p->newPoint(2, $this->v('halfBackTip')*4 + 40, 40, 'Bottom right');
        $p->newPoint(3, $p->x(2), 0, 'Top right');
        $p->newPoint(4, 0, $p->y(2), 'Bottom left');

        // Paths 
        $path = 'M 1 L 3 L 2 L 4 z';
        $p->newPath('outline', $path, ['class' => 'seamline']);
        
        // Anchors 
        $p->newPoint('titleAnchor', $p->x(2)/3, $p->y(2)/2, 'Title anchor point');
        $p->newPoint('gridAnchor', 0, $p->y(2), 'Grid anchor point');
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
     * Finalizes the Interfacing Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeInterfacingTip($model, $p)
    {
        // Title
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from tie interfacing'), 'vertical');
    }


    /**
     * Finalizes the Interfacing Tail
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeInterfacingTail($model, $p)
    {
        // Title 
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('from tie interfacing'), 'vertical');
    }

    /**
     * Finalizes the Fabric Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeFabricTip($model, $p)
    {
        // Title 
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '1x '.$this->t('from fabric'));
        
        // logo 
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-90, 50));
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        $p->newSnippet('cc', 'cc', 'logoAnchor');
        
        // Scalebox 
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 90));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        
        // Tip seam allowance 
        $p->offsetPathString('tipSA', 'M 4 L 1 L 3', -10, 0);
        $p->addPoint(10, $p->beamsCross('tipSA-line-1TO4', 'tipSA-line-4TO1', 6, 4), 'Left edge of tip SA');
        $p->addPoint(11, $p->flipX(10), 'Right edge of tip SA');
        $p->newPath('tipSA', 'M 4 L 10 L tipSA-line-1TO4XllXtipSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);
        
        // Mid-tie seam allowance
        $p->offsetPathString('midSA', 'M 5 L 6', -10, 0);
        $p->newPath('midSA', 'M 5 L midSA-line-5TO6 L midSA-line-6TO5 L 6', ['class' => 'seam-allowance']);
        
        // Notches
        $p->newSnippet('notch1', 'notch', 'notch1');
        $p->newSnippet('notch2', 'notch', 'notch2');
    }

    /**
     * Finalizes the Fabric Tail
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeFabricTail($model, $p)
    {
        // Title 
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '1x '.$this->t('from fabric'));

        // Tail seam allowance 
        $p->offsetPathString('tailSA', 'M 4 L 1 L 3', -10, 0);
        $p->addPoint(10, $p->beamsCross('tailSA-line-1TO4', 'tailSA-line-4TO1', 6, 4), 'Left edge of tail SA');
        $p->addPoint(11, $p->flipX(10), 'Right edge of tail SA');
        $p->newPath('tailSA', 'M 4 L 10 L tailSA-line-1TO4XllXtailSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);
        
        // Mid-tie seam allowance 
        $p->offsetPathString('midSA', 'M 5 L 6', -10, 0);
        $p->newPath('midSA', 'M 5 L midSA-line-5TO6 L midSA-line-6TO5 L 6', ['class' => 'seam-allowance']);
        
        // Notches
        $p->newSnippet('notch1', 'notch', 'notch1');
        $p->newSnippet('notch2', 'notch', 'notch2');
    }

    /**
     * Finalizes the Lining Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeLiningTip($model, $p)
    {
        // Re-using the fabric tip points
        $this->clonePoints('fabricTip', 'liningTip');
        
        // Title 
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '1x '.$this->t('from lining'));
        
        // Tip seam allowance 
        $p->newPath('tipSA', 'M 4 L 10 L tipSA-line-1TO4XllXtipSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);

        // Notch
        $p->newSnippet('notch', 'notch', 1);
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
        // Re-using the fabric tail points 
        $this->clonePoints('fabricTail', 'liningTail');

        // Title 
        $p->addTitle('titleAnchor', 6, $this->t($p->title), '1x '.$this->t('from lining'));
        
        // Tip seam allowance 
        $p->newPath('tailSA', 'M 4 L 10 L tailSA-line-1TO4XllXtailSA-line-1TO3 L 11 L 3', ['class' => 'seam-allowance']);
        
        // Notch 
        $p->newSnippet('notch', 'notch', 1);
    }

    /**
     * Finalizes the Loop
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeLoop($model, $p)
    {
        // Title 
        $p->addTitle('titleAnchor', 7, $this->t($p->title), '1x '.$this->t('from fabric'), 'horizontal');
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
     * Paperless instructions for the Interfacing Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessInterfacingTip($model, $p)
    {
        $this->paperlessTieShape($p,true);
    }

    /**
     * Paperless instructions for the Interfacing Tail
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessInterfacingTail($model, $p)
    {
        $this->paperlessTieShape($p,true);
    }

    /**
     * Paperless instructions for the Fabric Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessFabricTip($model, $p)
    {
        $this->paperlessTieShape($p,false);
    }

    /**
     * Paperless instructions for the Fabric Tail
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessFabricTail($model, $p)
    {
        $this->paperlessTieShape($p,false);
    }

    /**
     * Paperless instructions for the Lining Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessLiningTip($model, $p)
    {
        $this->paperlessLiningShape($p);
    }

    /**
     * Paperless instructions for the Lining Tail
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessLiningTail($model, $p)
    {
        $this->paperlessLiningShape($p);
    }

    /**
     * Paperless instructions for the Loop
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessLoop($model, $p)
    {
        // Width
        $p->newWidthDimension(4,2,$p->y(2)+15);

        // Height
        $p->newHeightDimension(4,1,$p->x(1)-15);
    }

    /**
     * Adds instructions for paperless to a lining shaped part
     *
     * Because these different pattern parts are so similar, we can simply
     * reuse these instructions.
     *
     * @param \Freesewing\Part $p The part to add instructions to
     * $param bool $interfacing Whether this is one of the interfacing pieces
     *
     * @return void
     */
    public function paperlessLiningShape($p)
    {
        // Height on the left
        $xBase = $p->x(4);
        $p->newHeightDimension(4,1,$xBase-10);
        $p->newHeightDimension(9,1,$xBase-25);

        // Tip dimension
        $p->newWidthDimension(4,3,$p->y(1)-25);

        // Knot dimensions
        $p->newWidthDimension(9,8, $p->y(8)+15);
    
        // Seam allowance note
        $p->newNote(1, 'notch1', $this->t("Standard\nseam\nallowance")."(".$this->unit(10).')', 6, 20, -7);
    }
    
    /**
     * Adds instructions for paperless to a tie shaped part
     *
     * Because these different pattern parts are so similar, we can simply
     * reuse these instructions.
     *
     * @param \Freesewing\Part $p The part to add instructions to
     * $param bool $interfacing Whether this is one of the interfacing pieces
     *
     * @return void
     */
    public function paperlessTieShape($p, $interfacing = false)
    {
        if ($interfacing) {
            $size= 'sm';
            $offset = 10;
        } else {
            $size='lg';
            $offset = 25;
        }
        // Height on the left
        $xBase = $p->x(4);
        $p->newHeightDimension(4,1,$xBase-10);
        $p->newHeightDimension(2,1,$xBase-25);

        // Tip dimensions
        if (!$interfacing) $p->newLinearDimension(1,'notch1',12,false,['class' => 'dimension dimension-sm'],['class' => 'note text-center', 'dy' => -2]);
        $p->newWidthDimension(4,3,$p->y(1)-$offset);

        // Knot dimensions
        $p->newWidthDimension('6a','5a', $p->y(6)+$offset);
        $p->newPath('knotHelpline', 'M 5 L 5a L 6a', ['class' => 'stroke-note dotted']);
        $p->newNote(1, '5b', '45 &#176;', 11, 15,0);
    
        if (!$interfacing) {
            // Seam allowance notes 
            $p->newNote(2, 2, $this->t("Standard\nseam\nallowance")."(".$this->unit(10).')', 11, 20, -4, ['class' => 'note', 'dy' => -10, 'line-height' => 6]);
            $p->newNote(3, 'notch2', $this->t("Standard\nseam\nallowance")."(".$this->unit(10).')', 6, 25, -6, $noteAttr);
        }
    }
}
