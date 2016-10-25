<?php
/** Freesewing\Point class */
namespace Freesewing;

/**
 * Holds data for a point.
 *
 * This class has a precision property that will
 * round all point data to this precision.
 * By default it's 3, rounding everything
 * to 1/1000th of a mm, which is very accurate.
 * If you increase the precision, some operations 
 * (like path offset) will become rather expensive (slow)
 * because mathematically, they are approximations.
 * And making the approximation overly precise is just being 
 * difficult for the sake of being difficult.
 * For all intends and purposes, 1/1000th of a mm is way more
 * precice than you'll even be able to sew or cut.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Point
{
    /** * @var float $x X-coordinate of the point */
    public $x = null;
    
    /** @var float $y Y-coordinate of the point */
    public $y = null;
    
    /** @var int $precision Precision to round point coordinates on */
    public $precision = 3;
    
    /** @var string $description Point description */
    public $description = null;

    /**
     * Sets the X-coordinate for a point
     *
     * @param float $x The value to set
     */
    public function setX($x)
    {
        if (is_numeric($x)) {
            $this->x = round($x, $this->precision);
        } else {
            $x = 0;
        }
    }

    /**
     * Returns the X-coordinate for a point
     *
     * @return float The X-coordinate
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Sets the Y-coordinate for a point
     *
     * @param float $y The value to set
     */
    public function setY($y)
    {
        if (is_numeric($y)) {
            $this->y = round($y, $this->precision);
        } else {
            $y = 0;
        }
    }

    /**
     * Returns the Y-coordinate for a point
     *
     * @return float The Y-coordinate
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Sets the description property
     *
     * @param string $description The point description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the description property
     *
     * @return string The point description
     */
    public function getDescription()
    {
        return $this->description;
    }
}
