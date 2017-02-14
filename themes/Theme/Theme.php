<?php
/** Freesewing\Themes\Sampler class */
namespace Freesewing\Themes;

use Freesewing\Context;
use Freesewing\Patterns\Pattern;
use Freesewing\SvgDocument;
use Freesewing\Utils;

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

    /** @var array $debug Debug to include in the pattern */
    public $debug = array();

    /** @var array $options Array of theme options */
    public $options = array();

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
     * Returns the option identified by $key
     *
     * @param string $key The key of the option in the options array
     *
     * @return mixed The value of the option
     */
    protected function getOption($key)
    {
        if(isset($this->options[$key])) return $this->options[$key];
        else return null;
    }

    /**
     * Returns the location of the theme config file
     */
    protected function getConfigFile()
    {
        return Utils::getClassDir($this).'/config.yml';
    }
    /**
     * Returns true if isPaperless is set to true in theme config
     *
     * This will be used to determine whether to include the extra
     * information for paperless on the pattern.
     * Extra information is things like instructions, notes and
     * seamlengths.
     *
     * @return true|false True is isPaperless is true in the config settings
     */
    public function isPaperless()
    {
        if ($this->config['settings']['isPaperless'] === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Loads messages/debug from pattern into messages/debug property
     *
     * @param Pattern $pattern The pattern object
     */
    public function themePattern(Pattern $pattern)
    {
        $this->messages = $pattern->getMessages();
        $this->debug = $pattern->getDebug();

        $pattern->replace('__SCALEBOX_METRIC__', $pattern->t('__SCALEBOX_METRIC__'));
        $pattern->replace('__SCALEBOX_IMPERIAL__', $pattern->t('__SCALEBOX_IMPERIAL__'));
    }

    /**
     * Adds templates to the SvgDocument
     *
     * @param SvgDocument $svgDocument The SvgDocument
     */
    public function themeSvg(SvgDocument $svgDocument)
    {
        $this->loadTemplates($svgDocument);
        
        if ($this->messages) {
            $svgDocument->footerComments->add($this->messages);
        }
        
        if ($this->debug) {
            $svgDocument->footerComments->add("\n\n\tDEBUG OUTPUT\n\n".$this->debug);
        }
    }

    /**
     * Adds templates to the SvgDocument
     *
     * @param SvgDocument $svgDocument The SvgDocument
     */
    private function loadTemplates($svgDocument)
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
    }

    /**
     * Returns a Response object with our SvgDocument in it
     *
     * @param context $context The context object
     * @return \Freesewing\Response
     */
    public function themeResponse(Context $context)
    {
        $response = new \Freesewing\Response();
        $response->addCacheHeaders($context->getRequest());
        $response->addHeader('Content-Type', 'Content-Type: image/svg+xml');
        $response->setFormat('svg');
        $response->setBody("{$context->getSvgDocument()}");

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
     * are extended too.
     * That means that the parent theme templates are loaded, unless
     * you override them in your extended theme.
     *
     * @return array $templates Array with template files
     */
    private function loadTemplateHierarchy()
    {
        $locations = $this->getClassChain();
        $templates = array();
        foreach ($locations as $location) {
            if (is_readable("$location/config.yml")) {
                $dir = "$location/templates";
                $config = \Freesewing\Yamlr::loadYamlFile("$location/config.yml");
                if (isset($config['templates'])) {
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
    private function getClassChain()
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
    protected function getTemplateDir()
    {
        return Utils::getClassDir($this).'/templates';
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
            $locfile = sprintf("%s/translations/messages.%s.yml", $location, $locale);
            $altfile = sprintf("%s/translations/messages.%s.yml", $location, $altloc);
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
     * A way for themes to set options based on the request data
     *
     * Maybe you want your theme to do something based on a request parameter
     * This allows you to configure these options in one place
     *
     * @param \Freesewing\Request $request The request object
     */
    public function setOptions($request)
    {
        foreach ($this->config['options'] as $key) {
            $value = $request->getData($key);
            if (strpos($value,',')) $this->options[$key] = Utils::asScrubbedArray($value, ',');
            else $this->options[$key] = $value;
        }
    }

    /**
     * A way for a theme to ultimately decide what should be rendered
     *
     * @param \Freesewing\Patterns\Pattern $pattern The pattern object
     */
    public function applyRenderMask(Pattern $pattern)
    {
        if($this->getOption('parts')) $this->applyRenderMaskOnParts($pattern);
    }

    /**
     * Sets the render property on parts based on theme options
     *
     * You can use the following request parameter to control the render mask for parts:
     *
     *  - parts
     *  - forceParts
     *
     * If *parts* is an array, all parts NOT in the array will NOT be rendered
     * If in addition, *forceParts* is 1, only parts in the *parts* array will be rendered
     * The difference is that *forceParts* will force all parts in the *parts* array
     * to be rendered. Even those that have their render property set to false.
     *
     * @param \Freesewing\Patterns\Pattern $pattern The pattern object
     */
    private function applyRenderMaskOnParts(Pattern $pattern)
    {
        $parts = $this->getOption('parts');
        // Force into array, even if it's just 1 part
        if(isset($parts) && !is_array($parts) && isset($pattern->parts[$parts])) $parts = [$parts];
        
        foreach ($pattern->parts as $key => $part) {
            if (!in_array($key, $parts)) {
                // Don't render what's not included
                $pattern->parts[$key]->setRender(false);
            } else {
                if ($this->getOption('forceParts')) {
                    // Force render of what's included
                    $pattern->parts[$key]->setRender(true);
                }
            }
        }

    }
}
