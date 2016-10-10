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
        $this->parts['frontBlock']->setRender(false);        
        $this->parts['backBlock']->setRender(false);        
        $this->parts['back']->setRender(false);        
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
        
        // Moving chest point because stretch
        $p->newPoint( 5, ($model->getMeasurement('chestCircumference') + $this->getOption('chestEase')) /4 * $this->getOption('stretchFactor'), $p->y(5), 'Quarter chest @ armhole depth');

        // Shoulders
        $p->newPoint( 100, $p->x(9), $p->y(1) + $this->getOption('necklineDrop'), 'Neck bottom @ CF');
        $p->addPoint( 101, $p->shiftTowards(8, 12, $p->distance(8, 12) * $this->getOption('shoulderStrapPlacement') * $this->getOption('stretchFactor')), 'Center of shoulder strap');
        $p->addPoint( 102, $p->shiftTowards(101, 12, $this->getOption('shoulderStrapWidth')/2), 'Shoulder strap edge on the shoulder side');
        $p->addPoint( 103, $p->shiftTowards(101, 8, $this->getOption('shoulderStrapWidth')/2), 'Shoulder strap edge on the neck side');
        $p->addPoint( '.help1', $p->shift(103, $p->angle(102,103)-90, 20), 'Helper point for 90 degree angle');
        $p->addPoint( '.help2', $p->shift(100, 180, 20), 'Helper point to intersect with bottom of neckline');
        $p->addPoint( 104, $p->linesCross(103, '.help1', 100, '.help2'), 'Control point for 100');
        $p->addPoint( 105, $p->shiftTowards(103, 104, $p->distance(103, 104) * $this->getOption('necklineBend')), 'Control point for 103');
        $p->addPoint( 106, $p->shift(102, $p->angle(102,103)-90, $p->deltaY(102, 5)/2), 'Control point for 102');
        $p->addPoint( 107, $p->shift(5, 0, $p->deltaX(5, 102)), 'Control point for 5');
        
        // Hips
        $p->newPoint( 110, ($model->getMeasurement('hipsCircumference')/4) * $this->getOption('stretchFactor'), $p->y(4) + $this->getOption('lengthBonus'), 'Hips @ trouser waist'); 
        $p->newPoint( 111, $p->x(1), $p->y(110), 'Hips @ CF');
        
        // Waist -> Same as hips because stretch
        $p->newPoint( 112, $p->x(110), $p->y(3), 'Side @ waist');
        $p->addPoint( 113, $p->shift(112, 90, $p->deltaY(5, 112)/3), 'Top control point for 112'); 
        
        // Armhole drop
        if($this->getOption('armholeDrop') > 0) {
            // Move point 5 along curve
            $p->addPoint( 5, $p->curveCrossesY(112, 112, 113, 5, $p->y(5)+$this->getOption('armholeDrop')));
            // Update other points accordingly
            $p->newPoint( 107, $p->x(107), $p->y(5), 'Control point for 5'); 
            $p->newPoint( 2, $p->x(2), $p->y(5), 'Center back @ armhole depth'); 
        }

        // Points for 'cut on fold' line and grainline
        $p->newPoint( 120, 0, $p->y(100) + 20, 'Cut on fold endpoint top');
        $p->newPoint( 121, 20, $p->y(120), 'Cut on fold corner top');
        $p->newPoint( 122, 0, $p->y(111) - 20, 'Cut on fold endpoint bottom');
        $p->newPoint( 123, 20, $p->y(122), 'Cut on fold corner bottom');
        $p->addPoint( 124, $p->shift(121, 0, 15), 'Grainline top');
        $p->clonePoint(124, 'gridAnchor');
        $p->addPoint( 125, $p->shift(123, 0, 15), 'Grainline bottom');
        
        // Title
        $p->newPoint('titleAnchor', $p->x(5)*0.4, $p->x(5)+40, 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title), $this->t('Cut 1 on fold'));

        // Cut on fold and grainline
        $p->newPath('cutOnFold', 'M 120 L 121 L 123 L 122', ['class' => 'grainline']);
        $p->newTextOnPath('cutonfold', 'M 123 L 121', $this->t("Cut on fold"), ['line-height' => 12, 'class' => 'text-sm', 'dy' => -2]);
        $p->newPath('grainline', 'M 124 L 125', ['class' => 'grainline']);
        $p->newTextOnPath('grainline', 'M 125 L 124', $this->t("Grainline"), ['line-height' => 12, 'class' => 'text-sm', 'dy' => -2]);
        
        // Seamline 
        $seamline = 'M 3 L 111 L 110 L 112 C 113 5 5 C 107 106 102 L 103 C 105 104 100 z';
        $p->newPath('seamline', $seamline);
    
        // Scalebox 
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 40));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Seam allowance 
        $p->offsetPath('sa', 'seamline', 10);
        $p->paths['sa']->setAttributes(['class' => 'marker']);
        $saAttr = ['class' => 'sa'];
        $p->newPath('sa-shoulder', 'M 103 L sa27 L sa26 L 102', $saAttr);
        $p->addPoint( 130, $p->shift('sa5', -90, 10)); 
        $p->addPoint( 131, $p->shift('sa7', -90, 10)); 
        $p->addPoint( 132, $p->shift(112, 0, 10)); 
        $p->newPath('sa-hemside', 'M 111 L 130 L 131 L 132 C sa9 sa11 sa11 L 5', $saAttr);

        $noteAttr = ['line-height' => 6, 'class' => 'text-sm']; 
        $p->newNote(1, 101,  $this->t("Standard\nseam\nallowance"), 6, 10, -5, $noteAttr );
        $p->newNote(2, 'sa40',  $this->t("HERENo\nseam\nallowance"), 5, 25, 10, $noteAttr );
        $p->newNote(3, 'sa20',  $this->t("No\nseam\nallowance"), 7, 25, 10, $noteAttr );
        $p->newNote(4, 132,  $this->t("Standard\nseam\nallowance"), 9, 25, 5, $noteAttr );
        $p->newPoint( 'note5', $p->x(110)/2, $p->y(110));
        $p->newNote(5, 'note5',  $this->t("Hem\nallowance")."\n(".$this->unit(20).')', 12, 25, -10, ['line-height' => 6, 'dy' => -20, 'class' => 'text-sm'] );
    
    
    }

}
