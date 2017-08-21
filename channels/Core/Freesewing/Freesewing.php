<?php
/** Freesewing\Channels\Core\Freesewing */
namespace Freesewing\Channels\Core;

use Freesewing\Context;
use Freesewing\Utils;

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
class Freesewing extends Channel
{
    /**
     * Allows the channel designer to implement access control
     *
     * You may not want to make your channel publically accessible.
     * You can limit access here in whatever way you like.
     * You have access to the entire context to decide what to do.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return bool true Always true in this case
     */
    public function isValidRequest(Context $context)
    {
        // Info service is always ok
        if($context->getService()->getServiceName() == 'info') return true;

        // For other services, the only thing we check 
        // is whether the pattern you request does actually exist
        $pattern = $context->getPattern();
        if(isset($pattern)) {
            $patternServed = basename($context->getPattern()->getClassChain()[0]);
            $patternRequested = $context->getRequest()->getData('pattern');

            if($patternRequested == $patternServed) return true;
        }
        return false;
    }

    /**
     * Channel designer gets the final say before we send a response
     *
     * Before we send a response, you get a chance to decide
     * whether you are ok with it or not.
     *
     * This is also the place to add headers to the response.
     *
     * @param \Freesewing\Context $context The context object
     *
     * @return bool true Always true in this case
     */
    public function isValidResponse(Context $context) {
        if(isset($this->config['headers'])) {
            foreach($this->config['headers'] as $name => $value) {
                $context->getResponse()->addHeader($name, "$name: $value");
            }
        }

        return true;
    }

    /**
     * Turn input into model measurements that we understand.
     *
     * This loads measurement names from the pattern config file
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\* $pattern The pattern object
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
                $input = $request->getData($key);
                if(isset($input) && $input !== null) $measurements[$key] = $input * $factor;
                else $measurements[$key] = $pattern->config['measurements'][$key];
            }
            return $measurements;
        } else return null;

    }

    /**
     * Turn input into pattern options that we understand.
     *
     * This loads pattern options from the sampler config file
     *
     * @param \Freesewing\Request $request The request object
     * @param \Freesewing\Patterns\* $pattern The pattern object
     *
     * @return array|null The pattern options or null of there are none
     */
    public function standardizePatternOptions($request, $pattern)
    {
        if(isset($pattern->config['options']) && is_array($pattern->config['options'])) {
            $units = $pattern->getUnits();
            if($units['in'] == 'imperial') $factor = 25.4;
            else $factor = 10;

            foreach ($pattern->config['options'] as $key => $val) {
                $input = $request->getData($key);
                switch ($val['type']) {
                    case 'angle':
                        if(isset($input) && $input !== null) {
                            $options[$key] = Utils::constraint(
                                $input, 
                                $val['min'],
                                $val['max']
                            );
                        } else {
                            $options[$key] = $val['default'];
                        }
                        break;
                    case 'measure':
                        if(isset($input) && $input !== null) {
                            $options[$key] = Utils::constraint(
                                $input * $factor, 
                                $val['min'],
                                $val['max']
                            );
                        } else {
                            $options[$key] = $val['default'];
                        }
                        break;
                    case 'percent':
                        if(isset($input) && $input !== null) {
                            (isset($val['min'])) ? $min = $val['min'] : $min = 0 ;
                            (isset($val['max'])) ? $max = $val['max'] : $max = 100 ;
                            $options[$key] = Utils::constraint(
                                $input / 100,
                                $min / 100,
                                $max / 100
                            );
                        } else {
                            $options[$key] = $val['default'] / 100;
                        }
                        break;
                    case 'chooseOne':
                        if(
                            isset($input) && 
                            $input !== null
                        ) {
                            $options[$key] = $input;
                        } else {
                            $options[$key] = $val['default'];
                        }
                        break;
                }

            }

            // Seam allowance option (sa) 
            // This is different because it's not listed in the pattern config file
            if($request->getData('sa') == 0) $options['sa'] = false;
            else {
                if($request->getData('userUnits') == 'imperial') $options['sa'] = Utils::constraint($request->getData('sa')*25.4,5,25.4);
                else $options['sa'] = Utils::constraint($request->getData('sa')*10,5,25.4);
            }

            return $options;

        } else return null;
    }
}
