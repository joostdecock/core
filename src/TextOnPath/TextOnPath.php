<?php
/** Freesewing\Boundary class */
namespace Freesewing;

/**
 * Holds text to be placed on a path.
 *
 * This is very much like the text class, except that it has a path property
 * rather than a point on which the text will be placed
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class TextOnPath
{
    /** @var \Freesewing\Path $path Path to place the text on */
    public $path;
    
    /** @var string $text The text itself */
    public $text;

    /** @var array $attributes Attributes for the text */
    public $attributes = array();

    /**
     * Sets the path property
     *
     * @param \Freesewing\Path $path The text anchor path
     */
    public function setPath(\Freesewing\Path $path)
    {
        $this->path = $path;
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
     * Returns the path property
     *
     * @return \Freesewing\Path the path for the text
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the text property
     *
     * @return string the text itself
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
