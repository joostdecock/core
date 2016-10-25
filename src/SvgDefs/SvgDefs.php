<?php
/** Freesewing\SvgDefs class */
namespace Freesewing;

/**
 * Holds defs (definitions) for an SVG document.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgDefs extends SvgBlock
{
    /**
     * Returns the object data as a sv defs block
     *
     * Note that we are returning this as a string,
     * using the magic __toString() method
     * which is defined in the parent class
     *
     * @see \Freesewing\SvgBlock::__toString()
     *
     * @return string svg defs block
     */
    public function load()
    {
        if ($this->getData() === false) {
            return false;
        } else {
            return "\n<defs id=\"defs\">\n    $this\n</defs>\n";
        }
    }
}
