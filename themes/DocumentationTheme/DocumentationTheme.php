<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\ExampleTheme class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class DocumentationTheme extends Theme
{
    /**
     * @codeCoverageIgnore
     */
    public function themeResponse($apiHandler)
    {

        /*
        $response = new \Freesewing\Response();
        $response->setFormat('raw');
        $response->setBody("{$apiHandler->svgDocument}");

        return $response;
        */

        ob_start();
        \Kint::$maxLevels = 0;
        \Kint::dump($apiHandler);
        $debug = ob_get_clean();

        $response = new \Freesewing\Response();
        $response->setFormat('json');
        $response->setBody([
            'status' => 'OK',
            'svg' => "{$apiHandler->svgDocument}",
            'debug' => $debug,
        ]);

        return $response;
    }
}
