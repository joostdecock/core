<?php

namespace Freesewing;

/**
 * Freesewing\Point class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Point
{
    public $x = null;
    public $y = null;
    public $precision = 3;
    public $description = null;

    public function setX($x)
    {
        if (is_numeric($x)) {
            $this->x = round($x, $this->precision);
        } else {
            $x = 0;
        }
    }

    public function getX()
    {
        return $this->x;
    }

    public function setY($y)
    {
        if (is_numeric($y)) {
            $this->y = round($y, $this->precision);
        } else {
            $y = 0;
        }
    }

    public function getY()
    {
        return $this->y;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
