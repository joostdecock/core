<?php
/** PenelopePencilSkirt class */
namespace Freesewing\Patterns\Beta;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/** Constant for the offset of the paperless dimensions */
const OFFSET = 15;

const DART_MIN_WIDTH = 6;
const DART_MIN_DIFF = 150;
const DART_MAX_DIFF = 250;
const DART_MIN_SIDE = 1.25;
const DART_BACK_1 = 100;
const DART_BACK_2 = 6;
const DART_BACK_3 = 5;
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
        /*
        $this->dartCalc( 760, 15, 610, 5 );
        $this->dartCalc( 860, 15, 650, 5 );
        $this->dartCalc( 850, 15, 640, 5 );
        $this->dartCalc( 920, 15, 690, 5 );
        $this->dartCalc( 930, 15, 670, 5 );
        $this->dartCalc( 970, 15, 660, 5 );
        $this->dartCalc( 890, 15, 660, 5 );
        $this->dartCalc( 860, 15, 640, 5 );
        $this->dartCalc( 940, 15, 720, 5 );
        $this->dartCalc( 980, 15, 710, 5 );
        $this->dartCalc( 930, 15, 710, 5 );
        $this->dartCalc( 950, 15, 680, 5 );
        $this->dartCalc( 940, 15, 810, 5 );
        $this->dartCalc(1160, 15, 760, 5 );
        $this->dartCalc( 910, 15, 760 ,5 );
        $this->dartCalc( 940, 15, 740 ,5 );
        $this->dartCalc( 990, 15, 810 ,5 );
        $this->dartCalc(1120, 15, 910, 5 );
        */

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

        $frontDartSize = DART_MIN_WIDTH + ((max(min($seatWaistDiff, DART_MAX_DIFF), DART_MIN_DIFF) -DART_MIN_DIFF) /4);

        if( $frontDartSize < DART_MIN_WIDTH ) {
            $frontDartSize = 0;
        }

        if( $seatWaistDiff/4 -$frontDartSize < DART_MIN_SIDE || $frontDartSize < DART_MIN_WIDTH *3 || $this->o('nrOfDarts') == 1 ) {
            $nrOfDarts = 1;
        } else {
            $nrOfDarts = 2;
        }

        $backDartSize = (DART_MIN_WIDTH + ($seatWaistDiff -DART_BACK_1 -(($seatWaistDiff -DART_BACK_1)/DART_BACK_2)) /DART_BACK_3) *(.5 +$this->o('dartToSideSeamFactor')) ;

        $this->setValueIfUnset('nrOfDarts', $nrOfDarts);
        $this->setValueIfUnset('frontDartSize', $frontDartSize );
        $this->setValueIfUnset('backDartSize', $backDartSize );

        //printf("seat: $seat, seatEase: $seatEase, waist: $waist, waistEase: $waistEase, seatWaistDiff: $seatWaistDiff, frontDartSize: $frontDartSize \n\r");
        $this->msg("$seat, $seatEase, $waist, $waistEase, $seatWaistDiff, $nrOfDarts, $frontDartSize, $backDartSize");
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
        $this->draftPart($model, 'front');
        $this->draftPart($model, 'back');
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
    public function draftPart($model, $partName)
    {
        $p = $this->parts[$partName];

        $this->setValue('skirtLength', $model->m('naturalWaistToKnee') +$this->o('lengthBonus'));

        $pathString = 'M ';

        $waist = $model->m('naturalWaist');
        $seat  = $model->m('seatCircumference') > $waist ? $model->m('seatCircumference') : $waist;
        $hip   = $model->m('hipsCircumference')  > $waist ? $model->m('hipsCircumference')  : $waist;
        $waistEase = $this->o('waistEase');
        $seatEase  = $this->o('seatEase');

        $dartSize = $this->v($partName.'DartSize');
        $dartDepthFactor = $this->o($partName.'DartDepthFactor');

        $this->msg("waist: $waist, hip: $hip, seat: $seat");

        $waist += $waistEase;
        $seat  += $seatEase;
        $hipEase = (abs($seatEase -$waistEase) / $model->m('naturalWaistToSeat')) * $model->m('naturalWaistToHip') +$waistEase;
        $hip   += $hipEase;
        $this->msg("waist: $waist, hip: $hip, seat: $seat");

        $sideSeamShift = ( $partName == 'front' ) ? 6 : -6 ;
        $sideSeam = ($seat/4) +$sideSeamShift;
        $hipSeam  = ($hip /4) +$sideSeamShift;
        $this->msg("sideseam: $sideSeam, hipSeam: $hipSeam");

        $p->newPoint('pA',          0, 0, 'Origin');
        $p->newPoint('pB',          0, $this->v('skirtLength') -$this->o('waistSideSeamRaise'));
        $p->newPoint('pC',  $sideSeam, 0 );
        $p->newPoint('pD',  $sideSeam, $this->v('skirtLength') -$this->o('waistSideSeamRaise'));
        $p->newPoint('pD1', $sideSeam +$this->o('hemBonus'), $this->v('skirtLength') -$this->o('waistSideSeamRaise'));

        $p->addPoint('pBt', $p->shift( 'pB',  90, HEM_DEPTH )); // Temporary point for creating the hem
        $p->addPoint('pBh', $p->shift( 'pB', 270, HEM_DEPTH )); // Point for the hem.
        $p->addPoint('pDt', $p->shift( 'pD',  90, HEM_DEPTH )); // Temporary point for creating the hem

        $p->newPoint('pE',          0, $model->m('naturalWaistToHip')  -$this->o('waistSideSeamRaise'));
        $p->newPoint('pF',   $hipSeam, $model->m('naturalWaistToHip')  -$this->o('waistSideSeamRaise'));

        $p->newPoint('pG',          0, $model->m('naturalWaistToSeat') -$this->o('waistSideSeamRaise'));
        $p->newPoint('pH',  $sideSeam, $model->m('naturalWaistToSeat') -$this->o('waistSideSeamRaise'));

        // Control points for the curve around the hip
        $p->addPoint('pHc1', $p->shift('pH',  90, ($model->m('naturalWaistToSeat') - $model->m('naturalWaistToHip'))/HIP_CURVE_DIV_UP));
        $p->addPoint('pHc2', $p->shift('pH', 270, ($model->m('naturalWaistToSeat') - $model->m('naturalWaistToHip'))*(abs($this->o('hemBonus'))/HIP_CURVE_DIV_DOWN)));

        $totalDartIntake = $this->v($partName.'DartSize');
        if( $this->v('nrOfDarts') > 1) {
            $dartSize1 = $dartSize2 = ($totalDartIntake-DART_MIN_WIDTH) /2;
            $dartSize1 += DART_MIN_WIDTH;
        } else {
            $dartSize1 = $totalDartIntake;
        }

        $hipAdjustment = (($model->m('naturalWaistToSeat') - $model->m('naturalWaistToHip'))/$model->m('naturalWaistToSeat'))/2;
        $p->newPoint('pF1',   $hipSeam +($hipAdjustment *$totalDartIntake), $model->m('naturalWaistToHip')-$this->o('waistSideSeamRaise') );
        $this->msg("[$hipSeam] [$hipAdjustment] [$totalDartIntake]");

        $waistFactor = 0.99;
        $waistDelta  = 1;
        $seamFactor  = 0.99;
        $seamDelta   = 1;
        $iteration   = 0;
        do {
            if($waistDelta < -1) {
                $waistFactor *= 0.9995;
            } else if($waistDelta > 1){
                $waistFactor *= 1.01;
            }
            if($seamDelta < -1) {
                $seamFactor *= 0.9995;
            } else if($seamDelta > 1){
                $seamFactor *= 1.01;
            }

            // Creating the new point at the waist/sideseam
            $p->addPoint('pZ2t1', $p->shift('pA',     0, (($waist/4)+$sideSeamShift)*$waistFactor));
            $p->addPoint('pZ2t2', $p->shift('pZ2t1',  0, $totalDartIntake));
            $p->addPoint('pZ2',   $p->shift('pZ2t2', 90, max($this->o('waistSideSeamRaise'),0.5) * $seamFactor));
            $p->addPoint('pAc',   $p->shift('pA',     0, $seat/12)); // One third from the mid point
            $p->addPoint('pZ2c',  $p->shift('pZ2',    $p->angle('pZ2', 'pF1') -90, $waist/16));

            $this->addDartToCurve( $p, 'pA', 'pAc', 'pZ2c', 'pZ2', ($waist) / (8.6), $dartSize1, $model->m('naturalWaistToSeat') * $dartDepthFactor, 'Dart1_' );

            $waistLength = $p->curveLen( 'Dart1_1', 'Dart1_2', 'Dart1_3', 'Dart1_4' );

            // If the high hip line is higher than the lowest part of the dart, we need to
            // move the point of the high hip at the sideseam out by the amount that the dart
            // takes up. This variable keeps track of that.
            $hipSeamDartMove = 0;

            if( $this->v('nrOfDarts') > 1 ) {
                $this->addDartToCurve( $p, 'Dart1_5', 'Dart1_6', 'Dart1_7', 'Dart1_8', 32, $dartSize2, $model->m('naturalWaistToSeat') * $dartDepthFactor * 0.80, 'Dart2_' );
                $waistLength += $p->curveLen( 'Dart2_1', 'Dart2_2', 'Dart2_3', 'Dart2_4' );
                $waistLength += $p->curveLen( 'Dart2_5', 'Dart2_6', 'Dart2_7', 'Dart2_8' );

                $p->clonePoint( 'Dart2_8', 'pTopRight' );

                if( $pIntersect1 = $p->linesCross('pE', 'pF', 'Dart2_4', 'Dart2_Bottom')) {
                    $pIntersect2 = $p->linesCross('pE', 'pF', 'Dart2_5', 'Dart2_Bottom');
                    $hipSeamDartMove += $pIntersect2->x -$pIntersect1->x;
                }
            } else {
                $waistLength += $p->curveLen( 'Dart1_5', 'Dart1_6', 'Dart1_7', 'Dart1_8' );

                $p->clonePoint( 'Dart1_8', 'pTopRight' );
            }

            if( $pIntersect1 = $p->linesCross('pE', 'pF', 'Dart1_4', 'Dart1_Bottom')) {
                $pIntersect2 = $p->linesCross('pE', 'pF', 'Dart1_5', 'Dart1_Bottom');
                $hipSeamDartMove += $pIntersect2->x -$pIntersect1->x;
            }
            // Redraw the high hip point at the side seam
            $p->newPoint('pF',   $hipSeam +$hipSeamDartMove, $model->m('naturalWaistToHip') -$this->o('waistSideSeamRaise'));
            $p->newPoint('pF1',  $hipSeam +($hipAdjustment *$totalDartIntake), $model->m('naturalWaistToHip')-$this->o('waistSideSeamRaise') );

            $waistDelta = (($waist/4)+$sideSeamShift) - $waistLength;

            if( $partName == 'front' ) {
                $seamDelta = 0;
            } else {
                $sideSeamLength = $p->curveLen( 'pTopRight', 'pF1', 'pHc1', 'pH' );
                $sideSeamLength += $p->distance( 'pH', 'pD' );

                $seamDelta = $this->sideSeamLength - $sideSeamLength;
            }

            $this->msg("[$iteration] Delta is: $waistDelta ($waistFactor) $seamDelta ($seamFactor)");
            //printf("[$iteration] Delta is: $waistDelta ($waistFactor) $seamDelta ($seamFactor)\n");
        } while ((abs($waistDelta) > 1 || abs($seamDelta) > 1) && $iteration++ < 200);

        if( $iteration >= 200 ) {
            die('oh shit');
        }

        if( $partName == 'front' ) {
            $sideSeamLength = $p->curveLen( 'pTopRight', 'pF1', 'pHc1', 'pH' );
            $sideSeamLength += $p->distance( 'pH', 'pD' );

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

        $p->curveCrossesLine('pH', 'pHc2', 'pD1', 'pD1', 'pBt', 'pDt', 'pDth');
        $p->addPoint('pD1h', $p->flipY('pDth1',$p->y('pD1')));

        if( $partName == 'back' && $this->o('vent') ) {
            /* I don't care what you're trying to create, the vent will not go higher than your seat. */
            $ventSize = min( $model->m('naturalWaistToKnee') -$model->m('naturalWaistToSeat'), $this->o('ventSize'));

            $p->addPoint('pV1', $p->shift('pB',  180, 50));
            $p->addPoint('pVh', $p->shift('pV1', 270, HEM_DEPTH));
            $p->addPoint('pV2', $p->shift('pV1',  90, $ventSize));
            $p->addPoint('pVt', $p->shift('pV2',   0, 50));
            $p->addPoint('pV3', $p->shift('pVt',  90, 50));

            $pathString .= 'C pF1 pHc1 pH C pHc2 pD1 pD1 L pD1h L pBh L pVh L pV2 L pV3 L pG Z';
            $p->newPath('hemLine', 'M pV1 L pD1', ['class' => 'helpline']);
        }
        else {
            $pathString .= 'C pF1 pHc1 pH C pHc2 pD1 pD1 L pD1h L pBh L pG Z';
            $p->newPath('hemLine', 'M pB L pD1', ['class' => 'helpline']);
        }

        $p->newPath('outline', $pathString, ['class' => 'fabric']);

        if( $partName == 'front' || $this->o('zipper') == 'side' ) {
            $p->newPath('outlineSA', str_replace( 'Dart1_Bottom L ','', str_replace( 'Dart2_Bottom L ','', str_replace( 'L pG Z','',$pathString ))), ['class' => 'hidden']);
        } else {
            $p->newPath('outlineSA', str_replace( 'Dart1_Bottom L ','', str_replace( 'Dart2_Bottom L ','', $pathString )), ['class' => 'hidden']);
        }

        // Mark for sampler
        $p->paths['outline']->setSample(true);
        $p->paths['outlineSA']->setSample(false);
        $p->paths['hemLine']->setSample(true);

        $p->newTextOnPath('hemLinetext', $p->paths['hemLine']->getPathstring(), 'Hem line', ['class' => 'text-center'], FALSE);

        /* Helper lines during design process
        $p->newPath('hipLine', 'M pE L pF', ['class' => 'helpline']);
        $p->newPath('seatLine', 'M pG L pH', ['class' => 'helpline']);

        $p->paths['hipLine']->setSample(true);
        $p->paths['seatLine']->setSample(true);
        */

        /* Print Data about calculations on the part
        $p->newPoint('tAnchor', $model->m('naturalWaist')/5, $model->m('naturalWaistToHip'), 'text point');

        $dartDepth = $model->m('naturalWaistToSeat') * $dartDepthFactor;
        $seat = $model->m('seatCircumference');
        $seatEase = $this->o('seatEase');
        $waist = $model->m('naturalWaist');
        $waistEase = $this->o('waistEase');
        $seat += $seatEase;
        $waist += $waistEase;

        $seatWaistDiff = max( $seat - $waist, 0 );

        $p->newText('thelp', 'tAnchor', "Dart Width: $totalDartIntake\nDart Depth: $dartDepth\ndart1: $dartSize1\ndart2: $dartSize2\nwaist: $waist\nwaistEase: $waistEase\nhip: $hip\nhipEase: $hipEase\nseat: $seat\nseatEase: $seatEase\nSeatWaistDiff: $seatWaistDiff\nwaistLength: $waistLength\nhipSeamDartMove: $hipSeamDartMove");
        */

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

        $p->newPoint('grainlineTop',    $p->x('pA') +50, $p->y('pA') +50 );
        $p->newPoint('grainlineBottom', $p->x('pB') +50, $p->y('pB') -50 );

        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        if( $this->o('sa') ) {
            $p->offsetPath('seamAllowance', 'outlineSA', $this->o('sa'), true, ['class' => 'fabric sa'] );

            if( $partName == 'Front' || $this->o('zipper') == 'side' ) {
                $sa = $p->paths['seamAllowance'];
                $sa->setPathstring( str_replace( 'M ', 'M pA L ', $sa->getPathstring()).' L pBh');

                $p->newPoint('COFTop',    $p->x('pA'), $p->y('pA') +100 );
                $p->newPoint('COFBottom', $p->x('pB'), $p->y('pB') -100 );
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
            $p->newPoint('scaleboxAnchor', $p->x('pB') +150, $p->y('pB') -100);
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
