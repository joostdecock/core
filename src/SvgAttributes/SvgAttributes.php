<?php
/** Freesewing\SvgAttributes class */
namespace Freesewing;

/**
 * Holds attributes or the svg tag of an SVG document
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgAttributes extends SvgBlock
{
    /**
     * Returns the full svg tag
     *
     * Note that we are returning this as a string,
     * using the magic __toString() method
     * which is defined in the parent class
     *
     * @see \Freesewing\SvgBlock::__toString()
     *
     * @return string svg tag
     */
    public function load()
    {
        return "\n<svg\n    $this\n>\n";
    }
}
