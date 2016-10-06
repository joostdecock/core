<?php

namespace Freesewing\Patterns;

/**
 * Freesewing\Patterns\Pattern class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Pattern
{
    /**
     * @var array
     */
    private $options = array();

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $partMargin = 5;

    public function getOption($key)
    {
        return $this->options[$key];
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function setWidth(int $width)
    {
        $this->width = $width;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setHeight(int $height)
    {
        $this->height = $height;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setPartMargin(int $margin)
    {
        $this->partMargin = $margin;
    }

    public function clonePoints($from, $into)
    {
        foreach($this->parts[$from]->points as $key => $point) {
            $this->parts[$into]->addPoint($key, $point);
        }
    }
    
    public function getPartMargin()
    {
        return $this->partMargin;
    }

    public function pileParts()
    {
        if (isset($this->parts) && count($this->parts) > 0) {
            foreach ($this->parts as $part) {
                if($part->render) {
                    $offsetX = $part->boundary->topLeft->x * -1;
                    $offsetY = $part->boundary->topLeft->y * -1;
                    $transform = new \Freesewing\Transform('translate', $offsetX, $offsetY);
                    $part->addTransform('#pileParts', $transform);
                }
            }
        }
    }

    public function addBoundary()
    {
        foreach ($this->parts as $part) {
            if($part->render) {
                if (!@is_object($topLeft)) {
                    $topLeft = new \Freesewing\Point(
                        $part->boundary->topLeft->x,
                        $part->boundary->topLeft->y,
                        'Top-left pattern boundary');
                    $bottomRight = new \Freesewing\Point(
                        $part->boundary->bottomRight->x,
                        $part->boundary->bottomRight->y,
                        'Bottom-right pattern boundary');
                } else {
                    if ($part->boundary->topLeft->x < $topLeft->x) {
                        $topLeft->setX($part->boundary->topLeft->x);
                    }
                    if ($part->boundary->topLeft->y < $topLeft->y) {
                        $topLeft->setY($part->boundary->topLeft->y);
                    }
                    if ($part->boundary->bottomRight->x < $bottomRight->x) {
                        $bottomRight->setX($part->boundary->bottomRight->x);
                    }
                    if ($part->boundary->bottomRight->y < $bottomRight->y) {
                        $bottomRight->setY($part->boundary->bottomRight->y);
                    }
                }
            }
        }
        $this->boundary = new \Freesewing\Boundary($topLeft, $bottomRight);
    }

    public function addPart($key) {
        if(isset($this->parts[$key])) {
            throw new \InvalidArgumentException("Duplicate part key: $key");
        } else {
            if(is_numeric($key) || is_string($key)) {
                $part = new \Freesewing\Part();
                $this->parts[$key] = $part;
            }
        }
    }

    public function addPartBoundaries()
    {
        if (isset($this->parts) && count($this->parts) > 0) {
            foreach ($this->parts as $part) {
              if($part->render) $part->addBoundary($this->partMargin);
            }
        }
    }

    public function addOptions($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->options[$key] = $value;
            }
        }
    }

    public function draft($model, $svgDocument)
    {
    }

    public function layout()
    {
        $this->addPartBoundaries();
        $this->pileParts();
        
        $this->packer = new \Freesewing\GrowingPacker();
        $this->layoutBlocks = $this->layoutPreSort($this->parts);
        $this->packer->fit($this->layoutBlocks);
        $this->layoutTransforms($this->layoutBlocks);
    }
    
    public function cleanUp()
    {
        foreach($pattern->parts as $partKey => $part) {
            unset($part->tmp);
        }
    }

    private function layoutTransforms($layoutBlocks)
    {
        foreach ($layoutBlocks as $key => $layoutBlock) {
            $transform = new \Freesewing\Transform('translate', $layoutBlock->fit->x, $layoutBlock->fit->y);
            $this->parts[$key]->addTransform('#layout', $transform);
            if($layoutBlock->fit->rotated) {
                $transform = new \Freesewing\Transform('translate', $layoutBlock->h, 0);
                $this->parts[$key]->addTransform('#layoutRotateTranslate', $transform);
                $transform = new \Freesewing\Transform('rotate', 0, 0, 90);
                $this->parts[$key]->addTransform('#layoutRotate', $transform);
            }
        }
    }

    private function layoutPreSort($parts)
    {
        $order = array();
        foreach ($parts as $key => $part) {
            $order[$key] = $part->boundary->maxSize;
        }
        arsort($order);
        foreach ($order as $key => $maxSize) {
            $layoutBlock = new \Freesewing\LayoutBlock();
            $layoutBlock->w = $parts[$key]->boundary->width;
            $layoutBlock->h = $parts[$key]->boundary->height;
            $sorted[$key] = $layoutBlock;
        }
        return $sorted;
    
    }
}
