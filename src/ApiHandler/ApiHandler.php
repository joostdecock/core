<?php

namespace Freesewing;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Freesewing\ApiHandler class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ApiHandler
{
    const configFile = __DIR__.'/../../config.yml';
    public $config;
    /**
     * @var \Freesewing\Model
     */
    public $model;

    /**
     * @var \Freesewing\SvgDocument
     */
    public $svgDocument;

    /**
     * @var array
     */
    private $context;

    /**
     * @var array
     */
    private $renderAs;

    /**
     * @var array
     */
    public $requestData;

    private $fallbackLocale = 'en';

    public function __construct($data)
    {
        $this->config = \Freesewing\Yamlr::loadConfig($this::configFile);
        $this->requestData = $data;
        $this->setContext();
    }

    public function handle()
    {
        $this->channel = $this->instantiateFromContext('channel');
        $this->pattern = $this->instantiateFromContext('pattern');
        $this->theme = $this->instantiateFromContext('theme');

        if (
            !isset($this->response)
            &&
            $this->channel->isValidRequest($this->requestData) === true
        ) :

            $this->model = new \Freesewing\Model();
        $this->model->addMeasurements(
                $this->channel->standardizeModelMeasurements($this->requestData)
            );

        $this->pattern->theme = $this->context['theme'];
        $this->pattern->setTranslator($this->getTranslator());
        $this->pattern->setUnits($this->context['units']);
        $this->pattern->addOptions(
                $this->channel->standardizePatternOptions($this->requestData)
            );

        $this->pattern->draft($this->model);

        $this->pattern->layout();

        $this->theme->themePattern($this->pattern);

        $this->renderAs = $this->theme->RenderAs();
        if ($this->renderAs['svg'] === true) {
            $this->svgRender();
        }
        if ($this->renderAs['js'] === true) {
            $this->jsRender();
        }

        $this->response = $this->theme->themeResponse($this);

            // Last minute replacements on entire response body
            $this->response->setBody($this->replace($this->response->getBody(), $this->pattern->getReplacements())); else: // channel->isValidRequest() !== true
            $this->response = $this->bailOut(
                'bad_request',
                'Request not valid for channel '.$this->context['channel']
            );
        endif;

        $this->response->send();

        $this->pattern->cleanUp();
        $this->theme->cleanUp();
        $this->channel->cleanUp();
    }

    public function getContext()
    {
        return $this->context;
    }

    private function getTranslator()
    {
        $locale = $this->getLocale();
        $altloc = $this->fallbackLocale;
        $themeTranslations = $this->theme->getTranslationFiles($locale, $altloc);
        $patternTranslations = $this->pattern->getTranslationFiles($locale, $altloc);
        $translations[$locale] = array_merge($themeTranslations[$locale], $patternTranslations[$locale]);
        $translations[$altloc] = array_merge($themeTranslations[$altloc], $patternTranslations[$altloc]);

        $translator = new Translator($locale);
        $translator->setFallbackLocales([$altloc]);
        $translator->addLoader('yaml', new YamlFileLoader());

        foreach ($translations[$locale] as $tfile) {
            $translator->addResource('yaml', $tfile, $locale);
        }
        foreach ($translations[$altloc] as $tfile) {
            $translator->addResource('yaml', $tfile, $altloc);
        }

        return $translator;
    }

    private function svgRender()
    {
        $this->svgRenderbot = new \Freesewing\SvgRenderbot();
        $this->svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        $this->svgDocument->svgAttributes->add('width ="'.$this->pattern->getWidth() * 3.54330709.'"');
        $this->svgDocument->svgAttributes->add('height ="'.$this->pattern->getHeight() * 3.54330709.'"');

        // format specific themeing
        $this->theme->themeSvg($this->svgDocument);

        // render SVG
        $this->svgDocument->setSvgBody($this->svgRenderbot->render($this->pattern));
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

    private function instantiateFromContext($type)
    {
        $namespace = ucwords($type).'s';
        $class = '\Freesewing\\'.$namespace.'\\'.$this->context[$type];

        if (class_exists($class)) {
            return new $class();
        } else {
            $this->response = $this->bailOut(
                'bad_request',
                ucwords($type).' '.$this->context[$type].' not found'
            );

            return false;
        }
    }

    private function setContext()
    {
        foreach (['channel', 'pattern', 'theme'] as $type) {
            if (isset($this->requestData[$type])) {
                $this->context[$type] = $this->requestData[$type];
            } else {
                $this->context[$type] = $this->config['defaults'][$type];
            }
        }
        $this->setLocale();
        $this->setUnits();
    }

    private function setLocale()
    {
        if (isset($this->requestData['lang'])) {
            $this->context['locale'] = strtolower($this->requestData['lang']);
        } else {
            $this->context['locale'] = 'en';
        }
    }

    private function setUnits()
    {
        if (isset($this->requestData['unitsIn']) && $this->requestData['unitsIn'] == 'imperial') {
            $this->context['units']['in'] = 'imperial';
        } else {
            $this->context['units']['in'] = 'metric';
        }
        if (isset($this->requestData['unitsOut']) && $this->requestData['unitsOut'] == 'imperial') {
            $this->context['units']['out'] = 'imperial';
        } else {
            $this->context['units']['out'] = 'metric';
        }
    }

    private function getLocale()
    {
        return $this->context['locale'];
    }
}
