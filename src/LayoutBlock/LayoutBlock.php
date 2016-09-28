<?php

namespace Freesewing;

/**
 * Freesewing\LayoutBlock class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class LayoutBlock
{
    public $x = null;
    public $y = null;
    public $w = null;
    public $h = null;
    public $used = false;

    public function position($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
    
    public function size($w, $h) 
    {
        $this->w = $w;
        $this->h = $h;
    }

    public function used($used=true) 
    {
        $this->used = $used;
    }
}
