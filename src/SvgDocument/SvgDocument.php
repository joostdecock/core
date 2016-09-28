<?php

namespace Freesewing;

/**
 * Freesewing\SvgDocument class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgDocument
{
    public $svgBody = '';
    public $headerComments;
    public $svgAttributes;
    public $css;
    public $defs;
    public $footerComments;

    public function __construct(
        \Freesewing\SvgComments $headerComments,
        \Freesewing\SvgAttributes $svgAttributes,
        \Freesewing\SvgCss $css,
        \Freesewing\SvgDefs $defs,
        \Freesewing\SvgComments $footerComments)
    {
        $this->headerComments = $headerComments;
        $this->svgAttributes = $svgAttributes;
        $this->css = $css;
        $this->defs = $defs;
        $this->footerComments = $footerComments;
    }

    public function __toString()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="no"?'.">\n\n".
            $this->headerComments->load().
            $this->svgAttributes->load().
            $this->css->load().
            $this->defs->load().
            $this->svgBody.
            "\n\n</svg>\n\n".
            $this->footerComments->load();
    }

    public function setSvgBody($svg)
    {
        $this->svgBody = $svg;
    }
}
