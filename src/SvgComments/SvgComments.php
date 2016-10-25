<?php
/** Freesewing\SvgComments class */
namespace Freesewing;

/**
 * Holds comments to go in the svg document.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgComments extends SvgBlock
{
    /**
     * Returns the comments as comment block
     *
     * Note that we are returning this as a string,
     * thereby it usesthe magic __toString() method
     * that is defined in the parent class
     *
     * @see \Freesewing\SvgBlock::__toString()
     *
     * @return string svg comment block
     */
    public function load()
    {
        if ($this->getData() === false) {
            return false;
        } else {
            return "\n<!--\n\n    $this\n\n  -->\n";
        }
    }
}
