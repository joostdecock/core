<?php
/** Freesewing\SvgCss class */
namespace Freesewing;

/**
 * Holds css style for an SVG document.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgCss extends SvgBlock
{
    /**
     * Returns the css as a style block
     *
     * Note that we are returning this as a string,
     * using the magic __toString() method
     * which is defined in the parent class
     *
     * @see \Freesewing\SvgBlock::__toString()
     *
     * @return string svg style block
     */
    public function load()
    {
        if ($this->getData() === false) {
            return false;
        } else {
            // Need to make sure @include lines go at the top
            return  "\n<style type=\"text/css\">\n    <![CDATA[\n".$this->sortCss()."\n    ]]>\n</style>\n";
        }
    }

    /**
     * Makes sure css @include lines go first
     *
     * CSS requires @include lines to go first
     * So we sort them to have these at the top
     *
     * @return string The sorted css
     */
    private function sortCss()
    {
        $theseFirst = '';
        $css = '';
        $data = $this->getData();
        if (is_array($data)) {
            foreach ($data as $origin) {
                foreach ($origin as $line) {
                    if (substr(trim($line), 0, 1) == '@') {
                        $theseFirst .= "\n    $line";
                    } else {
                        $css .= "\n    $line";
                    }
                }
            }
        }

        return $theseFirst.$css;
    }
}
