<?php
/** Freesewing\Patterns\Core\Pattern class */
namespace Freesewing\Patterns\Core;

use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Abstract class for patterns.
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Pattern
{
    /**
     * PI with max needed precision
     */
    const PI = 3.1415;

    /** @var array $parts Holds the pattern parts */
    public $parts = array();

    /** @var array $replacements Holds pattern string replacements */
    public $replacements = array();

    /** @var bool $isPaperless Add paperless info to pattern or not? */
    public $isPaperless;

    /** @var array $options Pattern options */
    private $options = array();

    /** @var array $values Pattern values */
    private $values = array();

    /** @var int $width Pattern width */
    private $width;

    /** @var int $height Pattern height */
    private $height;

    /** @var array $units Pattern units */
    private $units;

    /** @var array $messages Messages to include in SVG source */
    private $messages;

    /** @var float $partMargin Margin between pattern parts */
    private $partMargin;

    /** @var \Freesewing\GrowingPacker $packer */
    private $packer;

    /** @var array $layoutBlocks */
    private $layoutBlocks;

    /** @var array $debug collection of debug messages */
    private $debug;

    /**
     * Constructor stores Yaml config file in the config property
     *
     * @throws ParseException if the Yaml file is not valid
     */
    public function __construct($units='metric')
    {
        if (is_readable($this->getConfigFile())) {
            $this->config = \Freesewing\Yamlr::loadYamlFile($this->getConfigFile());
            $this->setUnits($units);
            $this->loadParts();
            $this->replace('__TITLE__', isset($this->config['info']['name']) ? $this->config['info']['name'] : NULL);
            $this->replace('__VERSION__', isset($this->config['info']['version']) ? $this->config['info']['version'] : NULL);
            $this->replace('__COMPANY__', isset($this->config['info']['company']) ? $this->config['info']['company'] : NULL);
            $this->replace('__AUTHOR__', isset($this->config['info']['author']) ? $this->config['info']['author'] : NULL);
        }
        $this->replace('__DATE__', date('l j F Y') !=  null ? date('l j F Y') : NULL);

        return $this;
    }

    /**
     * Makes sure to unset the parts array when cloning a pattern
     *
     * This is used by the sample service
     *
     * @see \Freesewing\MeasurementsSampler::samplerMeasurements()
     */
    public function __clone()
    {
        unset($this->parts);
    }

    /**
     * Patterns must implement this method called by the DraftService
     *
     * @param \Freesewing\Context $context The context
     */
    abstract public function draft($context);

    /**
     * Patterns must implement this method called by the SampleService
     *
     * @param \Freesewing\Context $context The context
     */
    abstract public function sample($context);

    /**
     * Add parts in config file to pattern
     *
     * This prevents you from having to manually add all parts.
     * Note that if there are parts you don't need
     * (depending on options for example) you could
     * override this function. Or, you can simple call
     * setRender(false) on them to keep them from being rendered.
     */
    public function loadParts()
    {
        foreach ($this->config['parts'] as $part => $title) {
            $this->newPart($part);
            $this->parts[$part]->setTitle($title);
        }
    }

    /**
     * Returns directory that holds translation file
     *
     * @return string $dir The directory holding the transaltion files
     */
    private function getTranslationsDir()
    {
        return \Freesewing\Utils::getClassDir($this) . '/translations';
    }

    /**
     * Returns the pattern config file
     *
     * @return string $config The pattern config file
     */
    private function getConfigFile()
    {
        return \Freesewing\Utils::getClassDir($this) . '/config.yml';
    }

    /**
     * Returns the pattern configuration
     *
     * This loads the configation Yaml file and returns it as an array
     *
     * @throws ParseException If the Yaml file is invalid
     *
     * @return array The pattern configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the units property
     *
     * @return string imperial|metric
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Returns a value from the options array
     *
     * @param string $key The key in the options array for which to return the value
     *
     * @return mixed $value The option value
     */
    public function getOption($key)
    {
        return $this->options[$key];
    }

    /**
     * Returns a value from the values array
     *
     * @param string $key The key in the values array for which to return the value
     *
     * @return mixed $value The value
     */
    public function getValue($key)
    {
        return $this->values[$key];
    }

    /**
     * Alias for getOption()
     *
     * @param string $key The key in the options array for which to return the value
     *
     * @return mixed $value The option value
     */
    public function o($key)
    {
        return $this->getOption($key);
    }

    /**
     * Alias for getValue()
     *
     * @param string $key The key in the values array for which to return the value
     *
     * @return mixed $value The value
     */
    public function v($key)
    {
        return $this->getValue($key);
    }

    /**
     * Sets the key $key in the options array to value $value
     *
     * @param string $key   The key in the options array
     * @param mixed  $value The option to set
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * Sets the key $key in the values array to value $value
     *
     * @param string $key   The key in the values array
     * @param mixed  $value The value to set
     */
    public function setValue($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * Sets the width property
     *
     * @param float $width The width of the pattern
     */
    private function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Returns the height property
     *
     * @return float $height The height of the pattern
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the height property
     *
     * @param float $height The height of the pattern
     */
    private function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Returns the height property
     *
     * @return float $height The height of the pattern
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the partMargin property
     *
     * @param float $margin The margin between pattern parts
     */
    public function setPartMargin($margin)
    {
        $this->partMargin = $margin;
    }

    /**
     * Clones points from one pattern part into another
     *
     * @param string $from The ID in the parts array of the source part
     * @param string $into The ID in the parts array of the destination part
     */
    public function clonePoints($from, $into)
    {
        foreach ($this->parts[$from]->points as $key => $point) {
            $this->parts[$into]->addPoint($key, $point);
        }
    }

    /**
     * Stacks all parts in the top left corner in preparation of layout
     *
     * When you draw a part, there's no reason it would have point (0,0) as
     * its top left corner. This takes care of that by pushing everything to
     * the top left (by adding a translate transform). We do this before
     * we layout the pattern with our packer.
     */
    private function pileParts()
    {
        if (isset($this->parts) && count($this->parts) > 0) {
            foreach ($this->parts as $part) {
                if ($part->getRender() === true) {
                    $offsetX = @$part->boundary->topLeft->x * -1; // FIXME Sample service issues a warning here
                    $offsetY = @$part->boundary->topLeft->y * -1; // FIXME Sample service issues a warning here
                    $transform = new \Freesewing\Transform('translate', $offsetX, $offsetY);
                    $part->addTransform('#pileParts', $transform);
                }
            }
        }
    }

    /**
     * Adds a part to the pattern by adding it to the parts array
     *
     * @param string $key The ID in the parts array of the part to add
     */
    public function newPart($key)
    {
        if (is_numeric($key) || is_string($key)) {
            $part = new \Freesewing\Part();
            $part->setUnits($this->units);
            $this->parts[$key] = $part;
        }
    }

    /**
     * Calls $part->addBoundary() on all parts
     */
    private function addPartBoundaries()
    {
        if (isset($this->parts) && count($this->parts) > 0) {
            foreach ($this->parts as $part) {
                if ($part->getRender() === true) {
                    $part->addBoundary($this->partMargin);
                }
            }
        }
    }

    /**
     * Adds an array of options to the options property
     *
     * Each key in the array (along with its value)
     * will be added to the options array, overwriting
     * options if they pre-exist
     *
     * @param array $array Array of options to add
     */
    public function addOptions($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->options[$key] = $value;
            }
        }
    }

    /**
     * Lays out pattern parts on the page
     *
     * This uses a packer to automatically lay out
     * the different pattern pieces on that page.
     */
    public function layout()
    {
        $this->addPartBoundaries();
        $this->pileParts();

        $this->packer = new \Freesewing\GrowingPacker();
        $this->layoutBlocks = $this->layoutPreSort($this->parts);
        $this->packer->fit($this->layoutBlocks);
        $this->layoutTransforms($this->layoutBlocks);
        $this->setWidth($this->packer->boundingBox->w);
        $this->setHeight($this->packer->boundingBox->h);
    }

    /**
     * Clean up before exiting
     */
    public function cleanUp()
    {
    }

    /**
     * Add a message to the pattern
     *
     * This is a helper function that pushes messages onto
     * the messages property.
     * In the default theme, these will be added to the
     * footer comments in the SVG file.
     *
     * @see \Freesewing\Theme::loadTemplates()
     *
     * @param string $msg The message to add
     */
    public function msg($msg)
    {
        $this->messages[] = $msg;
    }

    /**
     * Add a debug message to the pattern
     *
     * This is a helper function that pushes messages onto
     * the debug property.
     * In the default theme, these will not be included.
     * But in the designer theme, they will be added to the
     * footer comments in the SVG file.
     *
     * @see \Freesewing\Theme::loadTemplates()
     *
     * @param string $msg The message to add
     */
    public function dbg($msg)
    {
        $this->debug[] = $msg;
    }

    /**
     * Returns messages in the debug property as a text block
     *
     * @return string $messages The debug messages stored in the pattern
     */
    public function getDebug()
    {
        if (isset($this->debug)) {
            return implode("\n", $this->debug);
        } else {
            return false;
        }
    }

    /**
     * Returns messages in the messages property as a text block
     *
     * @return string $messages The messages stored in the pattern
     */
    public function getMessages()
    {
        if (isset($this->messages)) {
            return implode("\n", $this->messages);
        } else {
            return false;
        }
    }

    /**
     * Adds a search/replace pair to the replacements property
     *
     * @see \Freesewing\Service\DraftService::run()
     *
     * @param string $search  The string to search for
     * @param string $replace The string to replace it with
     */
    public function replace($search, $replace)
    {
        if ($replace != NULL)
        {
            $this->replacements[$search] = $replace;
        }
    }

    /**
     * Returns the replacements property
     *
     * @return array $replancements The replacements
     */
    public function getReplacements()
    {
        return $this->replacements;
    }

    /**
     * Stores a translator object in the translator property
     *
     * @param \Symfony\Component\Translation\Translator $translator The translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * Sets the units property
     *
     * @param array $units An array of units like ['in' => 'metric', 'out' => 'imperial']
     */
    public function setUnits($units)
    {
        $this->units = $units;
    }

    /**
     * Returns a translated string
     *
     * This is just a front for the trans method in the Symfony Translator
     *
     * @param string $msg The string to translate
     */
    public function t($msg)
    {
        return $this->translator->trans($msg);
    }

    /**
     * Add transforms to parts to implement layout calculated by the packer
     *
     * The packer does not actually layout the pattern. It merely calculates it
     * and returns an array of 'layoutblocks' that contain the needed information
     * on how to layout the different parts.
     * This takes that info and applies a tranform to the parts for it, thereby
     * making implementing the calculated layout in the SVG document.
     *
     * @param array $layoutBlocks Array of layoutblocks calculated by the packer
     */
    private function layoutTransforms($layoutBlocks)
    {
        foreach ($layoutBlocks as $key => $layoutBlock) {
            $transform = new \Freesewing\Transform('translate', $layoutBlock->fit->x, $layoutBlock->fit->y);
            $this->parts[$key]->addTransform('#layout', $transform);
        }
    }

    /**
     * Sorts parts by their largest side
     *
     * Our current packer (GrowingPacker) requires that we feed it
     * a list of pattern parts that are sorted by their largest size
     * (widht or height, whatever is highest).
     * This takes care of that.
     *
     * @param array $parts Array of pattern parts
     *
     * @return array|bool
     */
    private function layoutPreSort($parts)
    {
        $order = array();
        foreach ($parts as $key => $part) {
            if ($part->getRender() === true) {
                $order[$key] = @$part->boundary->maxSize; // FIXME Sample service issues a warning here
            }
        }
        arsort($order);
        foreach ($order as $key => $maxSize) {
            $layoutBlock = new \Freesewing\LayoutBlock();
            $layoutBlock->w = @$parts[$key]->boundary->width; // FIXME Sample service issues a warning here
            $layoutBlock->h = @$parts[$key]->boundary->height;// FIXME Sample service issues a warning here
            $sorted[$key] = $layoutBlock;
        }
        
        return $sorted;
    }

    /**
     * Returns an array of classes between this and the abstract pattern class
     *
     * If your pattern extends another pattern (and another, and ...) this
     * will construct the class chain up to the abstract pattern class.
     * This is needed for the theme to load template files hierarchically
     * In other words, when you extend a pattern, this makes sure the templates
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
        } while ($parent->name != 'Freesewing\Patterns\Core\Pattern');

        return $locations;
    }

    /**
     * Returns list of translation files
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
     * Returns list of sampler files
     *
     * @return array $files An array of sampler config files
     */
    private function getSamplerModelFiles()
    {
        $locations = $this->getClassChain();
        $files = array();
        foreach ($locations as $location) {
            $file = "$location/sampler/models.yml";
            if (is_readable($file)) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Returns list of sampler files
     *
     * @return array $files An array of sampler config files
     */
    public function getSamplerModelConfig()
    {
        $files = $this->getSamplerModelFiles(); // HERE
        $data = array();
        foreach ($files as $file) {
            $data = array_merge_recursive($data, \Freesewing\Yamlr::loadYamlFile($file));
        }

        return $data;
    }

    /**
     * Sets the paperless property
     *
     * This is used to determine whether to include the papeless-specific
     * stuff in the pattern.
     *
     * @param bool $bool True or false
     */
    public function setPaperless($bool)
    {
        $this->isPaperless = $bool;
    }
}
