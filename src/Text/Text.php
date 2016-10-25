<?php
/** Freesewing\Boundary class */
namespace Freesewing;

/**
 * Holds text to be placed on a pattern.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Text
{
    /** @var \Freesewing\Point $anchor Point to anchor the text on */
    public $anchor;
    
    /** @var string $text The text itself */
    public $text;

    /** @var array $attributes Attributes for the text */
    public $attributes = array();

    /**
     * Sets the anchor property
     *
     * @param \Freesewing\Point $anchor The text anchor point
     */
    public function setAnchor(\Freesewing\Point $anchor)
    {
        $this->anchor = $anchor;
    }

    /**
     * Sets the text property
     *
     * @param string The actual text to be displayed
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Sets the attributes property
     *
     * @param array The text attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns the anchor property
     *
     * @return \Freesewing\Point the text anchor
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     * Returns the text property
     *
     * string the text itself
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Returns the attributes property
     *
     * @return array the text attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
