<?php
/** Freesewing\Patterns\Beta\SandySkirt class */
namespace Freesewing\Patterns\Beta;

/**
 * The Sandy Skirt pattern
 *
 * @author Erica Alcusa Sáez
 * @copyright 2018 Erica Alcusa Sáez
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SandySkirt extends \Freesewing\Patterns\Core\Pattern
{
    /*
        ___       _ _   _       _ _
       |_ _|_ __ (_) |_(_) __ _| (_)___  ___
        | || '_ \| | __| |/ _` | | / __|/ _ \
        | || | | | | |_| | (_| | | \__ \  __/
       |___|_| |_|_|\__|_|\__,_|_|_|___/\___|

      Things we need to do before we can draft a pattern
     */

    // Minimum overlap between ends of the waistband to draw a button and a buttonhole = 1.5cm 
    const MIN_OVERLAP_BUTTON = 15;
    
    // Offset for the measures in the paperless theme = 1cm
    const PAPERLESS_OFFSET = 10;


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
        // Prevent missing sa from causing warnings
        $this->setOptionIfUnset('sa', 0);

        // Circumference of the top of the waistband, calculated from the waistbandPosition option
        $this->setValue('topCircumference', $this->o('waistbandPosition') * $model->m('hipsCircumference') +  (1-$this->o('waistbandPosition')) * $model->m('naturalWaist'));
        
        // Circumference of the bottom of the waistband
        if ($this->o('waistbandShape') > 0){
            // If the waistband is curved, the bottom circumference is calculated from the measurements
            $this->setValue('bottomCircumference', $this->v('topCircumference') + $this->o('waistbandWidth')*($model->m('hipsCircumference')-$model->m('naturalWaist'))/$model->m('naturalWaistToHip'));
        } else {
            // If the waistband is straight, the bottom circumference is the same as the top circumference
            $this->setValue('bottomCircumference', $this->v('topCircumference'));
        }

        // The top circumference of the skirt corresponds to the bottom circumference of the waistband, plus the extraWaist option for pleating
        $this->setValue('skirtCircumference', $this->v('bottomCircumference') * (1+ $this->o('extraWaist')));
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
        $this->sample($model);

        $this->finalizeSkirt($model);

        // Depending of waistbandShape, finalize the straight or the curved waistband
        if ($this->o('waistbandShape') > 0) $this->finalizeCurvedWaistband($model);
        else $this->finalizeStraightWaistband($model);

        // Add all the paperless info
        if ($this->isPaperless) {
            $this->paperlessSkirt($model);
            if ($this->o('waistbandShape') > 0) $this->paperlessCurvedWaistband($model);
            else $this->paperlessStraightWaistband($model);
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
        $this->initialize($model);

        // Drafts the skirt
        $this->draftSkirt($model);
    
        // Drafts the curved or straight waistband depending on the waistbandShape option
        if ($this->o('waistbandShape') > 0) $this->draftcurvedWaistband($model);
        else $this->draftStaightWaistband($model);
    }

    /**
     * This function is an extension of bezierCircle in bezierToolbox.
     * It admits an angle to draw arcs of any given angle and not
     * only quarter circles. Since the default angle is 90º, it
     * could be substituted for the original function, and it would
     * behave the same way if no angle is given.
     * The bigger the angle, the less acurate the approximation
     * to a circle. Not recommended for angles larger than 90º.
     *
     * @param float $radius The radius of the circle to aim for
     * @param float $angle The angle of the arc to aim for
     *
     * @return float The distance to the control point
     */
    protected function bezierCircleExtended($radius, $angle=90)
    {
        $argument = deg2rad($angle/2);

        return $radius * 4 * (1 - cos($argument)) / sin($argument) / 3;
    }

    /**
     * Drafts a sector of a given angle of a ring with a given
     * internal and external radius
     *
     * @param float $rot The angle to which the whole path is rotated
     * @param float $an The angle of the Ring sector
     * @param float $radIn The radius of the internal circumference
     * @param float $radEx The radius of the external circumference
     * @part string The name of the part in which the points are created
     *
     * @return string The path of the Ring sector
     */
    protected function draftRingSector($rot, $an, $radIn, $radEx, $part)
    {
        $p = $this->parts[$part];

        /**
        * Calculates the distance of the control point for the internal
        * and external arcs using bezierCircleExtended
        */
        $distIn=$this->bezierCircleExtended($radIn, $an/2);
        $distEx=$this->bezierCircleExtended($radEx, $an/2);

        // The centre of the circles
        $p->newPoint(0, 0, 0);
        
        /**
        * This function is expected to draft ring sectors for
        * angles up to 180%. Since bezierCircleExtended works
        * best for angles until 90º, we generate the ring
        * sector using the half angle and then duplicate it
        */
        
        /**
        * The first point of the internal arc, situated at
        * a $radIn distance below the centre
        */
        $p->addPoint('in1', $p->shift(0, -90, $radIn));
        
        /**
        * The control point for 'in1'. It's situated at a
        * distance $distIn calculated with bezierCircleExtended
        * and the line between it and 'in1' is perpendicular to
        * the line between 'in1' and the centre, so it's
        * shifted in the direction 0º
        */
        $p->addPoint('in1C', $p->shift('in1', 0, $distIn));
        
        /**
        * The second point of the internal arc, situated at
        * a $radIn distance of the centre in the direction
        * $an/2 - 90º
        */
        $p->addPoint('in2', $p->shift(0, $an/2 - 90, $radIn));

        /**
        * The control point for 'in2'. It's situated at a
        * distance $distIn calculated with bezierCircleExtended
        * and the line between it and 'in2' is perpendicular to
        * the line between 'in2' and the centre, so it's
        * shifted in the direction $an/2 + 180º
        */
        $p->addPoint('in2C', $p->shift('in2', $an/2 + 180, $distIn));

        /**
        * The points for the external arc are generated in the
        * same way, using $radEx and $distEx instead
        */
        $p->addPoint('ex1', $p->shift(0, -90, $radEx));
        $p->addPoint('ex1C', $p->shift('ex1', 0, $distEx));
        $p->addPoint('ex2', $p->shift(0, $an/2 - 90, $radEx));
        $p->addPoint('ex2C', $p->shift('ex2', $an/2 + 180, $distEx));

        // Flip all the points to generate the full ring sector
        $flipThese = ['in2', 'in2C', 'in1C', 'ex1C', 'ex2C', 'ex2'];
        foreach($flipThese as $id){
            $p->addPoint($p->newId('flip'), $p->flipX($id, 0));
        }

        // Rotate all the points an angle $rot
        $rotateThese = ['in1', 'in1C', 'in2', 'in2C', 'ex1', 'ex1C', 'ex2', 'ex2C', 'flip1', 'flip2', 'flip3', 'flip4', 'flip5', 'flip6'];
        foreach($rotateThese as $id){
            $p->addPoint($id, $p->rotate($id, 0, $rot));
        }

        // Return the path of the full ring sector
        return 'M flip1 C flip2 flip3 in1 C in1C in2C in2 L ex2 C ex2C ex1C ex1 C flip4 flip5 flip6 z';
    }

    /**
     * Drafts the skirt
     *
     * The skirt is just a ring sector with external and intenal
     * radius and angle calculated from measurements and options
     * 
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftSkirt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['skirt'];
    
        /**
        * Calculates the radius of the waist arc and the angle
        * of the ring sector
        */
        if ($this->o('seamlessFullCircle') > 0){
            /**
            * If the seamless full circle option is selected, the angle
            * is 90º, and the radius of the waist arc is half than if
            * it's not selected, because in this case the fabric is cut
            * in a double fold
            */
            $an = 90;
            $radiusWaist = $this->v('skirtCircumference')/deg2rad($an)/4;
        } else {
            /**
            * If the seamless full circle option is not selected, the
            * angle is calculated using the circlePercent option
            */
            $an = $this->o('circlePercent')*180;
            $radiusWaist = $this->v('skirtCircumference')/deg2rad($an)/2;
            
            /**
            * If the angle is too large, the seam allowance can fall out
            * of the fold of the fabric, so we limit the angle to a
            * maximum angle calculated so the seam allowance fits in the
            * fabric
            */
            if($an > 90 && $this->o('sa') > 0){
                $maxAn = rad2deg(atan($radiusWaist / $this->o('sa')));
                if($an > 90 + $maxAn) $an = 90 + $maxAn;
            }
        }
        
        /**
        * The radius of the hem arc is the radius of the waist
        * arc with the length of the skirt added
        */
        $radiusHem = $radiusWaist+$this->o('length');

        /**
        * The ring sector will be rotated an angle $an/2 so we
        * display the part with one edge of the skirt vertical
        */
        $rot=$an/2;

        // Call draftRingSector to draft the part
        $path = $this->draftRingSector($rot, $an, $radiusWaist, $radiusHem, 'skirt');

        // Anchor samples to the centre of the waist
        $p->addPoint('samplerAnchor', $p->shift('flip1', 0, 0));

        // Draft the seamline
        $p->newPath('seamline', $path, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);
    }

    /**
     * Drafts the curved waistband
     * 
     * The curved waistband is just a ring sector with external
     * and intenal radius and angle calculated from measurements
     * and options
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftCurvedWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['curvedWaistband'];

        // Calculate the angle of the ring sector and the radius of the upper arc
        $an = rad2deg($this->v('bottomCircumference')-$this->v('topCircumference'))/$this->o('waistbandWidth');
        $rad = $this->v('topCircumference') / deg2rad($an);

        // Extra angle to extend the waistband to overlap according to waistbandOverlap
        $anExtra = rad2deg($this->o('waistbandOverlap')/($rad+$this->o('waistbandWidth')));

        // The curved waistband is shown with no rotation
        $rot = 0;

        // Call draftRingSector to draft the part
        $path = $this->draftRingSector($rot, $an+$anExtra, $rad, $rad+$this->o('waistbandWidth'), 'curvedWaistband');

        // Anchor samples to the centre of the waist
        $p->addPoint('samplerAnchor', $p->shift('in1', 0, 0));
        
        // Draft the seamline
        $p->newPath('seamline', $path, ['class' => 'fabric']);

        // Mark path for sample service
        $p->paths['seamline']->setSample(true);

    }
     
    /**
     * Drafts the straight waistband
     * 
     * The straight waistband is just a rectangle with the width
     * of double the waistband width, since it will be folded
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftStaightWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['straightWaistband'];

        // Calculate the corners of the rectangle and other auxiliar points       
        $p->newPoint(0, 0, 0);
        $p->addPoint('centreLeft', $p->shift(0, 180, $this->v('topCircumference')/2));
        $p->addPoint('centreRight', $p->shift(0, 0, $this->v('topCircumference')/2 + $this->o('waistbandOverlap')));
        
        $p->addPoint('topRight', $p->shift('centreRight', 90, $this->o('waistbandWidth')));
        $p->addPoint('topLeft', $p->shift('centreLeft', 90, $this->o('waistbandWidth')));

        $p->addPoint('bottomRight', $p->flipY('topRight', 0));
        $p->addPoint('bottomLeft', $p->flipY('topLeft', 0));

        // Draft the rectangle and mark it for the sample service        
        $p->newPath('seamline', 'M topLeft L topRight L bottomRight L bottomLeft z', ['class' => 'fabric']);
        $p->paths['seamline']->setSample(true);

        // Draft the foldline and mark it for the sample service
        $p->newPath('foldline', 'M centreRight L centreLeft', ['class' => 'hint fabric']);
        $p->paths['foldline']->setSample(true);
    }

    /*
       _____ _             _ _
      |  ___(_)_ __   __ _| (_)_______
      | |_  | | '_ \ / _` | | |_  / _ \
      |  _| | | | | | (_| | | |/ /  __/
      |_|   |_|_| |_|\__,_|_|_/___\___|

      Adding titles/logos/sa fabric/measurements and so on
    */

    /**
     * Finalizes the skirt
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeSkirt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['skirt'];

        // Seam allowance
        if($this->o('sa')) {
            // Hem end seam allowance
            $p->offsetPathString('sa', 'M ex2 C ex2C ex1C ex1 C flip4 flip5 flip6', $this->o('hemWidth'), 1, ['class' => 'sa fabric']);

            if ($this->o('seamlessFullCircle') > 0){
                // Waist end and free edge seam allowance
                $p->offsetPathString('sa2', 'M flip1 C flip2 flip3 in1 C in1C in2C in2', $this->o('sa'), 1, ['class' => 'sa fabric']);
                // Join lines
                $p->newPath('sa3', 'M flip1 L sa2-startPoint M sa2-endPoint L in2 M ex2 L sa-startPoint M sa-endPoint L flip6', ['class' => 'sa fabric']);
            } else {
                // Waist end seam allowance (seamlessFullCircle option on, not free edge)
                $p->offsetPathString('sa2', 'M flip1 C flip2 flip3 in1 C in1C in2C in2 L ex2', $this->o('sa'), 1, ['class' => 'sa fabric']);
                // Join lines
                $p->addPoint('auxSa', $p->shiftTowards('sa2-endPoint', 'sa2-line-in2TOex2', -$this->o('hemWidth')));
                $p->newPath('sa3', 'M flip1 L sa2-startPoint M sa2-endPoint L auxSa L sa-startPoint M sa-endPoint L flip6', ['class' => 'sa fabric']);
            }
        }

        // Cut on fold line anchors
        $p->addPoint('cofTop', $p->shiftTowards('flip1', 'flip6', 40));
        $p->addPoint('cofBottom', $p->shiftTowards('flip6', 'flip1', 40));
        
        if ($this->o('seamlessFullCircle') > 0){
            //If seamlessFullCircle is on, draft two cut on fold lines
            $p->addPoint('cofLeft', $p->shiftTowards('in2', 'ex2', 40));
            $p->addPoint('cofRight', $p->shiftTowards('ex2', 'in2', 40));

            $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on double fold').', '.$this->t('Grainline'));
            $p->newCutonfold('cofLeft', 'cofRight', $this->t('Cut on double fold'));
        } else {
            //If seamlessFullCircle is off, draft one cut on fold line
            $p->newCutonfold('cofBottom', 'cofTop', $this->t('Cut on fold').', '.$this->t('Grainline'));
        }

        // Anchors for logo, scalebox and title
        $p->newPoint('logoAnchor', 150, ($p->y('flip1')+$p->y('flip6'))/2);
        $p->addPoint('scaleboxAnchor', $p->shift('logoAnchor', -90, 40));
        $p->addPoint('titleAnchor', $p->shift('logoAnchor', 90, 70));
        
        // Logo, scalebox and title
        $p->newSnippet('logo', 'logo', 'logoAnchor');
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        if ($this->o('seamlessFullCircle') > 0) $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('Cut on double fold'));
        else $p->addTitle('titleAnchor', 1, $this->t($p->title), '1x '.$this->t('Cut on fold'));

        // Notches at center and edges of the waistline, to match with the waistband
        $p->notch(['in2', 'flip1']);
       
    }

    /**
     * Finalizes the curved waistband
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeCurvedWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['curvedWaistband'];

        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'seamline', $this->o('sa'), 1, ['class' => 'sa fabric']);
        
        // Grainline
        $p->addPoint('grainlineAnchorRight', $p->shift('in1C', -90, $this->o('waistbandWidth')/2));
        $p->addPoint('grainlineAnchorLeft', $p->shift('flip3', -90, $this->o('waistbandWidth')/2));
        $p->newGrainline('grainlineAnchorLeft', 'grainlineAnchorRight', $this->t('Grainline'));

        // Title
        $p->addPoint('titleAnchor', $p->shift('in1C', -20, 20));
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '2x '.$this->t('from fabric')." + ".'2x '.$this->t('from interfacing'), ['scale' => 40, 'align' => 'left']);
        
        // If the waistband overlap is big enough, draft button and buttonhole
        if($this->o('waistbandOverlap') >= self::MIN_OVERLAP_BUTTON){
            // Angle to rotate the button and buttonhole
            $buttonAngle = $p->angle('flip1', 'flip6');

            // Buttonhole
            $p->newPoint('buttonholeAnchor', ($p->x('flip1') + $p->x('flip6'))/2, ($p->y('flip1') + $p->y('flip6'))/2);
            $p->addPoint('buttonholeAnchor', $p->shift('buttonholeAnchor', 90+$buttonAngle, $this->o('waistbandOverlap')/2));
            $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 'buttonholeAnchor', ['transform' => 'rotate('.(-$buttonAngle).' '.$p->x('buttonholeAnchor').' '.$p->y('buttonholeAnchor').')']);

            // Button
            $p->newPoint('buttonAnchor', ($p->x('in2') + $p->x('ex2'))/2, ($p->y('in2') + $p->y('ex2'))/2);
            $angle = $p->angle('flip1', 'flip6');
            $p->addPoint('buttonAnchor', $p->shift('buttonAnchor', 90-$buttonAngle, $this->o('waistbandOverlap')/2));
            $p->newSnippet($p->newId('button'), 'button', 'buttonAnchor', ['transform' => 'rotate('.$buttonAngle.' '.$p->x('buttonAnchor').' '.$p->y('buttonAnchor').')']);
        }

        // Centre point not counting the waistband Overlap
        $p->addPoint('centreBottom', $p->shiftAlong('ex1', 'flip4', 'flip5', 'flip6', $this->o('waistbandOverlap')/2));

        // Overlap point
        $p->addPoint('overlapNotch', $p->shiftAlong('ex2', 'ex2C', 'ex1C', 'ex1', $this->o('waistbandOverlap')));
        
        // Notches at centre and edges to match with the skirt
        $p->notch(['centreBottom', 'flip6','overlapNotch']);
    }

    /**
     * Finalizes the straight waistband
     *
     * Only draft() calls this method, sample() does not.
     * It does things like adding a title, logo, and any
     * text or instructions that go on the pattern.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizeStraightWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['straightWaistband'];

        // Seam allowance
        if($this->o('sa')) $p->offsetPath('sa', 'seamline', $this->o('sa'), 1, ['class' => 'sa fabric']);

        // Grainline
        $p->newPoint('grainlineAnchorLeft', -$this->v('topCircumference')/4, $this->o('waistbandWidth')/2);
        $p->newPoint('grainlineAnchorRight', $this->v('topCircumference')/4, $this->o('waistbandWidth')/2);
        $p->newGrainline('grainlineAnchorLeft', 'grainlineAnchorRight', $this->t('Grainline'));

        // Title
        $p->newPoint('titleAnchor', 0, -$this->o('waistbandWidth')/2);
        $p->addTitle('titleAnchor', 2, $this->t($p->title), '1x '.$this->t('from fabric')." + ".'1x '.$this->t('from interfacing'), ['scale' => 40]);

        // If the waistband overlap is big enough, draft button and buttonhole
        if($this->o('waistbandOverlap') >= self::MIN_OVERLAP_BUTTON){
            // Button
            $p->newPoint('buttonAnchor', $p->x('centreRight') - $this->o('waistbandOverlap')/2, ($p->y('centreRight') + $p->y('bottomRight'))/2);
            $p->newSnippet($p->newId('button'), 'button', 'buttonAnchor');

            // Buttonhole
            $p->newPoint('buttonholeAnchor', $p->x('centreLeft') + $this->o('waistbandOverlap')/2, ($p->y('centreLeft') + $p->y('bottomLeft'))/2);
            $p->newSnippet($p->newId('buttonhole'), 'buttonhole', 'buttonholeAnchor', ['transform' => 'rotate(90 '.$p->x('buttonholeAnchor').' '.$p->y('buttonholeAnchor').')']);
        }

        // Notch points
        $p->newPoint('centreBottom', 0, $this->o('waistbandWidth'));
        $p->newPoint('overlapNotch', $this->v('topCircumference')/2, $this->o('waistbandWidth'));

        // Notches at centre and edges to match with the skirt
        $p->notch(['bottomLeft', 'centreBottom', 'overlapNotch']);
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
     * Adds paperless info for the skirt
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessSkirt($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['skirt'];

        // Waist edge length
        $p->newCurvedDimension('M flip1 C flip2 flip3 in1 C in1C in2C in2', $this->o('sa')+self::PAPERLESS_OFFSET);

        // Hem edge length
        $p->newCurvedDimension('M flip6 C flip5 flip4 ex1 C ex1C ex2C ex2', -$this->o('hemWidth')-self::PAPERLESS_OFFSET);

        // Fold length
        $p->newLinearDimension('flip6', 'flip1', -self::PAPERLESS_OFFSET);

        // Centre to waist edge
        $p->newLinearDimension('flip1', 0, -self::PAPERLESS_OFFSET);
    }

    /**
     * Adds paperless info for the curved waistband
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessCurvedWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['curvedWaistband'];

        // Top edge length
        $p->newCurvedDimension('M flip1 C flip2 flip3 in1 C in1C in2C in2', $this->o('sa')+self::PAPERLESS_OFFSET);

        // Bottom edge length
        $p->newCurvedDimension('M flip6 C flip5 flip4 ex1 C ex1C ex2C ex2', -$this->o('sa')-self::PAPERLESS_OFFSET);

        // Height from bottom centre to bottom edge
        $p->newHeightDimension('ex2', 'ex1', $p->x('ex2')+$this->o('sa')+self::PAPERLESS_OFFSET);

        // Waistband width
        $p->newLinearDimension('in2', 'ex2', -$this->o('sa')-self::PAPERLESS_OFFSET);
    }

    /**
     * Adds paperless info for the straight waistband
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function paperlessStraightWaistband($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['straightWaistband'];

        // Height
        $p->newHeightDimension('topRight', 'bottomRight', $p->x('bottomRight')+$this->o('sa')+self::PAPERLESS_OFFSET);

        // Width
        $p->newWidthDimension('topLeft', 'topRight', $p->y('topRight')-self::PAPERLESS_OFFSET);
    }
}
