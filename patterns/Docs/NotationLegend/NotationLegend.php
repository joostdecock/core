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
        if($this->o('item') === 'none') {
            // Show all figures
            foreach(get_class_methods(__CLASS__) as $method) {
                if(substr($method,0,8) == 'example_') {
                    $this->newPart($method);
                    $this->{$method}($this->parts[$method],$model); 
                }
            }
        } else {
            // Show specific figure
            $method = 'example_'.$this->o('item'); 
            if(!method_exists($this,$method)) die('Method not found');
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
            $p->offsetPath("$type-sa", $type, -4, 1, ['class' => "$type sa"]);
            $p->newTextOnPath($type, "M $type-2 L $type-3", $type, ['dy' => -5, 'class' => "text-lg"], false);

        }
        $this->addBox($p,150,470);
    }
    
    /**
     * helpLine
     */
    private function example_helpLine($p, $model)
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
            $p->addPoint("$type-11", $p->shift("$type-4",-90,50));
            $p->newPoint("$type-12", $p->x("$type-1"), $p->y("$type-11"));
            $p->newPath("$type-help","M $type-11 L $type-12", ['class' => 'help']);   
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * hintLine
     */
    private function example_hintLine($p, $model)
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
            $p->addPoint("$type-11", $p->shift("$type-5",210,10));
            $p->addPoint("$type-12", $p->shift("$type-11",180,20));
            $p->addPoint("$type-13", $p->shift("$type-11",-90,20));
            $p->addPoint("$type-14", $p->shift("$type-12",-90,20));
            $p->newPoint("$type-15", $p->x("$type-14")+10, $p->y("$type-14")+3);
            $p->newPath("$type-help","M $type-11 L $type-13 L $type-15 L $type-14 L $type-12 z", ['class' => 'hint']);   
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * grainLine
     */
    private function example_grainLine($p, $model)
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
            $p->addPoint("$type-11", $p->shift("$type-8",-90,10));
            $p->newPoint("$type-12", $p->x("$type-11"), $p->y("$type-2")-5);
            $p->newGrainline("$type-12","$type-11", 'Grainline');
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * Cut-on-fold line, cofLine
     */
    private function example_cofLine($p, $model)
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
            $p->addPoint("$type-11", $p->shift("$type-1",-90,10));
            $p->addPoint("$type-12", $p->shift("$type-2",90,10));
            $p->newCutOnFold("$type-12","$type-11", 'Cut on fold', 13);
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * Notches
     */
    private function example_notches($p, $model)
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
            $p->addPoint("$type-11", $p->curveEdge("$type-4", "$type-5", "$type-6", "$type-7", 'left'));
            $p->notch(["$type-11"]);
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * Buttons
     */
    private function example_buttons($p, $model)
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
            $p->addPoint("$type-11", $p->shiftFractionTowards("$type-1", "$type-2", 0.2));
            $p->addPoint("$type-12", $p->shiftFractionTowards("$type-1", "$type-2", 0.4));
            $p->addPoint("$type-13", $p->shiftFractionTowards("$type-1", "$type-2", 0.6));
            $p->addPoint("$type-14", $p->shiftFractionTowards("$type-1", "$type-2", 0.8));
            $p->addPoint("$type-11", $p->shift("$type-11", 0, 8));
            $p->addPoint("$type-12", $p->shift("$type-12", 0, 8));
            $p->addPoint("$type-13", $p->shift("$type-13", 0, 8));
            $p->addPoint("$type-14", $p->shift("$type-14", 0, 8));
            $p->newSnippet("$type-btn1", 'button', "$type-11");
            $p->newSnippet("$type-btn2", 'button', "$type-12");
            $p->newSnippet("$type-btn3", 'button', "$type-13");
            $p->newSnippet("$type-btn4", 'button', "$type-14");
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * Buttonholess
     */
    private function example_buttonholes($p, $model)
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
            $p->addPoint("$type-11", $p->shiftFractionTowards("$type-1", "$type-2", 0.2));
            $p->addPoint("$type-12", $p->shiftFractionTowards("$type-1", "$type-2", 0.4));
            $p->addPoint("$type-13", $p->shiftFractionTowards("$type-1", "$type-2", 0.6));
            $p->addPoint("$type-14", $p->shiftFractionTowards("$type-1", "$type-2", 0.8));
            $p->addPoint("$type-11", $p->shift("$type-11", 0, 8));
            $p->addPoint("$type-12", $p->shift("$type-12", 0, 8));
            $p->addPoint("$type-13", $p->shift("$type-13", 0, 8));
            $p->addPoint("$type-14", $p->shift("$type-14", 0, 8));
            $p->newSnippet("$type-btn1", 'buttonhole', "$type-11");
            $p->newSnippet("$type-btn2", 'buttonhole', "$type-12");
            $p->newSnippet("$type-btn3", 'buttonhole', "$type-13");
            $p->newSnippet("$type-btn4", 'buttonhole', "$type-14");
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * Buttonholess
     */
    private function example_snaps($p, $model)
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
            $p->addPoint("$type-13", $p->shiftFractionTowards("$type-1", "$type-2", 0.8));
            $p->addPoint("$type-14", $p->shiftFractionTowards("$type-1", "$type-2", 0.95));
            $p->addPoint("$type-11", $p->shift("$type-13", 0, 72));
            $p->addPoint("$type-12", $p->shift("$type-14", 0, 72));
            $p->addPoint("$type-13", $p->shift("$type-13", 0, 8));
            $p->addPoint("$type-14", $p->shift("$type-14", 0, 8));
            $p->newSnippet("$type-btn1", 'snap-female', "$type-11");
            $p->newSnippet("$type-btn2", 'snap-male', "$type-12");
            $p->newSnippet("$type-btn3", 'snap-female', "$type-13");
            $p->newSnippet("$type-btn4", 'snap-male', "$type-14");
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * Logo
     */
    private function example_logo($p, $model)
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
            $p->addPoint("$type-11", $p->shiftFractionTowards("$type-1", "$type-2", 0.5));
            $p->addPoint("$type-11", $p->shift("$type-11", 0, 35));
            $p->newSnippet("$type-logo", 'logo', "$type-11");
        }
        $this->addBox($p,150,470);
    }
    
    /**
     * Scalebox
     */
    private function example_scalebox($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint (1, 50, 5);
        $p->newSnippet('box', 'scalebox', 1);
        $this->addBox($p,60,110);
        $this->replace('__REFERENCE__','abcde');
    }
    
    /**
     * Title
     */
    private function example_title($p, $model)
    {
        // Default
        $this->newPart("title1");
        $p = $this->parts["title1"];
        $p->newPoint (1, 50, 50);
        $p->addTitle(1, 1,"Part name", "This is a \ndefault title");
        $this->addBox($p,100,100);
        
        // Scale: 150
        $this->newPart("title2");
        $p = $this->parts["title2"];
        $p->newPoint (1, 50, 50);
        $p->addTitle(1, 2,"Part name", "This uses the scale option\nhere, it is set to 125\n(default is 100)", ['scale' => 125]);
        $this->addBox($p,100,100);
        
        // Scale: 50
        $this->newPart("title3");
        $p = $this->parts["title3"];
        $p->newPoint (1, 50, 50);
        $p->addTitle(1, 3,"Part name", "This uses the scale option\nhere, it is set to 75\n(default is 100)", ['scale' => 75]);
        $this->addBox($p,100,100);
        
        // Align: left
        $this->newPart("title4");
        $p = $this->parts["title4"];
        $p->newPoint (1, 10, 50);
        $p->addTitle(1, 4,"Part name", "This uses the align option\nhere, it is set to left\n(default is center)", ['align' => 'left']);
        $this->addBox($p,100,100);
        
        // Align: right
        $this->newPart("title5");
        $p = $this->parts["title5"];
        $p->newPoint (1, 90, 50);
        $p->addTitle(1, 5,"Part name", "This uses the align option\nhere, it is set to right\n(default is center)", ['align' => 'right']);
        $this->addBox($p,100,100);
        
        // Rotate: 90
        $this->newPart("title6");
        $p = $this->parts["title6"];
        $p->newPoint (1, 50, 50);
        $p->addTitle(1, 6,"Part name", "This uses the rotate option\nhere, it is set to 180\n(default is 0)", ['rotate' => 180]);
        $this->addBox($p,100,100);
        
        // Combo
        $this->newPart("title7");
        $p = $this->parts["title7"];
        $p->newPoint (1, 100, 10);
        $p->addTitle(1, "Combo\nexample","Part\nname", "You can also\ncombine these options\nin addition, line breaks\nwill be handled for you", ['rotate' => -90, 'scale' => 150, 'align' => 'right']);
        $this->addBox($p,200,200);
    }

    /**
     * Note
     */
    private function example_note($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint (1, 10, 10);
        $p->newNote('1', 1,'I am a note');
        $this->addBox($p,30,110);
    }
    
    /**
     * Dimension
     */
    private function example_dimension($p, $model)
    {
        /** @var \Freesewing\Part $p */
        $p->newPoint (1, 10, 20);
        $p->newPoint (2, 110, 20);
        $p->newLinearDimension(1,2);
        $this->addBox($p,30,110);
    }
    
}
