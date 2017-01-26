<?php

namespace Freesewing;

abstract class Output
{
    public static $headers = array();
    public static $body;

    public static function reset()
    {
        self::$headers = array();
        self::$body = null;
    }
}

function header($value)
{
    Output::$headers[] = $value;
}

function printf($format,$string)
{
    Output::$body .= $string;
}
