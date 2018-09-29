<?php
/** Freesewing\Patterns\Core\ShinSwimTrunks class */
namespace Freesewing\Patterns\Core;

/**
 * The Shin Swim Trunks pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ShinSwimTrunks extends Pattern
{
    public function initialize($model)
    {
        // Hips
        $this->setValue('hips', $model->m('hipsCircumference') /2  * $this->stretchToScale($this->o('stretch')));
        $front = 0.58;
        $this->setValue('hipFront', $this->v('hips') * $front);
        $this->setValue('hipBack', $this->v('hips') * (1 - $front));
        // Legs (will be further reduced below)
        $this->setValue('legs', $model->m('upperLegCircumference')  * $this->stretchToScale($this->o('stretch')));
        $front = 0.48;
        $this->setValue('legFront', $this->v('legs') * $front);
        $this->setValue('legBack', $this->v('legs') * (1 - $front));
        // Gusset
        $this->setValue('gusset', $model->m('hipsCircumference') / 14);

    }

    public function sample($model)
    {
        $this->initialize($model);
        $this->draftBack($model);
        $this->draftFront($model);
        $this->draftWaistband($model);
    }
    
    public function draft($model)
    {
        $this->sample($model);
        $this->finalizeBack($model);
        $this->finalizeFront($model);
        $this->finalizeWaistband($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            $this->paperlessBack($model);
            $this->paperlessFront($model);
            $this->paperlessWaistband($model);
        }
    }

    public function draftBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];
        $angle = 10;
        $p->newPoint('hipSide', 0, 0);
        $p->newPoint('hipCB', $this->v('hipBack'), 0);
        $p->addPoint('legSide', $p->shift('hipSide', -90 - $angle, $model->m('hipsToUpperLeg')));
        $p->addPoint('legSideCp', $p->shift('legSide', $angle * -1 +7, $this->v('legBack'))); 
        $p->addPoint('legInner', $p->shift('legSideCp', $angle * -1 -90, $this->v('gusset')/2)); 
        $p->addPoint('legSideCp', $p->shiftFractionTowards('legSideCp', 'legSide', 0.5));
        $p->addPoint('.tmp1', $p->shiftAlong('legInner', 'legInner', 'legSideCp', 'legSide', 2));
        $gussetAngle = $p->angle('legInner', '.tmp1'); 
        $p->addPoint('crossSeam', $p->shift('legInner', $gussetAngle -90, $this->v('gusset'))); 
        $p->addPoint('seatCB', $p->shift('hipCB', -86, $model->m('hipsToUpperLeg') * 0.62));
        $p->addPoint('.tmp2', $p->shift('crossSeam', $gussetAngle, 20));
        $p->addPoint('.tmp3', $p->beamsCross('crossSeam', '.tmp2', 'hipCB', 'seatCB'));
        $p->addPoint('seatCp', $p->shiftFractionTowards('seatCB', '.tmp3', 0.7));
        $p->addPoint('crossSeamCp', $p->shiftFractionTowards('crossSeam', '.tmp3', 0.7));
        // Now reduce the legs
        $p->addPoint('reducedLegInner', $p->shiftFractionAlong('legSide', 'legSideCp', 'legInner', 'legInner', 1-$this->o('legReduction')));
        $p->addPoint('reducedLegInnerCp', $p->rotate('legInner', 'reducedLegInner', 90));
        $p->addPoint('reducedCrossSeam', $p->shiftFractionAlong('crossSeam', 'crossSeamCp', 'seatCp', 'seatCB', $this->o('legReduction')*2));
        $p->splitCurve('crossSeam', 'crossSeamCp', 'seatCp', 'seatCB', 'reducedCrossSeam', 'crossSeam');
        // Lengthen legs
        if($this->o('legBonus') > 0) {
            $p->addPoint('legSide', $p->shift('legSide', -90, $model->m('hipsToUpperLeg') * $this->o('legBonus')));
            $p->addPoint('legSideCp', $p->shift('legSideCp', -90, $model->m('hipsToUpperLeg') * $this->o('legBonus')));
            $p->addPoint('reducedLegInner', $p->shift('reducedLegInner', -90, $model->m('hipsToUpperLeg') * $this->o('legBonus')));
        }
        // Rise
        if($this->o('rise') > 0) {
            $p->addPoint('hipSide', $p->shift('hipSide', 90, $model->m('hipsToUpperLeg') * $this->o('rise')));
            $p->addPoint('hipCB', $p->shift('hipCB', 90, $model->m('hipsToUpperLeg') * $this->o('rise')));
        }
        // Back rise
        if($this->o('backRise') > 0) {
            $p->addPoint('hipCB', $p->shift('hipCB', 90, $model->m('hipsToUpperLeg') * $this->o('backRise')));
            $p->addPoint('hipSide', $p->shift('hipSide', 90, $model->m('hipsToUpperLeg') * $this->o('backRise') / 2));
            $p->newPoint('hipCBCp', $p->x('hipCB')/2, $p->y('hipCB')); 
        }
        $seam = "M reducedCrossSeam ";
        $seam .= "C reducedCrossSeam reducedLegInnerCp reducedLegInner ";
        $seam .= "C reducedLegInner legSideCp legSide ";
        $seam .= "L hipSide ";
        if($this->o('backRise') > 0) $seam .= "C hipSide hipCBCp hipCB ";
        else $seam .= "L hipCB ";
        $seam .= "L seatCB ";
        $seam .= "C crossSeam6 crossSeam7 reducedCrossSeam ";
        $seam .= "z";
        $p->newPath('seam', $seam, ['class' => 'fabric']);
        /** Uncomment this to see the impact of the legReduction option 
        $p->newPath('reduction', '
            M reducedLegInner
            L legInner
            L crossSeam
            C crossSeam2 crossSeam3 reducedCrossSeam
            ', ['class' => 'hint']);
         */
    }

    public function draftFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];
        $angle = -12;
        $p->newPoint('hipSide', 0, 0);
        $p->newPoint('hipCB', $this->v('hipFront'), 0);
        $p->addPoint('legSide', $p->shift('hipSide', -90 - $angle, $model->m('hipsToUpperLeg')));
        $p->addPoint('legSideCp', $p->shift('legSide', 0, $this->v('legFront'))); 
        $p->addPoint('legInner', $p->shift('legSideCp', -100, $this->v('gusset')/2)); 
        $p->addPoint('crossSeam', $p->shift('legSideCp', 80, $this->v('gusset')/2)); 
        $p->addPoint('legSideCp', $p->shiftFractionTowards('legSide', 'legSideCp', 0.4));
        $p->addPoint('seatCB', $p->shift('hipCB', -90 - $angle - 5, $model->m('hipsToUpperLeg') * 0.67));
        $p->addPoint('.tmp2', $p->shift('crossSeam', $angle, 20));
        $p->addPoint('.tmp3', $p->beamsCross('crossSeam', '.tmp2', 'hipCB', 'seatCB'));
        $p->addPoint('seatCp', $p->shiftFractionTowards('seatCB', '.tmp3', 0.7));
        $p->addPoint('crossSeamCp', $p->shiftFractionTowards('crossSeam', '.tmp3', 0.7));
        $bulge = $model->m('hipsToUpperLeg') * $this->o('bulge');
        $p->addPoint('midFront', $p->shiftFractionTowards('hipCB', 'seatCB', 0.6));
        $p->addPoint('midFrontCpTop', $p->shiftFractionTowards('hipCB', 'seatCB', 0.3));
        $p->addPoint('midFrontCpBottom', $p->shiftFractionTowards('hipCB', 'seatCB', 0.9));
        $p->addPoint('midBulge', $p->shift('midFront', $angle * -1, $bulge));
        $p->addPoint('bulgeCpTop', $p->shift('midFrontCpTop', $angle * -1, $bulge));
        $p->addPoint('bulgeCpBottom', $p->shift('midFrontCpBottom', $angle * -1, $bulge));
        $p->addPoint('midSide', $p->shiftFractionTowards('hipSide', 'legSide', 0.5));
        $p->addPoint('hipSideCpBottom', $p->shiftFractionTowards('hipSide', 'legSide', 0.2));
        $p->addPoint('midSideCpTop', $p->shiftFractionTowards('hipSide', 'legSide', 0.3));
        $p->addPoint('midSideCpBottom', $p->shiftFractionTowards('hipSide', 'legSide', 0.7));
        $p->addPoint('legSideCpTop', $p->shiftFractionTowards('hipSide', 'legSide', 0.8));
        $p->addPoint('midSideBulge', $p->shift('midSide', $angle * -1, $bulge * -1));
        $p->addPoint('midSideBulgeCpTop', $p->shift('midSideCpTop', $angle * -1, $bulge * -1));
        $p->addPoint('midSideBulgeCpBottom', $p->shift('midSideCpBottom', $angle * -1, $bulge * -1));
        // Now reduce the legs
        $p->addPoint('reducedLegInner', $p->shiftFractionAlong('legSide', 'legSideCp', 'legInner', 'legInner', 1 - $this->o('legReduction')));
        $p->addPoint('reducedLegInnerCp', $p->rotate('legInner', 'reducedLegInner', 90));
        $p->addPoint('reducedCrossSeam', $p->shiftFractionAlong('crossSeam', 'crossSeamCp', 'seatCp', 'seatCB', $this->o('legReduction')*2));
        $p->splitCurve('crossSeam', 'crossSeamCp', 'seatCp', 'seatCB', 'reducedCrossSeam', 'crossSeam');
        // Lengthen legs
        if($this->o('legBonus') > 0) {
            $p->addPoint('legSide', $p->shift('legSide', -90, $model->m('hipsToUpperLeg') * $this->o('legBonus')));
            $p->addPoint('legSideCp', $p->shift('legSideCp', -90, $model->m('hipsToUpperLeg') * $this->o('legBonus')));
            $p->addPoint('reducedLegInner', $p->shift('reducedLegInner', -90, $model->m('hipsToUpperLeg') * $this->o('legBonus')));
        }
        // Rise
        if($this->o('rise') > 0) {
            $p->addPoint('hipSide', $p->shift('hipSide', 90, $model->m('hipsToUpperLeg') * $this->o('rise')));
            $p->addPoint('hipCB', $p->shift('hipCB', 90, $model->m('hipsToUpperLeg') * $this->o('rise')));
        }
        // Back rise
        if($this->o('backRise') > 0) {
            $p->addPoint('hipSide', $p->shift('hipSide', 90, $model->m('hipsToUpperLeg') * $this->o('backRise') / 2));
            $p->newPoint('hipCBCp', $p->x('hipCB')/2, $p->y('hipCB'));
        }
        $seam = "M reducedCrossSeam ";
        $seam .= "C reducedCrossSeam reducedLegInnerCp reducedLegInner ";
        $seam .= "C reducedLegInner legSideCp legSide ";
        $seam .= "C legSideCpTop midSideBulgeCpBottom midSideBulge ";
        $seam .= "C midSideBulgeCpTop hipSideCpBottom hipSide ";
        if($this->o('backRise') > 0) $seam .= "C hipSide hipCBCp hipCB ";
        else $seam .= "L hipCB ";
        $seam .= "C midFrontCpTop bulgeCpTop midBulge ";
        $seam .= "C bulgeCpBottom midFrontCpBottom seatCB ";
        $seam .= "C crossSeam6 crossSeam7 reducedCrossSeam ";
        $seam .= "z";
        $p->newPath('seam', $seam, ['class' => 'fabric']);
        /** Uncomment this to see the impact of the legReduction option 
        $p->newPath('reduction', '
            M reducedLegInner
            L legInner
            L crossSeam
            C crossSeam2 crossSeam3 reducedCrossSeam
            ', ['class' => 'hint']);
         */
    }

    public function draftWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['waistband'];

        $w = $this->o('elasticWidth')*2;
        $p->newPoint('topLeft', 0, 0);
        $p->newPoint('bottomLeft', 0, $w);
        $p->newPoint('topMidLeft', 50, 0);
        $p->newPoint('bottomMidLeft', 50, $w);
        $p->newPoint('topMidRight', 70, 0);
        $p->newPoint('bottomMidRight', 70, $w);
        $p->newPoint('topRight', 120, 0);
        $p->newPoint('bottomRight', 120, $w);
        $p->newPath('seam', '
            M topMidLeft L topLeft L bottomLeft L bottomMidLeft
            M bottomMidRight L bottomRight L topRight L topMidRight
            ', ['class' => 'fabric']);
        $p->newPath('hint', '
            M topMidLeft L topMidRight
            M bottomMidLeft L bottomMidRight
            ', ['class' => 'fabric dashed']);
    }

    public function finalizeBack($model)
    {
        $p = $this->parts['back'];

        // Grainline
        $angle = $p->angle('hipSide', 'legSide') + 90;
        $p->addPoint('glTop', $p->shiftFractionTowards('hipSide', 'legSide', 0.05));
        $p->addPoint('glTop', $p->shift('glTop', $angle, $this->v('gusset')/4));
        $p->addPoint('glBottom', $p->shiftFractionTowards('legSide', 'hipSide', 0.05));
        $p->addPoint('glBottom', $p->shift('glBottom', $angle, $this->v('gusset')/4));
        $p->newGrainline('glBottom','glTop', $this->t('Grainline'));

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathstring('sa', 'M legSide L hipSide L hipCB L seatCB C crossSeam6 crossSeam7 reducedCrossSeam C reducedCrossSeam reducedLegInnerCp reducedLegInner ', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->offsetPathstring('hem', 'M legSide C legSideCp reducedLegInner reducedLegInner', $this->o('sa')*-2, 1, ['class' => 'fabric sa']);
        // Join ends
        $p->newPath('sa2', 'M sa-startPoint L hem-startPoint M hem-endPoint L sa-endPoint', ['class' => 'fabric sa']);
        } 

        // Title
        $p->newPoint('titleAnchor', $p->x('seatCB') / 2, $p->y('seatCB')/2, 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title));

        // Logo
        $p->newPoint('logoAnchor', $p->x('titleAnchor'), $p->y('seatCp')*0.9, 'Logo anchor');
        $p->newSnippet('logo', 'logo', 'logoAnchor');
    }

    public function finalizeFront($model)
    {
        $p = $this->parts['front'];

        // Grainline
        $angle = $p->angle('hipCB', 'seatCB') - 90;
        $p->addPoint('glTop', $p->shift('hipCB', $angle, $this->v('gusset')/2));
        $p->addPoint('glBottom', $p->shift('seatCB', $angle, $this->v('gusset')/2));
        $p->newGrainline('glBottom','glTop', $this->t('Grainline'));

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathstring('sa', 'M legSide C legSideCpTop midSideBulgeCpBottom midSideBulge C midSideBulgeCpTop hipSideCpBottom hipSide L hipCB C midFrontCpTop bulgeCpTop midBulge C bulgeCpBottom midFrontCpBottom seatCB C crossSeam6 crossSeam7 reducedCrossSeam C reducedCrossSeam reducedLegInnerCp reducedLegInner', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->offsetPathstring('hem', 'M legSide C legSideCp reducedLegInner reducedLegInner', $this->o('sa')*-2, 1, ['class' => 'fabric sa']);
        // Join ends
        $p->newPath('sa2', 'M sa-startPoint L hem-startPoint M hem-endPoint L sa-endPoint', ['class' => 'fabric sa']);
        } 

        // Title
        $p->newPoint('titleAnchor', $p->x('seatCB') / 2, $p->y('seatCB')/2, 'Title anchor');
        $p->addTitle('titleAnchor', 2, $this->t($p->title));

        // Logo
        $p->newPoint('logoAnchor', $p->x('titleAnchor'), $p->y('seatCB'), 'Logo anchor');
        $p->newSnippet('logo', 'logo', 'logoAnchor');
    }

    public function finalizeWaistband($model)
    {
        $p = $this->parts['waistband'];

        $shift = $this->o('elasticWidth')/3;
        $p->addPoint('glTop', $p->shift('topLeft', -45, $shift));
        $p->addPoint('glBottom', $p->shift('bottomLeft', 45, $shift));
        $p->newGrainline('glBottom','glTop', $this->t('Grainline'));
        
        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPathstring('sa', 'M topLeft L topRight L bottomRight L bottomLeft L topLeft z', $this->o('sa'), 1, ['class' => 'fabric sa']);
        } 

        // Length indicator
        $p->newWidthDimension('bottomLeft','bottomRight', $p->y('bottomLeft') + 15 + $this->o('sa'), $p->unit($this->v('hips')*2));

        // Title
        $p->addPoint('titleAnchor', $p->shiftFractionTowards('topLeft', 'bottomRight', 0.5));
        $p->addTitle('titleAnchor', 3, $this->t($p->title));

    }

    public function paperlessBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];
        $sa = $this->o('sa');

        $p->newLinearDimension('legSide','hipSide', -15 - $sa);
        $p->newLinearDimension('hipSide','hipCB', -15 - $sa);
        $p->newLinearDimension('hipCB','seatCB', -15 - $sa);
        $p->newWidthDimension('legSide','hipCB', $p->y('hipCB') - 30 -$sa);
        $p->newCurvedDimension('M legSide C legSideCp reducedLegInner reducedLegInner', 15);
        $p->newCurvedDimension('M hipCB L seatCB C crossSeam6 crossSeam7 reducedCrossSeam', -15);
        $p->newWidthDimension('legSide','reducedLegInner', $p->y('reducedLegInner') + 15 + 2*$sa);
        $p->newWidthDimension('legSide','reducedCrossSeam', $p->y('reducedLegInner') + 30 + 2*$sa);
        $p->newHeightDimension('legSide','hipSide', $p->x('legSide') - 30 - $sa);
        $p->newHeightDimension('reducedCrossSeam','hipCB', $p->x('reducedCrossSeam') + 15 + $sa);
        $p->newHeightDimension('reducedLegInner','hipCB', $p->x('reducedCrossSeam') + 30 + $sa);
        $p->newLinearDimension('reducedLegInner','reducedCrossSeam', 15 + $sa);
    }

    public function paperlessFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];
        $sa = $this->o('sa');

        $p->newCurvedDimension('M legSide C legSideCpTop midSideBulgeCpBottom midSideBulge C midSideBulgeCpTop hipSideCpBottom hipSide', 15 + $sa);
        $p->newLinearDimension('hipSide','hipCB', -15 - $sa);
        $p->newLinearDimension('hipCB','seatCB', -15 - $sa);
        $p->newCurvedDimension('M legSide C legSideCp reducedLegInner reducedLegInner', 15);
        $p->newCurvedDimension('M hipCB C midFrontCpTop bulgeCpTop midBulge C bulgeCpBottom midFrontCpBottom seatCB C crossSeam6 crossSeam7 reducedCrossSeam', -15);
        $p->newWidthDimension('legSide','reducedLegInner', $p->y('reducedLegInner') + 15 + 2*$sa);
        $p->newWidthDimension('legSide','reducedCrossSeam', $p->y('reducedLegInner') + 30 + 2*$sa);
        $p->newHeightDimension('legSide','hipSide', $p->x('hipSide') - 30 - $sa);
        $p->newHeightDimension('reducedCrossSeam','hipCB', $p->x('reducedCrossSeam') + 15 + $sa);
        $p->newHeightDimension('reducedLegInner','hipCB', $p->x('reducedCrossSeam') + 30 + $sa);
        $p->newLinearDimension('reducedLegInner','reducedCrossSeam', 15 + $sa);
        $p->newWidthDimension('hipSide','reducedCrossSeam', $p->y('hipSide') - 30 - $sa);
    }

    public function paperlessWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['waistband'];
        
        $p->newHeightDimension('bottomRight','topRight', $p->x('topRight') + 15 + $this->o('sa'));
    }
}
