<?php
/** Freesewing\Context class */
namespace Freesewing;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Container for all info throughout the entire request.
 *
 * This context class holds all information that we need throughout the request.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Context
{
    /**
     * Stores a request object in the request property.
     *
     * @param \Freesewing\Request request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

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
        $this->config = $this->loadConfig();
        $this->service = $this->loadService();
        $this->channel = $this->loadChannel();
        $this->theme = $this->loadTheme();
        $this->locale = $this->loadLocale();
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
        $this->theme = $this->loadTheme();
    }

    /**
     * Adds a theme to the context
     */
    public function addModel()
    {
        $this->model = new \Freesewing\Model();
    }

    /**
     * Adds an optionsSampler to the context
     */
    public function addOptionsSampler()
    {
        $this->optionsSampler = new \Freesewing\OptionsSampler();
    }

    /**
     * Adds a measurementsSampler to the context
     */
    public function addMeasurementsSampler()
    {
        $this->measurementsSampler = new \Freesewing\MeasurementsSampler();
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
        $this->renderbot = $this->loadRenderbot();
    }

    /**
     * Returns the translator property
     *
     * @return \Freesewing\Translator
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
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Stores pattern in the pattern property
     *
     * (only) The SampleService uses this to override the pattern that we added initially
     *
     * @param \Freesewing\Response
     */
    public function setPattern($pattern)
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
     * Creates a new \Freesewing\SvgRenderbot
     *
     * @return \Freesewing\SvgRenderbot
     */
    private function loadRenderbot()
    {
        return new \Freesewing\SvgRenderbot();
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
     * @return \Freesewing\Translator
     */
    private function loadTranslator()
    {
        $locale = $this->locale;
        $altloc = $this->config['defaults']['locale'];
        $themeTranslations = $this->theme->getTranslationFiles($locale, $altloc);
        $patternTranslations = $this->pattern->getTranslationFiles($locale, $altloc);

        // Check if there is a theme-localization
        // allow to translate patterns independent of the theme
        if (!isset($themeTranslations[$locale])) {
            $themeTranslations[$locale] = $themeTranslations[$altloc];
        }

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
     * @return \Freesewing\DraftService|\Freesewing\SampleService|\Freesewing\InfoService
     *
     * @throws InvalidArgumentException if the specified service cannot be found
     */
    private function loadService()
    {
        if ($this->request->getData('service') !== null && in_array($this->request->getData('service'), $this->config['services'])) {
            $service = $this->request->getData('service');
        } else {
            $service = $this->config['defaults']['service'];
        }
        $class = '\\Freesewing\\'.ucfirst($service).'Service';
        if (class_exists($class)) {
            return new $class();
        } else {
            throw new \InvalidArgumentException('Cannot load service '.ucfirst($service).'Service, it does not exist');
        }
    }

    /**
     * Creates a new channel based on request data, or the default channel
     *
     * @return \Freesewing\Channel or equivalent
     *
     * @throws InvalidArgumentException if the specified channel cannot be found
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
     * @param string name The (optional) class of a name to load, only used by the sample service
     *
     * @return \Freesewing\Pattern or equivalent
     *
     * @throws InvalidArgumentException if the specified pattern cannot be found
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
     * @return \Freesewing\Theme or equivalent
     *
     * @throws InvalidArgumentException if the specified theme cannot be found
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
}
