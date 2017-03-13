<?php
/** Freesewing\Services\InfoService class */
namespace Freesewing\Services;

use Freesewing\Context;
use Freesewing\Utils;

/**
 * Handles the info service, providing info about the API.
 *
 * This InfoService class aims to make frontend integration simpler.
 * You can see it at work in the demo that is part of the documentation.
 *
 * @see       http://api.freesewing.org/docs/demo/
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class InfoService extends Service
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
        return 'info';
    }

    /**
     * Provides info
     *
     * This assembles information, sets the response and sends it
     * Essentially, it takes care of the entire remainder of the request
     *
     * @param \Freesewing\Context
     */
    public function run(Context $context)
    {
        if ($context->getChannel()->isValidRequest($context) === true) :
            $format = $context->getRequest()->getData('format');
            if ($context->getRequest()->getData('pattern') !== null) {
                $context->addPattern();
                $context->setResponse($context->getTheme()->themePatternInfo($this->getPatternInfo($context->getPattern()), $format));
            } else {
                $info['services'] = $context->getConfig()['services'];
                $info['patterns'] = $this->getPatternList($context);
                $info['channels'] = $this->getChannelList($context);
                $info['themes'] = $this->getThemeList($context);

                $context->setResponse($context->getTheme()->themeInfo($info, $format));
            }
        else :
            // channel->isValidRequest() !== true
            $context->getChannel()->handleInvalidRequest($context);
        endif;

        // Don't send response without approval from the channel
        if($context->getChannel()->isValidResponse($context)) {
            $context->getResponse()->send();
        } else {
            $context->getChannel()->handleInvalidResponse($context);
        }

        $context->cleanUp();
    }

    /**
     * Returns list of available patterns
     *
     * @param \Freesewing\Context
     *
     * @return array
     */
    private function getPatternList($context)
    {
        $list = [];
        foreach($context->getConfig()['patternNamespaces'] as $ns) {
            foreach (glob(Utils::getApiDir() . "/patterns/$ns/*", GLOB_ONLYDIR) as $dir) {
                $name = basename($dir);
                if ($name != 'Pattern') {
                    $config = $this->loadPatternConfig($name, $context);
                    if($config['hidden'] !== true) $list[$ns][$name] = $config['info']['name'];
                }
            }
        }
        
        return $list;
    }

    /**
     * Returns configuration for a pattern
     *
     * @param string pattern The name of the pattern
     *
     * @return array
     */
    private function loadPatternConfig($pattern, $context)
    {
        foreach($context->getConfig()['patternNamespaces'] as $ns) {
            $class = '\\Freesewing\\Patterns\\'.$ns.'\\'.$pattern;
            if (class_exists($class)) {
                $pattern = new $class();
                return $pattern->getConfig();
            }
        }
    }

    /**
     * Returns list of available channels
     *
     * @param \Freesewing\Context
     *
     * @return array
     */
    private function getChannelList($context)
    {
        $list = [];
        foreach($context->getConfig()['channelNamespaces'] as $ns) {
            foreach (glob(Utils::getApiDir() . "/channels/$ns/*", GLOB_ONLYDIR) as $dir) {
                $name = basename($dir);
                if ($name != 'Channel' && $name != 'Info') {
                    $list[$ns][] = $name;
                }
            }
        }

        return $list;
    }

    /**
     * Returns list of available themes
     *
     * @param \Freesewing\Context
     *
     * @return array
     */
    private function getThemeList($context)
    {
        $list = [];
        foreach($context->getConfig()['themeNamespaces'] as $ns) {
            foreach (glob(Utils::getApiDir() . "/themes/$ns/*", GLOB_ONLYDIR) as $dir) {
                $name = basename($dir);
                if ($name != 'Theme' && $name != 'Info' && $name != 'Sampler') {
                    $list[$ns][] = $name;
                }
            }
        }

        return $list;
    }

    /**
     * Returns information about a pattern
     *
     * @param string pattern The pattern name
     *
     * @return array
     */
    private function getPatternInfo($pattern)
    {
        $info = $pattern->getConfig();
        $info['models'] = $pattern->getSamplerModelConfig();
        $info['pattern'] = basename(Utils::getClassDir($pattern));

        return $info;
    }
}
