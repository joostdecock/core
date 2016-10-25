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
 * @copyright 2016 Joost De Cock
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
     *  - uti
     *  - time
     * based on the request
     *
     * @param array $data The request data
     */
    public function __construct($data=null)
    {
        $this->data = $data;
        $this->info['client'] = $_SERVER['REMOTE_ADDR'];
        $this->info['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        $this->info['host'] = $_SERVER['HTTP_HOST'];
        $this->info['uri'] = $_SERVER['REQUEST_URI'];
        $this->info['time'] = $_SERVER['REQUEST_TIME_FLOAT'];
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
