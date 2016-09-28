<?php

namespace Freesewing;

/**
 * Freesewing\GrowingPacker class.
 *
 * This is a port to PHP of an existing binpacking algorithm
 *
 * Original by Jake Gordon <jake@codeincomplete.com>
 * Available at: http://codeincomplete.com/posts/bin-packing/
 * 
 * Jake's code is MIT-licensed, but he has kindly granted me 
 * permission to distribute this port under GPLv3. 
 *
 * Thanks Jake!
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class GrowingPacker extends Packer
{

    public $boundingBox;

    public function fit(&$layoutBlocks)
    {
        $this->boundingBox = $this->getFirstBlock($layoutBlocks);
       
        foreach ($layoutBlocks as &$layoutBlock) {
            $space = $this->findSpace($this->boundingBox, $layoutBlock->w, $layoutBlock->h);
            if ($space) $layoutBlock->fit = $this->splitSpace($space, $layoutBlock->w, $layoutBlock->h);
            else $layoutBlock->fit = $this->growSpace($layoutBlock->w, $layoutBlock->h);
        }
    }
   
    private function getFirstBlock($layoutBlocks)
    {
        $box = array_shift($layoutBlocks);
        $box->x = 0;
        $box->y = 0;
        $box->used = false;
        return $box;
    }
   
    private function findSpace(&$block, $w, $h)
    {
        if (@$block->used) {
            $space = $this->findSpace($block->right, $w, $h);
            if ($space) return $space;
            
            $space = $this->findSpace($block->down, $w, $h);
            if ($space) return $space;
        }
        else {
            if (($w <= $block->w) && ($h <= $block->h)) return $block;
            /* 
            $this->rotate($block);
            if (($w <= $block->w) && ($h <= $block->h)) return $block;
            $this->rotate($block);
            */
        }
        return null;
    }

    private function rotate(&$block)
    {
        $h = $block->h;
        $block->h = $block->w;
        $block->w = $h;
        if($block->rotated) $block->rotated(false);
        else $block->rotated(true);
    }
   
    private function splitSpace(&$space, $w, $h)
    {
        $space->used = true;
        
        $space->down = new \Freesewing\LayoutBlock();
        $space->down->position($space->x, $space->y + $h);
        $space->down->size($space->w, $space->h - $h);

        $space->right = new \Freesewing\LayoutBlock();
        $space->right->position($space->x + $w, $space->y);
        $space->right->size($space->w - $w, $h);
        
        return $space;
    }
   
    private function growSpace($w, $h)
    {
        $canGrowDown = ($w <= $this->boundingBox->w);
        $canGrowRight = ($h <= $this->boundingBox->h);

        // aim for 1/sqrt(2) ratio 
        $shouldGrowRight = $canGrowRight && ($this->boundingBox->h*sqrt(2) >= ($this->boundingBox->w + $w));
        $shouldGrowDown = $canGrowDown && ($this->boundingBox->w >= ($this->boundingBox->h + $h)*sqrt(2));
    
        if ($shouldGrowRight)     return $this->growRight($w, $h);
        else if ($shouldGrowDown) return $this->growDown($w, $h);
        else if ($canGrowRight)   return $this->growRight($w, $h);
        else if ($canGrowDown)    return $this->growDown($w, $h);
        else  return null; // if this happens, sort sizes first
    }

    private function growRight($w, $h)
    {
        $down = $this->boundingBox;
        
        $right = new \Freesewing\LayoutBlock();
        $right->position($this->boundingBox->w, 0);
        $right->size($w, $this->boundingBox->h);

        $newBoundingBox = new \Freesewing\LayoutBlock();

        $newBoundingBox->used();
        $newBoundingBox->position(0, 0);
        $newBoundingBox->size($this->boundingBox->w + $w, $this->boundingBox->h);
        $newBoundingBox->down = $down;
        $newBoundingBox->right = $right;

        $this->boundingBox = $newBoundingBox;

        $space = $this->findSpace($this->boundingBox, $w, $h);
        if ($space)  return $this->splitSpace($space, $w, $h);
        else return null;
    }

    private function growDown($w, $h)
    {
        $right = $this->boundingBox;
        
        $down = new \Freesewing\LayoutBlock();
        $down->position(0, $this->boundingBox->h);
        $down->size($this->boundingBox->w, $h);

        $newBoundingBox = new \Freesewing\LayoutBlock();
        
        $newBoundingBox->used();
        $newBoundingBox->position(0, 0);
        $newBoundingBox->size($this->boundingBox->w, $this->boundingBox->h + $h);
        $newBoundingBox->down = $down;
        $newBoundingBox->right = $right;

        $this->boundingBox = $newBoundingBox;
        
        $space = $this->findSpace($this->boundingBox, $w, $h);
        if ($space) return $this->splitSpace($space, $w, $h);
        else return null;
    }
}

