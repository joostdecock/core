<?php

namespace Freesewing;

/**
 * Freesewing\Note class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Note extends Text
{
    public $points;
    public $path;

    public function setPoints($points)
    {
        $this->points = $points;
    }

    public function setPath(\Freesewing\Path $path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
