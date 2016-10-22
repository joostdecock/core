<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\Developer class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Developer extends Theme
{
    /**
     * @codeCoverageIgnore
     */
    public function themeResponse($context)
    {
        ob_start();
        \Kint::$maxLevels = 0;
        \Kint::dump($context);
        $debug = ob_get_clean();

        $response = new \Freesewing\Response();
        $response->setFormat('json');
        $response->setBody([
            'status' => 'OK',
            'svg' => "{$context->svgDocument}",
            'debug' => $debug,
        ]);

        return $response;
    }
}
