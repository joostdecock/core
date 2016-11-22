<?php
/** Freesewing\BezierToolbox class */
namespace Freesewing;

/**
 * Calculations involving Bezier curves.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BezierToolbox
{
    /** @var int Number of steps when walking a path */
    public static $steps = 100;

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
     * @return bool True if it is. False if it is not closed.
     */
    static function findBezierBoundary($start, $cp1, $cp2, $end)
    {
        $steps = BezierToolbox::$steps;
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
    static function findBezierEdge($start, $cp1, $cp2, $end, $direction='left')
    {
        $steps = BezierToolbox::$steps;
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
                    ($y < $edgeY && $direction == 'top') OR
                    ($y > $edgeY && $direction == 'bottom') OR
                    ($x < $edgeX && $direction == 'left') OR
                    ($x > $edgeX && $direction == 'right') 
                ) {
                    $edgeX = $x;
                    $edgeY = $y;
                }
            }
            $previousX = $x;
            $previousY = $y;
        }
        $edge = new \Freesewing\Point();
        $edge->setX($edgeX);
        $edge->setY($edgeY);

        return $edge;
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
    public static function cubicBezierLength($start, $cp1, $cp2, $end)
    {
        $length = 0;
        $steps = BezierToolbox::$steps;

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
     * Returns intersection between a curve and a line
     *
     * The number of intersections between a curve and a line 
     * varies. So we return an array of points.
     * 
     * @param \Freesewing\Point $lFrom The point at the start of the line
     * @param \Freesewing\Point $lTo The point at the end of the line
     * @param \Freesewing\Point $cFrom The point at the start of the curve
     * @param \Freesewing\Point $cC1 The first control point
     * @param \Freesewing\Point $cC2 The second control point
     * @param \Freesewing\Point $cTo The point at the end of the curve
     *
     * @return array|false An array of intersection points or false if there are none
     */
    public static function findLineCurveIntersections($lFrom, $lTo, $cFrom, $cC1, $cC2, $cTo)
    {
        $a1 = $lFrom->asVector();
        $a2 = $lTo->asVector();
        $p1 = $cFrom->asVector();
        $p2 = $cC1->asVector();
        $p3 = $cC2->asVector();
        $p4 = $cTo->asVector();
        
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
        for ($i=0 ; $i< count($roots) ; $i++) {
            $t = $roots[$i];

            if ( 0 <= $t && $t <= 1 ) {
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
                if ( $a1->getX() == $a2->getX() ) {
                    if ( $min->getY() <= $p10->getY() && $p10->getY() <= $max->getY() ) {
                        $points[] = $p10;
                    }
                } else if ( $a1->getY() == $a2->getY() ) {
                    if ( $min->getX() <= $p10->getX() && $p10->getX() <= $max->getX() ) {
                        $points[] = $p10;
                    }
                } else if ( $p10->gte($min) && $p10->lte($max) ) {
                    $points[] = $p10;
                }

            }
        }
        if(isset($points) && is_array($points)) {
            foreach($points as $key => $point) $intersections[$key] = $point->asPoint();
            return $intersections;
        }
        else return false;
    }
    
    /**
     * Returns intersection between a cubic Bezier and a line
     *
     * The number of intersections between a curve and a line 
     * varies. So we return an array of points.
     *
     * @deprecated This has been replaced and needs to be ripped out
     * 
     * @param \Freesewing\Point $lFrom The point at the start of the line
     * @param \Freesewing\Point $lTo The point at the end of the line
     * @param \Freesewing\Point $cFrom The point at the start of the curve
     * @param \Freesewing\Point $cC1 The first control point
     * @param \Freesewing\Point $cC2 The second control point
     * @param \Freesewing\Point $cTo The point at the end of the curve
     *
     * @return array|false An array of intersection points or false if there are none
     */
    public static function OLDfindLineCurveIntersections($lFrom, $lTo, $cFrom, $cC1, $cC2, $cTo)
    {
        $points = false;

        $X = array();

        $A = $lTo->getY() - $lFrom->getY(); // A=y2-y1
        $B = $lFrom->getX() - $lTo->getX(); // B=x1-x2
        $C = $lFrom->getX() * ($lFrom->getY() - $lTo->getY()) + $lFrom->getY() * ($lTo->getX() - $lFrom->getX()); // C=x1*(y1-y2)+y1*(x2-x1)

        $bx = BezierToolbox::bezierCoeffs($cFrom->getX(), $cC1->getX(), $cC2->getX(), $cTo->getX());
        $by = BezierToolbox::bezierCoeffs($cFrom->getY(), $cC1->getY(), $cC2->getY(), $cTo->getY());

        $P[0] = $A * $bx[0] + $B * $by[0];         /*t^3*/
        $P[1] = $A * $bx[1] + $B * $by[1];         /*t^2*/
        $P[2] = $A * $bx[2] + $B * $by[2];         /*t*/
        $P[3] = $A * $bx[3] + $B * $by[3] + $C;     /*1*/

        $r = BezierToolbox::cubicRoots($P);

        // Verify the roots are in bounds of the linear segment 
        for ($i = 0; $i < 3; ++$i) {
            $t = $r[$i];

            $X[0] = $bx[0] * $t * $t * $t + $bx[1] * $t * $t + $bx[2] * $t + $bx[3];
            $X[1] = $by[0] * $t * $t * $t + $by[1] * $t * $t + $by[2] * $t + $by[3];
            // above is intersection point assuming infinitely long line segment,
            // make sure we are also in bounds of the line
            if (($lTo->getX() - $lFrom->getX()) != 0) {           // if not vertical line
                $s = ($X[0] - $lFrom->getX()) / ($lTo->getX() - $lFrom->getX());
            } else {
                $s = ($X[1] - $lFrom->getY()) / ($lTo->getY() - $lFrom->getY());
            }

            // in bounds?
            if ($t < 0 || $t > 1.0 || $s < 0 || $s > 1.0) {
                $X[0] = -100;  // move off screen
                $X[1] = -100;
            } else {
                $I[$i] = $X;
            }
        }
        $i = 0;
        if(isset($I) AND is_array($I)) {
            foreach($I as $coords) {
                $point = new \Freesewing\Point();
                $point->setX($coords[0]);
                $point->setY($coords[1]);
                $points[] = $point;
            }
            return $points;
        }
        else return false;
    }
    
    /**
     * Returns coefficient of a point on a Bezier curve
     *
     * @param float $P0 Start value
     * @param float $P1 Control 1 value
     * @param float $P2 Control 2 value
     * @param float $P3 End value
     *
     * @return float $z The coefficient
     */
    public static function bezierCoeffs($P0, $P1, $P2, $P3)
    {
        $Z[0] = -1 * $P0 + 3 * $P1 + -3 * $P2 + $P3;
        $Z[1] = 3 * $P0 - 6 * $P1 + 3 * $P2;
        $Z[2] = -3 * $P0 + 3 * $P1;
        $Z[3] = $P0;

        return $Z;
    }

    /**
     * Returns the cubic roots for a Bezier curve
     *
     * @param array $P Array holding the coefficients
     *
     * @return array $t The sorted roots
     */
    public static function cubicRoots($P)
    {
        $a = $P[0];
        $b = $P[1];
        $c = $P[2];
        $d = $P[3];

        $A = $b / $a;
        $B = $c / $a;
        $C = $d / $a;

        $Q = (3 * $B - pow($A, 2)) / 9;
        $R = (9 * $A * $B - 27 * $C - 2 * pow($A, 3)) / 54;
        $D = pow($Q, 3) + pow($R, 2);    // polynomial discriminant

        if ($D >= 0) { // complex or duplicate roots
            $S = BezierToolbox::sgn($R + sqrt($D)) * pow(abs($R + sqrt($D)), 1 / 3);
            $T = BezierToolbox::sgn($R - sqrt($D)) * pow(abs($R - sqrt($D)), 1 / 3);

            $t[0] = -1 * $A / 3 + ($S + $T);    // real root
            $t[1] = -1 * $A / 3 - ($S + $T) / 2;  // real part of complex root
            $t[2] = -1 * $A / 3 - ($S + $T) / 2;  // real part of complex root
            $Im = abs(sqrt(3) * ($S - $T) / 2); // complex part of root pair

            /*discard complex roots*/
            if ($Im != 0) {
                $t[1] = -1;
                $t[2] = -1;
            }
        } else { // distinct real roots
            $th = acos($R / sqrt(pow($Q, 3) * -1));

            $t[0] = 2 * sqrt(-1 * $Q) * cos($th / 3) - $A / 3;
            $t[1] = 2 * sqrt(-1 * $Q) * cos(($th + 2 * pi()) / 3) - $A / 3;
            $t[2] = 2 * sqrt(-$Q) * cos(($th + 4 * pi()) / 3) - $A / 3;
            $Im = 0.0;
        }

        /*discard out of spec roots*/
        for ($i = 0; $i < 3; ++$i) {
            if ($t[$i] < 0 || $t[$i] > 1.0) {
                $t[$i] = -1;
            }
        }

        /*sort but place -1 at the end*/
        $t = BezierToolbox::sortSpecial($t);

        return $t;
    }
    
    /**
     * Returns the sign of number
     *
     * @param float $x Input number
     *
     * @return int 1|-1
     */
    public static function sgn($x)
    {
        if ($x < 0.0) return -1;

        return 1;
    }
    
    /**
     * Sorts, but places -1 at the end
     *
     * @param array $a The array to sort
     *
     * @return array $a The sorted array
     */
    public static function sortSpecial($a)
    {
        $flip;
        $temp;
        do {
            $flip = false;
            for ($i = 0; $i < count($a) - 1; ++$i) {
                if (($a[$i + 1] >= 0 && $a[$i] > $a[$i + 1]) || ($a[$i] < 0 && $a[$i + 1] >= 0)) {
                    $flip = true;
                    $temp = $a[$i];
                    $a[$i] = $a[$i + 1];
                    $a[$i + 1] = $temp;
                }
            }
        } while ($flip);

        return $a;
    }

    /**
     * Returns delta of split point on curve
     *
     * Approximate delta (between 0 and 1) of a point 'split' on
     * a Bezier curve 
     *
     * @param \Freesewing\Point $from Point at the start of the curve
     * @param \Freesewing\Point $cp1 Control point 1
     * @param \Freesewing\Point $cp2 Control point 2
     * @param \Freesewing\Point $to Point at the end of the curve
     * @param \Freesewing\Point $split The point to split on
     *
     * @return \Freesewing\Point The point where the curve crosses the Y-value
     */
    public static function cubicBezierDelta($from, $cp1, $cp2, $to, $split)
    {
        $steps = BezierToolbox::$steps;
        $best_t = null;
        $best_distance = false;
        $tmp = new \Freesewing\Point();
        for ($i = 0; $i <= $steps; ++$i) {
            $t = $i / $steps;
            $x = Utils::bezierPoint($t, $from->getX(), $cp1->getX(), $cp2->getX(), $to->getX());
            $y = Utils::bezierPoint($t, $from->getY(), $cp1->getY(), $cp2->getY(), $to->getY());
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
     * @param \Freesewing\Point $from The point at the start of the curve
     * @param \Freesewing\Point $cp1 The first control point
     * @param \Freesewing\Point $cp2 The second control point
     * @param \Freesewing\Point $to The point at the end of the curve
     * @param float $t The delta to split on, between 0 and 1
     *
     * @return array the 8 points resulting from the split
     */
    public static function calculateSplitCurvePoints($from, $cp1, $cp2, $to, $t)
    {
        $x1 = $from->getX();
        $y1 = $from->getY();
        $x2 = $cp1->getX();
        $y2 = $cp1->getY();
        $x3 = $cp2->getX();
        $y3 = $cp2->getY();
        $x4 = $to->getX();
        $y4 = $to->getY();

        $x12 = ($x2 - $x1) * $t + $x1;
        $y12 = ($y2 - $y1) * $t + $y1;

        $x23 = ($x3 - $x2) * $t + $x2;
        $y23 = ($y3 - $y2) * $t + $y2;

        $x34 = ($x4 - $x3) * $t + $x3;
        $y34 = ($y4 - $y3) * $t + $y3;

        $x123 = ($x23 - $x12) * $t + $x12;
        $y123 = ($y23 - $y12) * $t + $y12;

        $x234 = ($x34 - $x23) * $t + $x23;
        $y234 = ($y34 - $y23) * $t + $y23;

        $x1234 = ($x234 - $x123) * $t + $x123;
        $y1234 = ($y234 - $y123) * $t + $y123;

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
     * Takes radius of circle as input.
     */
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
     * Returns intersection between 2 cubic Bezier curves
     *
     * As the number of intersections between two curves
     * varies, we return an array of points.
     *
     * This implementation is based on the intersection 
     * procedures by Kevin Lindsey (http://www.kevlindev.com) 
     *
     * @param \Freesewing\Point $c1From The point at the start of the first curve
     * @param \Freesewing\Point $c1C1 The first control point of the first curve
     * @param \Freesewing\Point $c1C2 The second control point of the first curve
     * @param \Freesewing\Point $c1To The point at the end of the first curve
     * @param \Freesewing\Point $c2From The point at the start of the second curve
     * @param \Freesewing\Point $c2C1 The first control point of the second curve
     * @param \Freesewing\Point $c2C2 The second control point of the second curve
     * @param \Freesewing\Point $c2To The point at the end of the second curve
     *
     * @return array|false An array of intersection points or false if there are none
     */
    public static function findCurveCurveIntersections($c1From, $c1C1, $c1C2, $c1To, $c2From, $c2C1, $c2C2, $c2To)
    {
        $points = false;

        $a1 = $c1From->asVector();
        $a2 = $c1C1->asVector();
        $a3 = $c1C2->asVector();
        $a4 = $c1To->asVector();
        
        $b1 = $c2From->asVector();
        $b2 = $c2C1->asVector();
        $b3 = $c2C2->asVector();
        $b4 = $c2To->asVector();

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

        $roots = $poly->getRootsInInterval(0,1);

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

                if(true) { // Need a structure to break out of
                    for ($j=0; $j < count($xRoots); $j++) {
                        $xRoot = $xRoots[$j];
                        
                        if (0 <= $xRoot && $xRoot <= 1) {
                            for ($k=0;$k < count($yRoots);$k++) {
                                if (abs($xRoot - $yRoots[$k] ) < $TOLERANCE ) {
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
        if(isset($points) && is_array($points)) {
            foreach($points as $key => $point) $intersections[$key] = $point->asPoint();
            return $intersections;
        }
        else return false;
    }

}

