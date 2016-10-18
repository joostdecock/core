<?php

namespace Freesewing\Channels;

/**
 * Freesewing\Channels\Channel class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Channel
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

    public function standardizeModelMeasurements($requestData)
    {
        foreach ($this->config['measurements'] as $key => $val) {
            $measurements[$val] = $requestData[$key] * 10;
        }

        return $measurements;
    }

    public function standardizePatternOptions($requestData)
    {
        foreach ($this->config['cmoptions'] as $key => $val) {
            $options[$val] = $requestData[$key] * 10;
        }
        foreach ($this->config['percentoptions'] as $key => $val) {
            $options[$val] = $requestData[$key] / 100;
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
