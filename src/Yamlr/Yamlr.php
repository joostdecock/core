<?php

namespace Freesewing;

use Symfony\Component\Yaml\Yaml;

/**
 * Freesewing\Yamlr class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Yamlr
{
    public static function loadYamlFile($file)
    {
        // If Yaml file is not valid, Symphony will throw and exception
        $yaml = Yaml::parse(file_get_contents($file));

        return $yaml;
    }
}
