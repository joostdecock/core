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
     * @see http://www.particleincell.com/blog/2013/cubic-line-intersection/
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
        foreach($I as $coords) {
            $point = new \Freesewing\Point();
            $point->setX($coords[0]);
            $point->setY($coords[1]);
            $points[] = $point;
        }

        return $points;
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
        for ($i = 0; $i <= $steps; ++$i) {
            $t = $i / $steps;
            $x = Utils::bezierPoint($t, $from->getX(), $cp1->getX(), $cp2->getX(), $to->getX());
            $y = Utils::bezierPoint($t, $from->getY(), $cp1->getY(), $cp2->getY(), $to->getY());
            $distance = hopLen($split, array($x, $y));
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
}
