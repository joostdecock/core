<?php
/** Freesewing\Patterns\WahidWaistcoat class */
namespace Freesewing\Patterns;

/**
 * The Wahid Waistcoat pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class WahidWaistcoat extends JoostBodyBlock
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /**
     * Fix collar ease to 1.5cm
     */
    const COLLAR_EASE = 15;

    /**
     * Fix sleevecap ease to 1.5cm
     */
    const SLEEVECAP_EASE = 15;

    /**
     * Fix pocketWidht to 10cm
     */
    const POCKET_WIDTH = 100;

    /**
     * Fix pocketHeight to 1.5cm
     */
    const POCKET_HEIGHT = 15;

    /**
     * Fix pocketAngle to 5 degrees
     */
    const POCKET_ANGLE = 5;

    /**
     * Fix backNeckCutout to 2.5cm
     */
    const BACK_NECK_CUTOUT = 25;

    /**
     * Sets up options and values for our draft
     *
     * By branching this out of the sample/draft methods, we can
     * set a bunch of options and values the influence the draft
     * without having to touch the sample/draft methods
     * When extending this pattern so we can just implement the
     * initialize() method and re-use the other methods.
     *
     * Good to know:
     * Options are typically provided by the user, but sometimes they are fixed
     * Values are calculated for re-use later
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
        // Options that are fixed
        $this->setOption('collarEase', self::COLLAR_EASE);
        $this->setOption('sleevecapEase', self::SLEEVECAP_EASE);
        $this->setOption('backNeckCutout', self::BACK_NECK_CUTOUT);

        // Depth of the armhole
        $this->setValue('armholeDepth', 290 - self::BACK_NECK_CUTOUT + ($model->m('shoulderSlope') / 2 - 27.5) + ($model->m('bicepsCircumference') / 10));

        // Collar widht and depth
        $this->setValue('collarWidth', ($model->getMeasurement('neckCircumference') / self::PI) / 2 + 5);
        $this->setValue('collarDepth', ($model->getMeasurement('neckCircumference') + $this->getOption('collarEase')) / 5 - 8);

        // Cut front armhole a bit deeper
        $this->setValue('frontArmholeExtra', 5);

        // Overlap at front
        $this->setValue('frontOverlap', 10);

        // Some helper vars
        $this->setValue('chest', $model->m('chestCircumference') + $this->o('chestEase'));
        $this->setValue('waist', $model->m('naturalWaist') + $this->o('waistEase'));
        $this->setValue('hips',  $model->m('hipsCircumference')  + $this->o('hipsEase'));

        $waist_re = $this->v('chest') - $this->v('waist');
        $hips_re = $this->v('chest') - $this->v('hips');
        if ($hips_re <= 0) {
            $hips_re = 0;
        }
        if ($waist_re <= 0) {
            $waist_re = 0;
        }

        // How much to reduce in darts/sides
        $this->setValue('waistReduction', $waist_re);
        $this->setValue('hipsReduction', $hips_re);
        $this->setValue('scyeDart', (5 + $waist_re/10));
    }

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
     * Note that we're only including front and back in the sample
     * as the other parts add little relevance while cluttiring up the sample
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        // Setup all options and values we need
        $this->initialize($model);

        // Draft the base block
        /** @see \Freesewing\Patterns\JoostBodyBlock::draftBackBlock() */
        $this->draftBackBlock($model);
        /** @see \Freesewing\Patterns\JoostBodyBlock::draftFrontBlock() */
        $this->draftFrontBlock($model);

        // Draft base front to the point where it differs from back
        $this->draftWaistcoatFrontBlock($model);

        // Draft the final front and back
        $this->draftFront($model);
        $this->draftBack($model);

        // Do not render parts from parent pattern
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
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
        // Continue from sample
        $this->sample($model);

        // Draft all remaining blocks
        $this->draftFrontFacing($model);
        $this->draftFrontLining($model);
        $this->draftPocketWelt($model);
        $this->draftPocketInterfacing($model);
        $this->draftPocketFacing($model);
        $this->draftPocketBag($model);


        // Finalize front and back
        $this->finalizeFront($model);
        $this->finalizeBack($model);

        // Finalize all remaining parts
        $this->finalizeFrontFacing($model);
        $this->finalizeFrontLining($model);
        $this->finalizePocketWelt($model);
        $this->finalizePocketInterfacing($model);
        $this->finalizePocketFacing($model);
        $this->finalizePocketBag($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            // Add paperless info to all parts
            $this->paperlessFront($model);
            $this->paperlessBack($model);
            $this->paperlessFrontFacing($model);
            $this->paperlessFrontLining($model);
            $this->paperlessPocketWelt($model);
            $this->paperlessPocketInterfacing($model);
            $this->paperlessPocketFacing($model);
            $this->paperlessPocketBag($model);
        }
    }

    /**
     * Drafts the waistcoatFrontBlock
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
    public function draftWaistcoatFrontBlock($model)
    {
        $this->clonePoints('frontBlock', 'waistcoatFrontBlock');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['waistcoatFrontBlock'];

        // Neck cutout
        $p->newPoint(  300, $p->x(1)-$this->v('frontOverlap'), $p->y(5) - 80 + $this->o('frontDrop'), 'Neck cutout base point');
        if ($this->o('frontStyle') == 2) {
            $p->newPoint(  301, $p->x(8), $p->y(300), 'Neck cutout control point');
        } else {
            $p->newPoint(  301, $p->x(8)+20, $p->y(9), 'Neck cutout control point');
        }

        // Front inset
        $p->addPoint( 10, $p->shift(10,180,$this->o('frontInset')));
        $p->addPoint( 17, $p->shift(17,180,$this->o('frontInset')));
        $p->addPoint( 18, $p->shift(18,180,$this->o('frontInset')));
        $p->addPoint( 14, $p->shift(14,180,$this->o('frontInset')/2));
        $p->addPoint( 15, $p->shift(15,180,$this->o('frontInset')/2));
        $p->addPoint( 16, $p->shift(16,180,$this->o('frontInset')/2));

        // Shoulder inset
        $p->addPoint( 12, $p->shiftTowards(12,8,$this->o('shoulderInset')));
        //$p->addPoint( 19, $p->shiftTowards(12,8,10));
        //$p->addPoint( 19, $p->rotate(19,12,90));

        // Neck inset
        $p->addPoint( 8, $p->shiftTowards(8,12,$this->o('neckInset')));
        $p->addPoint( 20, $p->shiftTowards(8,12,20));
        $p->addPoint( 20, $p->rotate(20,8,-90));

        //Hem
        $p->newPoint(  302, $p->x(4)-$this->v('frontOverlap'), $p->y(4)+$this->o('lengthBonus'), 'Bottom edge');

        // Waist reduction
        $w8th = $this->v('waistReduction')/8;
        // Hips reduction
        if($this->v('hipsReduction') < 0) {
            // Ease is less than chest
            // To prevent dart from overlapping,
            // move excess ease to other seams
            $h8th = $this->v('hipsReduction')/6;
            $hDart = 0;
        } else {
            // More ease than chest, so divide easy evenly
            $h8th = $this->v('hipsReduction')/8;
            $hDart = $h8th;
        }

        // Front dart
        $p->newPoint(  900 , $p->x(5)*0.5,$p->y(3), 'Dart center');
        $p->addPoint(  901 , $p->shift(900,0, $w8th/2));
        $p->addPoint(  902 , $p->flipX(901,$p->x(900)));
        $p->addPoint(  903 , $p->shift(901,90,$p->deltaY(5,3)*0.25));
        $p->addPoint(  904 , $p->flipX(903,$p->x(900)));
        $p->addPoint(  905 , $p->shift(901,-90,$p->deltaY(3,4)*0.25));
        $p->addPoint(  906 , $p->flipX(905,$p->x(900)));
        $p->addPoint(  907 , $p->shift(900,90, $p->deltaY(5,3)));
        $p->addPoint(  908 , $p->shift(900,-90, $p->deltaY(3,4)));
        $p->addPoint(  909 , $p->shift(908,0, $hDart/2));
        $p->addPoint(  910 , $p->flipX(909,$p->x(900)));
        $p->newPoint(  911 , $p->x(909), $p->y(302));
        $p->newPoint(  912 , $p->x(910), $p->y(302));
        $p->addPoint(  913 , $p->shift(909,90,25));
        $p->addPoint(  914 , $p->shift(910,90,25));
        $this->setValue('frontDart', 'L 910 C 914 906 902 C 904 907 907 C 907 903 901 C 905 913 909');

        // Side dart
        $p->newPoint( 2900, $p->x(5), $p->y(900));
        $p->newPoint( 2901, $p->x(2900)+$w8th, $p->y(2900));
        $p->addPoint( 2902, $p->flipX(2901, $p->x(2900)));
        $p->addPoint( 2903, $p->shift(2901, 90, $p->deltaY(5, 3)*0.25));
        $p->addPoint( 2904, $p->flipX(2903, $p->x(2900)));
        $p->addPoint( 2905, $p->shift(2901, -90, $p->deltaY(3, 4)*0.25));
        $p->addPoint( 2906, $p->flipX(2905, $p->x(2900)));
        $p->addPoint( 2907, $p->shift(2900, 90, $p->deltaY(5, 3)*0.75));
        $p->newPoint( 2908, $p->x(2900)-$h8th/2, $p->y(4));
        $p->addPoint( 2909, $p->shift(2908, 0, $h8th/2));
        $p->addPoint( 2910, $p->shift(2908, 180, $h8th/2));
        $p->newPoint( 2911, $p->x(2909), $p->y(302));
        $p->newPoint( 2912, $p->x(2910), $p->y(302));
        $p->addPoint( 2913, $p->shift(2909, 90, 35));
        $p->addPoint( 2914, $p->shift(2910, 90, 35));


    }

    /**
     * Drafts the waistcoat front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFront($model)
    {
        $this->clonePoints('waistcoatFrontBlock', 'front');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Hem
        if($this->o('hemStyle')==1) { // Classic hem
          $r = $p->deltaX(302,2910)/4;
          $p->addPoint(4001, $p->shift(302,90,$r/2));
          $p->newPoint(4002, $p->x(4001)+$r,$p->y(4001)+$r);
          $p->addPoint(4003, $p->shift(4002,45,$r/4));
          //$p->newPoint(4004, $p->x(1911), $p->y(1911));
          $p->newPoint(4005, $p->x(2911), $p->y(2911));

          // Extend dart
          $p->curveCrossesX(4002,4003,911,2912,$p->x(911), 911);
          $p->curveCrossesX(4002,4003,911,2912,$p->x(912), 912);

          // Split arc
          $points = $p->splitCurve(4002,4003,911,2912,9121);
          $p->addPoint(4006, $points[1]);
          $p->addPoint(4007, $points[2]);
          $points = $p->splitCurve(4002,4003,911,2912,9111);
          $p->addPoint(4008, $points[5]);
          $p->addPoint(4009, $points[6]);

          // Hem line
          $hemA = " L 4002 C 4006 4007 9121 ";
          $hemB = " L 9111 C 4009 4008 2912 ";
        } else { // Rounded hem
            $r = $this->o('hemRadius');
            // Let's not arc into our dart
            if($r > $p->deltaX(302,910)) $r = $p->deltaX(302,910);
            $rc = \Freesewing\BezierToolbox::bezierCircle($r);
            $p->addPoint(4001, $p->shift(302,90,$r));
            $p->addPoint(4002, $p->shift(4001,-90,$rc));
            $p->addPoint(4004, $p->shift(302,0,$r));
            $p->addPoint(4003, $p->shift(4004,180,$rc));
            $hemA = "C 4002 4003 4004 L 912 ";
            $hemB = " L 911 L 2912";
        }
        $hemWithDart = $hemA.$this->v('frontDart').$hemB;
        $hemWithoutDart = $hemA.$hemB;

        // Buttons
        $p->newPoint(5000, $p->x(2),$p->y(300)+10);
        $p->newPoint(5050, $p->x(2),$p->y(4001)-10);
        $bc = $this->o('buttons')-1;
        for($i=1;$i<$bc;$i++) {
            $p->addPoint(5000+$i,$p->shift(5000,-90,($i)*$p->deltaY(5000,5050)/$bc));
            $p->newSnippet("buttonhole$i", 'buttonhole', 5000+$i);
            $p->newSnippet("button$i", 'button', 5000+$i);
        }
        $p->newSnippet("buttonholeTop", 'buttonhole', 5000);
        $p->newSnippet("buttonTop", 'button', 5000);
        $p->newSnippet("buttonholeBottom", 'buttonhole', 5050);
        $p->newSnippet("buttonBottom", 'button', 5050);

        // Pockets
        $pw = self::POCKET_WIDTH;
        $ph = self::POCKET_HEIGHT;
        $pa = self::POCKET_ANGLE;
        $p->newPoint(7000, $p->x(900),$p->y(900)+$p->deltaY(900,4001)*0.6-$ph/2); // Center dart, top
        $p->curveCrossesY(901,905,913,909,$p->y(7000), 700); // Creates point 7001, Right dart side, top
        $p->addPoint(7002, $p->flipX(7001,$p->x(7000))); // Left dart side, top
        $p->curveCrossesY(901,905,913,909,$p->y(7000)+$ph, '.help'); // Approx. right dart side, bottom
        $p->addPoint(7003, $p->shiftTowards(7001,'.help1',$ph)); // Exact right dart side, bottom. Taking dart angle into account
        $p->addPoint(7004, $p->flipX(7003,$p->x(7000))); // Left dart side, bottom

        $p->addPoint(7005, $p->shift(7001, $p->angle(7001,7003)-90+self::POCKET_ANGLE, $pw/2));
        $p->addPoint(7006, $p->shift(7005, $p->angle(7001,7003)-180+self::POCKET_ANGLE, $ph));

        $p->addPoint(7007, $p->shift(7002, $p->angle(7002,7004)-90+self::POCKET_ANGLE, $pw/-2));
        $p->addPoint(7008, $p->shift(7007, $p->angle(7002,7004)-180+self::POCKET_ANGLE, $ph));

        // Make Front shoulder 1cm more sloped
        $p->addPoint('shoulderFront', $p->shiftAlong(12,19,17,10,10));
        $p->addPoint('shoulderFrontCp', $p->shift('shoulderFront',$p->angle(8,'shoulderFront')+90,10));

        // Front scye dart
        $p->newPoint(2000, $p->x(5), $p->y(10));
        $p->curveCrossesLine(14,15,18,10,907,2000,200); // Creates point 2001
        $p->addPoint(2002, $p->shift(2001,$p->angle(907,2000)-90,$this->o('frontScyeDart')));
        $angle = $p->angle(907,2000) - $p->angle(907,2002);
        $this->msg("angle is $angle");
        $torotate = array(15,14,16,13,5,2907,2904,2902,2906,2914,2910,2912,911,909,913,905,901,903,7001,7003,7005,7006);
        if($this->o('hemStyle')==1) { // Classic hem
            $torotate[] = 4008;
            $torotate[] = 4009;
            $torotate[] = 9111;
        }
        // Rotate front scye dart into front dart
        foreach($torotate as $rp) $p->addPoint($rp, $p->rotate($rp,907,-$angle));

        // Facing/Lining boundary (flb)
        $p->addPoint('flbTop', $p->shiftTowards(8,'shoulderFront', $p->distance(8,'shoulderFront')/2));
        $p->addPoint('flbHelp', $p->rotate(8,'flbTop',90));
        $p->addPoint('flbCp', $p->beamsCross('flbTop','flbHelp', 900,907));

        // Paths
        $pocket = 'M 7001 L 7005 7006 7003 M 7004 L 7008 7007 7002';
        $p->newPath('pocket', $pocket, ['class' => 'helpline']);
        $flb = 'M flbTop C flbCp 907 907';
        $p->newPath('flb', $flb, ['class' => 'helpline']);
        $seamlineA = "M 300 L 4001 ";
        $seamlineB = " L 2910 C 2914 2906 2902 C 2904 2907 5 C 13 16 14 C 15 18 10 C 17 shoulderFrontCp shoulderFront L 8 C 20 301 300 z";
        $seamline = $seamlineA.$hemWithDart.$seamlineB;
        $p->newPath('seamline', $seamline);
        $saBase = $seamlineA.$hemWithoutDart.$seamlineB;
        $p->newPath('saBase', $saBase);

        // Grid anchor
        $p->clonePoint(302, 'gridAnchor');

        // Mark path for sample service
        $p->paths['pocket']->setSample(true);
        $p->paths['seamline']->setSample(true);
        $p->paths['saBase']->setRender(false);

        // Store shoulder seam length
        $this->setValue('frontShoulderSeamLength', $p->distance(8,'shoulderFront'));

    }

    /**
     * Drafts the waistcoat back
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
        /** @var \Freesewing\Part $wfb */
        $wfb = $this->parts['waistcoatFrontBlock'];

        // Back inset
        $p->addPoint( 10, $p->shift(10,180,$this->o('backInset')));
        $p->addPoint( 17, $p->shift(17,180,$this->o('backInset')));
        $p->addPoint( 18, $p->shift(18,180,$this->o('backInset')));
        $p->addPoint( 14, $p->shift(14,180,$this->o('backInset')/2));
        $p->addPoint( 15, $p->shift(15,180,$this->o('backInset')/2));
        $p->addPoint( 16, $p->shift(16,180,$this->o('backInset')/2));


        // Neck inset
        $p->addPoint( 8, $p->shiftTowards(8,12,$this->o('neckInset')));
        $p->addPoint( 20, $p->shiftTowards(8,12,20));
        $p->addPoint( 20, $p->rotate(20,8,-90));

        // Clone dart points from waistcoatFrontBlock
        $points = array(912,910,914,906,902,904,907,903,901,905,913,909,911,2910,2914,2906,2902,2904,2907);
        foreach($points as $i) $p->newPoint($i, $wfb->x($i), $wfb->y($i));

        // Make back shoulder 1cm more sloped
        $p->addPoint( 'shoulderBack', $p->shift(12,$p->angle(8,12)+90,10));

        // Shoulder inset
        $p->addPoint( 'shoulderBack', $p->shiftTowards('shoulderBack',8,$this->o('shoulderInset')));

        // Center back dart
        $p->addPoint(1, $p->shift(1,0,$this->o('centerBackDart')));
        $p->newPoint('1cp', $p->x(2), $p->y(10));

        // Make shoulder seam seam length
        $p->addPoint('shoulderBack', $p->shiftTowards(8,'shoulderBack',$this->v('frontShoulderSeamLength')));
        $p->addPoint( 19, $p->shiftTowards('shoulderBack',8,10));
        $p->addPoint( 19, $p->rotate(19,'shoulderBack',90));

        // Back scye dart
        $p->addPoint('.help1', $p->shift(10, $p->angle(907,10)-90, $this->o('backScyeDart')/2)); // Half of the dart
        $angle = 2*($p->angle(907,'.help1') - $p->angle(907,10)); // This is the dart angle
        $toRotate = [18,10,19,17,'shoulderBack',8,20,1]; // Points involved in dart rotation
        foreach($toRotate as $i) $p->addPoint($i, $p->rotate($i, 907, -1*$angle));

        // Add lenght bonus
        $p->newPoint(4000, $p->x(4), $p->y(4)+$this->o('lengthBonus'));
        $p->newPoint(2911, $p->x(2910), $p->y(4)+$this->o('lengthBonus'));



        // Paths
        $partA = 'M 1 C 1 1cp 2 L 3 L 4000 L 912 ';
        $dart = 'L 910 C 914 906 902 C 904 907 907 C 907 903 901 C 905 913 909 ';
        $partB = 'L 911 L 2911 L 2910 C 2914 2906 2902 C 2904 2907 5 C 13 16 14 C 15 18 10 C 17 19 shoulderBack L 8 C 20 1 1 z';
        $withDart = $partA.$dart.$partB;
        $withoutDart = $partA.$partB;
        $p->newPath('seamline', $withDart);
        $p->newPath('saBase', $withoutDart);
        $p->paths['saBase']->setRender(false);

        // Grid anchor
        $p->clonePoint(4, 'gridAnchor');

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Drafts the waistcoat front facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontFacing($model)
    {
        $this->clonePoints('front', 'frontFacing');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontFacing'];

        // Paths
        $seamline = "M 300 L 4001 ";
        // Classic hem or rounded?
        if($this->o('hemStyle')==1) $seamline .= " L 4002 C 4006 4007 9121 "; // Classic
        else $seamline .= ' C 4002 4003 4004 L 912 '; // Rounded
        $seamline .= " L 910 C 914 906 902 C 904 907 907 C 907 flbCp flbTop L 8 C 20 301 300 z";
        $p->newPath('seamline', $seamline);
    }

    /**
     * Drafts the waistcoat front lining
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontLining($model)
    {
        $this->clonePoints('front', 'frontLining');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontLining'];

        // Paths
        $seamline = "M flbTop L shoulderFront C shoulderFrontCp 17 10 C 18 15 14 C 16 13 5 C 2907 2904 2902 C 2906 2914 2910 L 2912 ";
        // Classic hem or rounded?
        if($this->o('hemStyle')==1) $seamline .= " C 4008 4009 9111 "; // Classic
        else $seamline .= ' L 911 '; // Rounded
        $seamline .= " L 909 C 913 905 901 C 903 907 907 C 907 flbCp flbTop z";
        $p->newPath('seamline', $seamline);
    }

    /**
     * Drafts the waistcoat pocket welt
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocketWelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketWelt'];

        $p->newPoint( 1, self::POCKET_WIDTH/2, 0);
        $p->newPoint( 2, $p->x(1)+20, $p->y(1)-10);
        $p->newPoint( 3, $p->x(2), $p->y(1)+self::POCKET_HEIGHT*4);

        $mirror = [1,2,3];
        foreach($mirror as $i) $p->addPoint($i*-1, $p->flipX($i,0));

        $p->newPath('pocketLine', 'M -1 L 1', ['class' => 'helpline']);
        $p->newPath('outline', 'M -2 L 2 L 3 L -3 z');
    }

    /**
     * Drafts the waistcoat pocket facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocketFacing($model)
    {
        $this->clonePoints('pocketWelt', 'pocketFacing');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketFacing'];

        $cplen = \Freesewing\BezierToolbox::bezierCircle(20);
        $p->addPoint( 3, $p->shift(3,-90,10));
        $p->addPoint( 4, $p->shift(3,-90,$cplen));
        $p->addPoint( 5, $p->shift(3,-90,20));
        $p->addPoint( 6, $p->shift(5,180,$cplen));
        $p->addPoint( 7, $p->shift(5,180,20));

        $mirror = [1,2,3,4,6,7];
        foreach($mirror as $i) $p->addPoint($i*-1, $p->flipX($i,0));

        $p->newPath('pocketLine', 'M -1 L 1', ['class' => 'helpline']);
        $p->newPath('outline', 'M -2 L 2 L 3 C 4 6 7 L -7 C -6 -4 -3 z');
    }

    /**
     * Drafts the waistcoat pocket bag
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocketBag($model)
    {
        $this->clonePoints('pocketFacing', 'pocketBag');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketBag'];

        $p->addPoint( 2, $p->shift(2,90,self::POCKET_HEIGHT*2));

        $mirror = [1,2,3,4,6,7];
        foreach($mirror as $i) $p->addPoint($i*-1, $p->flipX($i,0));

        $p->newPath('pocketLine', 'M -1 L 1', ['class' => 'helpline']);
        $p->newPath('outline', 'M -2 L 2 L 3 C 4 6 7 L -7 C -6 -4 -3 z');
    }

    /**
     * Drafts the waistcoat pocket interfacing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPocketInterfacing($model)
    {
        $this->clonePoints('pocketWelt', 'pocketInterfacing');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketInterfacing'];

        $p->addPoint( 3, $p->shift(3,90,self::POCKET_HEIGHT));

        $mirror = [1,2,3];
        foreach($mirror as $i) $p->addPoint($i*-1, $p->flipX($i,0));

        $p->newPath('pocketLine', 'M -1 L 1', ['class' => 'helpline']);
        $p->newPath('outline', 'M -2 L 2 L 3 L -3 z');
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
     *
     * @return void
     */
    public function finalizeFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Seam allowance
//        $p->offsetPath('sa', 'saBase', 10, 1, ['class' => 'seam-allowance']);

        // Title
        $p->newPoint('titleAnchor', $p->x(8), $p->y(5000));
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('With good sides together'));

        // Grainline
        $p->addPoint('grainlineTop', $p->shift(8,-45,10));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4001));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));
    }

    /**
     * Finalizes the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Seam allowance
        $p->offsetPath('sa', 'saBase', 10, 1, ['class' => 'seam-allowance']);

        // Title
        $p->newPoint('titleAnchor', $p->x(2)+$p->deltaX(2,907)/2, $p->y(5));
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".'2x '.$this->t('from lining')."\n".$this->t('With good sides together'));

        // Logo
        $p->newPoint('logoAnchor', $p->x(10)/2, $p->y(10));
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        $p->newSnippet('cc', 'cc', 'logoAnchor');

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('logoAnchor', -90, 40));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Grainline
        $p->addPoint('grainlineTop', $p->shift(1,-45,10));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4)-10);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));
    }

    /**
     * Finalizes the front facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrontFacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontFacing'];

        // Seam allowance
        $p->offsetPath('sa', 'seamline', 10, 1, ['class' => 'seam-allowance']);

        // Title
        $p->newPoint('titleAnchor', $p->x(300)+$p->deltaX(300,'flbTop')/2, $p->y(5000));
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('With good sides together'));

        // Grainline
        $p->addPoint('grainlineTop', $p->shift(8,-45,10));
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(4001));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));
    }

    /**
     * Finalizes the front lining
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrontLining($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontLining'];

        // Seam allowance
        $p->offsetPath('sa', 'seamline', -10, 1, ['class' => 'seam-allowance']);

        // Title
        $p->newPoint('titleAnchor', $p->x(907)+$p->deltaX(907,5)/2, $p->y(2907));
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '2x '.$this->t('from main fabric')."\n".$this->t('With good sides together'));

        // Grainline
        $p->addPoint('grainlineBottom', $p->shift(909,45,10));
        $p->newPoint('grainlineTop', $p->x('grainlineBottom'), $p->y('shoulderFront'));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));
    }

    /**
     * Finalizes the pocket welt
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePocketWelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketWelt'];

        // Title
        $p->newPoint('titleAnchor', 0, $p->y(3)/2);
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '2x '.$this->t('from main fabric'));

        // Grainline
        $p->addPoint('grainlineBottom', $p->shift(3,135,5));
        $p->addPoint('.help1', $p->shift(2,-135,5));
        $p->addPoint('grainlineTop', $p->rotate('.help1','grainlineBottom',self::POCKET_ANGLE));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notches
        $p->newSnippet('notchRight', 'notch', 1);
        $p->newSnippet('notchLeft', 'notch', -1);

        // No seam allowance note
        $p->newPoint('noSaAnchor', $p->x(-3),$p->y(-1));
        $p->newNote(1, 'noSaAnchor', $this->t("No\nseam\nallowance"), 4, 10, 0, ['line-height' => 4, 'class' => 'text']);
    }

    /**
     * Finalizes the pocket interfacing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePocketInterfacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketInterfacing'];

        // Title
        $p->newPoint('titleAnchor', 0, $p->y(3)/2+5);
        $p->addTitle('titleAnchor', 6, $this->t($p->title), '2x '.$this->t('from interfacing'));

        // Notches
        $p->newSnippet('notchRight', 'notch', 1);
        $p->newSnippet('notchLeft', 'notch', -1);

        // No seam allowance note
        $p->newPoint('noSaAnchor', $p->x(-3),$p->y(-1)-3);
        $p->newNote(1, 'noSaAnchor', $this->t("No\nseam\nallowance"), 4, 10, 0, ['line-height' => 4, 'class' => 'text']);
    }

    /**
     * Finalizes the pocket facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePocketFacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketFacing'];

        // Title
        $p->newPoint('titleAnchor', 0, $p->y(3)/2+5);
        $p->addTitle('titleAnchor', 7, $this->t($p->title), '2x '.$this->t('from main fabric'));

        // Grainline
        $p->addPoint('grainlineBottom', $p->shift(3,135,5));
        $p->addPoint('.help1', $p->shift(2,-135,5));
        $p->addPoint('grainlineTop', $p->rotate('.help1','grainlineBottom',self::POCKET_ANGLE));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Notches
        $p->newSnippet('notchRight', 'notch', 1);
        $p->newSnippet('notchLeft', 'notch', -1);

        // No seam allowance note
        $p->newPoint('noSaAnchor', $p->x(-3),$p->y(-1)+10);
        $p->newNote(1, 'noSaAnchor', $this->t("No\nseam\nallowance"), 4, 10, 0, ['line-height' => 4, 'class' => 'text']);
    }

    /**
     * Finalizes the pocket bag
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePocketBag($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketBag'];

        // Title
        $p->newPoint('titleAnchor', 0, $p->y(3)/2+5);
        $p->addTitle('titleAnchor', 8, $this->t($p->title), '2x '.$this->t('from lining'));

        // Notches
        $p->newSnippet('notchRight', 'notch', 1);
        $p->newSnippet('notchLeft', 'notch', -1);

        // No seam allowance note
        $p->newPoint('noSaAnchor', $p->x(-3),$p->y(-1)+10);
        $p->newNote(1, 'noSaAnchor', $this->t("No\nseam\nallowance"), 4, 10, 0, ['line-height' => 4, 'class' => 'text']);
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

        // Vertical measures on the left
        if($this->o('hemStyle') == 1) { // Classic hem
            $bottom = 4002;
            // Add point bottom left, and bottom at buttons
            $p->newPoint('bottomLeft', $p->x(4001), $p->y(4002));
            $p->newPoint('bottomButtons', $p->x(5050), $p->y(4002));
            // Measure help lines bottom origin (mhlBo)
            $mhlBo = 4002;
        } else { // Rounded hem
            $bottom = 4004;
            $p->newPoint('bottomLeft', $p->x(4001), $p->y(4004));
            $p->newPoint('bottomButtons', $p->x(5050), $p->y(4004));
            // Measure help lines bottom origin (mhlBo)
            $mhlBo = 4004;
        }

        $xBase = $p->x(300);
        $p->newHeightDimension($bottom, 4001, $xBase-20);  // Height to tip
        $p->newHeightDimension($bottom, 5050, $xBase-35);  // Height to first button
        // Button spacing
        for($i=1;$i<$this->o('buttons')-1;$i++) {
            $p->newHeightDimension((5000+$i), (4999+$i), $xBase-35);  // Height to next button
        }
        $p->newHeightDimension(5050, (4999+$i), $xBase-35);  // Height from bottom button
        $p->newHeightDimension($bottom, 300, $xBase-50); // Height to neck bottom of neck opening
        $p->newHeightDimension($bottom, 8, $xBase-65); // Total height

        // Horizontal measures at the bottom
        if($this->o('hemStyle') == 1) { // Classic hem
            $dartLeft = 9121;
            $dartRight = 9111;
        } else { // Rounded hem
            $dartLeft = 912;
            $dartRight = 911;
        }

        $yBase = $p->y($mhlBo);
        $p->newWidthDimension(4001, $mhlBo, $yBase+25);  // Width of the tip
        $p->newWidthDimension(4001, $dartLeft, $yBase+40); // Width to dart left side
        $p->newWidthDimension(4001, $dartRight, $yBase+55); // Width to dart right side
        $p->newWidthDimension(4001, 2912, $yBase+70); // Width to right edge

        // Horizontal measures at the top
        $yBase = $p->y(8);
        $p->newWidthDimension(300, 8, $yBase-25);  // Width of neck opening
        $p->newWidthDimension(300, 'shoulderFront', $yBase-40);  // Width of neck opening
        $p->newWidthDimension(300, 5, $yBase-55);  // Width to armhole

        $p->newLinearDimension(8, 'flbTop', -15);  // Shoulder seam to flb
        $p->newLinearDimension('flbTop', 'shoulderFront', -15);  // Flb to shoulder edge
        $p->newLinearDimension(8, 'shoulderFront', -30);  // Shoulder seam length

        // Pocket
        $p->newLinearDimension(7001, 7005, self::POCKET_HEIGHT/2);  // Half pocket width
        $p->newLinearDimension(7007, 7002, self::POCKET_HEIGHT/2);  // Half pocket width
        $p->newLinearDimension(7006, 7005, 15, $this->unit(self::POCKET_HEIGHT), ['class' => 'dimension dimension-sm'] );  // Pocket height
        $p->newHeightDimension($dartLeft, 7002, $p->x($dartLeft)+15);
        $p->newNote('pocketAngle', 7007, $this->t("Pocket is placed under angle").': '.self::POCKET_ANGLE.'&#176;', 10, 20, 0, ['line-height' => 4, 'class' => 'text']);
        $p->newNote('pocket', 7005, $this->t("Draw this half of the pocket\nafter you have closed the dart"), 12, 20, 0, ['line-height' => 5, 'class' => 'text','dy' => -2]);

        // Vertical measures at the right
        $p->newHeightDimension($bottom, $dartLeft, $p->x($dartLeft)+15);
        $p->newHeightDimension($bottom, $dartRight, $p->x($dartRight)+15);
        $xBase = $p->x(2912);
        $p->newHeightDimension($bottom, 2912, $xBase+25);
        $p->newHeightDimension($bottom, 907, $xBase+40);
        $p->newHeightDimension($bottom, 5, $xBase+55);
        $p->newHeightDimension($bottom, 'shoulderFront', $xBase+70);

        // Some extra dimensions
        $p->newLinearDimension(901,2902); // Width of right part
        $p->addPoint('3edge', $p->shift(3,180,10));
        $p->newWidthDimension('3edge', 902); // Width of left part
        $p->newLinearDimension(902, 901); // Widht of dart
        $p->newPoint('907edge', $p->x(300), $p->y(907));
        $p->newWidthDimension('907edge', 907); // Dart tip
        $p->newWidthDimension(4001,5050,$p->y(4001)+35, $this->unit(10) ,['class' => 'dimension dimension-sm']); // Button offset from edge
        $p->newWidthDimension(10,5); // Deph of armhole
        $p->newCurvedDimension('M 2912 L 2910 C 2914 2906 2902 C 2904 2907 5', 25); // Side curve

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

        // Vertical dimensions on the left
        $xBase = $p->x(4);
        $p->newHeightDimension(4000,2,$xBase-25); // Height to start of back curve
        $p->newHeightDimension(4000,1,$xBase-40); // Height to center back
        $p->newHeightDimension(4000,8,$xBase-55); // Total height

        // Dart
        $p->newHeightDimension(912,902,$p->x(912)-15); // Height to middle of dart
        $p->newHeightDimension(912,907,$p->x(912)-30); // Total dart height
        $p->newWidthDimension(902,901, false, false, ['class' => 'dimension dimension-sm']); // Dart width at middle

        // Horizontal dimensions bottom
        $yBase = $p->y(4000);
        $p->newWidthDimension(4000,910,$yBase+25); // Width to dart left side
        $p->newWidthDimension(4000,909,$yBase+40); // Width to dart right side
        $p->newWidthDimension(4000,2902,$yBase+55); // Waist width
        $p->newWidthDimension(4000,2910,$yBase+70); // Total hips width
        $p->newWidthDimension(4000,5,$yBase+85); // Total width

        // Vertical dimensions on the left
        $xBase = $p->x(5);
        $p->newCurvedDimension('M 2911 L 2910 C 2914 2906 2902 C 2904 2907 5', 25); // Side curve
        $p->newHeightDimension(2911,5,$xBase+40); // Height to armhole
        $p->newHeightDimension(2911,'shoulderBack',$xBase+55); // Height to shoulder point

        // Horizontal dimensions top
        $yBase = $p->y(8);
        $p->newWidthDimension(2,1,$yBase-25, false, ['class' => 'dimension dimension-sm']); // Width of center back curve
        $p->newWidthDimension(2,8,$yBase-40); // Width of neck opening
        $p->newWidthDimension(2,'shoulderBack',$yBase-55); // Width to shoulder

        // Shoulder seam
        $p->newLinearDimension(8,'shoulderBack', 15);
    }

    /**
     * Adds paperless info for front facing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFrontFacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontFacing'];
        $front = $this->parts['front'];

        $p->newPoint('textAnchor', $p->x('titleAnchor'), $p->y(2902));
        $p->newText('text1', 'textAnchor', $this->t("For dimensions,\nsee:").' '.$front->title, ['line-height' => 10, 'class' => 'note text-center text-xl']);
    }

    /**
     * Adds paperless info for front lining
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFrontLining($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontLining'];
        $front = $this->parts['front'];

        $p->newPoint('textAnchor', $p->x('titleAnchor'), $p->y(2902));
        $p->newText('text1', 'textAnchor', $this->t("For dimensions,\nsee:").' '.$front->title, ['line-height' => 10, 'class' => 'note text-center text-xl']);
    }

    /**
     * Adds paperless info for pocket welt
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPocketWelt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketWelt'];

        $p->newWidthDimension(-1,1, -20); // Welt line width
        $p->newWidthDimension(-2,2, -35); // Total width

        $xBase = $p->x(-2);
        $p->newHeightDimension(-3,-1, $xBase-10); // Height to welt line
        $p->newHeightDimension(-3,-2, $xBase-25); // Total height

        $p->addPoint('textAnchor', $p->shift('titleAnchor',-90,15));
        $p->newText('note2', 'textAnchor', $this->t('Grainline under the same angle as the pocket:').' '.self::POCKET_ANGLE.'&#176;', ['line-height' => 10, 'class' => 'note text-center text']);
    }

    /**
     * Adds paperless info for pocket interfacing
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPocketInterfacing($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketInterfacing'];

        $p->newWidthDimension(-1,1, -20); // Welt line width
        $p->newWidthDimension(-2,2, -35); // Total width

        $xBase = $p->x(-2);
        $p->newHeightDimension(-3,-1, $xBase-10); // Height to welt line
        $p->newHeightDimension(-3,-2, $xBase-25); // Total height
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

        $p->newWidthDimension(-1,1, -20); // Welt line width
        $p->newWidthDimension(-2,2, -35); // Total width

        $xBase = $p->x(-2);
        $p->newHeightDimension(-7,-1, $xBase-10); // Height to welt line
        $p->newHeightDimension(-7,-2, $xBase-25); // Total height

        $p->addPoint('textAnchor', $p->shift('titleAnchor',-90,15));
        $p->newText('note2', 'textAnchor', $this->t('Grainline under the same angle as the pocket:').' '.self::POCKET_ANGLE.'&#176;', ['line-height' => 10, 'class' => 'note text-center text']);

        $p->addPoint('radiusAnchor', $p->shiftAlong(-3,-4,-6,-7,11));
        $p->newNote(2, 'radiusAnchor', $this->t("Radius").': '.$p->unit($p->deltaX(-3,-7)), 2, 20, 0, ['line-height' => 4, 'class' => 'text']);
    }

    /**
     * Adds paperless info for the pocket bag
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPocketBag($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pocketBag'];

        $p->newWidthDimension(-1,1, -10); // Welt line width
        $p->newWidthDimension(-2,2, $p->y(-2)-10); // Total width

        $xBase = $p->x(-2);
        $p->newHeightDimension(-7,-1, $xBase-10); // Height to welt line
        $p->newHeightDimension(-7,-2, $xBase-25); // Total height

        $p->addPoint('radiusAnchor', $p->shiftAlong(-3,-4,-6,-7,11));
        $p->newNote(2, 'radiusAnchor', $this->t("Radius").': '.$p->unit($p->deltaX(-3,-7)), 2, 20, 0, ['line-height' => 4, 'class' => 'text']);
    }
}
