<?php
/** Freesewing\Services\DraftService class */
namespace Freesewing\Services;

use Freesewing\Utils;

/**
 * Handles the draft service, which drafts patterns.
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class DraftService extends AbstractService
{

    /**
     * Scale factor for SVG Rendering.
     */
    const SCALE = 3.54330709;

    /**
     * Returns the name of the service
     *
     * This is used to load the default theme for the service when no theme is specified
     *
     * @see Context::loadTheme()
     *
     * @return string
     */
    public function getServiceName()
    {
        return 'draft';
    }

    /**
     * Drafts a pattern
     *
     * This drafts a pattern, sets the response and sends it
     * Essentially, it takes care of the entire remainder of the request
     *
     * @param \Freesewing\Context
     */
    public function run(\Freesewing\Context $context)
    {
        $context->addPattern();

        if ($context->channel->isValidRequest($context) === true) :
            $context->addModel();
            $context->model->addMeasurements($context->channel->standardizeModelMeasurements($context->request,
                $context->pattern));

            $context->pattern->addOptions($context->channel->standardizePatternOptions($context->request, $context->pattern));

            $context->addUnits();
            $context->pattern->setUnits($context->getUnits());
            $context->addTranslator();
            $context->pattern->setTranslator($context->getTranslator());

            $context->theme->setOptions($context->request);

            $context->pattern->draft($context->model);
            $context->pattern->setPartMargin($context->theme->config['settings']['partMargin']);

            $context->theme->applyRenderMask($context->pattern);
            $context->pattern->layout();

            $context->theme->themePattern($context->pattern);

            $context->addSvgDocument();
            $context->addRenderbot();
            $this->svgRender($context);

            $context->setResponse($context->theme->themeResponse($context));

            /* Last minute replacements on the entire response body */
            $context->response->setBody($this->replace($context->response->getBody(), $context->pattern->getReplacements()));
        else :
            // channel->isValidRequest() !== true
            $context->channel->handleInvalidRequest($context);
        endif;

        $context->response->send();

        $context->cleanUp();
    }

    /**
     * Sets up the SVG document and calls the renderbot
     *
     * This add width and height attributes to the SVG and calls theme->themeSvg()
     * Then, it gets the renderbot to render the SVG body
     *
     * @param \Freesewing\Context
     */
    protected function svgRender($context)
    {
        $context->svgDocument->svgAttributes->add('width ="' . ($context->pattern->getWidth() * self::SCALE) . '"');
        $context->svgDocument->svgAttributes->add('height ="' . ($context->pattern->getHeight() * self::SCALE) . '"');

        $viewbox = $context->request->getData('viewbox');
        if ($viewbox !== null) {
            $viewbox = Utils::asScrubbedArray($viewbox, ',');
            $context->svgDocument->svgAttributes->add('viewbox ="' . $viewbox[0] . ' ' . $viewbox[1] . ' ' . $viewbox[2] . ' ' . $viewbox[3] . '"');
        } else {
            $context->svgDocument->svgAttributes->add('viewbox ="0 0 ' . ($context->pattern->getWidth() * self::SCALE) . ' ' . ($context->pattern->getHeight() * self::SCALE) . '"');
        }
        // format specific themeing
        $context->theme->themeSvg($context->svgDocument);

        // render SVG
        $context->svgDocument->setSvgBody($context->renderbot->render($context->pattern));
    }

    /**
     * Last minute replacements in the SVG
     *
     * Sometimes you want to add something to the SVG that is not available until later
     * Or, more commonly, you want to include something in defs or CSS and have it adapt to
     * whatever is going on in the pattern
     * For that, you can register a replacement in the pattern, and it will be handled here
     *
     * @param string svg
     * @param array  replacements
     *
     * @see \Freesewing\Patterns\Pattern::replace()
     * @return string
     */
    private function replace($svg, $replacements)
    {
        if (is_array($replacements)) {
            $svg = str_replace(array_keys($replacements), array_values($replacements), $svg);
        }

        return $svg;
    }
}
