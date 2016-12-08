<?php
/** Freesewing\SampleService class */
namespace Freesewing\Service;

/**
 * Handles the draft service, which samples patterns.
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SampleService extends DraftService
{

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
        return 'sample';
    }

    /**
     * Samples a pattern
     *
     * This samples a pattern, sets the response and sends it
     * Essentially, it takes care of the entire remainder of the request
     *
     * @param \Freesewing\Context
     */
    public function run(\Freesewing\Context $context)
    {
        $context->addPattern();

        if ($context->channel->isValidRequest($context) === true) :

            $context->addUnits();
            $context->pattern->setUnits($context->getUnits());

            $context->addTranslator();
            $context->pattern->setTranslator($context->getTranslator());

            $context->pattern->setPartMargin($context->theme->config['settings']['partMargin']);
            $context->theme->setOptions($context->request);

            if ($context->request->getData('mode') == 'options') { // Sampling options
                $context->addOptionsSampler();
                $context->optionsSampler->setPattern($context->pattern);

                $context->addModel();
                $context->model->addMeasurements($context->optionsSampler->loadModelMeasurements($context->pattern));

                $context->pattern->addOptions($context->optionsSampler->loadPatternOptions());
                $context->setPattern($context->optionsSampler->sampleOptions($context->model, $context->theme,
                    $context->request->getData('option'), $context->request->getData('steps')));
            } else { // Sampling measurements
                $context->addMeasurementsSampler();
                $context->measurementsSampler->setPattern($context->pattern);

                $context->pattern->addOptions($context->measurementsSampler->loadPatternOptions());

                $context->measurementsSampler->setModelConfig($context->pattern->getSamplerModelConfig());
                $context->measurementsSampler->loadPatternModels($context->request->getData('samplerGroup'));
                $context->setPattern($context->measurementsSampler->sampleMeasurements($context->theme));
            }

            $context->addSvgDocument();
            $context->addRenderbot();
            $this->svgRender($context);
            $context->setResponse($context->theme->themeResponse($context));
        else: // channel->isValidRequest() !== true
            $context->channel->handleInvalidRequest($context);
        endif;

        $context->response->send();

        $context->cleanUp();
    }
}
