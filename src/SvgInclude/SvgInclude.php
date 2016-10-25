<?php
/** Freesewing\SvgInclude */
namespace Freesewing;

/**
 * Holds SVG code to be included in output, no questions asked
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgInclude
{
    /** @var string $content SVG content to include */
    private $content;

    /**
     * Sets the content property
     *
     * @param string $content 
     */
    public function set($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the content property
     *
     * @return string 
     */
    public function get()
    {
        return $this->content;
    }
}
