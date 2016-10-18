<?php

namespace Freesewing;

/**
 * Freesewing\Model class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Model
{
    public $name;
    /**
     * @var array
     */
    private $measurements = array();

    public function setMeasurement($key, $value)
    {
        $this->measurements[$key] = $value;
    }

    public function getMeasurement($key)
    {
        return $this->measurements[$key];
    }

    public function addMeasurements($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->measurements[$key] = $value;
            }
        }
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
