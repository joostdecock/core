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
        /* Ratio of waist between parts */
        $this->setValue('waistRatioFront', 0.28);
        $this->setValue('waistRatioBack', 0.34);
        $this->setValue('waistRatioSide', (1 - ($this->v('waistRatioFront') + $this->v('waistRatioBack'))) / 2);
        
        /* Ration of leg between parts */
        $this->setValue('legRatioInset', 0.28);
        $this->setValue('legRatioBack', 0.29);
        $this->setValue('legRatioSide', (1 - ($this->v('legRatioInset') + $this->v('legRatioBack'))) / 2);
        
        /* Helpers */
        $this->setValue('halfCross', $model->getMeasurement('crossseamLength')/2);
        $this->setValue('sideWaist', $model->getMeasurement('hipsCircumference') * $this->v('waistRatioSide') * $this->getOption('horizontalStretchFactor'));
        $this->setValue('sideLeg', $model->getMeasurement('upperLegCircumference') * $this->v('legRatioSide') * $this->getOption('horizontalStretchFactor'));
        
        /* Keep stretch ratio to something sensible */
        if ($this->o('verticalStretchFactor')<0.5) {
            $this->setOption('verticalStretchFactor', 0.5);
        }
        if ($this->o('horizontalStretchFactor')<0.5) {
            $this->setOption('horizontalStretchFactor', 0.5);
        }
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
        
        if ($this->isPaperless) {
            $this->paperlessBack($model);
            $this->paperlessSide($model);
            $this->paperlessFront($model);
            $this->paperlessInset($model);
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

        $this->draftBack($model);
        $this->draftSide($model);
        $this->draftFront($model);
        $this->draftInset($model);
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        $p->newPoint(   1, 0, 0, 'Waistline @ center back');
        $p->newPoint(   
            2, 
            $model->getMeasurement('hipsCircumference') * $this->v('waistRatioBack') * $this->getOption('horizontalStretchFactor') / 2, 
            0 - $this->getOption('rise') * $this->getOption('verticalStretchFactor'), 
            'Waistline @ center back'
        );
        $p->addPoint( 201, $p->shift(2, 200, 25));
        $p->newPoint(   3, 0, $p->y(2) + $this->v('halfCross') * $this->getOption('verticalStretchFactor'), 'Crossseam point');
        $p->newPoint(   
            5, 
            $p->x(3) + $this->v('halfCross') * $this->getOption('verticalStretchFactor') * 0.145, 
            $p->y(3) + $this->v('halfCross') * $this->getOption('horizontalStretchFactor') * 0.265, 
            'Inside corner leg'
        );
        $p->newPoint(   
            4, 
            $p->x(5) + $model->getMeasurement('upperLegCircumference') * $this->v('legRatioBack') * $this->getOption('horizontalStretchFactor'), 
            $p->y(5), 
            'Pre-rotate outside corner leg'
        );
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

        $path = 'M -2 L 2 L 401 L 5 C 502 6 3 C -6 -502 -5 L -401 z';
        $p->newPath('seamline', $path, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        
        /* Store seamlength */
        $this->crotchSeamLength = $p->curveLen(5, 502, 6, 3)*2;
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];

        $p->newPoint( 1, 0, 0, 'Zero');
        $p->newPoint( 2, $this->v('sideWaist') / 2, -1* $this->o('rise') * $this->getOption('verticalStretchFactor'), 'Top right');
        $p->newPoint( 3, $this->v('sideLeg'), $this->v('halfCross') * $this->getOption('verticalStretchFactor') + ($model->getMeasurement('upperLegCircumference')/50 - $this->o('rise')), 'Bottom right');
        $p->addPoint( 4, $p->shift(3, -90, 15));
        $p->newPoint( 'gridAnchor', 0, $p->y(2));

        $flip = [ 2, 3, 4 ];
        foreach ($flip as $i) {
            $p->addPoint(-$i, $p->flipX($i), $p->points[$i]->getDescription());
        }
      
        // Storing seam length 
        $this->setValue('frontLength', $p->distance(2, 3));
        
        // Path 
        $path = 'M 2 L 3 L -3 L -2 z';
        $p->newPath('seamline', $path, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        $p->newPoint(   1, 0, 0, 'Waistline @ center front');
        $p->newPoint( 101, 0, -1 * $this->getOption('rise') * $this->getOption('verticalStretchFactor'), 'Waistline @ center front');
        $p->clonePoint(101, 'gridAnchor');
        $p->newPoint(   2, $model->getMeasurement('hipsCircumference') * $this->v('waistRatioFront') * $this->getOption('horizontalStretchFactor')/2, $p->y(101), 'Front top right');
        $p->newPoint(   3, $this->v('halfCross') * $this->getOption('verticalStretchFactor'), $p->y(101), 'Pre-rotate mid tusk point');
        $p->addPoint( 301, $p->rotate(3, 101, -71), 'Mid tusk point');
        $p->newPoint(   4, $p->x(2), ($this->v('halfCross') - $this->o('rise')) * $this->getOption('verticalStretchFactor') * 0.2, 'Pre-rotate tusk start');
        $p->addPoint( 401, $p->rotate(4, 2, 4));
        $p->newPoint( 403, $p->x(2), $p->y(4) - $p->deltaX(1, 2)*0.6, 'Pre-rotate control point');
        $p->addPoint( 404, $p->rotate(403, 4, 100), 'Control point');
        $p->newPoint(   5, $p->x(301) + $this->crotchSeamLength * 0.1, $p->y(301), 'Pre-rotate tusk corner');
        $p->addPoint( 501, $p->rotate(5, 301, 36), 'Tusk corner');
        $p->addPoint( 502, $p->rotate(501, 301, 180), 'Tusk corner');
        $p->addPoint( 503, $p->rotate(301, 502, 110), 'Tusk control point bottom');
        $p->newPoint(   6, 0, ($this->v('halfCross') - $this->o('rise')) * $this->getOption('verticalStretchFactor') * 0.58, 'Tusk join point');
        $p->newPoint( 601, $p->x(6), $p->y(6) + $p->deltaY(6, 502) * 0.25, 'Tusk control point');
        $p->newPoint(   7, $p->x(501), $p->y(2) + $p->deltaY(2, 501) * 0.6, 'Pre-rotate control point');
        $p->addPoint( 701, $p->rotate(7, 501, 35), 'Control point');
        $p->addPoint(   8, $p->shiftAlong(4, 404, 701, 501, 70), 'Notch');

        $this->setValue('insetFrontLength', $this->v('frontLength') - $p->distance(2, 4));

        $flip = [ 2, 4, 401, 404, 501, 502, 503, 701, 8 ];
        foreach ($flip as $i) {
            $p->addPoint(-$i, $p->flipX($i), $p->points[$i]->getDescription());
        }
  
        $path = 'M -2 L 2 L 4 C 404 701 501 L 502 C 503 601 6 C 601 -503 -502 L -501 C -701 -404 -4 z';
        $p->newPath('seamline', $path, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['inset'];

        $p->newPoint(   1, 0, 0, 'Top left' );
        $p->newPoint(   2, 0, $this->v('insetFrontLength'), 'Bottom right' );
        $p->clonePoint( 2, 'gridAnchor' );
        $p->newPoint(   3, $model->m('upperLegCircumference') * $this->v('legRatioInset') * $this->o('horizontalStretchFactor') * -1, $p->y(2), 'Bottom right' );
        $p->addPoint( 201, $p->shift(2, -90, 15) );
        $p->addPoint( 301, $p->shift(3, -90, 15) );
        $p->newPoint(   4, $p->x(3), $p->y(3) - $this->crotchSeamLength * 0.4, 'Bottom right' );
        $p->addPoint( 401, $p->rotate(4, 3, 16), 'Curve bottom point' );
        $p->addPoint( 402, $p->rotate(3, 401, 90), 'Curve bottom point' );
        $p->addPoint( 403, $p->shiftTowards(401, 402, $p->distance(401, 402)*2), 'Control point' );
        $p->newPoint( 101, $p->x(3)*0.7, 0, 'Control point' );
        $p->addPoint(   5, $p->shiftAlong(1, 101, 403, 401, 70), 'Notch' );

        $points = $p->addSplitCurve(1, 101, 403, 401, 0.5, 'split', true);
        
        $path = 'M 2 L 1 C split2 split3 split4 C split7 split6 401 L 3 z';
        
        $p->newPath('seamline', $path, ['class' => 'seamline']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
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

        // Seam allowance
        $p->offsetPath('sa','seamline', -10, 1, ['class' => 'seam-allowance']);
        
        // Extra hem allowance right leg 
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

        // Extra hem allowance left leg 
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

        // Grainline
        $p->newPoint('grainlineTop', $p->x(-2)+20, $p->y(-2)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(-401));
        $p->newGrainline('grainlineBottom','grainlineTop',$this->t('Grainline'));

        // Title 
        $p->newPoint('titleAnchor', 0, $p->y(11) + 70);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from fabric'));
        
        /* Scalebox */
        $p->newPoint('scaleboxAnchor', 0, $p->y(11) + 120);
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];

        // Title 
        $p->newPoint('titleAnchor', 0, $p->y('gridAnchor')+70);
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'));

        // Logo 
        $p->newPoint('logoAnchor', 0, $p->y('gridAnchor')+120);
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        
        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, 1, ['class' => 'seam-allowance']);

        // Extra hem allowance 
        $p->addPoint('sa-line-3TO-3', $p->shift('sa-line-3TO-3', -90, 10));
        $p->addPoint('sa-line--3TO3', $p->shift('sa-line--3TO3', -90, 10));
        $p->newPoint('sa-line-3TO2XllXsa-line-3TO-3', $p->x('sa-line-3TO2'), $p->y('sa-line-3TO-3'));
        $p->newPoint('sa-line--3TO3XllXsa-line--3TO-2', $p->x('sa-line--3TO-2'), $p->y('sa-line-3TO-3'));
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];
        
        // Title 
        $p->newPoint('titleAnchor', 0, $p->y('gridAnchor')+70);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'));

        // Standard seam allowance 
        $p->offsetPath('sa', 'seamline', -10, 1, ['class' => 'seam-allowance']);
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
        /** @var \Freesewing\Part $p */
        $p = $this->parts['inset'];

        // Title 
        $p->newPoint('titleAnchor', $p->x(3)/2.5, $p->y(401));
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'));
        
        // Seam allowance 
        $p->offsetPath('sa', 'seamline', 10, 1, ['class' => 'seam-allowance']);
    
        // Extra hem allowance 
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
    
        // Heights on the left
        $xBase = $p->x(-401);
        $p->newHeightDimension(3,-2,$xBase-15);
        $p->newHeightDimension(-401,-2,$xBase-30);
        $p->newHeightDimension(-5,-2,$xBase-45);

        // Widhts at the bototm
        $yBase = $p->y(-5);
        $p->newWidthDimension(-5,5,$yBase+35);
        $p->newWidthDimension(-401,401,$yBase+50);
        
        // Widhts at the top
        $yBase = $p->y(-2);
        $p->newWidthDimension(-2,2,$yBase-25);

        // Notes
        $p->addPoint('noteAnchor1', $p->shiftTowards(2, 401, 150));
        $p->newNote($p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 8, 10, -5);
        $p->newNote($p->newId(), 8, $this->t("Hem allowance")." (".$this->unit(20).')', 12, 25, -10);
    }

    /**
     * Adds paperless info for the side 
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];
        
        // Heights on the left
        $p->newHeightDimension(-3,-2,$p->x(-3)-25);
        
        // Width at the bottom
        $p->newWidthDimension(-3,3,$p->y(3)+35);
        
        // Width at the top
        $p->newWidthDimension(-2,2,$p->y(2)-25);
        
        // Notes
        $p->addPoint(  'noteAnchor1', $p->shiftTowards(2, 3, 100));
        $p->addPoint(  'noteAnchor2', $p->shift(3, 180, 40));
        $p->newNote( $p->newId(), 'noteAnchor1', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 8, 10, -5);
        $p->newNote( $p->newId(), 'noteAnchor2', $this->t("Hem\nallowance")."\n(".$this->unit(20).')', 12, 25, -13,['class' => 'note', 'dy' => -13, 'line-height' => 7]);
    }

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

        // Most narrow width
        $p->addPoint('narrowLeft', $p->curveEdgeRight(-4,-404,-701,-501));
        $p->addPoint('narrowRight', $p->flipX('narrowLeft',0));
        $p->newWidthDimension('narrowLeft','narrowRight');

        // Widths at the bottom
        $yBase = $p->y(-502);
        $p->newWidthDimension(-502,502,$yBase+25);
        $p->newWidthDimension(-501,501,$yBase+40);

        // Width at the top
        $p->newWidthDimension(-2,2,$p->y(2)-20);

        // Heights at the left
        $xBase = $p->x(-501);
        $p->newHeightDimension(-4,-2,$xBase-15);
        $p->newHeightDimension('narrowLeft',-2,$xBase-30);
        $p->newHeightDimension(6,-2,$xBase-45);
        $p->newHeightDimension(-501,-2,$xBase-60);
        $p->newHeightDimension(-502,-2,$xBase-75);
    }

    /**
     * Adds paperless info for the inset 
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessInset($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['inset'];

        // Height at the left
        $p->newHeightDimension(3,401,$p->x(401)-20);

        // Widths at the bottom
        $p->newWidthDimension(3,2,$p->y(2)+35);
        $p->newWidthDimension(401,2,$p->y(2)+50);
        
        // Height at the right
        $p->newHeightDimension(2,1,$p->x(1)+25);
    }
}
