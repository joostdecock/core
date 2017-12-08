<?php
/** PenelopePencilSkirt class */
namespace Freesewing\Patterns\Beta;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/**
 * The Penelope Pencil Skirt pattern
 *
 * @author Wouter van Wageningen
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class PenelopePencilSkirt extends \Freesewing\Patterns\Core\Pattern
{

    /** Constant for the offset of the paperless dimensions */
    const OFFSET = 15;
    
    private $sideSeamLength;

    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

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
        //$this->dartCalc( 1000, 600 );
        //$this->dartCalc( 1000, 650 );
        //$this->dartCalc( 1000, 700 );
        //$this->dartCalc( 1000, 750 );
        //$this->dartCalc( 1000, 800 );
        //$this->dartCalc( 1000, 850 );
        //$this->dartCalc( 1000, 900 );
        //$this->dartCalc( 1000, 950 );
        
        $hipWaistDiff = max( $model->m('hipsCircumference') - $model->m('naturalWaist'), 0);
        
        // Some helper vars
        $this->setValue('hipWaistDiff', $hipWaistDiff );
        $this->setValue('nrOfDarts', $hipWaistDiff>200 ? 2 : 1 );
        
        $this->setValue('backDartSize', (20 + ($hipWaistDiff -100 -(($hipWaistDiff -100)/5))/4)/$this->v('nrOfDarts'));
        $this->setValue('frontDartSize',(12 + ((max(min($hipWaistDiff, 300), 180) -180) /4))/$this->v('nrOfDarts'));

        $this->msg("hipWaistDiff"); 
        $this->msg($hipWaistDiff); 

        $this->msg("nrOfDarts"); 
        $this->msg($this->v('nrOfDarts')); 

        $this->msg("frontDartSize"); 
        $this->msg($this->v('frontDartSize')); 
        $this->msg("backDartSize"); 
        $this->msg($this->v('backDartSize')); 
    }

    public function dartCalc( $hip, $waist )
    {
        $hipWaistDiff = $hip - $waist;
        
        // Some helper vars
        $nrOfDarts = $hipWaistDiff>200 ? 2 : 1 ;
        
        $backDartSize = 20 + ($hipWaistDiff -100 -(($hipWaistDiff -100)/5)) /4 ;
        $frontDartSize = 12 + ((max(min($hipWaistDiff, 300), 180) -180) /4);

        $this->msg("$hip, $waist, $hipWaistDiff, $nrOfDarts, $frontDartSize, $backDartSize"); 
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
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        $this->initialize($model);

        $this->draftFront($model);
        $this->draftBack($model);
        if( $this->o('waistBand') ) {
            $this->draftWaistBand($model);
        }
    }

    /**
     * Generates a draft of the pattern
     *
     * This creates a draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bells and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        $this->sample( $model );

        $this->finalizePart( $model, 1, 'Front' );
		$this->finalizePart( $model, 1, 'Back' );

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            #$this->paperlessBowTie( $model, 'bowTie1' );
        }
    }

    /**
     * Drafts a basic bow tie shape for all options
     *
     * @param string $part for the (internal) name of the part
     * @param float $shift for the shift in the x direction of the bow shape
     *
     * @return void
     */
    public function draftFront($model)
    {
        $this->draftPart($model, 'front', $this->v('frontDartSize'), $this->o('waistEase'), 0, $this->o('frontDartDepthFactor'));
    }
    public function draftBack($model)
    {
        $this->draftPart($model, 'back', $this->v('backDartSize'), 0, $this->o('hipEase'), $this->o('backDartDepthFactor'));
    }

    public function draftPart($model, $part, $dartSize, $waistEase, $hipEase, $dartDepthFactor)
    {
        $p = $this->parts[$part];
        
        $pathString = 'M ';
        
        $waist = $model->m('naturalWaist');
        $hips  = $model->m('hipsCircumference') > $waist ? $model->m('hipsCircumference') : $waist;
        $hips += $hipEase;
        $waist += $waistEase;

        $p->newPoint('pA1',        0, 0, 'Origin');

        $p->newPoint('pB1',        0, $model->m('naturalWaistToKnee') );
        $p->newPoint('pA2',  $hips/4, 0 );
        $p->newPoint('pB2',  $hips/4, $model->m('naturalWaistToKnee') );
        $p->newPoint('pC1',        0, $model->m('naturalWaistToHip') );
        $p->newPoint('pC2',  $hips/4, $model->m('naturalWaistToHip') );
        $p->newPoint('pA2c', $hips/4, $model->m('naturalWaistToHip')/3 );
        $p->newPoint('pC2c', $hips/4, ($model->m('naturalWaistToHip')/3)*2 );

        
        $waistFactor = 0.99;
        $wdelta = 1;
        $sideFactor = 0.99;
        $sdelta = 1;
        $iteration = 0;
        do {
            if($wdelta < -1) {
                $waistFactor *= 0.9995;
            } else if($wdelta > 1){
                $waistFactor *= 1.01;
            }
            if($sdelta < -1) {
                $sideFactor *= 0.9995;
            } else if($sdelta > 1){
                $sideFactor *= 1.01;
            }

            $p->addPoint('pZ2t1', $p->shift('pA1',    0, ($waist/4)*$waistFactor));
            $p->addPoint('pZ2t2', $p->shift('pZ2t1',  0, $dartSize*$this->v('nrOfDarts')));
            $p->addPoint('pZ2',   $p->shift('pZ2t2', 90, 16 * $sideFactor));
            $p->addPoint('pA1c',  $p->shift('pA1',    0, $hips/12));
            $p->addPoint('pZ2c',  $p->shift('pZ2', $p->angle('pZ2', 'pA2c') -90, $waist/16));
     
			$this->addDartToCurve( $p, 'pA1', 'pA1c', 'pZ2c', 'pZ2', ($hips/4) /2.4, $dartSize, $model->m('naturalWaistToHip') * $dartDepthFactor, 'Dart1_' );
			
			$waistLength = $p->curveLen( 'Dart1_1', 'Dart1_2', 'Dart1_3', 'Dart1_4' ); 
			
            if( $this->v('nrOfDarts') > 1 ) {
				$this->addDartToCurve( $p, 'Dart1_5', 'Dart1_6', 'Dart1_7', 'Dart1_8', 32, $dartSize, $model->m('naturalWaistToHip') * $dartDepthFactor * 0.80, 'Dart2_' );
                $waistLength += $p->curveLen( 'Dart2_1', 'Dart2_2', 'Dart2_3', 'Dart2_4' ); 
                $waistLength += $p->curveLen( 'Dart2_5', 'Dart2_6', 'Dart2_7', 'Dart2_8' ); 
                
                $p->clonePoint( 'Dart2_8', 'pTopRight' );
            } else {
                $waistLength += $p->curveLen( 'Dart1_5', 'Dart1_6', 'Dart1_7', 'Dart1_8' ); 

                $p->clonePoint( 'Dart1_8', 'pTopRight' );
            }                
            
            $wdelta = ($waist/4) - $waistLength;
            
            if( $part == 'front' ) {
                $sdelta = 0;
            } else {
                $sideSeamLength = $p->curveLen( 'pTopRight', 'pA2c', 'pC2c', 'pC2' ); 
                $sideSeamLength += $p->distance( 'pC2', 'pB2' );
                
                $sdelta = $this->sideSeamLength - $sideSeamLength;
            }
                
			$this->msg("[$iteration] Delta is: $wdelta ($waistFactor) $sdelta ($sideFactor)");
			//printf("[$iteration] Delta is: $delta ($waistFactor)\n");
        } while ((abs($wdelta) > 1 || abs($sdelta) > 1) && $iteration++ < 100);

        if( $iteration >= 100 ) {
            die('oh shit');
        }

        if( $part == 'front' ) {
            $sideSeamLength = $p->curveLen( 'pTopRight', 'pA2c', 'pC2c', 'pC2' ); 
            $sideSeamLength += $p->distance( 'pC2', 'pB2' );
            
            $this->sideSeamLength = $sideSeamLength;
            
            $this->msg( "Front length: $sideSeamLength" );
        } else {
            $this->msg( "Back length: $sideSeamLength" );
        }
            
        
        $pathString .= 'Dart1_1 C Dart1_2 Dart1_3 Dart1_4 L Dart1_Bottom L ';
        
        if( $this->v('nrOfDarts') > 1 ) {
            $pathString .= 'Dart2_1 C Dart2_2 Dart2_3 Dart2_4 L Dart2_Bottom L ';
            $pathString .= 'Dart2_5 C Dart2_6 Dart2_7 pTopRight ';
        } else {
            $pathString .= 'Dart1_5 C Dart1_6 Dart1_7 pTopRight ';
        }
        
        $pathString .= 'C pA2c pC2c pC2 L pB2 L pB1 L pC1 Z';
        
        $p->newPath('outline', $pathString, ['class' => 'fabric']);
		
        if( $part == 'front' || $this->o('zipper') == 'side' ) {
            $p->newPath('outlineSA', str_replace( 'Dart1_Bottom L ','', str_replace( 'Dart2_Bottom L ','', str_replace( 'L pC1 Z','',$pathString ))), ['class' => 'hidden']);
        } else {
            $p->newPath('outlineSA', str_replace( 'Dart1_Bottom L ','', str_replace( 'Dart2_Bottom L ','', $pathString )), ['class' => 'hidden']);
        }
        
        // Mark for sampler
        $p->paths['outline']->setSample(true);
        $p->paths['outlineSA']->setSample(false);
    }


    public function pPoint( $pp, $point )
    {
        $xx = $pp->x($point); 
        $yy = $pp->y($point); 
        $this->msg("$point: [$yy,$xx]"); 
		printf("$point: [$yy,$xx]\n");
    }

    public function addDartToCurve( $p, $p1, $p2, $p3, $p4, $distance, $dartSize, $dartDepth, $prefix )
	{
		$p->addPoint('DTC_dartMiddle', $p->shiftAlong($p1, $p2, $p3, $p4, $distance));
		
		$p->splitCurve($p1, $p2, $p3, $p4, 'DTC_dartMiddle', 'DTC_SC1_');
		
		$p->addPoint('DTC_D1l', $p->shiftAlong('DTC_SC1_4', 'DTC_SC1_3', 'DTC_SC1_2', 'DTC_SC1_1', $dartSize /2));
		$p->addPoint('DTC_D1r', $p->shiftAlong('DTC_SC1_8', 'DTC_SC1_7', 'DTC_SC1_6', 'DTC_SC1_5', $dartSize /2));

		$p->splitCurve('DTC_SC1_1', 'DTC_SC1_2', 'DTC_SC1_3', 'DTC_SC1_4', 'DTC_D1l', 'DTC_LeftCurve_');
		$p->splitCurve('DTC_SC1_8', 'DTC_SC1_7', 'DTC_SC1_6', 'DTC_SC1_5', 'DTC_D1r', 'DTC_RightCurve_');
		
		$p->addPoint('DTC_D1b', $p->shift('DTC_dartMiddle', $p->angle('DTC_LeftCurve_4', 'DTC_RightCurve_8') -90, $dartDepth));

		$angleToCS = $p->angle('DTC_LeftCurve_4', 'DTC_D1b') -90;
		$p->addPoint('DTC_D1l3a', $p->shift('DTC_LeftCurve_4', $angleToCS, $p->distance('DTC_LeftCurve_3', 'DTC_LeftCurve_4')));

		$angleToSS = $p->angle('DTC_RightCurve_8', 'DTC_D1b') +90;
		$p->addPoint('DTC_D1r7a', $p->shift('DTC_RightCurve_8', $angleToSS, $p->distance('DTC_RightCurve_7', 'DTC_RightCurve_8')));
		
		$p->clonePoint('DTC_LeftCurve_1', ($prefix)."1");
		$p->clonePoint('DTC_LeftCurve_2', ($prefix)."2");
		$p->clonePoint('DTC_D1l3a', ($prefix)."3");
		$p->clonePoint('DTC_LeftCurve_4', ($prefix)."4");
		$p->clonePoint('DTC_RightCurve_5', ($prefix)."8");
		$p->clonePoint('DTC_RightCurve_6', ($prefix)."7");
		$p->clonePoint('DTC_D1r7a', ($prefix)."6");
		$p->clonePoint('DTC_RightCurve_8', ($prefix)."5");
		$p->clonePoint('DTC_D1b', ($prefix)."Bottom");
		
		unset($p->points['DTC_dartMiddle']);
		unset($p->points['DTC_D1l']);
		unset($p->points['DTC_D1r']);
		unset($p->points['DTC_D1l3a']);
		unset($p->points['DTC_D1r7a']);
		unset($p->points['DTC_SC1_1']);
		unset($p->points['DTC_SC1_2']);
		unset($p->points['DTC_SC1_3']);
		unset($p->points['DTC_SC1_4']);
		unset($p->points['DTC_SC1_5']);
		unset($p->points['DTC_SC1_6']);
		unset($p->points['DTC_SC1_7']);
		unset($p->points['DTC_SC1_8']);
		unset($p->points['DTC_LeftCurve_1']);
		unset($p->points['DTC_LeftCurve_2']);
		unset($p->points['DTC_LeftCurve_3']);
		unset($p->points['DTC_LeftCurve_4']);
		unset($p->points['DTC_LeftCurve_5']);
		unset($p->points['DTC_LeftCurve_6']);
		unset($p->points['DTC_LeftCurve_7']);
		unset($p->points['DTC_LeftCurve_8']);
		unset($p->points['DTC_RightCurve_1']);
		unset($p->points['DTC_RightCurve_2']);
		unset($p->points['DTC_RightCurve_3']);
		unset($p->points['DTC_RightCurve_4']);
		unset($p->points['DTC_RightCurve_5']);
		unset($p->points['DTC_RightCurve_6']);
		unset($p->points['DTC_RightCurve_7']);
		unset($p->points['DTC_RightCurve_8']);
		unset($p->points['DTC_D1b']);
	}

    /**
     * Drafts a rectangle to be placed between the bow tie shapes
     *
     * @return void
     */
    public function draftWaistBand($model)
    {
        $p = $this->parts['waistBand'];
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
     * Finalizes the basic bow tie shape for all options
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function finalizePart($model, $partNumber, $partName)
    {
        $p = $this->parts[ strtolower($partName) ];

        //$p->newPoint('grainlineLeft', $p->x('Origin') +30, $p->y('Origin') );
        //$p->newPoint('grainlineRight', $p->x(4), $p->y('Origin') );

        //$p->newGrainline('grainlineLeft', 'grainlineRight', $this->t('Grainline'));

        //if( $this->o('sa') ) {
            $p->offsetPath('seamAllowance', 'outlineSA', 16, true, ['class' => 'fabric sa'] );
            
            // Need to add $p->setPathString()
        //}

        // Title
        $p->newPoint('titleAnchor', $model->m('naturalWaist')/6, $model->m('naturalWaistToHip'), 'Title point');
        $p->addTitle('titleAnchor', $partNumber, $partName, '2x '.$this->t('from fabric'), 'Default');

		// Scalebox
		//$p->newPoint('scaleboxAnchor', $p->x('Origin') +50, $p->y('r7') +30);
		//$p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

		// logo
		//$p->addPoint('logoAnchorHelper', $p->shift(8, 180, 25));
		//$p->addPoint('logoAnchor', $p->shift('logoAnchorHelper', 270, 6));
		//$p->newSnippet('logo', 'logo-sm', 'logoAnchor');

    }

    public function finalizeCollarBand($model)
    {
        $p = $this->parts['collarBand'];

        $p->newPoint('grainlineLeft', $p->x('Origin') +130, $p->y('Origin') );
        $p->newPoint('grainlineRight', $p->x(3) -20, $p->y('Origin') );

        $p->newGrainline('grainlineLeft', 'grainlineRight', $this->t('Grainline'));

        if( $this->o('sa') ) {
            $p->offsetPath('seamAllowance', 'outline', $this->o('sa'), true, ['class' => 'fabric sa'] );
        }

        $p->newPoint('titleAnchor', $p->x('Origin') +50, $p->y('Origin') +5, 'Title point');

        // Title
        $p->addTitle('titleAnchor', ($this->o('adjustmentRibbon') ? 4 : 2), $this->t($p->title), '2x '.$this->t('from fabric').', 2x '.$this->t('from interfacing'), 'extrasmall');
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
     * Paperless instructions for the Interfacing Tip
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param \Freesewing\Part $p The part object
     *
     * @return void
     */
    public function paperlessBowTie($model, $part)
    {
        $p = $this->parts[$part];

        $bowStyle = $this->o('bowStyle');
        $butterfly = ($bowStyle == 'butterfly' || $bowStyle == 'diamond');

        $p->newWidthDimension('r2','r3',$p->y('r7')+self::OFFSET);
        $p->newWidthDimension('r3','r4',$p->y('r7')+self::OFFSET);
        $p->newHeightDimension(2,'r2',$p->x(2)-self::OFFSET);

        if( $butterfly ) {
            $p->newWidthDimension('r4','r5',$p->y('r7')+self::OFFSET);
            $p->newWidthDimension('r5','r6',$p->y('r7')+self::OFFSET);
            $p->newWidthDimension('r6','r7',$p->y('r7')+self::OFFSET);
            $p->newHeightDimension(5,'r5',$p->x(5));
            $p->newHeightDimension(6,'r6',$p->x(6));
            $p->newHeightDimension(7,'r7',$p->x(8)+self::OFFSET);
        } else {
            $p->newWidthDimension('r4','r7',$p->y('r7')+self::OFFSET);
            $p->newHeightDimension(7,'r7',$p->x(8)+self::OFFSET);
            if( $bowStyle == 'widesquare' ) {
                $p->newHeightDimension(4,'r4',$p->x(4));
            }
        }
        if( $this->o('endStyle') ) {
            $p->newWidthDimension('r7','8',$p->y('r7')+self::OFFSET);
        }
        
    }
    
    
    public function paperlessCollarBand($model)
    {
        $p = $this->parts['collarBand'];

        $p->newWidthDimension('r2','r3',$p->y('r2')+self::OFFSET);
        $p->newHeightDimension(3,'r3',$p->x(3)+self::OFFSET);
    }

}
