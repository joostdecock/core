<?php
/** Freesewing\Utils class */
namespace Freesewing;

use Freesewing\Point;

/**
 * Utilities that do not depend on an instantiated object
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Utils
{

    /**
     * Like PHP's explode() but weeds out empties
     *
     * This takes a string and explodes is by the seperator.
     * It then weeds out empty elements in the array, and strips spaces from the start
     * and end of the non-empty elements.
     *
     * @param string $data      The input data
     * @param string $seperator The seperator for the explode
     *
     * @return array The scrubbed array
     */
    public static function asScrubbedArray($data, $separator = ' ')
    {
        $array = explode($separator, $data);
        foreach ($array as $value) {
            if (rtrim($value) != '') {
                $return[] = rtrim($value);
            }
        }
        if (isset($return)) {
            return $return;
        } else {
            return false;
        }
    }

    /**
     * Like asScrubbedArray but weeds out path operations
     *
     * This takes a string and explodes is by spaces.
     * It then weeds out empty elements in the array, as well as
     * allowed path command (C, M, L, z) and strips spaces from the start
     * and end of the non-empty elements.
     *
     * It is essentially used to turn a pathstring into an array of
     * points used in that pathstring
     *
     * @param string $data      The input data
     *
     * @return array The scrubbed array
     */
    public static function asPointArray($data)
    {
        $array = explode(' ', $data);
        foreach ($array as $value) {
            $chunk = rtrim($value);
            if (Utils::isAllowedPathCommand($chunk) === false && $chunk != '') {
                $return[$value] = rtrim($value);
            }
        }
        if (isset($return)) {
            return (array_keys($return));
        } else {
            return false;
        }
    }

    /**
     * Verifies that a path command is one of the supported path commands
     *
     * @param string $command The path command to verify
     *
     * @return bool true|false True if the command is allowed. False if not.
     */
    public static function isAllowedPathCommand($command)
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

    /**
     * Flattens an array of attributes into attribute markup, optionally filtering out attributes
     *
     * This will take an array of key=>value pairs of attributes and join them in
     * a string that is suitable attribute markup.
     * In addition to that, you can pass it an array of attributes to filter out.
     * This is used by the renderText method to filter out the 'line-height' attribute
     * which is not an SVG attribute, but something we mimic the behavior of.
     *
     * @see \Freesewing\SvgRenderbot::renderText()
     *
     * @param array $array  The array of attributes
     * @param array $remove An array of attributes to filter out
     *
     * @return string The attributes markup
     */
    public static function flattenAttributes($array, $remove = array())
    {
        if (!is_array($array)) {
            return null;
        }
        $attributes = '';
        foreach ($array as $key => $value) {
            if (!in_array($key, $remove)) {
                $attributes .= "$key=\"$value\" ";
            }
        }

        return $attributes;
    }

    /**
     * Returns the class directory of the object passed to it
     *
     * @param object $class The object for which to return the class directory
     *
     * @return string The directory path
     */
    public static function getClassDir($class)
    {

        $reflector = new \ReflectionClass(get_class($class));
        $filename = $reflector->getFileName();

        return dirname($filename);
    }

    /**
     * Returns the directory in which freesewing was installed
     *
     * @return string
     */
    public static function getApiDir()
    {
        return realpath(".");
    }

    /**
     * Finds intersection between two (endless) lines
     *
     * @param Point $point1 The id of the start of line A
     * @param Point $point2 The id of the end line A
     * @param Point $point3 The id of the start of line B
     * @param Point $point4 The id of the end line B
     *
     * @return array|null The coordinates of the intersection, or null if the lines are parallel
     */
    public static function lineLineIntersection(Point $point1, Point $point2, Point $point3, Point $point4)
    {
        /* weed out parallel lines */
        // FIXME: This does not seem to make sense. Parallel slope check at the end of this method might be enough.
        if ($point1->getX() == $point2->getX() && $point3->getX() == $point4->getX()) {
            return false;
        }
        if ($point1->getY() == $point2->getY() && $point3->getY() == $point4->getY()) {
            return false;
        }

        /* If line is vertical, handle this special case */
        if ($point1->getX() == $point2->getX()) {
            $slope = Utils::getSlope($point3, $point4);
            $i = $point3->getY() - ($slope * $point3->getX());
            $x = $point1->getX();
            $y = $slope * $x + $i;
        } elseif ($point3->getX() == $point4->getX()) {
            $slope = Utils::getSlope($point1, $point2);
            $i = $point1->getY() - ($slope * $point1->getX());
            $x = $point3->getX();
            $y = $slope * $x + $i;
        } else {
            /* If line goes from right to left, swap points */
            if ($point1->getX() > $point2->getX()) {
                $tmp = $point1;
                $point1 = $point2;
                $point2 = $tmp;
            }
            if ($point3->getX() > $point4->getX()) {
                $tmp = $point3;
                $point3 = $point4;
                $point4 = $tmp;
            }
            /* Find slope */
            $slope1 = Utils::getSlope($point1, $point2);
            $slope2 = Utils::getSlope($point3, $point4);
            /* Find y intercept */
            $i1 = $point1->getY() - ($slope1 * $point1->getX());
            $i2 = $point3->getY() - ($slope2 * $point3->getX());
            
            // We're not parallel are we?
            if($slope1 == $slope2) return false;
            
            /* Find intersection */
            $x = ($i2 - $i1) / ($slope1 - $slope2);
            $y = $slope1 * $x + $i1;
        }

        return [$x, $y];
    }

    /**
     * Returns the slope of a line
     *
     * @param Point $point1 The point at the start of the line
     * @param Point $point2 The point at the end of the line
     *
     * @return float slope of the line
     */
    public static function getSlope($point1, $point2)
    {
        return ($point2->getY() - $point1->getY()) / ($point2->getX() - $point1->getX());
    }

    /**
     * Checks whether two points are (almost) the same.
     *
     * Checks whether two points are the same, or close enough to be considered the same.
     * Close enough means less than 0.1 mm difference between their coordinates on each axis.
     *
     * @param Point $point1 Point 1
     * @param Point $point2 Point 2
     *
     * @return bool True is they are the same. False if not.
     */
    public static function isSamePoint(Point $point1, Point $point2)
    {
        if (round($point1->getX(), 1) == round($point2->getX(), 1) && round($point1->getY(), 1) == round($point2->getY(), 1)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the distance between two points
     *
     * @param Point $point1 The first point
     * @param Point $point2 The second point
     *
     * @return float Distance between the points
     */
    public static function distance($point1, $point2)
    {
        $deltaX = $point1->getX() - $point2->getX();
        $deltaY = $point1->getY() - $point2->getY();

        return sqrt(pow($deltaX, 2) + pow($deltaY, 2));
    }

    /**
     * Returns kint formated debug for the data passed to it
     *
     * @param string $debug The kint formatted debug
     *
     * @return string
     */
    public static function debug($data)
    {
        ob_start();
        \Kint::$maxLevels = 0;
        \Kint::dump($data);
        return ob_get_clean();
    }

    /**
     * Returns a slug for a given string
     *
     * @param string $string The input string
     *
     * @return string
     */
    public static function slug($string)
    {
        return preg_replace('~[^\pL\d]+~u', '-', $string);
    }
    
    /**
     * Returns value that is within min and max boundaries
     *
     * @param float $value The value to check
     * @param float $min The minimum
     * @param float $max The maximum
     */
    public static function constraint($value, $min, $max)
    {
        if($value < $min) return $min;
        if($value > $max) return $max;

        return $value;
    }
    
    /** 
     * Finds intersection points of two circles 
     *
     * @see http://paulbourke.net/geometry/circlesphere/
     *
     * @param point $c1 Center of first circle
     * @param float $r1 Radius of first circle
     * @param point $c2 Center of second circle
     * @param float $r2 Radius of second circle
     *
     * @return array An array of points objects of the intersections
     * */
    public static function circleCircleIntersections($c1, $r1, $c2, $r2, $sort='x')
    {
        // First circle
        $x1 = $c1->getX();
        $y1 = $c1->getY();
        
        // Second circle
        $x2 = $c2->getX();
        $y2 = $c2->getY();

        // Distance between centers
        $dx = $x2 - $x1;
        $dy = $y2 - $y1;
        $d = \Freesewing\Utils::distance($c1, $c2);

        // Check for edge cases
        if ($d > ($r1 + $r2)) return false; // Circles do not intersect
        if ($d < ($r2 - $r1)) return false; // One circle is contained in the other
        if ($d == 0 && $r1 == $r2) return false; // Two circles are identical

        $chorddistance = ( pow($r1,2) - pow($r2,2) + pow($d,2) ) / (2 * $d);
        $halfchordlength = sqrt(pow($r1,2) - pow($chorddistance,2));
        $chordmidpointx = $x1 + ($chorddistance*$dx)/$d;
        $chordmidpointy = $y1 + ($chorddistance*$dy)/$d;
        $i1 = new \Freesewing\Point();
        $i2 = new \Freesewing\Point();
        $i1->setX($chordmidpointx + ($halfchordlength*$dy)/$d);
        $i1->setY($chordmidpointy - ($halfchordlength*$dx)/$d);
        $i2->setX($chordmidpointx - ($halfchordlength*$dy)/$d);
        $i2->setY($chordmidpointy + ($halfchordlength*$dx)/$d);

        if( ($sort == 'x' && $i1->getX() <= $i2->getX()) || ($sort == 'y' && $i1->getY() <= $i2->getY() )) {
            return [$i1, $i2];
        } else {
            return [$i2, $i1];
        }
    }
    
    /** 
     * Finds intersection points of a circle and line segment 
     *
     * @see http://csharphelper.com/blog/2014/09/determine-where-a-line-intersects-a-circle-in-c/
     *
     * @param point  $c   The center of the circle
     * @param float  $r   The radius of the first circle
     * @param point  $p1  The start point of the line
     * @param point  $p2  The end point of the line
     * @param string  $sort The axis to sort results by, either x (default) or y
     *
     * @return array An array of points objects of the intersections
     * */
    public static function circleLineIntersections($c, $r, $p1, $p2, $sort='x')
    {
        $dx = $p2->getX() - $p1->getX();
        $dy = $p2->getY() - $p1->getY();

        $A = pow($dx,2) + pow($dy,2);
        $B = 2 * ($dx * ($p1->getX() - $c->getX()) + $dy * ($p1->getY() - $c->getY()));
        $C = pow(($p1->getX() - $c->getX()),2) + pow(($p1->getY() - $c->getY()),2) - pow($r,2);
    
        $det = pow($B,2) - 4 * $A * $C;
    
        if (($A <= 0.0000001) || ($det < 0)) return false; // No real solutions
        else if ($det == 0) { // One solution
            $t = (-1 * $B) / (2 * $A);
            $i1 = new \Freesewing\Point();
            $i1->setX($p1->getX() + $t * $dx);
            $i1->setY($p1->getY() + $t * $dy);
            return [$i1];
        } else { // Two solutions
            $i1 = new \Freesewing\Point();
            $i2 = new \Freesewing\Point();
            $t = (((-1 * $B) + sqrt($det)) / (2 * $A));
            $i1->setX($p1->getX() + $t * $dx);
            $i1->setY($p1->getY() + $t * $dy);
            $t = (((-1 * $B) - sqrt($det)) / (2 * $A));
            $i2->setX($p1->getX() + $t * $dx);
            $i2->setY($p1->getY() + $t * $dy);
            if( ($sort == 'x' && $i1->getX() <= $i2->getX()) || ($sort == 'y' && $i1->getY() <= $i2->getY() )) {
                return [$i1, $i2];
            } else {
                return [$i2, $i1];
            }
        }
    }
}
