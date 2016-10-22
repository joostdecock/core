<?php

namespace Freesewing;

/**
 * Freesewing\Request class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Request
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->client = $_SERVER['REMOTE_ADDR'];
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->host = $_SERVER['HTTP_HOST'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->time = $_SERVER['REQUEST_TIME_FLOAT'];
    }

    public function getData($key)
    {
        if(isset($this->data[$key])) return $this->data[$key];
        else return null;
    }

}
