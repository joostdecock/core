<?php
/** Freesewing\Patterns\Docs\DesignTutorial class */
namespace Freesewing\Patterns\Docs;

use Freesewing\BezierToolbox;

/**
 * This pattern holds info used in the pattern design tutorial
 *
 * This is a **really bad** pattern to study to understand patterns as
 * this generates the figures for a tutorial, it's not a real pattern.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class DesignTutorial extends \Freesewing\Patterns\Core\Pattern
{
    public function sample($model) { } // Not used

    public function draft($model)
    {
        if($this->o('figure') === 'none') {
            // Show all figures
            foreach(get_class_methods(__CLASS__) as $method) {
                if(substr($method,0,8) == 'example_') {
                    $this->newPart($method);
                    $this->{$method}($this->parts[$method],$model); 
                }
            }
        } else {
            // Show specific figure
            $method = 'example_'.$this->o('figure'); 
            if(!method_exists($this,$method)) die('Method not found');
            $this->{$method}($this->parts['part'],$model);
        }
    }

    private function example_quarterNeck($p, $model)
    {
        $this->setValue('headNeckRatio', 0.8);
        $model->setMeasurement('chestCircumference', 489);
        $model->setMeasurement('headCircumference', 472);

        // Let's start with a precise neck opening
        $p->newPoint(1, 0, $model->m('headCircumference')/8, 'Bottom of the neck opening');
        $p->newPoint(2, $model->m('headCircumference')/6, 0, 'Right side of neck opening');
        $p->addPoint(3, $p->shift(1,0,$p->x(2)/2), 'Right control point for neckBottom');
        $p->addPoint(4, $p->shift(2,-90,$p->y(1)/2), 'Bottom control point for neckRight');

        // Path
        $p->newPath(1, 'M 1 C 3 4 2');   
    }    
        
    private function example_neckOpening($p, $model)
    {
        $this->setValue('headNeckRatio', 0.8);
        $model->setMeasurement('chestCircumference', 489);
        $model->setMeasurement('headCircumference', 472);

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

        // Draw the neck opening
        $p->newPath('neckOpening', 'M 1 C 3 4 2 C top3 top2 top1 C top4 top5 left1 C left3 left2 1 z');

    }

    private function neckOpeningDelta($model,$part)
    {
        $length = $part->curveLen(1,3,4,2) * 4;
        $target = $model->m('headCircumference') * $this->v('headNeckRatio');
        return $length - $target;
    }

    private function example_shape($p, $model)
    {
        $this->example_neckOpening($p, $model);

        // 25mm strap around the neck opening, which will also define the width of our bib
        $strap = 25;

        // Basic box
        $p->newPoint('topLeft', $p->x('left1')-$strap, $p->y('top1')-$strap);
        $p->addPoint('topRight', $p->flipX('topLeft', 0));
        $p->newPoint('bottomLeft', $p->x('topLeft'), $p->y(1)+$model->m('chestCircumference')/3);
        $p->addPoint('bottomRight', $p->flipX('bottomLeft', 0));
        
        // Draw the bounding box as a helpline
        $p->newPath('box', 'M bottomLeft L topLeft L topRight L bottomRight z', ['class' => 'helpline']);
    }   

    private function example_beziercircle($p, $model)
    {
        $this->example_shape($p, $model);

        // Make radius 50mm
        $radius = 50;

        // Bottom right corner
        $p->addPoint('bottomRightCornerStart', $p->shift('bottomRight',180,$radius));
        $p->addPoint('bottomRightCornerStartCp', $p->shift('bottomRightCornerStart',0, BezierToolbox::bezierCircle($radius)));
        $p->addPoint('bottomRightCornerEnd', $p->rotate('bottomRightCornerStart','bottomRight',-90));
        $p->addPoint('bottomRightCornerEndCp', $p->rotate('bottomRightCornerStartCp','bottomRight',-90));
        
        // Bottom left corner
        $p->addPoint('bottomLeftCornerStart', $p->flipX('bottomRightCornerStart',0));
        $p->addPoint('bottomLeftCornerStartCp', $p->flipX('bottomRightCornerStartCp',0));
        $p->addPoint('bottomLeftCornerEnd', $p->flipX('bottomRightCornerEnd',0));
        $p->addPoint('bottomLeftCornerEndCp', $p->flipX('bottomRightCornerEndCp',0));
        
        $p->newPath('outline', 'M bottomLeftCornerEnd C bottomLeftCornerEndCp bottomLeftCornerStartCp bottomLeftCornerStart L bottomRightCornerStart C bottomRightCornerStartCp bottomRightCornerEndCp bottomRightCornerEnd');
    }

    private function example_strapbend($p, $model)
    {
        $this->example_beziercircle($p, $model);

        // Top right corner
        $p->newPoint('topRightCornerStart', $p->x('topRight'), 0);
        $p->newPoint('topRightCornerStartCp', $p->x('topRight'), $p->y('topRight')/2);
        $p->newPoint('topRightCornerEnd', 0, $p->y('topRight'));
        $p->newPoint('topRightCornerEndCp', $p->x('topRight')/2, $p->y('topRight'));
        
        $p->newPath(
            'outline', 
            '
                M bottomLeftCornerEnd C bottomLeftCornerEndCp bottomLeftCornerStartCp bottomLeftCornerStart 
                L bottomRightCornerStart 
                C bottomRightCornerStartCp bottomRightCornerEndCp bottomRightCornerEnd
                L topRightCornerStart
                C topRightCornerStartCp topRightCornerEndCp topRightCornerEnd
                ');
    }
    
    private function example_strapshape($p, $model)
    {
        $this->example_strapbend($p, $model);

        // Snap anchor point
        $p->newPoint('snapAnchor', 0, $p->y('top1')-12.5);

        // Finish strap
        $p->addPoint('snapCpTop', $p->rotate('snapAnchor','topRightCornerEnd',-90));
        $p->addPoint('snapCpBottom', $p->rotate('snapAnchor','top1',90));

        $p->newPath(
            'outline', 
            '
                M bottomLeftCornerEnd C bottomLeftCornerEndCp bottomLeftCornerStartCp bottomLeftCornerStart 
                L bottomRightCornerStart 
                C bottomRightCornerStartCp bottomRightCornerEndCp bottomRightCornerEnd
                L topRightCornerStart
                C topRightCornerStartCp topRightCornerEndCp topRightCornerEnd
                C snapCpTop snapCpBottom top1
                ');
    }
    
    private function example_straprotate($p, $model)
    {
        $this->example_strapshape($p, $model);

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
    }

    private function example_outline($p, $model)
    {
        $this->example_straprotate($p, $model);

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

        // Hide paths drawn earlier
        $p->paths['box']->setRender(false);
        $p->paths['neckOpening']->setRender(false);
    }

    private function example_bias($p, $model)
    {
        $this->example_outline($p, $model);
    
        $p->offsetPath('bias', 'outline', 3, true, ['class' => 'helpline']);
    }

    private function example_snap($p, $model)
    {
        $this->example_bias($p, $model);
    
        // Snap button
        $p->newSnippet(1, 'snap-male', 'snapAnchor');
        $p->newSnippet(2, 'snap-female', 'snap2Anchor');

        // Logo
        $p->addPoint('logoAnchor', $p->shift(1,-90,120)); 
        $p->newSnippet('logo', 'logo', 'logoAnchor');
    }
    
    private function example_title($p, $model)
    {
        $this->example_snap($p, $model);

        $p->addPoint('titleAnchor', $p->shift(1,-90,60)); 
        $p->addTitle('titleAnchor', 1, $this->t('Baby bib'), '1x');

    }

    private function example_note($p, $model)
    {
        $this->example_title($p, $model);

        $p->newNote(1,1,$this->t('Finish with bias tape'), 12, 15, -3);
        $p->newNote(2,'snap2Anchor',$this->t('Attach snap at the back'), 6, 25, 4);
    }

    private function example_dimensions($p, $model)
    {
        $this->example_note($p, $model);

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

    public function draftFigure1($model)
    {
        $p = $this->parts['figure1'];
        $p->newPoint(1, 0, 0, 'Look, I am a point');
        $this->addBox($p,5);
    }

    public function draftFigure2($model)
    {
        $this->clonePoints('figure1','figure2');
        $p = $this->parts['figure2'];
        $p->newPoint('mySecondPoint', 100, 50);
        $this->addBox($p,50);
    }

    public function draftFigure3($model)
    {
        $this->clonePoints('figure2','figure3');
        $p = $this->parts['figure3'];
        $p->newPoint(3, 50, 0);
        $p->newPoint(4, 0, 50);
        $p->newNote( 1, 3, '', 9, 50, 0); 
        $p->newNote( 2, 4, '', 12, 50, 0); 
        $p->newTextOnPath(1, 'M 1 L 3', 'X-axis');
        $p->newTextOnPath(2, 'M 4 L 1', 'Y-axis');
        $this->addBox($p,50);
    }

    public function draftFigure4($model)
    {
        $this->clonePoints('figure2','figure4');
        $p = $this->parts['figure4'];
        $p->newPath(1,'M 1 L mySecondPoint');
        $this->addBox($p,50);
    }

    public function draftFigure11($model)
    {
        $this->clonePoints('figure2','figure11');
        $p = $this->parts['figure11'];
        $p->newPath(1,'M 1 L mySecondPoint', ['class' => 'helpline']);
        $this->addBox($p,50);
    }

    public function draftFigure5($model)
    {
        $p = $this->parts['figure5'];
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 100, 0);
        $p->newPoint(3, 100, 50);
        $p->newPoint(4, 0, 50);
        $p->newPoint(5, 50, 25);
        $this->addBox($p,50);
    }

    public function draftFigure6($model)
    {
        $this->clonePoints('figure5','figure6');
        $p = $this->parts['figure6'];
        $p->newPath(1,'M 5 L 2 L 3');
        $this->addBox($p,50);
    }

    public function draftFigure7($model)
    {
        $this->clonePoints('figure5','figure7');
        $p = $this->parts['figure7'];
        $p->newPath(1,'M 5 L 2 L 3 z');
        $this->addBox($p,50);
    }

    public function draftFigure8($model)
    {
        $this->clonePoints('figure5','figure8');
        $p = $this->parts['figure8'];
        $p->newPath(1,'M 1 C 2 3 4');
        $this->addBox($p,50);
    }

    public function draftFigure9($model)
    {
        $this->clonePoints('figure5','figure9');
        $p = $this->parts['figure9'];
        $p->newPath(1,'M 5 L 1 C 2 3 4 z');
        $this->addBox($p,50);
    }
    
    public function draftFigure10($model)
    {
        $p = $this->parts['figure10'];
        $p->newPoint(1, 20, 10);
        $p->newPoint(2, 40, 10);
        $p->newPoint(3, 60, 10);
        $p->newSnippet(1, 'notch', 1);
        $p->newSnippet(2, 'button', 2);
        $p->newSnippet(3, 'buttonhole', 3);
        $this->addBox($p,20);
    }

    public function draftFigure12($model)
    {
        $p = $this->parts['figure12'];
        $p->newPoint(1, 30, 10);
        $p->newText(1, 1, 'Hello world');
        $this->addBox($p,20);
    }

    public function draftFigure13($model)
    {
        $p = $this->parts['figure13'];
        $p->newPoint('titleAnchor', 50, 35);
        $p->addTitle('titleAnchor', 3, 'French cuff', "Cut 4x from fabric");;
        $this->addBox($p,50);
    }

    public function draftFigure14($model)
    {
        $p = $this->parts['figure14'];
        $p->newPoint('titleAnchor', 50, 35);
        $p->addTitle('titleAnchor', 3, 'French cuff', 'Mode small','small');;
        $this->addBox($p,50);
    }

    public function draftFigure15($model)
    {
        $p = $this->parts['figure15'];
        $p->newPoint('titleAnchor', 50, 35);
        $p->addTitle('titleAnchor', 3, 'French cuff', 'Mode vertical','vertical');;
        $this->addBox($p,100);
    }

    public function draftFigure16($model)
    {
        $p = $this->parts['figure16'];
        $p->newPoint('titleAnchor', 50, 35);
        $p->addTitle('titleAnchor', 3, 'French cuff', 'Mode horizontal','horizontal');;
        $this->addBox($p,50);
    }

    public function draftFigure17($model)
    {
        $p = $this->parts['figure17'];
        $p->newPoint('titleAnchor', 50, 35);
        $p->addTitle('titleAnchor', 3, 'French cuff', 'Mode vertical-small','vertical-small');;
        $this->addBox($p,80);
    }

    public function draftFigure18($model)
    {
        $p = $this->parts['figure18'];
        $p->newPoint('titleAnchor', 50, 35);
        $p->addTitle('titleAnchor', 3, 'French cuff', 'Mode horizontal-small','horizontal-small');;
        $this->addBox($p,50);
    }

    /**
     * Adds a box to a pattern part
     *
     * For the tutorial, we show parts that have only points, or parts
     * with points that would fall out of the bounding box.
     * So we draw an invisible box to make sure the points we are 
     * interested in are not cropped.
     *
     * @param \Freesewing\Part $part The part to add the box to
     * @param float $w The width of the box
     * @param float $h The height of the box
     *
     * @return void
     */
    private function addBox($p,$h=100,$w=200)
    {
        $p->newPoint('boxTopLeft', 0, 0);
        $p->newPoint('boxTopRight', $w, 0);
        $p->newPoint('boxBottomLeft', 0, $h);
        $p->newPoint('boxBottomRight', $w, $h);
        $p->newPath('box', 'M boxTopLeft L boxTopRight L boxBottomRight L boxBottomLeft z', ['class' => 'hidden']);
    }
}
