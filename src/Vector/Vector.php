<?php
/** Freesewing\Vector class */
namespace Freesewing;

/**
 * Holds data for a vector.
 *
 * This class is only used to help us determine the
 * intersections between two cubic Bezier curves.
 *
 * It provides basic vector operations.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Vector extends Coords
{
    /**
     * Clones a vector
     *
     * @param \Freesewing\Vector $source The vector to clone
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function __clone()
    {
        $v = new \Freesewing\Vector();
        $v->setX($this->getX());
        $v->setX($this->getY());

        return $v;
    }

    /**
     * Multiplies 2D vector coordinates
     *
     * @param float $value Value to multiply by
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function multiply($value)
    {
        $v = new \Freesewing\Vector();
        $v->setX($this->getX() * $value);
        $v->setY($this->getY() * $value);

        return $v;
    }

    /**
     * Add a point to anothe to get 2D vector coordinates
     *
     * @param \Freesewing\Vector $addMe The vector to add
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function add($addMe)
    {
        $v = new \Freesewing\Vector();
        $v->setX($this->getX() + $addMe->getX());
        $v->setY($this->getY() + $addMe->getY());

        return $v;
    }
    
    /**
     * Returns vector as point object
     *
     * @return \Freesewing\Point The new vector object
     */
    public function asPoint()
    {
        $point = new \Freesewing\Point();
        $point->setX($this->getX());
        $point->setY($this->getY());

        return $point;
    }
}
