<?php
/** Freesewing\DimensionWidth class */
namespace Freesewing;

/**
 * DimensionWidth is used to label widhts on a pattern
 *
 * This is mainly used in paperless patterns
 *
 * @see \Freesewing\Part::newNote() // FIXME
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class DimensionWidth extends Dimension
{
    /** @var \Freesewing\TextOnPath $label The label for the dimension */
    private $label;

    /**
     * Sets the label property
     *
     * @param \Freesewing\TextOnPath $label The label for the dimension
     */
    public function setLabel(\Freesewing\TextOnPath $label)
    {
        $this->label = $label;
    }

    /**
     * Gets the label property
     *
     * @return \Freesewing\TextOnPath The label for the dimension
     */
    public function getLabel()
    {
        return $this->label;
    }

}
