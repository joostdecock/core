<?php

namespace Freesewing;

use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Freesewing\DraftService class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class DraftService
{

    public function getServiceName()
    {
        return 'draft';
    }

    public function run($context)
    {
        $context->addPattern();

        if ($context->channel->isValidRequest($context) === true) :

            $context->addModel();
            $context->model->addMeasurements(
                $context->channel->standardizeModelMeasurements($context->request, $context->pattern)
            );
            
            $context->pattern->addOptions(
                    $context->channel->standardizePatternOptions($context->request, $context->pattern)
                );
            
            $context->addUnits();
            $context->pattern->setUnits($context->getUnits());
            $context->addTranslator();
            $context->pattern->setTranslator($context->getTranslator());
            
            $context->pattern->draft($context->model);
            $context->pattern->layout();
            $context->theme->themePattern($context->pattern);
            
            $context->addSvgDocument();
            $context->addRenderbot();
            $this->svgRender($context);
            
            $context->setResponse($context->theme->themeResponse($context));
            
            // Last minute replacements on entire response body
            $context->response->setBody($this->replace($context->response->getBody(), $context->pattern->getReplacements())); 
        
        else: // channel->isValidRequest() !== true
            $context->setResponse($this->bailOut( 'bad_request', 'Request not valid' ));
        endif;

        $context->response->send();

        $context->cleanUp();
    }

    protected function svgRender($context)
    {
        $context->svgDocument->svgAttributes->add('width ="'.$context->pattern->getWidth() * 3.54330709.'"');
        $context->svgDocument->svgAttributes->add('height ="'.$context->pattern->getHeight() * 3.54330709.'"');

        // format specific themeing
        $context->theme->themeSvg($context->svgDocument);

        // render SVG
        $context->svgDocument->setSvgBody($context->renderbot->render($context->pattern));
    }

    private function replace($svg, $replacements)
    {
        if (is_array($replacements)) {
            $svg = str_replace(array_keys($replacements), array_values($replacements), $svg);
        }

        return $svg;
    }

    private function bailOut($status, $info)
    {
        if (isset($this->response)) {
            return $this->response;
        } else {
            $response = new \Freesewing\Response();
            $response->setStatus($status);
            $response->setBody([
                'error' => ucwords(str_replace('_', ' ', $status)),
                'info' => $info,
            ]);
            $response->setFormat('json');

            return $response;
        }
    }

}
