<?php

namespace Freesewing;

/**
 * Freesewing\SvgScript class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgScript extends SvgBlock
{
    public function load()
    {
        if($this->getData() === false) return false;
        else return  "\n<script type=\"application/ecmascript\">\n    <![CDATA[\n    $this\n    ]]>\n</script>\n";
    }
}
