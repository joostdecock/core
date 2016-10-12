<?php

namespace Freesewing;

/**
 * Freesewing\Stack class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Stack
{
    public $items = array();

    public function push($items)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }

    public function replace($search, $replace)
    {
        $targetKey = false;
        foreach ($this->items as $key => $item) {
            if ($item == $search) {
                $targetKey = $key;
            }
        }
        if ($targetKey !== false) {
            array_splice($this->items, $targetKey, 1, $replace);
        }
    }
}
