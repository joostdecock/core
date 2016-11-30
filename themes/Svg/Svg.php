<?php
/** Freesewing\Themes\Info class */
namespace Freesewing\Themes;

/**
 * Default theme for the draft service. 
 *
 * A straight-forward theme for SVG output
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Svg extends Theme
{
    /**
     * Returns the SVG document
     * 
     * @param \Freesewing\Context $context The context object
     */
    public function themeResponse($context)
    {
        $response = new \Freesewing\Response();
        $response->addCacheHeaders($context->request);
        $response->addHeader('Content-Type', 'Content-Type: image/svg+xml');
        $response->setFormat('svg');
        $response->setBody("{$context->svgDocument}");

        return $response;
    }
    
    /**
     * Loads message from pattern into messages property
     * Adds translations for our scalebox
     *
     * @param \Freesewing\Patterns\* $pattern The pattern object
     */
    public function themePattern($pattern)
    {
        $this->messages = $pattern->getMessages();
        $pattern->replace('__SCALEBOX_METRIC__', $pattern->t('__SCALEBOX_METRIC__'));
        $pattern->replace('__SCALEBOX_IMPERIAL__', $pattern->t('__SCALEBOX_IMPERIAL__'));
    }

}
