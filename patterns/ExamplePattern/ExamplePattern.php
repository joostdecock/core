<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\JoostBodyBlock class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ExamplePattern extends Pattern
{
    public $config_file = __DIR__.'/config.yml';
    public $parts = array();

    public function draft($model)
    {
        $this->msg('this is a test');
        $this->msg('this is another test');
        $this->help = array();
        $this->help['armholeDepth'] = 200 + ($model->getMeasurement('shoulderSlope')/2 - 27.5) + ($model->getMeasurement('upperBicepsCircumference')/10);
        $this->help['collarShapeFactor'] = 1;
        $this->help['sleevecapShapeFactor'] = 1;
        
        $this->loadParts();
        
        $this->draftBack($model);
        $this->draftFront($model);
        $this->draftSleeve($model);
    }

    public function cleanUp()
    {
        unset($this->help);
    }

    private function loadParts()
    {
        foreach ($this->config['parts'] as $part => $title) {
            $this->addPart($part);
            $this->parts[$part]->setTitle($title);
        }
    }

    private function draftBack($model) 
    {
        $collarWidth = ($model->getMeasurement('neckCircumference')/3.1415) / 2 + 5;
        $collarDepth = $this->help['collarShapeFactor'] * (
                $model->getMeasurement('neckCircumference') 
                + $this->getOption('collarEase')
            ) / 5 - 8;
        
        $p = $this->parts['backBlock'];

        // Center vertical axis
        $p->newPoint( 1 ,   0,  $this->getOption('backNeckCutout'), 'Center back @ neck');
        $p->newPoint( 2 ,   0,  $p->y(1) + $this->help['armholeDepth'], 'Center back @ armhole depth' );
        $p->newPoint( 3 ,   0,  $p->y(1) + $model->getMeasurement('centerBackNeckToWaist'), 'Center back @ waist' );
        $p->newPoint( 4 ,   0,  $model->getMeasurement('centerBackNeckToWaist') + $model->getMeasurement('naturalWaistToTrouserWaist') + $this->getOption('backNeckCutout') , 'Center back @ trouser waist');
        
        // Side vertical axis
        $p->newPoint( 5 , $model->getMeasurement('chestCircumference')/4 + $this->getOption('chestEase')/4, $p->y(2) , 'Quarter chest @ armhole depth' );
        $p->newPoint( 6 , $p->x(5), $p->y(4), 'Quarter chest @ trouser waist' );
        
        // Back collar
        $p->newPoint( 7 , $collarWidth, $p->y(1) , 'Half collar width @ center back' );
        $p->newPoint( 8 , $p->x(7), $p->y(1) - $this->getOption('backNeckCutout'), 'Half collar width @ top of garment' );
        
        // Front collar
        $p->newPoint( 9 , 0, $p->y(1) + $collarDepth, 'Center front collar depth');

        // Armhole
        $p->newPoint( 10 , $model->getMeasurement('acrossBack')/2, $p->y(1) + $p->deltaY(1,2)/2, 'Armhole pitch point' );
        $p->newPoint( 11 , $p->x(10), $p->y(2) , 'Armhole pitch width @ armhole depth');
        $p->newPoint( 12 , $p->x(7) + sqrt(pow($model->getMeasurement('shoulderLength'),2) - pow($model->getMeasurement('shoulderSlope')/2,2)), $model->getMeasurement('shoulderSlope')/2, 'Shoulder tip' );
        $p->addPoint( 13 , $p->Shift(5, 180, $p->distance(11,5)/4) , 'Left curve control point for 5');
        $p->addPoint( '.help1' , $p->shift(11, 45, 5), '45 degrees upwards' );
        $p->addPoint( '.help2' , $p->linesCross(11, '.help1', 5, 10), 'Intersection');
        $p->addPoint( 14 , $p->shiftTowards(11, '.help2', $p->distance(11, '.help2')/2), 'Point on armhole curve');
        $p->addPoint( 15 , $p->shift(14, 135, 25), 'Top curve control point for 14' );
        $p->addPoint( 16 , $p->Shift(14, -45, 25), 'Bottom control point for 14' );
        $tmp =  $p->deltaY(12,10)/3;
        $p->addPoint( 17 , $p->shift(10, 90, $tmp), 'Top curve control point for 10' );
        $p->addPoint( 18 , $p->shift(10, -90, $tmp), 'Bottom curve control point for 10');
        $p->addPoint( 19 , $p->shift(12, $p->angle(8,12)+90, 10), 'Bottom control point for 12' );

        // Control points for collar
        $p->addPoint( 20 , $p->shift(8, $p->angle(8,12)+90, $this->getOption('backNeckCutout')), 'Curvei control point for collar' );
        $p->newPoint( 21 , $p->x(8), $p->y(9));

        // Paths
        $path = 'M 1 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 1 1 z';
        $p->newPath('outline', $path);

        // Title anchor
        $p->newPoint('titleAnchor', $p->x(10)/2, $p->y(10), 'Title anchor');
        $p->newText( 'title', 'titleAnchor', $p->title, [ 'id' => "base-title", 'class' => 'title' ]);
    }

    private function draftFront($model) 
    {
        $this->clonePoints('backBlock', 'frontBlock');
        $p = $this->parts['frontBlock'];
       
        // Mirorring half defined in base
        for($i=1; $i<=21; $i++) $p->addPoint($i, $p->flipX($i, $p->x(5)) );

        $frontExtra = 5; // Cutting out armhole a bit deeper at the front
        $p->addPoint( 10 , $p->shift(10, 0, $frontExtra) );
        $p->addPoint( 17 , $p->shift(17, 0, $frontExtra) );
        $p->addPoint( 18 , $p->shift(18, 0, $frontExtra) );
        
        $path = 'M 9 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 9 z';
        $p->newPath('outline', $path);
        
        $p->addPoint('titleAnchor', $p->flipX('titleAnchor', $p->x(5)) );
        $attr = ['id' => "front-title", 'class' => 'title'];
        $p->newText('title', 'titleAnchor', $p->title, $attr);
    }
    
    private function draftSleeve($model) 
    {
        $p = $this->parts['sleeveBlock'];
        
        $this->help['sleevecapSeamLength'] = $this->help['sleevecapShapeFactor'] * ($this->armholeLen() + $this->getOption('sleevecapEase'));
        
        // Sleeve center
        $p->newPoint( 1 , 0, 0 , 'Origin (Center sleeve @ shoulder)');
        $p->newPoint( 2 , 0, $this->help['sleevecapSeamLength']/3, 'Center sleeve @ sleevecap start' );
        $p->newPoint( 3 , 0, $model->getMeasurement('sleeveLengthToWrist') , 'Center sleeve @ wrist');
        
        // Sleeve half width
        $p->newPoint( 4 , $model->getMeasurement('upperBicepsCircumference')/2 + $this->getOption('bicepsEase')/2, 0 , 'Half width of sleeve @ shoulder'); 
        $p->newPoint( 5 , $p->x(4), $p->y(2), 'Half width of sleeve @ sleevecap start' );
        $p->newPoint( 6 , $p->x(4), $p->y(3), 'Half width of sleeve @ wrist' );

        // Sleeve quarter width
        $p->newPoint( 7 , $p->x(4)/2, 0, 'Quarter width of sleeve @ shoulder' );
        $p->newPoint( 8 , $p->x(7), $p->y(2), 'Quarter width of sleeve @ sleevecap start' );
        $p->newPoint( 9 , $p->x(7), $p->y(3), 'Quarter width of sleeve @ wrist' );
        
        // Mirror to get a full sleeve
        for($i=4; $i<=9; $i++) $p->addPoint($i*-1, $p->flipX($i, 0));

        // Back pitch point 
        $p->newPoint( 10 , $p->x(-7), $this->help['sleevecapSeamLength']/6 - 15, 'Back Pitch Point');
        
        // Front pitch point gets 5mm extra room
        $p->newPoint( 11 , $p->x(7) + 5, $p->y(10) + 15, 'Front Pitch Point');

        // Angles of the segments of the sleevecap
        $angleBackLow   = $p->angle(-5,10);
        $angleBackHigh  = $p->angle(10,1);
        $angleFrontLow  = $p->angle(5,11);
        $angleFrontHigh = $p->angle(11,1);
        
        // The 4 quarter marks
        $p->addPoint( 12 , $p->shiftTowards(-5, 10, $p->distance(-5,10)/2), 'Back low quarter');
        $p->addPoint( 13 , $p->shiftTowards(10, 1, $p->distance(10,1)/2), 'Back high quarter' );
        $p->addPoint( 14 , $p->shiftTowards(1, 11, $p->distance(1,11)/2), 'Front high quarter' );
        $p->addPoint( 15 , $p->shiftTowards(11,5, $p->distance(11,5)/2), 'Front low quarter' );

        // Bulge out or in at quarter marks
        $p->addPoint( 16 , $p->shift(12, $angleBackLow+90, 5), 'Back low valley');
        $p->addPoint( 17 , $p->shift(13, $angleBackHigh-90, 15), 'Back high peak' );
        $p->addPoint( 18 , $p->shift(14, $angleFrontHigh+90, 23), 'Front high peak' );
        $p->addPoint( 19 , $p->shift(15, $angleFrontLow-90, 15), 'Front low valley' );

        // Control points for bulges
        // Make control point offset relative to sleeve width
        $cpOffset = $p->x(7) * 0.27;
        $p->addPoint( 20 , $p->shift(16, $angleBackLow,  $cpOffset), 'Bottom control point for 16' );
        $p->addPoint( 21 , $p->shift(16, $angleBackLow, -1*$cpOffset), 'Top control point for 16' );
        $p->addPoint( 22 , $p->shift(17, $angleBackHigh,  $cpOffset), 'Bottom control point for 17' );
        $p->addPoint( 23 , $p->shift(17, $angleBackHigh, -1*$cpOffset), 'Top control point for 17' );
        $p->addPoint( 24,  $p->shift(18, $angleFrontHigh,  $cpOffset), 'Bottom control point for 18' );
        $p->addPoint( 25 , $p->shift(18, $angleFrontHigh, -1*$cpOffset), 'Top control point for 18' );
        $p->addPoint( 26,  $p->shift(19, $angleFrontLow,  $cpOffset), 'Bottom control point for 19' );
        $p->addPoint( 27 , $p->shift(19, $angleFrontLow, -1*$cpOffset), 'Top control point for 19' );
        
        // Sleeve crown
        $p->addPoint( 28 , $p->shift(1, 180, $cpOffset), 'Back control point for crown point' );
        $p->addPoint( 29 , $p->shift(1, 0, $cpOffset), 'Front control point for crown point' );
        // Shift crown point to the front by 0.5cm
        $p->addPoint( 30 , $p->shift(1 ,0, 5), 'Sleeve crown point' );

        // Wrist
        $wristWidth = $model->getMeasurement('wristCircumference') + $this->getOption('cuffEase');
        $p->newPoint( 31 , $wristWidth/-2, $p->y(3), 'Wrist point back');
        $p->newPoint( 32 , $wristWidth/2, $p->y(3), 'Wrist point front');

        // Elbow location
        $p->newPoint( 33 , 0, $p->y(2) + $p->distance(2,3)/2 - 25, 'Elbow point');
        $p->addPoint( '.help1' , $p->shift(33,0,10));
        $p->addPoint( 34 , $p->linesCross(-5, 31, 33, '.help1'), 'Elbow point back side');
        $p->addPoint( 35 , $p->linesCross(5, 32, 33, 34), 'Elbow point front side');

        $path = 'M 31 L -5 C -5 20 16 C 21 10 10 C 10 22 17 C 23 28 30 C 29 25 18 C 24 11 11 C 11 27 19 C 26 5 5 L 32 z';
        $p->newPath('outline', $path);
        
        $p->newPoint('titleAnchor', $p->x(2), $this->parts['frontBlock']->y('titleAnchor') );
        $attr = ['id' => "sleeve-title", 'class' => 'title'];
        $p->newText('title', 'titleAnchor', $p->title, $attr);

        $msg = 'This is a test';

        $p->newText('test', 33, $this->t($msg), ['line-height' => 12, 'class' => 'text-lg align-center']);
        

        $p->newTextOnPath('test1', 'M 10 C 10 22 17 C 23 28 30 C 29 25 18 ', "This text follows a curved path, which is kinda cool for adding notes and other stuff to a pattern", ['line-height' => 12, 'class' => 'text-xs', 'dy' => -2]);
        $p->newTextOnPath('test2', 'M 10 C 10 22 17 C 23 28 30 C 29 25 18 ', "This is the same but bigger text placed under the curve", ['line-height' => 12, 'class' => 'text-sm', 'dy' => 6]);

        if($this->paperless) {
            $attr = ['line-height' => 4, 'class' => 'text-xs', 'dy' => -5];
            $p->newNote('note1', 2,  "Note @\n pos 1", 1, 30, 2, $attr );
            $p->newNote('note2', 2,  "Note @\n pos 2", 2, 30, 2, $attr );
            $p->newNote('note3', 2,  "Note @\n pos 3", 3, 30, 2, $attr );
            $p->newNote('note4', 2,  "Note @\n pos 4", 4, 30, 2, $attr );
            $p->newNote('note5', 2,  "Note @\n pos 5", 5, 30, 2, $attr );
            $p->newNote('note6', 2,  "Note @\n pos 6", 6, 30, 2, $attr );
            $p->newNote('note7', 2,  "Note @\n pos 7", 7, 30, 2, $attr );
            $p->newNote('note8', 2,  "Note @\n pos 8", 8, 30, 2, $attr );
            $p->newNote('note9', 2,  "Note @\n pos 9", 9, 30, 2, $attr );
            $p->newNote('note10', 2, "Note @\n pos 10", 10, 30, 2, $attr );
            $p->newNote('note11', 2, "Note @\n pos 11", 11, 30, 2, $attr );
            $p->newNote('note12', 2, "Note @\n pos 12", 12, 30, 2, $attr );
        }
    
    }
    
    private function armholeLen() 
    {
        $back = $this->parts['backBlock'];
        $front = $this->parts['frontBlock'];
   
        return (
            $back->curveLen(12, 19, 17, 10) + 
            $back->curveLen(10, 18, 15, 14) + 
            $back->curveLen(14, 16, 13, 5) 
        ) + ( 
            $front->curveLen(12, 19, 17, 10) + 
            $front->curveLen(10, 18, 15, 14) + 
            $front->curveLen(14, 16, 13, 5) 
        ); 
    }
    
}
