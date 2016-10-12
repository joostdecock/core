<?php

namespace Freesewing;

/**
 * Freesewing\SvgCss class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgCss extends SvgBlock
{
    public function load()
    {
        // Need to make sure @includes go at the top
        return  "\n<style type=\"text/css\">\n    <![CDATA[\n".$this->sortCss()."\n    ]]>\n</style>\n";
    }

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
