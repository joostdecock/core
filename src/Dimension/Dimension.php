<?php
/** Freesewing\Dimension class */
namespace Freesewing;

/**
 * Dimension is used to label widhts/heights/angles on a pattern
 *
 * This is mainly used in paperless patterns
 *
 * @see \Freesewing\Part::newNote() // FIXME
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Dimension
{
    /** @var \Freesewing\TextOnPath $label The label for the dimension */
    private $label;

    /** @var array $leaders Array of leader lines/paths for the dimension */
    private $leaders = array();

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

    /**
     * Gets the path from the label
     *
     * @return \Freesewing\Path The label path
     */
    public function getPath()
    {
        return $this->label->getPath();
    }

    /**
     * Sets the leaders property
     *
     * @param array $leaders Array of leaders
     */
    public function setLeaders($leaders)
    {
        $this->leaders = $leaders;
    }

    /**
     * Gets the leaders property
     *
     * @return array The leaders
     */
    public function getLeaders()
    {
        return $this->leaders;
    }

    /**
     * Adds a leader to the leaders property
     *
     * @param \Freesewing\Path $leader A leader
     */
    public function addLeader($leader)
    {
        $this->leaders[] = $leader;
    }
}
