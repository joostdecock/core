<?php

namespace Freesewing;

/**
 * Freesewing\Path class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Path
{
    public $path = null;
    public $attributes = array();
    public $boundary = null;
    public $direction;


    public function setPath($path)
    {
        // Weeding out double spaces
        $this->path = trim(preg_replace('/ {2,}/', ' ', $path));
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setDirection($direction)
    {
        if($direction == 'ccw') $this->direction =  'ccw';
        else $this->direction =  'cw';
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function findBoundary($part)
    {
        foreach ($part->paths as $pathName => $pathObject) {
            /* 
             * breaking path into array 
             **/
            $pathAsArray = $pathObject->asArray();
            foreach ($pathAsArray as $index => $data) {
                /* 
                 * Are we dealing with a command or point index?
                 **/
                if ($this->isAllowedPathCommand($data)) { 
                    /* 
                     * This is a command
                     **/
                    $command = $data;
                } 
                if (!$this->isAllowedPathCommand($data)) { 
                    /* 
                     * This is a point index
                     **/
                    $pointIndex = $data;
                    if(!isset($part->points[$pointIndex])) {
                        /* 
                         * Reference to non-existing point. Bail out
                         **/
                        throw new \InvalidArgumentException('SVG path references non-existing point ' . $pointIndex);
                    }
                    if (!@is_object($topLeft)) {
                        /* 
                         * Topleft is not set. In other words, this is the first point we look at
                         * store it as both the topLeft and bottomRight point of our path boundary
                         **/
                        $topLeft = new \Freesewing\Point();
                        $topLeft->setX($part->points[$pointIndex]->x);
                        $topLeft->setY($part->points[$pointIndex]->y);
                        $bottomRight = new \Freesewing\Point();
                        $bottomRight->setX($part->points[$pointIndex]->x);
                        $bottomRight->setY($part->points[$pointIndex]->y);
                    } else {
                        /* 
                         * Topleft has been set. Let's compare this point to the current topLeft and bottomRight
                         **/
                        switch ($command) {
                            case 'M':
                            case 'L':
                                /* 
                                 * MoveTo and LineTo (M and L) are simple
                                 **/
                                if ($part->points[$pointIndex]->getX() < $topLeft->getX()) $topLeft->setX($part->points[$pointIndex]->getX());
                                if ($part->points[$pointIndex]->getY() < $topLeft->getY())  $topLeft->setY($part->points[$pointIndex]->getY());
                                if ($part->points[$pointIndex]->getX() > $bottomRight->getX()) $bottomRight->setX($part->points[$pointIndex]->getX());
                                if ($part->points[$pointIndex]->getY() > $bottomRight->getY()) $bottomRight->setY($part->points[$pointIndex]->getY());
                            break;
                            case 'C':
                                // Bezier curves need a bit more work
                                /* 
                                 * Bezier curves need a bit more work. 
                                 * We need to calculate their bounding box by stepping through them.
                                 * We need ther start and finish point + control points, and pass that to findBezierBoundary()
                                 **/
                                if ($pathAsArray[$index - 1] == 'C') { 
                                    /* 
                                     * Only run this once per CurveTo command, which uses 4 points
                                     * They are disributed in our array like this
                                     *     [keyIndex - 2] = Start point
                                     *     [keyIndex - 1] = 'C' command
                                     *     [keyIndex] = First control point <-- We are here in our foreach loop
                                     *     [keyIndex + 1 ] = Second control point
                                     *     [keyIndex + 2 ] = End point
                                     *
                                     **/
                                    $curveStart = $part->points[$pathAsArray[$index - 2]];
                                    $curveControlPoint1 = $part->points[$pathAsArray[$index]];
                                    $curveControlPoint2 = $part->points[$pathAsArray[$index + 1]];
                                    $curveEnd = $part->points[$pathAsArray[$index + 2]];
                                    $bezierBoundary = $this->findBezierBoundary( $curveStart, $curveControlPoint1, $curveControlPoint2, $curveEnd);
                                    if ($bezierBoundary->topLeft->getX() < $topLeft->getX()) $topLeft->setX($bezierBoundary->topLeft->getX());
                                    if ($bezierBoundary->topLeft->getY() < $topLeft->getY()) $topLeft->setY($bezierBoundary->topLeft->getY());
                                    if ($bezierBoundary->bottomRight->getX() > $bottomRight->getX()) $bottomRight->setX($bezierBoundary->bottomRight->getX());
                                    if ($bezierBoundary->bottomRight->getY() > $bottomRight->getY()) $bottomRight->setY($bezierBoundary->bottomRight->getY());
                                }
                            break;
                        }
                    }
                }
            }
        }
        $boundary = new \Freesewing\Boundary();
        $boundary->setTopLeft($topLeft);
        $boundary->setBottomRight($bottomRight);
        return $boundary;
    }

    private function findBezierBoundary($start, $cp1, $cp2, $end)
    {
        for ($i = 0; $i <= 100; ++$i) {
            $t = $i / 100;
            $x = $this->bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = $this->bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            if ($i == 0) {
                $minX = $x;
                $minY = $y;
                $maxX = $x;
                $maxY = $y;
                $previousX = $x;
                $previousY = $y;
            } else {
                if ($x < $previousX) {
                    $minX = $x;
                }
                if ($y < $previousY) {
                    $minY = $y;
                }
                if ($x > $previousX) {
                    $maxX = $x;
                }
                if ($y > $previousY) {
                    $maxY = $y;
                }
            }
            $previousX = $x;
            $previousY = $y;
        }
        $topLeft = new \Freesewing\Point();
        $topLeft->setX($minX);
        $topLeft->setY($minY);
        $bottomRight = new \Freesewing\Point();
        $bottomRight->setX($maxX);
        $bottomRight->setY($maxY);

        $boundary = new \Freesewing\Boundary();
        $boundary->setTopLeft($topLeft);
        $boundary->setBottomRight($bottomRight);
        return $boundary;
    }

    private function bezierPoint($t, $start, $cp1, $cp2, $end)
    {
        /* wikipedia.org/wiki/B%C3%A9zier_curve#Cubic_B.C3.A9zier_curves */
        return $start * (1.0 - $t) * (1.0 - $t) * (1.0 - $t)
            + 3.0 * $cp1 * (1.0 - $t) * (1.0 - $t) * $t
            + 3.0 * $cp2 * (1.0 - $t) * $t * $t
            + $end * $t * $t * $t;
    }
    
    public function isAllowedPathCommand($command)
    {
        $allowedPathCommands = [
            'M',
            'L',
            'C',
            'Z',
            'z',
        ];
        if (in_array($command, $allowedPathCommands)) {
            return true;
        } else {
            return false;
        }
    }

    private function asArray()
    {
        return explode(' ', $this->path);
    }

}
