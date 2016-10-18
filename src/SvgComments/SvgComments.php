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
        if($this->getData() === false) return false;
        else return "\n<!--\n\n    $this\n\n  -->\n";
    }
}
