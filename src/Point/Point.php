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
class Point extends Coords
{
    /** Precision to round point coordinates on */
    const PRECISION = 3;

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
            $this->x = round($x, self::PRECISION);
        } else {
            $x = 0;
        }
    }

    /**
     * Sets the Y-coordinate for a point
     *
     * @param float $y The value to set
     */
    public function setY($y)
    {
        if (is_numeric($y)) {
            $this->y = round($y, self::PRECISION);
        } else {
            $y = 0;
        }
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

    /**
     * Returns point as vector object
     *
     * @return \Freesewing\Vector The new vector object
     */
    public function asVector()
    {
        $vector = new \Freesewing\Vector();
        $vector->setX($this->getX());
        $vector->setY($this->getY());

        return $vector;
    }
}
