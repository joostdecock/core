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
     * Add parts in config file to pattern
     *
     * I override the pattern's class loadParts() here
     * because I want to not render the blocks that we're
     * getting from the parent.
     */
    public function loadParts()
    {
        foreach ($this->config['parts'] as $part => $title) {
            $this->addPart($part);
            $this->parts[$part]->setTitle($title);
        }
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
        $this->buildCore($model);
        $this->draftFrontPartA($model);
        $this->draftBack($model);
        $this->draftFrontPartB($model);

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
        $this->buildCore($model);

    }

    /**
     * Drafts the blocks this is based on
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function buildCore($model)
    {
        $this->setDefaults($model);
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        $this->draftWaistcoatFrontBlock($model);
    }

    /**
     * Presets some defaults
     *
     * I'm setting these as options. This way, we could always make them
     * options later, and the pattern will not require any changes
     * apart from in this method
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function setDefaults($model)
    {
        // These could in principle be options
        $this->setOption('pocketWidth', 90);
        $this->setOption('pocketHeight', 15);
        $this->setOption('pocketAngle', -5);
        $this->setOption('armholeDepth', 290 + ($model->m('shoulderSlope')/2-27.5) + ($model->m('bicepsCircumference')/10));

        // Some helper vars
        $chest = $model->m('chestCircumference') + $this->o('chestEase');
        $waist = $model->m('waistCircumference') + $this->o('waistEase');
        $hips  = $model->m('hipsCircumference')  + $this->o('hipsEase');
        $waist_re = $chest - $waist;
        $hips_re = $chest - $hips;
        if ($hips_re <= 0) {
            $hips_re = 0;
        }
        if ($waist_re <= 0) {
            $waist_re = 0;
        }
        $this->waistReduction = $waist_re;
        $this->hipsReduction = $hips_re;
        $this->scyeDart =  5+$waist_re/10;
        
        // These is irrelevant, but needed for JoostBodyBlock
        $this->setOption('collarEase', 15);

        // JoostBodyBlock stores these in $this->help, so we need this
        $this->help = array();
        $this->help['armholeDepth'] = $this->o('armholeDepth');
        $this->help['sleevecapShapeFactor'] = 1;
        $this->help['collarShapeFactor'] = 1;
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
        $p = $this->parts['waistcoatFrontBlock'];

        // Neck cutout
        $overlap = 10; // 1cm passed center front
        $p->newPoint(  300, $p->x(1)-$overlap, $p->y(5) - 80 + $this->o('frontDrop'), 'Neck cutout base point');
        if ($this->o('frontStyle') == 1) {
            $p->newPoint(  301, $p->x(8), $p->y(300), 'Neck cutout control point');
        } else {
            $p->newPoint(  301, $p->x(8), $p->y(9), 'Neck cutout control point');
        }
  
        //Hem
        $p->newPoint(  302, $p->x(4)-$overlap, $p->y(4)+$this->o('lengthBonus'), 'Bottom edge');

        // Dart units
        $w8th = $this->waistReduction/8;
        $h8th = $this->hipsReduction/8;
      
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
        $p->addPoint(  909 , $p->shift(908,0, $h8th/2));
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
    public function draftFrontPartA($model)
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
        $pw = $this->o('pocketWidth');
        $ph = $this->o('pocketHeight');
        $pa = $this->o('pocketAngle'); 
        $p->newPoint(7000, $p->x(900),$p->y(900)+$p->deltaY(900,4001)*0.6-$ph/2);
        $p->curveCrossesY(901,905,913,909,$p->y(7000), 700); // Creates point 7001
        $p->addPoint(7002, $p->flipX(7001,$p->x(7000)));
        $p->addPoint(7003, $p->shift(7000,-$pa,$pw/2));
        $p->addPoint(7004, $p->shift(7003,-$pa-90,$ph));
        $p->addPoint(7005, $p->shift(7000,180-$pa,$pw/2));
        $p->addPoint(7006, $p->shift(7005,-$pa-90,$ph));
        $p->curveCrossesY(901,905,913,909,$p->y(7000)+$ph, 701); // Creates point 7011
        $p->addPoint(7012, $p->flipX(7011,$p->x(7000)));

        // Make Front shoulder 1cm more sloped
        $p->addPoint('shoulderFront', $p->shiftAlong(12,19,17,10,10));
        $p->addPoint('shoulderFrontCp', $p->shift('shoulderFront',$p->angle(8,'shoulderFront')+90,10));
        
        // Front scye dart
        $p->newPoint(2000, $p->x(5), $p->y(10));
        $p->curveCrossesLine(14,15,18,10,907,2000,200); // Creates point 2001
        $p->addPoint(2002, $p->shift(2001,$p->angle(907,2000)-90,$this->o('frontScyeDart')));
        $angle = $p->angle(907,2000) - $p->angle(907,2002);
        $this->msg("angle is $angle");
        $torotate = array(15,14,16,13,5,2907,2904,2902,2906,2914,2910,2912,911,909,913,905,901,903,7001,7003,7004,7011);
        if($this->o('hemStyle')==1) { // Classic hem
            $torotate[] = 4008;
            $torotate[] = 4009;
            $torotate[] = 9111;
        } 
        // Rotate front scye dart into front dart
        foreach($torotate as $rp) $p->addPoint($rp, $p->rotate($rp,907,-$angle));
        
        
        // Paths
        $pocket = 'M 7001 L 7003 7004 7011 M 7012 L 7006 7005 7002';
        $p->newPath('pocket', $pocket, ['class' => 'helpline']);
        $path = 'M 300 L 302 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 301 300 z M 4001 L 4002';
        $p->newPath('help', $path, ['class' => 'seam-allowance']);
        $outline = "M 300 L 4001 $hem L 2910 C 2914 2906 2902 C 2904 2907 5 C 13 16 14 C 15 18 10 C 17 shoulderFrontCp shoulderFront L 8";
        $p->newPath('seamline', $outline);

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

        // Clone dart points from waistcoatFrontBlock
        $points = array(912,910,914,906,902,904,907,903,901,905,913,909,911,2910,2914,2906,2902,2904,2907);
        foreach($points as $i) $p->newPoint($i, $wfb->x($i), $wfb->y($i));

        // Make back shoulder 1cm more sloped
        $p->addPoint( 12, $p->shift(12,$p->angle(8,12)+90,10));
        
        // Bring 1cm from shoulder to front
        $p->addPoint( 'backShoulder', $p->shift(12,$p->angle(8,12)+90,10));
        $p->addPoint( 'backShoulderCp', $p->shift(12,$p->angle(8,12)+90,20));
        $p->addPoint( '.help', $p->shift(8,$p->angle(8,12)+90,10));
        $p->addPoint( '.help', $p->shift('.help',$p->angle(8,12),10));
        $p->curveCrossesLine(8,20,1,1,'backShoulder','.help', 'backNeck');
        $points = $p->splitCurve(8,20,1,1,'backNeck1');
        $p->addPoint('backNeck2', $points[1]);
        $p->addPoint('backNeck3', $points[2]);
        $p->addPoint('backNeck4', $points[6]);
        $p->addPoint('backNeck5', $points[7]);


        // Paths
        $path = 'M 1 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 1 1 z';
        $p->newPath('seamline', $path);

    }
    
    /** 
     * Drafts the waistcoat front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontPartB($model)
    {
        $p = $this->parts['front'];
        $b = $this->parts['back'];

        // Clone shoulder points from back
        $points = array('backShoulder', 'backNeck1','backNeck2', 'backNeck3');
        foreach($points as $i) $p->newPoint($i, $b->x($i), $b->y($i));

        // Put them in the right place
        $p->addPoint('backShoulder', $p->rotate('backShoulder','shoulderFront',180));
        $p->addPoint('backShoulderCp', $p->shift('backShoulder',$p->angle(8,'shoulderFront')+90,10));

    }
}
