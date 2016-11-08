<?php
/** Freesewing\Coords class */
namespace Freesewing;

/**
 * An object to hold coordinates.
 *
 * A generic class for coordinates, 
 * extended by Point and Vector
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Coords
{
    /** * @var float $x X-coordinate */
    public $x = null;
    
    /** @var float $y Y-coordinate */
    public $y = null;
    
    /**
     * Sets the X-coordinate 
     *
     * @param float $x The value to set
     */
    public function setX($x)
    {
        if (is_numeric($x)) $this->x = $x;
        else $x = 0;
    }

    /**
     * Sets the Y-coordinate
     *
     * @param float $y The value to set
     */
    public function setY($y)
    {
        if (is_numeric($y)) $this->y = $y;
        else $y = 0;
    }

    /**
     * Returns the X-coordinate
     *
     * @return float The X-coordinate
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Returns the Y-coordinate 
     *
     * @return float The Y-coordinate
     */
    public function getY()
    {
        return $this->y;
    }

}
