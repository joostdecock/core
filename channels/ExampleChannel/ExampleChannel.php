<?php

namespace Freesewing\Channels;

/**
 * Freesewing\Channels\ExampleChannel class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ExampleChannel extends Channel
{
    private $config = array();
    private $configFile = __DIR__.'/config.yml';

    public function __construct()
    {
        $this->config = \Freesewing\Yamlr::loadConfig($this->configFile);
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
}
