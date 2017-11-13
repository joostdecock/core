<?php
/** BenjaminBowTie class */
namespace Freesewing\Patterns\Beta;

use Freesewing\Utils;
use Freesewing\BezierToolbox;

/**
 * The Benjamin Bow Tie pattern
 *
 * @author Wouter van Wageningen
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BenjaminBowTie extends \Freesewing\Patterns\Core\Pattern
{

   /** Constants for the tie shape */
    const BUTTERFLY = 1;
    const DIAMOND = 2;
    const SQUARE = 3;
    const WIDESQUARE = 4;

    /** Constants for the tip */
    const POINTED = 1;
    const ROUNDED = 2;

    /** Constant for the offset of the paperless dinemsions */
    const OFFSET = 15;

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
        if( $this->o('bowStyle') == 'square' ) $this->setOption( 'tipWidth', $this->o('knotWidth') );
        
        // Some helper vars
        $this->setValue('backWidth', 24);
        $this->setValue('halfBackWidth', $this->v('backWidth') / 2);
        $this->setValue('halfTipWidth',  $this->o('tipWidth')  / 2);
        $this->setValue('halfBowLength', $this->o('bowLength') / 2);
        $this->setValue('halfKnotWidth', $this->o('knotWidth') / 2);
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

        $this->draftBowTie($model, 'bowTie1', 0);
        if( $this->o('adjustmentRibbon') ) {
            //$this->draftBowTie($model, 'bowTie2', 315 );
            $this->draftBowTie($model, 'bowTie2', 290 );
            $this->draftBowTie($model, 'bowTie3',  90 );
        } else {
            $this->draftCollarBand($model);
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

        $this->finalizeBowTie( $model, 'bowTie1' );
        if( $this->o('adjustmentRibbon') ) {
            $this->finalizeBowTie( $model, 'bowTie2' );
            $this->finalizeBowTie( $model, 'bowTie3' );
        } else {
            $this->finalizeCollarBand( $model );
        }

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            $this->paperlessBowTie( $model, 'bowTie1' );
            if( $this->o('adjustmentRibbon') ) {
                $this->paperlessBowTie( $model, 'bowTie2' );
                $this->paperlessBowTie( $model, 'bowTie3' );
            } else {
                $this->paperlessCollarBand( $model );
            }
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
    public function draftBowTie($model, $part, $shift)
    {
        $p = $this->parts[$part];

        $bowStyle = $this->o('bowStyle');
        $butterfly = ($bowStyle == 'butterfly' || $bowStyle == 'diamond');

        if( $part == 'bowTie1' ) {
            if( $this->o('endStyle') == 'pointed' ) {
                $tipAdjustment = $this->v('halfTipWidth')/1.3;
            } elseif( $this->o('endStyle') == 'rounded' ) {
                $tipAdjustment = $this->v('halfTipWidth');
            } else {
                $tipAdjustment = 0;
            }

            $hhbl /*halfHalfBowLength */ = ($this->v('halfBowLength')/2);
            $bcAdjust = ($bowStyle == 'diamond' ? 10 : $hhbl -5 );

            $p->newPoint('Origin', 0, 0, 'Origin');

            $p->addPoint(2, $p->shift('Origin', 90, $this->v('halfBackWidth')), 'Top Left of bow tie shape');
            $p->addPoint(3, $p->shift(2, 0, 70), 'Start');
            $p->newPoint(4, $p->x(3) +50, $p->y('Origin') -$this->v('halfKnotWidth'), 'Start of bow');
            $p->newPoint(5, $p->x(4) +$this->v('halfBowLength'), $p->y('Origin') -$this->v('halfTipWidth'), 'Middle of bow');
            if( $butterfly ) {
                $p->addPoint('4b', $p->shiftOutwards(3, 4, $bcAdjust), 'Helper');
                $p->newPoint('5a', $p->x(5) -$bcAdjust, $p->y(5), 'Helper');
                $p->newPoint('5b', $p->x(5) +$bcAdjust, $p->y(5), 'Helper');
                $p->newPoint(6, $p->x(5) +$this->v('halfBowLength'), $p->y(4), 'End of bow');
                $p->newPoint('6b', $p->x(6) +$bcAdjust, $p->y(6), 'Helper');
            }
            $p->newPoint(7, $p->x(4) +($this->v('halfBowLength') *3), $p->y('Origin') -$this->v('halfTipWidth'), 'Tip of bow');

            if( $butterfly ) {
                $p->newPoint('6a', $p->x(6) -$bcAdjust, $p->y(6), 'Helper');
                $p->newPoint('7a', $p->x(7) -$bcAdjust, $p->y(7), 'Helper');
            }

            $p->newPoint(8, $p->x(7) +$tipAdjustment, $p->y('Origin'), 'Tip');
            $p->newPoint('7b', $p->x(8), $p->y(7), 'Helper');
            $p->newPoint('8a', $p->x(8), $p->y(8) -($this->v('halfTipWidth')/4), 'Helper');

            if( $butterfly ) {
                $flip = [2, 3, 4, '4b', 5, '5a', '5b', 6, '6a', '6b', 7, '7a', '7b', '8a'];
            } else {
                $flip = [2, 3, 4, 7, '7b', '8a'];
            }
            foreach( $flip as $id ) {
                $p->addPoint('r'.$id,$p->flipY($id));
            }
        } else {
            $this->clonePoints( 'bowTie1', $part );

            $shiftPoints = [3, 4, 7, '7b', 8, '8a', 'r8a', 'r7b', 'r7', 'r4', 'r3'];

            if( $butterfly ) {
                $shiftPoints = array_merge( $shiftPoints, ['4b', 5, '5a', '5b', 6, '6a', '6b', '7a', 'r7a', 'r6b', 'r6a', 'r6', 'r5b', 'r5a', 'r5', 'r4b' ]);
            }
            foreach( $shiftPoints as $id ) {
                $p->addPoint($id, $p->shift($id, 0, $shift));
            }
        }

        if( $this->o('endStyle') == 'rounded' ) {
            $tipPath = 'C 7b 8a 8 C r8a r7b';
        } else {
            $tipPath = 'L 8 L';
        }
        if( $butterfly ) {
            $p->newPath('outline', 'M 2 L 3 L 4 C 4b 5a 5 C 5b 6a 6 C 6b 7a 7 '.$tipPath.' r7 C r7a r6b r6 C r6a r5b r5 C r5a r4b r4 L r3 L r2 Z', ['class' => 'fabric']);
        } else {
            $p->newPath('outline', 'M 2 L 3 L 4 L 7 '.$tipPath.' r7 L r4 L r3 L r2 Z', ['class' => 'fabric']);
        }

        // Mark for sampler
        $p->paths['outline']->setSample(true);
    }

    /**
     * Drafts a rectangle to be placed between the bow tie shapes
     *
     * @return void
     */
    public function draftCollarBand($model)
    {
        $p = $this->parts['collarBand'];

        $collarLength = $model->m('neckCircumference') +$this->o('collarEase') +(($this->o('knotWidth') *2) -108);

        $p->newPoint('Origin', 0, 0, 'Origin');

        $p->addPoint(2, $p->shift('Origin', 90, $this->v('halfBackWidth')), 'Top Left of collar band');
        $p->addPoint(3, $p->shift(2, 0, $collarLength), 'TR');
        foreach( array (2, 3) as $id ) {
            $p->addPoint('r'.$id,$p->flipY($id));
        }

        $p->newPath('outline', 'M 2 L 3 L r3 L r2 Z', ['class' => 'fabric']);

        // Mark for sampler
        $p->paths['outline']->setSample(true);
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
    public function finalizeBowTie($model, $part)
    {
        $p = $this->parts[$part];

        if( $part == 'bowTie1' ) {
            $copies = ( $this->o('adjustmentRibbon') ? 1 : 4 );
        } else {
            $copies = ( $part == 'bowTie2' ? 1 : 2 );
        }

        $p->newPoint('grainlineLeft', $p->x('Origin') +30, $p->y('Origin') );
        $p->newPoint('grainlineRight', $p->x(4), $p->y('Origin') );

        $p->newGrainline('grainlineLeft', 'grainlineRight', $this->t('Grainline'));

        if( $this->o('sa') ) {
            $p->offsetPath('seamAllowance', 'outline', $this->o('sa'), true, ['class' => 'fabric sa'] );
        }

        // Title
        $p->newPoint('titleAnchor', $p->x(5), $p->y('Origin') +6, 'Title point');
        $p->addTitle('titleAnchor', substr($part, -1), $this->t($p->title), $copies.'x '.$this->t('from fabric').', '.$copies.'x '.$this->t('from interfacing'), 'extrasmall');

        if( $part == 'bowTie1' ) {
            // Scalebox
            $p->newPoint('scaleboxAnchor', $p->x('Origin') +50, $p->y('r7') +30);
            $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

            // logo
            $p->addPoint('logoAnchorHelper', $p->shift(8, 180, 25));
            $p->addPoint('logoAnchor', $p->shift('logoAnchorHelper', 270, 6));
            $p->newSnippet('logo', 'logo-sm', 'logoAnchor');

            // Dummy line
            $p->addPoint('h1', $p->shift('scaleboxAnchor', -90, 50 ));
            $p->addPoint('h2', $p->shift('h1', 1, 0 ));

            $p->newPath('hidden', 'M h1 L h2', ['class' => 'hidden']);

            // Mark for sampler
            $p->paths['hidden']->setSample(true);
        }
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
