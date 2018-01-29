<?php
/** Freesewing\Patterns\Core\CathrinCorset class */
namespace Freesewing\Patterns\Core;

/**
 * The Cathrin Corset pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class CathrinCorset extends Pattern
{
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

        /* Where to divide our corset into panels */
        if ($this->o('panels') == 11) {
            $this->setValue('panels', 11);
            $this->setValue('gaps', [0.2,0.4,0.4,0.6,0.75]);
        } else {
            $this->setValue('panels', 13);
            $this->setValue('gaps', [0.15,0.275,0.4,0.6,0.75]);
        }

        /**
         * How much should we take in the corset at waist and bust
         *
         * I construct this corset assuming that the hips are larger
         * than the underbust. Can I be sure? Maybe not,
         * but I don't think I have ever seen a woman with a
         * larger underbust measurement than hips measurement.
         */
        $this->setValue('width', $model->m('hipsCircumference')/2 - $this->o('backOpening')/2);
        $this->setValue('bustIntake', $model->m('hipsCircumference')/2 - $model->m('underBust')/2);
        $this->setValue('waistIntake', $model->m('hipsCircumference')/2 - $model->m('naturalWaist')/2 + $this->o('waistReduction')/2);
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
     * all bells and whistles.
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
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

        $this->draftBase($model);
        $this->draftPanels($model);

        // Don't render base
        $this->parts['base']->setRender(false);
    }

    public function draft($model)
    {
        $this->sample($model);

        $this->finalizePanels($model);

        for ($i=1; $i<8; $i++) {
            $name = "Panel$i";
            if (!($this->v('panels') == 11 && $i == 4)) {
                if ($this->isPaperless) {
                    $this->{"paperless$name"}();
                }
            }
        }

        // Don't render panels
        $this->parts['panels']->setRender(false);
    }


    /**
     * Drafts the base
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftBase($model)
    {
        $p = $this->parts['base'];

        // Basic rectangle | Point index 1->
        $p->newPoint(   1, 0, 0, 'Underbust @ CF' );
        $p->newPoint(   2, 0, $model->m('naturalWaistToUnderbust') + $model->m('naturalWaistToHip'), 'Hips @ CF' );
        $p->newPoint(   3, $this->v('width'), $p->y(2), 'Hips @ side' );
        $p->newPoint(   4, $p->x(3), 0, 'Underbust @ side' );
        $p->newPoint(   5, $p->x(3)/2, 0, 'Quarter top' );
        $p->newPoint(   6, $p->x(3)/2, $p->y(2), 'Quarter bottom' );
        $p->newPoint(   7, 0, $model->m('naturalWaistToUnderbust'), 'Waist @ CF' );
        $p->newPoint(   8, $p->x(3), $p->y(7), 'Waist @ side' );

        // frontRise | Point index 10->
        $p->addPoint(   10, $p->shift( 1, 90, $this->o('frontRise'), 'Corset top edge @ CF'));
        $p->addPoint(   11, $p->shift(10, 0, $this->v('width') * 0.11, 'Control point for 10'));
        $p->addPoint(   12, $p->shift( 1, 0, $this->v('width') * 0.11, 'Control point fo 13'));
        $p->addPoint(   13, $p->shift( 1, 0, $this->v('width') * 0.15, 'frontRise curve ends here'));

        // frontDrop | Point index 20->
        $p->addPoint(   20, $p->shift( 2, -90, $this->o('frontDrop'), 'Corset bottom edge @ CF'));
        $p->addPoint(   21, $p->shift(20, 0, $this->v('width') * 0.11, 'Control point for 10'));

        // hipRise | Point index 30->
        $p->addPoint(   30, $p->shift( 6, 90, $this->o('hipRise'), 'Corset bottom edge @ CF'));
        $p->addPoint(   31, $p->shift(30, 180, $this->v('width') * 0.3, 'Control point for 30'));
        $p->addPoint(   32, $p->shift(30, 0, $this->v('width') * 0.2, 'Control point for 30'));

        // backDrop | Point index 40->
        $p->addPoint(   40, $p->shift( 3, -90, $this->o('backDrop'), 'Corset bottom edge @ side'));
        $p->addPoint(   41, $p->shift(40, 180, $this->v('width') * 0.3, 'Control point for 40'));

        // backRise | Point index 50->
        $p->addPoint(   50, $p->shift( 4, 90, $this->o('backRise'), 'Corset top edge @ side'));
        $p->addPoint(   51, $p->shift(50, 0, $this->v('width') * -0.4, 'Control point for 50'));
        $p->addPoint(   52, $p->shift(5, 0, $this->v('width') * 0.2, 'Control point for 5'));
        
        $helpPath = 'M 1 L 2 L 3 L 4 z M 5 L 6 M 7 L 8';
        $p->newPath('help', $helpPath, ['class' => 'helpline']);

        $path = 'M 20 C 21 31 30 C 32 41 40 L 50 C 51 52 5 L 13 C 12 11 10 z';
        $p->newPath('outline', $path, ['class' => 'seamline']);
    }

    /**
     * Drafts the corset panels
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function draftPanels($model)
    {
        $this->clonePoints('base', 'panels');
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        $helpLines = '';
        foreach ($this->v('gaps') as $g => $gap) {
            $i = $g+1; // Avoid zero
            // Underbust
            $p->newPoint(   100*$i, $this->v('width')*$gap, 0, "Gap $i center @ underbust" );
            $p->addPoint(   100*$i+1, $p->shift(100*$i, 180, $this->v('bustIntake') * 0.1), "Right edge @ underbust" );
            $p->addPoint(   100*$i+2, $p->shift(100*$i, 0, $this->v('bustIntake') * 0.1), "Left edge @ ubderbust" );
            $p->addPoint(   100*$i+3, $p->shift(100*$i+1, -90, $model->m('naturalWaistToUnderbust') * 0.15), "Control point for ".(100*$i+1) );
            $p->addPoint(   100*$i+4, $p->shift(100*$i+2, -90, $model->m('naturalWaistToUnderbust') * 0.15), "Control point for ".(100*$i+2) );

            // Waist
            $p->newPoint(   100*$i+40, $this->v('width')*$gap, $p->y(7), "Gap $i center @ waist" );
            $p->addPoint(   100*$i+41, $p->shift(100*$i+40, 180, $this->v('waistIntake') * 0.1), "Right edge @ waist" );
            $p->addPoint(   100*$i+42, $p->shift(100*$i+40, 0, $this->v('waistIntake') * 0.1), "Left edge @ waist" );
            $p->addPoint(   100*$i+43, $p->shift(100*$i+41, 90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+41) );
            $p->addPoint(   100*$i+44, $p->shift(100*$i+42, 90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+42) );
            $p->addPoint(   100*$i+45, $p->shift(100*$i+41, -90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+43) );
            $p->addPoint(   100*$i+46, $p->shift(100*$i+42, -90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+44) );

            // Hips
            $p->newPoint(   100*$i+80, $this->v('width')*$gap, $p->y(2), "Gap $i center @ hips" );
            $helpLines .= ' M '.(100*$i).' L '.(100*$i+80);
            $helpLines .= ' M '.(100*$i+1).' C '.(100*$i+3).' '.(100*$i+43).' '.(100*$i+41).' C '.(100*$i+45).' '.(100*$i+80).' '.(100*$i+80);
            $helpLines .= ' M '.(100*$i+2).' C '.(100*$i+4).' '.(100*$i+44).' '.(100*$i+42).' C '.(100*$i+46).' '.(100*$i+80).' '.(100*$i+80);
        }

        // Panel paths
        for ($i=1; $i<8; $i++) {
            $name = "Panel$i";
            $this->{"draft$name"}($model);
            $p->newPath($name, $this->{"path$i"}, ['class' => 'seamline']);
            // Mark for sampler service
            $p->paths[$name]->setSample(true);
        }

        // We're not rendering panels, but this will help pattern developers
        $path = 'M 20 C 21 31 30 C 32 41 40 L 50 C 51 52 5 L 13 C 12 11 10 z M 1 L 2 L 3 L 4 z M 5 L 6 M 7 L 8';
        $path.= $helpLines;
        $p->newPath('corsetStructure', $path, ['class' => 'helpline']);
    }

    /**
     * Drafts corset panel 1
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel1($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        // Containing the frontRise curve in this panel by shifting points
        $p->addPoint( 11, $p->shift(11, 180, $p->distance(100, 101)) );
        $p->addPoint( 12, $p->shift(12, 180, $p->distance(100, 101)) );
        $p->addPoint( 13, $p->shift(13, 180, $p->distance(100, 101)) );

        // Joint between panel 1 and to at the top
        $p->curveCrossesX(10, 11, 12, 13, $p->x(11), '50-');
        $p->splitCurve(10, 11, 12, 13, '50-1', '51-');

        // Joint between panel 1 and to at the bottom
        $p->curveCrossesX(20, 21, 31, 30, $p->x(180)/2.5, '52-');
        $p->splitCurve(20, 21, 31, 30, '52-1', '53-');

        // path
        $this->path1 = 'M 10 L 20 C 53-2 53-3 52-1 L 50-1 C 51-3 51-2 10 z';

        // Title anchor
        $p->newPoint('titleAnchor1', $p->x('50-1')/2, $p->y(7)/2, 'Title anchor panel 1');

        // Grid anchor
        $p->clonePoint(7, 'gridAnchor1');
    }

    /**
     * Drafts corset panel 2
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel2($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        $p->curveCrossesX(20, 21, 31, 30, $p->x(180), '190-'); // Intersection in 190-1
        $p->splitCurve('52-1', '53-7', '53-6', 30, '190-1', '191-');
        if ($p->y('190-1') < $p->y(180)) {
            // Dart still open at edge
            $p->curveCrossesY(141, 145, 180, 180, $p->y('190-1'), '192-'); // Intersection is in 192-1
            $p->curveCrossesY(142, 146, 180, 180, $p->y('190-1'), '193-'); // Intersection in 193-1
            $this->path2 = 'M 50-1 L 52-1 C 191-2 191-3 192-1 C 192-1 145 141 ';
            $this->path3 = 'M 102 C 104 144 142 C 146 193-1 193-1 ';

        } else {
            // dart is closed at edge. Easy! :)
            $this->path2 = 'M 50-1 L 52-1 C 191-2 191-3 190-1 L 180 C 180 145 141 ';
            $this->path3 = 'M 102 C 104 144 142 C 146 180 180 L 190-1 ';
        }
        $this->path2 .= 'C 143 103 101 C 51-6 51-7 50-1 z';
        // Title anchor
        $p->newPoint('titleAnchor2', $p->x(140)/1.5, $p->y(2)*0.7, 'Title anchor panel 2');
        // Grid anchor
        $p->newPoint('gridAnchor2', $p->x('titleAnchor2'), $p->y(7), 'Grid anchor panel 2');
    }

    /**
     * Drafts corset panel 3
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel3($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        // Where does gap center cut through bottom curve
        $p->curveCrossesX(20, 21, 31, 30, $p->x(280), '290-'); // Intersection in 290-1
        $p->splitCurve('190-1', '191-7', '191-6', 30, '290-1', '291-');
        if ($p->y('290-1') < $p->y(180)) {
            // Dart still open at edge
            $p->curveCrossesY(241, 245, 280, 280, $p->y('290-1'), '292-'); // Intersection is in 292-1
            $p->curveCrossesY(242, 246, 280, 280, $p->y('290-1'), '293-'); // Intersection is in 293-1
            $this->path3 .= 'C 291-2 291-3 292-1 C 292-1 245 241 ';
            $this->path4 = 'M 202 C 204 244 242 C 246 293-1 293-1 ';
        } else {
            // dart is closed at edge. Easy! :)
            $this->path3 .= 'C 291-2 291-3 290-1 L 280 C 280 245 241 ';
            $this->path4 = 'M 202 C 204 244 242 C 246 280 280 L 290-1 ';
        }
        $this->path3 .= 'C 243 203 201 z';
        // Title anchor
        $p->newPoint('titleAnchor3', $p->x(240)-$p->deltaX(140, 240)/2, $p->y('titleAnchor2'), 'Title anchor panel 3');
        // Grid anchor
        $p->newPoint('gridAnchor3', $p->x('titleAnchor3'), $p->y(7), 'Grid anchor panel 3');
    }

    /**
     * Drafts corset panel 4
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel4($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        // Where does gap center cut through bottom curve
        $p->curveCrossesX(20, 21, 31, 30, $p->x(380), '390-'); // Intersection in 390-1
        $p->splitCurve('290-1', '291-7', '291-6', 30, '390-1', '391-');
        if ($p->y('390-1') < $p->y(180)) {
            // Dart still open at edge
            $p->curveCrossesY(341, 345, 380, 380, $p->y('390-1'), '392-'); // Intersection is in 392-1
            $p->curveCrossesY(342, 346, 380, 380, $p->y('390-1'), '393-'); // Intersection is in 393-1
            // Dart is not symmetric
            $p->addPoint('cpPart4', $p->shift('392-1', 90, $p->deltaY(7, '392-1')/1.6), 'Control point for assymetric dart');
            $this->path4 .= 'C 391-2 391-3 392-1 C cpPart4 345 341 ';
            $this->path5 = 'C 304 344 342 C 346 393-1 393-1 ';
        } else {
            // dart is closed at edge. Easy! :)
            $p->addPoint('cpPart4', $p->shift(380, 90, $p->deltaY(7, 380)/1.6), 'Control point for assymetric dart');
            $this->path4 .= 'C 391-2 391-3 390-1 L 380 C cpPart4 345 341 ';
            $this->path5 = 'C 304 344 342 C 346 380 380 ';
        }
        $this->path4 .= 'C 343 303 301 z';

        // Title anchor
        $p->newPoint('titleAnchor4', $p->x(340)-$p->deltaX(240, 340)/2, $p->y('titleAnchor2'), 'Title anchor panel 4');

        // Grid anchor
        $p->newPoint('gridAnchor4', $p->x('titleAnchor4'), $p->y(7), 'Grid anchor panel 4');
    }

    /**
     * Drafts corset panel 5
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel5($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        // Where does the right edge cut through top curve
        $p->curveCrossesX(50, 51, 52, 5, $p->x(401), '410-'); // Intersection in 410-1
        $p->splitCurve(50, 51, 52, 5, '410-1', '411-');
        $this->path5 = 'M 441 C 443 403 401 L 410-1 C 411-7 411-6 5 L 302 '. $this->path5;
        // Where does gap center cut through bottom curve
        $p->curveCrossesX(30, 32, 41, 40, $p->x(480), '490-'); // Intersection in 490-1
        $p->splitCurve(40, 41, 32, 30, '490-1', '491-');
        if ($p->y('490-1') < $p->y(180)) {
            // Dart still open at edge
            $p->curveCrossesY(441, 445, 480, 480, $p->y('490-1'), '492-'); // Intersection is in 492-1
            $p->curveCrossesY(442, 446, 480, 480, $p->y('490-1'), '493-'); // Intersection is in 493-1
            $this->path5 .= 'C 391-7 391-6 30 C 491-6 491-7 492-1 C 492-1 445 441 z';
            $this->path6 = 'C 446 cpPart6 493-1 ';
            /* Dart is not symmetric */
            $p->addPoint('cpPart6', $p->shift('493-1', 90, $p->deltaY(7, '493-1')/1.6), 'Control point for assymetric dart');
        } else {
            // dart is closed at edge. Easy! :)
            $p->addPoint('cpPart6', $p->shift(480, 90, $p->deltaY(7, 480)/1.6), 'Control point for assymetric dart');
            $this->path5 .= 'L 390-1 C 391-7 391-6 30 C 491-6 491-7 490-1 L 480 C 480 445 441 ';
            $this->path6 = 'C 446 cpPart6 480 L 490-1 ';
        }

        // Title anchor
        $p->newPoint('titleAnchor5', $p->x(440)-$p->deltaX(340, 440)/2, $p->y('titleAnchor2'), 'Title anchor panel 5');

        // Grid anchor
        $p->newPoint('gridAnchor5', $p->x('titleAnchor5'), $p->y(7), 'Grid anchor panel 5');
    }

    /**
     * Drafts corset panel 6
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel6($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        // Where does the left edge cut through top curve
        $p->curveCrossesX(50, 51, 52, 5, $p->x(402), '420-'); // Intersection in 420-1
        $p->splitCurve('410-1', '411-3', '411-2', 50, '420-1', '421-');

        // Where does the right edge cut through top curve
        $p->curveCrossesX(50, 51, 52, 5, $p->x(501), '510-'); // Intersection in 510-1
        $p->splitCurve('420-1', '421-7', '421-6', 50, '510-1', '421-');
        if ($this->o('backRise') == 0) {
            $this->path6 = 'M 541 C 543 503 501 L 402 C 404 444 442 '. $this->path6;
        } else {
            $this->path6 = 'M 541 C 543 503 501 L 510-1 C 421-3 421-2 420-1 L 402 C 404 444 442 '. $this->path6;
            // We need to left edge and control point to keep the same height
            $deltaY = $p->deltaY('410-1', '420-1');
            $p->addPoint('420-1', $p->shift('420-1', 90, $deltaY));
            $p->addPoint('421-2', $p->shift('421-2', 90, $deltaY));
        }

        // Where does gap center cut through bottom curve
        $p->curveCrossesX(30, 32, 41, 40, $p->x(580), '590-'); // Intersection in 490-1
        $p->splitCurve(40, '491-2', '491-3', '490-1', '590-1', '591-');
        if ($p->y('590-1') < $p->y(180)) {
            // Dart still open at edge
            $p->curveCrossesY(541, 545, 580, 580, $p->y('590-1'), '592-'); // Intersection is in 592-1
            $p->curveCrossesY(542, 546, 580, 580, $p->y('590-1'), '593-'); // Intersection is in 593-1
            $this->path6 .= 'C 591-6 591-7 592-1 C 592-1 545 541 z';
        } else {
            // dart is closed at edge. Easy! :)
            $this->path6 .= 'C 591-6 591-7 590-1 L 580 C 580 543 541 z';
        }

        // Title anchor
        $p->newPoint('titleAnchor6', $p->x(540)-$p->deltaX(440, 540)/2, $p->y('titleAnchor2'), 'Title anchor panel 6');

        // Grid anchor
        $p->newPoint('gridAnchor6', $p->x('titleAnchor6'), $p->y(7), 'Grid anchor panel 6');
    }

    /**
     * Drafts corset panel 7
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel7($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panels'];

        // Where does the left edge cut through top curve
        $p->curveCrossesX(50, 51, 52, 5, $p->x(502), '520-'); // Intersection in 520-1
        $p->splitCurve('510-1', '421-7', '421-6', 50, '520-1', '521-');

        // We need to left edge and control point to keep the same height
        $deltaY = $p->deltaY('510-1', '520-1');
        $p->addPoint('520-1', $p->shift('520-1', 90, $deltaY));
        $p->addPoint('521-7', $p->shift('521-7', 90, $deltaY));
        $this->path7 = 'M 50 C 521-6 521-7 520-1 L 502 C 504 544 542 ';
        if ($p->y('590-1') < $p->y(180)) {
            // Dart still open at edge
            $this->path7 .= 'C 546 593-1 593-1 ';
        } else {
            // dart is closed at edge. Easy! :)
            $this->path7 .= 'C 546 580 580 L 590-1 ';
        }
        $this->path7 .= 'C 591-3 591-2 40 z';

        // Title anchor
        $p->newPoint('titleAnchor7', $p->x(8)-$p->deltaX(540, 8)/2, $p->y('titleAnchor2'), 'Title anchor panel 7');

        // Grid anchor
        $p->clonePoint(8, 'gridAnchor7');
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
     * Finalizes the corset panels
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void
     */
    public function finalizePanels($model)
    {
        // Clone each panel into its own pattern part
        for ($i=1; $i<8; $i++) {
            if (!($this->v('panels') == 11 && $i == 4)) {
                $part = "panel$i";
                $this->newPart($part);
                $p = $this->parts[$part];
                $this->clonePoints('panels', $part);
                $p->newPath($part, $this->{"path$i"}, ['class' => 'fabric']);
                $p->paths[$part]->setSample(true);
                $p->clonePoint("gridAnchor$i", 'gridAnchor');
                if ($i == 1) {
                    $msg =  '1x '.$this->t('from fabric')."\n".$this->t('Cut on fold');
                } else {
                    $msg = '2x '.$this->t('from fabric');
                }
                $p->addTitle("titleAnchor$i", $i, $this->t('Panel')." $i", $msg, ['rotate' => -90, 'align' => 'left', 'scale' => 50]);
                if($this->o('sa')) $p->offsetPathString($part.'-sa', $this->{"path$i"}, $this->o('sa')*-1, 1, ['class' => 'fabric sa']);

            }
        }
        
        $this->finalizePanel1($model);
        $this->finalizePanel2($model);
        $this->finalizePanel3($model);
        if (isset($this->parts['panel4'])) {
            $this->finalizePanel4($model);
        }
        $this->finalizePanel5($model);
        $this->finalizePanel6($model);
        $this->finalizePanel7($model);
    }

    /**
     * Finalizes corset panel 1
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel1($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel1'];

        $p->addPoint('cofTop', $p->shift(10, -90, 20));
        $p->addPoint('cofBottom', $p->shift(20, 90, 20));
        $p->newCutonfold('cofBottom','cofTop',$this->t('Cut on fold').' - '.$this->t('Grainline'),9);
    }

    /**
     * Finalizes corset panel 2
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel2($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel2'];

        $p->newPoint('grainlineTop', $p->x('titleAnchor2'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2));
        $p->newGrainline('grainlineBottom','grainlineTop',$this->t('Grainline'));
    }

    /**
     * Finalizes corset panel 3
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel3($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel3'];

        $p->newPoint('grainlineTop', $p->x('titleAnchor3'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise'));
        $p->newGrainline('grainlineBottom','grainlineTop',$this->t('Grainline'));
    }

    /**
     * Finalizes corset panel 4
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel4($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel4'];

        $p->newPoint('grainlineTop', $p->x('titleAnchor4'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise')-10);
        $p->newGrainline('grainlineBottom','grainlineTop',$this->t('Grainline'));
    }

     /**
     * Finalizes corset panel 5
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel5($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel5'];

        $p->newPoint('grainlineTop', $p->x('titleAnchor5'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise')-10);
        $p->newGrainline('grainlineBottom','grainlineTop',$this->t('Grainline'));

        // Logo and CC
        $p->addPoint('logoAnchor', $p->shift('411-5', -90, 40));
        $p->newSnippet('logo', 'logo-sm', 'logoAnchor');
    }

    /**
     * Finalizes corset panel 6
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel6($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel6'];

        $p->newPoint('grainlineTop', $p->x('titleAnchor6'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise')-10);
        $p->newGrainline('grainlineBottom','grainlineTop',$this->t('Grainline'));
    }

    /**
     * Finalizes corset panel 7
     *
     * @param \Freesewing\Model $model The model to draft for
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel7($model)
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel7'];

        $p->newPoint('grainlineTop', $p->x(40)-10, $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-10);
        $p->newGrainline('grainlineBottom','grainlineTop',$this->t('Grainline'));
        $p->newPoint('scaleboxAnchor', $p->x(50)-66, $p->y(50)+60);
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        // This part is narrow, better rotate that scalebox
        $p->snippets['scalebox']->setAttributes(['transform' => 'rotate( -90 '.$p->points['scaleboxAnchor']->getX().' '.$p->points['scaleboxAnchor']->getY().')']);
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
     * Adds paperless info for panel1
     *
     * @return void
     */
    public function paperlessPanel1()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel1'];

        // Helpline at waist
        $p->addPoint('.waist1', $p->beamsCross('51-8','191-1','gridAnchor',141)); // Help point at waist
        $p->newPath('.helpWaist', 'M gridAnchor L .waist1', ['class' => 'helpline']);

        // Lenght of seam right
        $p->newLinearDimension('52-1','50-1',25);

        // Heights on the right
        $xBase = $p->x('50-1')+25;
        $p->newHeightDimension('191-1','.waist1',$p->x('50-1')+40);
        $p->newHeightDimension(20,'.waist1',$p->x(20)-25);
        $p->newHeightDimension('.waist1','51-8',$p->x('50-1')+40);
        $p->newHeightDimension('.waist1','51-1',$p->x(20)-25);

        //Width at the top
        $p->newWidthDimension(10,'50-1',$p->y(10)-25);

        // Width at the bottom
        $p->newWidthDimensionSm(20,'191-1',$p->y(20)+25);

        // Width at the waist
        $p->newWidthDimensionSm('gridAnchor','.waist1');
    }

    /**
     * Adds paperless info for panel2
     *
     * @return void
     */
    public function paperlessPanel2()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel2'];

        // Helpline at waist
        $p->addPoint('.waist2', $p->beamsCross('50-1','52-1','gridAnchor2',141)); // Help point at waist
        $p->newPath('.helpWaist', 'M 141 L .waist2', ['class' => 'helpline']);

        // Seam length right
        $p->newCurvedDimension('M 291-1 C 180 145 141 C 143 103 101', -25);

        // Seam length left
        $p->newLinearDimension('191-1','50-1', -25);

        // Heights on the right
        $xBase = $p->x('291-1');
        $p->newHeightDimension('291-1',141,$xBase+40);
        $p->newHeightDimension('191-1',141,$xBase+55);
        $p->newHeightDimension(141,101,$xBase+40);
        $p->newHeightDimension(141,'50-1',$xBase+55);

        //Width at the top
        $p->newWidthDimensionSm('50-1',101,$p->y('50-1')-25);

        // Width at the bottom
        $p->newWidthDimensionSm('191-1','291-1',$p->y('191-1')+25);

        // Width at the waist
        $p->newWidthDimensionSm('.waist2',141);
    }

    /**
     * Adds paperless info for panel3
     *
     * @return void
     */
    public function paperlessPanel3()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel3'];

        // Width at the waist
        $p->newWidthDimensionSm(142,241);

        // Width at the bottom
        $p->newWidthDimension('190-1','292-1',$p->y('190-1')+25);

        // Width at the top
        $p->newWidthDimension(102,201,$p->y(201)-25);

        // Seam length right
        $p->newCurvedDimension('M 292-1 C 292-1 245 241 C 243 203 201', -45);

        // Seam length left
        $p->newCurvedDimension('M 291-1 C 291-1 146 142 C 144 104 102', 40);

        // Heights at the left
        $p->newHeightDimension('291-1',142,$p->x('291-1')-15);
        $p->newHeightDimension(142,102,$p->x('291-1')-15);

        // Heights at the right
        $p->newHeightDimension('292-1',241,$p->x('292-1')+20);
        $p->newHeightDimension(241,201,$p->x('292-1')+20);
    }

    /**
     * Adds paperless info for panel4
     *
     * @return void
     */
    public function paperlessPanel4()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel4'];

        // Width at the waist
        $p->newWidthDimensionSm(242,341);

        // Width at the bottom
        $p->newWidthDimension('290-1','392-1',$p->y('290-1')+25);

        // Width at the top
        $p->newWidthDimension(202,301,$p->y(301)-25);

        // Seam length right
        $p->newCurvedDimension('M 392-1 C 392-1 345 341 C 343 303 301', -45);

        // Seam length left
        $p->newCurvedDimension('M 391-1 C 391-1 246 242 C 244 204 202', 40);

        // Heights at the left
        $p->newHeightDimension('391-1',242,$p->x('391-1')-15);
        $p->newHeightDimension(242,202,$p->x('391-1')-15);

        // Heights at the right
        $p->newHeightDimension('392-1',341,$p->x('392-1')+20);
        $p->newHeightDimension(341,301,$p->x('392-1')+20);
    }

    /**
     * Adds paperless info for panel5
     *
     * @return void
     */
    public function paperlessPanel5()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel5'];

        // Width at the waist
        $p->newWidthDimension(342,441);

        // Width at the bottom
        $p->newWidthDimension('393-1','492-1',$p->y('390-1')+25);

        // Width at the top
        $p->newWidthDimension(302,401,$p->y(401)-25);

        // Seam length right
        $p->newCurvedDimension('M 492-1 C 492-1 445 441 C 443 403 401', -45);

        // Seam length left
        $p->newCurvedDimension('M 393-1 C 393-1 346 342 C 344 304 302', 40);

        // Heights at the left
        $p->newHeightDimension('393-1',342,$p->x('393-1')-15);
        $p->newHeightDimension(342,302,$p->x(302)-15);

        // Heights at the right
        $p->newHeightDimension('492-1',441,$p->x('492-1')+20);
        $p->newHeightDimension(441,401,$p->x('492-1')+20);
    }

    /**
     * Adds paperless info for panel6
     *
     * @return void
     */
    public function paperlessPanel6()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel6'];

        // Width at the waist
        $p->newWidthDimensionSm(442,541);

        // Width at the bottom
        $p->newWidthDimension('493-1','592-1',$p->y('490-1')+25);

        // Width at the top
        $p->newWidthDimension(402,501,$p->y(501)-25);

        // Seam length right
        $p->newCurvedDimension('M 592-1 C 592-1 545 541 C 543 503 501', -45);

        // Seam length left
        $p->newCurvedDimension('M 493-1 C 493-1 446 442 C 444 404 402', 40);

        // Heights at the left
        $p->newHeightDimension('493-1',442,$p->x('493-1')-15);
        $p->newHeightDimension(442,402,$p->x(402)-15);

        // Heights at the right
        $p->newHeightDimension('592-1',541,$p->x('592-1')+20);
        $p->newHeightDimension(541,501,$p->x('592-1')+20);
    }

    /**
     * Adds paperless info for panel7
     *
     * @return void
     */
    public function paperlessPanel7()
    {
        /** @var \Freesewing\Part $p */
        $p = $this->parts['panel7'];

        // Width at the waist
        $p->newWidthDimension(542,'gridAnchor');

        // Width at the bottom
        $p->newWidthDimension('593-1',40,$p->y(40)+25);

        // Width at the top
        $p->newWidthDimension(502,50,$p->y(50)-25);

        // Seam length left
        $p->newCurvedDimension('M 593-1 C 593-1 546 542 C 544 504 502', 40);

        // Heights at the left
        $p->newHeightDimension('593-1',542,$p->x('593-1')-15);
        $p->newHeightDimension(542,502,$p->x('593-1')-15);

        // Heights at the right
        $p->newHeightDimension(40,'gridAnchor',$p->x(40)+20);
        $p->newHeightDimension('gridAnchor',50,$p->x(40)+20);
    }


}
