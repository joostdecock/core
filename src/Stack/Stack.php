<?php
/** Freesewing\Boundary class */
namespace Freesewing;

/**
 * A stack you can push data onto or replace 1 element with many
 *
 * This stack is used for offsetting paths. When doing so, we 
 * split a path into individual steps. Sometimes, we need to add
 * steps in between those steps (to fill gaps for example).
 * This stack class handles this for us. We can push data 
 * on a stack, or replace one element with several taking its place.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Stack
{
    /** @var array $items Items on the stack */
    public $items = array();

    /** @var array $intersections Intersections on the stack */
    public $intersections = array();

    /**
     * Adds items to the stack
     *
     * @param array items The items to add
     */
    public function push($items)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }

    /**
     * Adds intersection to the stack
     *
     * @param array intersections The intersection to add
     */
    public function addIntersection($intersection)
    {
            $this->intersections[] = $intersection;
    }

    /**
     * Replaces an item on the stack
     *
     * @param string $search The content of the item to replace
     * @param array $replace The replacement items
     */
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
