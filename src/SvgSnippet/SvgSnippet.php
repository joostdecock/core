<?php
/** Freesewing\SvgInclude */
namespace Freesewing;

/**
 * Holds an SVG snippet, a use of something defined in the SVG defs
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgSnippet
{
    /** @var \Freesewing\Point $anchor The point to anchor the snippet on */
    private $anchor;
    
    /** @var string $reference The ID of the element in defs */
    private $reference = null;
    
    /** @var string $description An optional description of the snippet */
    private $description = null;
    
    /** @var array $attributes The snippet attributes */
    private $attributes;

    /**
     * Returns the anchor property
     *
     * @return \Freesewing\Point The snippet anchor
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     * Returns the reference property
     *
     * @return string The snippet reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Returns the description property
     *
     * @return string The snippet description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the attributes property
     *
     * @return string The snippet description
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Sets the anchor property
     *
     * @param \Freesewing\Point $anchor The snippet anchor
     */
    public function setAnchor(\Freesewing\Point $anchor)
    {
        $this->anchor = $anchor;
    }

    /**
     * Sets the reference property
     *
     * @param string $reference The ID of the defs source for the snippet
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Sets the description property
     *
     * @param string $description An optional for the snippet
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Sets the attributes property
     *
     * @param array $attributes Array of attribute keys and values
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
}
