<?php
/** Freesewing\Patterns\TnameTop class */
namespace Freesewing\Patterns;

/**
 * The Tname Top pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TnameTop extends Pattern
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
        $this->finalizeTop($model, $this->parts['top']);
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
        $this->draftTop($model, $this->parts['top']);
    }

    /**
     * Finalizes the Interfacing Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizeTop($model, $p)
    {
        /* Title */
        $p->newPoint('titleAnchor', $p->x(42)/2, $p->y(42)/2);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('From main fabric')."\n".$this->t('Cut on fold')); 

        // Cut-on-fold (cof)
        $p->newPoint('cofStart', $p->x(1)+40, $p->y(2));
        $p->newPoint('cofLeft', $p->x('cofStart'), $p->y(5)-30);
        $p->newPoint('cofEnd', $p->x(42)-40, $p->y(2));
        $p->newPoint('cofRight', $p->x('cofEnd'), $p->y('cofLeft'));
        $cof = 'M cofStart L cofLeft L cofRight L cofEnd';
        $p->newPath('cof', $cof, ['class' => 'grainline']);
        $p->newTextOnPath('cof', $cof, $this->t('Cut on fold'), ['line-height' => 12, 'class' => 'text-lg fill-fabric text-center', 'dy' => -2]);
     
        // Grainline
        $p->newPoint('grainlineTop', $p->x(1)+10, $p->y('cofLeft')-30);
        $p->newPoint('grainlineBottom', $p->x(42)-10, $p->y('grainlineTop'));
        $p->newPath('grainline', 'M grainlineTop L grainlineBottom', ['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M grainlineTop L grainlineBottom', $this->t('Grainline'), ['line-height' => 12, 'class' => 'text-lg fill-fabric text-center', 'dy' => -2]);
        
        // Paperless
        if ($this->isPaperless) {
            // Measures
            $pAttr = ['class' => 'measure-lg'];
            $hAttr = ['class' => 'stroke-note stroke-sm'];

            $key = 'toArmholeTop';
            $path = 'M 2 L 3';
            $p->offsetPathString($key, $path, 15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(2, 3)), $this->textAttr(13));

            $key = 'toArmholeBottom';
            $path = 'M 2 L 4';
            $p->offsetPathString($key, $path, 30, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(2, 4)), $this->textAttr(28));

            $key = 'width';
            $path = 'M 2 L 42';
            $p->offsetPathString($key, $path, 45, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(2, 42)), $this->textAttr(43));

            // Bring points to the left
            $p->newPoint(100, $p->x(1), $p->y(23));
            $p->newPoint(101, $p->x(1), $p->y(22));
            
            $key = 'sideHeight1';
            $path = 'M 100 L 41';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(41, 100)), $this->textAttr(-17));
            
            $key = 'sideHeight2';
            $path = 'M 101 L 41';
            $p->offsetPathString($key, $path, -30, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(41, 101)), $this->textAttr(-32));
            
            $key = 'sideHeight3';
            $path = 'M 1 L 41';
            $p->offsetPathString($key, $path, -45, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(41, 1)), $this->textAttr(-47));
            
            $key = 'sideHeight4';
            $path = 'M 8 L 41';
            $p->offsetPathString($key, $path, -60, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(41, 1)), $this->textAttr(-62));
            
            $key = 'height';
            $path = 'M 2 L 41';
            $p->offsetPathString($key, $path, -75, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(41, 2)), $this->textAttr(-77));

        }
    }

    /**
     * Drafts the Top
     *
     * @see \Freesewing\Patterns\TrayvonTie::draftInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function draftTop($model, $p)
    {
        // Shoulder to shoulder len
        $s2s = $model->m('acrossBack')+50;
        // Armhole depth
        $ad = $model->m('acrossBack')*0.65;
        // Chest depth
        $ch = $model->m('chestCircumference')/2+2;
  
        $p->newPoint(     1 , 0, 0);
        $p->newPoint(     2 , $p->x(1),sqrt(pow($s2s,2)-100));
        $p->newPoint(     3 , 100,$p->y(2));
        $p->newPoint(     4 , $p->x(3)+$ad,$p->y(3));
        $p->newPoint(     5 , $p->x(3)+$ad/2,$p->y(3)-40);
        $p->newPoint(     6 , $p->x(5)-$ad/2.5,$p->y(5));
        $p->addPoint(     7 , $p->flipX(6,$p->x(5)));
        $p->newPoint(     8 , $p->x(1),$p->y(1)+50);
        $p->addPoint(     9 , $p->shiftTowards(3,1,50));
        $angle = $p->angle(8,3);
        $p->addPoint(    10 , $p->rotate(9,3,$angle-90));
        $p->newPoint(    20 , $p->x(4),$p->y(4)-$ch);
        $p->addPoint(    21 , $p->rotate(20,4,$angle-90));
        $p->addPoint(    22 , $p->shiftTowards(1,21,$ad));
        $p->addPoint(    23 , $p->shiftTowards(1,21,$model->m('centerBackNeckToWaist')+$model->m('naturalWaistToHip')));
        $p->addPoint(    24 , $p->shiftTowards(1,21,$model->m('centerBackNeckToWaist')+$model->m('naturalWaistToHip')+40));
        $p->addPoint(    31 , $p->shift(1,0,5));
        $p->addPoint(    38 , $p->shift(8,0,5));
        $p->newPoint(    41 , $p->x(1),$p->y(24));
        $p->newPoint(    42 , $p->x(1)+$model->m('centerBackNeckToWaist')+$model->m('naturalWaistToHip')+200+$this->o('lengthBonus'),$p->y(3));
        $p->newPoint(    43 , $p->x(42),$p->y(41));
        $p->newPoint(    44 , $p->x(1),$p->y(3));  

        // Paths
        $arc = '3 C 3 6 5 C 7 4 4';
        $sew = "M 22 L 23 M 10 L 3 M 31 L 38 M $arc" ;
        $fabric = "M 41 L 44 $arc L 42 L 43 z";
        $p->newPath('cutline', $fabric);
        $p->newPath('sewline', $sew, ['class' => 'helpline']);

        // Mark paths for sample service
        $p->paths['cutline']->setSample(true);
        $p->paths['sewline']->setSample(true);
         
    }
    
    private function textAttr($dy)
    {
        return ['class' => 'text-lg fill-note text-center', 'dy' => $dy];
    }

}
