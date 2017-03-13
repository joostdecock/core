<?php
/** Freesewing\Patterns\Docs\BabyBib class */
namespace Freesewing\Patterns\Docs;

/**
 *  Making a baby bib pattern
 */
class BabyBib extends \Freesewing\Patterns\Core\Pattern
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
     * @param \Freesewing\Model $model The model to sample for
     * @return void
     */
    public function initialize($model)
    {
        // Set headNeckRatio value for use later
        $this->setValue('headNeckRatio', 0.8);
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
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        // Setup all options and values we need
        $this->initialize($model);

        // Draft our bib 
        $this->draftBib($model);
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

        // Finalize our bib
        $this->finalizeBib($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to our bib
            $this->paperlessBib($model);
        }
    }

    /**
     * Drafts our bib
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBib($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['bib'];
        
        // Let's start with a precise neck opening
        $this->setValue('neckOpeningFactor', 1);
        $delta = 1;
        do {
            if($delta > 0) {
                $this->setValue('neckOpeningFactor', $this->v('neckOpeningFactor') * 0.99);
            } else {
                $this->setValue('neckOpeningFactor', $this->v('neckOpeningFactor') * 1.015);
            }
            $p->newPoint(1, 0, $this->v('neckOpeningFactor') * $model->m('headCircumference')/8, 'Bottom of the neck opening');
            $p->newPoint(2, $this->v('neckOpeningFactor') * $model->m('headCircumference')/6, 0, 'Right side of neck opening');
            $p->addPoint(3, $p->shift(1,0,$p->x(2)/2), 'Right control point for neckBottom');
            $p->addPoint(4, $p->shift(2,-90,$p->y(1)/2), 'Bottom control point for neckRight');
            
            $delta = $this->neckOpeningDelta($model, $p);
            $this->msg("Neck opening is $delta mm off"); 
        } while (abs($delta) > 1);
        
        // Mirror quarter opening around X axis
        $flip = [2,3,4];
        foreach($flip as $id) {
            $p->addPoint($p->newId('left'), $p->flipX($id, 0));
        }

        // Mirror half opening around Y axis
        $flip = [1,3,4,'left2','left3'];

        foreach($flip as $id) {
            $p->addPoint($p->newId('top'), $p->flipY($id, 0));
        }

        
        
        // 25mm strap around the neck opening, which will also define the width of our bib
        $strap = 25;

        // Basic box
        $p->newPoint('topLeft', $p->x('left1')-$strap, $p->y('top1')-$strap);
        $p->addPoint('topRight', $p->flipX('topLeft', 0));
        $p->newPoint('bottomLeft', $p->x('topLeft'), $p->y(1)+$model->m('chestCircumference')/3 + $this->o('lengthBonus'));
        $p->addPoint('bottomRight', $p->flipX('bottomLeft', 0));

        
        // Make radius 50mm
        $radius = 50;

        // Bottom right corner
        $p->addPoint('bottomRightCornerStart', $p->shift('bottomRight',180,$radius));
        $p->addPoint('bottomRightCornerStartCp', $p->shift('bottomRightCornerStart',0, \Freesewing\BezierToolbox::bezierCircle($radius)));
        $p->addPoint('bottomRightCornerEnd', $p->rotate('bottomRightCornerStart','bottomRight',-90));
        $p->addPoint('bottomRightCornerEndCp', $p->rotate('bottomRightCornerStartCp','bottomRight',-90));
        
        // Bottom left corner
        $p->addPoint('bottomLeftCornerStart', $p->flipX('bottomRightCornerStart',0));
        $p->addPoint('bottomLeftCornerStartCp', $p->flipX('bottomRightCornerStartCp',0));
        $p->addPoint('bottomLeftCornerEnd', $p->flipX('bottomRightCornerEnd',0));
        $p->addPoint('bottomLeftCornerEndCp', $p->flipX('bottomRightCornerEndCp',0));
        
        // Top right corner
        $p->newPoint('topRightCornerStart', $p->x('topRight'), 0);
        $p->newPoint('topRightCornerStartCp', $p->x('topRight'), $p->y('topRight')/2);
        $p->newPoint('topRightCornerEnd', 0, $p->y('topRight'));
        $p->newPoint('topRightCornerEndCp', $p->x('topRight')/2, $p->y('topRight'));

        // Snap anchor point
        $p->newPoint('snapAnchor', 0, $p->y('top1')-12.5);

        // Finish strap
        $p->addPoint('snapCpTop', $p->rotate('snapAnchor','topRightCornerEnd',-90));
        $p->addPoint('snapCpBottom', $p->rotate('snapAnchor','top1',90));

        // Rotate strap out of the way to avoid overlap
        // Points to rotate
        $rotateThese = [
            'top2',
            'top1',
            'snapCpBottom',
            'snapAnchor',
            'snapCpTop',
            'topRightCornerEnd',
            'topRightCornerEndCp'
        ];

        // Rotate until our curve is 2.5mm out of center
        do {
            foreach($rotateThese as $id) {
                $p->addPoint($id,$p->rotate($id,2,-1));
            }
        $p->addPoint('edge',$p->curveEdge('topRightCornerEnd', 'snapCpTop', 'snapCpBottom', 'top1', 'left')); 

        } while ($p->x('edge') < 2.5);

        // Mirror points we need for the left strap
        $p->addPoint('topLeftCornerStart', $p->flipX('topRightCornerStart',0));
        $p->addPoint('topLeftCornerStartCp', $p->flipX('topRightCornerStartCp',0));
        $p->addPoint('topLeftCornerEndCp', $p->flipX('topRightCornerEndCp',0));
        $p->addPoint('topLeftCornerEnd', $p->flipX('topRightCornerEnd',0));
        $p->addPoint('snap2Anchor', $p->flipX('snapAnchor',0));
        $p->addPoint('snap2CpTop', $p->flipX('snapCpTop',0));
        $p->addPoint('snap2CpBottom', $p->flipX('snapCpBottom',0));
        $p->addPoint('top4', $p->flipX('top2',0));
        $p->addPoint('top12', $p->flipX('top1',0));

        $p->newPath(
            'outline', 
            '
                M bottomLeftCornerEnd C bottomLeftCornerEndCp bottomLeftCornerStartCp bottomLeftCornerStart 
                L bottomRightCornerStart 
                C bottomRightCornerStartCp bottomRightCornerEndCp bottomRightCornerEnd
                L topRightCornerStart
                C topRightCornerStartCp topRightCornerEndCp topRightCornerEnd
                C snapCpTop snapCpBottom top1
                C top2 top3 2
                C 4 3 1
                C left2 left3 left1
                C top5 top4 top12
                C snap2CpBottom snap2CpTop topLeftCornerEnd 
                C topLeftCornerEndCp topLeftCornerStartCp topLeftCornerStart 
                z
            ');

        // Grid anchor
        $p->newPoint('samplerAnchor', $p->x(1),$p->y(1));

        // Include outline path in the sample service
        $p->paths['outline']->setSample(true);

        // Draw the neck opening
        //$p->newPath('neckOpening', 'M 1 C 3 4 2 C top3 top2 top1 C top4 top5 left1 C left3 left2 1 z');
        
        // Draw the bounding box as a helpline
        $p->newPath('box', 'M bottomLeft L topLeft L topRight L bottomRight z', ['class' => 'helpline']);
        $p->paths['box']->setRender(false);
    }

    protected function neckOpeningDelta($model,$part)
    {
        $length = $part->curveLen(1,3,4,2) * 4;
        $target = $model->m('headCircumference') * $this->v('headNeckRatio');
        return $length - $target;
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
     * Finalizes the example part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBib($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['bib'];

        // Bias binding line
        $p->offsetPath('bias', 'outline', -3, true, ['class' => 'helpline']);

        // Snap button
        $p->newSnippet(1, 'snap-male', 'snapAnchor');
        $p->newSnippet(2, 'snap-female', 'snap2Anchor');

        // Title
        $p->addPoint('titleAnchor', $p->shift(1,-90,50)); 
        $p->addTitle('titleAnchor', 1, $this->t($p->getTitle()), '1x');
    
        // Logo
        $p->addPoint('logoAnchor', $p->shift(1,-90,120)); 
        $p->newSnippet('logo', 'logo', 'logoAnchor');
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
     * Adds paperless info for the example part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBib($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['bib'];

        // Width at the bottom
        $p->newWidthDimension('bottomLeftCornerEnd','bottomRightCornerEnd', $p->y('bottomRightCornerStart')+15);
        
        // Heights on the right side
        $xBase = $p->x('bottomRightCornerEnd');
        $p->newHeightDimension('bottomLeftCornerStart', 'bottomRightCornerEnd', $xBase + 15);
        $p->newHeightDimension('bottomLeftCornerStart', 1, $xBase + 30);
        $p->newHeightDimension('bottomLeftCornerStart', 2, $xBase + 45);
        $p->newHeightDimension('bottomLeftCornerStart', 'top1', $xBase + 60);
        $p->newHeightDimension('bottomLeftCornerStart', 'topRightCornerEnd', $xBase + 75);
        
        // Neck opening
        $p->newLinearDimension('left1',2);
        $p->newCurvedDimension('M top12 C top4 top5 left1 C left3 left2 1 C 3 4 2 C top3 top2 top1', -5);
    }
}
