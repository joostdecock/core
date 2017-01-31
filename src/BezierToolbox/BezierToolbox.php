<?php
/** Freesewing\BezierToolbox class */
namespace Freesewing;

use \Freesewing\Point;
use \Freesewing\Boundary;

/**
 * Static utility methods to help with handling Bezier curves.
 *  
 * All of the public methods in this class are static, 
 * so there is no need to instantiate an object use them.
 *
 * ## Typical use
 *
 * The BezierToolbox class is internal. 
 * It provides helper methods to the Path and Part classes.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016-2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BezierToolbox
{
    /** Number of steps when walking a path */
    const STEPS = 100;

    /**
     * Returns the distance for a control point to approximate a circle
     *
     * Note that circle is not perfect, but close enough
     *
     * @param float $radius The radius of the circle to aim for
     *
     * @return loat The distance to the control point
     */
    public static function bezierCircle($radius)
    {
        return $radius * 4 * (sqrt(2) - 1) / 3;
    }

    /**
     * Returns the length of a cubic Bezier curve
     *
     * There is no closed-form solution to calculate the length of cubic polynomial curves.
     * Instead, we'll subdivide this cube into a bunch of tiny steps
     * and treat those as straight lines.
     *
     * @param \Freesewing\Point $start Point at the start of the curve
     * @param \Freesewing\Point $cp1 First control point
     * @param \Freesewing\Point $cp2 Second control point
     * @param \Freesewing\Point $end Point at the end of the curve
     *
     * @return float The length of the curve
     */
    public static function bezierLength(Point $start, Point $cp1, Point $cp2, Point $end)
    {
        $length = 0;
        $steps = self::STEPS;

        for ($i = 0; $i <= $steps; ++$i) {
            $t = $i / $steps;
            $x = Utils::bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = Utils::bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            if ($i > 0) {
                $deltaX = $x - $previousX;
                $deltaY = $y - $previousY;
                $length += sqrt(pow($deltaX, 2) + pow($deltaY, 2));
            }
            $previousX = $x;
            $previousY = $y;
        }

        return $length;
    }

    /**
     * Finds one edge of a Bezier curve
     *
     * This walks through the curve while keeping an eye on the coordinates
     * and registering them most [left|right|up|down] depending on the value
     * of $direction.
     *
     * @param \Freesewing\Point $start The start of the curve
     * @param \Freesewing\Point $cp1 The control point for the start of the curve
     * @param \Freesewing\Point $cp2 The control point for the end of the curve
     * @param \Freesewing\Point $end The end of the curve
     * @param string $direction One of: left, right, up, down
     *
     * @return \Freesewing\Point The point at the edge
     */
    public static function bezierEdge(Point $start, Point $cp1, Point $cp2, Point $end, $direction = 'left')
    {
        $steps = self::STEPS;
        for ($i = 0; $i <= $steps; ++$i) {
            $t = $i / $steps;
            $x = Utils::bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = Utils::bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            if ($i == 0) {
                $edgeX = $x;
                $edgeY = $y;
                $previousX = $x;
                $previousY = $y;
            } else {
                if (
                    ($y < $edgeY && $direction == 'top') or
                    ($y > $edgeY && $direction == 'bottom') or
                    ($x < $edgeX && $direction == 'left') or
                    ($x > $edgeX && $direction == 'right')
                ) {
                    $edgeX = $x;
                    $edgeY = $y;
                }
            }
            $previousX = $x;
            $previousY = $y;
        }
        $edge = new Point();
        $edge->setX($edgeX);
        $edge->setY($edgeY);

        return $edge;
    }

    /**
     * Finds the boundary of a Bezier curve
     *
     * This calculates the bounding box by walking through
     * the curve while keeping an eye on the coordinates
     * and registering the most topLeft and bottomRight point
     * we encounter.
     *
     * @param \Freesewing\Point $start The start of the curve
     * @param \Freesewing\Point $cp1 The control point for the start of the curve
     * @param \Freesewing\Point $cp2 The control point for the end of the curve
     * @param \Freesewing\Point $end The end of the curve
     *
     * @return \Freesewing\Boundary A boundary object.
     */
    public static function bezierBoundary(Point $start, Point $cp1, Point $cp2, Point $end)
    {
        $steps = self::STEPS;
        for ($i = 0; $i <= $steps; ++$i) {
            $t = $i / $steps;
            $x = Utils::bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = Utils::bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            if ($i == 0) {
                $minX = $x;
                $minY = $y;
                $maxX = $x;
                $maxY = $y;
                $previousX = $x;
                $previousY = $y;
            } else {
                if ($x < $minX) {
                    $minX = $x;
                }
                if ($y < $minY) {
                    $minY = $y;
                }
                if ($x > $maxX) {
                    $maxX = $x;
                }
                if ($y > $maxY) {
                    $maxY = $y;
                }
            }
            $previousX = $x;
            $previousY = $y;
        }
        $topLeft = new Point();
        $topLeft->setX($minX);
        $topLeft->setY($minY);
        $bottomRight = new Point();
        $bottomRight->setX($maxX);
        $bottomRight->setY($maxY);

        $boundary = new Boundary();
        $boundary->setTopLeft($topLeft);
        $boundary->setBottomRight($bottomRight);

        return $boundary;
    }

    /**
     * Returns delta of split point on curve
     *
     * Approximate delta (between 0 and 1) of a point 'split' on
     * a Bezier curve
     *
     * @param \Freesewing\Point $start Point at the start of the curve
     * @param \Freesewing\Point $cp1 Control point 1
     * @param \Freesewing\Point $cp2 Control point 2
     * @param \Freesewing\Point $end Point at the end of the curve
     * @param \Freesewing\Point $split The point to split on
     *
     * @return float The delta between 0 and 1
     */
    public static function bezierDelta($start, $cp1, $cp2, $end, $split)
    {
        $steps = self::STEPS;
        $best_t = null;
        $best_distance = false;
        $tmp = new \Freesewing\Point();
        for ($i = 0; $i <= $steps; ++$i) {
            $t = $i / $steps;
            $x = Utils::bezierPoint($t, $start->getX(), $cp1->getX(), $cp2->getX(), $end->getX());
            $y = Utils::bezierPoint($t, $start->getY(), $cp1->getY(), $cp2->getY(), $end->getY());
            $tmp->setX($x);
            $tmp->setY($y);
            $distance = Utils::distance($split, $tmp);
            if ($distance < $best_distance || $best_distance === false) {
                $best_t = $t;
                $best_distance = $distance;
            }
        }

        return $best_t;
    }

    /**
     * Returns points for a curve splitted on a given delta
     *
     * This does the actually splitting
     *
     * @see \Freesewing\Part::splitCurve()
     *
     * @param \Freesewing\Point $start The point at the start of the curve
     * @param \Freesewing\Point $cp1 The first control point
     * @param \Freesewing\Point $cp2 The second control point
     * @param \Freesewing\Point $end The point at the end of the curve
     * @param float $delta The delta to split on, between 0 and 1
     *
     * @return array the 8 points resulting from the split
     */
    public static function bezierSplit($start, $cp1, $cp2, $end, $delta)
    {
        $x1 = $start->getX();
        $y1 = $start->getY();
        $x2 = $cp1->getX();
        $y2 = $cp1->getY();
        $x3 = $cp2->getX();
        $y3 = $cp2->getY();
        $x4 = $end->getX();
        $y4 = $end->getY();

        $x12 = ($x2 - $x1) * $delta + $x1;
        $y12 = ($y2 - $y1) * $delta + $y1;

        $x23 = ($x3 - $x2) * $delta + $x2;
        $y23 = ($y3 - $y2) * $delta + $y2;

        $x34 = ($x4 - $x3) * $delta + $x3;
        $y34 = ($y4 - $y3) * $delta + $y3;

        $x123 = ($x23 - $x12) * $delta + $x12;
        $y123 = ($y23 - $y12) * $delta + $y12;

        $x234 = ($x34 - $x23) * $delta + $x23;
        $y234 = ($y34 - $y23) * $delta + $y23;

        $x1234 = ($x234 - $x123) * $delta + $x123;
        $y1234 = ($y234 - $y123) * $delta + $y123;

        $cp1 = new \Freesewing\Point();
        $cp2 = new \Freesewing\Point();
        $end = new \Freesewing\Point();

        $cp1->setX($x12);
        $cp1->setY($y12);
        $cp2->setX($x123);
        $cp2->setY($y123);
        $end->setX($x1234);
        $end->setY($y1234);

        return [
            $start,
            $cp1,
            $cp2,
            $end,
        ];
    }

    /**
     * Returns intersection between a curve and a line
     *
     * The number of intersections between a curve and a line
     * varies. So we return an array of points.
     *
     * @param \Freesewing\Point $lineStart The point at the start of the line
     * @param \Freesewing\Point $lineEnd The point at the end of the line
     * @param \Freesewing\Point $curveStart The point at the start of the curve
     * @param \Freesewing\Point $curveCp1 The first control point
     * @param \Freesewing\Point $curveCp2 The second control point
     * @param \Freesewing\Point $curveEnd The point at the end of the curve
     *
     * @return array|false An array of intersection points or false if there are none
     */
    public static function bezierLineIntersections($lineStart, $lineEnd, $curveStart, $curveCp1, $curveCp2, $curveEnd)
    {
        $a1 = $lineStart->asVector();
        $a2 = $lineEnd->asVector();
        $p1 = $curveStart->asVector();
        $p2 = $curveCp1->asVector();
        $p3 = $curveCp2->asVector();
        $p4 = $curveEnd->asVector();

        $min = $a1->min($a2); // used to determine if point is on line segment
        $max = $a1->max($a2); // used to determine if point is on line segment

        // Cubic polynomial coefficients of the curve
        $a = $p1->multiply(-1);
        $b = $p2->multiply(3);
        $c = $p3->multiply(-3);
        $d = $a->add($b->add($c->add($p4)));
        $c3 = clone $d;

        $a = $p1->multiply(3);
        $b = $p2->multiply(-6);
        $c = $p3->multiply(3);
        $d = $a->add($b->add($c));
        $c2 = clone $d;

        $a = $p1->multiply(-3);
        $b = $p2->multiply(3);
        $c = $a->add($b);
        $c1 = clone $c;

        $c0 = clone $p1;

        // Convert line to normal form: ax + by + c = 0
        // Find normal to line: negative inverse of original line's slope
        $n = new Vector();
        $n->setX($a1->getY() - $a2->getY());
        $n->setY($a2->getX() - $a1->getX());

        // Determine new c coefficient
        $cl = $a1->getX() * $a2->getY() - $a2->getX() * $a1->getY();

        // Rotate each cubic coefficient using line for new coordinate system?
        // Find roots of rotated cubic
        $roots = new Polynomial([
            $n->dot($c3),
            $n->dot($c2),
            $n->dot($c1),
            $n->dot($c0) + $cl,
        ]);
        $roots = $roots->getRoots();

        // Any roots in closed interval [0,1] are intersections on Bezier, but
        // might not be on the line segment.
        // Find intersections and calculate point coordinates
        for ($i=0; $i< count($roots); $i++) {
            $t = $roots[$i];

            if (0 <= $t && $t <= 1) {
                // We're within the Bezier curve
                // Find point on Bezier
                $p5 = $p1->lerp($p2, $t);
                $p6 = $p2->lerp($p3, $t);
                $p7 = $p3->lerp($p4, $t);

                $p8 = $p5->lerp($p6, $t);
                $p9 = $p6->lerp($p7, $t);

                $p10 = $p8->lerp($p9, $t);

                // See if point is on line segment
                // Had to make special cases for vertical and horizontal lines due
                // to slight errors in calculation of p6
                if ($a1->getX() == $a2->getX()) {
                    if ($min->getY() <= $p10->getY() && $p10->getY() <= $max->getY()) {
                        $points[] = $p10;
                    }
                } elseif ($a1->getY() == $a2->getY()) {
                    if ($min->getX() <= $p10->getX() && $p10->getX() <= $max->getX()) {
                        $points[] = $p10;
                    }
                } elseif ($p10->gte($min) && $p10->lte($max)) {
                    $points[] = $p10;
                }
            }
        }
        if (isset($points) && is_array($points)) {
            foreach ($points as $key => $point) {
                $intersections[$key] = $point->asPoint();
            }
            return $intersections;
        } else {
            return false;
        }
    }

    /**
     * Returns intersection between 2 cubic Bezier curves
     *
     * As the number of intersections between two curves
     * varies, we return an array of points.
     *
     * This implementation is based on the intersection
     * procedures by Kevin Lindsey (http://www.kevlindev.com)
     *
     * @param \Freesewing\Point $curve1Start The point at the start of the first curve
     * @param \Freesewing\Point $curve1Cp1 The first control point of the first curve
     * @param \Freesewing\Point $curve1Cp2 The second control point of the first curve
     * @param \Freesewing\Point $curve1End The point at the end of the first curve
     * @param \Freesewing\Point $curve2Start The point at the start of the second curve
     * @param \Freesewing\Point $curve2Cp1 The first control point of the second curve
     * @param \Freesewing\Point $curve2Cp2 The second control point of the second curve
     * @param \Freesewing\Point $curve2End The point at the end of the second curve
     *
     * @return array|false An array of intersection points or false if there are none
     */
    public static function bezierBezierIntersections($curve1Start, $curve1Cp1, $curve1Cp2, $curve1End, $curve2Start, $curve2Cp1, $curve2Cp2, $curve2End)
    {
        $points = false;

        $a1 = $curve1Start->asVector();
        $a2 = $curve1Cp1->asVector();
        $a3 = $curve1Cp2->asVector();
        $a4 = $curve1End->asVector();

        $b1 = $curve2Start->asVector();
        $b2 = $curve2Cp1->asVector();
        $b3 = $curve2Cp2->asVector();
        $b4 = $curve2End->asVector();

        // Cubic polynomial coefficients of the first curve
        $a = $a1->multiply(-1);
        $b = $a2->multiply(3);
        $c = $a3->multiply(-3);
        $d = $a->add($b->add($c->add($a4)));
        $c13 = clone $d;

        $a = $a1->multiply(3);
        $b = $a2->multiply(-6);
        $c = $a3->multiply(3);
        $d = $a->add($b->add($c));
        $c12 = clone $d;

        $a = $a1->multiply(-3);
        $b = $a2->multiply(3);
        $c = $a->add($b);
        $c11 = clone $c;

        $c10 = clone $a1;

        // Cubic polynomial coefficients of the second curve
        $a = $b1->multiply(-1);
        $b = $b2->multiply(3);
        $c = $b3->multiply(-3);
        $d = $a->add($b->add($c->add($b4)));
        $c23 = clone $d;

        $a = $b1->multiply(3);
        $b = $b2->multiply(-6);
        $c = $b3->multiply(3);
        $d = $a->add($b->add($c));
        $c22 = clone $d;

        $a = $b1->multiply(-3);
        $b = $b2->multiply(3);
        $c = $a->add($b);
        $c21 = clone $c;

        $c20 = clone $b1;

        // Moving on
        $c10x2 = $c10->getX() * $c10->getX();
        $c10x3 = $c10->getX() * $c10->getX() * $c10->getX();
        $c10y2 = $c10->getY() * $c10->getY();
        $c10y3 = $c10->getY() * $c10->getY() * $c10->getY();

        $c11x2 = $c11->getX() * $c11->getX();
        $c11x3 = $c11->getX() * $c11->getX() * $c11->getX();
        $c11y2 = $c11->getY() * $c11->getY();
        $c11y3 = $c11->getY() * $c11->getY() * $c11->getY();

        $c12x2 = $c12->getX() * $c12->getX();
        $c12x3 = $c12->getX() * $c12->getX() * $c12->getX();
        $c12y2 = $c12->getY() * $c12->getY();
        $c12y3 = $c12->getY() * $c12->getY() * $c12->getY();

        $c13x2 = $c13->getX() * $c13->getX();
        $c13x3 = $c13->getX() * $c13->getX() * $c13->getX();
        $c13y2 = $c13->getY() * $c13->getY();
        $c13y3 = $c13->getY() * $c13->getY() * $c13->getY();

        $c20x2 = $c20->getX() * $c20->getX();
        $c20x3 = $c20->getX() * $c20->getX() * $c20->getX();
        $c20y2 = $c20->getY() * $c20->getY();
        $c20y3 = $c20->getY() * $c20->getY() * $c20->getY();

        $c21x2 = $c21->getX() * $c21->getX();
        $c21x3 = $c21->getX() * $c21->getX() * $c21->getX();
        $c21y2 = $c21->getY() * $c21->getY();

        $c22x2 = $c22->getX() * $c22->getX();
        $c22x3 = $c22->getX() * $c22->getX() * $c22->getX();
        $c22y2 = $c22->getY() * $c22->getY();

        $c23x2 = $c23->getX() * $c23->getX();
        $c23x3 = $c23->getX() * $c23->getX() * $c23->getX();
        $c23y2 = $c23->getY() * $c23->getY();
        $c23y3 = $c23->getY() * $c23->gety() * $c23->getY();

        // Brace yourself

        $coefs = array();

        $coefs [] = -$c13x3*$c23y3 + $c13y3*$c23x3 - 3*$c13->getX()*$c13y2*$c23x2*$c23->getY() +
        3*$c13x2*$c13->getY()*$c23->getX()*$c23y2;

        $coefs [] = -6*$c13->getX()*$c22->getX()*$c13y2*$c23->getX()*$c23->getY() + 6*$c13x2*$c13->getY()*$c22->getY()*$c23->getX()*$c23->getY() + 3*$c22->getX()*$c13y3*$c23x2 -
            3*$c13x3*$c22->getY()*$c23y2 - 3*$c13->getX()*$c13y2*$c22->getY()*$c23x2 + 3*$c13x2*$c22->getX()*$c13->getY()*$c23y2;

        $coefs [] = -6*$c21->getX()*$c13->getX()*$c13y2*$c23->getX()*$c23->getY() - 6*$c13->getX()*$c22->getX()*$c13y2*$c22->getY()*$c23->getX() + 6*$c13x2*$c22->getX()*$c13->getY()*$c22->getY()*$c23->getY() +
            3*$c21->getX()*$c13y3*$c23x2 + 3*$c22x2*$c13y3*$c23->getX() + 3*$c21->getX()*$c13x2*$c13->getY()*$c23y2 - 3*$c13->getX()*$c21->getY()*$c13y2*$c23x2 -
            3*$c13->getX()*$c22x2*$c13y2*$c23->getY() + $c13x2*$c13->getY()*$c23->getX()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) + $c13x3*(-$c21->getY()*$c23y2 -
            2*$c22y2*$c23->getY() - $c23->getY()*(2*$c21->getY()*$c23->getY() + $c22y2));

        $coefs [] = $c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX()*$c23->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getX()*$c23->getY() + 6*$c21->getX()*$c22->getX()*$c13y3*$c23->getX() +
            3*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c23y2 + 6*$c10->getX()*$c13->getX()*$c13y2*$c23->getX()*$c23->getY() - 3*$c11->getX()*$c12->getX()*$c13y2*$c23->getX()*$c23->getY() -
            3*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23x2 - 6*$c10->getY()*$c13x2*$c13->getY()*$c23->getX()*$c23->getY() - 6*$c20->getX()*$c13->getX()*$c13y2*$c23->getX()*$c23->getY() +
            3*$c11->getY()*$c12->getY()*$c13x2*$c23->getX()*$c23->getY() - 2*$c12->getX()*$c12y2*$c13->getX()*$c23->getX()*$c23->getY() - 6*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2*$c23->getY() -
            6*$c21->getX()*$c13->getX()*$c13y2*$c22->getY()*$c23->getX() - 6*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2*$c23->getX() + 6*$c21->getX()*$c13x2*$c13->getY()*$c22->getY()*$c23->getY() +
            2*$c12x2*$c12->getY()*$c13->getY()*$c23->getX()*$c23->getY() + $c22x3*$c13y3 - 3*$c10->getX()*$c13y3*$c23x2 + 3*$c10->getY()*$c13x3*$c23y2 +
            3*$c20->getX()*$c13y3*$c23x2 + $c12y3*$c13->getX()*$c23x2 - $c12x3*$c13->getY()*$c23y2 - 3*$c10->getX()*$c13x2*$c13->getY()*$c23y2 +
            3*$c10->getY()*$c13->getX()*$c13y2*$c23x2 - 2*$c11->getX()*$c12->getY()*$c13x2*$c23y2 + $c11->getX()*$c12->getY()*$c13y2*$c23x2 - $c11->getY()*$c12->getX()*$c13x2*$c23y2 +
            2*$c11->getY()*$c12->getX()*$c13y2*$c23x2 + 3*$c20->getX()*$c13x2*$c13->getY()*$c23y2 - $c12->getX()*$c12y2*$c13->getY()*$c23x2 -
            3*$c20->getY()*$c13->getX()*$c13y2*$c23x2 + $c12x2*$c12->getY()*$c13->getX()*$c23y2 - 3*$c13->getX()*$c22x2*$c13y2*$c22->getY() +
            $c13x2*$c13->getY()*$c23->getX()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c13x2*$c22->getX()*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) +
            $c13x3*(-2*$c21->getY()*$c22->getY()*$c23->getY() - $c20->getY()*$c23y2 - $c22->getY()*(2*$c21->getY()*$c23->getY() + $c22y2) - $c23->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $coefs [] = 6*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY()*$c23->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY()*$c23->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY()*$c23->getX() -
            $c11->getY()*$c12->getX()*$c13->getX()*$c22->getX()*$c13->getY()*$c23->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY()*$c23->getX() - 6*$c11->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY()*$c23->getX() -
            6*$c10->getX()*$c22->getX()*$c13y3*$c23->getX() + 6*$c20->getX()*$c22->getX()*$c13y3*$c23->getX() + 6*$c10->getY()*$c13x3*$c22->getY()*$c23->getY() + 2*$c12y3*$c13->getX()*$c22->getX()*$c23->getX() -
            2*$c12x3*$c13->getY()*$c22->getY()*$c23->getY() + 6*$c10->getX()*$c13->getX()*$c22->getX()*$c13y2*$c23->getY() + 6*$c10->getX()*$c13->getX()*$c13y2*$c22->getY()*$c23->getX() +
            6*$c10->getY()*$c13->getX()*$c22->getX()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c22->getX()*$c13y2*$c23->getY() - 3*$c11->getX()*$c12->getX()*$c13y2*$c22->getY()*$c23->getX() +
            2*$c11->getX()*$c12->getY()*$c22->getX()*$c13y2*$c23->getX() + 4*$c11->getY()*$c12->getX()*$c22->getX()*$c13y2*$c23->getX() - 6*$c10->getX()*$c13x2*$c13->getY()*$c22->getY()*$c23->getY() -
            6*$c10->getY()*$c13x2*$c22->getX()*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c13x2*$c13->getY()*$c22->getY()*$c23->getX() - 4*$c11->getX()*$c12->getY()*$c13x2*$c22->getY()*$c23->getY() -
            6*$c20->getX()*$c13->getX()*$c22->getX()*$c13y2*$c23->getY() - 6*$c20->getX()*$c13->getX()*$c13y2*$c22->getY()*$c23->getX() - 2*$c11->getY()*$c12->getX()*$c13x2*$c22->getY()*$c23->getY() +
            3*$c11->getY()*$c12->getY()*$c13x2*$c22->getX()*$c23->getY() + 3*$c11->getY()*$c12->getY()*$c13x2*$c22->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c13->getX()*$c22->getX()*$c23->getY() -
            2*$c12->getX()*$c12y2*$c13->getX()*$c22->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c22->getX()*$c13->getY()*$c23->getX() - 6*$c20->getY()*$c13->getX()*$c22->getX()*$c13y2*$c23->getX() -
            6*$c21->getX()*$c13->getX()*$c21->getY()*$c13y2*$c23->getX() - 6*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2*$c22->getY() + 6*$c20->getX()*$c13x2*$c13->getY()*$c22->getY()*$c23->getY() +
            2*$c12x2*$c12->getY()*$c13->getX()*$c22->getY()*$c23->getY() + 2*$c12x2*$c12->getY()*$c22->getX()*$c13->getY()*$c23->getY() + 2*$c12x2*$c12->getY()*$c13->getY()*$c22->getY()*$c23->getX() +
            3*$c21->getX()*$c22x2*$c13y3 + 3*$c21x2*$c13y3*$c23->getX() - 3*$c13->getX()*$c21->getY()*$c22x2*$c13y2 - 3*$c21x2*$c13->getX()*$c13y2*$c23->getY() +
            $c13x2*$c22->getX()*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c13x2*$c13->getY()*$c23->getX()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c21->getX()*$c13x2*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) + $c13x3*(-2*$c20->getY()*$c22->getY()*$c23->getY() - $c23->getY()*(2*$c20->getY()*$c22->getY() + $c21y2) -
            $c21->getY()*(2*$c21->getY()*$c23->getY() + $c22y2) - $c22->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $coefs [] = $c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY()*$c23->getX() + $c11->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY()*$c22->getY() -
            $c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c13->getY()*$c23->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY()*$c23->getX() - $c11->getY()*$c12->getX()*$c13->getX()*$c22->getX()*$c13->getY()*$c22->getY() -
            6*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() - 6*$c10->getX()*$c21->getX()*$c13y3*$c23->getX() + 6*$c20->getX()*$c21->getX()*$c13y3*$c23->getX() +
            2*$c21->getX()*$c12y3*$c13->getX()*$c23->getX() + 6*$c10->getX()*$c21->getX()*$c13->getX()*$c13y2*$c23->getY() + 6*$c10->getX()*$c13->getX()*$c21->getY()*$c13y2*$c23->getX() +
            6*$c10->getX()*$c13->getX()*$c22->getX()*$c13y2*$c22->getY() + 6*$c10->getY()*$c21->getX()*$c13->getX()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c21->getX()*$c13y2*$c23->getY() -
            3*$c11->getX()*$c12->getX()*$c21->getY()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c22->getX()*$c13y2*$c22->getY() + 2*$c11->getX()*$c21->getX()*$c12->getY()*$c13y2*$c23->getX() +
            4*$c11->getY()*$c12->getX()*$c21->getX()*$c13y2*$c23->getX() - 6*$c10->getY()*$c21->getX()*$c13x2*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c13x2*$c21->getY()*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c13x2*$c22->getX()*$c13->getY()*$c22->getY() - 6*$c20->getX()*$c21->getX()*$c13->getX()*$c13y2*$c23->getY() - 6*$c20->getX()*$c13->getX()*$c21->getY()*$c13y2*$c23->getX() -
            6*$c20->getX()*$c13->getX()*$c22->getX()*$c13y2*$c22->getY() + 3*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2*$c23->getY() - 3*$c11->getY()*$c12->getY()*$c13->getX()*$c22x2*$c13->getY() +
            3*$c11->getY()*$c12->getY()*$c13x2*$c21->getY()*$c23->getX() + 3*$c11->getY()*$c12->getY()*$c13x2*$c22->getX()*$c22->getY() - 2*$c12->getX()*$c21->getX()*$c12y2*$c13->getX()*$c23->getY() -
            2*$c12->getX()*$c21->getX()*$c12y2*$c13->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c13->getX()*$c21->getY()*$c23->getX() - 2*$c12->getX()*$c12y2*$c13->getX()*$c22->getX()*$c22->getY() -
            6*$c20->getY()*$c21->getX()*$c13->getX()*$c13y2*$c23->getX() - 6*$c21->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2 + 6*$c20->getY()*$c13x2*$c21->getY()*$c13->getY()*$c23->getX() +
            2*$c12x2*$c21->getX()*$c12->getY()*$c13->getY()*$c23->getY() + 2*$c12x2*$c12->getY()*$c21->getY()*$c13->getY()*$c23->getX() + 2*$c12x2*$c12->getY()*$c22->getX()*$c13->getY()*$c22->getY() -
            3*$c10->getX()*$c22x2*$c13y3 + 3*$c20->getX()*$c22x2*$c13y3 + 3*$c21x2*$c22->getX()*$c13y3 + $c12y3*$c13->getX()*$c22x2 +
            3*$c10->getY()*$c13->getX()*$c22x2*$c13y2 + $c11->getX()*$c12->getY()*$c22x2*$c13y2 + 2*$c11->getY()*$c12->getX()*$c22x2*$c13y2 -
            $c12->getX()*$c12y2*$c22x2*$c13->getY() - 3*$c20->getY()*$c13->getX()*$c22x2*$c13y2 - 3*$c21x2*$c13->getX()*$c13y2*$c22->getY() +
            $c12x2*$c12->getY()*$c13->getX()*(2*$c21->getY()*$c23->getY() + $c22y2) + $c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) +
            $c21->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c12x3*$c13->getY()*(-2*$c21->getY()*$c23->getY() - $c22y2) +
            $c10->getY()*$c13x3*(6*$c21->getY()*$c23->getY() + 3*$c22y2) + $c11->getY()*$c12->getX()*$c13x2*(-2*$c21->getY()*$c23->getY() - $c22y2) +
            $c11->getX()*$c12->getY()*$c13x2*(-4*$c21->getY()*$c23->getY() - 2*$c22y2) + $c10->getX()*$c13x2*$c13->getY()*(-6*$c21->getY()*$c23->getY() - 3*$c22y2) +
            $c13x2*$c22->getX()*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) + $c20->getX()*$c13x2*$c13->getY()*(6*$c21->getY()*$c23->getY() + 3*$c22y2) +
            $c13x3*(-2*$c20->getY()*$c21->getY()*$c23->getY() - $c22->getY()*(2*$c20->getY()*$c22->getY() + $c21y2) - $c20->getY()*(2*$c21->getY()*$c23->getY() + $c22y2) -
            $c21->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $coefs [] = -$c10->getX()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getY() + $c10->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getY() + 6*$c10->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getY() - $c10->getY()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() + $c10->getY()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getX() +
            $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX()*$c23->getY() - $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getY()*$c23->getX() + $c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getY() +
            $c11->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() + $c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY() + $c11->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c22->getX()*$c13->getY() -
            $c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c23->getY() - 6*$c20->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY()*$c23->getX() - $c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY()*$c23->getX() -
            $c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c13->getY()*$c22->getY() - $c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13->getY() - 6*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() -
            6*$c10->getX()*$c20->getX()*$c13y3*$c23->getX() - 6*$c10->getX()*$c21->getX()*$c22->getX()*$c13y3 - 2*$c10->getX()*$c12y3*$c13->getX()*$c23->getX() + 6*$c20->getX()*$c21->getX()*$c22->getX()*$c13y3 +
            2*$c20->getX()*$c12y3*$c13->getX()*$c23->getX() + 2*$c21->getX()*$c12y3*$c13->getX()*$c22->getX() + 2*$c10->getY()*$c12x3*$c13->getY()*$c23->getY() - 6*$c10->getX()*$c10->getY()*$c13->getX()*$c13y2*$c23->getX() +
            3*$c10->getX()*$c11->getX()*$c12->getX()*$c13y2*$c23->getY() - 2*$c10->getX()*$c11->getX()*$c12->getY()*$c13y2*$c23->getX() - 4*$c10->getX()*$c11->getY()*$c12->getX()*$c13y2*$c23->getX() +
            3*$c10->getY()*$c11->getX()*$c12->getX()*$c13y2*$c23->getX() + 6*$c10->getX()*$c10->getY()*$c13x2*$c13->getY()*$c23->getY() + 6*$c10->getX()*$c20->getX()*$c13->getX()*$c13y2*$c23->getY() -
            3*$c10->getX()*$c11->getY()*$c12->getY()*$c13x2*$c23->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getX()*$c23->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getY()*$c23->getX() +
            6*$c10->getX()*$c20->getY()*$c13->getX()*$c13y2*$c23->getX() + 6*$c10->getX()*$c21->getX()*$c13->getX()*$c13y2*$c22->getY() + 6*$c10->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2 +
            4*$c10->getY()*$c11->getX()*$c12->getY()*$c13x2*$c23->getY() + 6*$c10->getY()*$c20->getX()*$c13->getX()*$c13y2*$c23->getX() + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c13x2*$c23->getY() -
            3*$c10->getY()*$c11->getY()*$c12->getY()*$c13x2*$c23->getX() + 2*$c10->getY()*$c12->getX()*$c12y2*$c13->getX()*$c23->getX() + 6*$c10->getY()*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2 -
            3*$c11->getX()*$c20->getX()*$c12->getX()*$c13y2*$c23->getY() + 2*$c11->getX()*$c20->getX()*$c12->getY()*$c13y2*$c23->getX() + $c11->getX()*$c11->getY()*$c12y2*$c13->getX()*$c23->getX() -
            3*$c11->getX()*$c12->getX()*$c20->getY()*$c13y2*$c23->getX() - 3*$c11->getX()*$c12->getX()*$c21->getX()*$c13y2*$c22->getY() - 3*$c11->getX()*$c12->getX()*$c21->getY()*$c22->getX()*$c13y2 +
            2*$c11->getX()*$c21->getX()*$c12->getY()*$c22->getX()*$c13y2 + 4*$c20->getX()*$c11->getY()*$c12->getX()*$c13y2*$c23->getX() + 4*$c11->getY()*$c12->getX()*$c21->getX()*$c22->getX()*$c13y2 -
            2*$c10->getX()*$c12x2*$c12->getY()*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c20->getX()*$c13x2*$c13->getY()*$c23->getY() - 6*$c10->getY()*$c20->getY()*$c13x2*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c21->getX()*$c13x2*$c13->getY()*$c22->getY() - 2*$c10->getY()*$c12x2*$c12->getY()*$c13->getX()*$c23->getY() - 2*$c10->getY()*$c12x2*$c12->getY()*$c13->getY()*$c23->getX() -
            6*$c10->getY()*$c13x2*$c21->getY()*$c22->getX()*$c13->getY() - $c11->getX()*$c11->getY()*$c12x2*$c13->getY()*$c23->getY() - 2*$c11->getX()*$c11y2*$c13->getX()*$c13->getY()*$c23->getX() +
            3*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2*$c23->getY() - 2*$c20->getX()*$c12->getX()*$c12y2*$c13->getX()*$c23->getY() - 2*$c20->getX()*$c12->getX()*$c12y2*$c13->getY()*$c23->getX() -
            6*$c20->getX()*$c20->getY()*$c13->getX()*$c13y2*$c23->getX() - 6*$c20->getX()*$c21->getX()*$c13->getX()*$c13y2*$c22->getY() - 6*$c20->getX()*$c13->getX()*$c21->getY()*$c22->getX()*$c13y2 +
            3*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2*$c23->getX() + 3*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2*$c22->getY() + 3*$c11->getY()*$c12->getY()*$c13x2*$c21->getY()*$c22->getX() -
            2*$c12->getX()*$c20->getY()*$c12y2*$c13->getX()*$c23->getX() - 2*$c12->getX()*$c21->getX()*$c12y2*$c13->getX()*$c22->getY() - 2*$c12->getX()*$c21->getX()*$c12y2*$c22->getX()*$c13->getY() -
            2*$c12->getX()*$c12y2*$c13->getX()*$c21->getY()*$c22->getX() - 6*$c20->getY()*$c21->getX()*$c13->getX()*$c22->getX()*$c13y2 - $c11y2*$c12->getX()*$c12->getY()*$c13->getX()*$c23->getX() +
            2*$c20->getX()*$c12x2*$c12->getY()*$c13->getY()*$c23->getY() + 6*$c20->getY()*$c13x2*$c21->getY()*$c22->getX()*$c13->getY() + 2*$c11x2*$c11->getY()*$c13->getX()*$c13->getY()*$c23->getY() +
            $c11x2*$c12->getX()*$c12->getY()*$c13->getY()*$c23->getY() + 2*$c12x2*$c20->getY()*$c12->getY()*$c13->getY()*$c23->getX() + 2*$c12x2*$c21->getX()*$c12->getY()*$c13->getY()*$c22->getY() +
            2*$c12x2*$c12->getY()*$c21->getY()*$c22->getX()*$c13->getY() + $c21x3*$c13y3 + 3*$c10x2*$c13y3*$c23->getX() - 3*$c10y2*$c13x3*$c23->getY() +
            3*$c20x2*$c13y3*$c23->getX() + $c11y3*$c13x2*$c23->getX() - $c11x3*$c13y2*$c23->getY() - $c11->getX()*$c11y2*$c13x2*$c23->getY() +
            $c11x2*$c11->getY()*$c13y2*$c23->getX() - 3*$c10x2*$c13->getX()*$c13y2*$c23->getY() + 3*$c10y2*$c13x2*$c13->getY()*$c23->getX() - $c11x2*$c12y2*$c13->getX()*$c23->getY() +
            $c11y2*$c12x2*$c13->getY()*$c23->getX() - 3*$c21x2*$c13->getX()*$c21->getY()*$c13y2 - 3*$c20x2*$c13->getX()*$c13y2*$c23->getY() + 3*$c20y2*$c13x2*$c13->getY()*$c23->getX() +
            $c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c12x3*$c13->getY()*(-2*$c20->getY()*$c23->getY() - 2*$c21->getY()*$c22->getY()) +
            $c10->getY()*$c13x3*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) + $c11->getY()*$c12->getX()*$c13x2*(-2*$c20->getY()*$c23->getY() - 2*$c21->getY()*$c22->getY()) +
            $c12x2*$c12->getY()*$c13->getX()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()) + $c11->getX()*$c12->getY()*$c13x2*(-4*$c20->getY()*$c23->getY() - 4*$c21->getY()*$c22->getY()) +
            $c10->getX()*$c13x2*$c13->getY()*(-6*$c20->getY()*$c23->getY() - 6*$c21->getY()*$c22->getY()) + $c20->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c23->getY() + 6*$c21->getY()*$c22->getY()) +
            $c21->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) + $c13x3*(-2*$c20->getY()*$c21->getY()*$c22->getY() - $c20y2*$c23->getY() -
            $c21->getY()*(2*$c20->getY()*$c22->getY() + $c21y2) - $c20->getY()*(2*$c20->getY()*$c23->getY() + 2*$c21->getY()*$c22->getY()));

        $coefs [] = -$c10->getX()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY() + $c10->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY() + 6*$c10->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() -
            6*$c10->getY()*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY() - $c10->getY()*$c11->getX()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() + $c10->getY()*$c11->getY()*$c12->getX()*$c13->getX()*$c22->getX()*$c13->getY() +
            $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX()*$c22->getY() - $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c22->getX()*$c13->getY() + $c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c13->getY()*$c22->getY() +
            $c11->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() + $c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY() - $c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY()*$c22->getY() -
            6*$c20->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c22->getX()*$c13->getY() - $c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c22->getX()*$c13->getY() - $c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c21->getY()*$c13->getY() -
            6*$c10->getX()*$c20->getX()*$c22->getX()*$c13y3 - 2*$c10->getX()*$c12y3*$c13->getX()*$c22->getX() + 2*$c20->getX()*$c12y3*$c13->getX()*$c22->getX() + 2*$c10->getY()*$c12x3*$c13->getY()*$c22->getY() -
            6*$c10->getX()*$c10->getY()*$c13->getX()*$c22->getX()*$c13y2 + 3*$c10->getX()*$c11->getX()*$c12->getX()*$c13y2*$c22->getY() - 2*$c10->getX()*$c11->getX()*$c12->getY()*$c22->getX()*$c13y2 -
            4*$c10->getX()*$c11->getY()*$c12->getX()*$c22->getX()*$c13y2 + 3*$c10->getY()*$c11->getX()*$c12->getX()*$c22->getX()*$c13y2 + 6*$c10->getX()*$c10->getY()*$c13x2*$c13->getY()*$c22->getY() +
            6*$c10->getX()*$c20->getX()*$c13->getX()*$c13y2*$c22->getY() - 3*$c10->getX()*$c11->getY()*$c12->getY()*$c13x2*$c22->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getX()*$c22->getY() +
            2*$c10->getX()*$c12->getX()*$c12y2*$c22->getX()*$c13->getY() + 6*$c10->getX()*$c20->getY()*$c13->getX()*$c22->getX()*$c13y2 + 6*$c10->getX()*$c21->getX()*$c13->getX()*$c21->getY()*$c13y2 +
            4*$c10->getY()*$c11->getX()*$c12->getY()*$c13x2*$c22->getY() + 6*$c10->getY()*$c20->getX()*$c13->getX()*$c22->getX()*$c13y2 + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c13x2*$c22->getY() -
            3*$c10->getY()*$c11->getY()*$c12->getY()*$c13x2*$c22->getX() + 2*$c10->getY()*$c12->getX()*$c12y2*$c13->getX()*$c22->getX() - 3*$c11->getX()*$c20->getX()*$c12->getX()*$c13y2*$c22->getY() +
            2*$c11->getX()*$c20->getX()*$c12->getY()*$c22->getX()*$c13y2 + $c11->getX()*$c11->getY()*$c12y2*$c13->getX()*$c22->getX() - 3*$c11->getX()*$c12->getX()*$c20->getY()*$c22->getX()*$c13y2 -
            3*$c11->getX()*$c12->getX()*$c21->getX()*$c21->getY()*$c13y2 + 4*$c20->getX()*$c11->getY()*$c12->getX()*$c22->getX()*$c13y2 - 2*$c10->getX()*$c12x2*$c12->getY()*$c13->getY()*$c22->getY() -
            6*$c10->getY()*$c20->getX()*$c13x2*$c13->getY()*$c22->getY() - 6*$c10->getY()*$c20->getY()*$c13x2*$c22->getX()*$c13->getY() - 6*$c10->getY()*$c21->getX()*$c13x2*$c21->getY()*$c13->getY() -
            2*$c10->getY()*$c12x2*$c12->getY()*$c13->getX()*$c22->getY() - 2*$c10->getY()*$c12x2*$c12->getY()*$c22->getX()*$c13->getY() - $c11->getX()*$c11->getY()*$c12x2*$c13->getY()*$c22->getY() -
            2*$c11->getX()*$c11y2*$c13->getX()*$c22->getX()*$c13->getY() + 3*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2*$c22->getY() - 2*$c20->getX()*$c12->getX()*$c12y2*$c13->getX()*$c22->getY() -
            2*$c20->getX()*$c12->getX()*$c12y2*$c22->getX()*$c13->getY() - 6*$c20->getX()*$c20->getY()*$c13->getX()*$c22->getX()*$c13y2 - 6*$c20->getX()*$c21->getX()*$c13->getX()*$c21->getY()*$c13y2 +
            3*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2*$c22->getX() + 3*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2*$c21->getY() - 2*$c12->getX()*$c20->getY()*$c12y2*$c13->getX()*$c22->getX() -
            2*$c12->getX()*$c21->getX()*$c12y2*$c13->getX()*$c21->getY() - $c11y2*$c12->getX()*$c12->getY()*$c13->getX()*$c22->getX() + 2*$c20->getX()*$c12x2*$c12->getY()*$c13->getY()*$c22->getY() -
            3*$c11->getY()*$c21x2*$c12->getY()*$c13->getX()*$c13->getY() + 6*$c20->getY()*$c21->getX()*$c13x2*$c21->getY()*$c13->getY() + 2*$c11x2*$c11->getY()*$c13->getX()*$c13->getY()*$c22->getY() +
            $c11x2*$c12->getX()*$c12->getY()*$c13->getY()*$c22->getY() + 2*$c12x2*$c20->getY()*$c12->getY()*$c22->getX()*$c13->getY() + 2*$c12x2*$c21->getX()*$c12->getY()*$c21->getY()*$c13->getY() -
            3*$c10->getX()*$c21x2*$c13y3 + 3*$c20->getX()*$c21x2*$c13y3 + 3*$c10x2*$c22->getX()*$c13y3 - 3*$c10y2*$c13x3*$c22->getY() + 3*$c20x2*$c22->getX()*$c13y3 +
            $c21x2*$c12y3*$c13->getX() + $c11y3*$c13x2*$c22->getX() - $c11x3*$c13y2*$c22->getY() + 3*$c10->getY()*$c21x2*$c13->getX()*$c13y2 -
            $c11->getX()*$c11y2*$c13x2*$c22->getY() + $c11->getX()*$c21x2*$c12->getY()*$c13y2 + 2*$c11->getY()*$c12->getX()*$c21x2*$c13y2 + $c11x2*$c11->getY()*$c22->getX()*$c13y2 -
            $c12->getX()*$c21x2*$c12y2*$c13->getY() - 3*$c20->getY()*$c21x2*$c13->getX()*$c13y2 - 3*$c10x2*$c13->getX()*$c13y2*$c22->getY() + 3*$c10y2*$c13x2*$c22->getX()*$c13->getY() -
            $c11x2*$c12y2*$c13->getX()*$c22->getY() + $c11y2*$c12x2*$c22->getX()*$c13->getY() - 3*$c20x2*$c13->getX()*$c13y2*$c22->getY() + 3*$c20y2*$c13x2*$c22->getX()*$c13->getY() +
            $c12x2*$c12->getY()*$c13->getX()*(2*$c20->getY()*$c22->getY() + $c21y2) + $c11->getX()*$c12->getX()*$c13->getX()*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c12x3*$c13->getY()*(-2*$c20->getY()*$c22->getY() - $c21y2) + $c10->getY()*$c13x3*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c11->getY()*$c12->getX()*$c13x2*(-2*$c20->getY()*$c22->getY() - $c21y2) + $c11->getX()*$c12->getY()*$c13x2*(-4*$c20->getY()*$c22->getY() - 2*$c21y2) +
            $c10->getX()*$c13x2*$c13->getY()*(-6*$c20->getY()*$c22->getY() - 3*$c21y2) + $c20->getX()*$c13x2*$c13->getY()*(6*$c20->getY()*$c22->getY() + 3*$c21y2) +
            $c13x3*(-2*$c20->getY()*$c21y2 - $c20y2*$c22->getY() - $c20->getY()*(2*$c20->getY()*$c22->getY() + $c21y2));

        $coefs [] = -$c10->getX()*$c11->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY() + $c10->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY() + 6*$c10->getX()*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() -
            6*$c10->getY()*$c11->getX()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY() - $c10->getY()*$c11->getX()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() + $c10->getY()*$c11->getY()*$c12->getX()*$c21->getX()*$c13->getX()*$c13->getY() -
            $c11->getX()*$c11->getY()*$c12->getX()*$c21->getX()*$c12->getY()*$c13->getY() + $c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX()*$c21->getY() + $c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c21->getY()*$c13->getY() +
            6*$c11->getX()*$c12->getX()*$c20->getY()*$c13->getX()*$c21->getY()*$c13->getY() + $c11->getX()*$c20->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() - $c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c21->getY()*$c13->getY() -
            6*$c20->getX()*$c11->getY()*$c21->getX()*$c12->getY()*$c13->getX()*$c13->getY() - $c11->getY()*$c12->getX()*$c20->getY()*$c21->getX()*$c13->getX()*$c13->getY() - 6*$c10->getX()*$c20->getX()*$c21->getX()*$c13y3 -
            2*$c10->getX()*$c21->getX()*$c12y3*$c13->getX() + 6*$c10->getY()*$c20->getY()*$c13x3*$c21->getY() + 2*$c20->getX()*$c21->getX()*$c12y3*$c13->getX() + 2*$c10->getY()*$c12x3*$c21->getY()*$c13->getY() -
            2*$c12x3*$c20->getY()*$c21->getY()*$c13->getY() - 6*$c10->getX()*$c10->getY()*$c21->getX()*$c13->getX()*$c13y2 + 3*$c10->getX()*$c11->getX()*$c12->getX()*$c21->getY()*$c13y2 -
            2*$c10->getX()*$c11->getX()*$c21->getX()*$c12->getY()*$c13y2 - 4*$c10->getX()*$c11->getY()*$c12->getX()*$c21->getX()*$c13y2 + 3*$c10->getY()*$c11->getX()*$c12->getX()*$c21->getX()*$c13y2 +
            6*$c10->getX()*$c10->getY()*$c13x2*$c21->getY()*$c13->getY() + 6*$c10->getX()*$c20->getX()*$c13->getX()*$c21->getY()*$c13y2 - 3*$c10->getX()*$c11->getY()*$c12->getY()*$c13x2*$c21->getY() +
            2*$c10->getX()*$c12->getX()*$c21->getX()*$c12y2*$c13->getY() + 2*$c10->getX()*$c12->getX()*$c12y2*$c13->getX()*$c21->getY() + 6*$c10->getX()*$c20->getY()*$c21->getX()*$c13->getX()*$c13y2 +
            4*$c10->getY()*$c11->getX()*$c12->getY()*$c13x2*$c21->getY() + 6*$c10->getY()*$c20->getX()*$c21->getX()*$c13->getX()*$c13y2 + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c13x2*$c21->getY() -
            3*$c10->getY()*$c11->getY()*$c21->getX()*$c12->getY()*$c13x2 + 2*$c10->getY()*$c12->getX()*$c21->getX()*$c12y2*$c13->getX() - 3*$c11->getX()*$c20->getX()*$c12->getX()*$c21->getY()*$c13y2 +
            2*$c11->getX()*$c20->getX()*$c21->getX()*$c12->getY()*$c13y2 + $c11->getX()*$c11->getY()*$c21->getX()*$c12y2*$c13->getX() - 3*$c11->getX()*$c12->getX()*$c20->getY()*$c21->getX()*$c13y2 +
            4*$c20->getX()*$c11->getY()*$c12->getX()*$c21->getX()*$c13y2 - 6*$c10->getX()*$c20->getY()*$c13x2*$c21->getY()*$c13->getY() - 2*$c10->getX()*$c12x2*$c12->getY()*$c21->getY()*$c13->getY() -
            6*$c10->getY()*$c20->getX()*$c13x2*$c21->getY()*$c13->getY() - 6*$c10->getY()*$c20->getY()*$c21->getX()*$c13x2*$c13->getY() - 2*$c10->getY()*$c12x2*$c21->getX()*$c12->getY()*$c13->getY() -
            2*$c10->getY()*$c12x2*$c12->getY()*$c13->getX()*$c21->getY() - $c11->getX()*$c11->getY()*$c12x2*$c21->getY()*$c13->getY() - 4*$c11->getX()*$c20->getY()*$c12->getY()*$c13x2*$c21->getY() -
            2*$c11->getX()*$c11y2*$c21->getX()*$c13->getX()*$c13->getY() + 3*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2*$c21->getY() - 2*$c20->getX()*$c12->getX()*$c21->getX()*$c12y2*$c13->getY() -
            2*$c20->getX()*$c12->getX()*$c12y2*$c13->getX()*$c21->getY() - 6*$c20->getX()*$c20->getY()*$c21->getX()*$c13->getX()*$c13y2 - 2*$c11->getY()*$c12->getX()*$c20->getY()*$c13x2*$c21->getY() +
            3*$c11->getY()*$c20->getY()*$c21->getX()*$c12->getY()*$c13x2 - 2*$c12->getX()*$c20->getY()*$c21->getX()*$c12y2*$c13->getX() - $c11y2*$c12->getX()*$c21->getX()*$c12->getY()*$c13->getX() +
            6*$c20->getX()*$c20->getY()*$c13x2*$c21->getY()*$c13->getY() + 2*$c20->getX()*$c12x2*$c12->getY()*$c21->getY()*$c13->getY() + 2*$c11x2*$c11->getY()*$c13->getX()*$c21->getY()*$c13->getY() +
            $c11x2*$c12->getX()*$c12->getY()*$c21->getY()*$c13->getY() + 2*$c12x2*$c20->getY()*$c21->getX()*$c12->getY()*$c13->getY() + 2*$c12x2*$c20->getY()*$c12->getY()*$c13->getX()*$c21->getY() +
            3*$c10x2*$c21->getX()*$c13y3 - 3*$c10y2*$c13x3*$c21->getY() + 3*$c20x2*$c21->getX()*$c13y3 + $c11y3*$c21->getX()*$c13x2 - $c11x3*$c21->getY()*$c13y2 -
            3*$c20y2*$c13x3*$c21->getY() - $c11->getX()*$c11y2*$c13x2*$c21->getY() + $c11x2*$c11->getY()*$c21->getX()*$c13y2 - 3*$c10x2*$c13->getX()*$c21->getY()*$c13y2 +
            3*$c10y2*$c21->getX()*$c13x2*$c13->getY() - $c11x2*$c12y2*$c13->getX()*$c21->getY() + $c11y2*$c12x2*$c21->getX()*$c13->getY() - 3*$c20x2*$c13->getX()*$c21->getY()*$c13y2 +
            3*$c20y2*$c21->getX()*$c13x2*$c13->getY();

        $coefs [] = $c10->getX()*$c10->getY()*$c11->getX()*$c12->getY()*$c13->getX()*$c13->getY() - $c10->getX()*$c10->getY()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY() + $c10->getX()*$c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getY() -
            $c10->getY()*$c11->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getX() - $c10->getX()*$c11->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c13->getY() + 6*$c10->getX()*$c20->getX()*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY() +
            $c10->getX()*$c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY() - $c10->getY()*$c11->getX()*$c20->getX()*$c12->getY()*$c13->getX()*$c13->getY() - 6*$c10->getY()*$c11->getX()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY() +
            $c10->getY()*$c20->getX()*$c11->getY()*$c12->getX()*$c13->getX()*$c13->getY() - $c11->getX()*$c20->getX()*$c11->getY()*$c12->getX()*$c12->getY()*$c13->getY() + $c11->getX()*$c11->getY()*$c12->getX()*$c20->getY()*$c12->getY()*$c13->getX() +
            $c11->getX()*$c20->getX()*$c20->getY()*$c12->getY()*$c13->getX()*$c13->getY() - $c20->getX()*$c11->getY()*$c12->getX()*$c20->getY()*$c13->getX()*$c13->getY() - 2*$c10->getX()*$c20->getX()*$c12y3*$c13->getX() +
            2*$c10->getY()*$c12x3*$c20->getY()*$c13->getY() - 3*$c10->getX()*$c10->getY()*$c11->getX()*$c12->getX()*$c13y2 - 6*$c10->getX()*$c10->getY()*$c20->getX()*$c13->getX()*$c13y2 +
            3*$c10->getX()*$c10->getY()*$c11->getY()*$c12->getY()*$c13x2 - 2*$c10->getX()*$c10->getY()*$c12->getX()*$c12y2*$c13->getX() - 2*$c10->getX()*$c11->getX()*$c20->getX()*$c12->getY()*$c13y2 -
            $c10->getX()*$c11->getX()*$c11->getY()*$c12y2*$c13->getX() + 3*$c10->getX()*$c11->getX()*$c12->getX()*$c20->getY()*$c13y2 - 4*$c10->getX()*$c20->getX()*$c11->getY()*$c12->getX()*$c13y2 +
            3*$c10->getY()*$c11->getX()*$c20->getX()*$c12->getX()*$c13y2 + 6*$c10->getX()*$c10->getY()*$c20->getY()*$c13x2*$c13->getY() + 2*$c10->getX()*$c10->getY()*$c12x2*$c12->getY()*$c13->getY() +
            2*$c10->getX()*$c11->getX()*$c11y2*$c13->getX()*$c13->getY() + 2*$c10->getX()*$c20->getX()*$c12->getX()*$c12y2*$c13->getY() + 6*$c10->getX()*$c20->getX()*$c20->getY()*$c13->getX()*$c13y2 -
            3*$c10->getX()*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2 + 2*$c10->getX()*$c12->getX()*$c20->getY()*$c12y2*$c13->getX() + $c10->getX()*$c11y2*$c12->getX()*$c12->getY()*$c13->getX() +
            $c10->getY()*$c11->getX()*$c11->getY()*$c12x2*$c13->getY() + 4*$c10->getY()*$c11->getX()*$c20->getY()*$c12->getY()*$c13x2 - 3*$c10->getY()*$c20->getX()*$c11->getY()*$c12->getY()*$c13x2 +
            2*$c10->getY()*$c20->getX()*$c12->getX()*$c12y2*$c13->getX() + 2*$c10->getY()*$c11->getY()*$c12->getX()*$c20->getY()*$c13x2 + $c11->getX()*$c20->getX()*$c11->getY()*$c12y2*$c13->getX() -
            3*$c11->getX()*$c20->getX()*$c12->getX()*$c20->getY()*$c13y2 - 2*$c10->getX()*$c12x2*$c20->getY()*$c12->getY()*$c13->getY() - 6*$c10->getY()*$c20->getX()*$c20->getY()*$c13x2*$c13->getY() -
            2*$c10->getY()*$c20->getX()*$c12x2*$c12->getY()*$c13->getY() - 2*$c10->getY()*$c11x2*$c11->getY()*$c13->getX()*$c13->getY() - $c10->getY()*$c11x2*$c12->getX()*$c12->getY()*$c13->getY() -
            2*$c10->getY()*$c12x2*$c20->getY()*$c12->getY()*$c13->getX() - 2*$c11->getX()*$c20->getX()*$c11y2*$c13->getX()*$c13->getY() - $c11->getX()*$c11->getY()*$c12x2*$c20->getY()*$c13->getY() +
            3*$c20->getX()*$c11->getY()*$c20->getY()*$c12->getY()*$c13x2 - 2*$c20->getX()*$c12->getX()*$c20->getY()*$c12y2*$c13->getX() - $c20->getX()*$c11y2*$c12->getX()*$c12->getY()*$c13->getX() +
            3*$c10y2*$c11->getX()*$c12->getX()*$c13->getX()*$c13->getY() + 3*$c11->getX()*$c12->getX()*$c20y2*$c13->getX()*$c13->getY() + 2*$c20->getX()*$c12x2*$c20->getY()*$c12->getY()*$c13->getY() -
            3*$c10x2*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY() + 2*$c11x2*$c11->getY()*$c20->getY()*$c13->getX()*$c13->getY() + $c11x2*$c12->getX()*$c20->getY()*$c12->getY()*$c13->getY() -
            3*$c20x2*$c11->getY()*$c12->getY()*$c13->getX()*$c13->getY() - $c10x3*$c13y3 + $c10y3*$c13x3 + $c20x3*$c13y3 - $c20y3*$c13x3 -
            3*$c10->getX()*$c20x2*$c13y3 - $c10->getX()*$c11y3*$c13x2 + 3*$c10x2*$c20->getX()*$c13y3 + $c10->getY()*$c11x3*$c13y2 +
            3*$c10->getY()*$c20y2*$c13x3 + $c20->getX()*$c11y3*$c13x2 + $c10x2*$c12y3*$c13->getX() - 3*$c10y2*$c20->getY()*$c13x3 - $c10y2*$c12x3*$c13->getY() +
            $c20x2*$c12y3*$c13->getX() - $c11x3*$c20->getY()*$c13y2 - $c12x3*$c20y2*$c13->getY() - $c10->getX()*$c11x2*$c11->getY()*$c13y2 +
            $c10->getY()*$c11->getX()*$c11y2*$c13x2 - 3*$c10->getX()*$c10y2*$c13x2*$c13->getY() - $c10->getX()*$c11y2*$c12x2*$c13->getY() + $c10->getY()*$c11x2*$c12y2*$c13->getX() -
            $c11->getX()*$c11y2*$c20->getY()*$c13x2 + 3*$c10x2*$c10->getY()*$c13->getX()*$c13y2 + $c10x2*$c11->getX()*$c12->getY()*$c13y2 +
            2*$c10x2*$c11->getY()*$c12->getX()*$c13y2 - 2*$c10y2*$c11->getX()*$c12->getY()*$c13x2 - $c10y2*$c11->getY()*$c12->getX()*$c13x2 + $c11x2*$c20->getX()*$c11->getY()*$c13y2 -
            3*$c10->getX()*$c20y2*$c13x2*$c13->getY() + 3*$c10->getY()*$c20x2*$c13->getX()*$c13y2 + $c11->getX()*$c20x2*$c12->getY()*$c13y2 - 2*$c11->getX()*$c20y2*$c12->getY()*$c13x2 +
            $c20->getX()*$c11y2*$c12x2*$c13->getY() - $c11->getY()*$c12->getX()*$c20y2*$c13x2 - $c10x2*$c12->getX()*$c12y2*$c13->getY() - 3*$c10x2*$c20->getY()*$c13->getX()*$c13y2 +
            3*$c10y2*$c20->getX()*$c13x2*$c13->getY() + $c10y2*$c12x2*$c12->getY()*$c13->getX() - $c11x2*$c20->getY()*$c12y2*$c13->getX() + 2*$c20x2*$c11->getY()*$c12->getX()*$c13y2 +
            3*$c20->getX()*$c20y2*$c13x2*$c13->getY() - $c20x2*$c12->getX()*$c12y2*$c13->getY() - 3*$c20x2*$c20->getY()*$c13->getX()*$c13y2 + $c12x2*$c20y2*$c12->getY()*$c13->getX();

        $poly = new \Freesewing\Polynomial($coefs);

        $roots = $poly->getRootsInInterval(0, 1);

        for ($i=0; $i<count($roots); $i++) {
            $s = $roots[$i];
            $xP = new \Freesewing\Polynomial([
                $c13->getX(),
                $c12->getX(),
                $c11->getX(),
                $c10->getX() - $c20->getX() - $s*$c21->getX() - $s*$s*$c22->getX() - $s*$s*$s*$c23->getX()
            ]);
            $xRoots = $xP->getRoots();

            $yP = new \Freesewing\Polynomial([
                $c13->getY(),
                $c12->getY(),
                $c11->getY(),
                $c10->getY() - $c20->getY() - $s*$c21->getY() - $s*$s*$c22->getY() - $s*$s*$s*$c23->getY()
            ]);
            $yRoots = $yP->getRoots();

            if (count($xRoots) > 0 && count($yRoots) > 0) {
                $TOLERANCE = 0.0001;

                if (true) {
                    // Need a structure to break out of
                    for ($j=0; $j < count($xRoots); $j++) {
                        $xRoot = $xRoots[$j];

                        if (0 <= $xRoot && $xRoot <= 1) {
                            for ($k=0; $k < count($yRoots); $k++) {
                                if (abs($xRoot - $yRoots[$k]) < $TOLERANCE) {
                                    $j1  = $c21->multiply($s);
                                    $j2  = $j1->add($c20);
                                    $j3 = $c22->multiply($s*$s);
                                    $j4 = $j3->add($j2);
                                    $j5 = $c23->multiply($s*$s*$s);
                                    $points[] = $j5->add($j4);
                                    //break 4;
                                }
                            }
                        }
                    }
                }
            }
        }
        if (isset($points) && is_array($points)) {
            foreach ($points as $key => $point) {
                $intersections[$key] = $point->asPoint();
            }
            return $intersections;
        } else {
            return false;
        }
    }
}
