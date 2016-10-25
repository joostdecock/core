<?php
/** Freesewing\Channels\Docs */
namespace Freesewing\Channels;

/**
 * Channel used by the Documentation.
 *
 * This channel is what we use for the documentation
 * and more precisely the demo.
 * It implements the bare minimum, and if you're looking
 * to implement your own channel, this is a good place to start.
 *
 * @see http://api.freesewing.org/docs/demo/
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Docs extends Channel
{
    /**
     * Turn input into model measurements that we understand.
     *
     * This loads measurement names from the pattern config file
     * 
     * @todo What to do when measurments are missing?
     * @todo What about imperial?
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\[pattern] $pattern The pattern object
     *
     * @return array The model measurements
     */ 
    public function standardizeModelMeasurements($request, $pattern)
    {
        foreach ($pattern->config['measurements'] as $key => $val) {
            $measurements[$val] = $request->getData($val) * 10;
        }

        return $measurements;
    }

    /**
     * Turn input into pattern options that we understand.
     *
     * This loads pattern options from the sampler config file
     * 
     * @todo What to do when options are missing?
     * @todo What about imperial?
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\[pattern] $pattern The pattern object
     *
     * @return array The pattern options
     */ 
    public function standardizePatternOptions($request, $pattern)
    {
        $samplerOptionConf = \Freesewing\Yamlr::loadYamlFile(\Freesewing\Utils::getClassDir($pattern).'/sampler/options.yml');
        foreach ($samplerOptionConf as $key => $option) {
            switch ($option['type']) {
                case 'percent':
                    if ($request->getData($key) !== null) {
                        $options[$key] = $request->getData($key) / 100;
                    } else {
                        $options[$key] = $option['default'] / 100;
                    }
                break;
                default:
                    if ($request->getData($key) !== null) {
                        $options[$key] = $request->getData($key) * 10;
                    } else {
                        $options[$key] = $option['default'] * 10;
                    }
                break;
            }
        }

        return $options;
    }
}
