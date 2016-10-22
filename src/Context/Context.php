<?php

namespace Freesewing;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Freesewing\Context class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Context
{


    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function configure()
    {
        $this->config  = $this->loadConfig();
        $this->service = $this->loadService();
        $this->channel = $this->loadChannel();
        $this->theme   = $this->loadTheme();
        $this->locale  = $this->loadLocale();
    }

    public function runService()
    {
        $this->service->run($this);   
    }

    public function addPattern()
    {
        $this->pattern = $this->loadPattern();
    }

    public function addTheme()
    {
        $this->theme = $this->loadTheme();
    }

    public function addModel()
    {
        $this->model = new \Freesewing\Model();
    }

    public function addOptionsSampler()
    {
        $this->optionsSampler = new \Freesewing\OptionsSampler;
    }

    public function addMeasurementsSampler()
    {
        $this->measurementsSampler = new \Freesewing\MeasurementsSampler;
    }

    public function addTranslator()
    {
        $this->translator = $this->loadTranslator();
    }

    public function addUnits()
    {
        $this->units = $this->loadUnits();
    }

    public function addSvgDocument()
    {
        $this->svgDocument = $this->loadSvgDocument();
    }

    public function addRenderbot()
    {
        $this->renderbot = $this->loadRenderbot();
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    public function getApiDir()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filename = $reflector->getFileName();

        return dirname(dirname(dirname($filename)));
    }

    public function cleanUp()
    {
        if(is_object($this->pattern)) $this->pattern->cleanUp();
        $this->theme->cleanUp();
        $this->channel->cleanUp();
    }

    private function loadRenderbot()
    {
        return new \Freesewing\SvgRenderbot();
    }

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

    private function loadLocale()
    {
        if ($this->request->getData('lang') !== null) {
            $locale = strtolower($this->request->getData('lang'));
        } else {
            $locale = $this->config['defaults']['locale'];
        }

        return $locale;
    }

    private function loadUnits()
    {
        if ($this->request->getData('unitsIn') == 'imperial') $units['in'] = 'imperial';
        else $units['in'] = 'metric';
        if ($this->request->getData('unitsOut') == 'imperial') $units['out'] = 'imperial';
        else $units['out'] = 'metric';

        return $units;
    }
    
    private function loadConfig()
    {
        return \Freesewing\Yamlr::loadYamlFile($this->getConfigFile());
    }
    
    private function getConfigFile()
    {
        return $this->getApiDir().'/config.yml';
    }
    
    private function loadService()
    {
        if($this->request->getData('service') !== null && in_array($this->request->getData('service'), $this->config['services'])) $service = $this->request->getData('service');
        else $service = $this->config['defaults']['service'];
        $class = '\\Freesewing\\'.ucfirst($service).'Service';
        if (class_exists($class)) return new $class();
        else throw new \InvalidArgumentException("Cannot load service ".ucfirst($service)."Service, it does not exist");
    }

    private function loadChannel()
    {
        if($this->request->getData('channel') !== null) $channel = $this->request->getData('channel');
        else $channel = $this->config['defaults']['channel'];
        $class = '\\Freesewing\\Channels\\'.$channel;
        if (class_exists($class)) return new $class();
        else throw new \InvalidArgumentException("Cannot load channel $channel, it does not exist");
    }

    private function loadPattern()
    {
        if($this->request->getData('pattern') !== null) $pattern = $this->request->getData('pattern');
        else $pattern = $this->config['defaults']['pattern'];
        $class = '\\Freesewing\\Patterns\\'.$pattern;
        if (class_exists($class)) return new $class();
        else throw new \InvalidArgumentException("Cannot load pattern $pattern, it does not exist");
    }

    private function loadTheme()
    {
        if($this->request->getData('theme') !== null) $theme = $this->request->getData('theme');
        else $theme = $this->config['defaults'][$this->service->getServiceName().'Theme'];
        $class = '\\Freesewing\\Themes\\'.$theme;
        if (class_exists($class)) return new $class();
        else throw new \InvalidArgumentException("Cannot load theme $theme, it does not exist");
    }

}
