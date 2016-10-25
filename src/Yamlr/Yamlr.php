<?php
/** Freesewing\Yamlr class */
namespace Freesewing;

use Symfony\Component\Yaml\Yaml;

/**
 * A wafer-thin wrapper around Symfony\Component\Yaml\Yaml
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Yamlr
{
    /**
     * Loads a Yaml file and returns it as an array
     *
     * @param string $file The name of the file to load
     *
     * @return array The Yaml file as array
     *
     * @throws exception Symphony will throw and exception if the Yaml file is not valid
     */
    public static function loadYamlFile($file)
    {
        $yaml = Yaml::parse(file_get_contents($file));

        return $yaml;
    }
}
