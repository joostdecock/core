<?php

namespace Freesewing;

/**
 * Freesewing\Text class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TextOnPath
{
    public $path;
    public $text;
    public $attributes;

    public function setPath(\Freesewing\Path $path)
    {
        $this->path = $path;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}
