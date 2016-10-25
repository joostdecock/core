<?php
/** Freesewing\Note class */
namespace Freesewing;

/**
 * A note is text + an arrow pointing to something.
 *
 * This extends text, and is a way to add text to your pattern with a 
 * little arrow pointing to a particular point. 
 * It's useful for adding instructions and remarks to 
 * your pattern about something in particular.
 *
 * @see \Freesewing\Part::newNote()
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Note extends Text
{
    /** @var array Path of the arrow */
    public $path;

    /**
     * Sets the path that the arrow follows.
     *
     * @param \Freesewing\Path path
     */
    public function setPath(\Freesewing\Path $path)
    {
        $this->path = $path;
    }

    /**
     * Returns the path that the arrow follows.
     *
     * @return \Freesewing\Path
     */
    public function getPath()
    {
        return $this->path;
    }
}
