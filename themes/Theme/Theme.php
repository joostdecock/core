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
    public $messages = array();

    public function renderAs() 
    {
        return [
            'svg' => true,
            'js' => false,
        ];
    }

    public function themePattern($pattern)
    {
        $this->messages = $pattern->getMessages();
    }

    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $templateDir = $this->templateDir();
        $svgDocument->headerComments->add(file_get_contents("$templateDir/header.comments"));
        $svgDocument->svgAttributes->add(file_get_contents( "$templateDir/svg.attributes"));
        $svgDocument->css->add(file_get_contents(           "$templateDir/svg.css"));
        $svgDocument->defs->add(file_get_contents(          "$templateDir/svg.defs"));
        $svgDocument->footerComments->add($this->messages);
        $svgDocument->footerComments->add(file_get_contents("$templateDir/footer.comments"));
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

    public function templateDir()
    {
        return __DIR__.'/templates';
    }
}
