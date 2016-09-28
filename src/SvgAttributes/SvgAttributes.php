<?php

namespace Freesewing;

/**
 * Freesewing\SvgAttributes class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgAttributes extends SvgBlock
{
    public function load()
    {
        return "\n<svg\n    $this\n>\n";
    }
}
