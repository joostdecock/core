<?php
/** Freesewing\Themes\Sampler class */
namespace Freesewing\Themes;

/**
 * Abstract class for themes.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Theme
{
    /** @var array $messages Messages to include in the pattern */
    public $messages = array();

    /**
     * Constructor loads the Yaml config file into the config property
     *
     * @throws Exception if the Yaml file is invalid
     */
    public function __construct()
    {
        if (is_readable($this->getConfigFile())) {
            $this->config = \Freesewing\Yamlr::loadYamlFile($this->getConfigFile());
        }
    }
    
    /**
     * Returns the location of the theme config file
     */
    public function getConfigFile()
    {
        return \Freesewing\Utils::getClassDir($this).'/config.yml';
    }

    /**
     * Loads message from pattern into messages property
     *
     * @param \Freesewing\Patterns\* $pattern The pattern object
     */
    public function themePattern($pattern)
    {
        $this->messages = $pattern->getMessages();
    }

    /**
     * Adds templates to the SvgDocument
     *
     * @param \Freesewing\SvgDocument $svgDocument The SvgDocument
     */
    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $this->loadTemplates($svgDocument);
    }

    /**
     * Adds templates to the SvgDocument
     *
     * @param \Freesewing\SvgDocument $svgDocument The SvgDocument
     */
    public function loadTemplates($svgDocument)
    {
        $templates = $this->loadTemplateHierarchy();
        if (isset($templates['js'])) {
            foreach ($templates['js'] as $js) {
                $svgDocument->script->add($js);
            }
        }
        if (isset($templates['css'])) {
            foreach ($templates['css'] as $css) {
                $svgDocument->css->add($css);
            }
        }
        if (isset($templates['defs'])) {
            foreach ($templates['defs'] as $defs) {
                $svgDocument->defs->add($defs);
            }
        }
        if (isset($templates['header'])) {
            foreach ($templates['header'] as $comments) {
                $svgDocument->headerComments->add($comments);
            }
        }
        if (isset($templates['footer'])) {
            foreach ($templates['footer'] as $comments) {
                $svgDocument->headerComments->add($comments);
            }
        }
        if (isset($templates['attributes'])) {
            foreach ($templates['attributes'] as $attr) {
                $svgDocument->svgAttributes->add($attr);
            }
        }

        if ($this->messages !== false) {
            $svgDocument->footerComments->add(implode("\n", $this->messages));
        }
    }

    /**
     * Returns a Response object with our SvgDocument in it
     *
     * @param \Freesewing\context $context The context object
     */
    public function themeResponse($context)
    {
        $response = new \Freesewing\Response();
        $response->setFormat('raw');
        $response->setBody("{$context->svgDocument}");

        return $response;
    }

    /**
     * This does nothing, but gets called for themes who want it
     */
    public function cleanUp()
    {
    }

    /**
     * Loads templates from themes and possible parent themes
     *
     * This makes sure that when you extend a theme, the templates 
     * are extended to.
     * That means that the parent theme templates are loaded, unless
     * you override them in your extended theme.
     *
     * @return array $templates Array with template files
     */
    public function loadTemplateHierarchy()
    {
        $locations = $this->getClassChain();
        $templates = array();
        foreach ($locations as $location) {
            if (is_readable("$location/config.yml")) {
                $dir = "$location/templates";
                $config = \Freesewing\Yamlr::loadYamlFile("$location/config.yml");
                if(isset($config['templates'])) {
                    foreach ($config['templates'] as $type => $entries) {
                        foreach ($entries as $entry) {
                            if (!isset($templates[$type][$entry])) {
                                $template = "$location/templates/$entry";
                                if (is_readable($template)) {
                                    $templates[$type][$entry] = file_get_contents($template);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $templates;
    }

    /**
     * Returns an array of classes between this and the abstract theme class
     *
     * If your ptheme extends another theme (and another, and ...) this
     * will construct the class chain up to the abstract theme class.
     * This is needed for the theme to load template files hierarchically
     * In other words, when you extend a theme, this makes sure the templates
     * are extended too.
     *
     * @return array $locations An array of class directories
     */ 
    public function getClassChain()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filename = $reflector->getFileName();
        $locations[] = dirname($filename);
        do {
            $parent = $reflector->getParentClass();
            $reflector = new \ReflectionClass($parent->name);
            $filename = $reflector->getFileName();
            $locations[] = dirname($filename);
        } while ($parent->name != 'Freesewing\Themes\Theme');

        return $locations;
    }

    /**
     * Returns the themes's template directory
     *
     * @return string $dir The directory path
     */
    public function getTemplateDir()
    {
        return \Freesewing\Utils::getClassDir($this).'/templates';
    }

    /**
     * Returns the theme translation translation files
     *
     * @param string $locale The primary locale
     * @param string $altloc The fallback locale
     *
     * @return array $translations An array of translation files
     */
    public function getTranslationFiles($locale, $altloc)
    {
        $locations = $this->getClassChain();
        $translations = array();
        foreach ($locations as $location) {
            $locfile = "$location/translations/messages.$locale.yml";
            $altfile = "$location/translations/messages.$altloc.yml";
            if (is_readable($locfile)) {
                $translations[$locale][] = $locfile;
            }
            if (is_readable($altfile)) {
                $translations[$altloc][] = $altfile;
            }
        }

        return $translations;
    }

    /**
     * Themes a path for the sampler servic
     *
     * This is only needed for themes that are used by the sampler serivce
     * But heck, best include it here just in case
     *
     * @param int $step Current step (in the sampler)
     * @param int $steps Total steps (in the sampler)
     *
     * @return void null
     *
     */ 
    public function samplerPathStyle($step, $totalSteps)
    {
        return null;
    }

    /**
     * Returns the name of the theme
     *
     * @return string $name The theme name
     */
    public function getThemeName()
    {
        return basename(\Freesewing\Utils::getClassDir($this)); 
    }
}
