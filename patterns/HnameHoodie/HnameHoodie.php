<?php
/** Freesewing\Patterns\JoostBodyBlock class */
namespace Freesewing\Patterns;

/**
 * The Hname Hoodie pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class HnameHoodie extends JoostBodyBlock
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
        $path = 'M 11 C 11 15 12 C 14 13 13 C 20 23 17 C 22 25 6 C 8 7 11';
        $p->newPath('seamline', $path);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
        
        // Grid anchor
        $p->clonePoint(11, 'gridAnchor');
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

        // Cut on fold (cof)
        $p->newPoint('cofStart', $p->x(9), $p->y(9)+40);
        $p->newPoint('cofTop', $p->x(9)+20, $p->y('cofStart'));
        $p->newPoint('cofEnd', $p->x(9), $p->y(4)-40);
        $p->newPoint('cofBottom', $p->x(9)+20, $p->y('cofEnd'));
        $p->newPath('cutOnFold','M cofStart L cofTop L cofBottom L cofEnd',['class' => 'stroke-lg stroke-note double-arrow']);
        $p->newTextOnPath('cutonfold', 'M cofBottom L cofTop', $this->t('Cut on fold'), ['line-height' => 12, 'class' => 'text-lg fill-note', 'dy' => -2]);

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
        
        // Logo
        $p->newSnippet('logo', 'logo', 'titleAnchor');
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
        
        // Title
        $p->newPoint('titleAnchor', $p->x(5)/2, $p->y(2)+$p->deltaY(2,3)/2);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('from main fabric')."\n".$this->t('Cut on fold'));
        
        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 30));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
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
        $p->newPoint('grainlineTop', $p->x(105)+40, $p->y(105)+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4)-20);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        
        // Title
        $p->newPoint('titleAnchor', $p->x('grainlineBottom') + $p->deltaX('grainlineBottom',101)/2, $p->y(105)+$p->deltaY(105,4)/2);
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '1x '.$this->t('from main fabric')."\n".$this->t('Cut on  fold'));
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
        // Title
        $p->newPoint('titleAnchor', $p->x(111) + $p->deltaX(111,103)/2, $p->y(111)+35);
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('Cut with good sides together'), 'vertical');
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
        
        // Title
        $p->addPoint('titleAnchor', $p->shift(12,-70,100));
        $p->addTitle('titleAnchor', 6, $this->t($p->title), '4x '.$this->t('from main fabric')." (2x2)\n".$this->t('Cut with good sides together'));
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
        
        // Title
        $p->newPoint('titleAnchor', $p->x(2)/2, $p->y(3)/2);
        $p->addTitle('titleAnchor', 7, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('Cut with good sides together'));
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
        $p->addTitle('titleAnchor', 9, $this->t($p->title), '1x '.$this->t('from main fabric'), 'vertical');
    }
}
