<?php

namespace Freesewing;

/**
 * Freesewing\SvgInclude class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgInclude
{
    private $content;

    public function set($content)
    {
        $this->content = $content;
    }

    public function get()
    {
        return $this->content;
    }
}
