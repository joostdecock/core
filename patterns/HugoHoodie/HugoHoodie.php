<?php
/** Freesewing\Patterns\HugoHoodie class */
namespace Freesewing\Patterns;

/**
 * The Hugo Hoodie pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class HugoHoodie extends BrianBodyBlock
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */


    // Nothing to do, we call initialize() from the parent class


    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
    */

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
        $this->initialize($model);

        // Lower the armhole
        $this->setValue('armholeDepth', $this->getValue('armholeDepth') + 50);

        // Draft all parts
        foreach ($this->parts as $key => $part) {
            $this->{'draft'.ucfirst($key)}($model);
        }

        // Hide base blocks
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
        $this->parts['sleeveBlock']->setRender(false);
    }

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
        foreach ($this->parts as $key => $part) {
            if (!strpos($key, 'Block')) {
                $this->{'finalize'.ucfirst($key)}($model,$part);
            }
        }

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            foreach ($this->parts as $key => $part) {
                if (!strpos($key, 'Block')) {
                    $this->{'paperless'.ucfirst($key)}($model);
                }
            }
        }
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

        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Making neck opening wider and deeper
        $p->addPoint(  9, $p->shift( 9, -90, 10), 'Center front collar depth');
        $p->addPoint( 21, $p->shift(21, -90, 10), 'Control point for 9');
        $angle = $p->angle(8, 12);
        $p->addPoint(  8, $p->shift( 8, $angle, -20, 'Collar width @ top of garment'));
        $p->addPoint( 20, $p->shift(20, $angle, -20, 'Collar width @ top of garment'));
        // Making garment longer
        $p->addPoint(  4, $p->shift( 4, -90, 60), 'Center back at front bottom');
        $p->addPoint(  6, $p->shift( 6, -90, 60), 'Quarter chest at front bottom');

        // Adding points from index 100 onwards
        $p->addPoint( 100, $p->shiftAlong(8, 20, 21, 9, $p->curveLen(8, 20, 21, 9)/3), 'Raglan front tip');
        $p->addSplitCurve(8, 20, 21, 9, 100, 5);

        // Add pocket points
        $p->newPoint( 101, $p->x(6)*0.65 - 25, $p->y(6));
        $p->newPoint( 102, $p->x(6)*0.65, $p->y(6)-50);
        $p->newPoint( 103, $p->x(101), $p->y(4)-$p->x(101));
        $p->addPoint( 104, $p->shift(103, 90, $p->deltaY(102, 103)*0.75));
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

        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Making neck opening wider and deeper
        $p->addPoint(  1, $p->shift( 1, -90, 10), 'Center front collar depth');
        $angle = $p->angle(8, 12);
        $p->addPoint(  8, $p->shift( 8, $angle, -20, 'Collar width @ top of garment'));
        $p->addPoint( 20, $p->shift(20, $angle, -20, 'Collar width @ top of garment'));
        // Making garment longer
        $p->addPoint(  4, $p->shift( 4, -90, 60), 'Center back at front bottom');
        $p->addPoint(  6, $p->shift( 6, -90, 60), 'Quarter chest at front bottom');

        // Adding points from index 100 onwards
        $p->newPoint( 21, $p->x(21), $p->y(1), 'Control point for 1'); // Re-using point 21
        $p->addPoint( 100, $p->shiftAlong(8, 20, 21, 1, $p->curveLen(8, 20, 21, 1)/2), 'Raglan back tip');
        $p->addSplitCurve(8, 20, 21, 1, 100, 5);

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

        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeve'];

        // Importing cut off shoulder part from front
        $front = $this->parts['front'];
        $load = [12,8,52,53,20,100,15,14,18,10,17,19];
        foreach ($load as $i) {
            $p->addPoint( 100+$i, $front->loadPoint($i), $front->points[$i]->getDescription());
        }
        // Shift in place
        $angle = $p->angle(1, 112);
        $distance = $p->distance(1, 112);
        foreach ($load as $i) {
            $p->addPoint( 100+$i, $p->shift(100+$i, $angle, $distance));
        }
        // Rotate in place
        $angle = 90-$p->angle(108, 1);
        foreach ($load as $i) {
            $p->addPoint( 100+$i, $p->rotate(100+$i, 1, $angle));
        }
        // Flip in place
        foreach ($load as $i) {
            $p->addPoint( 100+$i, $p->flipX( 100+$i, $p->x(1)));
        }

        // Importing cut off shoulder part from back
        $back = $this->parts['back'];
        $load = [12,8,52,53,20,100,15,14,18,10,17,19];
        foreach ($load as $i) {
            $p->addPoint( 200+$i, $back->loadPoint($i), $back->points[$i]->getDescription());
        }
        // Shift in place
        $angle = $p->angle(1, 212);
        $distance = $p->distance(1, 212);
        foreach ($load as $i) {
            $p->addPoint( 200+$i, $p->shift(200+$i, $angle, $distance));
        }
        // Rotate in place
        $angle = 90-$p->angle(208, 1);
        foreach ($load as $i) {
            $p->addPoint( 200+$i, $p->rotate(200+$i, 1, $angle));
        }

        // Raglan seam lengths
        $this->setValue('frontRaglan', $front->curveLen(100, 100, 15, 14) + $front->curveLen(14, 14, 13, 5));
        $this->setValue('backRaglan',  $back->curveLen( 100, 100, 15, 14) + $back->curveLen(14, 14, 13, 5));


        // Sleevecap front
        $p->addPoint( 130, $p->shiftTowards(200, 114, $this->v('frontRaglan')));
        $p->addPoint( 131, $p->shiftTowards(130, 5, $p->distance(130, 5)/2));
        $p->addPoint( 132, $p->shiftTowards(200, 131, $this->v('frontRaglan')));
        $p->addPoint( 133, $p->shift(132, 0, $this->v('frontRaglan')*0.1));
        $p->addPoint( 134, $p->shiftTowards(200, 114, $this->v('frontRaglan')*0.5));
        $delta = $p->curveLen(200, 134, 132, 133) - $this->v('frontRaglan');
        $i=1;
        while (abs($delta) > 1) {
            if ($delta > 0) {
                $angle = 90; // Move up
            } else {
                $angle = -90; // Move down
            }
            $p->newPoint( "132-$i", $p->x(132), $p->y(132));
            $p->addPoint( 132, $p->shift(132, $angle, $delta));
            $p->newPoint( 133, $p->x(133), $p->y(132));
            $delta = $p->curveLen(200, 134, 132, 133) - $this->v('frontRaglan');
            $this->dbg("Iteration $i, front sleevecap delta is $delta");
            $i++;
            if ($i>40) {
                break; // Not good!
            }
        }

        // Sleevecap back
        $p->addPoint( 230, $p->shiftTowards(300, 214, $this->v('backRaglan')));
        $p->addPoint( 231, $p->shiftTowards(230, -5, $p->distance(230, -5)/2));
        $p->addPoint( 232, $p->shiftTowards(300, 231, $this->v('backRaglan')));
        $p->addPoint( 233, $p->shift(232, 180, $this->v('backRaglan')*0.1));
        $p->addPoint( 234, $p->shiftTowards(300, 214, $this->v('backRaglan')*0.5));
        $p->newPoint( 232, $p->x(232), $p->y(132));
        $p->newPoint( 233, $p->x(233), $p->y(232));

        // Adjust sleeve length
        $delta = $p->deltaY(5, 133);
        $p->addPoint( 32, $p->shift(32, -90, $delta+50));
        $p->addPoint( 32, $p->shift(32, 0, 30)); //Make sleeve wider
        $p->addPoint( 31, $p->shift(31, -90, $delta+50));
        $p->addPoint( 31, $p->shift(31, 180, 30)); //Make sleeve wider

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

        /** @var \Freesewing\Part $p */
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

        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketFacing'];

        $p->addPoint( 110, $p->shiftTowards(102, 101, 25));
        $p->addPoint( 111, $p->shift(103, 180, 25));
        $p->addPoint( 112, $p->shift(104, 180, 25));
        $p->addPoint( 112, $p->shift(112, -90, $p->deltaY(102, 110)));
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
        /** @var \Freesewing\Part $front */
        $front = $this->parts['front'];

        /** @var \Freesewing\Part $back */
        $back = $this->parts['back'];

        /** @var \Freesewing\Part $sleeve */
        $sleeve = $this->parts['sleeve'];

        /** @var \Freesewing\Part $p */
        $p = $this->parts['hoodSide'];

        // Some measures we'll need
        $backNeckLen  = ( $back->curveLen( 1, 56, 57, 100) + $sleeve->curveLen(108, 252, 253, 300) );
        $frontNeckLen = ( $front->curveLen(9, 56, 57, 100) + $sleeve->curveLen(108, 153, 152, 200) );
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
        $p->addPoint(  7, $p->shift(1, 0, $hw/2));
        $p->newPoint(  8, $p->x(7), $p->y(6));
        $p->addPoint( 11, $p->shift(1, 0, 30)); // 30 = half of mid panel
        $p->addPoint(  9, $p->shiftAlong(11, 7, 8, 6, $hw-30));
        $p->addPoint( 10, $p->shiftAlong(11, 7, 8, 6, $backNeckLen-30), 'Shoulder notch');
        $p->newPoint( 12, $p->x(10), $p->y(3)+30, 'Crown point');
        $p->newPoint( 13, $p->x(6), $p->y(4)+105);
        $p->addPoint( 14, $p->shift(12, 0, ($p->x(13)-$p->x(12))/2));
        $p->newPoint( 15, $p->x(1)-50, $p->y(12));
        $p->addPoint( 16, $p->rotate(12, 13, 80));
        $p->addPoint( 17, $p->shift(2, 180, 25));
        $p->addPoint( 18, $p->shift(17, 90, 50));
        $p->addPoint( 19, $p->shift(17, -90, 20));
        $p->addPoint( 20, $p->shift(16, 180, 25));
        $p->addPoint( 21, $p->shift(6, 90, 25));
        $p->addPoint( 23, $p->shiftTowards(17, 20, 25));
        $p->addPoint( 22, $p->rotate(23, 17, 180));
        $p->addPoint( 25, $p->shift(6, 90, 25));

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
        /** @var \Freesewing\Part $side */
        $side = $this->parts['hoodSide'];
        $len = $side->curveLen(11, 11, 15, 12) + $side->curveLen(12, 14, 13, 13);

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
        /** @var \Freesewing\Part $front */
        $front = $this->parts['front'];
        $len = $front->deltaX(4, 6) * 4 * $this->o('ribbingStretchFactor');
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
        /** @var \Freesewing\Part $sleeve */
        $sleeve = $this->parts['sleeve'];
        $len = $sleeve->deltaX(31, 32) * $this->o('ribbingStretchFactor');

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
        $p->newPoint( 1, 0, 0);
        $p->newPoint( 2, $w, 0);
        $p->newPoint( 3, $w, $h);
        $p->newPoint( 4, 0, $h);

        // Paths
        $path = 'M 1 L 2 L 3 L 4 z';
        $p->newPath('seamline', $path);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/grainline and so on
    */

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
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Cut on fold (cof)
        $p->newPoint('cofTop', $p->x(9), $p->y(9)+40);
        $p->newPoint('cofBottom', $p->x(9), $p->y(4)-40);
        $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on fold'));

        // Sleeve notch
        $this->frontNotchLen = $p->curveLen(100, 100, 15, 14)/2;
        $p->addPoint('sleeveNotch', $p->shiftAlong(100, 100, 15, 14, $this->frontNotchLen), 'Front sleeve notch');
        $p->newSnippet('sleeveNotch', 'notch', 'sleeveNotch');

        // Mark pocket in helpline
        $p->newPath('pocket', 'M 101 L 102 C 102 104 103 L 105', ['class' => 'helpline']);
        $p->newSnippet('pocketNotch2', 'notch', 102);
        $p->newSnippet('pocketNotch3', 'notch', 103);

        // Title
        $p->newPoint('titleAnchor', $p->x(5)/2, $p->y(2)+$p->deltaY(2, 3)/2);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));

        // Seam allowance
        $sa = 'M 4 L 6 L 5 C 13 16 14 C 15 100 100 C 57 56 9';
        $p->offsetPathString('sa', $sa, 10, true, ['class' => 'seam-allowance']);

        // Close path at the fold
        $p->paths['sa']->setPath('M 4 L sa-line-4TO6 M 9 L sa-curve-9TO100 '.$p->paths['sa']->getPath());
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
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Cut on fold (cof)
        $p->newPoint('cofTop', $p->x(1), $p->y(1)+40);
        $p->newPoint('cofBottom', $p->x(1), $p->y(4)-40);
        $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on fold'));

        // Title
        $p->newPoint('titleAnchor', $p->x(5)/2, $p->y(2)+$p->deltaY(2, 3)/2);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 30));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Logo & CC
        $p->addPoint('logoAnchor', $p->shift('scaleboxAnchor', -90, 90));
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        $p->newSnippet('cc', 'cc', 'logoAnchor');

        // Seam allowance
        $sa = 'M 4 L 6 L 5 C 13 16 14 C 15 100 100 C 57 56 1';
        $p->offsetPathString('sa', $sa, 10, true, ['class' => 'seam-allowance']);
        // Close path at the fold
        $p->paths['sa']->setPath('M 4 L sa-line-4TO6 M 1 L sa-curve-1TO100 '.$p->paths['sa']->getPath());

        // Sleeve notch
        $this->backNotchLen = $p->curveLen(100, 100, 15, 14)/2;
        $p->addPoint('sleeveNotch', $p->shiftAlong(100, 100, 15, 14, $this->backNotchLen), 'Back sleeve notch');
        $p->addPoint('sleeveNotcha', $p->shiftAlong(100, 100, 15, 14, $this->backNotchLen + 2.5), 'Back sleeve notch a');
        $p->addPoint('sleeveNotchb', $p->shiftAlong(100, 100, 15, 14, $this->backNotchLen - 2.5), 'Back sleeve notch b');
        $p->newSnippet('sleeveNotcha', 'notch', 'sleeveNotcha');
        $p->newSnippet('sleeveNotchb', 'notch', 'sleeveNotchb');
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
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->clonePoint(8, 'titleAnchor');
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'));

        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);

        // Sleeve notches
        $p->addPoint('frontSleeveNotch', $p->shiftAlong(200, 134, 132, 133, $this->frontNotchLen), 'Front sleeve notch');
        $p->addPoint('backSleeveNotcha', $p->shiftAlong(300, 234, 232, 233, $this->backNotchLen -2.5), 'Back sleeve notch a');
        $p->addPoint('backSleeveNotchb', $p->shiftAlong(300, 234, 232, 233, $this->backNotchLen +2.5), 'Back sleeve notch b');
        $p->newSnippet('sleeveNotch', 'notch', 'frontSleeveNotch');
        $p->newSnippet('sleeveNotcha', 'notch', 'backSleeveNotcha');
        $p->newSnippet('sleeveNotchb', 'notch', 'backSleeveNotchb');
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
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Cut on fold (cof)
        $p->newPoint('cofTop', $p->x(105), $p->y(105)+10);
        $p->newPoint('cofBottom', $p->x(105), $p->y(4)-10);
        $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on fold'));

        // Title
        $p->newPoint('titleAnchor', $p->x('grainlineBottom') + $p->deltaX('grainlineBottom', 101)/2, $p->y(105)+$p->deltaY(105, 4)/2);
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));

        // Seam allowance
        $sa = 'M 105 L 103 C 104 102 102 L 101 L 4';
        $p->offsetPathString('sa', $sa, -10, true, ['class' => 'seam-allowance']);
        $p->paths['sa']->setPath('M 4 L sa-line-4TO101 M 105 L sa-line-105TO103 '.$p->paths['sa']->getPath());
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
        $p->addPoint('grainlineTop', $p->shift(103, -115, 15));
        $p->addPoint('grainlineBottom', $p->shift(104, -115, 15));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(111) + $p->deltaX(111, 103)/2, $p->y(111)+35);
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'), 'vertical');

        // Seam allowance
        $p->offsetPath('sa', 'seamline', 10, true, ['class' => 'seam-allowance']);
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
        $p->addPoint( 'grainlineTop', $p->shift(12, -90, 5));
        $p->addPoint( 'grainlineBottom', $p->shift(10, 90, 5));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->addPoint('titleAnchor', $p->shift(12, -70, 100));
        $p->addTitle('titleAnchor', 6, $this->t($p->title), '4x '.$this->t('from fabric')." (2x2)\n".$this->t('Good sides together'));

        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);

        // Notch
        $p->newSnippet('sleeveNotch', 'notch', 10);
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
        $this->finalizeRectangle($model, $p, 7, '2x '.$this->t('from fabric')."\n".$this->t('Good sides together'));
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
        $this->finalizeRectangle($model, $p, 8, '2x '.$this->t('from ribbing')."\n".$this->t('Good sides together'));
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
        $this->finalizeRectangle($model, $p, 9, '1x '.$this->t('from ribbing'));
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
        $this->finalizeRectangle($model, $p, 10, '1x '.$this->t('from fabric'), 'vertical');
    }

    private function textAttr($dy)
    {
        // FIXME remove this
        return ['class' => 'text-lg fill-note text-center', 'dy' => $dy];
    }

    /**
     * Finalizes one of the rectanglular parts
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add the info to
     *
     * @return void
     */
    public function finalizeRectangle($model, $p, $nr, $cut, $titleOption = '')
    {
        // Grainline
        $p->newPoint( 'grainlineTop', $p->x(1)+5, $p->y(3)/1.5);
        $p->newPoint( 'grainlineBottom', $p->x(2)-5, $p->y('grainlineTop'));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(2)/2, $p->y(3)/4);
        $p->addTitle('titleAnchor', $nr, $this->t($p->title), $cut, $titleOption);

        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, true, ['class' => 'seam-allowance']);
    }


    /*
        ____                       _
       |  _ \ __ _ _ __   ___ _ __| | ___  ___ ___
       | |_) / _` | '_ \ / _ \ '__| |/ _ \/ __/ __|
       |  __/ (_| | |_) |  __/ |  | |  __/\__ \__ \
       |_|   \__,_| .__/ \___|_|  |_|\___||___/___/
                  |_|

      Instructions for paperless patterns
    */


    /**
     * Adds paperless info for front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Heights on the left
        $xBase = $p->x(2);
        $p->newHeightDimension(4,9,$xBase-15);
        $p->newHeightDimension(4,100,$xBase-30);

        // Heights on the right
        $xBase = $p->x(6);
        $p->newHeightDimension(6,102,$xBase+25);
        $p->newHeightDimension(6,103,$xBase+40);
        $p->newHeightDimension(6,5,$xBase+55);

        // Width at the bottom
        $yBase = $p->y(6);
        $p->newWidthDimension(4,101,$yBase+25);
        $p->newWidthDimension(4,102,$yBase+40);
        $p->newWidthDimension(4,6,$yBase+55);

        // Width at the top
        $yBase = $p->y(100);
        $p->newWidthDimension(9,100,$yBase-30);

        // Distance to notch
        $p->newLinearDimension(100,'sleeveNotch',-20);

        // Notes
        $p->newPoint('saNote', $p->x(5), $p->y(103)-40, 'sa note anchor');
        $p->newNote('saNote', 'saNote', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 10, -5);
    }

    /**
     * Adds paperless info for back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Heights on the left
        $xBase = $p->x(2);
        $p->newHeightDimension(4,1,$xBase-15);
        $p->newHeightDimension(4,100,$xBase-30);

        // Heights on the right
        $xBase = $p->x(6);
        $p->newHeightDimension(6,5,$xBase+25);

        // Width at the bottom
        $p->newWidthDimension(4,6,$p->y(4)+25);

        // Width at the top
        $p->newWidthDimension(1,100,$p->y(100)-25);

        // Distance to notch
        $p->newLinearDimension(100,'sleeveNotch',-20);

        // Notes
        $p->newPoint('saNote', $p->x(5), $p->y(3), 'sa note anchor');
        $p->newNote('saNote', 'saNote', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 10, -5);
    }

    /**
     * Adds paperless info for sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeve'];

        // Horizontal help line
        $p->newPath($p->newId(),'M 233 L 133',['class' => 'helpline']);

        // Widths at the top
        $yBase = $p->y(300);
        $p->newWidthDimension(300,108,$yBase-25);
        $p->newWidthDimension(108,200,$yBase-25);
        $p->newWidthDimension(233,108,$yBase-40);
        $p->newWidthDimension(108,133,$yBase-40);

        // Heights at the right
        $xBase = $p->x(133);
        $p->newHeightDimension(133,108,$xBase+25);
        $p->newHeightDimension(133,200,$xBase+40);
        $p->newHeightDimension(133,300,$xBase+55);
        $p->newHeightDimension(32,133,$xBase+25);

        // Widths at bottom
        $p->newWidthDimension(31,32,$p->y(31)+25);

        // Notes
        $noteAttr = ['line-height' => 7, 'class' => 'text-lg'];
        $p->addPoint('saNote', $p->beamsCross(133, 32, 34, 35), 'sa note anchor');
        $p->newNote('saNote', 'saNote', $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 10, -5, $noteAttr);
    }

    /**
     * Adds paperless info for pocket
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPocket($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocket'];

        // Widths at the bottom
        $yBase = $p->y(4);
        $p->newWidthDimension(4,101,$yBase+25);
        $p->newWidthDimension(4,102,$yBase+40);

        // Heights at the right
        $xBase = $p->x(102);
        $p->newHeightDimension(101,102,$xBase+25);
        $p->newHeightDimension(101,103,$xBase+40);
    }

    /**
     * Adds paperless info for pocket facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPocketFacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketFacing'];

        // Width at the top
        $p->newWidthDimension(111,103,$p->y(111)-25);
        $p->newWidthDimension(111,102,$p->y(111)-40);

        // Heights at the right
        $xBase = $p->x(102);
        $p->newHeightDimension(102,103,$xBase+25);
        $p->newHeightDimension(110,103,$xBase+40);
    }

    /**
     * Adds paperless info for hood side
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessHoodSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['hoodSide'];

        // Edge of curves
        $p->addPoint(201, $p->curveEdgeLeft(12, 15, 11, 11));
        $p->addPoint(202, $p->curveEdgeLeft(13, 20, 23, 17));

        // Front depth of the hood
        $p->newWidthDimension(202,6);

        // Width at the bottom
        $yBase = $p->y(6);
        $p->newWidthDimension(11,6,$yBase+40);
        $p->newWidthDimension(201,6,$yBase+55);

        // Height at the right
        $xBase = $p->x(13);
        $p->newHeightDimension(6,13,$xBase+25);
        $p->newHeightDimension(6,12,$xBase+40);

        // Notes
        $p->newNote('saNote', 201, $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 3, 10, -3, ['line-height' => 6, 'class' => 'text-lg', 'dy' => -10]);

        // Split curve for notch distance from left side
        $p->addSplitCurve(11,7,8,6,10,'.neckCurve');
        $p->newCurvedDimension('M 11 C .neckCurve2 .neckCurve3 10', 25);
        $p->newCurvedDimension('M 10 C .neckCurve7 .neckCurve6 6', 25);

        // Hood len
        $p->newCurvedDimension('M 11 C 11 15 12 C 14 13 13', -25);
    }

    /**
     * Adds paperless info for hood center
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessHoodCenter($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['hoodCenter'];

        $this->paperlessRectangle($p);
    }

    /**
     * Adds paperless info for cuff
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCuff($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['cuff'];

        $this->paperlessRectangle($p);
    }

    /**
     * Adds paperless info for waistband
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['waistband'];

        $this->paperlessRectangle($p);
    }

    /**
     * Adds paperless info for neck binding
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessNeckBinding($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['neckBinding'];

        $this->paperlessRectangle($p);
    }

    /**
     * Adds paperless info for a rectangular part
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessRectangle($p)
    {
        // Width
        $p->newWidthDimension(4,3,$p->y(4)+25);

        // Height
        $p->newHeightDimension(3,2,$p->x(3)+25);

        // Notes
        $p->newPoint(200, $p->x(3), $p->y('grainlineTop') - 20);
        $p->newNote('saNote', 200, $this->t("Standard\nseam\nallowance")."\n(".$this->unit(10).')', 9, 10, -3, ['line-height' => 6, 'class' => 'text-lg', 'dy' => -10]);
    }
}
