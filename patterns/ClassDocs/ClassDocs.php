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

    public function draft($model)
    {
        // Total number of figures to draft
        $total = 1;

        // Let's go
        for($i=1;$i<=$total;$i++) {
            $this->addPart("figure$i");
            $method = "draftFigure$i";
            $this->{$method}($model);
        }
    }

    /**
     * BezierToolbox::findBezierBoundary example
     */
    public function draftFigure1($model)
    {
        $p = $this->parts['figure1'];
        $p->newPoint(1, 50, 50);
        $p->newPoint(2, 0, 0);
        $p->newPoint(3, 230, 120);
        $p->newPoint(4, 100, 100);
        $p->newPath(1,'M 1 C 2 3 4');   
        $boundary = $p->paths[1]->findBoundary($p);
        $p->addPoint('topLeft', $boundary->getTopLeft());
        $p->addPoint('bottomRight', $boundary->getBottomRight());
        $p->newPoint('topRight', $p->x('bottomRight'), $p->y('topLeft'));
        $p->newPoint('bottomLeft', $p->x('topLeft'), $p->y('bottomRight'));
        $p->newPath(2,'M topLeft L topRight L bottomRight L bottomLeft z', ['class' => 'helpline']);   
        $p->newTextOnPath(1,'M 1 C 2 3 4', 'Bezier curve', ['dy' => -2]);
        $p->newNote(1,'topRight', 'Boundary', 2, 15, 0);
        $this->addBox($p,120,230);
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
