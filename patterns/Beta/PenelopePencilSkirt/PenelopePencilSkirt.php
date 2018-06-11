<?php
/** PenelopePencilSkirt class */
namespace Freesewing\Patterns\Beta;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/** Constant for the offset of the paperless dimensions */
const OFFSET = 15;

const DART_MIN_WIDTH = 6;
const DART_MIN_DIFF = 180;
const DART_MAX_DIFF = 300;
const DART_MIN_SIDE = 10;
const DART_BACK_1 = 100;
const DART_BACK_2 = 5;
const DART_BACK_3 = 4;
const HIP_CURVE_DIV_UP = 3;
const HIP_CURVE_DIV_DOWN = 40;
const HEM_DEPTH = 25;
const WAIST_BAND_OVERLAP = 25;

/**
 * The Penelope Pencil Skirt pattern
 *
 * @author Wouter van Wageningen
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class PenelopePencilSkirt extends \Freesewing\Patterns\Core\Pattern
{
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
        // Calculate the number of darts and their sizes. Add values to the $part
        $this->dartCalc( $model->m('seatCircumference'), $this->o('seatEase'), $model->m('naturalWaist'), $this->o('waistEase'));

        // If we make a vent, the zipper is moved to the back
        if( $this->o('vent') ) {
            $this->setOption('zipper', 'back');
        }

        $this->msg("nrOfDarts");
        $this->msg($this->v('nrOfDarts'));
        $this->msg("frontDartSize");
        $this->msg($this->v('frontDartSize'));
        $this->msg("backDartSize");
        $this->msg($this->v('backDartSize'));
    }

    private function dartCalc( $seat, $seatEase, $waist, $waistEase )
    {
        $seat += $seatEase;
        $waist += $waistEase;
        $seatWaistDiff = max( $seat - $waist, 0 );
        $this->setValueIfUnset('seatWaistDiff', $seatWaistDiff );

        $nrOfDarts = $this->o('nrOfDarts') ;

        //$this->msg("Seat: $seat"); $this->msg("Waist: $waist"); $this->msg("nrOfDarts: $nrOfDarts");

        $frontDartSize = (DART_MIN_WIDTH + ((max(min($seatWaistDiff, DART_MAX_DIFF), DART_MIN_DIFF) -DART_MIN_DIFF) /4)) /$nrOfDarts;
        //$this->msg("frontDartSize: $frontDartSize");
        if( $frontDartSize <= DART_MIN_WIDTH *$nrOfDarts && $nrOfDarts > 1 ) {
            $nrOfDarts --;
            $frontDartSize = (DART_MIN_WIDTH + ((max(min($seatWaistDiff, DART_MAX_DIFF), DART_MIN_DIFF) -DART_MIN_DIFF) /4)) /$nrOfDarts;
        }
        //$this->msg("frontDartSize: $frontDartSize");
        if( $seatWaistDiff/4 -$frontDartSize < DART_MIN_SIDE ) {
            $frontDartSize = 0;
        }
        //$this->msg("frontDartSize: $frontDartSize");

        if( $seatWaistDiff/4 -$frontDartSize < DART_MIN_SIDE || $frontDartSize < DART_MIN_WIDTH *$nrOfDarts ) {
            $nrOfDarts = 1;
        }
        //$this->msg("nrOfDarts: $nrOfDarts");

        $backDartSize = ((DART_MIN_WIDTH + ($seatWaistDiff -DART_BACK_1 -(($seatWaistDiff -DART_BACK_1)/DART_BACK_2)) /DART_BACK_3)/$nrOfDarts) *(.5 +$this->o('dartToSideSeamFactor')) ;
        if( $backDartSize < DART_MIN_WIDTH *$nrOfDarts && $nrOfDarts > 1 )
        {
            $nrOfDarts = 1;
            $frontDartSize = (DART_MIN_WIDTH + ((max(min($seatWaistDiff, DART_MAX_DIFF), DART_MIN_DIFF) -DART_MIN_DIFF) /4)) /$nrOfDarts;
            $backDartSize = ((DART_MIN_WIDTH + ($seatWaistDiff -DART_BACK_1 -(($seatWaistDiff -DART_BACK_1)/DART_BACK_2)) /DART_BACK_3)/$nrOfDarts) *(.5 +$this->o('dartToSideSeamFactor')) ;
        }
        //$this->msg("frontDartSize: $frontDartSize"); $this->msg("backDartSize: $backDartSize"); $this->msg("nrOfDarts: $nrOfDarts");

        $this->setValue('frontDartSize', $frontDartSize);
        $this->setValue('backDartSize', $backDartSize);
        $this->setValue('nrOfDarts', $nrOfDarts );

        //$this->msg("$seat, $seatEase, $waist, $waistEase, $seatWaistDiff, $nrOfDarts, $frontDartSize, $backDartSize");
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

        //Always draft the back after the front.
        //The back will try to match the sideseam length of the front
        $this->draftPart($model, 'front', $this->v('frontDartSize'));
        $this->draftPart($model, 'back', $this->v('backDartSize'));

        if( $this->o('waistBand') == 'yes') {
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
        $this->finalizePart( $model, 2, 'Back' );

        if( $this->o('waistBand') == 'yes') {
            $this->finalizeWaistBand( $model );
        }

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            #$this->paperlessBowTie( $model, 'bowTie1' );
        }
    }

    /**
     * Drafts a basic shape for all options
     *
     * @param \Freesewing\Model $model The model to sample for
     * @param string $part   'front' or 'back'
     *
     * @return void
     */
    public function draftPart($model, $part, $dartSize)
    {
        $p = $this->parts[$part];
        $waistEase = $this->o('waistEase');
        $seatEase  = $this->o('seatEase');
        $dartDepthFactor = $this->o($part.'DartDepthFactor');

        $pathString = 'M ';

        $waist = $model->m('naturalWaist');
        $seat  = $model->m('seatCircumference') > $waist ? $model->m('seatCircumference') : $waist;
        $hip   = $model->m('hipsCircumference')  > $waist ? $model->m('hipsCircumference')  : $waist;

        $sideSeamShift = ( $part == 'front' ) ? -6 : 6 ;

        $seat += $seatEase;
        $waist += $waistEase;

        $sideSeam = ($seat/4) +$sideSeamShift;
        $hipSeam  = ($hip /4) +$sideSeamShift;

        $p->newPoint('pA1',          0, 0, 'Origin');
        $p->newPoint('pB1',          0, $model->m('naturalWaistToKnee') );
        $p->newPoint('pA2',  $sideSeam, 0 );
        $p->newPoint('pB2',  $sideSeam, $model->m('naturalWaistToKnee') );
        $p->newPoint('pC1',          0, $model->m('naturalWaistToSeat') );
        $p->newPoint('pC2',  $sideSeam, $model->m('naturalWaistToSeat') );
        $p->newPoint('pA2c', $sideSeam, $model->m('naturalWaistToSeat')/3 );
        $p->newPoint('pC2c', $sideSeam, ($model->m('naturalWaistToSeat')/3)*2 );

        $p->newPoint('pH',    $sideSeam, $model->m('naturalWaistToHip') -$this->o('waistSideSeamRaise'));

        $waistFactor = 0.99;
        $wdelta = 1;
        $sideFactor = 0.97;
        $sdelta = 1;
        $iteration = 0;
        do {
            if($wdelta < -1) {
                $waistFactor *= 0.9995;
            } else if($wdelta > 1){
                $waistFactor *= 1.01;
            }
            if($sdelta < -1) {
                $sideFactor *= 0.995;
            } else if($sdelta > 1){
                $sideFactor *= 1.01;
            }
            $p->addPoint('pZ2t1', $p->shift('pA1',    0, ($waist/4)*$waistFactor));
            $p->addPoint('pZ2t2', $p->shift('pZ2t1',  0, $dartSize*$this->v('nrOfDarts')));
            $p->addPoint('pZ2',   $p->shift('pZ2t2', 90, 16 * $sideFactor));
            $p->addPoint('pA1c',  $p->shift('pA1',    0, $seat/12));
            $p->addPoint('pZ2c',  $p->shift('pZ2', $p->angle('pZ2', 'pA2c') -90, $waist/16));

			if( $dartSize > 0 ) {
                $this->addDartToCurve( $p, 'pA1', 'pA1c', 'pZ2c', 'pZ2', ($seat/4) /2.4, $dartSize, $model->m('naturalWaistToSeat') * $dartDepthFactor, 'Dart_1_' );
            } else {
                $p->clonePoint( 'pA1',  'Dart_1_1' );
                $p->clonePoint( 'pA1c', 'Dart_1_2' );
                $p->clonePoint( 'pZ2c', 'Dart_1_3' );
                $p->clonePoint( 'pZ2',  'Dart_1_4' );
                $p->clonePoint( 'pZ2',  'Dart_1_5' );
                $p->clonePoint( 'pZ2',  'Dart_1_6' );
                $p->clonePoint( 'pZ2',  'Dart_1_7' );
                $p->clonePoint( 'pZ2',  'Dart_1_8' );
                $p->clonePoint( 'pZ2',  'Dart_1_Bottom' );
            }

			$waistLength = $p->curveLen( 'Dart_1_1', 'Dart_1_2', 'Dart_1_3', 'Dart_1_4' );

            if( $this->v('nrOfDarts') > 1 ) {
				$this->addDartToCurve( $p, 'Dart_1_5', 'Dart_1_6', 'Dart_1_7', 'Dart_1_8', 32, $dartSize, $model->m('naturalWaistToSeat') * $dartDepthFactor * 0.80, 'Dart_2_' );
                $waistLength += $p->curveLen( 'Dart_2_1', 'Dart_2_2', 'Dart_2_3', 'Dart_2_4' );
                $waistLength += $p->curveLen( 'Dart_2_5', 'Dart_2_6', 'Dart_2_7', 'Dart_2_8' );

                $p->clonePoint( 'Dart_2_8', 'pTopRight' );
            } else {
                $waistLength += $p->curveLen( 'Dart_1_5', 'Dart_1_6', 'Dart_1_7', 'Dart_1_8' );
                $p->clonePoint( 'Dart_1_8', 'pTopRight' );
            }

            $wdelta = ($waist/4) - $waistLength;

            if( $part == 'front' ) {
                $sdelta = 0;
            } else {
                $sideSeamLength = $p->curveLen( 'pTopRight', 'pA2c', /*'p1C2c'*/ 'pH', 'pC2' );
                $sideSeamLength += $p->distance( 'pC2', 'pB2' );

                $sdelta = $this->sideSeamLength - $sideSeamLength;
            }

            $this->msg("[$iteration] Delta is: $wdelta ($waistFactor) $sdelta ($sideFactor)");
        } while ((abs($wdelta) > 1 || abs($sdelta) > 1) && $iteration++ < 100);
        if( $iteration >= 100 ) {
            die("oh shit\n");
        }
        if( $part == 'front' ) {
            $sideSeamLength = $p->curveLen( 'pTopRight', 'pC2c', 'pC2c', 'pC2' );
            $sideSeamLength += $p->distance( 'pC2', 'pB2' );

            $this->sideSeamLength = $sideSeamLength;

            $this->msg( "Front length: $sideSeamLength" );
        } else {
            $this->msg( "Back length: $sideSeamLength" );
        }


        $pathString .= 'Dart_1_1 C Dart_1_2 Dart_1_3 Dart_1_4 L Dart_1_Bottom L ';

        if( $this->v('nrOfDarts') > 1 ) {
            $pathString .= 'Dart_2_1 C Dart_2_2 Dart_2_3 Dart_2_4 L Dart_2_Bottom L ';
            $pathString .= 'Dart_2_5 C Dart_2_6 Dart_2_7 pTopRight ';
        } else {
            $pathString .= 'Dart_1_5 C Dart_1_6 Dart_1_7 pTopRight ';
        }

        if( $part == 'back' && $this->o('vent') ) {
            /* I don't care what you're trying to create, the vent will not go higher than your hips. */
            $ventSize = min( $model->m('naturalWaistToKnee') -$model->m('naturalWaistToHip'), $this->o('ventSize'));
            $p->addPoint('pV1', $p->shift('pB1', 180, 50));
            $p->addPoint('pV2', $p->shift('pV1',  90, $ventSize));
            $p->addPoint('pVtemp', $p->shift('pV2',   0, 50));
            $p->addPoint('pV3', $p->shift('pVtemp',  90, 50));

            $pathString .= 'C pH pH pC2 L pB2 L pB1 L pV1 L pV2 L pV3 L pC1 Z';
        }
        else {
            $pathString .= 'C pH pH pC2 L pB2 L pB1 L pC1 Z';
        }

        $p->newPath('outline', $pathString, ['class' => 'fabric SA']);

        if( $part == 'front' || $this->o('zipper') == 'side' ) {
            $p->newPath('outlineSA', str_replace( 'Dart_1_Bottom L ','', str_replace( 'Dart_2_Bottom L ','', str_replace( 'L pC1 Z','',$pathString ))), ['class' => 'hidden']);
        } else {
            $p->newPath('outlineSA', str_replace( 'Dart_1_Bottom L ','', str_replace( 'Dart_2_Bottom L ','', $pathString )), ['class' => 'hidden']);
        }

        // Mark for sampler
        $p->paths['outline']->setSample(true);
        $p->paths['outlineSA']->setSample(false);
    }

    /**
     * Little helper method to print info on points
     */
    public function pPoint( $pp, $point )
    {
        $xx = $pp->x($point);
        $yy = $pp->y($point);
        $this->msg("$point: [$yy,$xx]");
        printf("$point: [$yy,$xx]\n");
    }

    /**
     * Method to add a dart onto a curveLen
     * The dart is added at an 90 degree angle with the curve for a certain depth and Width
     * @param part $p                   The part to which the points will be added
     * @param point $p1, $p2, $p3, $p4  The points defining the curve
     * @param float $distance           Distance from $p1 where the middle of the dart will be
     * @param float $dartSize1          The width of the dart opening at the curve
     * @param float $dartDepth          The depth of the dart
     * @param string $prefix            The prefix for the new points to be added
     *
     * @return nothing                  Adds points to the $part
     */
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

        // Rename the points
        $p->clonePoint('DTC_LeftCurve_1',  ($prefix)."1");
        $p->clonePoint('DTC_LeftCurve_2',  ($prefix)."2");
        $p->clonePoint('DTC_D1l3a',        ($prefix)."3");
        $p->clonePoint('DTC_LeftCurve_4',  ($prefix)."4");
        $p->clonePoint('DTC_RightCurve_5', ($prefix)."8");
        $p->clonePoint('DTC_RightCurve_6', ($prefix)."7");
        $p->clonePoint('DTC_D1r7a',        ($prefix)."6");
        $p->clonePoint('DTC_RightCurve_8', ($prefix)."5");
        $p->clonePoint('DTC_D1b',          ($prefix)."Bottom");

        // Remove unused points
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

        $waist = $model->m('naturalWaist');
        $waistEase = $this->o('waistEase');
        $waist += $waistEase;

        $p->newPoint('pA', 0, 0, 'Origin');
        $p->newPoint('pB', 0, $waist/2 +(WAIST_BAND_OVERLAP*2));
        $p->newPoint('pC', $this->o('waistBandHeight') *2, 0 );
        $p->newPoint('pD', $this->o('waistBandHeight') *2, $waist/2 +(WAIST_BAND_OVERLAP*2));

        $p->newPath('outline', "M pA L pB L pD L pC Z", ['class' => 'fabric']);

        if( $this->o('zipper') == 'side' ) {
            // Not quite sure what kind of notches to add in this orientation
        } else {
            $p->addPoint('pSideSeamA', $p->shift('pA', 270, $waist/4 +6 +WAIST_BAND_OVERLAP));
            $p->addPoint('pSideSeamC', $p->shift('pC', 270, $waist/4 +6 +WAIST_BAND_OVERLAP));
            $p->notch(['pSideSeamA','pSideSeamC']);
        }
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

        $p->newPoint('grainlineTop',    $p->x('pA1') +50, $p->y('pA1') +50 );
        $p->newPoint('grainlineBottom', $p->x('pB1') +50, $p->y('pB1') -50 );

        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        if( $this->o('sa') ) {
            $p->offsetPath('seamAllowance', 'outlineSA', $this->o('sa'), true, ['class' => 'fabric sa'] );

            if( $partName == 'Front' || $this->o('zipper') == 'side' ) {
                $sa = $p->paths['seamAllowance'];
                //$sa->setPathstring( str_replace( 'M ', 'M p1A1 L ', $sa->getPathstring()).' L pBh');

                $p->newPoint('COFTop',    $p->x('pA1'), $p->y('pA1') +100 );
                $p->newPoint('COFBottom', $p->x('pB1'), $p->y('pB1') -100 );
                $p->newCutonfold('COFTop', 'COFBottom', $this->t('Cut on fold'), -20) ;
            }
        }

        // Title
        $p->newPoint('titleAnchor', $model->m('naturalWaist')/6, $model->m('naturalWaistToSeat'), 'Title point');
        if( $partName == 'Front' || $this->o('zipper') == 'side' ) {
            $p->addTitle('titleAnchor', $partNumber, $partName, '1x cut on fold '.$this->t('from fabric'), 'Default');
        } else {
            $p->addTitle('titleAnchor', $partNumber, $partName, '2x '.$this->t('from fabric'), 'Default');
        }

        // Scalebox
        if( $partName == 'Front' ) {
            $p->newPoint('scaleboxAnchor', $p->x('pB1') +150, $p->y('pB1') -100);
            $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        }

        // logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', 270, 75));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

    }

    public function finalizeWaistBand($model)
    {
        $p = $this->parts['waistBand'];

        $p->addPoint('pCB1', $p->shift('pA', 270, WAIST_BAND_OVERLAP));
        $p->addPoint('pCB2', $p->shift('pC', 270, WAIST_BAND_OVERLAP));

        $p->newPath('CBLine', 'M pCB1 L pCB2', ['class' => 'helpline']);
        $p->newTextOnPath('CBLinetext', $p->paths['CBLine']->getPathstring(), "Center\nback", ['class' => 'text-center'], FALSE);

        if( $this->o('sa') ) {
            $p->offsetPath('seamAllowance', 'outline', -1 *$this->o('sa'), true, ['class' => 'fabric sa'] );
        }

        // Title
        $p->newPoint('titleAnchor', $p->x('pA') +$this->o('waistBandHeight'), $p->y('pA') +55 , 'Title point');
        $p->addTitle('titleAnchor', 3, 'Waistband', '1x cut on fold '.$this->t('from fabric'), 'vertical-extrasmall');

        // logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', 270, 75));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');

        $p->newPoint('grainlineTop',    $p->x('pA') +4, $p->y('logoAnchor') +15 );
        $p->newPoint('grainlineBottom', $p->x('pB') +4, $p->y('pB') -50 );

        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        $p->addPoint('pF1', $p->shift('logoAnchor', 270, 8));
        $p->addPoint('pF2', $p->shift('pB', 0, $this->o('waistBandHeight')));
        $p->addPoint('pF3', $p->shift('pA', 0, $this->o('waistBandHeight')));
        $p->addPoint('pF4', $p->shift('pF3', 270, WAIST_BAND_OVERLAP +4));

        $p->newPath('foldLine1', 'M pF1 L pF2', ['class' => 'helpline']);
        $p->newPath('foldLine2', 'M pF3 L pF4', ['class' => 'helpline']);
        $p->newTextOnPath('foldLinetext', $p->paths['foldLine1']->getPathstring(), 'Fold line', ['class' => 'text-center'], FALSE);

        $p->newPoint('COFLeft',   $p->x('pB') +3, $p->y('pB'));
        $p->newPoint('COFRight',  $p->x('pD') -3, $p->y('pD'));
        $p->newCutonfold('COFLeft', 'COFRight', $this->t('Cut on fold'), -10) ;
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
     * @param \Freesewing\Model $model The model to finalize the part for
     * @param string $partName The name of the part object
     *
     * @return void
     */
    public function paperlessPart($model, $partName)
    {
        $p = $this->parts[$partName];

        //$p->newWidthDimension('r2','r3',$p->y('r7')+self::OFFSET);
    }


    public function paperlessWaistBand($model)
    {
        $p = $this->parts['waistBand'];

        //$p->newWidthDimension('r2','r3',$p->y('r2')+self::OFFSET);
    }

}