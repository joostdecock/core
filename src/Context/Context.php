<?php
/** Freesewing\Context class */
namespace Freesewing;

use Freesewing\Services\AbstractService;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Container for all info throughout the entire request.
 *
 * This context class holds all information that we need throughout the request.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016-2017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Context
{
    /** @var \Freesewing\Services\AbstractService */
    protected $service;

    /** @var \Freesewing\Patterns\Pattern */
    protected $pattern;

    /** @var \Freesewing\Themes\Theme */
    protected $theme;

    /** @var \Freesewing\Channels\Channel */
    protected $channel;

    /** @var \Freesewing\Request */
    protected $request;

    /** @var \Freesewing\Response */
    protected $response;

    /** @var \Freesewing\Model */
    protected $model;

    /** @var \Symfony\Component\Translation\Translator */
    protected $translator;

    /** @var \Freesewing\OptionsSampler */
    protected $optionsSampler;

    /** @var \Freesewing\MeasurementsSampler */
    protected $measurementsSampler;

    /** @var \Freesewing\SvgRenderbot */
    protected $renderbot;

    /** @var \Freesewing\SvgDocument */
    protected $svgDocument;

    /** @var string */
    protected $locale;

    /** @var array */
    protected $config;

    /** @var array */
    protected $units;


    /**
     * Sets properties based on the data passed in the request
     *
     * The configure method sets up properties that are common to all requests.
     * They are:
     *  - config
     *  - service
     *  - channel
     *  - theme
     *  - locale
     */
    public function configure()
    {
        $this->setConfig($this->loadConfig());
        $this->setService($this->loadService());
        $this->setChannel($this->loadChannel());
        $this->setTheme($this->loadTheme());
        $this->setLocale($this->loadLocale());
    }

    /**
     * Calls the run() method on the service property
     */
    public function runService()
    {
        $this->service->run($this);
    }

    /**
     * Adds a pattern to the context
     *
     * Stores the result fo loadPattern() in the pattern property
     * Also makes the theme name avaialble to the pattern
     */
    public function addPattern()
    {
        $this->pattern = $this->loadPattern();
        $this->pattern->setPaperless($this->theme->isPaperless());
    }

    /**
     * Adds a theme to the context
     */
    public function addTheme()
    {
        $this->setTheme($this->loadTheme());
    }

    /**
     * Adds a theme to the context
     */
    public function addModel()
    {
        $this->setModel(new \Freesewing\Model());
    }

    /**
     * Adds an optionsSampler to the context
     */
    public function addOptionsSampler()
    {
        $this->setOptionsSampler(new \Freesewing\OptionsSampler());
    }

    /**
     * Adds a measurementsSampler to the context
     */
    public function addMeasurementsSampler()
    {
        $this->setMeasurementsSampler(new \Freesewing\MeasurementsSampler());
    }

    /**
     * Adds a translator to the context
     */
    public function addTranslator()
    {
        $this->translator = $this->loadTranslator();
    }

    /**
     * Adds units to the context
     */
    public function addUnits()
    {
        $this->units = $this->loadUnits();
    }

    /**
     * Adds an svgDocument to the context
     */
    public function addSvgDocument()
    {
        $this->svgDocument = $this->loadSvgDocument();
    }

    /**
     * Adds a renderbot to the context
     */
    public function addRenderbot()
    {
        $this->setRenderbot(new \Freesewing\SvgRenderbot());
    }

    /**
     * Returns the translator property
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Returns the units property
     *
     * @return array
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Stores response in the response property
     *
     * @param \Freesewing\Response
     */
    public function setResponse(\Freesewing\Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Stores pattern in the pattern property
     *
     * (only) The SampleService uses this to override the pattern that we added initially
     *
     * @param \Freesewing\Patterns\Pattern
     */
    public function setPattern(\Freesewing\Patterns\Pattern $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Returns the directory in which freesewing was installed
     *
     * @return string
     */
    public function getApiDir()
    {
        return dirname(dirname(Utils::getClassDir($this)));
    }

    /**
     * Allows pattern, theme, and channel to clean up
     *
     * This calls the cleanUp() method on channel, theme, and pattern(*)
     * For example: If your channel logs things to a database, you could close that connection in channel->cleanUp()
     * (*) The InfoService does not instantiate a pattern
     */
    public function cleanUp()
    {
        if (isset($this->pattern)) {
            $this->pattern->cleanUp();
        }
        $this->theme->cleanUp();
        $this->channel->cleanUp();
    }

    /**
     * Creates a new \Freesewing\SvgDocument
     *
     * This also feeds newly instatiated (extentions of) SvgBlocks to the constructor. Full list:
     *  - \Freesewing\SvgComments
     *  - \Freesewing\SvgAttributes
     *  - \Freesewing\SvgCss
     *  - \Freesewing\SvgScript
     *  - \Freesewing\SvgDefs
     *  - \Freesewing\SvgComments
     *
     * @return \Freesewing\SvgDocument
     */
    private function loadSvgDocument()
    {
        return new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
    }

    /**
     * Creates a new \Freesewing\Translator
     *
     * Loads translations for the locale set in the context and
     * the backup (default) locale set in the config file
     *
     * @return \Symfony\Component\Translation\Translator
     */
    private function loadTranslator()
    {
        $locale = $this->locale;
        $altloc = $this->config['defaults']['locale'];
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

    /**
     * Returns the locale to use for this request
     *
     * This checks for a locale parameter in the request
     * and falls back to the locale defined in the config file
     *
     * @return string
     */
    private function loadLocale()
    {
        if ($this->request->getData('lang') !== null) {
            $locale = strtolower($this->request->getData('lang'));
        } else {
            $locale = $this->config['defaults']['locale'];
        }

        return $locale;
    }

    /**
     * Returns the units to use for this request
     *
     * Checks for untisIn and unitsOut parameters in the request
     * If they are anything else than 'imperial' this returns 'metric' for both
     *
     * @return array
     */
    private function loadUnits()
    {
        if ($this->request->getData('unitsIn') == 'imperial') {
            $units['in'] = 'imperial';
        } else {
            $units['in'] = 'metric';
        }
        if ($this->request->getData('unitsOut') == 'imperial') {
            $units['out'] = 'imperial';
        } else {
            $units['out'] = 'metric';
        }

        return $units;
    }

    /**
     * Returns config file as an array
     *
     * @return array
     */
    private function loadConfig()
    {
        return \Freesewing\Yamlr::loadYamlFile($this->getConfigFile());
    }

    /**
     * Returns the location of the config file
     *
     * @return string
     */
    private function getConfigFile()
    {
        return $this->getApiDir().'/config.yml';
    }

    /**
     * Creates a new service based on request data, or the default service
     *
     * @return \Freesewing\Services\AbstractService|\Freesewing\Services\DraftService|\Freesewing\Services\SampleService|\Freesewing\Services\InfoService
     *
     * @throws \InvalidArgumentException if the specified service cannot be found
     */
    private function loadService()
    {
        if ($this->request->getData('service') !== null && in_array($this->request->getData('service'), $this->config['services'])) {
            $service = $this->request->getData('service');
        } else {
            $service = $this->config['defaults']['service'];
        }
        $class = '\\Freesewing\\Services\\'.ucfirst($service).'Service';
        
        return new $class();
    }

    /**
     * Creates a new channel based on request data, or the default channel
     *
     * @return \Freesewing\Channels\Channel or equivalent
     *
     * @throws \InvalidArgumentException if the specified channel cannot be found
     */
    private function loadChannel()
    {
        if ($this->request->getData('channel') !== null) {
            $channel = $this->request->getData('channel');
        } else {
            $channel = $this->config['defaults']['channel'];
        }
        $class = '\\Freesewing\\Channels\\'.$channel;
        if (class_exists($class)) {
            return new $class();
        } else {
            throw new \InvalidArgumentException("Cannot load channel $channel, it does not exist");
        }
    }

    /**
     * Creates a new pattern based on request data, or the default pattern
     *
     * @return \Freesewing\Patterns\Pattern or equivalent
     *
     * @throws \InvalidArgumentException if the specified pattern cannot be found
     */
    private function loadPattern()
    {
        if ($this->request->getData('pattern') !== null) {
            $pattern = $this->request->getData('pattern');
        } else {
            $pattern = $this->config['defaults']['pattern'];
        }         $class = '\\Freesewing\\Patterns\\'.$pattern;
        if (class_exists($class)) {
            return new $class();
        } else {
            throw new \InvalidArgumentException("Cannot load pattern $pattern, it does not exist");
        }
    }

    /**
     * Creates a new theme based on request data, or the default theme
     *
     * @return \Freesewing\Themes\Theme or equivalent
     *
     * @throws \InvalidArgumentException if the specified theme cannot be found
     */
    private function loadTheme()
    {
        if ($this->request->getData('theme') !== null) {
            $theme = $this->request->getData('theme');
        } else {
            $theme = $this->config['defaults'][$this->service->getServiceName().'Theme'];
        }
        $class = '\\Freesewing\\Themes\\'.$theme;
        if (class_exists($class)) {
            return new $class();
        } else {
            throw new \InvalidArgumentException("Cannot load theme $theme, it does not exist");
        }
    }

    /**
     * @return AbstractService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param AbstractService $service
     */
    public function setService(\Freesewing\Services\AbstractService $service)
    {
        $this->service = $service;
    }

    /**
     * @return Themes\Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param \Freesewing\Themes\Theme or \Freesewing\Themes\Info $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @return Channels\Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param Channels\Channel $channel
     */
    public function setChannel(\Freesewing\Channels\Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return Patterns\Pattern
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Stores a request object in the request property.
     *
     * @param \Freesewing\Request request
     */
    public function setRequest(\Freesewing\Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     *
     * @param \Freesewing\Model $model The model
     */
    public function setModel(\Freesewing\Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return OptionsSampler
     */
    public function getOptionsSampler()
    {
        return $this->optionsSampler;
    }

    /**
     * @param OptionsSampler $optionsSampler
     *
     * @param \Freesewing\OptionsSampler $optionsSampler The options sampler
     */
    public function setOptionsSampler(\Freesewing\OptionsSampler $optionsSampler)
    {
        $this->optionsSampler = $optionsSampler;
    }

    /**
     * @return MeasurementsSampler
     */
    public function getMeasurementsSampler()
    {
        return $this->measurementsSampler;
    }

    /**
     * @param MeasurementsSampler $measurementsSampler
     *
     * @param \Freesewing\MeasurementsSampler $measurementsSampler The measurements sampler
     */
    public function setMeasurementsSampler(\Freesewing\MeasurementsSampler $measurementsSampler)
    {
        $this->measurementsSampler = $measurementsSampler;
    }

    /**
     * @return SvgRenderbot
     */
    public function getRenderbot()
    {
        return $this->renderbot;
    }

    /**
     * @param SvgRenderbot $renderbot
     *
     * @param \Freesewing\SvgRenderbot $renderbot The Svg Renderbot
     */
    public function setRenderbot(\Freesewing\SvgRenderbot $renderbot)
    {
        $this->renderbot = $renderbot;
    }

    /**
     * @return SvgDocument
     */
    public function getSvgDocument()
    {
        return $this->svgDocument;
    }

    /**
     * @param SvgDocument $svgDocument
     *
     * @param \Freesewing\SvgDocument $svgDocument The SvgDocument
     */
    public function setSvgDocument(\Freesewing\SvgDocument $svgDocument)
    {
        $this->svgDocument = $svgDocument;
    }
}
