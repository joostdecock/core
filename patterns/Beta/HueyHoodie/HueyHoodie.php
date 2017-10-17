<?php
/** Freesewing\Patterns\Beta\HueyHoodie class */
namespace Freesewing\Patterns\Beta;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/**
 * A zip-up hoodie pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class HueyHoodie extends \Freesewing\Patterns\Core\BrianBodyBlock
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /** Neck opening ratio = 48% of shoulder to shoulder measurement */
    const NECK_OPENING_RATIO = 0.48;

    /** Sleevecap height factor = 50% */
    const SLEEVECAP_HEIGHT_FACTOR = 0.5;
    
    /** Fix sleevecap ease to 0 */
    const SLEEVECAP_EASE = 0;



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
        $this->setOptionIfUnset('sleevecapHeightFactor', self::SLEEVECAP_HEIGHT_FACTOR);
        $this->setOptionIfUnset('sleevecapEase', self::SLEEVECAP_EASE);

        parent::initialize($model);
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
     * Here, you create a sample of the pattern for a given model
     * and set of options. You should get a barebones pattern with only
     * what it takes to illustrate the effect of changes in
     * the sampled option or measurement.
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function sample($model)
    {
        // Setup all options and values we need
        $this->initialize($model);

        // Draft our basic block
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        // Tweak the sleeve until it fits the armhole
        do {
            $this->draftSleeveBlock($model);
        } while (abs($this->armholeDelta($model)) > 1);
        $this->msg('After '.$this->v('sleeveTweakRun').' attemps, the sleeve head is '.round($this->armholeDelta($model),1).'mm off.');

        // Draft Huey
        $this->draftBack($model);
        $this->draftFront($model);
        $this->draftSleeve($model);
        $this->draftHood($model);
        if($this->o('pouch')) $this->draftPouch($model);

        // Don't render Brian blocks
        $this->parts['backBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['sleeveBlock']->setRender(false);

    }

    /**
     * Generates a draft of the pattern
     *
     * Here, you create the full draft of this pattern for a given model
     * and set of options. You get a complete pattern with
     * all bels and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draft($model)
    {
        // Continue from sample
        $this->sample($model);

        // Finalize parts
        $this->finalizeFront($model);
        $this->finalizeBack($model);
        $this->finalizeSleeve($model);
        $this->finalizeHood($model);
        if($this->o('pouch') == 1) $this->finalizePouch($model);


        // Is this a paperless pattern?
        if ($this->isPaperless) {
            $this->paperlessBack($model);
            $this->paperlessFront($model);
            $this->paperlessSleeve($model);
            $this->paperlessHood($model);
            if($this->o('pouch') == 1) $this->paperlessPouch($model);
        }
    }

    /**
     * Drafts the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBack($model)
    {
        $this->clonePoints('backBlock','back');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Widen neck opening
        $cplen = $p->distance(8,20);
        $p->newPoint('.helper1', $model->m('shoulderToShoulder') * self::NECK_OPENING_RATIO/2, -10);
        $p->newPoint('.helper2', $model->m('shoulderToShoulder') * self::NECK_OPENING_RATIO/2, 10);
        $p->addPoint('new8', $p->beamsCross('.helper1','.helper2',8,12));
        $p->addPoint('.helper3', $p->rotate(12,'new8',-90));
        $p->addPoint('new20', $p->beamsCross('new8','.helper3',1,20));

        // Adapt length of the body
        $p->newPoint('new4', $p->x(4), $p->y(4)+$this->o('lengthBonus')-$this->o('ribbingWidth'));
        $p->newPoint('new6', $model->m('hipsCircumference')/4 + $this->o('hipsEase')/4, $p->y(6)+$this->o('lengthBonus')-$this->o('ribbingWidth'));
        $p->newPoint('new6cp', $p->x('new6')-15, $p->y(3));
        
        // Paths
        $path = 'M 1 L 2 L 3 L new4 L new6 C new6cp 5 5 C 13 16 14 C 15 18 10 C 17 19 12 L new8 C new20 1 1 z';
        $p->newPath('seamline', $path, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);

        // Store length of the neck seam
        $this->setValue('backNeckSeamLength', $p->curveLen('new8','new20',1,1));
    }

    /**
     * Drafts the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFront($model)
    {
        $this->clonePoints('frontBlock','front');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];
        // Widen neck opening
        $cplen = $p->distance(8,20);
        $p->newPoint('.helper1', $model->m('shoulderToShoulder') * self::NECK_OPENING_RATIO/2, -10);
        $p->newPoint('.helper2', $model->m('shoulderToShoulder') * self::NECK_OPENING_RATIO/2, 10);
        $p->addPoint('new8', $p->beamsCross('.helper1','.helper2',8,12));
        $p->addPoint('.helper3', $p->rotate(12,'new8',-90));
        $p->addPoint('new20', $p->beamsCross('new8','.helper3',1,20));

        // Adapt length of the body
        $p->newPoint('new4', $p->x(4), $p->y(4)+$this->o('lengthBonus')-$this->o('ribbingWidth'));
        $p->newPoint('new6', $model->m('hipsCircumference')/4 + $this->o('hipsEase')/4, $p->y(6)+$this->o('lengthBonus')-$this->o('ribbingWidth'));
        $p->newPoint('new6cp', $p->x('new6')-15, $p->y(3));

        
        if($this->o('pouch') == 1) {
            // Front pouch
            $p->newPoint('pouch1', 0, $p->y(4) * 0.70);
            $p->newPoint('pouch2', $p->x('new6') * 0.4, $p->y('pouch1'));
            $p->newPoint('pouch3', $p->x('new6') * 0.67, $p->y('new6')*0.915);
            $p->newPoint('pouch4', $p->x('pouch3'), $p->y('new6'));
            $p->newPath('pouch', 'M pouch1 L pouch2 L pouch3 L pouch4', ['class' => 'help']);
            $p->paths['pouch']->setSample(true);
        }
        
        // Paths
        $path = 'M 9 L 2 L 3 L new4 L new6 C new6cp  5 5 C 13 16 14 C 15 18 10 C 17 19 12 L new8 C new20 21 9 z';
        $p->newPath('seamline', $path, ['class' => 'fabric']);
       
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);

        // Store length of the neck seam
        $this->setValue('frontNeckSeamLength', $p->curveLen('new8','new20',21,9));
    }

    /**
     * Drafts the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSleeve($model)
    {
        $this->clonePoints('sleeveBlock','sleeve');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeve'];

        // Adapt sleeve length
        $p->newPoint('new32', $model->m('wristCircumference')/2 + $this->o('cuffEase')/2 + $this->o('cuffDrape')/2, $model->m('shoulderToWrist') + $this->o('sleeveLengthBonus') - $this->o('ribbingWidth'));
        $p->addPoint('new31', $p->flipX('new32',0));
        

        $path = 'M new31 L -5 C -5 20 16 C 21 10 10 C 10 22 17 C 23 28 30 C 29 25 18 C 24 11 11 C 11 27 19 C 26 5 5 L new32 z';
        $p->newPath('seamline', $path, ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Drafts the hood
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftHood($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['hood'];

        $base = $this->v('frontNeckSeamLength') + $this->v('backNeckSeamLength');


        $p->newPoint(1, 0,0);
        $p->newPoint(2, $base,0);
        $p->addPoint(2, $p->rotate(2,1,5));
        $p->newPoint(3, 0, $model->m('headCircumference') * -0.59); // Height is 59% of head circumference
        $p->newPoint(4, $p->x(2), $p->y(3));

        $p->addPoint(5, $p->shiftTowards(1,2,$model->m('headCircumference') * 0.1));
        $p->addPoint(5, $p->rotate(5,1,90));
        $p->newPoint(6, $model->m('headCircumference') * 0.135, $p->y(3));
        $p->newPoint('6cpBot', $p->x(6), $p->y(5));

        $p->newPoint(7, $p->x(2) + $model->m('headCircumference') * 0.085, $p->y(3)*0.6);
        $p->addPoint('7cpTop', $p->shift(7, 90, $model->m('headCircumference') * 0.1));
        $p->addPoint('7cpBot', $p->shift(7,-90, $model->m('headCircumference') * 0.1));


        $path = 'M 1 L 5 C 5 6cpBot 6 C 4 7cpTop 7 C 7cpBot 2 2 L 1 z';
        $p->newPath('seamline', $path, ['class' => 'fabric']);

        // Store length useable by zipper
        $this->setValue('hoodZipLength', $p->distance(1,5));

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Drafts the pouch
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPouch($model)
    {
        $this->clonePoints('front','pouch');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['pouch'];

        $p->newPath('seamline', 'M new4 L pouch1 L pouch2 L pouch3 L pouch4 z', ['class' => 'fabric']);


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
     * Finalizes the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        if($this->o('sa')) {
            // Seam allowance | Point indexes from 200 upward
            $p->offsetPath('sa', 'seamline', $this->o('sa') * -1, 1, ['class' => 'fabric sa']);
        }
        
        if($this->o('pouch')) {
            $p->newPath('pouch', 'M pouch1 L pouch2 L pouch3 L pouch4', ['class' => 'fabric help']);
            $p->notch(['pouch1']);
        }

        // Grainline
        $p->newPoint('grainlineTop', 35, $p->y(9) + 20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('new4') - 20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(5) * 0.4, $p->x(5), 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', -90, 70));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('titleAnchor', -90, 110));
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Note
        $len = $p->distance(9,'new4') + $this->o('ribbingWidth') + $this->v('hoodZipperLength');
        $p->newNote(1,3,$this->t('Maximum zipper length is ').$p->unit($len), 3, 50);

        // Facing
        $p->addPoint('facingTop', $p->shiftFractionTowards('new8', 12, 0.3));
        $p->newPoint('facingBottom', $p->x('facingTop'), $p->y('new6'));
        $p->newPath('facing', 'M facingTop L facingBottom', ['class' => 'fabric help']);
        $p->newPoint('facingNoteAnchor', $p->x('facingTop'), $p->y(10));
        $p->newNote(3,'facingNoteAnchor',$this->t('Facing edge')."\n".'Cut everything left from this line'."\n".'2x '.$this->t('from fabric'), 9);
    }


    /**
     * Finalizes the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        if($this->o('sa')) {
            // Seam allowance | Point indexes from 200 upward
            $p->offsetPathString('sa', 'M 1 C 1 new20 new8 L 12 C 19 17 10 C 18 15 14 C 16 13 5 C 5 new6cp new6 L new4', $this->o('sa'), 1, ['class' => 'fabric sa']);
            $p->newPath('saClose', 'M 9 L sa-startPoint M sa-endPoint L new4', ['class' => 'fabric sa']);
        }
        
        // Cut on fold line and grainline
        $p->newPoint('cofTop', 0, $p->y(1) + 20, 'Cut on fold top');
        $p->newPoint('cofBottom', 0, $p->y('new4') - 20, 'Cut on fold bottom');
        $p->newPoint('grainlineTop', 35, $p->y('cofTop'));
        $p->newPoint('grainlineBottom', 35, $p->y('cofBottom'));

        $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on fold'));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(5) * 0.4, $p->x(5), 'Title anchor');
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', -90, 70));
        $p->newSnippet('logo', 'logo', 'logoAnchor');
    }

    
    /**
     * Finalizes the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeve'];
        
        if($this->o('sa')) {
            // Seam allowance | Point indexes from 200 upward
            $p->offsetPath('sa', 'seamline', $this->o('sa'), 1, ['class' => 'fabric sa']);
        }

        // Grainline
        $p->newPoint('grainlineTop', 0, 20);
        $p->newPoint('grainlineBottom', 0, $p->y('new31') - 20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(5) * 0.4, $p->y(33), 'Title anchor');
        $p->addTitle(33, 3, $this->t($p->title), '2x '.$this->t('from fabric'));

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', -90, 70));
        $p->newSnippet('logo', 'logo', 2);

    }


    /**
     * Finalizes the hood
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeHood($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['hood'];
        
        if($this->o('sa')) {
            // Seam allowance 
            $p->offsetPath('sa', 'seamline', $this->o('sa'), 1, ['class' => 'fabric sa']);
        }
        // Grainline
        $p->newPoint('grainlineTop', $p->x(6)+20, $p->y(6)+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2));
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(7) * 0.6, $p->y(7), 'Title anchor');
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '4x '.$this->t('from fabric'));
    }


    /**
     * Finalizes the pouch
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePouch($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pouch'];
        
        if($this->o('sa')) {
            // Seam allowance 
            $p->offsetPath('sa', 'seamline', $this->o('sa'), 1, ['class' => 'fabric sa']);
            // Extra sa/facing
            $p->addPoint('sa-line-pouch2TOpouch3', $p->shiftTowards('pouch2','sa-line-pouch2TOpouch3',25));
            $p->addPoint('sa-line-pouch3TOpouch2', $p->shiftTowards('pouch3','sa-line-pouch3TOpouch2',25));
        }
        
        // Grainline
        $p->newPoint('grainlineTop', $p->x('pouch1')+20, $p->y('pouch1')+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('new4')-20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x('pouch4') / 2, $p->y('pouch3'), 'Title anchor');
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '2x '.$this->t('from fabric'));
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
     * Adds paperless info for the front
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFront($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['front'];

        // Height on the left
        $p->newHeightDimension('new4', 9, -25);
        $p->newHeightDimension(9, 'new8', -25);
        $p->newHeightDimension('new4', 'new8', -40);

        // Height on the right
        $xbase = $p->x(5);
        $p->newHeightDimension('new6', 5, $xbase + 25);
        $p->newHeightDimension(5, 12, $xbase + 25);
        $p->newHeightDimension('new6', 12, $xbase + 40);
        $p->newHeightDimensionSm(12, 'new8', $xbase + 25);

        // Width at the top
        $ybase = $p->y('new8');
        $p->newWidthDimension(9, 'new8', $ybase - 25);
        $p->newWidthDimension(9, 12, $ybase - 40);
        $p->newWidthDimension(9, 5, $ybase - 55);
        $p->newLinearDimension('new8', 12, -25);
        $p->newWidthDimension(9, 10, $p->y(10)) ;

        // Width at the bottom
        $ybase = $p->y('new4');
        $p->newWidthDimension('new4', 'new6', $ybase + 25);

    }

    /**
     * Adds paperless info for the back
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBack($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['back'];

        // Height on the left
        $p->newHeightDimension('new4', 1, -25);
        $p->newHeightDimension('new4', 'new8', -40);

        // Height on the right
        $xbase = $p->x(5);
        $p->newHeightDimension('new6', 5, $xbase + 25);
        $p->newHeightDimension(5, 12, $xbase + 25);
        $p->newHeightDimension('new6', 12, $xbase + 40);
        $p->newHeightDimensionSm(12, 'new8', $xbase + 25);
        
        // Width at the top
        $ybase = $p->y('new8');
        $p->newWidthDimension(9, 'new8', $ybase - 25);
        $p->newWidthDimension(9, 12, $ybase - 40);
        $p->newWidthDimension(9, 5, $ybase - 55);
        $p->newLinearDimension('new8', 12, -25);
        $p->newWidthDimension(9, 10, $p->y(10)) ;

        // Width at the bottom
        $ybase = $p->y('new4');
        $p->newWidthDimension('new4', 'new6', $ybase + 25);
    }

    /**
     * Adds paperless info for the sleeve
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSleeve($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sleeve'];
        
        // Height on the left
        $xbase = $p->x(-5);
        $p->newHeightDimension('new31', -5, $xbase-25);
        $p->newHeightDimension('new31', 1, $xbase-40);
        
        // Width at the top
        $p->newWidthDimension(-5, 5, -25);
        
        // Width at the bottom
        $ybase = $p->y('new31');
        $p->newWidthDimension('new31', 'new32', $ybase + 25);
    }

    /**
     * Adds paperless info for the hood
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessHood($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['hood'];
        
        // Height on the left
        $xbase = $p->x(1);
        $p->newHeightDimension(1, 5, $xbase-25);
        $p->newHeightDimension(1, 6, $xbase-40);
        
        // Height on the right
        $p->newHeightDimensionSm(1, 2, $p->x(2)+25);
        $p->newHeightDimension(1, 7, $p->x(7)+25);

        // Width
        $ybase = $p->y(1);
        $p->newWidthDimension(1,2,$ybase+25);
        $p->newWidthDimension(5,2,$ybase+40);
        $p->newWidthDimension(5,6,$p->y(6)-25);
        $p->newWidthDimension(6,7,$p->y(6)-25);

    }

    /**
     * Adds paperless info for the pouch
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessPouch($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['pouch'];

        $p->newHeightDimension('new4','pouch1', $p->x('new4')-25);
        $p->newHeightDimension('pouch4','pouch3', $p->x('pouch4')+25);
        $p->newWidthDimension('new4','pouch4', $p->y('pouch4')+25);
        $p->newWidthDimension('pouch1','pouch2', $p->y('pouch1')-25);

        $p->addPoint('noteAnchor', $p->shiftFractionTowards('pouch2','pouch3',0.5));
        $p->newNote(1, 'noteAnchor', $p->unit(25).' '.$this->t('facing'), 9, 20, -10 );
    }
}
