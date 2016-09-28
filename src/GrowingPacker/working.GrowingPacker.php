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

    public $root;
    private function getFirstBlock($blocks)
    {
        $box = array_shift($blocks);
        $box->x = 0;
        $box->y = 0;
        $box->used = false;
        return $box;
    }
   
    // Note that $blocks needs to be converted to a StdObject temporarily
    // as arrays cannot be properly passed by reference. This algorithm
    // cannot work in PHP using arrays.
   
    public function fit(&$blocks)
    {
        $this->root = $this->getFirstBlock($blocks);
       
        foreach ($blocks as &$block) {
            $node = $this->findNode($this->root, $block->w, $block->h);
           
            if ($node) {
                $block->fit = $this->splitNode($node, $block->w, $block->h);
            }
            else {
                $block->fit = $this->growNode($block->w, $block->h);
            }
        }
    }
   
    public function findNode(&$root, $w, $h)
    {
        if (@$root->used) {
            $node = $this->findNode($root->right, $w, $h);
            if ($node) {
                return $node;
            }
            $node = $this->findNode($root->down, $w, $h);
            if ($node) {
                return $node;
            }
        }
        else
        if (($w <= $root->w) && ($h <= $root->h)) {
            return $root;
        }
        else {
            return null;
        }
    }
   
    public function splitNode(&$node, $w, $h)
    {
        $node->used = true;
        $node->down = (object)array(
            'x' => $node->x,
            'y' => $node->y + $h,
            'w' => $node->w,
            'h' => $node->h - $h,
        );
        $node->right = (object)array(
            'x' => $node->x + $w,
            'y' => $node->y,
            'w' => $node->w - $w,
            'h' => $h,
        );
        return $node;
    }
   
    public function growNode($w, $h)
    {
        $canGrowDown = ($w <= $this->root->w);
        $canGrowRight = ($h <= $this->root->h);
       
        $shouldGrowRight = $canGrowRight && ($this->root->h >= ($this->root->w + $w));
        $shouldGrowDown = $canGrowDown && ($this->root->w >= ($this->root->h + $h));
        if ($shouldGrowRight) {
            return $this->growRight($w, $h);
        }
        else
        if ($shouldGrowDown) {
            return $this->growDown($w, $h);
        }
        else
        if ($canGrowRight) {
            return $this->growRight($w, $h);
        }
        else
        if ($canGrowDown) {
            return $this->growDown($w, $h);
        }
        else {
            // if this happens, sort sizes first
            return null;
        }
    }
    public function growRight($w, $h)
    {
        $this->root = (object)array(
            'used' => true,
            'x' => 0,
            'y' => 0,
            'w' => $this->root->w + $w,
            'h' => $this->root->h,
            'down' => $this->root,
            'right' => (object)array(
                'x' => $this->root->w,
                'y' => 0,
                'w' => $w,
                'h' => $this->root->h,
            )
        );
        $node = $this->findNode($this->root, $w, $h);
        if ($node) {
            return $this->splitNode($node, $w, $h);
        } else {
            return null;
        }
    }
    public function growDown($w, $h)
    {
        $this->root = (object)array(
            'used' => true,
            'x' => 0,
            'y' => 0,
            'w' => $this->root->w,
            'h' => $this->root->h + $h,
            'down' => $this->root,
            'right' => (object)array(
                'x' => 0,
                'y' => $this->root->h,
                'w' => $this->root->w,
                'h' => $h,
            )
        );
        $node = $this->findNode($this->root, $w, $h);
        if ($node) {
            return $this->splitNode($node, $w, $h);
        } else {
            return null;
        }
    }
}

