<?php

namespace Freesewing;

/**
 * Freesewing\SvgDefs class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgDefs extends SvgBlock
{
    public function load()
    {
        return "\n<defs id=\"defs\">\n    $this\n</defs>\n";
    }
}
