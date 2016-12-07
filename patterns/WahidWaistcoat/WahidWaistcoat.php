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
        //$this->parts['frontBlock']->setRender(false);
        //$this->parts['backBlock']->setRender(false);
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
        $this->draftWaistcoatBlock($model);
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
     * Drafts the front
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
    public function draftWaistcoatBlock($model)
    {
        $this->clonePoints('frontBlock', 'waistcoatBlock');
        $p = $this->parts['waistcoatBlock'];

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
      
        // Back dart
        $p->newPoint(  900, $p->x(5)*0.5, $p->y(3), 'Dart center');
        $p->addPoint(  901, $p->shift(900, 0, $w8th/2));
        $p->addPoint(  902, $p->flipX(901, $p->x(900)));
        $p->addPoint(  903, $p->shift(901, 90, $p->deltaY(5, 3)*0.25));
        $p->addPoint(  904, $p->flipX(903, $p->x(900)));
        $p->addPoint(  905, $p->shift(901, -90, $p->deltaY(3, 4)*0.25));
        $p->addPoint(  906, $p->flipX(905, $p->x(900)));
        $p->addPoint(  907, $p->shift(900, 90, $p->deltaY(5, 3)));
        $p->addPoint(  908, $p->shift(900, -90, $p->deltaY(3, 4)));
        $p->addPoint(  909, $p->shift(908, 0, $h8th/2));
        $p->addPoint(  910, $p->flipX(909, $p->x(900)));
        $p->newPoint(  911, $p->x(909), $p->y(302));
        $p->newPoint(  912, $p->x(910), $p->y(302));
        $p->addPoint(  913, $p->shift(909, 90, 25));
        $p->addPoint(  914, $p->shift(910, 90, 25));

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
        
        $path = 'M 300 L 302 L 4 L 6 L 5 C 13 16 14 C 15 18 10 C 17 19 12 L 8 C 20 301 300 z';
        $p->newPath('seamline', $path, ['class' => 'seam-allowance']);


    }
}
