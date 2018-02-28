<?php
/** Freesewing\Patterns\Beta\CarlitaCoat class */
namespace Freesewing\Patterns\Beta;

/**
 * A pattern template
 *
 * If you'd like to add you own pattern, you can copy this class/directory.
 * It's an empty skeleton for you to start working with
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class CarlitaCoat extends CarltonCoat
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
    */

    /** Armhole depth factor is always 77% */
    const ARMHOLE_DEPTH_FACTOR = 0.77;

    /** Minimum distance for the chest pocket to set below bustpoint is 3cm */
    const CHEST_POCKET_MIN_BELOW_BUST_APEX = 30;

    /**
     * Sets up options and values for our draft
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function initialize($model)
    {
        // Fix the armholeDepthFactor to 77%
        $this->setOptionIfUnset('armholeDepthFactor', self::ARMHOLE_DEPTH_FACTOR);
        
        // We want this coat with the high bust, so let's trick Carlton
        $this->setValue('bust', $model->m('chestCircumference'));
        $model->setMeasurement('chestCircumference', $model->m('highBust'));

        // Make princessSeamSmoothFactor a value between 2 and 8
        $this->setOption('princessSeamSmoothFactor', 2 + 6*$this->o('princessSeamSmoothFactor'));
              
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

        // Get to work
        $this->draftBackBlock($model);
        $this->draftFrontBlock($model);
        
        $this->draftSleeveBlock($model);
        $this->draftTopsleeveBlock($model);
        $this->draftUndersleeveBlock($model);
         
        $this->draftFront($model);
        $this->draftBack($model);
        $this->draftTail($model);

        $this->draftCollar($model);
        $this->draftCollarStand($model);

        $this->draftTopsleeve($model);
        $this->draftUndersleeve($model);
        $this->draftCuffFacing($model);
        $this->draftBelt($model);

        $this->draftPocket($model);
        $this->draftPocketFlap($model);
        $this->draftChestPocketWelt($model);
        $this->draftInnerPocketWelt($model);
        $this->draftInnerPocketBag($model);
        $this->draftInnerPocketTab($model);

        // Female stuff
        $this->draftFrontPs($model);
        $this->draftFrontPanel($model);
        $this->draftSidePanel($model);
        

        // Hide the blocks used in construction
        $this->parts['sleeveBlock']->setRender(false);
        $this->parts['topsleeveBlock']->setRender(false);
        $this->parts['undersleeveBlock']->setRender(false);
        $this->parts['frontBlock']->setRender(false);
        $this->parts['backBlock']->setRender(false);
//        $this->parts['frontPs']->setRender(false);
//        $this->parts['front']->setRender(false);
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
        
        $this->finalizeFront($model);
        $this->finalizeBack($model);
        $this->finalizeTail($model);
        
        $this->finalizeTopsleeve($model);
        $this->finalizeUndersleeve($model);
        
        $this->finalizeBelt($model);
        $this->finalizeCollarStand($model);
        $this->finalizeCollar($model);
        $this->finalizeCuffFacing($model);
        $this->finalizePocket($model);
        $this->finalizePocketFlap($model);
        $this->finalizeChestPocketWelt($model);
        $this->finalizeInnerPocketWelt($model);
        $this->finalizeInnerPocketBag($model);
        $this->finalizeInnerPocketTab($model);

        // Female stuff
        $this->finalizeFrontPanel($model);
        $this->finalizeSidePanel($model);

        // Is this a paperless pattern?
        if ($this->isPaperless) {
            $this->paperlessFrontPanel($model);
            $this->paperlessSidePanel($model);
            $this->paperlessBack($model);
            $this->paperlessTail($model);
            $this->paperlessTopsleeve($model);
            $this->paperlessUndersleeve($model);
            $this->paperlessBelt($model);
            $this->paperlessCollarStand($model);
            $this->paperlessCollar($model);
            $this->paperlessCuffFacing($model);
            $this->paperlessPocket($model);
            $this->paperlessPocketFlap($model);
            $this->paperlessChestPocketWelt($model);
            $this->paperlessInnerPocketWelt($model);
            $this->paperlessInnerPocketBag($model);
            $this->paperlessInnerPocketTab($model);
        }
    }

    /**
     * Drafts the front princess seam
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontPs($model)
    {
        $this->clonePoints('front', 'frontPs');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontPs'];

        // This is Carlton's front panel. Let's adjust it to fit (.Y.) shall we?

        // Step 1: Locate the bust point
        // we're adding half of the proportionate amount of chest east for the bust span
        // Only half because this is not where ease is needed/pools
        $p->newPoint('bustPoint', $model->m('bustSpan')/2 + $model->m('bustSpan')/$this->v('bust') * $this->o('chestEase') / 4, $p->y(8) + $model->m('highPointShoulderToBust'));

        // Step 2: Draw in the princess seam 
        $p->newPoint('psFrontBottom', $p->x('bustPoint'), $p->y('hemSide'));
        $p->addPoint('psFrontCpRight', $p->shiftTowards(10, 'bustPoint', $p->deltaX('bustPoint', 10)/2));
        $p->addPoint('psFrontCpRight', $p->rotate('psFrontCpRight', 10, -15));
        $p->addPoint('psFrontCpTop', $p->shift('bustPoint', 90, $p->deltaY(10,'bustPoint')/2));
        $p->addPoint('.helper', $p->shift('bustPoint', 90, 10));
        $p->addPoint('psFrontCpTop', $p->beamsCross(10,'psFrontCpRight','bustPoint', '.helper'));
        $p->newPath('princessSeam', 'M 10 C psFrontCpRight psFrontCpTop bustPoint L psFrontBottom', ['class' => 'debug']);
        $p->newPath('cutLine', 'M 5 L bustPoint L 10', ['class' => 'help']);
        $p->newPath('shape', 'M hemSide L seatSide C seatSideCpTop waistSideCpBottom waistSide C waistSideCpTop chestSideCp 5 C 13 16 14 C 15 18 10 C psFrontCpRight psFrontCpTop bustPoint L psFrontBottom z', ['class' => 'help']);


        // Step 3: Copy points
        // Before rotating, let's clone all these points so we don't loose the originals
        $clone = [
            'bustPoint', 
            'psFrontBottom', 
            'psFrontCpRight', 
            'psFrontCpTop',
            5,13,16,14,15,18,10,
            'chestSideCp',
            'waistSideCpTop',
            'waistSide',
            'waistSideCpBottom',
            'seatSideCpTop',
            'seatSide',
            'hemSide',

        ];
        // Clones hemSide into sideHemSide and so on
        foreach ($clone as $pid) $p->clonePoint($pid,'side'.ucfirst($pid));

        // Step 4: Shaping
        // How much (horizontal) room do we need to create?
        $extra = ($this->v('bust') - $model->m('highBust')) / 2;

        // Cut from point 10 to bustpoint and rotate until we have created enough room
        $added = $p->deltaX('bustPoint','sideBustPoint');
        $delta = $extra - $added;
        $count = 1;
        $this->dbg("Created $added room for bust. Target is $extra, count is $count. To go: $delta"); 
        while(abs($delta) > 0.5 && $count < 50) {
            foreach($clone as $pid) {
                if($pid != 10) {
                    $p->addPoint('side'.ucfirst($pid), $p->rotate('side'.ucfirst($pid), 10, $delta/5));
                }
            }
            $added = $p->deltaX('bustPoint','sideBustPoint');
            $delta = $extra - $added;
            $count++;
            $this->dbg("Created $added room for bust. Target is $extra, count is $count. To go: $delta"); 
        }
        // Now cut from point 5 to rotate bustpoint and rotate again
        $angle = $p->angle('sideBustPoint','sidePsFrontBottom');
        $this->dbg("Angle is $angle");
        $p->clonePoint('side5','bottom5'); // we need to duplicate our rotated point 5
        foreach($clone as $pid) {
            if($pid == 5 || !is_numeric($pid)) $p->addPoint('bottom'.ucfirst($pid), $p->rotate('side'.ucfirst($pid), 'sideBustPoint', -1*$angle-90));
        }
        // Cut from bustPoint to sideBustpoint and rotate again
        $final = [
            10,
            'psFrontCpRight',
            'psFrontCpTop',
            'bustPoint',
            'side18',
            'side15',
            'side14',
            'side16',
            'side13',
            'side5',
            'bottomChestSideCp',
        ];
        $angle = $p->angle('bottomBustPoint', 'side5');
        foreach($final as $pid) {
            $p->addPoint('final'.ucfirst($pid), $p->rotate($pid, 'bottomBustPoint', -1*$angle));
        }

        $p->newPath('triangle', 'M side5 L sideBustPoint L side10 C side18 side15 side14 C side16 side13 side5', ['class' => 'debug']);
        //$p->newPath('rotation1', 'M sideHemSide L sideSeatSide C sideSeatSideCpTop sideWaistSideCpBottom sideWaistSide C sideWaistSideCpTop sideChestSideCp side5 L sideBustPoint L sidePsFrontBottom z', ['class' => 'help']);
        $p->newPath('rotation2', 'M bottomHemSide L bottomSeatSide C bottomSeatSideCpTop bottomWaistSideCpBottom bottomWaistSide C bottomWaistSideCpTop bottomChestSideCp bottom5 L bottomBustPoint L bottomPsFrontBottom z', ['class' => 'debug']);
        $p->newPath('rotation3', 'M finalSide5 C finalSide13 finalSide16 finalSide14 C finalSide15 finalSide18 final10 C finalPsFrontCpRight finalPsFrontCpTop finalBustPoint', ['class' => 'fabric']);

        // Step 5: Draw new curve
        $p->addPoint('finalBustPointCpBottom', $p->rotate('finalPsFrontCpTop', 'finalBustPoint', 180));
        // Make the vertical distance we take to merge back to the original PS line a factor of breast size
        $vspace = $this->o('princessSeamSmoothFactor') * abs($p->deltaX('finalBustPointCpBottom','bottomBustPoint'));
        $p->addPoint('psWaist', $p->shift('bottomBustPoint', -90, $vspace));
        $p->newPoint('psWaistCpTop', $p->x('psWaist'), $p->y('finalBustPointCpBottom'));
        $p->newPath('final', '
            M finalSide5 
            C finalSide13 finalSide16 finalSide14 
            C finalSide15 finalSide18 final10 
            C finalPsFrontCpRight finalPsFrontCpTop finalBustPoint
            C finalBustPointCpBottom psWaistCpTop psWaist
            L bottomPsFrontBottom
            L bottomHemSide
            L bottomSeatSide
            C bottomSeatSideCpTop bottomWaistSideCpBottom bottomWaistSide 
            C bottomWaistSideCpTop bottomChestSideCp finalSide5
            z
            ', ['class' => 'fabric']);

        // Step 6: Adapt length of the front part
        $p->newPath('frontPanel', 'M collarTip
            C collarTip collarBendPointCpTop collarBendPoint
            L hemFrontEdge 
            L psFrontBottom
            L bustPoint
            C psFrontCpTop psFrontCpRight 10
            C 17 19 12
            L 8
            C 20 21 9
            L collarTip
            z
            ', ['class' => 'hint']);

        $psSideLen = $p->curveLen('final10','finalPsFrontCpRight','finalPsFrontCpTop','finalBustPoint') + 
            $p->curveLen('finalBustPoint','finalBustPointCpBottom','psWaistCpTop','psWaist') +
            $p->distance('psWaist','bottomPsFrontBottom');
        $psFrontLen = $p->curveLen(10, 'psFrontCpRight','psFrontCpTop','bustPoint') +
            $p->distance('bustPoint','psFrontBottom');
        $delta = $psSideLen - $psFrontLen;
        $this->dbg("Length delta is $delta");
        
        $shiftThese = [
            'hemFrontEdge',
            'hemMiddle',
            'psFrontBottom',
            3
        ];
        foreach($shiftThese as $pid) $p->addPoint($pid, $p->shift($pid, -90, $delta));

        // Step 7: Shift buttons/pockets on the front part only
        $this->buttons = [
            'button1Left',
            'button2Left',
            'button3Left',
            'button1Right',
            'button2Right',
            'button3Right',
        ];
        $this->pocketFlap = [
            'pocketFlapTopLeft',
            'pocketFlapTopRight',
            'pocketFlapBottomLeftTop',
            'pocketFlapBottomRightTop',
            'pocketFlapBottomLeftTopCp',
            'pocketFlapBottomRightTopCp',
            'pocketFlapBottomLeftRightCp',
            'pocketFlapBottomRightLeftCp',
            'pocketFlapBottomLeftRight',
            'pocketFlapBottomRightLeft',
        ];
        $this->pocket = [
            'pocketTopLeft',
            'pocketTopRight',
            'pocketBottomLeftTop',
            'pocketBottomRightTop',
            'pocketBottomLeftTopCp',
            'pocketBottomRightTopCp',
            'pocketBottomLeftRightCp',
            'pocketBottomRightLeftCp',
            'pocketBottomLeftRight',
            'pocketBottomRightLeft',
        ];
        $this->mapPocket = [
            'chestPocketTopLeft',
            'chestPocketTopRight',
            'chestPocketBottomLeft',
            'chestPocketBottomRight',
        ];
        foreach(array_merge($this->buttons, $this->pocketFlap, $this->pocket, $this->mapPocket) as $pid) {
            // Merge only the ones left from the princess seam
            if($p->x($pid) < $p->x('bustPoint')) $p->addPoint($pid, $p->shift($pid, -90, $delta));
        }

        // Move the map/chest pocket into the princess seam
        $belowApex = $p->deltaY('bustPoint', 'chestPocketTopLeft');
        if($belowApex < self::CHEST_POCKET_MIN_BELOW_BUST_APEX) $belowApex = self::CHEST_POCKET_MIN_BELOW_BUST_APEX;
        $pocketWidth = $p->distance('chestPocketTopLeft','chestPocketTopRight');
        $pocketHeight = $p->distance('chestPocketTopLeft','chestPocketBottomLeft');
        $p->addPoint('chestPocketTopLeft', $p->shift('bustPoint', -90, $belowApex));
        $p->addPoint('chestPocketBottomLeft', $p->shift('chestPocketTopLeft', -90, $pocketHeight));
        $p->addPoint('chestPocketTopRight', $p->shift('chestPocketTopLeft', 0, $pocketWidth));
        $p->addPoint('chestPocketBottomRight', $p->shift('chestPocketBottomLeft', 0, $pocketWidth));

        // Shift front pocket sideways to keep its width
        $delta = $p->deltaX('bustPoint', 'bottomBustPoint');
        foreach(array_merge($this->pocket, $this->pocketFlap) as $pid) {
            // Shift only points right of bust point
            if($p->x($pid) > $p->x('bustPoint')) {
                $p->addPoint($pid,$p->shift($pid, 0, $delta));
            } 
        }
    }

    /**
     * Drafts the front panel
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftFrontPanel($model)
    {
        $this->clonePoints('frontPs', 'frontPanel');
        
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontPanel'];

        // Inner pocket path
        $p->newPath('innerPocket', 'M innerPocketTopLeft L innerPocketTopRight
            L innerPocketBottomRight L innerPocketBottomLeft z
            M innerPocketLeft L innerPocketRight'
        , ['class' => 'help']);
        
        $p->newPath('outline', 'M collarTip
            C collarTip collarBendPointCpTop collarBendPoint
            L hemFrontEdge 
            L psFrontBottom
            L bustPoint
            C psFrontCpTop psFrontCpRight 10
            C 17 19 12
            L 8
            C 20 21 9
            L collarTip
            z
            ', ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->clonePoint('hemFrontEdge','gridAnchor');
    }

    /**
     * Drafts the side panel
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSidePanel($model)
    {
        $this->clonePoints('frontPs', 'sidePanel');

        /** @var \Freesewing\Part $p */
        $p = $this->parts['sidePanel'];

        // Paths 
        $p->newPath('outline', '
            M finalSide5 
            C finalSide13 finalSide16 finalSide14 
            C finalSide15 finalSide18 final10 
            C finalPsFrontCpRight finalPsFrontCpTop finalBustPoint
            C finalBustPointCpBottom psWaistCpTop psWaist
            L bottomPsFrontBottom
            L bottomHemSide
            L bottomSeatSide
            C bottomSeatSideCpTop bottomWaistSideCpBottom bottomWaistSide 
            C bottomWaistSideCpTop finalBottomChestSideCp finalSide5
            z
            ', ['class' => 'fabric']);
        
        // Mark path for sample service
        $p->paths['outline']->setSample(true);
        $p->clonePoint('bottomPsFrontBottom','gridAnchor');
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
     * Finalizes the front panel
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeFrontPanel($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontPanel'];

        // Fusible interfacing at sleeve
        $fw = $this->getValue("fusibleWidth");
        $p->offsetPathString('fuse1', 'M 10 C 17 19 12', $fw, 1, ['class' => 'help']);
        $p->newTextOnPath('fuse1', 'M 10 C 17 19 12', $this->t('Apply fusible interfacing here'), ['dy' => $fw/-2, 'class' => 'center'], 0);
        $p->addPoint('fuseExtended',$p->rotate('fuse1-cp1--10.17.19.12', 'fuse1-curve-10TO12', 180));
        $p->curveCrossesLine('side10', 'psFrontCpRight', 'psFrontCpTop', 'bustPoint', 'fuse1-curve-10TO12', 'fuseExtended', '.fuseExt');
        $p->clonePoint('.fuseExt1', 'fuseEnd');
        $p->newPath('fuse2', 'M fuse1-curve-10TO12 L fuseEnd', ['class' => 'help']);

        // Title
        $p->newPoint('titleAnchor', $p->x(9), $p->y('bustPoint'));
        $p->addTitle('titleAnchor', '1a', $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('facing from fabric').
            "\n2x ".
            $this->t('non-facing from lining')
        );

        // Logo
        $p->newSnippet('logo','logo', 3);

        // Grainline
        $p->newPoint('grainlineTop', $p->x('collarTip')+30, $p->y('collarTip')+20);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y('hemFrontEdge')-20);
        $p->newGrainline('grainlineBottom', 'grainlineTop', $this->t('Grainline'));

        // Center front helpline
        $p->newPath('cf', 'M hemMiddle L 9', ['class' => 'help']);
        $p->newTextOnPath('cf1', 'M hemMiddle L 4', $this->t('Center front'), ['dy' => -2], false);
        $p->newTextOnPath('cf2', 'M 4 L 3', $this->t('Center front'), ['dy' => -2], false);
        $p->newTextOnPath('cf3', 'M 3 L 2', $this->t('Center front'), ['dy' => -2], false);
        $p->newTextOnPath('cf4', 'M 2 L 9', $this->t('Center front'), ['dy' => -2], false);

        // Buttons
        foreach($this->buttons as $pid) {
            if($p->x($pid) < $p->x('bustPoint')) $p->newSnippet($pid,'button-lg', $pid);
        }

        // Waist and seat line
        $p->newPoint('waistPsRight', $p->x('bustPoint'), $p->y(3));
        $p->newPoint('waistPsLeft', $p->x('collarBendPoint'), $p->y(3));
        $p->newPath('waistLine', 'M waistPsLeft L waistPsRight', ['class' => 'help']);
        $p->notch(['waistPsRight']);

        // Map pocket
        $p->newPath('mapPocket', 'M chestPocketTopLeft L chestPocketTopRight L chestPocketBottomRight L chestPocketBottomLeft', ['class' => 'hint']);

        // Facing/Lining boundary
        if($p->x('bustPoint') > $p->x(8)) $p->addPoint('liningTop', $p->beamsCross(8,12,'bustPoint','psFrontBottom'));
        else $p->addPoint('liningTop', $p->curveCrossesX(8,20,21,9,$p->x('bustPoint')));
        $p->newPath('facing', 'M bustPoint L liningTop', ['class' => 'fabric']);
        $p->newPath('lining', 'M bustPoint L liningTop', ['class' => 'lining lashed']);
        $p->newTextOnPath(1, 'M bustPoint L liningTop', $this->t('Facing/Lining boundary - Facing side'), ['dy' => -2, 'class' => 'fill-fabric'], false);
        $p->newTextOnPath(2, 'M liningTop L bustPoint', $this->t('Facing/Lining boundary - Lining side'), ['dy' => -2, 'class' => 'fill-lining'], false);
        $p->newPoint('facingNoteAnchor', $p->x('liningTop'), $p->y(9));
        $p->newNote('flb', 'facingNoteAnchor', $this->t('Add seam allowance at the facing/lining border'), 4, 20 );

        // Notches
        $p->notch(['bustPoint', 'chestPocketTopLeft', 'chestPocketBottomLeft']);

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline',$this->o('sa')*-1,1,['class' => 'sa fabric']);
            // Extra hem SA
            $p->addPoint('sa-line-hemFrontEdgeTOpsFrontBottom', $p->shift('sa-line-hemFrontEdgeTOpsFrontBottom',-90,$this->o('sa')*4));
            $p->addPoint('sa-line-psFrontBottomTOhemFrontEdge', $p->shift('sa-line-psFrontBottomTOhemFrontEdge',-90,$this->o('sa')*4));
            // Notes
            $p->addPoint('noteAnchor1', $p->shift('psFrontBottom', 90, 30));
            $p->addPoint('noteAnchor2', $p->shift('psFrontBottom', 180, 120));
            $p->newNote('sa1', 'noteAnchor1', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 10, 40, $this->o('sa')*-0.5);
            $p->newNote('sa2', 'noteAnchor2', $this->t('Hem allowance')."\n(".$p->unit($this->o('sa')*5).')', 12, 30, $this->o('sa')*-2.5);
            // Straighten hem
            $p->newPoint('sa-line-hemFrontEdgeTOpsFrontBottom', $p->x('sa-line-hemFrontEdgeTOcollarBendPoint'), $p->y('sa-line-hemFrontEdgeTOpsFrontBottom'));
            $p->newPoint('sa-line-psFrontBottomTOhemFrontEdge', $p->x('sa-line-psFrontBottomTObustPoint'), $p->y('sa-line-psFrontBottomTOhemFrontEdge'));
        }
    }

    /**
     * Finalizes the side panel
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSidePanel($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sidePanel'];

        // Fusible interfacing at sleeve
        $fw = $this->getValue("fusibleWidth");
        $fstring = 'M final10 C finalSide18 finalSide15 finalSide14 C finalSide16 finalSide13 finalSide5';
        $p->offsetPathString('fuse1', $fstring, $fw*-1, 1, ['class' => 'help']);
        $p->newTextOnPath('fuse1', $fstring, $this->t('Apply fusible interfacing here'), ['dy' => $fw/2, 'class' => 'center'], 0);
        //$p->addPoint('fuseExtended',$p->rotate('fuse1-cp1--10.17.19.12', 'fuse1-curve-10TO12', 180));
        //$p->curveCrossesLine('side10', 'psFrontCpRight', 'psFrontCpTop', 'bustPoint', 'fuse1-curve-10TO12', 'fuseExtended', '.fuseExt');
        //$p->clonePoint('.fuseExt1', 'fuseEnd');
        //$p->newPath('fuse2', 'M fuse1-curve-10TO12 L fuseEnd', ['class' => 'help']);

        // Title
        $p->addPoint('titleAnchor', $p->shiftFractionTowards('bottomSeatSide', 'bottomPsFrontBottom', 0.5));
        $p->addTitle('titleAnchor', '1b', $this->t($p->title), 
            '2x '.
            $this->t('from fabric').
            "\n2x ".
            $this->t('from lining')
        );

        // Logo
        $p->addPoint('logoAnchor', $p->shift('titleAnchor', 90, 70));
        $p->newSnippet('logo','logo', 'logoAnchor');

        // Scalebox
        $p->addPoint('scaleboxAnchor', $p->shift('logoAnchor', 90, 100));
        $p->newSnippet('scalebox','scalebox','scaleboxAnchor');

        // Pocket
        $p->newPoint('pocketTopEndsHere', $p->x('psWaist'), $p->y('pocketTopRight'));
        $p->newPoint('pocketBottomEndsHere', $p->x('psWaist'), $p->y('pocketBottomRight'));
        $p->newPath('pocket', '
            M pocketBottomEndsHere
            L pocketBottomRightLeft
            C pocketBottomRightLeftCp pocketBottomRightTopCp pocketBottomRightTop
            L pocketTopRight
            L pocketTopEndsHere
            ', ['class' => 'hint']);
        
        // Pocket flap
        $p->newPoint('pocketFlapTopEndsHere', $p->x('psWaist'), $p->y('pocketFlapTopRight'));
        $p->newPoint('pocketFlapBottomEndsHere', $p->x('psWaist'), $p->y('pocketFlapBottomRight'));
        $p->newPath('pocketFlap', '
            M pocketFlapBottomEndsHere
            L pocketFlapBottomRightLeft 
            C pocketFlapBottomRightLeftCp pocketFlapBottomRightTopCp pocketFlapBottomRightTop 
            L pocketFlapTopRight 
            L pocketFlapTopEndsHere 
            ', ['class' => 'hint']);

        // Notches
        $p->notch(['finalBustPoint']);

        // Seam allowance
        if($this->o('sa')) {
            $p->offsetPath('sa','outline',$this->o('sa')*-1,1,['class' => 'sa fabric']);
            // Extra hem SA
            $p->addPoint('sa-line-bottomPsFrontBottomTObottomHemSide', $p->shift('sa-line-bottomPsFrontBottomTObottomHemSide',-90,$this->o('sa')*4));
            $p->addPoint('sa-line-bottomHemSideTObottomPsFrontBottom', $p->shift('sa-line-bottomHemSideTObottomPsFrontBottom',-90,$this->o('sa')*4));
            // Notes
            $p->addPoint('noteAnchor1', $p->shift('bottomHemSide', 90, 30));
            $p->addPoint('noteAnchor2', $p->shift('bottomHemSide', 180, 120));
            $p->newNote('sa1', 'noteAnchor1', $this->t('Standard seam allowance')."\n(".$p->unit($this->o('sa')).')', 10, 40, $this->o('sa')*-0.5);
            $p->newNote('sa2', 'noteAnchor2', $this->t('Hem allowance')."\n(".$p->unit($this->o('sa')*5).')', 12, 30, $this->o('sa')*-2.5);
            // Straighten hem
            $p->newPoint('sa-line-bottomHemSideTObottomPsFrontBottom', $p->x('sa-line-bottomHemSideTObottomSeatSide'), $p->y('sa-line-bottomHemSideTObottomPsFrontBottom'));
            $p->newPoint('sa-line-bottomPsFrontBottomTObottomHemSide', $p->x('sa-line-bottomPsFrontBottomTOpsWaist'), $p->y('sa-line-bottomPsFrontBottomTObottomHemSide'));
        }
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
     * Adds paperless info for the front panel
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessFrontPanel($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['frontPanel'];
        
        // Heigh left side
        $xBase = $p->x('hemFrontEdge') - 15;
        if($this->o('sa')) {
            $xBase -= $this->o('sa');
            $sa = $this->o('sa');
        } else $sa = 0;
        $p->newHeightDimension('collarBendPoint', 'collarTip', $xBase);
        $p->newHeightDimension('button1Left', 'button2Left', $xBase-15);
        $p->newHeightDimension('button2Left', 'button3Left', $xBase-15);
        $p->newHeightDimension('button3Left', 'collarTip', $xBase-15);
        $p->newHeightDimension('hemFrontEdge', 'collarTip', $xBase-45);
        $p->newHeightDimension('hemFrontEdge', 8, $xBase-60);
        $p->newHeightDimension('hemFrontEdge', 3, $xBase-30);
        $p->newHeightDimension(3, 'collarTip', $xBase-30);
        $p->newHeightDimension(3, 'button3Right', $p->x('button3Right')+20);

        // Heigh right side
        $xBase = $p->x(12);
        if($this->o('sa')) $xBase += $this->o('sa');
        $p->newHeightDimension('waistPsRight', 'chestPocketTopRight', $xBase-30);
        $p->newHeightDimension('waistPsRight', 'bustPoint', $xBase-15);
        $p->newHeightDimension('waistPsRight', 10, $xBase);
        $p->newHeightDimension('waistPsRight', 12, $xBase+15);
        $p->newHeightDimension('waistPsRight', 8, $xBase+30);

        // Width top
        $p->newWidthDimensionSm('collarBendPoint','collarTip', $p->y('collarTip')-15-$sa);
        $p->newWidthDimension('collarBendPoint',9, $p->y('collarTip')-30-$sa);
        $p->newWidthDimension('collarBendPoint',8, $p->y(8)-15-$sa);
        $p->newWidthDimension('collarBendPoint','liningTop', $p->y(8)-30-$sa);
        $p->newWidthDimension('collarBendPoint',10, $p->y(8)-45-$sa);
        $p->newWidthDimension('collarBendPoint',12, $p->y(8)-60-$sa);
        
        // Width bottom
        $p->newWidthDimension('hemFrontEdge','hemMiddle', $p->y('hemFrontEdge')+15+$sa*5);
        $p->newWidthDimension('hemFrontEdge','psFrontBottom', $p->y('hemFrontEdge')+30+$sa*5);

        // Chest pocket
        $p->newLinearDimension('chestPocketTopLeft','chestPocketTopRight', -15);
        $p->newLinearDimension('chestPocketBottomRight','chestPocketTopRight', 15);
        $p->newNote(1, 'chestPocketBottomRight', $this->t('Integrate pocket in seam'), 5, 20, -5);
        $p->newWidthDimension(3, 'chestPocketTopLeft', $p->y('chestPocketTopLeft')+15);
        
        // Inner pocket
        $p->newWidthDimension(3, 'innerPocketBottomLeft', $p->y('innerPocketBottomLeft')+15);
        $p->newWidthDimension('innerPocketBottomLeft', 'innerPocketBottomRight', $p->y('innerPocketBottomLeft')-20);
        $p->newHeightDimension('waistPsRight', 'innerPocketRight', $p->x('innerPocketLeft')-10);
    }
    
    /**
     * Adds paperless info for the side panel
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSidePanel($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['sidePanel'];

        // Width at the bottom
        $p->newWidthDimension('bottomPsFrontBottom','bottomHemSide', $p->y('bottomHemSide')+15+$this->o('sa'));

        // Width middle
        $p->newWidthDimension('psWaist','bottomWaistSide', $p->y('bottomWaistSide'));
        $p->newWidthDimensionSm('finalBustPoint', 'psWaist', $p->y('bottomWaistSide')-15);
        $p->newWidthDimension('psWaist', 'finalSide5', $p->y('bottomWaistSide')-15);

        // Width top
        $p->newWidthDimension('finalBustPoint','final10', $p->y('final10')-15-$this->o('sa'));
        $p->newWidthDimension('finalBustPoint','finalSide5', $p->y('final10')-30-$this->o('sa'));

        // Height right side
        $p->newHeightDimension('bottomWaistSide','finalSide5', $p->x('finalSide5')+15+$this->o('sa'));
        $p->newHeightDimension('bottomWaistSide','final10', $p->x('finalSide5')+30+$this->o('sa'));
        $p->newHeightDimension('bottomHemSide','bottomWaistSide', $p->x('bottomHemSide')+15+$this->o('sa'));

        // Pocket
        $p->newHeightDimension('pocketFlapTopRight','bottomWaistSide', $p->x('pocketTopRight')+15);
        $p->newHeightDimension('pocketTopRight','bottomWaistSide', $p->x('pocketTopRight')+30);
        $p->newWidthDimension('pocketBottomEndsHere','pocketBottomRightTop', $p->y('pocketBottomRight')+15);


    }
}
