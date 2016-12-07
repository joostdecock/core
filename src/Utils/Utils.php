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
     * Returns single-axis coordinate of a point along a cubic Bezier curve
     *
     * This returns the coordinate for a point $t into a curve
     * $t is between 0 and 1
     *
     * @param float  $t        Value between 0 and 1 to indicate how far along the curve we are
     * @param string $startval X or Y value for the start of the curve
     * @param string $cp1val   X or Y  value for the first control point
     * @param string $cp2val   X or Y value for the second control point
     * @param string $endval   X or Y value for the end of the curve
     *
     * @see http://en.wikipedia.org/wiki/B%C3%A9zier_curve#Cubic_B.C3.A9zier_curves
     *
     * @return float The single-axis coordinate
     */
    public static function bezierPoint($t, $startval, $cp1val, $cp2val, $endval)
    {
        return $startval * (1.0 - $t) * (1.0 - $t) * (1.0 - $t) + 3.0 * $cp1val * (1.0 - $t) * (1.0 - $t) * $t + 3.0 * $cp2val * (1.0 - $t) * $t * $t + $endval * $t * $t * $t;
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
     * Finds intersection between two (endless) lines
     *
     * @param Point $point1 The id of the start of line A
     * @param Point $point2 The id of the end line A
     * @param Point $point3 The id of the start of line B
     * @param Point $point4 The id of the end line B
     *
     * @return array|null The coordinates of the intersection, or null if the lines are parallel
     */
    public static function findLineLineIntersection(Point $point1, Point $point2, Point $point3, Point $point4)
    {
        /* weed out parallel lines */
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
     * Close enough means less than 0.01 mm difference between their coordinates on each axis.
     *
     * @param Point $point1 Point 1
     * @param Point $point2 Point 2
     *
     * @return bool True is they are the same. False if not.
     */
    public static function isSamePoint($point1, $point2)
    {
        if (round($point1->getX(), 2) == round($point2->getX(), 2) && round($point1->getY(), 2) == round($point2->getY(), 2)) {
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
    public function distance($point1, $point2)
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
}
