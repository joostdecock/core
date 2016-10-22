<?php

namespace Freesewing;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Freesewing\SampleService class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SampleService extends DraftService
{
    public function getServiceName()
    {
        return 'sample';
    }
    public function run($context)
    {
        $context->addPattern();

        if ($context->channel->isValidRequest($context) === true) :

            $context->addUnits();
            $context->pattern->setUnits($context->getUnits());
            $context->addTranslator();
            $context->pattern->setTranslator($context->getTranslator());
            
            if($context->request->getData('mode') == 'options') { // Sampling options
                $context->addOptionsSampler();
                $context->optionsSampler->setPattern($context->pattern);
                
                $context->addModel();
                $context->model->addMeasurements($context->optionsSampler->loadModelMeasurements($context->pattern));
                
                $context->pattern->addOptions($context->optionsSampler->loadPatternOptions());
                $context->setPattern($context->optionsSampler->sampleOptions($context->model, $context->theme, $context->request->getData('option'), $context->request->getData('steps')));

            } else { // Sampling measurements
                $context->addMeasurementsSampler();
                $context->measurementsSampler->setPattern($context->pattern);
                
                $context->pattern->addOptions($context->measurementsSampler->loadPatternOptions());
                
                $context->measurementsSampler->loadPatternModels($context->request->getData('samplerGroup'));
                $context->setPattern($context->measurementsSampler->sampleMeasurements($context->theme));
            }

            $context->addSvgDocument();
            $context->addRenderbot();
            $this->svgRender($context);
            $context->setResponse($context->theme->themeResponse($context));

        else: // channel->isValidRequest() !== true
            $context->setResponse($this->bailOut( 'bad_request', 'Request not valid' ));
        endif;

        $context->response->send();

        $context->cleanUp();
    }

}
