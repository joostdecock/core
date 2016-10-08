<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\AidenAshirt class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class AidenAshirt extends JoostBodyBlock
{
    public function draft($model)
    {
        $this->loadHelp($model);
        
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        $this->draftFront($model);
    }

    public function draftBack($model)
    {
        $this->clonePoints('backBlock', 'back');
        $p = $this->parts['back'];

        // Paths
        $path = 'M 1 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 1 1 z';
        $p->newPath('outline', $path);
         
        // Drop back neck 0.5 cm
        $p->newPoint( 1 , $p->x(1), $p->y(1)+5, 'Center back @ neck');
        
/*
      // Bring over points
      foreach(array('ShoulderStrapCenter','ShoulderStrapRight','ShoulderStrapLeft','WaistPoint','WaistPointcpa','WaistPointcpb','HipsPointcp','HipsPoint','HemPoint','HemCF') as $id) addPntAr("Back$id", xMirror($id,px(-5)));
      addPntAr('helper1', rotate('BackShoulderStrapCenter','BackShoulderStrapLeft',90));
      addPoint('helper2', 0,py(-13));
      addPntAr('BackShoulderStrapLeftMax', lineIsect('helper1','BackShoulderStrapLeft',-13,'helper2'));
      addPntAr('BackShoulderStrapLeftcp', hopShift('BackShoulderStrapLeftMax',-13,hopLen('BackShoulderStrapLeftMax',-13)*0.4));
      addPntAr('helper1', rotate('BackShoulderStrapCenter','BackShoulderStrapRight',-90));
      if(getp('ARMHOLE_DROP') > 0) {
        // Move point -5 along curve
        addPntAr('old-5', pxy(-5));
        addPntAr(-5, arcYsect(-5,-5,'BackWaistPointcpa','BackWaistPoint',py(-5)+getp('ARMHOLE_DROP')));
        // Update -2 accordingly
        addPoint(-2, px(-2),py(-2)+getp('ARMHOLE_DROP'));
      }
      addPntAr('BackShoulderStrapRightMax', lineIsect('helper1','BackShoulderStrapRight',-5,-2));
      // BACKLINE_BEND should stay between 0.5 and 0.9, so let's make sure of that.
      $backlineBend = 0.5+getp('BACKLINE_BEND')*0.4;
      addPntAr('BackShoulderStrapcp1', hopShift('BackShoulderStrapRight','BackShoulderStrapRightMax',hopLen('BackShoulderStrapRight','BackShoulderStrapRightMax')*$backlineBend));
      addPntAr('BackShoulderStrapcp2', hopShift(-5,'BackShoulderStrapRightMax',hopLen(-5,'BackShoulderStrapRightMax')*$backlineBend));
      // Helper points for cut-on-fold (cof) line
      addPntAr('cof1', pShift(-13,-90,20));
      addPntAr('cof2', pShift('cof1',180,20));
      addPntAr('cof3', pShift('BackHemCF',90,20));
      addPntAr('cof4', pShift('cof3',180,20));
      // Helper points for grainline (gl)
      addPntAr('gl1', pShift('cof2',180,40));
      addPntAr('gl2', pShift('cof4',180,40));
      // Anchor points for ribbing text
      addPntAr('text', pShift(-5,-45,75));
      $armholeLengthBack = arclen('BackShoulderStrapRight','BackShoulderStrapcp1','BackShoulderStrapcp2',-5);
      $neckholeLengthBack = arclen(-13,'BackShoulderStrapLeftcp','BackShoulderStrapLeftMax','BackShoulderStrapLeft');
      setp('ARMHOLE_LEN', round(getp('ARMHOLE_LEN_FRONT')/10 + $armholeLengthBack/10,1));
      setp('NECKHOLE_LEN', round((getp('NECKHOLE_LEN_FRONT')/10 + $neckholeLengthBack/10)*2,1));
      addText('
TO FINISH ARMHOLES AND NECK OPENING:|
====================================|
Cut two 6cm wide and '.round(getp('ARMHOLE_LEN'),0).'cm long strips to finish the armholes|
Cut one 6cm wide and '.round(getp('NECKHOLE_LEN'),0).'cm long trip to finish the neck opening','text');
      // No seam allowance at neck and armhole
      // This requires some extra points to draw paths 
      addPntAr('5sa1',arcShift(-5,-5,'BackWaistPointcpa','BackWaistPoint',10));
      addPntAr('5sa2',pShift('5sa1',0,100));
      addPntAr('BackHemCFsa1',pShift('BackHemCF',180,10));
      addPntAr('BackHemCFsa2',pShift('BackHemCFsa1',90,100));
      addPntAr('BackShoulderStrapLeftsa', pShift('BackShoulderStrapLeft',angle('BackShoulderStrapLeftMax','BackShoulderStrapLeft')+180,10));
      addPntAr('BackShoulderStrapRightsa', pShift('BackShoulderStrapRight',angle('BackShoulderStrapcp1','BackShoulderStrapRight')+180,10));
      addPntAr('BackHemCFsa3',pShift('BackHemCF',-90,20));
      addPntAr('BackHemPointsa1',pShift('BackHemPoint',-90,20));
      addPntAr('BackHemPointsa1',pShift('BackHemPointsa1',180,10));
      addPntAr('BackHemPointsa2',pShift('BackHemPoint',180,10));
*/

    }

    public function draftFront($model)
    {
        $this->clonePoints('frontBlock', 'front');
        $p = $this->parts['front'];
        
        // Shoulders
        $p->newPoint( 100, $p->x(9), $p->y(1) + $this->getOption('necklineDrop'), 'Neck bottom @ CF');
        $p->addPoint( 101, $p->shiftTowards(8, 12, $p->distance(8, 12) * $this->getOption('shoulderStrapPlacement') * $this->getOption('stretchFactor')), 'Center of shoulder strap');
        $p->addPoint( 102, $p->shiftTowards(101, 12, $this->getOption('shoulderStrapWidth')/2), 'Shoulder strap edge on the shoulder side');
        $p->addPoint( 103, $p->shiftTowards(101, 8, $this->getOption('shoulderStrapWidth')/2), 'Shoulder strap edge on the neck side');
        $p->addPoint( '.help1', $p->shift(103, $p->angle(102,103)+90, 20), 'Helper point for 90 degree angle');
        $p->addPoint( '.help2', $p->shift(100, 180, 20), 'Helper point to intersect with bottom of neckline');
        $p->addPoint( 104, $p->linesCross(103, '.help1', 100, '.help2'), 'Control point for 100');
        $p->addPoint( 105, $p->shiftTowards(103, 104, $p->distance(103, 104) * $this->getOption('necklineBend')), 'Control point for 103');
        $p->addPoint( 106, $p->shift(102, $p->angle(102,103)+90, $p->deltaY(102, 5)/2), 'Control point for 102');
        $p->addPoint( 107, $p->shift(5, 0, $p->deltaX(5, 102)), 'Control point for 5');
        
        // Hips
        $p->newPoint( 110, $p->x(1) - ($model->getMeasurement('hipsCircumference')/4) * $this->getOption('stretchFactor'), $p->y(4) + $this->getOption('lengthBonus'), 'Hips @ trouser waist');   
        $p->newPoint( 111, $p->x(1), $p->y(110), 'Hips @ CF');
/*

      // Hips
      addPoint('HipsPoint', mgetp('QuarterHips'), py(200));
      addPoint('HemPoint', px('HipsPoint'),py('HipsPoint') + getp('LENGTH_BONUS'));
      addPntAr('HipsPointcp', pShift('HipsPoint',90,yDist('WaistPoint','HipsPoint')/3));
      addPoint('HemCF', px(0),py('HemPoint'));
      // Waist
      // Actually, screw the waist. This is typically stretch, and it doesn't look nice to come in at the waist
      addPoint('WaistPoint', px('HipsPoint'), py(3));
      addPntAr('WaistPointcpa', pShift('WaistPoint',90,yDist(-5,-7)/3));
      addPntAr('WaistPointcpb', pShift('WaistPoint',-90,yDist(-7,201)/2));
      addPntAr('5cp', pShift(5,-90,yDist(-5,-7)/3));
      // Armhole drop
      if(getp('ARMHOLE_DROP') > 0) {
        // Move point 5 along curve
        addPntAr('old5', pxy(5));
        addPntAr(5, arcYsect(5,5,'WaistPointcpa','WaistPoint',py(-5)+getp('ARMHOLE_DROP')));
        // Update other points accordingly
        addPoint(2, px(2),py(2)+getp('ARMHOLE_DROP'));
        addPntAr('ShoulderStrapRightcpb',pShift('ShoulderStrapRightcpb',angle('old5',5)+180,hopLen('old5',5)));
        addPntAr('ShoulderX', lineIsect('ShoulderStrapRight','ShoulderX',5,2));
      addPntAr('ShoulderStrapRightcpa', hopShift('ShoulderStrapRight','ShoulderX',hopLen('ShoulderStrapRight','ShoulderX')/2));
      // Helper points for cut-on-fold (cof) line
      addPntAr('cof1', pShift('NeckBottom',-90,20));
      addPntAr('cof2', pShift('cof1',0,20));
      addPntAr('cof3', pShift('HemCF',90,20));
      addPntAr('cof4', pShift('cof3',0,20));
      // Helper points for grainline (gl)
      addPntAr('gl1', pShift('cof2',0,40));
      addPntAr('gl2', pShift('cof4',0,40));
      // Anchor points for scalebox/helplink
      addPntAr('scalebox', pShift(50,-110,75));
      addPntAr('helplink', pShift('scalebox',-90,75));
      $armholeLengthFront = arclen('ShoulderStrapRight','ShoulderStrapRightcpa','ShoulderStrapRightcpb',-5);
      $neckholeLengthFront = arclen('ShoulderStrapLeft','ShoulderStrapLeftcpa','ShoulderStrapLeftcpb',-5);
      setp('ARMHOLE_LEN_FRONT', $armholeLengthFront);
      setp('NECKHOLE_LEN_FRONT', $neckholeLengthFront);
      // No seam allowance at neck and armhole
      // This requires some extra points to draw paths 
      addPntAr('5sa1',arcShift(5,'5','WaistPointcpa','WaistPoint',10));
      addPntAr('5sa2',pShift('5sa1',180,100));
      addPntAr('HemCFsa1',pShift('HemCF',0,10));
      addPntAr('HemCFsa2',pShift('HemCFsa1',90,100));
      addPntAr('ShoulderStrapLeftsa', pShift('ShoulderStrapLeft',angle('ShoulderStrapLeftMax','ShoulderStrapLeft')+180,10));
      addPntAr('ShoulderStrapRightsa', pShift('ShoulderStrapRight',angle('ShoulderStrapRightcpa','ShoulderStrapRight')+180,10));
      addPntAr('HemCFsa3',pShift('HemCF',-90,20));
      addPntAr('HemPointsa1',pShift('HemPoint',-90,20));
      addPntAr('HemPointsa1',pShift('HemPointsa1',0,10));
      addPntAr('HemPointsa2',pShift('HemPoint',0,10));
     */
        $path = 'M 110 L 111 M 9 L 2 L 3 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 21 9 z';
        $p->newPath('xoutline', $path);
        
        $path = 'M 9 L 2 L 3 L 4 L 6 L 5 C 107 106 102 L 103 C 105 104 100 z';
        $p->newPath('outline', $path);
    
    }

}
