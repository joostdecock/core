<?php

namespace Freesewing;

/**
 * Freesewing\Utils class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Utils
{
    public static function asScrubbedArray($data, $separator=' ')
    {
        $array = explode($separator, $data);
        foreach($array as $value) {
            if(rtrim($value) != '') $return[] = rtrim($value);
        }
        return $return;
    }

    public static function getUid($prefix='')
    {
        return uniqid($prefix);
    }
}
