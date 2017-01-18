<?php
/** Freesewing\GrowingPacker class */
namespace Freesewing;

/**
 * Port to PHP of an existing binpacking algorithm
 *
 * Original by Jake Gordon <jake@codeincomplete.com>
 * Available at: http://codeincomplete.com/posts/bin-packing/
 * Jake's code is MIT-licensed, but he has kindly granted me
 * permission to distribute this port under GPLv3.
 * Thanks Jake!
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class GrowingPacker extends Packer
{
    /**
     * Fits a number of rectangles in a space
     *
     * @param array layoutBlocks An array of \freesewing\Layoutblock objects that we need to fit
     */
    public function fit(&$layoutBlocks)
    {
        $this->boundingBox = $this->getFirstBlock($layoutBlocks);

        foreach ($layoutBlocks as &$layoutBlock) {
            $space = $this->findSpace($this->boundingBox, $layoutBlock->w, $layoutBlock->h);
            if ($space) {
                $layoutBlock->fit = $this->splitSpace($space, $layoutBlock->w, $layoutBlock->h);
            } else {
                $layoutBlock->fit = $this->growSpace($layoutBlock->w, $layoutBlock->h);
            }
        }
    }

    /**
     * Repurposes the first \Freesewing\LayoutBlock as the space to fit the rest in
     *
     * We need to fit all these \Freesewing\LayoutBlock into a space, but we have
     * to start somewhere. So we take the first \Freesewing\LayoutBlock off the array, and
     * use that as our intial space to fit things in.
     * Obviously, this space will only fit the first block, but we'll grow the space later
     *
     * @param array layoutBlocks An array of \freesewing\Layoutblock objects that we need to fit
     *
     * @return \freesewing\LayoutBlock
     */
    private function getFirstBlock($layoutBlocks)
    {
        $box = array_shift($layoutBlocks);
        $box->setPosition(0, 0);
        $box->setUsed(false);

        return $box;
    }

    /**
     * Looks for a space to fit a [$w x $h] block in the boundingBox
     *
     * This will check to see if we have space to fit a block if width $w and height $h into $box.
     * If we do not, it will return null.
     * If we do, it will return the \Freesewing\LayoutBlock it found where we can place the block
     *
     * @param \Freesewing\LayoutBlock block The space to place the block into
     * @param float w The width of the block to place
     * @param float h The height of the block to place
     *
     * @return \Freesewing\LayoutBlock|null A LayoutBlock if we found a space to fit the block, or null
     */
    private function findSpace(&$block, $w, $h)
    {
        if ($block->isUsed()) {
            $space = $this->findSpace($block->right, $w, $h);
            if ($space) {
                return $space;
            }

            $space = $this->findSpace($block->down, $w, $h);
            if ($space) {
                return $space;
            }
        } else {
            if (($w <= $block->w) && ($h <= $block->h)) {
                return $block;
            }
        }

        return null;
    }

    /**
     * Splits a space down and to the right
     *
     * This is used after placing a block in a space.
     * The remaining room will be spit into what's to the right, and what's below.
     *
     * @param \Freesewing\LayoutBlock space The space we need to split
     * @param float w The width at which to split the space
     * @param float h The height at which to split the space
     *
     * @return \Freesewing\LayoutBlock|null A LayoutBlock if we found a space to fit the block, or null
     */
    private function splitSpace(&$space, $w, $h)
    {
        $space->setUsed(true);

        $space->down = new \Freesewing\LayoutBlock();
        $space->down->setPosition($space->x, $space->y + $h);
        $space->down->setSize($space->w, $space->h - $h);

        $space->right = new \Freesewing\LayoutBlock();
        $space->right->setPosition($space->x + $w, $space->y);
        $space->right->setSize($space->w - $w, $h);

        return $space;
    }

    /**
     * Makes the space bigger
     *
     * If we can't place a block, we're going to need
     * a bigger boat ^H^H^H^H^H^H^H^H^H^H^H^H more space
     * We can grow right or down, and try to stick to a 1/sqrt(2) ratio
     * because that's the ratio of DIN page sizes (A4 and such)
     *
     * @param float w The width of the space we're growing
     * @param float h The height of the space we're growing
     *
     * @return \Freesewing\LayoutBlock The bigger space
     */
    private function growSpace($w, $h)
    {
        $canGrowDown = ($w <= $this->boundingBox->w);
        $canGrowRight = ($h <= $this->boundingBox->h);

        // aim for 1/sqrt(2) ratio
        $shouldGrowRight = $canGrowRight && ($this->boundingBox->h * sqrt(2) >= ($this->boundingBox->w + $w));
        $shouldGrowDown = $canGrowDown && ($this->boundingBox->w >= ($this->boundingBox->h + $h) * sqrt(2));

        if ($shouldGrowRight) {
            return $this->growRight($w, $h);
        } elseif ($shouldGrowDown) {
            return $this->growDown($w, $h);
        } elseif ($canGrowRight) {
            return $this->growRight($w, $h);
        } elseif ($canGrowDown) {
            return $this->growDown($w, $h);
        } else {
            return null; /* We pre-sort to avoid this from happening */
        }
    }

    /**
     * Makes the space bigger towards the right
     *
     * @param float w The width of the space we're growing
     * @param float h The height of the space we're growing
     *
     * @return \Freesewing\LayoutBlock The bigger space
     */
    private function growRight($w, $h)
    {
        $down = $this->boundingBox;

        $right = new \Freesewing\LayoutBlock();
        $right->setPosition($this->boundingBox->w, 0);
        $right->setSize($w, $this->boundingBox->h);

        $newBoundingBox = new \Freesewing\LayoutBlock();

        $newBoundingBox->setUsed(true);
        $newBoundingBox->setPosition(0, 0);
        $newBoundingBox->setSize($this->boundingBox->w + $w, $this->boundingBox->h);
        $newBoundingBox->down = $down;
        $newBoundingBox->right = $right;

        $this->boundingBox = $newBoundingBox;

        $space = $this->findSpace($this->boundingBox, $w, $h);
        
        return $this->splitSpace($space, $w, $h);
    }

    /**
     * Makes the space bigger towards the bottom
     *
     * @param float w The width of the space we're growing
     * @param float h The height of the space we're growing
     *
     * @return \Freesewing\LayoutBlock The bigger space
     */
    private function growDown($w, $h)
    {
        $right = $this->boundingBox;

        $down = new \Freesewing\LayoutBlock();
        $down->setPosition(0, $this->boundingBox->h);
        $down->setSize($this->boundingBox->w, $h);

        $newBoundingBox = new \Freesewing\LayoutBlock();

        $newBoundingBox->setUsed();
        $newBoundingBox->setPosition(0, 0);
        $newBoundingBox->setSize($this->boundingBox->w, $this->boundingBox->h + $h);
        $newBoundingBox->down = $down;
        $newBoundingBox->right = $right;

        $this->boundingBox = $newBoundingBox;

        $space = $this->findSpace($this->boundingBox, $w, $h);
        
        return $this->splitSpace($space, $w, $h);
    }
}
