<?php
/** Freesewing\Themes\Core\BasicJson class */
namespace Freesewing\Themes\Core;

use Freesewing\Context;

/**
 * A JSON version of the Basic theme
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2018 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class BasicJson extends Basic
{
    /**
     * Outputs the pattern + info as JSON
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return \Freesewing\Response $response The response object
     */
    public function themeResponse(Context $context)
    {
        $response = new \Freesewing\Response();
        $response->addCacheHeaders($context->getRequest());
        $response->addHeader('Content-Type', 'Content-Type: application/json');
        $response->setFormat('json');
        $response->setBody([
            'version' => $context->getPattern()->getVersion(),
            'svg'   => "{$context->getSvgDocument()}",
        ]);
        
        return $response;
    }
}
