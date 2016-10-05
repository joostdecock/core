<?php

namespace Freesewing;

/**
 * Freesewing\Part class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Part
{
    public $points = array();
    public $snippets = array();
    public $texts = array();
    public $paths = array();
    public $transforms = array();
    public $title = null;
    public $boundary = array();
    public $render = true;
    public $maxOffsetTolerance = 5;

    private $steps = 100;

    public function setRender($bool)
    {
        $this->render = $bool;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function newPath($key, $pathString, $attributes=null)
    {
        $path = new \Freesewing\Path();
        $path->setPath($pathString);
        $path->setAttributes($attributes);
        $this->addPath($key, $path);
    }
    
    public function newPoint($key, $x, $y, $description=null)
    {
        $point = new \Freesewing\Point(); 
        $point->setX($x);
        $point->setY($y);
        $point->setDescription($description);
        $this->addPoint($key, $point);
    }
    
    public function newSnippet($key, $reference, $anchor, $attributes=null, $description=null)
    {
        $snippet = new \Freesewing\Snippet(); 
        $snippet->setReference($reference);
        $snippet->setAnchor($anchor);
        $snippet->setDescription($description);
        $snippet->setAttributes($attributes);
        $this->addSnippet($key, $snippet);
    }
    
    public function newText($key, $anchor, $msg, $attributes=null)
    {
        $text = new \Freesewing\Text();
        $text->setAnchor($anchor);
        $text->setText($msg);
        $text->setAttributes($attributes);
        $this->addText($key, $text);
    }
    
    public function createPoint($x, $y, $description=null)
    {
        $point = new \Freesewing\Point($key); 
        $point->setX($x);
        $point->setY($y);
        $point->setDescription($description);
        return $point;
    }

    public function addPoint($key, \Freesewing\Point $point, $description=null)
    {
        if($description !== null) $point->setDescription($description);
        $this->points[$key] = $point;
    }

    public function addSnippet($key, \Freesewing\Snippet $snippet)
    {
        $this->snippets[$key] = $snippet;
    }

    
    public function addText($key, \Freesewing\Text $text)
    {
        $this->texts[$key] = $text;
    }

    public function addPath($key, \Freesewing\Path $path)
    {
        $this->paths[$key] = $path;
    }

    public function addTransform($key, \Freesewing\Transform $transform)
    {
        $this->transforms[$key] = $transform;
    }

    public function addBoundary($margin=0)
    {
        if (count($this->paths) == 0) {
            return false;
        }
        foreach ($this->paths as $path) {
            $path->boundary = $path->findBoundary($this);
            if (!@is_object($topLeft)) {
                $topLeft = new \Freesewing\Point('topLeft');
                $topLeft->setX($path->boundary->topLeft->x);
                $topLeft->setY($path->boundary->topLeft->y);
                $bottomRight = new \Freesewing\Point('bottomRigh');
                $bottomRight->setX($path->boundary->bottomRight->x);
                $bottomRight->setY($path->boundary->bottomRight->y);
            } else {
                if ($path->boundary->topLeft->x < $topLeft->x) $topLeft->setX($path->boundary->topLeft->x);
                if ($path->boundary->topLeft->y < $topLeft->y) $topLeft->setY($path->boundary->topLeft->y);
                if ($path->boundary->bottomRight->x < $bottomRight->x) $bottomRight->setX($path->boundary->bottomRight->x);
                if ($path->boundary->bottomRight->y < $bottomRight->y) $bottomRight->setY($path->boundary->bottomRight->y);
            }
        }
        $topLeft->setX($topLeft->getX() - $margin);
        $topLeft->setY($topLeft->getY() - $margin);
        $bottomRight->setX($bottomRight->getX() + $margin);
        $bottomRight->setY($bottomRight->getY() + $margin);
        $this->boundary = new \Freesewing\Boundary();
        $this->boundary->setTopLeft($topLeft);
        $this->boundary->setBottomRight($bottomRight);
    }

    public function offsetPath($newKey, $srcKey, $distance=10)
    {
        $path = $this->paths[$srcKey];
       if ($this->findPathDirection($srcKey) == 'ccw') $distance *= -1; 

        $stack = $this->pathOffsetAsStack($path, $distance);
        $this->fillPathStackGaps($stack);
        $this->pathStackToPath($newKey, $stack);
        $this->purgePoints('.po');
    }

    private function purgePoints($prefix=false)
    {
        if($prefix !== false) {
            $len = strlen($prefix);
            foreach($this->points as $key => $point) {
                if(substr($key,0, $len) == $prefix) unset($this->points[$key]);
            }
        }
    }
    private function cloneOffsetPoint($key, &$i) 
    {
        $newKey = $this->newId('sa');
        $this->clonePoint($key, $newKey);
        return $newKey;
    }

    private function pathStackToPath($key, $stack)
    {
        $chunks = count($stack->items);
        $count = 1;
        $i=1;
        foreach($stack->items as $chunk) {
            ob_flush();
            if($count == 1) {
                if($chunk['type'] == 'line') {
                    $path = 'M '.
                        $this->cloneOffsetPoint($chunk['offset'][0]).
                        ' L '.
                        $this->cloneOffsetPoint($chunk['offset'][1]);
                }
                else if($chunk['type'] == 'curve') {
                    $path = 'M '.
                        $this->cloneOffsetPoint($chunk['offset'][0]).
                        ' C '.
                        $this->cloneOffsetPoint($chunk['offset'][1]).
                        ' '.
                        $this->cloneOffsetPoint($chunk['offset'][2]).
                        ' '.
                        $this->cloneOffsetPoint($chunk['offset'][3]);
                }
            } else if($count == $chunks-1) {
                if($chunk['type'] == 'line') {
                    $path .= ' L '.
                        $this->cloneOffsetPoint($chunk['offset'][1]).
                        ' z';
                }
                else if($chunk['type'] == 'curve') {
                    $path .= ' C '.
                        $this->cloneOffsetPoint($chunk['offset'][1]).
                        ' '.
                        $this->cloneOffsetPoint($chunk['offset'][2]).
                        ' '.
                        $this->cloneOffsetPoint($chunk['offset'][3]).
                        ' z';
                }
            } else {
                if($chunk['type'] == 'line') {
                    $path .= ' L ' . $this->cloneOffsetPoint($chunk['offset'][0]);
                }
                else if($chunk['type'] == 'curve') {
                    $path .= ' C '.
                        $this->cloneOffsetPoint($chunk['offset'][1]).
                        ' '.
                        $this->cloneOffsetPoint($chunk['offset'][2]).
                        ' '.
                        $this->cloneOffsetPoint($chunk['offset'][3]);
                }
            }
            $count++;
        }
        $this->newPath($key, $path, ['class' => 'sa']);
    }

    public function newId($prefix='-i')
    {
        if(isset($this->tmp['id'][$prefix])) {
            $this->tmp['id'][$prefix]++;
        } else $this->tmp['id'][$prefix] = 1;

        return $prefix.$this->tmp['id'][$prefix];
    }

    private function fillPathStackGaps($stack)
    {
        $chunks = count($stack->items);
        $count = 1;
        $array = $stack->items;
        foreach($array as $chunk) {
            $id = $this->newId('.po');
            if($count == $chunks) $next = $array[0];
            else $next = $array[$count];
            if($chunk['type'] == 'line' && $next['type'] == 'line') {
                if($this->distance($chunk['offset'][1], $next['offset'][0]) > 0.25) {
                    $this->addPoint( $id , $this->linesCross($chunk['offset'][0], $chunk['offset'][1],  $next['offset'][0],  $next['offset'][1]) );
                    $pathString = 'M '.$chunk['offset'][1]." L $id L ". $next['offset'][0];
                    $new[] = $chunk;
                    $new[] = ['type' => 'line', 'offset' => [$chunk['offset'][1], $id]];
                    $new[] = ['type' => 'line', 'offset' => [$id, $next['offset'][0]]];
                    $stack->replace($chunk, $new);
                }
            } 
            else if($chunk['type'] == 'line' && $next['type'] == 'curve') {
                if($this->distance($chunk['offset'][1], $next['offset'][0]) > 0.25) {
                    $this->addPoint( '-po_helper' , $this->shiftAlong($next['offset'][0], $next['offset'][1],  $next['offset'][2],  $next['offset'][3], 0.5) );
                    $this->addPoint( $id , $this->linesCross($chunk['offset'][0], $chunk['offset'][1],  '-po_helper',  $next['offset'][0]) );
                    $pathString = 'M '.$chunk['offset'][1]." L $id L ". $next['offset'][0]; 
                    $new[] = $chunk;
                    $new[] = ['type' => 'line', 'offset' => [$chunk['offset'][1], $id]];
                    $new[] = ['type' => 'line', 'offset' => [$id, $next['offset'][0]]];
                    $stack->replace($chunk, $new);
                }
            } 
            else if($chunk['type'] == 'curve' && $next['type'] == 'line') {
                if($this->distance($chunk['offset'][1], $next['offset'][0]) > 0.25) {
                    $this->addPoint( '-po_helper' , $this->shiftAlong($chunk['offset'][3], $chunk['offset'][2],  $chunk['offset'][1],  $chunk['offset'][0], 0.5) );
                    $this->addPoint( $id , $this->linesCross($chunk['offset'][3], '-po_helper', $next['offset'][0], $next['offset'][1]) );
                    $pathString = 'M '.$chunk['offset'][3]." L $id L ". $next['offset'][0]; 
                    $new[] = $chunk;
                    $new[] = ['type' => 'line', 'offset' => [$chunk['offset'][3], $id]];
                    $new[] = ['type' => 'line', 'offset' => [$id, $next['offset'][0]]];
                    $stack->replace($chunk, $new);
                }
            } 
            else if($chunk['type'] == 'curve' && $next['type'] == 'curve') {
                if($this->distance($chunk['offset'][1], $next['offset'][0]) > 0.25) {
                    $pathString = 'M '.$chunk['offset'][3].' L '. $next['offset'][0]; 
                    
                    $new[] = $chunk;
                    $new[] = ['type' => 'line', 'offset' => [$chunk['offset'][3], $next['offset'][0]]];
                    $stack->replace($chunk, $new);
                     
                }
            }
            $count++;
            unset($new);
        }
       return $stack; 
    }

    private function pathOffsetAsStack($path, $distance)
    {
        $stack = new \Freesewing\Stack();
        foreach($path->breakUp() as &$chunk) {
            if($chunk['type'] == 'L') $stack->push($this->offsetLine($chunk['path'], $distance));
            if($chunk['type'] == 'C') $stack->push($this->offsetCurve($chunk['path'], $distance));
        }
        return $stack;
    }
    
    private function offsetLine($line, $distance)
    {
        $points = Utils::asScrubbedArray($line); 
        $from = $points[1];
        $to = $points[3];
        $offset = $this->getLineOffsetPoints($from, $to, $distance);
        
        $fromId = $this->newId('.po');
        $toId = $this->newId('.po');
        $pathId = $this->newId('.po');

        $this->addPoint($fromId, $offset[0]);
        $this->addPoint($toId, $offset[1]);
        return [ 0 => ['type' => 'line', 'key' => $pathId, 'original' => [$from, $to], 'offset' => [$fromId, $toId]]];
    }

    private function getLineOffsetPoints($from, $to, $distance)
    {
        $angle = $this->angle($from, $to) - 90;
        return [
            $this->shift($from, $angle, $distance),
            $this->shift($to, $angle, $distance),
        ];
    }

    private function getCurveOffsetPoints($from, $cp1, $cp2, $to, $distance)
    {
        if ($from == $cp1) $halfA = $this->getNonCubicCurveOffsetPoints('cp1', $from, $cp1, $cp2, $to, $distance);
        else $halfA = $this->getLineOffsetPoints($from, $cp1, $distance);
        if ($cp2 == $to) $halfB = $this->getNonCubicCurveOffsetPoints('cp2', $from, $cp1, $cp2, $to, $distance);
        else $halfB = $this->getLineOffsetPoints($cp2, $to, $distance);
        return [
            $halfA[0],
            $halfA[1],
            $halfB[0],
            $halfB[1],
        ];
    }
    
    private function getNonCubicCurveOffsetPoints($missing, $from, $cp1, $cp2, $to, $distance)
    {
        if($missing == 'cp1') {
            $almostTheSamePoint = $this->addPoint('-shifthelper', $this->shiftAlong($from, $cp1, $cp2, $to, 0.5));
            $angle = $this->angle($from, '-shifthelper') -90 ;
            $p = $from;
        }
        else {
            $almostTheSamePoint = $this->addPoint('-shifthelper', $this->shiftAlong($to, $cp2, $cp1, $from, 0.5));
            $angle = $this->angle('-shifthelper', $to) - 90;
            $p = $to;
        }
        
        $offset = $this->shift($p, $angle, $distance);
        return [ $offset, $offset ];
    }

    private function offsetCurve($curve, $distance)
    {
        $points = Utils::asScrubbedArray($curve); 
        $from = $points[1];
        $cp1 = $points[3];
        $cp2 = $points[4];
        $to = $points[5];
        
        $offset = $this->getCurveOffsetPoints($from, $cp1, $cp2, $to, $distance);
        $fromId = $this->newId('.po');
        $cp1Id  = $this->newId('.po'); 
        $cp2Id  = $this->newId('.po'); 
        $toId   = $this->newId('.po');
        $pathId = $this->newId('.po'); 

        $this->addPoint($fromId, $offset[0]);
        $this->addPoint($cp1Id, $offset[1]);
        $this->addPoint($cp2Id, $offset[2]);
        $this->addPoint($toId, $offset[3]);
        $pathString = "M $fromId C $cp1Id $cp2Id $toId";
        
        $chunks[] = ['type' => 'curve', 'key' => $pathId, 'original' => [$from, $cp1, $cp2, $to], 'offset' => [$fromId, $cp1Id, $cp2Id, $toId]];
        $tolerance = $this->offsetTolerance($chunks[0], $distance);
        if($tolerance['score'] > $this->maxOffsetTolerance) {
            $splitId = $this->newId('.po');
            $this->addSplitCurve($splitId.'-', $from, $cp1, $cp2, $to, $tolerance['index'],1);
            unset($chunks);
            $subDivide = $this->offsetCurve("M $splitId-1 C $splitId-2 $splitId-3 $splitId-4", $distance);
            foreach($subDivide as $chunk) $chunks[] = $chunk;
            $subDivide = $this->offsetCurve("M $splitId-8 C $splitId-7 $splitId-6 $splitId-5", $distance);
            foreach($subDivide as $chunk) $chunks[] = $chunk;
        }
        return $chunks;
    }

   
    
    private function offsetTolerance($entry, $distance)
    {
        $origFrom = $this->loadPoint($entry['original'][0]);
        $origCp1 = $this->loadPoint($entry['original'][1]);
        $origCp2 = $this->loadPoint($entry['original'][2]);
        $origTo = $this->loadPoint($entry['original'][3]);

        $offsetFrom = $this->loadPoint($entry['offset'][0]);
        $offsetCp1 = $this->loadPoint($entry['offset'][1]);
        $offsetCp2 = $this->loadPoint($entry['offset'][2]);
        $offsetTo = $this->loadPoint($entry['offset'][3]);

        $this->newPoint('-po_orig', 0, 0);
        $this->newPoint('-po_offset', 0, 0);

        $worstDelta = 0;
        for ($i = 0; $i < 100; $i++) {
            $t = $i / 100;
            $xOrig = $this->bezierPoint($t, $origFrom->getX(), $origCp1->getX(), $origCp2->getX(), $origTo->getX());
            $yOrig = $this->bezierPoint($t, $origFrom->getY(), $origCp1->getY(), $origCp2->getY(), $origTo->getY());
            $xOffset = $this->bezierPoint($t, $offsetFrom->getX(), $offsetCp1->getX(), $offsetCp2->getX(), $offsetTo->getX());
            $yOffset = $this->bezierPoint($t, $offsetFrom->getY(), $offsetCp1->getY(), $offsetCp2->getY(), $offsetTo->getY());
            
            $this->points['-po_orig']->setX($xOrig);
            $this->points['-po_orig']->setY($yOrig);
            $this->points['-po_offset']->setX($xOffset);
            $this->points['-po_offset']->setY($yOffset);
           
            $offset = $this->distance('-po_orig', '-po_offset');
            $delta = abs($offset/(abs($distance)/100) -100);
            if($delta>$worstDelta) {
                $worstDelta = round($delta,2);
                $worstIndex = $t;
            }
        }
        return ['score' => $worstDelta, 'index' => $worstIndex];
    }
    
    private function findPathDirection($key)
    {
        $path = $this->paths[$key];
        $pathArray = Utils::asScrubbedArray($this->straightenCurves($path->getPath()));
        $first = true; 
        $second = true; 
        $angle = 0;
        
        foreach ($pathArray as $key => $p) {
            $p = rtrim($p);
            if ($p != '' && $path->isAllowedPathCommand($p)) {
                $command = $p;
                if(strtolower($command) == 'z') {
                    $absoluteAngle = $this->angle($previousPoint, $firstPoint);
                    $relativeAngle = $absoluteAngle - $previousAbsoluteAngle;
                    if($relativeAngle > 180) $relativeAngle = -360+$relativeAngle;
                    if($relativeAngle < -180) $relativeAngle = 360+$relativeAngle;
                    $angle += $relativeAngle + ($startingAngle - $absoluteAngle);
                }
            } 
            else {
                if($first) {
                    $firstPoint = $p;
                    $first = false; 
                }
                if($command == 'L') {
                    $absoluteAngle = $this->angle($previousPoint, $p);
                    $relativeAngle = $absoluteAngle - $previousAbsoluteAngle;
                    if($relativeAngle > 180) $relativeAngle = -360+$relativeAngle;
                    if($relativeAngle < -180) $relativeAngle = 360+$relativeAngle;
                    if($second) $startingAngle = $absoluteAngle;
                    if(!$second) $angle += $relativeAngle;
                    $second = false;
                }
                $previousPoint = $p;
            }
            if(@isset($absoluteAngle)) $previousAbsoluteAngle = $absoluteAngle;
        }
        if($angle<0) return 'cw';
        else return 'ccw';
    }

    private function straightenCurves($pathString)
    {
        $pathArray = Utils::asScrubbedArray($pathString, 'C');
        if(count($pathArray) == 1) return $pathString; // No curves
        $path = '';
        $first = true;
        foreach ($pathArray as $key => $chunk) {
            $chunk = rtrim($chunk);
            if($first) { // Start is always a move
                $path .= $chunk;
                $first = false;
            } 
            else {
                $chunkArray = Utils::asScrubbedArray($chunk);
                $path .= ' L '.array_shift($chunkArray).' L '.array_shift($chunkArray).' L '.array_shift($chunkArray).' ';
                $path .= implode(' ',$chunkArray);
            }
        }

        return $path;
    }

    private function stripEmptyArrayItems($array)
    {
        foreach($array as $key => $value) {
            if(rtrim($value) != '') $return[$key] = $value;
        }
        return $return;
    }

    /* 
     * macros start here 
     *
     **/

    private function clonePoint($sourceKey, $targetKey)
    {
        $this->points[$targetKey] = $this->points[$sourceKey];
    }

    private function loadPoint($key)
    {
        return $this->points[$key];
    }

    /**
     * X-coordinate of a point
     **/
    public function x($key)
    {
        return $this->points[$key]->getX();
    }

    /**
     * Y-coordinate of a point
     **/
    public function y($key)
    {
        return $this->points[$key]->getY();
    }

    /**
     * Distance between two points
     **/
    public function distance($key1, $key2)
    {
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2);
        $deltaX = $point1->getX() - $point2->getX();
        $deltaY = $point1->getY() - $point2->getY();
        return sqrt( pow($deltaX,2) + pow($deltaY,2) );
    }

    /**
     * Distance between two points along X-axis
     **/
    public function deltaX($key1, $key2)
    {
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2);
        return $point2->getX() - $point1->getX();
    }

    /**
     * Distance between two points along Y-axis
     **/
    public function deltaY($key1, $key2)
    {
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2);
        return $point2->getY() - $point1->getY();
    }

    /**
     * Rotate point 1 around point 2
     **/
    public function rotate($key1, $key2, $rotation)
    {
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2);
        $radius = $this->distance($key1, $key2);
        $angle = $this->angle($key1, $key2);

        $x = $point2->getX() + $radius * cos( deg2rad( $angle + $rotation ) );
        $y = $point2->getY() + $radius * sin( deg2rad( $angle + $rotation ) ) * -1;
        
        return $this->createPoint($x, $y);
    }

    /**
     * Angle between two points
     **/
    public function angle($key1, $key2)
    {
        $distance = $this->distance($key1, $key2);
        $deltaX = $this->deltaX($key1, $key2);
        $deltaY = $this->deltaY($key1, $key2);
        
        if ($deltaX == 0 && $deltaY == 0) $angle = 0;
        elseif ($deltaX == 0 && $deltaY>0) $angle = 90;
        elseif ($deltaX == 0 && $deltaY<0) $angle = 270;
        elseif ($deltaY == 0 && $deltaX>0) $angle = 180;
        elseif ($deltaY == 0 && $deltaX<0) $angle = 0;
        else {
          if ($deltaY>0)     $angle = 180 - rad2deg( acos( $deltaX / $distance ) );
          elseif ($deltaY<0) $angle = 180 + rad2deg( acos( $deltaX / $distance ) );
        }
        
        return $angle;
    }

    /**
     * Length of a cubic bezier curve
     **/
    public function curveLen($keyStart, $keyControl1, $keyControl2, $keyEnd) {
      return $this->cubicBezierLength($keyStart, $keyControl1, $keyControl2, $keyEnd);
    }
    
    /** 
     Approximate length of the Bezier curve which starts at "start" and
     ends at "end" and is defined by control points "cp1" on the start
     side and "cp2" on the end side.
     Seems there is no closed form integral for this.
     Whatever that means :)
    */	
    private function cubicBezierLength($keyStart, $keyControl1, $keyControl2, $keyEnd) {
      $start = $this->loadPoint($keyStart);
      $cp1 = $this->loadPoint($keyControl1);
      $cp2 = $this->loadPoint($keyControl2);
      $end = $this->loadPoint($keyEnd);
      $length = 0; 

      for ($i = 0; $i <= $this->steps; $i++) {
        $t = $i / $this->steps;
        $x = $this->bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
        $y = $this->bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
        if ($i > 0) {
          $deltaX = $x - $previousX;
          $deltaY = $y - $previousY;
          $length += sqrt (pow($deltaX,2) + pow($deltaY,2));
        }
        $previousX = $x;
        $previousY = $y;
      }
      return $length;
    }
    
    private function bezierPoint($t, $startval, $cp1val, $cp2val, $endval) {
      # See http://en.wikipedia.org/wiki/B%C3%A9zier_curve#Cubic_B.C3.A9zier_curves	
      return $startval * (1.0 - $t) * (1.0 - $t)  * (1.0 - $t)
        + 3.0 *  $cp1val * (1.0 - $t) * (1.0 - $t)  * $t
        + 3.0 *  $cp2val * (1.0 - $t) * $t          * $t
        +        $endval * $t         * $t          * $t;
    }

    /**
     * Shift point along a line
     **/
    public function shiftTowards($key1, $key2, $distance)
    {
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2);
        $angle = $this->angle($key1, $key2);
        // cos is x axis, sin is y axis
        $deltaX = $distance * abs( cos( deg2rad( $angle )));
        $deltaY = $distance * abs( sin( deg2rad( $angle )));
        if ($point1->getX() < $point2->getX() && $point1->getY() > $point2->getY()) {
          $x = $point1->getX() + abs($deltaX);
          $y = $point1->getY() - abs($deltaY);
        }
        elseif ($point1->getX() < $point2->getX() && $point1->getY() < $point2->getY()) { 
          $x = $point1->getX() + abs($deltaX);
          $y = $point1->getY() + abs($deltaY);
        }
        elseif ($point1->getX() > $point2->getX() && $point1->getY() > $point2->getY()) { 
          $x = $point1->getX() - abs($deltaX);
          $y = $point1->getY() - abs($deltaY);
        }
        elseif ($point1->getX() > $point2->getX() && $point1->getY() < $point2->getY()) { 
          $x = $point1->getX() - abs($deltaX);
          $y = $point1->getY() + abs($deltaY);
        }
        else { // FIXME: Other cases?
          $x = $point1->getX() + $deltaX;
          $y = $point1->getY() + $deltaY;
        }
  
        return $this->createPoint($x, $y, "Point $key1 shifted towards $key2 by $distance");
    }

    /**
     * Shift point along a line
     * Shift, or move, a distance $s along a bezier curves from $start to $end
     * via control points $cp1 and $cp2.
     * Note that this is approximate.
     **/
    public function shiftAlong($keyStart, $keyControl1, $keyControl2, $keyEnd, $distance)
    {
        $length = 0;
        $start = $this->loadPoint($keyStart);
        $cp1 = $this->loadPoint($keyControl1);
        $cp2 = $this->loadPoint($keyControl2);
        $end = $this->loadPoint($keyEnd);

        for ($i = 0; $i <= $this->steps; $i++) {
            $t = $i / $this->steps;
            $x = $this->bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = $this->bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            if ($i > 0) {
                $deltaX = $x - $previousX;
                $deltaY = $y - $previousY;
                $length += sqrt (pow($deltaX,2) + pow($deltaY,2));
                if($length > $distance) {
                    return $this->createPoint($x, $y, "Point shifted $distance along curve $keyStart $keyControl1 $keyControl2 $keyEnd");
                }
            }
            $previousX = $x;
            $previousY = $y;
        }
        /* 
         * We only arrive here if the curve is shorter than the requested offset. 
         **/
        throw new \InvalidArgumentException('Ran out of curve to move along');
    }

    // Get the slope of a straight line, given two points on it
    private function getSlope($key1,$key2) 
    {
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2); 
    
        return ( $point2->getY() - $point1->getY() )  /  ( $point2->getX() - $point1->getX() );
    }
    
    // Find the intersection point between two lines
    public function linesCross($key1, $key2, $key3, $key4)
    { 
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2); 
        $point3 = $this->loadPoint($key3);
        $point4 = $this->loadPoint($key4); 
        
        // If line is vertical, handle this special case
        if($point1->getX() == $point2->getX()) { 
            $slope = $this->getSlope($key3, $key4);
            $i = $point3->getY()  -  ( $slope * $point3->getX() );
            $x = $point1->getX();
            $y =  $slope * $x + $i;
        }
        elseif( $point3->getX() == $point4->getX() ) { 
            $slope = $this->getSlope($key1, $key2);
            $i = $point1->getY()  -  ( $slope * $point1->getX() );
            $x = $point3->getX();
            $y =  $slope * $x + $i;
        }
        else {
            // If line goes from right to left, swap points
            if ( $point1->getX() > $point2->getX() ) { $tmp=$key1; $key1=$key2; $key2=$tmp; }
            if ( $point3->getX() > $point4->getX() ) { $tmp=$key3; $key3=$key4; $key4=$tmp; }
            // Find slope
            $slope1 = $this->getSlope($key1, $key2);
            $slope2 = $this->getSlope($key3, $key4);
            // Find y intercept
            $i1 = $point1->getY() - ( $slope1 * $point1->getX() );
            $i2 = $point3->getY() - ( $slope2 * $point3->getX() );
            // Find intersection
            $x = ( $i2 - $i1 ) / ( $slope1 - $slope2 );
            $y =  $slope1 * $x + $i1;
        }
        
        return $this->createPoint($x, $y, "Intersection of $key1,$key2 and $key3,$key4");
    }

    /**
     * Return true if point is defined
     */
    public function isPoint($key)
    {
        $point = $this->loadPoint($key);
        if( $point instanceof \Freesewing\Point) return true;
        else return false;
    }

    /**
     * Takes a point (id) and mirrors it's X value around a point on the X axis
     */
    function flipX($key, $anchorX=0) {
        $point = $this->loadPoint($key);
        $deltaX = $anchorX - $point->getX();
        $x = $anchorX + $deltaX;
        
        return $this->createPoint($x, $point->getY(), "Point $key flipped around X coordinate $anchorX");
    }

    /**
     * Takes a point (id) and mirrors it's Y value around a point on the Y axis
     */
    function flipY($key, $anchorY=0) {
        $point = $this->loadPoint($key);
        $deltaY = $anchorY - $point->getY();
        $y = $anchorY + $deltaY;
        
        return $this->createPoint($x, $point->getY(), "Point $key flipped around Y coordinate $anchorY");
    }

    // Find intersection of bezier curve with a certain X value
    // Note that this is approximate.
    public function curveCrossesX($keyStart, $keyControl1, $keyControl2, $keyEnd, $targetX) {
        $start = $this->loadPoint($keyStart);
        $cp1 = $this->loadPoint($keyControl1);
        $cp2 = $this->loadPoint($keyControl2);
        $end = $this->loadPoint($keyEnd);
        $bestDistance = 1000000000;
        $newPoint = new \Freesewing\Point();
        $newPoint->setDescription("Crossing of arc $keyStart, $keyControl1, $keyControl2, $keyEnd through X coordinate $targetX");
        for ($i = 0; $i <= $this->steps; $i++) {
            $t = $i / $this->steps;
            $x = $this->bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = $this->bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            $distance = abs($targetX - $x);
            if($distance < $bestDistance) {
                $newPoint->setX($x);
                $newPoint->setY($y);
                $bestDistance = $distance;
            }
        }
        return $newPoint;
    }

    // Find intersection of bezier curve with a certain Y value
    // Note that this is approximate.
    public function curveCrossesY($keyStart, $keyControl1, $keyControl2, $keyEnd, $targetY) 
    {
        $start = $this->loadPoint($keyStart);
        $cp1 = $this->loadPoint($keyControl1);
        $cp2 = $this->loadPoint($keyControl2);
        $end = $this->loadPoint($keyEnd);
        $bestDistance = 1000000000;
        $newPoint = new \Freesewing\Point();
        $newPoint->setDescription("Crossing of arc $keyStart, $keyControl1, $keyControl2, $keyEnd through X coordinate $targetY");
        for ($i = 0; $i <= $this->steps; $i++) {
            $t = $i / $this->steps;
            $x = $this->bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = $this->bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            $distance = abs($targetY - $y);
            if($distance < $bestDistance) {
                $newPoint->setX($x);
                $newPoint->setY($y);
                $bestDistance = $distance;
            }
        }
        return $newPoint;
    }

    public function shift($key, $angle, $distance) // FIXME, left it here
    {
        $point = $this->loadPoint($key);
        $newPoint = new \Freesewing\Point();
        $newPoint->setX($point->getX() + $distance);
        $newPoint->setY($point->getY());
        $this->addPoint('-shiftHelper', $newPoint);
    
        return $this->rotate('-shiftHelper', $key, $angle);
    }
    
    public function arcCrossLine($keyStart, $keyControl1, $keyControl2, $keyEnd, $key1, $key2) 
    {
        $start = $this->loadPoint($start);
        $cp1 = $this->loadPoint($cp1);
        $cp2 = $this->loadPoint($cp2);
        $end = $this->loadPoint($end);
        $l1 = $this->loadPoint($l1);
        $l2 = $this->loadPoint($l2);
        return computeIntersections($keyStart, $keyControl1, $keyControl2, $keyEnd, $key1, $key2);
    }

    /*computes intersection between a cubic spline and a line segment*/
    private function computeArcLineIntersections($keyStart, $keyControl1, $keyControl2, $keyEnd, $key1, $key2) {
        $start = $this->loadPoint($keyStart);
        $cp1 = $this->loadPoint($keyControl1);
        $cp2 = $this->loadPoint($keyControl2);
        $end = $this->loadPoint($keyEnd);
        $point1 = $this->loadPoint($key1);
        $point2 = $this->loadPoint($key2);

        $A = $point2->getY() - $point1->getY(); // A=y2-y1
        $B = $point1->getX() - $point2->getX(); // B=x1-x2
        $C = $point1->getX() * ($point1->getY() - $point2->getY()) + $point1->getY() * ($point2->getX() - $point1->getX()); // C=x1*(y1-y2)+y1*(x2-x1)
    
        $bx = bezierCoeffs($start->getX(),$cp1->getX(),$cp2->getX(),$end->getX());
        $by = bezierCoeffs($start->getY(),$cp1->getY(),$cp2->getY(),$end->getY());
    
        $P[0] = $A*$bx[0]+$B*$by[0];         /*t^3*/
        $P[1] = $A*$bx[1]+$B*$by[1];         /*t^2*/
        $P[2] = $A*$bx[2]+$B*$by[2];         /*t*/
        $P[3] = $A*$bx[3]+$B*$by[3] + $C;     /*1*/
    
        $r=cubicRoots($P);
    
        /*verify the roots are in bounds of the linear segment*/
        for ($i=0;$i<3;$i++) {
            $t=$r[$i];
    
            $X[0]=$bx[0]*$t*$t*$t+$bx[1]*$t*$t+$bx[2]*$t+$bx[3];
            $X[1]=$by[0]*$t*$t*$t+$by[1]*$t*$t+$by[2]*$t+$by[3];
    
            /* above is intersection point assuming infinitely long line segment,
            make sure we are also in bounds of the line*/
            if (($lx[1]-$lx[0])!=0)           /*if not vertical line*/
                $s=($X[0]-$lx[0])/($lx[1]-$lx[0]);
            else
                $s=($X[1]-$ly[0])/($ly[1]-$ly[0]);
    
            /*in bounds?*/
            if ($t<0 || $t>1.0 || $s<0 || $s>1.0) {
                $X[0]=-100;  // move off screen
                $X[1]=-100;
            }
            else  $I[$i]= $X;
      }
      return $I;
    }

    private function bezierCoeffs($P0,$P1,$P2,$P3) 
    {
        $Z[0] = -1*$P0 + 3*$P1 + -3*$P2 + $P3;
        $Z[1] = 3*$P0 - 6*$P1 + 3*$P2;
        $Z[2] = -3*$P0 + 3*$P1;
        $Z[3] = $P0;
        return $Z;
    }

    // sign of number
    private function sgn($x) 
    {
        if ($x < 0.0) return -1;
        return 1;
    }
    
    function cubicRoots($P) {
        $a=$P[0];
        $b=$P[1];
        $c=$P[2];
        $d=$P[3];

        $A=$b/$a;
        $B=$c/$a;
        $C=$d/$a;

        $Q = (3*$B - pow($A,2))/9;
        $R = (9*$A*$B - 27*$C - 2*pow($A,3))/54;
        $D = pow($Q,3) + pow($R,2);    // polynomial discriminant

        if ($D >= 0) { // complex or duplicate roots
          $S = sgn($R + sqrt($D)) * pow(abs($R + sqrt($D)),1/3);
          $T = sgn($R - sqrt($D)) * pow(abs($R - sqrt($D)),1/3);
 
          $t[0] = -1*$A/3 + ($S + $T);    // real root
          $t[1] = -1*$A/3 - ($S + $T)/2;  // real part of complex root
          $t[2] = -1*$A/3 - ($S + $T)/2;  // real part of complex root
          $Im = abs(sqrt(3)*($S - $T)/2); // complex part of root pair
 
          /*discard complex roots*/
          if ($Im!=0) {
            $t[1]=-1;
            $t[2]=-1;
          }

        } else { // distinct real roots
          $th = acos($R/sqrt(pow($Q,3)*-1));

          $t[0] = 2*sqrt(-1*$Q)*cos($th/3) - $A/3;
          $t[1] = 2*sqrt(-1*$Q)*cos(($th + 2*pi())/3) - $A/3;
          $t[2] = 2*sqrt(-$Q)*cos(($th + 4*pi())/3) - $A/3;
          $Im = 0.0;
        }

        /*discard out of spec roots*/
        for ($i=0;$i<3;$i++)
          if ($t[$i]<0 || $t[$i]>1.0) $t[$i]=-1;

        /*sort but place -1 at the end*/
        $t=sortSpecial($t);
        return $t;
    }

    private function sortSpecial($a) {
        $flip;
        $temp;
        do {
            $flip=false;
            for ($i=0;$i<count($a)-1;$i++) {
                if (($a[$i+1]>=0 && $a[$i]>$a[$i+1]) || ($a[$i]<0 && $a[$i+1]>=0)) {
                    $flip=true;
                    $temp=$a[$i];
                    $a[$i]=$a[$i+1];
                    $a[$i+1]=$temp;
                }
            }
        } while ($flip);
        return $a;
    }

    private function cubicBezierDelta($from, $cp1, $cp2, $to, $split) {
        $from = $this->loadPoint($from);
        $cp1 = $this->loadPoint($cp1);
        $cp2 = $this->loadPoint($cp2);
        $to = $this->loadPoint($to);
        $split = $this->loadPoint($split);
        /* 
          Approximate delta (between 0 and 1) of a point 'split' on 
         a Bezier curve which starts at "start" and
         ends at "end" and is defined by control points "cp1" on the start
         side and "cp2" on the end side.
         Seems there is no closed form integral for this.
         Whatever that means :)
        */	
        $best_t = NULL;
        $best_distance = 100000000;
        for ($i = 0; $i <= $this->steps; $i++) {
          $t = $i / $this->steps;
          $x = $this->bezierPoint($t, $from->getX(), $cp1->getX(), $cp2->getX(), $to->getX());
          $y = $this->bezierPoint($t, $from->getY(), $cp1->getY(), $cp2->getY(), $to->getY());
          $distance = hopLen($split, array($x,$y));
          if($distance<$best_distance) {
            $best_t = $t;
            $best_distance = $distance;
          }
        }
        return $best_t;
    }

    public function addSplitCurve($prefix, $from,$cp1,$cp2,$to,$split,$splitOnDelta=false) {
        $points = $this->splitCurve($from,$cp1,$cp2,$to,$split,$splitOnDelta);
        $this->addPoint($prefix.'1', $points[0]);
        $this->addPoint($prefix.'2', $points[1]);
        $this->addPoint($prefix.'3', $points[2]);
        $this->addPoint($prefix.'4', $points[3]);
        $this->addPoint($prefix.'5', $points[4]);
        $this->addPoint($prefix.'6', $points[5]);
        $this->addPoint($prefix.'7', $points[6]);
        $this->addPoint($prefix.'8', $points[7]);
    }

    public function splitCurve($from,$cp1,$cp2,$to,$split,$splitOnDelta=false) {
        if($splitOnDelta) $t = $split;
        else $t = cubicBezierDelta($from,$cp1,$cp2,$to,$split);

        $curve1 = $this->calculateSplitCurvePoints($from,$cp1,$cp2,$to,$t);
        $t = 1-$t;
        $curve2 = $this->calculateSplitCurvePoints($to,$cp2,$cp1,$from,$t);

        return [
            $curve1[0],
            $curve1[1],
            $curve1[2],
            $curve1[3],
            $curve2[0],
            $curve2[1],
            $curve2[2],
            $curve2[3],
        ];
    }

    private function calculateSplitCurvePoints($from,$cp1,$cp2,$to,$t)
    {
        $from = $this->loadPoint($from);
        $cp1 = $this->loadPoint($cp1);
        $cp2 = $this->loadPoint($cp2);
        $to = $this->loadPoint($to);
    
        $x1 = $from->getX();
        $y1 = $from->getY();
        $x2 = $cp1->getX();
        $y2 = $cp1->getY();
        $x3 = $cp2->getX();
        $y3 = $cp2->getY();
        $x4 = $to->getX();
        $y4 = $to->getY();
    
        $x12 = ($x2-$x1)*$t+$x1;
        $y12 = ($y2-$y1)*$t+$y1;
    
        $x23 = ($x3-$x2)*$t+$x2;
        $y23 = ($y3-$y2)*$t+$y2;
    
        $x34 = ($x4-$x3)*$t+$x3;
        $y34 = ($y4-$y3)*$t+$y3;
    
        $x123 = ($x23-$x12)*$t+$x12;
        $y123 = ($y23-$y12)*$t+$y12;
    
        $x234 = ($x34-$x23)*$t+$x23;
        $y234 = ($y34-$y23)*$t+$y23;
    
        $x1234 = ($x234-$x123)*$t+$x123;
        $y1234 = ($y234-$y123)*$t+$y123;

        $cp1 = new \Freesewing\Point();
        $cp2 = new \Freesewing\Point();
        $to = new \Freesewing\Point();
        
        $cp1->setX($x12);
        $cp1->setY($y12);
        $cp2->setX($x123);
        $cp2->setY($y123);
        $to->setX($x1234);
        $to->setY($y1234);

        return [
            $from,
            $cp1,
            $cp2,
            $to,
        ];
    }

    /** returns distance for controle point to make a circle
    * Note that circle is not perfect, but close enough
    * Takes radius of circle as input
    */
    public function bezierCircle($radius) {
        return $radius*4*(sqrt(2)-1)/3;
    }

}
