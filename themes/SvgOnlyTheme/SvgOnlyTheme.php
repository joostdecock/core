<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\SvgOnlyTheme class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgOnlyTheme extends Theme
{
    public function themeResponse($apiHandler)
    {

        $response = new \Freesewing\Response();
        $response->setFormat('svg');
        $response->setBody("{$apiHandler->svgDocument}");

        return $response;
    }
    
    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $svgDocument->headerComments->add(file_get_contents(__DIR__.'/templates/header.comments'));
        $svgDocument->svgAttributes->add(file_get_contents(__DIR__.'/templates/svg.attributes'));
        $svgDocument->css->add(file_get_contents(__DIR__.'/templates/svg.css'));
        $svgDocument->script->add(file_get_contents(__DIR__.'/templates/svg.script'));
        $svgDocument->defs->add(file_get_contents(__DIR__.'/templates/svg.defs'));
        $svgDocument->footerComments->add(file_get_contents(__DIR__.'/templates/footer.comments'));
    }
}
