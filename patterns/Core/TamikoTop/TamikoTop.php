<?php
/** Freesewing\Patterns\Core\TamikoTop class */
namespace Freesewing\Patterns\Core;

/**
 * The Tamiko Top pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TamikoTop extends Pattern
{
    /*
        ____             __ _
       |  _ \ _ __ __ _ / _| |_
       | | | | '__/ _` | |_| __|
       | |_| | | | (_| |  _| |_
       |____/|_|  \__,_|_|  \__|

      The actual sampling/drafting of the pattern
    */

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
        $this->sample($model);

        $this->finalizeTop($model);

        if ($this->isPaperless) {
            $this->paperlessTop($model);
        }

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
        $this->draftTop($model);
    }

    /**
     * Drafts the Top
     *
     * @see \Freesewing\Patterns\TrayvonTie::draftInterfacingTip()
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTop($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['top'];

        // Shoulder to shoulder len
        $s2s = $model->m('shoulderToShoulder')+50;
        // Armhole depth
        $ad = $model->m('shoulderToShoulder')*0.5;
        // Chest depth
        $ch = $model->m('chestCircumference')/2+2;

        $p->newPoint(     1, 0, 0);
        $p->newPoint(     2, $p->x(1), sqrt(pow($s2s, 2))-50);
        $p->newPoint(     3, 100, $p->y(2));
        $p->newPoint(     4, $p->x(3)+$ad, $p->y(3));
        $p->newPoint(     5, $p->x(3)+$ad/2, $p->y(3)-40);
        $p->newPoint(     6, $p->x(5)-$ad/2.5, $p->y(5));
        $p->addPoint(     7, $p->flipX(6, $p->x(5)));
        $p->newPoint(     8, $p->x(1), $p->y(1)+50);
        $p->addPoint(     9, $p->shiftTowards(3, 1, 50));
        $angle = $p->angle(8, 3);
        $p->addPoint(    10, $p->rotate(9, 3, $angle+90));
        $p->newPoint(    20, $p->x(4), $p->y(4)-$ch);
        $p->addPoint(    21, $p->rotate(20, 4, $angle+90));
        $p->addPoint(    22, $p->shiftTowards(1, 21, $ad));
        $p->addPoint(    23, $p->shiftTowards(1, 21, $model->m('centerBackNeckToWaist')+$model->m('naturalWaistToHip')));
        $p->addPoint(    24, $p->shiftTowards(1, 21, $model->m('centerBackNeckToWaist')+$model->m('naturalWaistToHip')+40));
        $p->addPoint(    31, $p->shift(1, 0, 5));
        $p->addPoint(    38, $p->shift(8, 0, 5));
        $p->newPoint(    41, $p->x(1), $p->y(24));
        $p->newPoint(    42, $p->x(1)+$model->m('centerBackNeckToWaist')+$model->m('naturalWaistToHip')+200+$this->o('lengthBonus'), $p->y(3));
        $p->newPoint(    43, $p->x(42), $p->y(41));
        $p->newPoint(    44, $p->x(1), $p->y(3));

        // Paths
        $arc = '3 C 3 6 5 C 7 4 4';
        $sew = "M 22 L 23 M 10 L 3 M 31 L 38 M $arc" ;
        $fabric = "M 41 L 44 $arc L 42 L 43 z";
        $p->newPath('cutline', $fabric, ['class' => 'fabric']);
        $p->newPath('sewline', $sew, ['class' => 'fabric']);

        // Mark paths for sample service
        $p->paths['cutline']->setSample(true);
        $p->paths['sewline']->setSample(true);

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
     * Finalizes the top
     *
     * @param \Freesewing\Model $model The model to finalize the part for
     *
     * @return void
     */
    public function finalizeTop($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['top'];

        // Title
        $p->newPoint('titleAnchor', $p->x(42)/2, $p->y(42)/2);
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));

        // Cut-on-fold (cof)
        $p->newPoint('cofStart', $p->x(1)+40, $p->y(2));
        $p->newPoint('cofEnd', $p->x(42)-40, $p->y(2));
        $p->newCutOnFold('cofStart','cofEnd',$this->t('Cut on fold').' - '.$this->t('Grainline'),-95);

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',90,130));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('logoAnchor',-90,30));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Seam allowance
        if($this->o('sa')) $p->offsetPathString('sa', 'M 3 C 3 6 5 C 7 4 4', $this->o('sa')*-1, 1, ['class' => 'sa fabric']);

        // Notches
        $p->notch([23,22,31,38,10]);
    }


    private function textAttr($dy)
    {
        return ['class' => 'text-lg fill-note text-center', 'dy' => $dy];
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
     * Adds paperless info for the top
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessTop($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['top'];

        // Widths at the bottom
        $yBase = $p->y(2);
        $p->newWidthDimension(2,10,$yBase+15);
        $p->newWidthDimension(2,3,$yBase+30);
        $p->newWidthDimension(2,4,$yBase+45);
        $p->newWidthDimension(2,42,$yBase+60);

        // Depth armhole
        $p->newHeightDimension(3,5,$p->x(5));

        // Heigths at the left
        $xBase = $p->x(41);
        $p->newHeightDimension(22,41,$xBase-15);
        $p->newHeightDimension(31,41,$xBase-30);
        $p->newHeightDimension(38,31,$xBase-30);
        $p->newHeightDimension(2,31,$xBase-45);
        $p->newHeightDimension(42,10,$xBase-15);

        // Widths at the top
        $yBase = $p->y(41);
        $p->newWidthDimension(41,22,$yBase-15);
        $p->newWidthDimension(41,23,$yBase-30);

        // Extra heigth
        $p->newHeightDimension(23,41,$p->x(23)+15);

        // Notes
        $p->addPoint(200, $p->shift(5, -5, 30));
        $p->addPoint(201, $p->shift(8, -90, 50));
        $p->newNote('saNote', 200, $this->t("Standard\nseam\nallowance")."\n(".$p->unit(10).')', 1, 30, -3, ['line-height' => 6, 'class' => 'text-lg', 'dy' => -15]);
        $p->newNote('noSaNote', 201, $this->t("No\nseam\nallowance"), 3, 30, 0, ['line-height' => 6, 'class' => 'text-lg', 'dy' => -5]);
        $p->newNote('0.5cm', 31, $p->unit(5).' '.$this->t("from the edge"), 2, 30, 0, ['line-height' => 6, 'class' => 'text-lg', 'dy' => 0]);
    }
}
