<?php

namespace Freesewing;

/**
 * Freesewing\Boundary class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Boundary
{
    public $topLeft;
    public $bottomRight;
    public $width;
    public $height;
    public $maxSize;

    public function setTopLeft($point)
    {
        $this->topLeft = $point;
        $this->updateDimensions();
    }

    public function getTopLeft()
    {
        return $this->topLeft;
    }

    public function setBottomRight($point)
    {
        $this->bottomRight = $point;
        $this->updateDimensions();
    }

    public function getBottomRight()
    {
        return $this->bottomRight;
    }

    public function updateDimensions()
    {
        if(is_object($this->topLeft) && is_object($this->bottomRight)) {
            $this->width = $this->bottomRight->x - $this->topLeft->x;
            $this->height = $this->bottomRight->y - $this->topLeft->y;
            if($this->height > $this->width) $this->maxSize = $this->height;
            else $this->maxSize = $this->width;
        }
    }
}
