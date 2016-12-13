<?php
/** Freesewing\Patterns\BruceBoxerBriefs class */
namespace Freesewing\Patterns;

/**
 * The Bruce Boxer Briefs pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BruceBoxerBriefs extends Pattern
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
        
        $this->finalizeBack($model);
        $this->finalizeSide($model);
        $this->finalizeFront($model);
        $this->finalizeInset($model);
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

        $this->draftBack($model);
        $this->draftSide($model);
        $this->draftFront($model);
        $this->draftInset($model);
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
        /* Ration of waist between parts */
        $this->waistRatioFront = 0.28;
        $this->waistRatioBack  = 0.34;
        $this->waistRatioSide  = (1 - ($this->waistRatioFront + $this->waistRatioBack)) / 2;
        
        /* Ration of leg between parts */
        $this->legRatioInset = 0.28;
        $this->legRatioBack  = 0.29;
        $this->legRatioSide  = (1 - ($this->legRatioInset + $this->legRatioBack)) / 2;
        
        /* Helpers */
        $this->halfCross = $model->getMeasurement('crossseamLength')/2;
        $this->sideWaist = $model->getMeasurement('hipsCircumference') * $this->waistRatioSide * $this->getOption('horizontalStretchFactor');
        $this->sideLeg   = $model->getMeasurement('upperLegCircumference') * $this->legRatioSide * $this->getOption('horizontalStretchFactor');
        
        /* style */
        $this->mAttr = ['class' => 'measure']; // measure attributes
        $this->hAttr = ['class' => 'stroke-note stroke-sm']; // help line attributes
        $this->tAttr = ['class' => 'text fill-note text-center', 'dy' => -1]; // text attributes
        $this->noteAttr = ['line-height' => 7]; // note attributes

        /* Keep stretch ratio to something sensible */
        if ($this->o('verticalStretchFactor')<0.5) {
            $this->setOption('verticalStretchFactor', 0.5);
        }
        if ($this->o('horizontalStretchFactor')<0.5) {
            $this->setOption('horizontalStretchFactor', 0.5);
        }
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
        $p = $this->parts['inset'];

        $p->newPoint(   1, 0, 0, 'Top left' );
        $p->newPoint(   2, 0, $this->insetFrontLength, 'Bottom right' );
        $p->clonePoint( 2, 'gridAnchor' );
        $p->newPoint(   3, $model->m('upperLegCircumference') * $this->legRatioInset * $this->o('horizontalStretchFactor') * -1, $p->y(2), 'Bottom right' );
        $p->addPoint( 201, $p->shift(2, -90, 15) );
        $p->addPoint( 301, $p->shift(3, -90, 15) );
        $p->newPoint(   4, $p->x(3), $p->y(3) - $this->crotchSeamLength * 0.4, 'Bottom right' );
        $p->addPoint( 401, $p->rotate(4, 3, 16), 'Curve bottom point' );
        $p->addPoint( 402, $p->rotate(3, 401, 90), 'Curve bottom point' );
        $p->addPoint( 403, $p->shiftTowards(401, 402, $p->distance(401, 402)*2), 'Control point' );
        $p->newPoint( 101, $p->x(3)*0.7, 0, 'Control point' );
        $p->addPoint(   5, $p->shiftAlong(1, 101, 403, 401, 70), 'Notch' );

        $points = $p->addSplitCurve(1, 101, 403, 401, 0.5, 'split', true);
        $this->insetPath = 'M 2 L 1 C split2 split3 split4 C split7 split6 401 L 3 z';
        
        $p->newPath('outline', $this->insetPath, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
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
        $p = $this->parts['inset'];

        /* Title */
        $p->newPoint('titleAnchor', $p->x(3)/2.5, $p->y(401));
        $p->addTitle('titleAnchor', 4, $this->t($p->title), $this->t('Cut 2')."\n".$this->t('Good sides together'));

        /* Standard seam allowance */
        $p->offsetPathString('sa', $this->insetPath, 10, 1, ['class' => 'seam-allowance']);
    
        /* Extra hem allowance */
        $moveThese = [
            'sa-line-2TO3XllXsa-line-2TO1',
            'sa-line-2TO3',
            'sa-line-3TO2',
            'sa-line-3TO401XllXsa-line-3TO2',
        ];
        $angle = -90;
        foreach ($moveThese as $i) {
            $p->addPoint($i, $p->shift($i, $angle, 10));
        }

        /**
         * Extra instructions for paperless patterns
         */
        if ($this->isPaperless) {
            /* Measurement points */
            $p->newPoint( 100, $p->x(3)+20, $p->y(3) );
            $p->newPoint( 101, $p->x(100), $p->y(401) );
            $p->addPoint( 102, $p->shift(3, -90, 7) );
            $p->addPoint( 103, $p->shift(2, -90, 7) );
            $p->addPoint( 105, $p->shift(103, -90, 7) );
            $p->newPoint( 104, $p->x(401), $p->y(105) );
            $p->addPoint( 106, $p->shift(1, 0, 7) );
            $p->addPoint( 107, $p->shift(2, 0, 7) );
            
            /* Measurement help lines */
            $p->newPath( $p->newId(), "M 401 L 101", $this->hAttr );
            $p->newPath( $p->newId(), "M 3 L 102 M 2 L 103", $this->hAttr );
            $p->newPath( $p->newId(), "M 1 L 106 M 2 L 107", $this->hAttr );
            $p->newPath( $p->newId(), "M 2 L 105 M 401 L 104", $this->hAttr );
            
            /* Measurement lines */
            $p->newPath( $p->newId(), "M 100 L 101", $this->mAttr );
            $p->newPath( $p->newId(), "M 102 L 103", $this->mAttr );
            $p->newPath( $p->newId(), "M 106 L 107", $this->mAttr );
            $p->newPath( $p->newId(), "M 104 L 105", $this->mAttr );
            
            /* Measurement text */
            $p->newTextOnPath( $p->newId(), "M 100 L 101", $this->unit( $p->distance(100, 101) ), $this->tAttr );
            $p->newTextOnPath( $p->newId(), "M 102 L 103", $this->unit( $p->distance(102, 103) ), $this->tAttr );
            $p->newTextOnPath( $p->newId(), "M 107 L 106", $this->unit( $p->distance(106, 107) ), $this->tAttr );
            $p->newTextOnPath( $p->newId(), "M 104 L 105", $this->unit( $p->distance(104, 105) ), $this->tAttr );
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
        $p = $this->parts['front'];

        $p->newPoint(   1, 0, 0, 'Waistline @ center front');
        $p->newPoint( 101, 0, -1 * $this->getOption('rise') * $this->getOption('verticalStretchFactor'), 'Waistline @ center front');
        $p->clonePoint(101, 'gridAnchor');
        $p->newPoint(   2, $model->getMeasurement('hipsCircumference') * $this->waistRatioFront * $this->getOption('horizontalStretchFactor')/2, $p->y(101), 'Front top right');
        $p->newPoint(   3, $this->halfCross * $this->getOption('verticalStretchFactor'), $p->y(101), 'Pre-rotate mid tusk point');
        $p->addPoint( 301, $p->rotate(3, 101, -71), 'Mid tusk point');
        $p->newPoint(   4, $p->x(2), ($this->halfCross - $this->o('rise')) * $this->getOption('verticalStretchFactor') * 0.2, 'Pre-rotate tusk start');
        $p->addPoint( 401, $p->rotate(4, 2, 4));
        $p->newPoint( 403, $p->x(2), $p->y(4) - $p->deltaX(1, 2)*0.6, 'Pre-rotate control point');
        $p->addPoint( 404, $p->rotate(403, 4, 100), 'Control point');
        $p->newPoint(   5, $p->x(301) + $this->crotchSeamLength * 0.1, $p->y(301), 'Pre-rotate tusk corner');
        $p->addPoint( 501, $p->rotate(5, 301, 36), 'Tusk corner');
        $p->addPoint( 502, $p->rotate(501, 301, 180), 'Tusk corner');
        $p->addPoint( 503, $p->rotate(301, 502, 110), 'Tusk control point bottom');
        $p->newPoint(   6, 0, ($this->halfCross - $this->o('rise')) * $this->getOption('verticalStretchFactor') * 0.58, 'Tusk join point');
        $p->newPoint( 601, $p->x(6), $p->y(6) + $p->deltaY(6, 502) * 0.25, 'Tusk control point');
        $p->newPoint(   7, $p->x(501), $p->y(2) + $p->deltaY(2, 501) * 0.6, 'Pre-rotate control point');
        $p->addPoint( 701, $p->rotate(7, 501, 35), 'Control point');
        $p->addPoint(   8, $p->shiftAlong(4, 404, 701, 501, 70), 'Notch');

        $this->insetFrontLength = $this->frontLength - $p->distance(2, 4);
        $this->frontArcLength = $p->curveLen(4, 404, 701, 501);

        $flip = [ 2, 4, 401, 404, 501, 502, 503, 701, 8 ];
        foreach ($flip as $i) {
            $p->addPoint(-$i, $p->flipX($i), $p->points[$i]->getDescription());
        }
  
        $this->frontPath = 'M -2 L 2 L 4 C 404 701 501 L 502 C 503 601 6 C 601 -503 -502 L -501 C -701 -404 -4 z';
        $p->newPath('outline', $this->frontPath, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
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
        $p = $this->parts['front'];
        
        /* Title */
        $p->newPoint('titleAnchor', 0, $p->y('gridAnchor')+70);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), $this->t('Cut 2')."\n".$this->t('Good sides together'));

        /* Standard seam allowance */
        $p->offsetPathString('sa', $this->frontPath, -10, 1, ['class' => 'seam-allowance']);

        /**
         * Extra instructions for paperless patterns
         */
        if ($this->isPaperless) {
            /**
             * These paperless instructions require some extra points
             *
             * To keep things from getting mixed up, I'm staring from
             * point index 100 upwards.
             **/
            $p->newPoint(  100, $p->x(2), $p->y(2)+20);
            $p->newPoint( -100, $p->x(-2), $p->y(2)+20);
            $p->newPoint(  101, $p->x(6)-20, $p->y(6));
            $p->newPoint(  102, $p->x(101), $p->y(502));
            $p->newPoint(  103, $p->x(101)-20, $p->y(2));
            $p->newPoint(  104, $p->x(103), $p->y(502));
            $p->newPoint(  105, $p->x(-4)+20, $p->y(-4));
            $p->newPoint(  106, $p->x(105), $p->y(2));
            $p->newPoint(  111, $p->x(502)-40, $p->y(501));
            $p->newPoint(  112, $p->x(111), $p->y(2));
            /* Find most narrow point */
            $p->newPath('.tmp', 'M -4 C -404 -701 -501');
            $p->paths['.tmp']->setRender(false);
            $boundary = $p->paths['.tmp']->findBoundary($p);
            $p->curveCrossesX(-4, -404, -701, -501, $boundary->bottomRight->getX()-0.2, 'narrow-'); // -0.2 to make sure we find an intersection
            $p->addPoint(  107, $p->flipX('narrow-1'));
            $p->addPoint(  108, $p->flipX(107));
            $p->newPoint(  109, $p->x(102), $p->y(108));
            $p->newPoint(  110, $p->x(102), $p->y(2));
            $p->newPoint(  120, $p->x(2)/2, $p->y(2));

            /**
             *  Measurement help lines
             *
             *  The path class takes care of spurious spaces and newline
             *  characters. So I'm writing it like this as I find it easier
             *  to read.
             *  Especially since I'm cobbling together 4 paths becuase
             *  there's really no need for the overhead of having them
             *  as 4 different path objects.
             */
            $p->newPath($p->newId(), "
                M 6 L 101     
                M -4 L 105
            ", $this->hAttr);

            /** Measurement arrows
             *
             * We need an individual path for each arrow
             * because of the start and end markers
             */
            $p->newPath($p->newId(), "M -100 L 100", $this->mAttr);
            $p->newPath($p->newId(), "M -502 L 502", $this->mAttr);
            $p->newPath($p->newId(), "M -501 L 501", $this->mAttr);
            $p->newPath($p->newId(), "M 101 L 102", $this->mAttr);
            $p->newPath($p->newId(), "M 103 L 104", $this->mAttr);
            $p->newPath($p->newId(), "M 105 L 106", $this->mAttr);
            $p->newPath($p->newId(), "M 111 L 112", $this->mAttr);
            $p->newPath($p->newId(), "M 107 L 108", $this->mAttr);
            $p->newPath($p->newId(), "M 109 L 110", $this->mAttr);

            /* Measurements text */
            $p->newTextOnPath($p->newId(), "M -100 L 100", $this->unit($p->distance(-2, 2)), $tthis->Attr);
            $p->newTextOnPath($p->newId(), "M -502 L 502", $this->unit($p->distance(-502, 502)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M -501 L 501", $this->unit($p->distance(-501, 501)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 102 L 101", $this->unit($p->distance(101, 102)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 104 L 103", $this->unit($p->distance(103, 104)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 105 L 106", $this->unit($p->distance(105, 106)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 111 L 112", $this->unit($p->distance(111, 112)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 108 L 107", $this->unit($p->distance(107, 108)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 109 L 110", $this->unit($p->distance(109, 110)), $this->tAttr);

            /* Notes */
            $p->newNote($p->newId(), 120, $this->t("Standard seam allowance")."\n(".$this->unit(10).')', 6, 30, -5, $this->noteAttr);
        }
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
        $p = $this->parts['side'];

        $p->newPoint( 1, 0, 0, 'Zero');
        $p->newPoint( 2, $this->sideWaist / 2, -1* $this->o('rise') * $this->getOption('verticalStretchFactor'), 'Top right');
        $p->newPoint( 3, $this->sideLeg, $this->halfCross * $this->getOption('verticalStretchFactor') + ($model->getMeasurement('upperLegCircumference')/50 - $this->o('rise')), 'Bottom right');
        $p->addPoint( 4, $p->shift(3, -90, 15));
        $p->newPoint( 'gridAnchor', 0, $p->y(2));

        $flip = [ 2, 3, 4 ];
        foreach ($flip as $i) {
            $p->addPoint(-$i, $p->flipX($i), $p->points[$i]->getDescription());
        }
      
        /* Storing seam length */
        $this->frontLength = $p->distance(2, 3);
        
        /* Path */
        $this->sidePath = 'M 2 L 3 L -3 L -2 z';
        $p->newPath('outline', $this->sidePath, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
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
        $p = $this->parts['side'];

        /* Title */
        $p->newPoint('titleAnchor', 0, $p->y('gridAnchor')+70);
        $p->addTitle('titleAnchor', 3, $this->t($p->title), $this->t('Cut 2')."\n".$this->t('Good sides together'));

        /* Logo */
        $p->newPoint('logoAnchor', 0, $p->y('gridAnchor')+120);
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        
        /* Standard seam allowance */
        $p->offsetPathString('sa', $this->sidePath, -10, 1, ['class' => 'seam-allowance']);

        /* Extra hem allowance */
        $p->addPoint('sa-line-3TO-3', $p->shift('sa-line-3TO-3', -90, 10));
        $p->addPoint('sa-line--3TO3', $p->shift('sa-line--3TO3', -90, 10));
        $p->newPoint('sa-line-3TO2XllXsa-line-3TO-3', $p->x('sa-line-3TO2'), $p->y('sa-line-3TO-3'));
        $p->newPoint('sa-line--3TO3XllXsa-line--3TO-2', $p->x('sa-line--3TO-2'), $p->y('sa-line-3TO-3'));
        
        /**
         * Extra instructions for paperless patterns
         */
        if ($this->isPaperless) {
            /* Points */
            $p->newPoint(  100, $p->x(2), $p->y(2)+20);
            $p->newPoint( -100, $p->x(-2), $p->y(2)+20);
            $p->newPoint(  101, $p->x(3), $p->y(3)+10);
            $p->newPoint( -101, $p->x(-3), $p->y(3)+10);
            $p->newPoint(  102, $p->x(-2)+20, $p->y(2));
            $p->newPoint(  103, $p->x(102), $p->y(3));
            $p->addPoint(  110, $p->shiftTowards(2, 3, 100));
            $p->addPoint(  111, $p->shift(3, 180, 40));


            /* Measurement help lines */
            $p->newPath( $p->newId(), "
                M 2 L 100 
                M -2 L -100
                M 3 L 101
                M -3 L -101               
            ", $this->hAttr);

            /* Measurement arrows */
            $p->newPath( $p->newId(), "M -100 L 100", $this->mAttr);
            $p->newPath( $p->newId(), "M -101 L 101", $this->mAttr);
            $p->newPath( $p->newId(), "M 102 L 103", $this->mAttr);
            
            /* Measurements text */
            $p->newTextOnPath( $p->newId(), "M -100 L 100", $this->unit($p->distance(-2, 2)), $this->tAttr);
            $p->newTextOnPath( $p->newId(), "M -101 L 101", $this->unit($p->distance(-3, 3)), $this->tAttr);
            $p->newTextOnPath( $p->newId(), "M 103 L 102", $this->unit($p->distance(102, 103)), $this->tAttr);
            
            /* Notes */
            $p->newNote( $p->newId(), 110, $this->t("Standard seam allowance")."\n(".$this->unit(10).')', 8, 10, -5, $this->noteAttr);
            $p->newNote( $p->newId(), 111, $this->t("Hem allowance")." (".$this->unit(20).')', 12, 25, -13, $this->noteAttr);
        }
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
        $p = $this->parts['back'];

        $p->newPoint(   1, 0, 0, 'Waistline @ center back');
        $p->newPoint(   2, $model->getMeasurement('hipsCircumference') * $this->waistRatioBack * $this->getOption('horizontalStretchFactor') / 2, 0 - $this->getOption('rise') * $this->getOption('verticalStretchFactor'), 'Waistline @ center back');
        $p->addPoint( 201, $p->shift(2, 200, 25));
        $p->newPoint(   3, 0, $p->y(2) + $this->halfCross * $this->getOption('verticalStretchFactor'), 'Crossseam point');
        $p->newPoint(   5, $p->x(3) + $this->halfCross * $this->getOption('verticalStretchFactor') * 0.145, $p->y(3) + $this->halfCross * $this->getOption('horizontalStretchFactor') * 0.265, 'Inside corner leg');
        $p->newPoint(   4, $p->x(5) + $model->getMeasurement('upperLegCircumference') * $this->legRatioBack * $this->getOption('horizontalStretchFactor'), $p->y(5), 'Pre-rotate outside corner leg');
        $p->addPoint( 401, $p->rotate(4, 5, 14), 'Outside corner leg');
        $p->addPoint( 501, $p->shiftTowards(5, 401, $p->deltaY(3, 5)/2), 'Control point');
        $p->addPoint( 402, $p->shiftTowards(401, 2, 15));
        $p->addPoint( 403, $p->rotate(402, 401, 180));
        $p->newPoint( 404, $p->x(201), $p->y(401));
        $p->addPoint( 502, $p->rotate(501, 5, 90));
        $p->addPoint( 503, $p->shiftTowards(5, 401, 15), 'Pre-rotate SA offset');
        $p->addPoint( 504, $p->rotate(503, 5, -90), 'SA offset');
        $p->newPoint(   6, $p->x(3) + $p->deltaX(3, 5)/2, $p->y(3), 'Control point');
        $p->addPoint(   8, $p->shiftTowards(501, 401, $p->deltaY(6, 501)*0.7), 'Control point');
        $p->addPoint(  10, $p->shiftAlong(5, 502, 6, 3, $p->curveLen(5, 502, 6, 3)/2), 'Notch');
        $p->newPoint(  11, 0, $p->y(2), 'Center back');
        $p->clonePoint(11, 'gridAnchor');

        $flip = [ 2, 5, 401, 501, 402, 403, 502, 504, 6, 10, ];
        foreach ($flip as $i) {
            $p->addPoint(-$i, $p->flipX($i), $p->points[$i]->getDescription());
        }

        $this->backPath = 'M -2 L 2 L 401 L 5 C 502 6 3 C -6 -502 -5 L -401 z';
        $p->newPath('outline', $this->backPath, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        
        /* Store seamlength */
        $this->crotchSeamLength = $p->curveLen(5, 502, 6, 3)*2;
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
        $p = $this->parts['back'];

        /* Standard seam allowance */
        $p->offsetPathString('sa', $this->backPath, -10, 1, ['class' => 'seam-allowance']);
        
        /* Extra hem allowance right leg */
        $moveThese = [
            'sa-line-401TO2XllXsa-line-401TO5',
            'sa-line-401TO5',
            'sa-line-5TO401',
            'sa-line-5TO401XlcXsa-curve-5TO3',
        ];
        $angle = $p->angle(8, 5)-90;
        foreach ($moveThese as $i) {
            $p->addPoint($i, $p->shift($i, $angle, 10));
        }

        /* Extra hem allowance left leg */
        $moveThese = [
            'sa-curve--5TO3XclXsa-line--5TO-401',
            'sa-line--5TO-401',
            'sa-line--401TO-5',
            'sa-line--401TO-5XllXsa-line--401TO-2',
        ];
        $angle = $p->angle(-5, -401)-90;
        foreach ($moveThese as $i) {
            $p->addPoint($i, $p->shift($i, $angle, 10));
        }

        /* Title */
        $p->newPoint('titleAnchor', 0, $p->y(11) + 70);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), $this->t('Cut 1'));
        
        /* Scalebox */
        $p->newPoint('scaleboxAnchor', 0, $p->y(11) + 120);
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        
        /**
         * Extra instructions for paperless patterns
         */
        if ($this->isPaperless) {
            /* Points */
            $p->newPoint(  100, $p->x(2), $p->y(2)+20);
            $p->newPoint( -100, $p->x(-2), $p->y(2)+20);
            $p->newPoint(  101, $p->x(-2)+20, $p->y(401));
            $p->newPoint(  102, $p->x(101), $p->y(-2));
            $p->newPoint(  103, $p->x(-5)-20, $p->y(-5));
            $p->newPoint(  104, $p->x(103), $p->y(-2));
            $p->newPoint(  105, 0, $p->y(5));
            $p->newPoint(  106, $p->x(401), $p->y(5)+15);
            $p->addPoint( -106, $p->flipX(106));
            $p->addPoint(  110, $p->shiftTowards(2, 401, 150));


            /* Measurement help lines */
            $p->newPath($p->newId(), "
                M 2 L 100 
                M -2 L -100
                M -401 L 101
                M -5 L 103
                M -401 L -106
                M 401 L 106
            ", $this->hAttr);
            
            /* Measurement arrows */
            $p->newPath($p->newId(), "M -100 L 100", $this->mAttr);
            $p->newPath($p->newId(), "M 101 L 102", $this->mAttr);
            $p->newPath($p->newId(), "M 103 L 104", $this->mAttr);
            $p->newPath($p->newId(), "M -5 L 5", $this->mAttr);
            $p->newPath($p->newId(), "M 105 L 3", $this->mAttr);
            $p->newPath($p->newId(), "M -106 L 106", $this->mAttr);

            /* Measurements text */
            $p->newTextOnPath($p->newId(), "M -100 L 100", $this->unit($p->distance(-2, 2)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 101 L 102", $this->unit($p->distance(101, 102)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M 103 L 104", $this->unit($p->distance(103, 104)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M -5 L 5", $this->unit($p->distance(-5, 5)), ['class' => 'text-lg fill-note text-center', 'dy' => -3, 'dx' => 12]);
            $p->newTextOnPath($p->newId(), "M 105 L 3", $this->unit($p->distance(105, 3)), $this->tAttr);
            $p->newTextOnPath($p->newId(), "M -106 L 106", $this->unit($p->distance(-106, 106)), $this->tAttr);
            
            /* Notes */
            $p->newNote($p->newId(), 110, $this->t("Standard seam allowance")."\n(".$this->unit(10).')', 8, 10, -5, $this->noteAttr);
            $p->newNote($p->newId(), 8, $this->t("Hem allowance")." (".$this->unit(20).')', 12, 25, -10, $this->noteAttr);
        }
    }
}
