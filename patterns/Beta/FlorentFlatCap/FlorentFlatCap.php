<?php
/** Freesewing\Patterns\Beta\FlorentFlatCap class */
namespace Freesewing\Patterns\Beta;

/**
 * A flat cap pattern by Quentin Felix
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class FlorentFlatCap extends \Freesewing\Patterns\Core\Pattern
{

    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /* the repartition between top and side. defined as side/total circumference */
    const REPARTITION_CIRCUMFERENCE = 0.8;
    
    const BRIM_EXTRA = 0;

    /**
     * Sets up options and values for our draft
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
        $this->setValueIfUnset('coef', 1);
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
        $this->initialize($model);

        $tries = 0;
        do {
            $this->draftTop($model);
            $this->draftSide($model);

            if ($this->headCircDelta($model)<0) $this->setValue('coef', $this->v('coef')*1.03);
            else $this->setValue('coef', $this->v('coef')*0.99);
        
            $tries++;
        } while ($tries < 70 and abs($this->headCircDelta($model)) > 0.8);
        $this->msg("After $tries attempts, head circumference is ".round($delta,2).'mm off.');
        
        $this->draftBrimBottom($model);
        $this->draftBrimTop($model);
        $this->draftBrimPlastic($model);
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
        $this->finalizeTop($model);
        $this->finalizeSide($model);
        
        $this->finalizeBrimBottom($model);
        $this->finalizeBrimTop($model);
        $this->finalizeBrimPlastic($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            $this->paperlessTop($model);
            $this->paperlessSide($model);
            $this->paperlessBrimTop($model);
            $this->paperlessBrimBottom($model);
            $this->paperlessBrimPlastic($model);
        }
    }


    /**
     * Drafts the Top
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftTop($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['top'];
		
        $p->newPoint(1, 0, 0, 'Middle front');
        $p->clonePoint(1, 'gridAnchor');
		$p->newPoint(2, $this->v('coef')*202, 0, 'Middle start of curve');
		$p->addPoint(13, $p->shiftOutwards(1, 2, $this->v('coef')*48),'Middle start of curve handle');
		$p->newPoint(3, $this->v('coef')*388.5, $this->v('coef')*73.5, 'Middle back');
		$p->newPoint(400, 0, $this->v('coef')*40, 'Side front handle');
		$p->addPoint(4, $p->shift (1, $p->angle(2, 1)+90, $this->v('coef')*44));

		$p->newPoint(6, $this->v('coef')*150, $this->v('coef')*106, 'Side side point');
		$p->addPoint(5, $p->shift (6, $p->angle(2, 1), $this->v('coef')*110));
		$p->addPoint(7, $p->shift (6, $p->angle(1, 2), $this->v('coef')*60));
		$p->newPoint(9, $this->v('coef')*290, $this->v('coef')*80.8, 'Side inner curve point');
		$p->addPoint(8, $p->shift (9, $p->angle(2, 1), $this->v('coef')*20));
		$p->addPoint(10, $p->shift (9, $p->angle(1, 2), $this->v('coef')*20));	
		$p->newPoint(12, $this->v('coef')*342, $this->v('coef')*110, 'Top side point ');
		$p->addPoint(30, $p->shift (12, $p->angle(12, 3)+90, $this->v('coef')*15));
		$p->addPoint(31, $p->shift (3, $p->angle(12, 3)+90, $this->v('coef')*15));
		$p->addPoint(11, $p->shiftOutwards(12, 30, $this->v('coef')*8), 'handle');	
		$p->addPoint(14, $p->shiftOutwards(3, 31, $this->v('coef')*34), 'handle');	
		
		$p->addPoint(33, $p->shiftFractionTowards(2,31,0.52), 'construction point for middle');
		$p->addPoint(32, $p->shift(33, $p->angle(31, 2)-90, $this->v('coef')*13.5), 'middle point');
		
		$p->addPoint(34, $p->shift(32, $p->angle(33, 32)+90, $this->v('coef')*13), 'middle point');
		$p->addPoint(35, $p->shift(32, $p->angle(33, 32)-90, $this->v('coef')*32), 'middle point');
		
		$p->addPoint(43, $p->shiftFractionTowards(6,9,0.65), 'construction point for middle');
		$p->addPoint(42, $p->shift(43, $p->angle(9, 6)-90, -1.5*$this->v('coef')), 'middle point');
		
		$p->addPoint(44, $p->shift(42, $p->angle(43, 42)+90, $this->v('coef')*5), 'middle point');
		$p->addPoint(45, $p->shift(42, $p->angle(43, 42)-90, $this->v('coef')*5), 'middle point');
		
		
		$path = 'M 1 L 2 C  13 34 32 C 35 14 31 L 3 L 12 L 30 C 11 10 9 C 8 44 42 C 45 7 6 C 5 4 1 Z ';
		$p->newPath('seamline', $path, ['class' => 'fabric']);	
		$p->paths['seamline']->setSample(true);
		$this->setValue('topHeadCirc', 2*$p->distance(3,12));
        $p->newPoint('samplerAnchor', $p->x('2'),$p->y('2'));
    }

	public function draftSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];
		
		$p->newPoint(1, 0, 0);
        $p->clonePoint(1, 'gridAnchor');
		$p->newPoint(2, $this->v('coef')*75, 0 );
		$p->addPoint(3, $p->shift (1, $p->angle(2, 1)+90, $this->v('coef')*25), 'handle for 1');
		$p->addPoint(4, $p->shift (2, $p->angle(2, 1)+90, $this->v('coef')*50), 'handle for 2');
		$p->addPoint(5, $p->shiftOutwards(1, 2, $this->v('coef')*169.5), 'construction point for 6' );
		$p->addPoint(6, $p->shift (5, $p->angle(2, 1)+90, $this->v('coef')*123));

		$p->addPoint(8, $p->shift (6, $p->angle(2, 1)+87, $this->v('coef')*15));
		$p->addPoint(7, $p->shift (6, $p->angle(8, 6)+90, $this->v('coef')*40), 'handle for 6');
		$p->addPoint(9, $p->shift (8, $p->angle(6, 8), $this->v('coef')*60), 'handle for 8');
		$p->addPoint(10, $p->shiftOutwards(1, 2, $this->v('coef')*120), 'construction point for 11' );
		$p->addPoint(11, $p->shift (10, $p->angle(2, 1)+90, $this->v('coef')*200));
		$p->addPoint(12, $p->shift(11, $p->angle(1, 2), $this->v('coef')*20), 'handle 1 for 11');
		$p->addPoint(13, $p->shift(11, $p->angle(2, 1), $this->v('coef')*25), 'handle 2 for 11');
		
		// shifting the curve between 11 and 1
		$p->addPoint(14, $p->shiftFractionTowards(11,1,0.58), 'construction point for 15');
		$p->addPoint(15, $p->shift(14, $p->angle(1, 11)-90, $this->v('coef')*42.5), 'point between 1 and 11');
		
		$p->addPoint(16, $p->shift(15, $p->angle(14, 15)+90, $this->v('coef')*85), 'handle 1 for 14');
		$p->addPoint(17, $p->shift(15, $p->angle(16, 15),$this->v('coef')*45), 'handle 2 for 14');

		// shifting the curve between 2 and 6	
		$p->addPoint(18, $p->shiftFractionTowards(6,2,0.5), 'construction point for 19');
		$p->addPoint(19, $p->shift(18, $p->angle(2, 6)-90, $this->v('coef')*50), 'point between 2 and 6');
		
		$p->addPoint(20, $p->shift(19, $p->angle(18, 19)+90, $this->v('coef')*30), 'handle 1 for 18');
		$p->addPoint(21, $p->shift(19, $p->angle(18, 19)-90, $this->v('coef')*20), 'handle 1 for 18');		

		
		$path1 = 'M 2 C 4 21 19 C 20 7 6 L 8 C 9 12 11 C 13 16 15 C 17 3 1';
		$p->newPath('seamline1', $path1, ['class' => 'fabric']);
		$path2 = 'M 1 L 2';
		$p->newPath('seamline2', $path2, ['class' => 'fabric']);
		$this->setValue('sideHeadCirc',2* $p->curveLen(2, 4, 21, 19) + 2* $p->curveLen(19, 20, 7, 6));
		$p->paths['seamline1']->setSample(true);
        $p->paths['seamline2']->setSample(true);

        
        // Tweak sideseam to fit the top part
        // but only if head circumference is ok
        if(abs($this->headCircDelta($model)) <= 1) {
            $tries = 0;
            $delta = $this->sideSeamDelta();
            while (abs($delta)>1 && $tries < 50) {
                $tries++;
                $angle = $delta/2;
                $p->addPoint(8, $p->rotate(8, 6, $angle));
                $p->addPoint(9, $p->rotate(9, 6, $angle));
                $delta = $this->sideSeamDelta();
            }
            $this->msg("After $tries attempts, side seam is ".round($delta,2).'mm off.');
        }
    }

	public function draftBrimBottom($model)
    {
        /** @var \Freesewing\Part $p */
		$p = $this->parts['brimBottom'];

		$p->newPoint(1, $this->v('coef')*-88, $this->v('coef')*-78);
		$p->addPoint(2, $p->shift(1, -65, $this->v('coef')*30), 'handle for inner border');
		$p->newPoint(4, 0, 0, 'middle of inner border');
		$p->addPoint(3, $p->shift(4, 180, $this->v('coef')*70), 'Handle for inner border middle');
		$p->addPoint(5,$p->flipX(3,$p->x(4)));
		$p->addPoint(6,$p->flipX(2,$p->x(4)));
		$p->addPoint(7,$p->flipX(1,$p->x(4)));
		
		$p->addPoint(8, $p->shift(1, -105, $this->v('coef')*118), 'Handle for outer border');
		$p->addPoint(10, $p->shift(4, -90, $this->v('coef')*58), 'top of the brim');
        $p->clonePoint(10, 'gridAnchor');
		$p->addPoint(9, $p->shift(10, 180, $this->v('coef')*40), 'Handle for top of the brim');
		
		$p->addPoint(11,$p->flipX(8,$p->x(4)));
		$p->addPoint(12,$p->flipX(9,$p->x(4)));
		
		$path = 'M 1 C 2 3 4 C 5 6 7 C 11 12 10 C 9 8 1  z';
		$p->newPath('seamline', $path, ['class' => 'fabric']);
		$p->paths['seamline']->setSample(true);
    }

	public function draftBrimTop($model)
    {	
		$p = $this->parts['brimTop'];
		$this->clonePoints('brimBottom', 'brimTop');

		$pathinner = 'M 1 C 2 3 4 C 5 6 7';
		$p->newPath('seamline1', $pathinner, ['class' => 'fabric']);
		$p->paths['seamline1']->setRender(false);
		
		$pathouter = 'M 7 C 11 12 10 C 9 8 1';
		$p->newPath('seamline2', $pathouter, ['class' => 'hint']);
		$p->paths['seamline2']->setRender(false);
		

		$p->offsetPath('seamline45', 'seamline2', 3, false, ['class' => 'fabric']);

		$allpathstring = $pathinner;
		$addthis =  $p->paths['seamline45']->getPathstring();
		$addthis = substr ( $addthis , 2 , strlen( $addthis )-1);
		$allpathstring .=' L '. $addthis.' Z';

		$p->newPath('seamline10', $allpathstring, ['class' => 'fabric']);
		//$p->paths['seamline45']->setSample(true);
		//$p->paths['seamline10']->setSample(true);
    }
	
	public function draftBrimPlastic($model)
    {	
		$p = $this->parts['brimPlastic'];
		 $this->clonePoints('brimBottom', 'brimPlastic');
		$p->addPoint(501, $p->shiftAlong(1,2,3,4,3));
		$p->splitCurve(1,2,3,4,501,'s');	

		$p->addPoint(502,$p->flipX(501,$p->x(4)));
		$p->addPoint(503,$p->flipX('s7',$p->x(4)));
		$p->addPoint(504,$p->flipX('s6',$p->x(4)));

		$pathinner = 'M 501 C s7 s6 4 C 504 503 502';
		$p->newPath('seamline1', $pathinner, ['class' => 'hint']);
		$p->paths['seamline1']->setRender(false);		
		
		$p->addPoint(511, $p->shiftAlong(1,8,9,10,4));
		$p->addPoint(512,$p->flipX(511,$p->x(4)));
		$p->splitCurve(1,8,9,10,511,'t');
		$p->addPoint(513,$p->flipX('t7',$p->x(4)));
		$p->addPoint(514,$p->flipX('t6',$p->x(4)));
		$pathouter = 'M 512 C 513 514 10 C t6 t7 511';
		$p->newPath('seamline2', $pathouter, ['class' => 'hint']);
		$p->paths['seamline2']->setRender(false);

		$p->offsetPath('seamline60', 'seamline1', -1.5, true, ['class' => 'fabric']);
		$p->offsetPath('seamline65', 'seamline2', 1.5, true, ['class' => 'fabric']);		
		$p->newPath('seamline3', "M seamline60-startPoint L seamline65-endPoint z", ['class' => 'fabric']);
		$p->newPath('seamline4', "M seamline65-startPoint L seamline60-endPoint z", ['class' => 'fabric']);
		
    }

    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/seam-allowance/grainline and so on
    */
   public function finalizeTop($model)
    {
        /** @var Part $p */
        $p = $this->parts['top'];
		
		// Seam allowances
		if($this->o('sa')) {
			$p->offsetPath('sideSA', 'seamline', $this->o('sa'), 1, ['class' => 'fabric sa']);
		}
		
		// Grainline
        $p->newPoint('grainlineTop', 0.8*$p->x(1)+0.2*$p->x(2),$p->y(32));
        $p->newPoint('grainlineBottom',  0.2*$p->x(2)+ 0.8*$p->x(32), $p->y('grainlineTop'));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(6),  0.5*($p->y(grainlineTop) +$p->y(6)) , 'Title anchor');
        $p->addTitle('titleAnchor', 1, $this->t($p->title), '2x main, 2x lining','small');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',0, 50));
        $p->newSnippet('logo', 'logo', 'logoAnchor');

        // Notches
        $p->notch([6,9,2]);
        $this->setValue('sideSeamToFirstNotch', $p->distance(12,30) + $p->curveLen(30,11,10,9));
        $this->setValue('sideSeamToSecondNotch', $this->v('sideSeamToFirstNotch') + $p->curveLen(9,8,44,42) + $p->curveLen(42,45,7,6));
    }
	
	 public function finalizeSide($model)
    {
        /** @var Part $p */
        $p = $this->parts['side'];
		
		// Seam allowances
		if($this->o('sa')) {
			$p->offsetPath('path51', 'seamline1', $this->o('sa'), 1,['class' => 'fabric sa'] );
			$p->newPath('path52', 'M 2 L path51-startPoint',['class' => 'fabric sa'] );
			$p->newPath('path53', 'M 1 L path51-endPoint',['class' => 'fabric sa'] );
		}

        // Cut on fold
        $p->newPoint('cofTop', $p->x(1) + 10, $p->y(1) + 0, 'Cut on fold top');
        $p->newPoint('cofBottom', $p->x(2) - 10, $p->y('cofTop'), 'Cut on fold bottom');
        $p->newCutonfold('cofTop','cofBottom',  $this->t('Cut on fold'));

		// Grainline
		$p->newPoint('grainlineTop', 0.5*( $p->x(19)+ $p->x(15)),0.5*( $p->y(8)+$p->y(6)));
        $p->newPoint('grainlineBottom', $p->x(8)-10, $p->y('grainlineTop'));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(11),  0.5*($p->y(grainlineTop) +$p->y(11)) , 'Title anchor');
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x  main, 1x lining '.$this->t('Cut on fold'),'small');

        // Logo
        $p->addPoint('logoAnchor', $p->shiftFractionTowards(14,15, 0.5));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');

        // Scalebox
        $p->newPoint('scaleboxAnchor', $p->x(6)-60,$p->y(2)+10);
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');

        // Notches
        $this->msg('Side seam to first notch is '.$this->v('sideSeamToFirstNotch'));
        $p->addPoint('notch1', $p->shiftAlong(8,9,12,11,$this->v('sideSeamToFirstNotch') - $p->distance(6,8)));
        $p->addPoint('notch2', $p->shiftAlong(11,13,16,15,$this->v('sideSeamToSecondNotch') - $p->distance(6,8) - $p->curveLen(8,9,12,11)));
        $p->notch(['notch1','notch2']);

    }
	
	   public function finalizeBrimBottom($model)
    {
        /** @var Part $p */
        $p = $this->parts['brimBottom'];
		
		// Seam allowances
		if($this->o('sa')) $p->offsetPath('path51', 'seamline', $this->o('sa'), 1,['class' => 'fabric sa'] );
		
       // Grainline
        $p->newPoint('grainlineTop', $p->x(4), 0.05*$p->y(10)+ 0.95*$p->y(4));
        $p->newPoint('grainlineBottom',  $p->x(grainlineTop), 0.95*$p->y(10)+ 0.05*$p->y(4));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(4)-40,  0.6*$p->y(4) +0.4*$p->y(10) , 'Title anchor');
        $p->addTitle('titleAnchor', 3, $this->t($p->title), '1x ','small');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-5, 80));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
        
        // Notches
        $p->notch([4,10]);
    }
	
	   public function finalizeBrimTop($model)
    {
        /** @var Part $p */
        $p = $this->parts['brimTop'];
		
		// Seam allowances
		if($this->o('sa')) {		
		$p->offsetPath('path51', 'seamline10', $this->o('sa'), 1,['class' => 'fabric sa'] );
		}
		
		// Grainline
        $p->newPoint('grainlineTop', $p->x(4), 0.05*$p->y(10)+ 0.95*$p->y(4));
        $p->newPoint('grainlineBottom',  $p->x(grainlineTop), 0.95*$p->y(10)+ 0.05*$p->y(4));
        $p->newGrainline('grainlineTop', 'grainlineBottom', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', $p->x(4)-40,  0.6*$p->y(4) +0.4*$p->y(10) , 'Title anchor');
        $p->addTitle('titleAnchor', 4, $this->t($p->title), '1x ','small');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-5, 80));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
        
        // Notches
        $p->notch([4,10]);
    }
	
		   public function finalizeBrimPlastic($model)
    {
        /** @var Part $p */
        $p = $this->parts['brimPlastic'];

        // Title
        $p->newPoint('titleAnchor', $p->x(4)-40,  0.6*$p->y(4) +0.4*$p->y(10) , 'Title anchor');
        $p->addTitle('titleAnchor', 5, $this->t($p->title), '1x ','extrasmall-horizontal');

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor',-5, 80));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
        
        // Notches
        $p->notch(['seamline60-curve-4TO502','seamline65-curve-10TO511']);
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

        // Vertical left side
        $curvedOffset = 15;
        $xBase = $p->x(1) - $curvedOffset;
        if($this->o('sa')) {
            $xBase -= $this->o('sa');
            $curvedOffset += 10;
        }

        $p->newHeightDimension(6,1, $xBase - 10);

        // Vertical right side
        $xBase = $p->x(3);
        if($this->o('sa')) $xBase += $this->o('sa');
        $p->newHeightDimension(3,2, $xBase + 10);
        $p->newHeightDimension(9,2, $xBase + 25);
        $p->newHeightDimension(12,2, $xBase + 40);

        // Horizontal bottom
        $yBase = $p->y(12) + $curvedOffset;
        if($this->o('sa')) $yBase += $this->o('sa');
        $p->newWidthDimension(1,6, $yBase + 10);
        $p->newWidthDimension(1,9, $yBase + 25);
        $p->newWidthDimension(1,12, $yBase + 40);
        $p->newWidthDimension(1,3, $yBase + 55);

        // Horizontal top
        $yBase = $p->y(1);
        if($this->o('sa')) $yBase -= $this->o('sa');
        $p->newWidthDimension(1,2, $yBase - 10);

        // Side seam
        $p->newCurvedDimension('M 1 C 4 5 6 C 7 45 42 C 44 8 9 C 10 11 30 L 12', -1*$curvedOffset);

    }



    /**
     * Adds paperless info for the side
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSide($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['side'];

        // Side seam
        $curvedOffset = 15;
        if($this->o('sa')) $curvedOffset += 10;
        $p->newCurvedDimension('M 2 C 4 21 19 C 20 7 6', $curvedOffset);
        $p->newCurvedDimension('M 1 C 3 17 15 C 16 13 11 C 12 9 8 L 6', -1 * $curvedOffset);

        // Width at the top
        $yBase = $p->y(1);
        if($this->o('sa')) $yBase -= $this->o('sa');
        $p->newWidthDimension(1,2, $yBase - 10);
        $p->newWidthDimension(1,6, $yBase - 25);

        // Height at the right side
        $xBase = $p->x(8);
        if($this->o('sa')) $xBase += $this->o('sa');
        $p->newHeightDimension(6,2, $xBase + 15);
        $p->newHeightDimension(11,2, $xBase + 30);
    }

    /**
     * Adds paperless info for the brim top
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBrimTop($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['brimTop'];

        // Notes
        $p->newNote(1, 4, $this->t("Inner curve is the same as the brim bottom"), 12, 20, 3);
        $extra = $p->unit(3);
        $p->newNote(2, 10, $this->t("Outer curve lies $extra outside the brim bottom outer curve"), 6, 25, 3);
        // Extend the part so note is not cut off
        $p->addPoint('extender1', $p->shift(10,-90,30));
        $p->addPoint('extender2', $p->shift('extender1',0,30));
        $p->newPath('extender', 'M extender1 L extender2', ['class' => 'hidden']);
        
    }

    /**
     * Adds paperless info for the brim bottom
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBrimBottom($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['brimBottom'];
        
        // Width at the top
        $yBase = $p->y(1);
        if($this->o('sa')) $yBase -= $this->o('sa');
        $p->addPoint('leftEdge', $p->curveEdge(1,8,9,10,'left'));
        $p->addPoint('rightEdge', $p->curveEdge(7,11,12,10,'right'));

        // Width at the top
        $p->newWidthDimension(1,7, $yBase - 10);
        $p->newWidthDimension('leftEdge', 'rightEdge', $yBase - 25);

        // Heights
        $xBase = $p->x(7);
        if($this->o('sa')) $xBase -= $this->o('sa');
        $p->newHeightDimension(4,7, $xBase + 20);
        $p->newHeightDimension(10,7, $xBase + 35);
    }

    /**
     * Adds paperless info for the brim plastic
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessBrimPlastic($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['brimPlastic'];
        
        $p->paths['seamline1']->setRender(true);
        $p->paths['seamline2']->setRender(true);
        
        // Notes

        $extra = $p->unit(1.5);
        $p->newNote(1, 501, $this->t("The dashed line is the shape of the brim bottom"), 3, 50, 0);
        $p->newNote(2, 'seamline60-curve-4TO502', $this->t("The inner curve is inset by $extra"), 12, 40, 0);
        $p->newNote(3, 'seamline65-curve-10TO511', $this->t("Th outer curve is outset by $extra"), 6, 25, 0);
        // Extend the part so note is not cut off
        $p->addPoint('extender1', $p->shift(10,-90,30));
        $p->addPoint('extender2', $p->shift('extender1',0,30));
        $p->newPath('extender', 'M extender1 L extender2', ['class' => 'hidden']);
    }





    /** 
     * Calculates the difference between the side seam in the top part
     * and the side seam in the side part.
     *
     * Positive values mean the top part side seam is longer
     *
     * @return $delta the difference between the side seams in mm
     */
    protected function sideSeamDelta() 
    {
        $top = $this->parts['top'];
        $side = $this->parts['side'];

        $topLen = 
            $top->curveLen(1,4,5,6) +
            $top->curveLen(6,7,45,42) +
            $top->curveLen(42,44,8,9) +
            $top->curveLen(9,10,11,30) +
            $top->distance(30,12)
        ;

        $sideLen = 
            $side->curveLen(1,3,17,15) +
            $side->curveLen(15,16,13,11) +
            $side->curveLen(11,12,9,8) +
            $side->distance(8,6)
        ;

        return $topLen - $sideLen;
    }

    protected function headCircDelta($model) 
    {
        $this->setValue('headCircActual', $this->v('sideHeadCirc') + $this->v('topHeadCirc'));
        return $this->v('headCircActual') - ($model->m('headCircumference') + $this->o('headEase'));
    }

}
