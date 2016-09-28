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
}
