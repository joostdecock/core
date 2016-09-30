<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\Theme class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Theme
{
    public function renderAs() 
    {
        return [
            'svg' => true,
            'js' => false,
        ];
    }

    public function themePattern($pattern)
    {
    }

    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $svgDocument->headerComments->add(file_get_contents(__DIR__.'/templates/header.comments'));
        $svgDocument->svgAttributes->add(file_get_contents(__DIR__.'/templates/svg.attributes'));
        $svgDocument->css->add(file_get_contents(__DIR__.'/templates/svg.css'));
        $svgDocument->defs->add(file_get_contents(__DIR__.'/templates/svg.defs'));
        $svgDocument->footerComments->add(file_get_contents(__DIR__.'/templates/footer.comments'));
    }

    public function themeResponse($apiHandler)
    {
        $response = new \Freesewing\Response();
        $response->setFormat('raw');
        $response->setBody("{$apiHandler->svgDocument}");

        return $response;
    }

    public function cleanUp()
    {
    }
}
