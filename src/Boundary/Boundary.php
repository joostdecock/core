<?php
/** Freesewing\Boundary class */
namespace Freesewing;

/**
 * Bounding box in which a path or part is contained.
 *
 * Holds a top left and bottom right coordinate and keeps width and height properties
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Boundary
{
    /** @var \Freesewing\Point topLeft Top-left point of the boundary */
    public $topLeft;

    /** @var \Freesewing\Point bottomRight Bottom-right point of the boundary */
    public $bottomRight;

    /** @var float width Width of the boundary */
    public $width;

    /** @var float height Height of the boundary */
    public $height;

    /**
     * Sets the topLeft property and updates width and height.
     *
     * @param \Freesewing\Point point
     */
    public function setTopLeft($point)
    {
        $this->topLeft = $point;
        $this->updateDimensions();
    }

    /**
     * Sets the bottomRight property and updates width and height.
     *
     * @param \Freesewing\Point point
     */
    public function setBottomRight($point)
    {
        $this->bottomRight = $point;
        $this->updateDimensions();
    }

    /**
     * Returns the topLeft property.
     *
     * @return \Freesewing\Point
     */
    public function getTopLeft()
    {
        return $this->topLeft;
    }

    /**
     * Returns the bottomRight property.
     *
     * @return \Freesewing\Point
     */
    public function getBottomRight()
    {
        return $this->bottomRight;
    }

    /**
     * Calculates boundary dimensions and updates width and height properties.
     */
    private function updateDimensions()
    {
        if (is_object($this->topLeft) && is_object($this->bottomRight)) {
            $this->width = $this->bottomRight->x - $this->topLeft->x;
            $this->height = $this->bottomRight->y - $this->topLeft->y;
            if ($this->height > $this->width) {
                $this->maxSize = $this->height;
            } else {
                $this->maxSize = $this->width;
            }
        }
    }
}
