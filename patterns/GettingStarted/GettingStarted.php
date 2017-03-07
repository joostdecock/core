<?php
/** Freesewing\Patterns\GettingStarted class */
namespace Freesewing\Patterns;

/**
 * This pattern holds info used in the getting started guide
 *
 * This is a **really bad** pattern to study to understand patterns as
 * this generates the figures for the documentation, it's not a real pattern.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class GettingStarted extends Pattern
{
    public function sample($model) { }

    /**
     * This is not a regular pattern. Instead, we take a 
     * class and method option as submitted in the URL 
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
        $method = 'example_'.$this->o('figure'); 
        if(!method_exists($this,$method)) die();
        $this->{$method}($this->parts['part'],$model);
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

    /**
     * Shows the pattern/measurements/options ingredients
     */
    private function example_ingredients($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 50, 0);
        $p->newPoint(3, 50, 25);
        $p->newPoint(4, 0, 25);
        $p->newPoint(5, 25, 12.5);
        $p->newPoint(6, 25, 25);

        for ($i=1;$i<7;$i++) $p->addPoint("1$i",$p->shift($i,0,75));
        for ($i=1;$i<7;$i++) $p->addPoint("2$i",$p->shift($i,0,150));
        for ($i=1;$i<7;$i++) $p->addPoint("4$i",$p->shift("1$i",-90,110));
        for ($i=1;$i<7;$i++) $p->addPoint("5$i",$p->shift("1$i",-90,160));
        for ($i=1;$i<7;$i++) $p->addPoint("6$i",$p->shift("5$i",180,75));

        $p->newPoint(31, 100, 50);
        $p->newPoint(32, 120, 70);
        $p->newPoint(33, 100, 90);
        $p->newPoint(34, 80, 70);
        $p->newPoint(35, 90, 60);
        $p->newPoint(36, 110, 60);

        $white = 'fill:#ffffff;';
        $p->newText(1, 5, "Our\npattern", ['class' => 'text-lg text-center', 'line-height' => 8, 'dy' => -2, 'style' => $white]);
        $p->newText(2, 15, "Your\nmeasurements", ['class' => 'text-lg text-center', 'line-height' => 8, 'dy' => -2, 'style' => $white]);
        $p->newText(3, 25, "Your\noptions", ['class' => 'text-lg text-center', 'line-height' => 8, 'dy' => -2, 'style' => $white]);
        $p->newText(4, 33, "Our\ncode", ['class' => 'text-lg text-center', 'line-height' => 8, 'dy'=>-20, 'style' => $white]);
        $p->newText(5, 46, "Your\ndraft", ['class' => 'text-lg text-center', 'line-height' => 8, 'dy'=>-14, 'style' => $white]);
        $p->newText(6, 56, "You make\nsomething", ['class' => 'text-lg text-center', 'line-height' => 8, 'dy'=>-14, 'style' => $white]);
        $p->newText(7, 66, "Our\ndocumentation", ['class' => 'text-lg text-center', 'line-height' => 8, 'dy'=>-14, 'style' => $white]);

        $purple = ['style' => 'fill:rgba(102,63,149,1); stroke: none;'];
        $blue = ['style' => 'fill:#5bc0de; stroke: none;'];

        $p->newPath(1,"M 1 L 2 L 3 L 4 z", $purple);   
        $p->newPath(2,"M 11 L 12 L 13 L 14 z", $blue);   
        $p->newPath(3,"M 21 L 22 L 23 L 24 z", $blue);   
        $p->newPath(4,"M 31 L 32 L 33 L 34 z", $purple);   
        $p->newPath(5,"M 41 L 42 L 43 L 44 z", $blue);   
        $p->newPath(6,"M 51 L 52 L 53 L 54 z", $blue);   
        $p->newPath(7,"M 61 L 62 L 63 L 64 z", $purple);   

        $p->newNote(1, 35, '', 10, 69,0);
        $p->newNote(2, 31, '', 12, 25,0);
        $p->newNote(3, 36, '', 2, 69,0);
        $p->newNote(4, 45, '', 12, 32,12.5);
        $p->newNote(5, 55, '', 12, 37.5,12.5);
        $p->newNote(6, 55, '', 9, 50,25);
    }
}
