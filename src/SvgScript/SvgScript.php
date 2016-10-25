<?php
/** Freesewing\SvgScript class */

namespace Freesewing;

/**
 * Holds ECMA script for an SVG document.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgScript extends SvgBlock
{
    /**
     * Returns the ECAM script as a script block
     *
     * Note that we are returning this as a string,
     * using the magic __toString() method
     * which is defined in the parent class
     *
     * @see \Freesewing\SvgBlock::__toString()
     *
     * @return string svg style block
     */
    public function load()
    {
        if ($this->getData() === false) {
            return false;
        } else {
            return  "\n<script type=\"application/ecmascript\">\n    <![CDATA[\n    $this\n    ]]>\n</script>\n";
        }
    }
}
