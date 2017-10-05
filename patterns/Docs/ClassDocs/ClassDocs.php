<?php
/** Freesewing\Patterns\Docs\ClassDocs class */
namespace Freesewing\Patterns\Docs;

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
class ClassDocs extends \Freesewing\Patterns\Core\Pattern
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
        if($this->o('class') === 'none' && $this->o('method') === 'none') {
            // Show all
            foreach(get_class_methods(__CLASS__) as $method) {
                if(substr($method,0,8) == 'example_') {
                    $this->newPart($method);
                    $this->{$method}($this->parts[$method],$model); 
                }
            }
        } else {
            // Show specific example
            $method = 'example_'.$this->o('class').'_'.$this->o('method'); 
            if(!method_exists($this,$method)) die('No such method');
            $this->{$method}($this->parts['part'],$model);
        }
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
        
        $p->newTextOnPath(1,"M 1 C 2 3 4", "Length of this curve: ".$p->unit($p->curveLen(1,2,3,4)), ["dy" => -2,'class' => 'text-center']);

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
        $p->splitCurve(1,2,3,4,5,'s');

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

    /**
     * Text::generic example (not tied to a method)
     */
    private function example_Text_generic($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);

        $p->newText(1, 1, "Hello world");
        $p->newText(2, 1, "I am text with some attributes set",['class' => 'text-sm fill-note', 'dy' => 6]);
        
        $this->addBox($p,30);
    }

    /**
     * Part::unit example
     */
    private function example_Part_unit($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 40, 10);
        $p->newPoint(2, 40, 20);
        
        // If not set explicitly, units are metric by default
        $p->newNote(1, 1, $p->unit(100),9,15,0);

        $p->setUnits('imperial');
        $p->newNote(2, 2, $p->unit(100),9,15,0);
        $this->addBox($p,30);
    }

    /**
     * Part::newPath example
     */
    private function example_Part_newPath($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);
        $p->newPoint(2, 100, 20);
        $p->newPoint(3, 50, 40);
        $p->newPoint(4, 40, 30);
        $p->newPoint(5, 40, 0);
        
        $p->newPath(1, 'M 1 L 2');
        $p->newPath('another', 'M 4 C 3 2 5 z', ['class' => 'helpline']);

        $this->addBox($p,35);
    }

    /**
     * Part::newPoint example
     */
    private function example_Part_newPoint($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);
        $p->newPoint(2, 100, 20);
        $p->newPoint(3, 50, 40);
        $p->newPoint(4, -40, -30, 'Negative coordinates point');

        $this->addBox($p,35,200, $p->loadPoint(4));
    }

    /**
     * Part::newSnippet example
     */
    private function example_Part_newSnippet($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 20, 10);
        $p->newPoint(2, 40, 10);
        $p->newPoint(3, 60, 10);

        $p->newSnippet(1, 'button', 1);
        $p->newSnippet('anotherOne', 'buttonhole', 2);
        $p->newSnippet(3, 'buttonhole', 3, [
            'transform' => 'rotate(90 '.$p->x(3).' '.$p->y(3).')'
        ]);

        $this->addBox($p,20);
    }

    /**
     * Part::newInclude example
     */
    private function example_Part_newInclude($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newInclude(1, '<image x="0" y="0" width="80" height="80" xlink:href="https://upload.wikimedia.org/wikipedia/commons/0/02/SVG_logo.svg" />');

        $this->addBox($p,80);
    }

    /**
     * Part::newText example
     */
    private function example_Part_newText($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 50, 5);
        $p->newPoint(2, 90, 40);

        $p->newText(1, 1, 'This is standard text');
        $p->newText(2, 1, 'This is text with CSS classes set', ['class' => 'text-center text-lg fill-note', 'dy' => 10]);
        $p->newText(3, 1, "Multiline text\nsupport is\nshaky", ['dx' => -20, 'dy' => 20,]);
        $p->newText(4, 2, "Use line-height as attribute\nand avoid the dx and dy attributes\nfor multiline text", ['line-height' => 6] );

        $this->addBox($p,60);
    }

    /**
     * Part::newNote example
     */
    private function example_Part_newNote($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 50, 55);
        $p->newPoint(2, 140, 55);
        $p->newPoint(3, 220, 55);

        for($i=1;$i<=12;$i++) {
            $p->newNote("direction$i", 1, $i, $i);
            $p->newNote("length$i", 2, (15+$i*2), $i, (15+$i*2));
            $p->newNote("offset$i", 3, ($i*2), $i, 25, $i);
        }
        $p->newText(1, 1, "Direction from 1 to 12, like hands of a clock", ['dy' => 40, 'class' => 'text-center']);
        $p->newText(2, 2, "Lenght of the arrow", ['dy' => 40, 'class' => 'text-center']);
        $p->newText(3, 3, "Offset from the anchor point", ['dy' => 40, 'class' => 'text-center']);
        $p->newText(4, 2, "Any attributes you pass will apply to the text, not the arrow", ['dy' => 55, 'class' => 'text-xl text-center fill-warning', 'stroke-width' => 0.1, 'fill' => 'none']);


        $this->addBox($p,130,260);
    }

    /**
     * Part::newTextOnPath example
     */
    private function example_Part_newTextOnPath($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 10);
        $p->newPoint(2, 100, 10);
        $p->newPoint(3, 100, 50);
        $p->newPoint(4, 60, 40);

        $p->newTextOnPath(1,'M 1 L 2', 'This is text on a path');
        $p->newTextOnPath(2,'M 2 C 3 4 1', "I'm like, super upside down right now", ['class' => 'text-center fill-warning', 'dy' => -2]);
        $p->newTextOnPath(3,'M 1 C 4 3 2', "Text on a curved path is a bit more interesting", ['class' => 'text-center', 'dy' => -2]);

        $this->addBox($p,40,120);
    }

    /**
     * Part::addTitle example
     */
    private function example_Part_addTitle($p, $model)
    {
        $modes = [
            'default',
            'vertical',
            'horizontal',
            'small',
            'vertical-small',
            'horizontal-small',
        ];
        
        foreach($modes as $mode) {
            // Only one title per part, so we need a part for each mode
            $this->newPart($mode);
            /** @var \Freesewing\Part $p */
            $p = $this->parts[$mode];
            $p->newPoint(1, 20, 35);
            $p->addTitle(1, 3, 'Title', $mode, $mode);

            if(strpos($mode,'mall')) {
                if(strpos($mode,'tical')) $this->addBox($p,65,40);
                else $this->addBox($p,45,30);
            } else {
                if(strpos($mode,'tical')) $this->addBox($p,70,40);
                else $this->addBox($p,50,60);
            }
        }
        
    }

    /**
     * Part::addPoint example
     */
    private function example_Part_addPoint($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);
        
        $p->addPoint(2, $p->flipX(1,30));

        $this->addBox($p,40,120);
    }

    /**
     * Part::offsetPath example
     */
    private function example_Part_offsetPath($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);
        $p->newPoint(2, 20, 90);
        $p->newPoint(3, 30, 10);
        $p->newPoint(4, 80, 40);
        $p->newPoint(5, 110, 10);
        $p->newPoint(6, 110, 80);
        $p->newPoint(7, 30, 80);
        
        $p->newPath(1, 'M 1 L 2');
        $p->newPath(2, 'M 3 L 4 L 5 C 6 7 3 z');

        $p->offsetPath(4,1,-10,1, ['class' => 'seam-allowance']);
        $p->offsetPath(5,2,10,1, ['class' => 'seam-allowance']);
    }
    
    /**
     * Part::offsetPathString example
     */
    private function example_Part_offsetPathString($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 30, 10);
        $p->newPoint(2, 80, 40);
        $p->newPoint(3, 110, 10);
        $p->newPoint(4, 110, 80);
        $p->newPoint(5, 30, 80);
        
        $p->newPath(1, 'M 1 L 2 L 3 C 4 5 1 z');

        $p->offsetPathString(2,'M 3 C 4 5 1',5,1, ['class' => 'stroke-warning']);
        $p->offsetPathString(3,'M 1 L 2 L 3',10,1, ['class' => 'stroke-note']);
    }
    
    /**
     * Part::rotate example
     */
    private function example_Part_rotate($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint('earth', 40, 40);
        $p->newPoint('moon', 80, 40);

        for ($i=0;$i<360;$i+=10) {
            $p->addPoint($i,$p->rotate('moon','earth',$i));
            $deltaX = $p->deltaX('earth', $i);
            $deltaY = $p->deltaY('earth', $i);
            $rad = atan2($deltaY*-1,$deltaX);
            //$this->dbg("deltaX of point $i is $deltaX");
            //$this->dbg("deltaY of point $i is $deltaY");
            //$this->dbg("Radials of point $i is $rad");
            $this->dbg("Angle of point $i is ".round($p->angle('earth', $i)));
            //$this->dbg(" ");
        }    
        $p->newNote(60,60,60,7,15);
        $p->newNote(120,120,120,5,15);
        $p->newNote(180,180,180,3,15);
        $p->newNote(270,270,'270 or -90',12,15);
        $p->newNote(0,0,0,9,15);
        
        $this->addBox($p,80);
    }
    
    /**
     * Part::angle example
     */
    private function example_Part_angle($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint('earth', 40, 40);
        $p->newPoint('moon', 80, 40);

        for ($i=0;$i<360;$i+=10) {
            $p->addPoint($i,$p->rotate('moon','earth',$i));
            $p->newTextOnPath($i, "M earth L $i", round($p->angle('earth',$i),0), ['class' => 'text-sm', 'dy' => 1]);
        }    
        
        $this->addBox($p,80);
    }
    
    /**
     * Part::shiftTowards example
     */
    private function example_Part_shiftTowards($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint('origin', 90, 0);
        $p->newPoint('direction', 10, 90);
        $p->addPoint(1, $p->shiftTowards('origin','direction',30));

        $p->newNote(1,'origin','origin',3);
        $p->newNote(2,'direction','direction',3);

        $p->newNote(3,1,'Point origin shifted towards direction by 30mm',5);
        $p->newLinearDimensionSm(1,'origin');
        
        $this->addBox($p,90);
    }
    
    /**
     * Part::shiftFractionTowards example
     */
    private function example_Part_shiftFractionTowards($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint('origin', 90, 30);
        $p->newPoint('direction', 60, 70);

        $p->addPoint(1, $p->shiftFractionTowards('origin','direction',0.5));
        $p->addPoint(2, $p->shiftFractionTowards('origin','direction',1.2));

        $p->newPath('line', 'M origin L direction', ['class' => 'hint']);
        $p->newNote(1,'origin','origin',3);
        $p->newNote(2,'direction','direction',3);

        $p->newNote(3,1,'Point shifted from origin 50% towards direction');
        $p->newNote(4,2,'Point shifted from origin 120% towards direction');
        
        $this->addBox($p,90);
    }
    
    /**
     * Part::shiftAlong example
     */
    private function example_Part_shiftAlong($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 100, 50);
        $p->addPoint(5, $p->shiftAlong(1,2,3,4, 50));
 
        $p->newPath(1,"M 1 C 2 3 4");
        
        $this->addBox($p,90);
    }
    
    /**
     * Part::shiftOutwards example
     */
    private function example_Part_shiftOutwards($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint('origin', 90, 0);
        $p->newPoint('direction', 40, 50);
        $p->newPath('line', 'M origin L direction', ['class' => 'hint']);

        $p->addPoint(1, $p->shiftOutwards('origin','direction',30));

        $p->newNote(1,'origin','origin',3);
        $p->newNote(2,'direction','direction',3);
        $p->newNote(3,1,'Point shifted outwards by 3cm');
        $p->newLinearDimensionSm(1,'direction');
        
        $this->addBox($p,90);
    }
    
    /**
     * Part::shiftFractionAlong example
     */
    private function example_Part_shiftFractionAlong($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 100, 50);
        $p->addPoint(5, $p->shiftFractionAlong(1,2,3,4, 0.5));
 
        $p->newPath(1,"M 1 C 2 3 4");
        
        $p->newNote(1,5,'Point shifted 50% along the curve',2);
        $this->addBox($p,90);
    }
    
    /**
     * Part::linesCross example
     */
    private function example_Part_linesCross($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 70, 50);
        $p->newPoint(3, 100, 0);
        $p->newPoint(4, 10, 50);

        $p->addPoint(5, $p->linesCross(1,2,3,4));
 
        $p->newPath(1,"M 1 L 2 M 3 L 4");
        
        $this->addBox($p,90);
    }
    
    /**
     * Part::beamsCross example
     */
    private function example_Part_beamsCross($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 21, 15);
        $p->newPoint(3, 100, 0);
        $p->newPoint(4, 10, 50);

        $p->addPoint(5, $p->beamsCross(1,2,3,4));
 
        $p->newPath(1,"M 1 L 2 M 3 L 4");
        
        $this->addBox($p,90);
    }
    
    /**
     * Part::flipX example
     */
    private function example_Part_flipX($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 50, 0);
        $p->newPoint(2, 30, 35);
        $p->newPoint(3, 35, 35);
        $p->newPoint(4, 15, 70);
        $p->newPoint(5, 40, 70);
        $p->newPoint(6, 40, 90);

        for($i=2;$i<7;$i++) {
            $p->addPoint(-$i,$p->flipX($i,50));
        }
        
        $p->newPath(1, 'M 1 L 2 L 3 L 4 L 5 L 6 L -6 L -5 L -4 L -3 L -2 z');
        
        $this->addBox($p,90);
    }

    /**
     * Part::flipY example
     */
    private function example_Part_flipY($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 40);
        $p->newPoint(2, 100, 40);
        $p->newPoint(3, 10, 40);
        $p->newPoint(4, 10, 20);
        $p->newPoint(5, 20, 20);
        $p->newPoint(6, 20, 40);
        $p->newPoint(7, 25, 40);
        $p->newPoint(8, 25, 30);
        $p->newPoint(9, 30, 25);
        $p->newPoint(10, 45, 25);
        $p->newPoint(11, 45, 15);
        $p->newPoint(12, 50, 5);
        $p->newPoint(13, 55, 15);
        $p->newPoint(14, 55, 40);

        $p->newPath(1,'M 1 L 2 M 3 L 4 L 5 L 6 L 7 L 8 L 9 L 10 L 11 L 12 L 13 L 14');

        for($i=3;$i<15;$i++) {
            $p->addPoint(-$i,$p->flipY($i,40));
        }
        $p->newPath(2,'M -3 L -4 L -5 L -6 L -7 L -8 L -9 L -10 L -11 L -12 L -13 L -14', ['class' => 'helpline']);

        $this->addBox($p,75);
    }

    /**
     * Part::curveCrossesX example
     */
    private function example_Part_curveCrossesX($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 40, 0);
        $p->newPoint(2, 90, 10);
        $p->newPoint(3, 10, 60);
        $p->newPoint(4, 80, 40);
        

        $p->newPath(1,'M 1 C 2 3 4');

        // This will add points sample1, 
        // sample2, and sample3 to the part
        $p->curveCrossesX(1,2,3,4,55,'sample');

        $this->addBox($p,60);
    }

    /**
     * Part::curveCrossesY example
     */
    private function example_Part_curveCrossesY($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 70, 20);
        $p->newPoint(2, 80, 60);
        $p->newPoint(3, 10, 0);
        $p->newPoint(4, 40, 60);
        

        $p->newPath(1,'M 1 C 2 3 4');

        // This will add points sample1, 
        // sample2, and sample3 to the part
        $p->curveCrossesY(1,2,3,4,33,'sample');

        $this->addBox($p,60);
    }

    /**
     * Part::shift example
     */
    private function example_Part_shift($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);

        // Point2: Shift Point 1 20 to the right
        $p->addPoint(2, $p->shift(1,0,20));

        // Point 3: Shift Point 2 20 down
        $p->addPoint(3, $p->shift(2,-90,20));

        // Point 4: Shift Point 3 20 to the left
        $p->addPoint(4, $p->shift(3,180,20));

        $this->addBox($p,20);
    }

    /**
     * Part:splitCurve example
     */
    private function example_Part_splitCurve($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 100, 100);
        $p->newPoint(4, 100, 50);
        $p->addPoint(5, $p->shiftAlong(1,2,3,4, 50));

        // This will add points 's1' to 's8' 
        // to the part's points array
        $p->splitCurve(1,2,3,4,5,'s');

        $p->newPath(1,"M 1 C 2 3 4", ['class' => 'debug']);
        $p->newPath(2,"M s1 C s2 s3 s4", ['class' => 'seam-allowance stroke-note']);
        $p->newPath(3,"M s5 C s6 s7 s8", ['class' => 'seam-allowance']);
        $this->addBox($p,100,100);
    }

    /**
     * Part::circlesCross example
     */
    private function example_Part_circlesCross($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 75, 40);
        $p->newPoint(2, 125, 60);

        $p->newInclude('circle1', '<circle xmlns="http://www.w3.org/2000/svg" cx="75" cy="40" r="40" style="stroke: #ccc; stroke-width: 0.3; stroke-dasharray: 1 1;"/>'); 
        $p->newInclude('circle2', '<circle xmlns="http://www.w3.org/2000/svg" cx="125" cy="60" r="30" style="stroke: #ccc; stroke-width: 0.3; stroke-dasharray: 1 1;"/>'); 

        $p->circlesCross(1,40,2,30,'isect');
        $p->notch(['isect1','isect2']);

        $this->addBox($p,90);
    }

    /**
     * Part::circlesCrossesLine example
     */
    private function example_Part_circlesCrossesLine($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 75, 40);
        $p->newPoint(2, 25, 80);
        $p->newPoint(3, 145, 20);

        $p->newPath('line', 'M 2 L 3', ['style' =>'stroke: #ccc; stroke-width: 0.3; stroke-dasharray: 1 1;']);
        $p->newInclude('circle', '<circle xmlns="http://www.w3.org/2000/svg" cx="75" cy="40" r="40" style="stroke: #ccc; stroke-width: 0.3; stroke-dasharray: 1 1;"/>'); 

        $p->circleCrossesLine('1',40,2,3,'isect');
        $p->notch(['isect1','isect2']);

        $this->addBox($p,90);
    }

    /**
     * Part:newWidthDimension example
     */
    private function example_Part_newWidthDimension($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 25);
        $p->newPoint(2, 80, 40);

        // Minimal 
        $p->newWidthDimension(1,2);

        // With Y-value
        $p->newWidthDimension(1,2, 60);

        // With all options
        $p->newWidthDimension(
            1,
            2, 
            10, 
            'Hello world',
            ['class' => 'seam-allowance dimension'],
            ['class' => 'text-xl fill-brand dimension-label', 'dy' => -1],
            ['class' => 'debug']
        );
        $this->addBox($p,80);
    }
    
    /**
     * Part:newHeightDimension example
     */
    private function example_Part_newHeightDimension($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 80, 70);

        // Minimal 
        $p->newHeightDimension(1,2);

        // With X-value
        $p->newHeightDimension(1,2, 40);

        // With all options
        $p->newHeightDimension(
            1,
            2, 
            90, 
            'Hello world',
            ['class' => 'seam-allowance dimension'],
            ['class' => 'text-xl fill-brand dimension-label', 'dy' => -1],
            ['class' => 'debug']
        );
        $this->addBox($p,70);
    }
    
    /**
     * Part:newLinearDimension example
     */
    private function example_Part_newLinearDimension($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 20);
        $p->newPoint(2, 80, 70);

        // Minimal 
        $p->newLinearDimension(1,2);

        // With offset
        $p->newLinearDimension(1,2, 20);

        // With all options
        $p->newLinearDimension(
            1,
            2, 
            -20, 
            'Hello world',
            ['class' => 'seam-allowance dimension'],
            ['class' => 'text-xl fill-brand dimension-label', 'dy' => -1],
            ['class' => 'debug']
        );
        $this->addBox($p,90);
    }

    /**
     * Part:newWidthDimensionSm example
     */
    private function example_Part_newWidthDimensionSm($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 25);
        $p->newPoint(2, 30, 40);

        // Minimal 
        $p->newWidthDimensionSm(1,2);

        // With Y-value
        $p->newWidthDimensionSm(1,2, 60);

        // With all options
        $p->newWidthDimensionSm(
            1,
            2, 
            10, 
            'Hello world',
            ['class' => 'seam-allowance dimension dimension-sm'],
            ['class' => 'text-sm fill-brand dimension-label', 'dy' => -1],
            ['class' => 'debug']
        );
        $this->addBox($p,60);
    }
    
    /**
     * Part:newHeightDimensionSm example
     */
    private function example_Part_newHeightDimensionSm($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 20, 30);

        // Minimal 
        $p->newHeightDimensionSm(1,2);

        // With X-value
        $p->newHeightDimensionSm(1,2, 30);

        // With all options
        $p->newHeightDimensionSm(
            1,
            2, 
            40, 
            'Hello world',
            ['class' => 'seam-allowance dimension dimension-sm'],
            ['class' => 'text-sm fill-brand dimension-label', 'dy' => -1],
            ['class' => 'debug']
        );
        $this->addBox($p,30);
    }
    
    /**
     * Part:newLinearDimensionSm example
     */
    private function example_Part_newLinearDimensionSm($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 20);
        $p->newPoint(2, 40, 30);

        // Minimal 
        $p->newLinearDimensionSm(1,2);

        // With offset
        $p->newLinearDimensionSm(1,2, 20);

        // With all options
        $p->newLinearDimensionSm(
            1,
            2, 
            -20, 
            'Hello world',
            ['class' => 'seam-allowance dimension dimension-sm'],
            ['class' => 'fill-brand dimension-label text-sm', 'dy' => -1],
            ['class' => 'debug']
        );
        $this->addBox($p,90);
    }
    
    /**
     * Part:newCurvedDimension example
     */
    private function example_Part_newCurvedDimension($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 100);
        $p->newPoint(2, 30, 0);
        $p->newPoint(3, 70, 80);
        $p->newPoint(4, 100, 50);

        $p->newPoint(5, 10, 0);
        $p->newPoint(6, 14, 20);
        $p->newPoint(7, 80, 30);

        $p->newPath(1,"M 1 C 2 3 4");   
        $p->newPath(2,"M 5 L 6 L 7");   

        // A typical curved dimension 
        $p->newCurvedDimension('M 1 C 2 3 4', 10);

        // Along a path that isn't curved
        $p->newCurvedDimension('M 5 L 6 L 7', 10);

        $this->addBox($p,90);
    }

    /**
     * Part:newGrainline example
     */
    private function example_Part_newGrainline($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 0, 0);
        $p->newPoint(2, 40, 80);

        $p->newGrainline(1,2,$this->t('Grainline'));
        $this->addBox($p,80);
    }

    /**
     * Part:newCutonfold example
     */
    private function example_Part_newCutonfold($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);
        $p->newPoint(2, 10, 90);

        $p->newCutonfold(2,1,$this->t('Cut on fold'));
        $this->addBox($p,90);
    }

    /**
     * Part:notch example
     */
    private function example_Part_notch($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint(1, 10, 10);
        $p->newPoint(2, 20, 10);
        $p->newPoint(3, 30, 10);
        $p->newPoint(4, 40, 10);
        $p->newPoint(5, 50, 10);

        $p->notch([2,4,5]);
        $this->addBox($p,10);
    }
}
