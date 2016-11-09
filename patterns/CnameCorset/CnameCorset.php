<?php
/** Freesewing\Patterns\CnameCorset class */
namespace Freesewing\Patterns;

/**
 * The Cname Corset pattern
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class CnameCorset extends Pattern
{
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
        $this->finalizePanels($model);
        /* Don't render panels */
        $this->parts['panels']->setRender(false);
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
        $this->loadHelp($model);

        $this->draftBase($model);
        $this->draftPanels($model);
        /* Don't render base */
        $this->parts['base']->setRender(false);

    }

    /**
     * Sets up some properties shared between methods
     *
     * @param \Freesewing\Model $model The model to sample for
     *
     * @return void
     */
    public function loadHelp($model)
    {
      
        /* Where to divide our corset into panels */
        if($this->o('panels') == 11) {
            $this->panels = 11;
            $this->gaps = array(0.2,0.4,0.4,0.6,0.75);
        } else {
            $this->panels = 13;
            $this->gaps = array(0.15,0.275,0.4,0.6,0.75);
        }

        /** 
         * How much should we take in the corset at waist and bust
         *
         * I construct this corset assuming that the hips are larger
         * than the underbust. Can I be sure? Maybe not, 
         * but I don't think I have ever seen a woman with a 
         * larger underbust measurement than hips measurement.
         */
        $this->width = $model->m('hips')/2 - $this->o('backOpening')/2;
        $this->bustIntake  = $model->m('hips')/2 - $model->m('underBust')/2;
        $this->waistIntake = $model->m('hips')/2 - $model->m('naturalWaist')/2 + $this->o('waistReduction')/2;
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
        
        /* Clone base points */
        $this->clonePoints('base', 'panels');
        
        $p = $this->parts['panels'];

        $helpLines = '';
        foreach($this->gaps as $g => $gap) {
            $i = $g+1; // Avoid zero
            /* Underbust */
            $p->newPoint(   100*$i,    $this->width*$gap, 0, "Gap $i center @ underbust" );
            $p->addPoint(   100*$i+1, $p->shift(100*$i, 180, $this->bustIntake * 0.1), "Right edge @ underbust" );
            $p->addPoint(   100*$i+2, $p->shift(100*$i,   0, $this->bustIntake * 0.1), "Left edge @ ubderbust" );
            $p->addPoint(   100*$i+3, $p->shift(100*$i+1, -90, $model->m('naturalWaistToUnderbust') * 0.15), "Control point for ".(100*$i+1) );
            $p->addPoint(   100*$i+4, $p->shift(100*$i+2, -90, $model->m('naturalWaistToUnderbust') * 0.15), "Control point for ".(100*$i+2) );
  
            /* Waist */
            $p->newPoint(   100*$i+40, $this->width*$gap, $p->y(7), "Gap $i center @ waist" );
            $p->addPoint(   100*$i+41, $p->shift(100*$i+40, 180, $this->waistIntake * 0.1), "Right edge @ waist" );
            $p->addPoint(   100*$i+42, $p->shift(100*$i+40,   0, $this->waistIntake * 0.1), "Left edge @ waist" );
            $p->addPoint(   100*$i+43, $p->shift(100*$i+41,  90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+41) );
            $p->addPoint(   100*$i+44, $p->shift(100*$i+42,  90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+42) );
            $p->addPoint(   100*$i+45, $p->shift(100*$i+41, -90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+43) );
            $p->addPoint(   100*$i+46, $p->shift(100*$i+42, -90, $model->m('naturalWaistToUnderbust') * 0.2), "Control point for ".(100*$i+44) );
  
            /* Hips */
            $p->newPoint(   100*$i+80, $this->width*$gap, $p->y(2), "Gap $i center @ hips" );
            $helpLines .= ' M '.(100*$i).' L '.(100*$i+80);
            $helpLines .= ' M '.(100*$i+1).' C '.(100*$i+3).' '.(100*$i+43).' '.(100*$i+41).' C '.(100*$i+45).' '.(100*$i+80).' '.(100*$i+80);
            $helpLines .= ' M '.(100*$i+2).' C '.(100*$i+4).' '.(100*$i+44).' '.(100*$i+42).' C '.(100*$i+46).' '.(100*$i+80).' '.(100*$i+80);
        }


        // Draft panels, mark path for sampler
        for($i=1;$i<8;$i++) {
            $name = "Panel$i";
            $this->{"draft$name"}($model, $p);
            $p->newPath($name, $this->{"path$i"}, ['class' => 'seamline']);
            $p->paths[$name]->setSample(true);
        }
        $path = 'M 20 C 21 31 30 C 32 41 40 L 50 C 51 52 5 L 13 C 12 11 10 z M 1 L 2 L 3 L 4 z M 5 L 6 M 7 L 8';
        $path.= $helpLines;
        $p->newPath('corsetStructure', $path, ['class' => 'helpline']);
    }

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
        for($i=1;$i<8;$i++) {
            if(!($this->panels == 11 && $i == 4)) {
                $part = "panel$i";
                $this->addPart($part);
                $p = $this->parts[$part];
                $this->clonePoints('panels', $part);
                $p->newPath($part, $this->{"path$i"}, ['class' => 'seamline']);
                $p->paths[$part]->setSample(true);
                $p->clonePoint("gridAnchor$i", 'gridAnchor');
                if($i == 1) $msg =  $this->t('Cut 1 on fold');
                else $msg = $this->t('Cut 2');
                $p->addTitle("titleAnchor$i", $i, $this->t('Panel')." $i", $msg, 'vertical');
                $p->offsetPathString($part, $this->{"path$i"}, 10, 1, ['class' => 'seam-allowance']);

            }
        }
        $this->finalizePanel1($model,$this->parts['panel1']);
        $this->finalizePanel2($model,$this->parts['panel2']);
        $this->finalizePanel3($model,$this->parts['panel3']);
        if(isset($this->parts['panel4'])) $this->finalizePanel4($model,$this->parts['panel4']);
        $this->finalizePanel5($model,$this->parts['panel5']);
        $this->finalizePanel5($model,$this->parts['panel5']);
        $this->finalizePanel7($model,$this->parts['panel7']);
    }

    /**
     * Drafts corset panel 1
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel1($model, $p)
    {
        // Containing the frontRise curve in this panel by shifting points
        $p->addPoint( 11, $p->shift(11, 180, $p->distance(100,101)) );
        $p->addPoint( 12, $p->shift(12, 180, $p->distance(100,101)) );
        $p->addPoint( 13, $p->shift(13, 180, $p->distance(100,101)) );
        /* Joint between panel 1 and to at the top */
        $p->curveCrossesX(10,11,12,13,$p->x(11), '50-');
        $p->addSplitCurve('51-',10,11,12,13,'50-1');
        /* Joint between panel 1 and to at the bottom */
        $p->curveCrossesX(20,21,31,30,$p->x(180)/2.5, '52-');
        $p->addSplitCurve('53-',20,21,31,30,'52-1');
        /* path */
        $this->path1 = 'M 10 L 20 C 53-2 53-3 52-1 L 50-1 C 51-3 51-2 10 z';
        /* Title anchor */
        $p->newPoint('titleAnchor1', $p->x('50-1')/2, $p->y(7)/2, 'Title anchor panel 1');
        /* Grid anchor */
        $p->clonePoint(7,'gridAnchor1');

    }

    /**
     * Finalizes corset panel 1
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel1($model, $p)
    {
        $p->addPoint('grainlineTop', $p->shift(10,-45,10));
        $p->addPoint('grainlineBottom', $p->shift(20,45,10));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
    }

    /**
     * Drafts corset panel 2
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel2($model, $p)
    {
        $p->curveCrossesX(20,21,31,30,$p->x(180), '190-'); // Intersection in 190-1
        $p->addSplitCurve('191-','52-1','53-7','53-6',30,'190-1'); 
        if($p->y('190-1') < $p->y(180)) { // Dart still open at edge
            $p->curveCrossesY(141,145,180,180,$p->y('190-1'), '192-'); // Intersection is in 192-1
            $p->curveCrossesY(142,146,180,180,$p->y('190-1'), '193-'); // Intersection in 193-1
            $this->path2 = 'M 50-1 L 52-1 C 191-2 191-3 192-1 C 192-1 145 141 '; 
            $this->path3 = 'M 102 C 104 144 142 C 146 193-1 193-1 ';

        } else { // dart is closed at edge. Easy! :) 
            $this->path2 = 'M 50-1 L 52-1 C 191-2 191-3 190-1 L 180 C 180 145 141 '; 
            $this->path3 = 'M 102 C 104 144 142 C 146 180 180 L 190-1 ';
        } 
        $this->path2 .= 'C 143 103 101 C 51-6 51-7 50-1 z';
        /* Title anchor */
        $p->newPoint('titleAnchor2', $p->x(140)/1.5, $p->y(2)*0.7, 'Title anchor panel 2');
        /* Grid anchor */
        $p->newPoint('gridAnchor2', $p->x('titleAnchor2'), $p->y(7), 'Grid anchor panel 2');
    }

    /**
     * Finalizes corset panel 2
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel2($model, $p)
    {
        $p->newPoint('grainlineTop', $p->x('titleAnchor2'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
    }

    /**
     * Drafts corset panel 3
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel3($model, $p)
    {
        /* Where does gap center cut through bottom curve */
        $p->curveCrossesX(20,21,31,30,$p->x(280), '290-'); // Intersection in 290-1
        $p->addSplitCurve('291-','190-1','191-7','191-6',30,'290-1'); 
        if($p->y('290-1') < $p->y(180)) { // Dart still open at edge
            $p->curveCrossesY(241,245,280,280,$p->y('290-1'), '292-'); // Intersection is in 292-1
            $p->curveCrossesY(242,246,280,280,$p->y('290-1'), '293-'); // Intersection is in 293-1
            $this->path3 .= 'C 291-2 291-3 292-1 C 292-1 245 241 '; 
            $this->path4 = 'M 202 C 204 244 242 C 246 293-1 293-1 ';
        } else { // dart is closed at edge. Easy! :) 
            $this->path3 .= 'C 291-2 291-3 290-1 L 280 C 280 245 241 '; 
            $this->path4 = 'M 202 C 204 244 242 C 246 280 280 L 290-1 ';
        } 
        $this->path3 .= 'C 243 203 201 z';
        /* Title anchor */
        $p->newPoint('titleAnchor3', $p->x(240)-$p->deltaX(140,240)/2, $p->y('titleAnchor2'), 'Title anchor panel 3');
        /* Grid anchor */
        $p->newPoint('gridAnchor3', $p->x('titleAnchor3'), $p->y(7), 'Grid anchor panel 3');
    }

    /**
     * Finalizes corset panel 3
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel3($model, $p)
    {
        $p->newPoint('grainlineTop', $p->x('titleAnchor3'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise'));
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
    }

    /**
     * Drafts corset panel 4
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel4($model, $p)
    {
        /* Where does gap center cut through bottom curve */
        $p->curveCrossesX(20,21,31,30,$p->x(380), '390-'); // Intersection in 390-1
        $p->addSplitCurve('391-','290-1','291-7','291-6',30,'390-1'); 
        if($p->y('390-1') < $p->y(180)) { // Dart still open at edge
            $p->curveCrossesY(341,345,380,380,$p->y('390-1'), '392-'); // Intersection is in 392-1
            $p->curveCrossesY(342,346,380,380,$p->y('390-1'), '393-'); // Intersection is in 393-1
            /* Dart is not symmetric */
            $p->addPoint('cpPart4', $p->shift('392-1',90,$p->deltaY(7, '392-1')/1.6),'Control point for assymetric dart');
            $this->path4 .= 'C 391-2 391-3 392-1 C cpPart4 345 341 '; 
            $this->path5 = 'C 304 344 342 C 346 393-1 393-1 ';
        } else { // dart is closed at edge. Easy! :) 
            $p->addPoint('cpPart4', $p->shift(380,90,$p->deltaY(7, 380)/1.6),'Control point for assymetric dart');
            $this->path4 .= 'C 391-2 391-3 390-1 L 380 C cpPart4 345 341 '; 
            $this->path5 = 'C 304 344 342 C 346 380 380 ';
        } 
        $this->path4 .= 'C 343 303 301 z';
        /* Title anchor */
        $p->newPoint('titleAnchor4', $p->x(340)-$p->deltaX(240,340)/2, $p->y('titleAnchor2'), 'Title anchor panel 4');
        /* Grid anchor */
        $p->newPoint('gridAnchor4', $p->x('titleAnchor4'), $p->y(7), 'Grid anchor panel 4');
    }

    /**
     * Finalizes corset panel 4
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel4($model, $p)
    {
        $p->newPoint('grainlineTop', $p->x('titleAnchor4'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise')-10);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
    }

    /**
     * Drafts corset panel 5
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel5($model, $p)
    {
        /* Where does the right edge cut through top curve */
        $p->curveCrossesX(50,51,52,5,$p->x(401), '410-'); // Intersection in 410-1
        $p->addSplitCurve('411-',50,51,52,5,'410-1'); 
        $this->path5 = 'M 441 C 443 403 401 L 410-1 C 411-7 411-6 5 L 302 '. $this->path5;
        /* Where does gap center cut through bottom curve */
        $p->curveCrossesX(30,32,41,40,$p->x(480), '490-'); // Intersection in 490-1
        $p->addSplitCurve('491-',40,41,32,30,'490-1'); 
        if($p->y('490-1') < $p->y(180)) { // Dart still open at edge
            $p->curveCrossesY(441,445,480,480,$p->y('490-1'), '492-'); // Intersection is in 492-1
            $p->curveCrossesY(442,446,480,480,$p->y('490-1'), '493-'); // Intersection is in 493-1
            $this->path5 .= 'C 391-7 391-6 30 C 491-6 491-7 492-1 C 492-1 445 441 z'; 
            $this->path6 = 'C 446 cpPart6 493-1 ';
            /* Dart is not symmetric */
            $p->addPoint('cpPart6', $p->shift('493-1',90,$p->deltaY(7, '493-1')/1.6),'Control point for assymetric dart');
        } else { // dart is closed at edge. Easy! :) 
            $p->addPoint('cpPart6', $p->shift(480,90,$p->deltaY(7, 480)/1.6),'Control point for assymetric dart');
            $this->path5 .= 'L 390-1 C 391-7 391-6 30 C 491-6 491-7 490-1 L 480 C 480 445 441 '; 
            $this->path6 = 'C 446 cpPart6 480 L 490-1 ';
        } 
        /* Title anchor */
        $p->newPoint('titleAnchor5', $p->x(440)-$p->deltaX(340,440)/2, $p->y('titleAnchor2'), 'Title anchor panel 5');
        /* Grid anchor */
        $p->newPoint('gridAnchor5', $p->x('titleAnchor5'), $p->y(7), 'Grid anchor panel 5');
    }

     /**
     * Finalizes corset panel 5
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel5($model, $p)
    {
        $p->newPoint('grainlineTop', $p->x('titleAnchor5'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise')-10);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
    }

    /**
     * Drafts corset panel 6
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel6($model, $p)
    {
        /* Where does the left edge cut through top curve */
        $p->curveCrossesX(50,51,52,5,$p->x(402), '420-'); // Intersection in 420-1
        $p->addSplitCurve('421-','410-1','411-3','411-2',50,'420-1'); 
        /* Where does the right edge cut through top curve */
        $p->curveCrossesX(50,51,52,5,$p->x(501), '510-'); // Intersection in 420-1
        $p->addSplitCurve('421-','420-1','421-7','421-6',50,'510-1'); 
        $this->path6 = 'M 541 C 543 503 501 L 510-1 C 421-3 421-2 420-1 L 402 C 404 444 442 '. $this->path6;
        // We need to left edge and control point to keep the same height
        $deltaY = $p->deltaY('410-1','420-1');
        $p->addPoint('420-1', $p->shift('420-1',90,$deltaY));
        $p->addPoint('421-2', $p->shift('421-2',90,$deltaY));
        // Where does gap center cut through bottom curve 
        $p->curveCrossesX(30,32,41,40,$p->x(580), '590-'); // Intersection in 490-1
        $p->addSplitCurve('591-',40,'491-2','491-3','490-1','590-1'); 
        if($p->y('590-1') < $p->y(180)) { // Dart still open at edge
            $p->curveCrossesY(541,545,580,580,$p->y('590-1'), '592-'); // Intersection is in 592-1
            $p->curveCrossesY(542,546,580,580,$p->y('590-1'), '593-'); // Intersection is in 593-1
            $this->path6 .= 'C 591-6 591-7 592-1 C 592-1 545 541 z'; 
        } else { // dart is closed at edge. Easy! :) 
            $this->path6 .= 'C 591-6 591-7 590-1 L 580 C 580 543 541 z'; 
        } 
        /* Title anchor */
        $p->newPoint('titleAnchor6', $p->x(540)-$p->deltaX(440,540)/2, $p->y('titleAnchor2'), 'Title anchor panel 6');
        /* Grid anchor */
        $p->newPoint('gridAnchor6', $p->x('titleAnchor6'), $p->y(7), 'Grid anchor panel 6');
    }

    /**
     * Finalizes corset panel 6
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel6($model, $p)
    {
        $p->newPoint('grainlineTop', $p->x('titleAnchor6'), $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-$this->o('hipRise')-10);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
    }

    /**
     * Drafts corset panel 7
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function draftPanel7($model, $p)
    {
        /* Where does the left edge cut through top curve */
        $p->curveCrossesX(50,51,52,5,$p->x(502), '520-'); // Intersection in 520-1
        $p->addSplitCurve('521-','510-1','421-7','421-6',50,'520-1'); 
        // We need to left edge and control point to keep the same height
        $deltaY = $p->deltaY('510-1','520-1');
        $p->addPoint('520-1', $p->shift('520-1',90,$deltaY));
        $p->addPoint('521-7', $p->shift('521-7',90,$deltaY));
        $this->path7 = 'M 50 C 521-6 521-7 520-1 L 502 C 504 544 542 ';
        if($p->y('590-1') < $p->y(180)) { // Dart still open at edge
            $this->path7 .= 'C 546 593-1 593-1 '; 
        } else { // dart is closed at edge. Easy! :) 
            $this->path7 .= 'C 546 580 580 L 590-1 '; 
        } 
        $this->path7 .= 'C 591-3 591-2 40 z'; 
        /* Title anchor */
        $p->newPoint('titleAnchor7', $p->x(8)-$p->deltaX(540,8)/2, $p->y('titleAnchor2'), 'Title anchor panel 7');
        /* Grid anchor */
        $p->clonePoint(8, 'gridAnchor7');
    } 
        
    /**
     * Finalizes corset panel 7
     *
     * @param \Freesewing\Model $model The model to draft for
     * @param \Freesewing\Part $p The part to add info to
     *
     * @return void Nothing, adds into to part $p
     */
    protected function finalizePanel7($model, $p)
    {
        $p->newPoint('grainlineTop', $p->x(40)-10, $p->y(101)+10);
        $p->newPoint('grainlineBottom', $p->x('grainlineTop'), $p->y(2)-10);
        $p->newPath('grainline','M grainlineTop L grainlineBottom',['class' => 'grainline']);
        $p->newPoint('scaleboxAnchor', $p->x('titleAnchor7')-25, $p->y('titleAnchor7')-100);
        $p->newSnippet('scalebox', 'scalebox', 'scaleboxAnchor');
        // This part is narrow, better rotate that scalebox
        $p->snippets['scalebox']->setAttributes(['transform' => 'rotate( -90 '.$p->points['scaleboxAnchor']->getX().' '.$p->points['scaleboxAnchor']->getY().')']);
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

        /* Basic rectangle | Point index 1-> */
        $p->newPoint(   1, 0, 0, 'Underbust @ CF' );
        $p->newPoint(   2, 0, $model->m('naturalWaistToUnderbust') + $model->m('naturalWaistToHips'), 'Hips @ CF' );
        $p->newPoint(   3, $this->width, $p->y(2), 'Hips @ side' );
        $p->newPoint(   4, $p->x(3), 0, 'Underbust @ side' );
        $p->newPoint(   5, $p->x(3)/2, 0, 'Quarter top' );
        $p->newPoint(   6, $p->x(3)/2, $p->y(2), 'Quarter bottom' );
        $p->newPoint(   7, 0, $model->m('naturalWaistToUnderbust'), 'Waist @ CF' );
        $p->newPoint(   8, $p->x(3), $p->y(7), 'Waist @ side' );


        /* frontRise | Point index 10-> */
        $p->addPoint(   10, $p->shift( 1, 90, $this->o('frontRise'), 'Corset top edge @ CF'));
        $p->addPoint(   11, $p->shift(10, 0, $this->width * 0.11, 'Control point for 10'));
        $p->addPoint(   12, $p->shift( 1, 0, $this->width * 0.11, 'Control point fo 13'));
        $p->addPoint(   13, $p->shift( 1, 0, $this->width * 0.15, 'frontRise curve ends here'));
        
        /* frontDrop | Point index 20-> */
        $p->addPoint(   20, $p->shift( 2, -90, $this->o('frontDrop'), 'Corset bottom edge @ CF'));
        $p->addPoint(   21, $p->shift(20, 0, $this->width * 0.11, 'Control point for 10'));

        /* hipRise | Point index 30-> */
        $p->addPoint(   30, $p->shift( 6, 90, $this->o('hipRise'), 'Corset bottom edge @ CF'));
        $p->addPoint(   31, $p->shift(30, 180, $this->width * 0.3, 'Control point for 30'));
        $p->addPoint(   32, $p->shift(30,   0, $this->width * 0.2, 'Control point for 30'));

        /* backDrop | Point index 40-> */
        $p->addPoint(   40, $p->shift( 3, -90, $this->o('backDrop'), 'Corset bottom edge @ side'));
        $p->addPoint(   41, $p->shift(40, 180, $this->width * 0.3, 'Control point for 40'));

        /* backRise | Point index 50-> */
        $p->addPoint(   50, $p->shift( 4, 90, $this->o('backRise'), 'Corset top edge @ side'));
        $p->addPoint(   51, $p->shift(50, 0, $this->width * -0.4, 'Control point for 50'));
        $p->addPoint(   52, $p->shift(5, 0, $this->width * 0.2, 'Control point for 5'));

        $helpPath = 'M 1 L 2 L 3 L 4 z M 5 L 6 M 7 L 8';
        $p->newPath('help', $helpPath, ['class' => 'helpline']);

        $path = 'M 20 C 21 31 30 C 32 41 40 L 50 C 51 52 5 L 13 C 12 11 10 z';
        $p->newPath('outline', $path, ['class' => 'seamline']);

    }

}
