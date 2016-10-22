<?php

namespace Freesewing\Channels;

/**
 * Freesewing\Channels\Channel class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Channel
{
    private $config = array();

    public function __construct()
    {
        $this->config = \Freesewing\Yamlr::loadYamlFile($this->getChannelDir().'/config.yml');
    }

    public function isValidRequest($requestData)
    {
        return true;
    }

    public function cleanUp()
    {
    }

    public function standardizeModelMeasurements($request, $pattern)
    {
        foreach ($pattern->config['measurements'] as $key => $val) {
            $measurements[$val] = $request->getData($val) * 10;
        }

        return $measurements;
    }

    public function standardizePatternOptions($request, $pattern)
    {
        $samplerOptionConf = \Freesewing\Yamlr::loadYamlFile($pattern->getPatternDir().'/sampler/options.yml');
        foreach ($samplerOptionConf as $key => $option) {
            switch($option['type']) {
                case 'percent':
                    if($request->getData($key) !== null) $options[$key] = $request->getData($key) /100;
                    else $options[$key] = $option['default'] /100;
                break;
                default:
                    if($request->getData($key) !== null) $options[$key] = $request->getData($key) *10;
                    else $options[$key] = $option['default'] *10;
                break;
            }
        }

        return $options;
    }
    
    public function getChannelDir()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filename = $reflector->getFileName();

        return dirname($filename);
    }
}
