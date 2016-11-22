<?php
/** Freesewing\Patterns\JoostBodyBlock class */
namespace Freesewing\Patterns;

/**
 * The Hugo Hoodie pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class HugoHoodie extends JoostBodyBlock
{
    /**
     * Generates a draft of the pattern
     *
     * This creates a draft of this pattern for a given model
     * and set of options. You get a complete pattern with 
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->sample($model);
        // Finalize all parts, but not the blocks
        foreach($this->parts as $key => $part) {
            if(!strpos($key,'Block')) {
                $this->{'finalize'.ucfirst($key)}($model, $this->parts[$key]);
            }
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
        $this->loadHelp($model);
        // Lower the armhole
        $this->help['armholeDepth'] += 50;

        // Draft all parts
        foreach($this->parts as $key => $part) $this->{'draft'.ucfirst($key)}($model);
        
        // Hide base blocks
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
        $this->parts['sleeveBlock']->setRender(false);
    }

    /**
     * Drafts the front
     *
     * I'm using a draft[part name] scheme here but
     * don't let that think that this is something specific
     * to the draft service.
     * 
     * This draft method does the basic drafting and is
     * called by both the draft AND sample methods.
     *
     * The difference starts after this method is done.
     * For sample, this is all we need, but draft calls
     * the finalize[part name] method after this.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFront($model)
    {
        $this->clonePoints('frontBlock', 'front'); 
        $p = $this->parts['front'];

        // Making neck opening wider and deeper
        $p->addPoint(  9, $p->shift( 9,-90,10), 'Center front collar depth');
        $p->addPoint( 21, $p->shift(21,-90,10), 'Control point for 9');
        $angle = $p->angle(8,12);
        $p->addPoint(  8, $p->shift( 8,$angle,-20, 'Collar width @ top of garment'));
        $p->addPoint( 20, $p->shift(20,$angle,-20, 'Collar width @ top of garment'));
        // Making garment longer
        $p->addPoint(  4, $p->shift( 4,-90,60), 'Center back at front bottom');
        $p->addPoint(  6, $p->shift( 6,-90,60), 'Quarter chest at front bottom');
        
        // Adding points from index 100 onwards
        $p->addPoint( 100, $p->shiftAlong(8,20,21,9, $p->curveLen(8,20,21,9)/3), 'Raglan front tip');
        $p->addSplitCurve(5,8,20,21,9,100);

        // Add pocket points
        $p->newPoint( 101, $p->x(6)*0.6 - 25, $p->y(6));
        $p->newPoint( 102, $p->x(6)*0.6, $p->y(6)-50);
        $p->newPoint( 103, $p->x(101), $p->y(4)-$p->x(102));
        $p->addPoint( 104, $p->shift(103,90,$p->deltaY(102,103)*0.75));
        $p->newPoint( 105, 0, $p->y(103));
        
        // Paths
        $path = 'M 9 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 100 100 C 57 56 9 z';
        $p->newPath('seamline', $path);

        /**
         * Uncomment paths below if you want to hack this pattern
         * they will help you understand things
         */
        // $frontBlock = 'M 9 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 9 z';
        // $backBlock = 'M 1 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 1 z';
        // $p->newPath('frontBlock', $frontBlock,['class' => 'helpline']);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        
        // Grid anchor
        $p->clonePoint(4, 'gridAnchor');
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
        $this->clonePoints('backBlock', 'back'); 
        $p = $this->parts['back'];
        // Making neck opening wider and deeper
        $p->addPoint(  1, $p->shift( 1,-90,10), 'Center front collar depth');
        $angle = $p->angle(8,12);
        $p->addPoint(  8, $p->shift( 8,$angle,-20, 'Collar width @ top of garment'));
        $p->addPoint( 20, $p->shift(20,$angle,-20, 'Collar width @ top of garment'));
        // Making garment longer
        $p->addPoint(  4, $p->shift( 4,-90,60), 'Center back at front bottom');
        $p->addPoint(  6, $p->shift( 6,-90,60), 'Quarter chest at front bottom');
     
        // Adding points from index 100 onwards
        $p->newPoint( 21, $p->x(21), $p->y(1),'Control point for 1'); // Re-using point 21
        $p->addPoint( 100, $p->shiftAlong(8,20,21,1, $p->curveLen(8,20,21,1)/2), 'Raglan back tip');
        $p->addSplitCurve(5,8,20,21,1,100);
     
        // Paths
        $path = 'M 1 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 100 100 C 57 56 1 z';
        $p->newPath('seamline', $path);

        /**
         * Uncomment paths below if you want to hack this pattern
         * they will help you understand things
         */
        // $backBlock = 'M 1 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 1 z';
        // $p->newPath('backBlock', $backBlock,['class' => 'helpline']);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        
        // Grid anchor
        $p->clonePoint(4, 'gridAnchor');
    }
    
    /**
     * Drafts the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSleeve($model)
    {
        $this->clonePoints('sleeveBlock', 'sleeve'); 
        $p = $this->parts['sleeve'];
        
        // Importing cut off shoulder part from front
        $front = $this->parts['front'];
        $load = [12,8,52,53,20,100,15,14,18,10,17,19];
        foreach($load as $i) $p->addPoint( 100+$i, $front->loadPoint($i), $front->points[$i]->getDescription());
        // Shift in place
        $angle = $p->angle(1,112);
        $distance = $p->distance(1,112);
        foreach($load as $i) $p->addPoint( 100+$i, $p->shift(100+$i,$angle,$distance));
        // Rotate in place
        $angle = 90-$p->angle(108,1);
        foreach($load as $i) $p->addPoint( 100+$i, $p->rotate(100+$i,1,$angle));
        // Flip in place
        foreach($load as $i) $p->addPoint( 100+$i, $p->flipX( 100+$i, $p->x(1)));
        
        // Importing cut off shoulder part from back
        $back = $this->parts['back'];
        $load = [12,8,52,53,20,100,15,14,18,10,17,19];
        foreach($load as $i) $p->addPoint( 200+$i, $back->loadPoint($i), $back->points[$i]->getDescription());
        // Shift in place
        $angle = $p->angle(1,212);
        $distance = $p->distance(1,212);
        foreach($load as $i) $p->addPoint( 200+$i, $p->shift(200+$i,$angle,$distance));
        // Rotate in place
        $angle = 90-$p->angle(208,1);
        foreach($load as $i) $p->addPoint( 200+$i, $p->rotate(200+$i,1,$angle));
        
        // Raglan seam lengths
        $this->frontRaglan = $front->curveLen(100,100,15,14) + $front->curveLen(14,14,13,5);
        $this->backRaglan =  $back->curveLen( 100,100,15,14) + $back->curveLen(14,14,13,5);
        $this->sleeveFrontRaglan = $p->distance(200,114);
        $this->sleeveBackRaglan = $p->distance(300,214);


        // Sleevecap front
        $p->addPoint( 130, $p->shiftTowards(200,114,$this->frontRaglan));
        $p->addPoint( 131, $p->shiftTowards(130,5,$p->distance(130,5)/2));
        $p->addPoint( 132, $p->shiftTowards(200,131,$this->frontRaglan));
        $p->addPoint( 133, $p->shift(132,0,$this->frontRaglan*0.1));
        $p->addPoint( 134, $p->shiftTowards(200,114,$this->frontRaglan*0.5));
        $delta = $p->curveLen(200,134,132,133) - $this->frontRaglan;
        $i=1;
        while(abs($delta) > 1) {
            if($delta>0) $angle = 90; // Move up
            else $angle = -90; // Move down
            $p->newPoint( "132-$i", $p->x(132), $p->y(132));
            $p->addPoint( 132, $p->shift(132,$angle,$delta));
            $p->newPoint( 133, $p->x(133),$p->y(132));
            $delta = $p->curveLen(200,134,132,133) - $this->frontRaglan;
            $this->dbg("Iteration $i, front sleevecap delta is $delta");
            $i++;
            if($i>40)break; // Not good!
        }

        // Sleevecap back
        $p->addPoint( 230, $p->shiftTowards(300,214,$this->backRaglan));
        $p->addPoint( 231, $p->shiftTowards(230,-5,$p->distance(230,-5)/2));
        $p->addPoint( 232, $p->shiftTowards(300,231,$this->backRaglan));
        $p->addPoint( 233, $p->shift(232,180,$this->backRaglan*0.1));
        $p->addPoint( 234, $p->shiftTowards(300,214,$this->backRaglan*0.5));
        $p->newPoint( 232, $p->x(232), $p->y(132));
        $p->newPoint( 233, $p->x(233),$p->y(232));

        // Adjust sleeve length
        $delta = $p->deltaY(5,133);
        $p->addPoint( 32, $p->shift(32,-90,$delta+50));
        $p->addPoint( 32, $p->shift(32,0,30)); //Make sleeve wider
        $p->addPoint( 31, $p->shift(31,-90,$delta+50));
        $p->addPoint( 31, $p->shift(31,180,30)); //Make sleeve wider

        // Paths
        $path = 'M 31 L 233 C 232 234 300 C 253 252 208 C 152 153 200 C 134 132 133 L 32 z';
        $p->newPath('seamline', $path);

        /**
         * Uncomment paths below if you want to hack this pattern
         * they will help you understand things
         */
        /*
        $sleeveBlockPath = 'M 31 L -5 C -5 20 16 C 21 10 10 C 10 22 17 C 23 28 30 C 29 25 18 C 24 11 11 C 11 27 19 C 26 5 5 L 32 z';
        $p->newPath('sleeveBlockPath', $sleeveBlockPath, ['class' => 'helpline']);
        $frontShoulder = 'M 112 L 108 C 152 153 200 C 200 115 114 C 115 118 110 C 117 119 112 z';
        $p->newPath('frontShoulder', $frontShoulder, ['class' => 'helpline']);
        $backShoulder = 'M 212 L 208 C 252 253 300 C 300 215 214 C 215 218 210 C 217 219 212 z';
        $p->newPath('backShoulder', $backShoulder, ['class' => 'helpline']);
        $help = 'M 200 L 130 L 230 M 200 C 134 132 133 M 300 C 234 232 233';
        $p->newPath('help', $help, ['class' => 'helpline']);
         */
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }
    
    /**
     * Drafts the pocket
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocket($model)
    {
        $this->clonePoints('front', 'pocket'); 
        $p = $this->parts['pocket'];
        
        $pocket = 'M 105 L 103 C 104 102 102 L 101 L 4 z';
        $p->newPath('seamline', $pocket);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }
    
    /**
     * Drafts the pocket facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocketFacing($model)
    {
        $this->clonePoints('front', 'pocketFacing'); 
        $p = $this->parts['pocketFacing'];

        $p->addPoint( 110, $p->shiftTowards(102,101,25)); 
        $p->addPoint( 111, $p->shift(103,180,25)); 
        $p->addPoint( 112, $p->shift(104,180,25));
        $p->addPoint( 112, $p->shift(112,-90,$p->deltaY(102,110)));
        $pocket = 'M 111 C 112 110 110 L 102 C 102 104 103 z';
        $p->newPath('seamline', $pocket);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        
        // Grid anchor
        $p->clonePoint(103, 'gridAnchor');
    }
    
    /**
     * Drafts the hood side
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftHoodSide($model)
    {
        $front = $this->parts['front'];
        $back = $this->parts['back'];
        $sleeve = $this->parts['sleeve'];
        $p = $this->parts['hoodSide'];

        // Some measures we'll need
        $backNeckLen  = ( $back->curveLen( 1,56,57,100) + $sleeve->curveLen(108,252,253,300) );
        $frontNeckLen = ( $front->curveLen(9,56,57,100) + $sleeve->curveLen(108,153,152,200) );
        $hw = $backNeckLen + $frontNeckLen; // Hood width
        $this->neckBindingLen = $hw*2;
        $hh = $front->y(9) - $back->y(1);  // Hood height
        $ho = $model->m('headCircumference') * 1.35; // Hood opening
        $this->dbg(' head circ is '.$model->m('headCircumference'));
        $this->dbg(" backNeckLen  =  $backNeckLen  frontNeckLen = $frontNeckLen hh is $hh and ho is $ho");
        $p->newPoint(  1, 0, 0);
        $p->newPoint(  2, $hw, $p->y(1));
        $p->newPoint(  3, $p->x(1), ($ho-30)/-2, 'Center back neck point');
        $p->newPoint(  4, $p->x(2), $p->y(3));
        $p->newPoint(  5, $p->x(1), $hh);
        $p->newPoint(  6, $p->x(2)+50, $p->y(5));
        $p->addPoint(  7, $p->shift(1,0,$hw/2));
        $p->newPoint(  8, $p->x(7),$p->y(6));
        $p->addPoint( 11, $p->shift(1,0,30)); // 30 = half of mid panel
        $p->addPoint(  9, $p->shiftAlong(11,7,8,6,$hw-30));
        $p->addPoint( 10, $p->shiftAlong(11,7,8,6,$backNeckLen-30),'Shoulder notch');
        $p->newPoint( 12, $p->x(10),$p->y(3)+30,'Crown point');
        $p->newPoint( 13, $p->x(6), $p->y(4)+105);
        $p->addPoint( 14, $p->shift(12,0,($p->x(13)-$p->x(12))/2));
        $p->newPoint( 15, $p->x(1)-50, $p->y(12));
        $p->addPoint( 16, $p->rotate(12,13,80));
        $p->addPoint( 17, $p->shift(2,180,25));
        $p->addPoint( 18, $p->shift(17,90,50));
        $p->addPoint( 19, $p->shift(17,-90,20));
        $p->addPoint( 20, $p->shift(16,180,25));
        $p->addPoint( 21, $p->shift(6,90,25));
        $p->addPoint( 23, $p->shiftTowards(17,20,25));
        $p->addPoint( 22, $p->rotate(23,17,180));
        $p->addPoint( 25, $p->shift(6,90,25));
        
        // Paths 
        $path = 'M 11 C 11 15 12 C 14 13 13 C 20 23 17 C 22 25 6 C 8 7 11 z';
        $p->newPath('seamline', $path);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        
        // Grid anchor
        $p->clonePoint(6, 'gridAnchor');
    }
    
    /**
     * Drafts the hood center
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftHoodCenter($model)
    {
        $side = $this->parts['hoodSide'];
        $len = $side->curveLen(11,11,15,12) + $side->curveLen(12,14,13,13);
        
        $this->draftRectangle(160, $len, $this->parts['hoodCenter']);
    }
    
    /**
     * Drafts the waistband
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftWaistband($model)
    {
        $front = $this->parts['front'];
        $len = $front->deltaX(4,6) * 4 * $this->o('ribbingStretchFactor');
        $this->draftRectangle(160, $len, $this->parts['waistband']);
    }
        
    /**
     * Drafts the cuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftCuff($model)
    {
        $sleeve = $this->parts['sleeve'];
        $len = $sleeve->deltaX(31,32) * $this->o('ribbingStretchFactor');

        $this->draftRectangle(160, $len, $this->parts['cuff']);
    }

    /**
     * Drafts the neckBinding
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftNeckBinding($model)
    {
        // $this->neckBindingLen is sit in draftHoodSide
        $this->draftRectangle(50, $this->neckBindingLen, $this->parts['neckBinding']);
    }

    /**
     * Drafts a rectangle
     *
     * @param float $w The width of the rectangle
     * @param float $h The height of the rectangle
     * @param \Freesewing\Part $p The part to add the rectangle to
     *
     * @return void
     */
    public function draftRectangle($w, $h, $p)
    {
        $p->newPoint( 1,  0,0);
        $p->newPoint( 2, $w,0);
        $p->newPoint( 3, $w,$h);
        $p->newPoint( 4,  0,$h);

        // Paths 
        $path = 'M 1 L 2 L 3 L 4 z';
        $p->newPath('seamline', $path);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }
        

    /**
     * Finalizes the front
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeFront($model, $p)
    {
        // Grainline
        $p->newPoint('grainlineTop', $p->x(9)+40, $p->y(9)+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4)-20);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M grainlineBottom L grainlineTop', $this->t('Grainline'), ['line-height' => 12, 'class' => 'text-lg fill-fabric text-center', 'dy' => -2]);

        // Cut on fold (cof)
        $p->newPoint('cofStart', $p->x(9), $p->y(9)+40);
        $p->newPoint('cofTop', $p->x(9)+20, $p->y('cofStart'));
        $p->newPoint('cofEnd', $p->x(9), $p->y(4)-40);
        $p->newPoint('cofBottom', $p->x(9)+20, $p->y('cofEnd'));
        $p->newPath('cutOnFold','M cofStart L cofTop L cofBottom L cofEnd',['class' => 'grainline']);
        $p->newTextOnPath('cutonfold', 'M cofBottom L cofTop', $this->t('Cut on fold'), ['line-height' => 12, 'class' => 'text-lg fill-fabric text-center', 'dy' => -2]);

        // Sleeve notch
        $this->frontNotchLen = $p->curveLen(100,100,15,14)/2;
        $p->addPoint('sleeveNotch', $p->shiftAlong(100,100,15,14, $this->frontNotchLen),'Front sleeve notch');
        $p->newSnippet('sleeveNotch', 'notch', 'sleeveNotch');

        // Mark pocket in helpline
        $p->newPath('pocket','M 101 L 102 C 102 104 103 L 105',['class' => 'helpline']);
        $p->newSnippet('pocketNotch2', 'notch', 102);
        $p->newSnippet('pocketNotch3', 'notch', 103);

        
        // Title
        $p->newPoint('titleAnchor', $p->x(5)/2, $p->y(2)+$p->deltaY(2,3)/2);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from main fabric')."\n".$this->t('Cut on fold'));
        
        // Seam allowance
        $sa = 'M 4 L 6 L 5 C 13 16 14 C 15 100 100 C 57 56 9';
        $p->offsetPathString('sa',$sa,10, true, ['class' => 'seam-allowance']);
        // Add bits at the fold
        $p->paths['sa']->setPath('M 4 L sa-line-4TO6 M 9 L sa-curve-9TO100 '.$p->paths['sa']->getPath());
        
        // Paperless
        if ($this->isPaperless) {

            // Measures
            $pAttr = ['class' => 'measure-lg'];
            $hAttr = ['class' => 'stroke-note stroke-sm'];

            $key = 'frontHeight';
            $path = 'M 4 L 9';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(4, 9)), $this->textAttr(-17));
            
            $key = 'neckHeight';
            $path = 'M 9 L 200';
            $p->newPoint(200, 0, $p->y(100));
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(9, 100)), $this->textAttr(-17));

            $key = 'neckWidth';
            $path = 'M 9 L 201';
            $p->newPoint(201, $p->x(100), $p->y(9));
            $p->offsetPathString($key, $path, 15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaX(9, 100)),  $this->textAttr(13));

            $key = 'neckCurve';
            $path = 'M 9 C 56 57 100';
            $p->offsetPathString($key, $path, -20, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(9,56,57,100)),  $this->textAttr(-23));

            $key = 'notchDistance';
            $path = 'M 100 L sleeveNotch';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(100, 'sleeveNotch')), $this->textAttr(-17));
            
            $key = 'width';
            $path = 'M 2 L 5';
            $p->offsetPathString($key, $path, 25, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaX(2, 5)),  $this->textAttr(23));
            
            $key = 'sideLen';
            $path = 'M 6 L 5';
            $p->offsetPathString($key, $path, 25, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaY(5, 6)),  $this->textAttr(23));
            
            $key = 'pocket1';
            $path = 'M 105 L 103';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaX(105, 103)),  $this->textAttr(-7));
            
            $key = 'pocket2';
            $path = 'M 202 L 102';
            $p->newPoint(202, $p->x(102), $p->y(4));
            $p->offsetPathString($key, $path, 15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaY(102, 202)), $this->textAttr(13));
            
            $key = 'pocket3';
            $path = 'M 101 L 103';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaY(103, 101)),  $this->textAttr(-17));
            
            $key = 'pocket4';
            $path = 'M 203 L 102';
            $p->newPoint(203, $p->x(4), $p->y(102));
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaX(203, 102)),  $this->textAttr(-17));
            
            $measureHelpLines = 'M 100 L neckHeight-line-200TO9 '.
                'M 9 L frontHeight-line-9TO4 '.
                'M 4 L frontHeight-line-4TO9 '.
                'M 100 L neckWidth-line-201TO9 '.
                'M 9 L neckCurve-curve-9TO100 '.
                'M 100 L  neckCurve-curve-100TO9 '.
                'M 100 L notchDistance-line-100TOsleeveNotch '.
                'M sleeveNotch L notchDistance-line-sleeveNotchTO100 '.
                'M 5 L sideLen-line-5TO6 '.
                'M 6 L sideLen-line-6TO5 '.
                'M 102 L pocket2-line-102TO202 '.
                'M 102 L pocket4-line-102TO203';
            $p->newPath($p->newId(),$measureHelpLines, $hAttr);

            // Draw an extra path to prevent the measure label getting cut off
            $p->addPoint(204, $p->shift(2,180,25));
            $p->newPath($p->newId(),'M 2 L 204', ['class' => 'hidden']);
            
            // Notes
            $noteAttr = ['line-height' => 7, 'class' => 'text-lg'];
            $p->newPoint('saNote', $p->x(5), $p->y(103), 'sa note anchor');
            $p->newNote('saNote', 'saNote', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 10, -5, $noteAttr);
        }
    }
    
    /**
     * Finalizes the back
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeBack($model, $p)
    {
        // Grainline
        $p->newPoint('grainlineTop', $p->x(1)+40, $p->y(1)+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4)-20);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M grainlineBottom L grainlineTop', $this->t('Grainline'), ['line-height' => 12, 'class' => 'text-lg fill-fabric text-center', 'dy' => -2]);
        
        // Cut on fold (cof)
        $p->newPoint('cofStart', $p->x(1), $p->y(1)+40);
        $p->newPoint('cofTop', $p->x(1)+20, $p->y('cofStart'));
        $p->newPoint('cofEnd', $p->x(1), $p->y(4)-40);
        $p->newPoint('cofBottom', $p->x(1)+20, $p->y('cofEnd'));
        $p->newPath('cutOnFold','M cofStart L cofTop L cofBottom L cofEnd',['class' => 'grainline']);
        $p->newTextOnPath('cutonfold', 'M cofBottom L cofTop', $this->t('Cut on fold'), ['line-height' => 12, 'class' => 'text-lg fill-fabric text-center', 'dy' => -2]);
        
        // Title
        $p->newPoint('titleAnchor', $p->x(5)/2, $p->y(2)+$p->deltaY(2,3)/2);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('from main fabric')."\n".$this->t('Cut on fold'));
        
        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 30));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        
        // Logo
        $p->addPoint('logoAnchor', $p->shift('scaleboxAnchor',-90,90));
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        $p->newSnippet('cc', 'cc', 'logoAnchor');

        // Seam allowance
        $sa = 'M 4 L 6 L 5 C 13 16 14 C 15 100 100 C 57 56 1';
        $p->offsetPathString('sa',$sa,10, true, ['class' => 'seam-allowance']);
        // Add bits at the fold
        $p->paths['sa']->setPath('M 4 L sa-line-4TO6 M 1 L sa-curve-1TO100 '.$p->paths['sa']->getPath());
        
        // Sleeve notch
        $this->backNotchLen = $p->curveLen(100,100,15,14)/2;
        $p->addPoint('sleeveNotch', $p->shiftAlong(100,100,15,14, $this->backNotchLen),'Back sleeve notch');
        $p->addPoint('sleeveNotcha', $p->shiftAlong(100,100,15,14, $this->backNotchLen + 2.5),'Back sleeve notch a');
        $p->addPoint('sleeveNotchb', $p->shiftAlong(100,100,15,14, $this->backNotchLen - 2.5),'Back sleeve notch b');
        $p->newSnippet('sleeveNotcha', 'notch', 'sleeveNotcha');
        $p->newSnippet('sleeveNotchb', 'notch', 'sleeveNotchb');
        
        // Paperless
        if ($this->isPaperless) {

            // Measures
            $pAttr = ['class' => 'measure-lg'];
            $hAttr = ['class' => 'stroke-note stroke-sm'];

            $key = 'frontHeight1';
            $path = 'M 4 L 1';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(4, 1)), $this->textAttr(-17));

            $key = 'frontHeight2';
            $path = 'M 4 L 200';
            $p->newPoint(200, 0, $p->y(100));
            $p->offsetPathString($key, $path, -30, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(4, 200)), $this->textAttr(-32));

            $key = 'neckWidth';
            $path = 'M 200 L 100';
            $p->offsetPathString($key, $path, 15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(200, 100)), $this->textAttr(13));

            $key = 'notchLen';
            $path = 'M 100 L sleeveNotch';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(100, 'sleeveNotch')), $this->textAttr(-17));

            $key = 'width';
            $path = 'M 2 L 5';
            $p->offsetPathString($key, $path, 25, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaX(2, 5)),  $this->textAttr(23));
            
            $key = 'sideLen';
            $path = 'M 6 L 5';
            $p->offsetPathString($key, $path, 25, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->deltaY(5, 6)),  $this->textAttr(23));
            


            $measureHelpLines = 'M 1 L frontHeight1-line-1TO4 '.
                'M 100 L frontHeight2-line-200TO4 '.
                'M 100 L neckWidth-line-100TO200 '.
                'M 100 L notchLen-line-100TOsleeveNotch '.
                'M sleeveNotch L notchLen-line-sleeveNotchTO100 '.
                'M 5 L sideLen-line-5TO6 '.
                'M 6 L sideLen-line-6TO5 '.
                '';
            $p->newPath($p->newId(),$measureHelpLines, $hAttr);
        
            // Draw an extra path to prevent the measure label getting cut off
            $p->addPoint(204, $p->shift(2,180,40));
            $p->newPath($p->newId(),'M 2 L 204', ['class' => 'hidden']);
            
            // Notes
            $noteAttr = ['line-height' => 7, 'class' => 'text-lg'];
            $p->newPoint('saNote', $p->x(5), $p->y(3), 'sa note anchor');
            $p->newNote('saNote', 'saNote', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 10, -5, $noteAttr);
        }
    }

    /**
     * Finalizes the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeSleeve($model, $p)
    {
        // Grainline
        $p->newPoint('grainlineTop', $p->x(108), $p->y(108)+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(32)-20);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        
        // Title
        $p->clonePoint(8, 'titleAnchor');
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('Cut with good sides together'));
        
        // Seam allowance
        $p->offsetPath('sa','seamline',-10, true, ['class' => 'seam-allowance']);
        
        // Sleeve notches
        $p->addPoint('frontSleeveNotch', $p->shiftAlong(200,134,132,133, $this->frontNotchLen),'Front sleeve notch');
        $p->addPoint('backSleeveNotcha', $p->shiftAlong(300,234,232,233, $this->backNotchLen -2.5),'Back sleeve notch a');
        $p->addPoint('backSleeveNotchb', $p->shiftAlong(300,234,232,233, $this->backNotchLen +2.5),'Back sleeve notch b');
        $p->newSnippet('sleeveNotch', 'notch', 'frontSleeveNotch');
        $p->newSnippet('sleeveNotcha', 'notch', 'backSleeveNotcha');
        $p->newSnippet('sleeveNotchb', 'notch', 'backSleeveNotchb');
        
        // Paperless
        if ($this->isPaperless) {

            // Measures
            $pAttr = ['class' => 'measure-lg'];
            $hAttr = ['class' => 'stroke-note stroke-sm'];

            $key = 'cuffWidthBack';
            $path = 'M 400 L 32';
            $p->newPoint(400, 0, $p->y(31));
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(400, 32)), $this->textAttr(-17));
            
            $key = 'cuffWidthFront';
            $path = 'M 31 L 400';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(31, 400)), $this->textAttr(-17));
            
            $key = 'widthFront';
            $path = 'M 233 L 401';
            $p->newPoint(401, 0, $p->y(233));
            $p->offsetPathString($key, $path, 15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(233, 401)), $this->textAttr(13));
            
            $key = 'widthBack';
            $path = 'M 401 L 133';
            $p->offsetPathString($key, $path, 15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(233, 401)), $this->textAttr(13));
            
            $key = 'widthBackNotch';
            $path = 'M backSleeveNotcha L 402';
            $p->newPoint(402, 0, $p->y('backSleeveNotcha'));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(402, 'backSleeveNotcha')), $this->textAttr(-2));
            
            $key = 'widthFrontNotch';
            $path = 'M 403 L frontSleeveNotch';
            $p->newPoint(403, 0, $p->y('frontSleeveNotch'));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance('frontSleeveNotch', 403)), $this->textAttr(-2));
            
            $key = 'frontNotchHeight';
            $path = 'M 404 L frontSleeveNotch';
            $p->newPoint(404, $p->x('frontSleeveNotch'), $p->y(233));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance('frontSleeveNotch', 404)), $this->textAttr(-2));
            
            $key = 'backNotchHeight';
            $path = 'M 405 L backSleeveNotcha';
            $p->newPoint(405, $p->x('backSleeveNotcha'), $p->y(233));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance('backSleeveNotcha', 405)), $this->textAttr(-2));
            
            $key = 'backHeight';
            $path = 'M 406 L 300';
            $p->newPoint(406, $p->x(300), $p->y(233));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(300, 406)), $this->textAttr(-2));
            
            $key = 'frontHeight';
            $path = 'M 407 L 200';
            $p->newPoint(407, $p->x(200), $p->y(233));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(200, 407)), $this->textAttr(-2));
            
            $key = 'frontOpening';
            $path = 'M 401 L 407';
            $p->offsetPathString($key, $path, $p->deltaY(401,208)+15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(401, 407)), $this->textAttr($p->deltaY(401,208)+13));
            
            $key = 'backOpening';
            $path = 'M 406 L 401';
            $p->offsetPathString($key, $path, $p->deltaY(401,208)+15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(406, 401)), $this->textAttr($p->deltaY(401,208)+13));
            
            $key = 'neckCurve';
            $path = 'M 300 C 253 252 208 C 152 153 200';
            $p->offsetPathString($key, $path, -3, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(300, 253, 252, 208)+$p->curveLen(208, 152, 153, 200)), $this->textAttr(-4));

            $key = 'length';
            $path = 'M 408 L 406'; 
            $p->newPoint(408, $p->x(406), $p->y(31));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(406, 408)), $this->textAttr(-3));
            
            $measureHelpLines = 'M 31 L cuffWidthFront-line-31TO400 '.
                'M 400 L cuffWidthFront-line-400TO31 '.
                'M 32 L cuffWidthBack-line-32TO400 '.
                'M 233 L widthFront-line-233TO401 '.
                'M 133 L widthBack-line-133TO401 '.
                'M 108 L backOpening-line-401TO406 '.
                'M 300 L neckCurve-curve-300TO208 '. 
                'M 200 L neckCurve-curve-200TO208 '.
                '';
            $p->newPath($p->newId(),$measureHelpLines, $hAttr);
            $p->newPath($p->newId(),'M 233 L 133', ['class' => 'stroke-note stroke-sm']);

            // Notes
            $noteAttr = ['line-height' => 7, 'class' => 'text-lg'];
            $p->addPoint('saNote', $p->beamsCross(133,32,34,35), 'sa note anchor');
            $p->newNote('saNote', 'saNote', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 10, -5, $noteAttr);
        }
    }
    
    /**
     * Finalizes the pocket
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizePocket($model, $p)
    {
        // Grainline
        $p->newPoint('grainlineTop', $p->x(105)+35, $p->y(105)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4)-10);
        $p->newPath('grainline','M grainlineBottom L grainlineTop',['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M grainlineBottom L grainlineTop', $this->t('Grainline'), ['class' => 'text-lg fill-fabric text-center', 'dy' => -2]);
        
        // Cut on fold (cof)
        $p->newPoint('cofStart', $p->x(105), $p->y(105)+10);
        $p->newPoint('cofTop', $p->x(105)+20, $p->y('cofStart'));
        $p->newPoint('cofEnd', $p->x(105), $p->y(4)-10);
        $p->newPoint('cofBottom', $p->x(105)+20, $p->y('cofEnd'));
        $p->newPath('cutOnFold','M cofStart L cofTop L cofBottom L cofEnd',['class' => 'grainline']);
        $p->newTextOnPath('cutonfold', 'M cofBottom L cofTop', $this->t('Cut on fold'), ['line-height' => 12, 'class' => 'text-lg fill-fabric text-center', 'dy' => -2]);

        // Title
        $p->newPoint('titleAnchor', $p->x('grainlineBottom') + $p->deltaX('grainlineBottom',101)/2, $p->y(105)+$p->deltaY(105,4)/2);
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '1x '.$this->t('from main fabric')."\n".$this->t('Cut on  fold'));
        
        // Seam allowance
        $sa = 'M 105 L 103 C 104 102 102 L 101 L 4';
        $p->offsetPathString('sa',$sa,-10, true, ['class' => 'seam-allowance']);
        $p->paths['sa']->setPath('M 4 L sa-line-4TO101 M 105 L sa-line-105TO103 '.$p->paths['sa']->getPath());
        
        // Paperless
        if ($this->isPaperless) {

            // Measures
            $pAttr = ['class' => 'measure-lg'];
            $hAttr = ['class' => 'stroke-note stroke-sm'];

            $key = 'height1';
            $path = 'M 201 L 200';
            $p->newPoint(200, $p->x('grainlineTop') + 20, $p->y(105));
            $p->newPoint(201, $p->x(200), $p->y(4));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(200, 201)), $this->textAttr(-2));

            $key = 'width1';
            $path = 'M 4 L 101';
            $p->offsetPathString($key,$path,20, true, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(4, 101)), $this->textAttr(18));
            
            $key = 'width2';
            $path = 'M 4 L 202';
            $p->newPoint(202, $p->x(102), $p->y(4));
            $p->offsetPathString($key,$path, 32, true, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(4, 202)), $this->textAttr(30));
            
            $key = 'height2';
            $path = 'M 204 L 203';
            $p->newPoint(203, $p->x(103) - 10, $p->y(102));
            $p->newPoint(204, $p->x(203), $p->y(4));
            $p->newPath($key, $path, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(203, 204)), $this->textAttr(-2));

            $measureHelpLines = 'M 101 L width1-line-101TO4 '.
                'M 102 L width2-line-202TO4 '.
                'M 4 L width2-line-4TO202 '.
                'M 203 L 102';
            $p->newPath($p->newId(),$measureHelpLines, $hAttr);
        }
    }

    
    /**
     * Finalizes the pocket facing
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizePocketFacing($model, $p)
    {
        // Grainline
        $p->addPoint('grainlineTop', $p->shift(103,-115,15));
        $p->addPoint('grainlineBottom', $p->shift(104,-115,15));
        $p->newPath('grainline','M grainlineBottom L grainlineTop',['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M grainlineBottom L grainlineTop', $this->t('Grainline'), ['class' => 'text fill-fabric text-center', 'dy' => -1]);

        // Title
        $p->newPoint('titleAnchor', $p->x(111) + $p->deltaX(111,103)/2, $p->y(111)+35);
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('Cut with good sides together'), 'vertical');
        
        // Seam allowance
        $p->offsetPath('sa', 'seamline', 10, true, ['class' => 'seam-allowance']);
        
        // Paperless
        if ($this->isPaperless) {

            // Measures
            $pAttr = ['class' => 'measure-lg'];
            $hAttr = ['class' => 'stroke-note stroke-sm'];

            $key = 'width';
            $path = 'M 111 L 103';
            $p->offsetPathString($key, $path, -15, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(111, 103)), $this->textAttr(-17));
            
            $measureHelpLines = 'M 111 L width-line-111TO103 '.
                'M 103 L width-line-103TO111 '.
                '';
            $p->newPath($p->newId(),$measureHelpLines, $hAttr);
            
            // Add some space so measure text is not cut off
            $p->addPoint(200, $p->shift(111,90,23));
            $p->newPath($p->newId(),'M 111 L 200', ['class' => 'hidden']);
            
            // Notes
            $p->newNote('saNote', 110, $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 12, 10, -5, ['line-height' => 4, 'class' => 'text-sm', 'dy' => -10]);
        }
    }
    
    /**
     * Finalizes the hood side
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeHoodSide($model, $p)
    {
        // Grainline
        $p->addPoint( 'grainlineTop', $p->shift(12,-90,5));
        $p->addPoint( 'grainlineBottom', $p->shift(10,90,5));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M grainlineBottom L grainlineTop', $this->t('Grainline'), ['class' => 'text-lg fill-fabric text-center', 'dy' => -2]);
        
        // Title
        $p->addPoint('titleAnchor', $p->shift(12,-70,100));
        $p->addTitle('titleAnchor', 6, $this->t($p->title), '4x '.$this->t('from main fabric')." (2x2)\n".$this->t('Cut with good sides together'));
        
        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);

        // Notch
        $p->newSnippet('sleeveNotch', 'notch', 10);

        // Paperless
        if ($this->isPaperless) {

            // Measures
            $pAttr = ['class' => 'measure-lg'];
            $hAttr = ['class' => 'stroke-note stroke-sm'];

            // Edge of curves
            $p->addPoint(201, $p->curveEdgeLeft(12,15,11,11));
            $p->addPoint(202, $p->curveEdgeLeft(13,20,23,17));

            // Bring points down
            $p->newPoint(203, $p->x(201), $p->y(6));
            $p->newPoint(204, $p->x(202), $p->y(6));
            $p->newPoint(205, $p->x(10), $p->y(6));
            $p->newPoint(206, $p->x(11), $p->y(6));

            // Bring points to the right
            $p->newPoint(207, $p->x(6), $p->y(10));
            $p->newPoint(208, $p->x(6), $p->y(11));
            $p->newPoint(209, $p->x(6), $p->y(202));
            $p->newPoint(210, $p->x(6), $p->y(201));
            $p->newPoint(211, $p->x(6), $p->y(12));
            

            $key = 'width1';
            $path = 'M 204 L 6';
            $p->offsetPathString($key, $path, 20, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(204, 6)), $this->textAttr(18));

            $key = 'width2';
            $path = 'M 205 L 6';
            $p->offsetPathString($key, $path, 32, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(205, 6)), $this->textAttr(30));

            $key = 'width3';
            $path = 'M 206 L 6';
            $p->offsetPathString($key, $path, 44, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(206, 6)), $this->textAttr(42));

            $key = 'width4';
            $path = 'M 203 L 6';
            $p->offsetPathString($key, $path, 56, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(203, 6)), $this->textAttr(54));

            $key = 'height1';
            $path = 'M 6 L 207';
            $p->offsetPathString($key, $path, 20, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(6, 207)), $this->textAttr(18));

            $key = 'height2';
            $path = 'M 6 L 208';
            $p->offsetPathString($key, $path, 32, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(6, 208)), $this->textAttr(30));

            $key = 'height3';
            $path = 'M 6 L 209';
            $p->offsetPathString($key, $path, 44, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(6, 209)), $this->textAttr(42));

            $key = 'height4';
            $path = 'M 6 L 210';
            $p->offsetPathString($key, $path, 56, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(6, 210)), $this->textAttr(54));

            $key = 'height5';
            $key = 'height5';
            $path = 'M 6 L 13';
            $p->offsetPathString($key, $path, 68, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(6, 13)), $this->textAttr(66));

            $key = 'height6';
            $path = 'M 6 L 211';
            $p->offsetPathString($key, $path, 80, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->unit($p->distance(6, 211)), $this->textAttr(78));

            $key = 'hoodlen';
            $path = 'M 11 C 11 15 12 C 14 13 13';
            $p->offsetPathString($key, $path, -20, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(13,13,14,12)+$p->curveLen(12,15,11,11)), $this->textAttr(-22));

            $key = 'necklen';
            $path = 'M 11 C 7 8 6';
            $p->offsetPathString($key, $path, -5, 1, $pAttr);
            $p->newTextOnPath($key, $path, $this->t('Curve length').': '.$this->unit($p->curveLen(11,7,8,6)), $this->textAttr(-8));


            $measureHelpLines = 'M 6 L width4-line-6TO203 '.
                'M 201 L  width4-line-203TO6 '.
                'M 11 L width3-line-206TO6 '.
                'M 10 L width2-line-205TO6 '.
                'M 202 L width1-line-204TO6 '.
                'M 6 L height6-line-6TO211 '.
                'M 10 L height1-line-207TO6 '.
                'M 11 L height2-line-208TO6 '.
                'M 202 L height3-line-209TO6 '.
                'M 201 L height4-line-210TO6 '.
                'M 13 L height5-line-13TO6 '.
                'M 12 L height6-line-211TO6 '.
                'M 11 L hoodlen-curve-11TO12 '.
                'M 13 L hoodlen-curve-13TO12 '.
                '';
            $p->newPath($p->newId(),$measureHelpLines, $hAttr);
            
           /* 
            // Add some space so measure text is not cut off
            $p->addPoint(200, $p->shift(111,90,23));
            $p->newPath($p->newId(),'M 111 L 200', ['class' => 'hidden']);
            
            // Notes
            $p->newNote('saNote', 110, $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 12, 10, -5, ['line-height' => 4, 'class' => 'text-sm', 'dy' => -10]);
            */
        }
    }
    
    /**
     * Finalizes the hood center
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeHoodCenter($model, $p)
    {
        // Grainline
        $p->newPoint( 'grainlineTop', $p->x(1)+10, $p->y(3)/2+30);
        $p->newPoint( 'grainlineBottom', $p->x(2)-10, $p->y('grainlineTop'));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M grainlineBottom L grainlineTop', $this->t('Grainline'), ['class' => 'text-lg fill-fabric text-center', 'dy' => -1]);
        
        // Title
        $p->newPoint('titleAnchor', $p->x(2)/2, $p->y(3)/2);
        $p->addTitle('titleAnchor', 7, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('Cut with good sides together'));
        
        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);
    }
    
    /**
     * Finalizes the cuffs
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeCuff($model, $p)
    {
        // Grainline
        $p->newPoint( 'grainlineTop', $p->x(1)+10, $p->y(3)/2+30);
        $p->newPoint( 'grainlineBottom', $p->x(2)-10, $p->y('grainlineTop'));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        
        // Title
        $p->newPoint('titleAnchor', $p->x(2)/2, $p->y(3)/2);
        $p->addTitle('titleAnchor', 8, $this->t($p->title), '2x '.$this->t('from ribbing')."\n".$this->t('Cut with good sides together'));
        
        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);
    }
    
    /**
     * Finalizes the waistband
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeWaistband($model, $p)
    {
        // Grainline
        $p->newPoint( 'grainlineTop', $p->x(1)+10, $p->y(3)/2+30);
        $p->newPoint( 'grainlineBottom', $p->x(2)-10, $p->y('grainlineTop'));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        
        // Title
        $p->newPoint('titleAnchor', $p->x(2)/2, $p->y(3)/2);
        $p->addTitle('titleAnchor', 9, $this->t($p->title), '1x '.$this->t('from ribbing'));
        
        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);
    }
    
    /**
     * Finalizes the neck binding
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeNeckBinding($model, $p)
    {
        // Grainline
        $p->newPoint( 'grainlineTop', $p->x(1)+10, $p->y(3)/2+140);
        $p->newPoint( 'grainlineBottom', $p->x(2)-10, $p->y('grainlineTop'));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        
        // Title
        $p->newPoint('titleAnchor', $p->x(2)/2, $p->y(3)/2);
        $p->addTitle('titleAnchor', 10, $this->t($p->title), '1x '.$this->t('from main fabric'), 'vertical');
        
        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);
    }

    private function textAttr($dy)
    {
        return ['class' => 'text-lg fill-note text-center', 'dy' => $dy];
    }
}
