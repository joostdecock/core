<?php
/** Freesewing\Patterns\ClassDocs class */
namespace Freesewing\Patterns;

/**
 * This pattern holds info used in the class documentation
 *
 * This is a **really bad** pattern to study to understand patterns as
 * this generates the figures for the documentation, it's not a real pattern.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ClassDocs extends Pattern
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
        $method = 'example_'.$this->o('class').'_'.$this->o('method'); 
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
    private function addBox($p,$h=100,$w=200)
    {
        $p->newPoint('boxTopLeft', 0, 0);
        $p->newPoint('boxTopRight', $w, 0);
        $p->newPoint('boxBottomLeft', 0, $h);
        $p->newPoint('boxBottomRight', $w, $h);
        $p->newPath('box', 'M boxTopLeft L boxTopRight L boxBottomRight L boxBottomLeft z', ['class' => 'hidden']);
    }

    /**
     * BezierToolbox::bezierBoundary example
     */
    private function example_BezierToolbox_bezierBoundary($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 50, 50);
        $p->newPoint(2, 0, 0);
        $p->newPoint(3, 230, 120);
        $p->newPoint(4, 100, 100);

        $p->newPath(1,"M 1 C 2 3 4");   
        $boundary = $p->paths[1]->findBoundary($p);

        $p->addPoint("topLeft", $boundary->getTopLeft());
        $p->addPoint("bottomRight", $boundary->getBottomRight());
        $p->newPoint("topRight", $p->x("bottomRight"), $p->y("topLeft"));
        $p->newPoint("bottomLeft", $p->x("topLeft"), $p->y("bottomRight"));

        $p->newPath(2,"M topLeft L topRight L bottomRight L bottomLeft z", ["class" => "helpline"]);   
        $p->newTextOnPath(1,"M 1 C 2 3 4", "Bezier curve", ["dy" => -2]);
        $p->newNote(1,"topRight", "Boundary", 2, 15, 0);
        $this->addBox($p,120,230);
    }

    /**
     * BezierToolbox::bezierEdge example
     */
    private function example_BezierToolbox_bezierEdge($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 50, 50);
        $p->newPoint(2, 0, 0);
        $p->newPoint(3, 230, 120);
        $p->newPoint(4, 100, 100);

        $p->newPath(1,"M 1 C 2 3 4");   
        
        $p->addPoint('leftEdge', $p->curveEdge(1,2,3,4,'left'));
        $p->addPoint('rightEdge', $p->curveEdge(1,2,3,4,'right'));
        $p->addPoint('topEdge', $p->curveEdge(1,2,3,4,'top'));
        $p->addPoint('bottomEdge', $p->curveEdge(1,2,3,4,'bottom'));

        $p->newTextOnPath(1,"M 1 C 2 3 4", "Bezier curve", ["dy" => -2]);
        $p->newNote(1,"leftEdge", "Left edge", 9, 15, 0);
        $p->newNote(2,"rightEdge", "Right edge", 3, 15, 0);
        $p->newNote(3,"topEdge", "Top edge", 12, 15, 0);
        $p->newNote(4,"bottomEdge", "Bottom edge", 6, 15, 0);

        $this->addBox($p,120,230);
    }

    /**
     * BezierToolbox::cubucBezierLength example
     */
    private function example_BezierToolbox_bezierLength($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 100, 50);

        $p->newPath(1,"M 1 C 2 3 4");   
        
        $p->newTextOnPath(1,"M 1 C 2 3 4", "Length of this curve: ".$this->unit($p->curveLen(1,2,3,4)), ["dy" => -2,'class' => 'text-center']);

        $this->addBox($p,100,100);
    }

    /**
     * BezierToolbox::bezierLineIntersections example
     */
    private function example_BezierToolbox_bezierLineIntersections($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 100, 50);

        $p->newPoint(5, 0, 80);
        $p->newPoint(6, 110, 55);
        
        $p->newPath(1,"M 1 C 2 3 4 M 5 L 6");

        // This will add points 'i1', 'i2' and 'i3' 
        // to the part's points array
        $p->curveCrossesLine(1,2,3,4,5,6,'i');
        
        $this->addBox($p,100,100);
    }

    /**
     * BezierToolbox::bezierDelta example
     */
    private function example_BezierToolbox_bezierDelta($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 100, 50);
        $p->addPoint(5, $p->shiftAlong(1,2,3,4, 50));

        $p->newPath(1,"M 1 C 2 3 4");

        $delta = \Freesewing\BezierToolbox::bezierDelta(
            $p->loadPoint(1),
            $p->loadPoint(2),
            $p->loadPoint(3),
            $p->loadPoint(4),
            $p->loadPoint(5)
        );
        $p->newNote(1,5, "Delta of this point: $delta", 5, 25, 0, ['dy' => 2]);
        $this->addBox($p,100,100);
    }

    /**
     * BezierToolbox::bezierSplit example
     */
    private function example_BezierToolbox_bezierSplit($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 100, 50);
        $p->addPoint(5, $p->shiftAlong(1,2,3,4, 50));

        $p->newPath(1,"M 1 C 2 3 4");

        // This will add points 's1' to 's8' 
        // to the part's points array
        $p->addSplitCurve(1,2,3,4,5,'s');

        $p->newPath(2,"M s5 C s6 s7 s8", ['class' => 'debug']);
        $this->addBox($p,100,100);
    }

    /**
     * BezierToolbox::bezierCircle example
     */
    private function example_BezierToolbox_bezierCircle($p, $model)
    {
        $r = \Freesewing\BezierToolbox::bezierCircle(50);
        
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 50, 0);
        $p->newPoint(3, 50+$r, 0);
        $p->newPoint(4, 100, 50-$r);
        $p->newPoint(5, 100, 50);
        $p->newPoint(6, 100,100);

        $p->newPath(1,"M 1 L 2 C 3 4 5 L 6");

        $this->addBox($p,100,100);
    }

    /**
     * BezierToolbox::bezierBezierIntersections example
     */
    private function example_BezierToolbox_bezierBezierIntersections($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 0, -200);
        $p->newPoint(3, 100, 300);
        $p->newPoint(4, 100, 0);
        $p->newPath(1,"M 1 C 2 3 4");

        $p->newPoint(5, 0, 10);
        $p->newPoint(6, 330, 10);
        $p->newPoint(7, -230, 90);
        $p->newPoint(8, 100, 90);
        $p->newPath(2,"M 5 C 6 7 8");

        // This will add points 'i1' => 'i9' 
        // to the part's points array
        $p->curvesCross(1,2,3,4,5,6,7,8,'i');
        
        $this->addBox($p,100,100);
    }

    /**
     * Dimension::generic example (not tied to a method)
     */
    private function example_Dimension_generic($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);
        $p->newPoint(2, 110, 10);

        $p->newWidthDimension(1,2, 30, 'I am a dimension');
        
        $p->newPoint('leaderNoteAnchor', 110, 20);
        $p->newPoint('labelNoteAnchor', 55, 25);

        $p->newNote(1,'leaderNoteAnchor','Dimension leader',3,15,0);
        $p->newNote(2,'labelNoteAnchor','Dimension label',12,15,0);
        
        $this->addBox($p,50);
    }

    /**
     * Note::generic example (not tied to a method)
     */
    private function example_Note_generic($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 70, 30);

        $p->newNote(1, 1, "I am a note",12,25,0);
        $p->newNote(2, 1, "Hi there,\nI am a note too",9,25,0);
        $p->newNote(3, 1, "Me too!\nBut I'm keeping my distance",4,45,10);
        
        $this->addBox($p,60);
    }

}
