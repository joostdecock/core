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
     * Returns vector dot product (aka scalar product)
     *
     * @param \Freesewing\Vector $that The second vector
     *
     * @return float The dot product
     */
    public function dot($that)
    {
        return ( $this->getX() * $that->getX() + $this->getY() * $that->getY() );
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

    /**
     * Linear interpolation
     *
     * @param \Freesewing\Vector $that The vector to interpolate with
     * @param float $t Position on Bezier curve between 0 and 1
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function lerp($that, $t)
    {
        $v = new \Freesewing\Vector();
        $v->setX($this->getX() + ( $that->getX() - $this->getX() ) * $t );
        $v->setY($this->getY() + ( $that->getY() - $this->getY() ) * $t );

        return $v;
    }

    /**
     * Top-left corner
     *
     * @param \Freesewing\Vector $that The second vector of our box
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function min($that)
    {
        $v = new \Freesewing\Vector();
        $v->setX( min( $this->getX(), $that->getX() ) );
        $v->setY( min( $this->getY(), $that->getY() ) );

        return $v;
    }

    /**
     * Bottom-right corner
     *
     * @param \Freesewing\Vector $that The second vector of our box
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function max($that)
    {
        $v = new \Freesewing\Vector();
        $v->setX( max( $this->getX(), $that->getX() ) );
        $v->setY( max( $this->getY(), $that->getY() ) );

        return $v;
    }

    /**
     * Checks whether point is to the right-botom
     *
     * @param \Freesewing\Vector $that The second vector of our box
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function gte($that)
    {
        return ($this->getX()>=$that->getX()&&$this->getY()>=$that->getY());
    }

    /**
     * Checks whether point is to the top-left
     *
     * @param \Freesewing\Vector $that The second vector of our box
     *
     * @return \Freesewing\Vector $v The new vector
     */
    public function lte($that)
    {
        return ($this->getX()<=$that->getX()&&$this->getY()<=$that->getY());
    }
}
