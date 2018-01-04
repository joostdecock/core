<?php
/** Freesewing\Themes\Core\PaperlessJson class */
namespace Freesewing\Themes\Core;

use Freesewing\Context;

/**
 * A JSON version of the Paperless theme
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2018 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class PaperlessJson extends Paperless
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
        $response->setFormat('json');
        $response->setBody([
            'svg'   => "{$context->getSvgDocument()}",
            'version' => $context->getPattern()->getVersion(),
        ]);
        
        return $response;
    }
}
