<?php
/** Freesewing\SvgDocument class */
namespace Freesewing;

/**
 * The (data to construct a) SVG document
 *
 * An SVG document is constructed from:
 *  - header comments
 *  - attrributes
 *  - CSS
 *  - EcmaScript
 *  - defs
 *  - the SVG body
 *  - footercomments
 *
 *  Only the SVG body is a plain data property here
 *  The rest is handled by objects injected into the constructor
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgDocument
{
    /** @var string $svgBody The SVG body */
    public $svgBody = '';

    /** @var \Freesewing\SvgComments $headerComments Comments to go in the header */
    public $headerComments;

    /** @var \Freesewing\SvgAttributes $avgAttributes Attributes for the svg tag */
    public $svgAttributes;

    /** @var \Freesewing\SvgCss $svgCss CSS style */
    public $css;

    /** @var \Freesewing\SvgScript $svgScript ECMA Script */
    public $script;

    /** @var \Freesewing\SvgDefs $svgDefs SVG defs */
    public $defs;

    /** @var \Freesewing\SvgComments $footerComments Comments to go in the footer */
    public $footerComments;

    /**
     * Creates an SVG document
     *
     * @param \Freesewing\SvgComments $headerComments Comments to go in the header
     * @param \Freesewing\SvgAttributes $svgAttributes Attributes for the SVG tag
     * @param \Freesewing\SvgCss $css CSS style
     * @param \Freesewing\SvgScript $script EcmaScript
     * @param \Freesewing\SvgDefs $defs SVG defs
     * @param \Freesewing\SvgComments $footerComments Comments to go in the footer
     */
    public function __construct(
        \Freesewing\SvgComments $headerComments,
        \Freesewing\SvgAttributes $svgAttributes,
        \Freesewing\SvgCss $css,
        \Freesewing\SvgScript $script,
        \Freesewing\SvgDefs $defs,
        \Freesewing\SvgComments $footerComments)
    {
        $this->headerComments = $headerComments;
        $this->svgAttributes = $svgAttributes;
        $this->css = $css;
        $this->script = $script;
        $this->defs = $defs;
        $this->footerComments = $footerComments;
    }

    /**
     * Outputs an SVG document
     *
     * We use the magic __toSting method to compile all
     * the different building blocks into an SVG document.
     * This way, one can simply output this object as a string
     * and get SVG output.
     *
     * @return string The SVG document
     */
    public function __toString()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="no"?'.">\n\n".
            $this->headerComments->load().
            $this->svgAttributes->load().
            $this->css->load().
            $this->script->load().
            $this->defs->load().
            $this->svgBody.
            "\n\n</svg>\n\n".
            $this->footerComments->load();
    }

    /**
     * Sets the svgBody property
     *
     * @param string $svg The SVG body
     */
    public function setSvgBody($svg)
    {
        $this->svgBody = $svg;
    }
}
