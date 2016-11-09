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
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\[pattern] $pattern The pattern object
     *
     * @return array|null The model measurements or null of there are none
     */ 
    public function standardizeModelMeasurements($request, $pattern)
    {
        if(isset($pattern->config['measurements']) && is_array($pattern->config['measurements'])) {
            $units = $pattern->getUnits();
            if($units['in'] == 'imperial') $factor = 25.4;
            else $factor = 10;
            foreach ($pattern->config['measurements'] as $key => $val) {
                $measurements[$val] = $request->getData($val) * $factor;
            }
            return $measurements;
        } else return null;

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
     * @return array|null The pattern options or null of there are none
     */ 
    public function standardizePatternOptions($request, $pattern)
    {
        $units = $pattern->getUnits();
        if($units['in'] == 'imperial') $factor = 25.4;
        else $factor = 10;

        $samplerOptionConf = \Freesewing\Yamlr::loadYamlFile(\Freesewing\Utils::getClassDir($pattern).'/sampler/options.yml');
        if(is_array($samplerOptionConf)) {
            foreach ($samplerOptionConf as $key => $option) {
                if ($request->getData($key) !== null) {
                    $options[$key] = $request->getData($key);
                } else {
                    $options[$key] = $option['default'];
                }

                switch ($option['type']) {
                    case 'measure':
                        $options[$key] = $options[$key] * $factor;
                        break;
                    case 'percent':
                        $options[$key] = $options[$key] / 100;
                        break;
                }
            }
            return $options;
        } else return null;
    }
}
