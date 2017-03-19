<?php
/** Freesewing\Request class */
namespace Freesewing;

/**
 * Stores request data.
 *
 * This class stores request parameters along
 * with information about the client
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 20162017 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Request
{
    /** @var array $data Request parameters */
    private $data;

    /** @var array $info Client and request info */
    private $info;

    /**
     * Stores data passed to it and client info
     *
     * The constructor stores the data passed to it
     * in the data property, and sets these properties
     *  - userAgent
     *  - host
     *  - uri
     *  - time
     * based on the request
     *
     * @param array $data The request data
     */
    public function __construct($data = null)
    {
        if(php_sapi_name() === 'cli') {
            // Get command line parameters
            $input = $_SERVER['argv'];
            if(count($input) > 1) {
                array_shift($input);
                foreach ($input as $pair) {
                    if(strpos($pair, '=')) {
                        $keyval = explode('=', $pair);
                        $key = $keyval[0];
                        $value = $keyval[1];
                        $data[$key] = $value;
                    }
                }
                if(isset($data)) $this->data = $data;
            } else if (strpos($input[0], 'phpunit')) { // Called as unit test
                $this->data = $data;
            } else {
                die("\nCommand-line use is supported, but requires arguments\n\n");
            }
        } else { // Called through browser
            $this->data = $data;
            if(isset($_SERVER['REMOTE_ADDR'])) $this->info['client'] = $_SERVER['REMOTE_ADDR'];
            else $this->info['client'] = 'unknown';
            
            if(isset($_SERVER['HTTP_USER_AGENT'])) $this->info['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
            else $this->info['userAgent'] = 'unknown';
            
            if(isset($_SERVER['HTTP_HOST'])) $this->info['host'] = $_SERVER['HTTP_HOST'];
            else $this->info['host'] = 'unknown';
            
            if(isset($_SERVER['REQUEST_URI'])) $this->info['uri'] = $_SERVER['REQUEST_URI'];
            else $this->info['uri'] = 'unknown';
        
        }
        if(isset($_SERVER['REQUEST_TIME_FLOAT'])) $this->info['time'] = $_SERVER['REQUEST_TIME_FLOAT'];
        else $this->info['time'] = time();
    }

    /**
     * Returns a value from the data array
     *
     * This returns the value stored at a given
     * key in the data array.
     * If you want all data instead, use the
     * getAllData method
     *
     * @param string $key The key in the data array
     *
     * @return null|data
     */
    public function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    /**
     * Returns the data property
     *
     * @return array The data
     */
    public function getAllData()
    {
        return $this->data;
    }

    /**
     * Returns the info property
     *
     * @return array The info
     */
    public function getInfo()
    {
        return $this->info;
    }
}
