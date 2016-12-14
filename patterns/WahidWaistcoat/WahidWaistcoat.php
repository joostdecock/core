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
        
        $this->setValue('waistReduction', $waist_re);
        $this->setValue('hipsReduction', $hips_re);
        $this->setValue('scyeDart', (5 + $waist_re/10));
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
        $this->initialize($model);
        
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        $this->draftWaistcoatFrontBlock($model);
        
        $this->draftFront($model);
        $this->draftBack($model);

        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
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
        $this->frontDart = 'L 910 C 914 906 902 C 904 907 907 C 907 903 901 C 905 913 909';

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
          $hem = " L 4002 C 4006 4007 9121 L 912 ".$this->frontDart." L 9111 C 4009 4008 2912 ";
        } else { // Rounded hem
            $r = $this->o('hemRadius');
            // Let's not arc into our dart
            if($r > $p->deltaX(302,910)) $r = $p->deltaX(302,910);
            $rc = \Freesewing\BezierToolbox::bezierCircle($r);
            $p->addPoint(4001, $p->shift(302,90,$r));
            $p->addPoint(4002, $p->shift(4001,-90,$rc));
            $p->addPoint(4004, $p->shift(302,0,$r));
            $p->addPoint(4003, $p->shift(4004,180,$rc));
            $hem = "C 4002 4003 4004 L 912 ".$this->frontDart." L 911 L 2912";
        }
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
        
        
        // Paths
        $pocket = 'M 7001 L 7005 7006 7003 M 7004 L 7008 7007 7002';
        $p->newPath('pocket', $pocket, ['class' => 'helpline']);
        $outline = "M 300 L 4001 $hem L 2910 C 2914 2906 2902 C 2904 2907 5 C 13 16 14 C 15 18 10 C 17 shoulderFrontCp shoulderFront L 8 C 20 301 300 z";
        $p->newPath('seamline', $outline);

        // Grid anchor
        $p->clonePoint(302, 'gridAnchor');

        // Mark path for sample service
        $p->paths['pocket']->setSample(true);
        $p->paths['seamline']->setSample(true);
        
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
        $p = $this->parts['back'];
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


        // Paths
        $partA = 'M 1 C 1 1cp 2 L 3 L 4 ';
        $dart = 'L 910 C 914 906 902 C 904 907 907 C 907 903 901 C 905 913 909 ';
        $partB = 'L 2910 C 2914 2906 2902 C 2904 2907 5 C 13 16 14 C 15 18 10 C 17 19 shoulderBack L 8 C 20 1 1 z';
        $withDart = $partA.$dart.$partB;
        $withoutDart = $partA.$partB;
        $p->newPath('seamline', $withDart);
        $p->newPath('sa', $withoutDart);
        $p->paths['sa']->setRender(false);

        // Grid anchor
        $p->clonePoint(4, 'gridAnchor');
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }
    
}
