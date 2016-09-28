<?php

namespace Freesewing;

/**
 * Freesewing\SvgCss class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgCss extends SvgBlock
{
    public function load()
    {
        return  "\n<style type=\"text/css\">\n    <![CDATA[\n    $this\n    ]]>\n</style>\n";
    }
}
