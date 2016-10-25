<?php
/** Freesewing\Utils class */
namespace Freesewing;

/**
 * Utilities that do not depend on an instantiated object
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
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
     * @param string $data The input data
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

        return $return;
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
     * @param array $array The array of attributes
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
     * @param float $t Value between 0 and 1 to indicate how far along the curve we are
     * @param string $startval X or Y value for the start of the curve
     * @param string $cp1val X or Y  value for the first control point
     * @param string $cp2val X or Y value for the second control point
     * @param string $endval X or Y value for the end of the curve
     *
     * @see http://en.wikipedia.org/wiki/B%C3%A9zier_curve#Cubic_B.C3.A9zier_curves
     *
     * @return float The single-axis coordinate
     */
    public static function bezierPoint($t, $startval, $cp1val, $cp2val, $endval)
    {
      return $startval * (1.0 - $t) * (1.0 - $t) * (1.0 - $t)
        + 3.0 * $cp1val * (1.0 - $t) * (1.0 - $t) * $t
        + 3.0 * $cp2val * (1.0 - $t) * $t * $t
        + $endval * $t * $t * $t;
    }
    
    /**
     * Returns the class directory of the object passed to it
     *
     * @param $class The object for which to return the class directory
     *
     * @return string The directory path
     */
    public static function getClassDir($class)
    {
        $reflector = new \ReflectionClass(get_class($class));
        $filename = $reflector->getFileName();

        return dirname($filename);
    }
}
