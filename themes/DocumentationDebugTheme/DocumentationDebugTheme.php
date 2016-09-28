<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\ExampleTheme class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class DocumentationDebugTheme extends SvgOnlyDebugTheme
{
    /**
     * @codeCoverageIgnore
     */
    public function themeResponse($apiHandler)
    {

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
