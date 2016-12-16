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
abstract class Dimension
{
    /** @var string $from ID of the point the dimension starts from */
    private $from;
    
    /** @var string $to ID of the point where the dimension ends */
    private $to;
    
    /** @var \Freesewing\Path $path The path followed by the dimension */
    private $path;
    
    /** @var array $leaders Array of leader lines/paths for the dimension */
    private $leaders = array();
    
    /**
     * Sets the from property
     *
     * @param \Freesewing\Point $start The dimension start point
     */
    public function setFrom(\Freesewing\Point $from)
    {
        $this->from = $from;
    }

    /**
     * Gets the from property
     *
     * @return \Freesewing\Point The dimension start point
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Sets the to property
     *
     * @param \Freesewing\Point $to The dimension end point
     */
    public function setTo(\Freesewing\Point $to)
    {
        $this->to = $to;
    }

    /**
     * Gets the to property
     *
     * @return \Freesewing\Point The dimension end point
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Sets the path property
     *
     * @param \Freesewing\Path $path The dimension path
     */
    public function setPath(\Freesewing\Path $path)
    {
        $this->path = $path;
    }

    /**
     * Gets the path property
     *
     * @return \Freesewing\Path The dimension path
     */
    public function getPath()
    {
        return $this->path;
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
