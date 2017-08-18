<?php
/** Freesewing\Patterns\Docs\NotationLegend class */
namespace Freesewing\Patterns\Docs;

/**
 * This pattern holds info used in the pattern notation documentation
 *
 * This is a **really bad** pattern to study to understand patterns as
 * this generates the figures for the documentation, it's not a real pattern.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class NotationLegend extends \Freesewing\Patterns\Core\Pattern
{
    public function sample($model) { }

    /**
     * This is not a regular pattern. Instead, we take an item
     * option as submitted in the URL 
     * parameters, and construct the method from it.
     *
     * This pattern has only one part, that is passed to the called
     * method. This way, only what we need is calculated, because
     * selecting parts with the part parameter only prevents them
     * from being rendered, not from being calculated.
     *
     * Long story short, this is a bit of a hack.
     */
    public function draft($model)
    {
        $item = 'example_'.$this->o('item'); 
        if(!method_exists($this,$item)) die();
        $this->{$item}($this->parts['part'],$model);
    }

    /**
     * Adds a box to a pattern part
     *
     * For the documentation, we show parts that have only points, or parts
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
    private function addBox($p,$h=100,$w=200, $topLeft=false)
    {
        if($topLeft) $p->addPoint('boxTopLeft', $topLeft);
        else $p->newPoint('boxTopLeft', 0, 0);
        $p->newPoint('boxTopRight', $w, 0);
        $p->newPoint('boxBottomLeft', 0, $h);
        $p->newPoint('boxBottomRight', $w, $h);
        $p->newPath('box', 'M boxTopLeft L boxTopRight L boxBottomRight L boxBottomLeft z', ['class' => 'hidden']);
    }

    private function garment($p, $dx=0, $dy=0, $prefix='')
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint($prefix.'-1', $dx+20, $dy+20);
        $p->newPoint($prefix.'-2', $dx+20, $dy+140);
        $p->newPoint($prefix.'-3', $dx+100,$dy+140);
        $p->newPoint($prefix.'-4', $dx+100,$dy+50);
        $p->newPoint($prefix.'-5', $dx+90, $dy+50);
        $p->newPoint($prefix.'-6', $dx+80, $dy+30);
        $p->newPoint($prefix.'-7', $dx+90, $dy+10);
        $p->newPoint($prefix.'-8', $dx+50, $dy+0);
        $p->newPoint($prefix.'-9', $dx+45, $dy+20);
        $p->newPoint($prefix.'-10',$dx+30, $dy+20);
    }

    /**
     * seamline
     */
    private function example_seamline($p, $model)
    {
        /** @var \Freesewing\Part $p */

        $this->garment($p, 0  ,5,'fabric');
        $this->garment($p, 90 ,5,'lining');
        $this->garment($p, 180,5,'interfacing');
        $this->garment($p, 270,5,'canvas');
        $this->garment($p, 360,5,'various');

        $types = ['fabric','lining','interfacing','canvas','various'];
        foreach($types as $type) {
            $p->newPath($type,"M $type-1 L $type-2 L $type-3 L $type-4 C $type-5 $type-6 $type-7 L $type-8 C $type-9 $type-10 $type-1 z", ['class' => $type]);   
            $p->newTextOnPath($type, "M $type-2 L $type-3", $type, ['dy' => -5, 'class' => "text-lg"], false);

        }
        $this->addBox($p,150,470);
    }
    
    /**
     * seamAllowance
     */
    private function example_seamAllowance($p, $model)
    {
        /** @var \Freesewing\Part $p */

        $this->garment($p, 0  ,5,'fabric');
        $this->garment($p, 90 ,5,'lining');
        $this->garment($p, 180,5,'interfacing');
        $this->garment($p, 270,5,'canvas');
        $this->garment($p, 360,5,'various');

        $types = ['fabric','lining','interfacing','canvas','various'];
        foreach($types as $type) {
            $p->newPath($type,"M $type-1 L $type-2 L $type-3 L $type-4 C $type-5 $type-6 $type-7 L $type-8 C $type-9 $type-10 $type-1 z", ['class' => $type]);   
            $p->offsetPath("$type-sa", $type, 4, 1, ['class' => "$type sa"]);
            $p->newTextOnPath($type, "M $type-2 L $type-3", $type, ['dy' => -5, 'class' => "text-lg"], false);

        }
        $this->addBox($p,150,470);
    }
}
