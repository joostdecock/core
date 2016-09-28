<?php

namespace Freesewing;

/**
 * Freesewing\SvgComments class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgComments extends SvgBlock
{
    public function load()
    {
        return "\n<!--$this\n  -->\n";
    }
}
