<?php

namespace Freesewing;

/**
 * Freesewing\Text class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Text
{
    public $anchor;
    public $text;
    public $attributes;

    public function setAnchor(\Freesewing\Point $anchor)
    {
        $this->anchor = $anchor;
    }
    
    public function setText($text)
    {
        $this->text = $text;
    }
    
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAnchor()
    {
        return $this->anchor;
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
