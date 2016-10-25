<?php
/** Freesewing\LayoutBlock class */
namespace Freesewing;

/**
 * A layout element used by the packer
 *
 * @see \Freesewing\GrowingPacker
 * 
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class LayoutBlock
{
    /** @var float x Position X-value  */
    public $x = null;

    /** @var float y Postition Y-value  */
    public $y = null;

    /** @var float w Width  */
    public $w = null;

    /** @var float h Height  */
    public $h = null;

    /** @var bool used Whether this block is in use or not  */
    private $used = false;

    /**
     * Sets the position 
     *
     * @param float x Position X-value
     * @param float y Position Y-value
     */
    public function setPosition($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Sets the size 
     *
     * @param float w Width
     * @param float h Height
     */
    public function setSize($w, $h)
    {
        $this->w = $w;
        $this->h = $h;
    }

    /**
     * Sets whether the block is used or not 
     *
     * @param bool used 
     */
    public function setUsed($used = true)
    {
        $this->used = $used;
    }

    /**
     * Return whether the block is used or not 
     *
     * @return bool True if it's used, false if not
     */
    public function isUsed()
    {
        if($this->used === true) return true;
        else return false;
    }

}
