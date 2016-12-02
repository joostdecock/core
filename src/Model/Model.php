<?php
/** Freesewing\Model class */
namespace Freesewing;

/**
 * Holds measurements of a model.
 *
 * This is mainly used to hold measurements for a model.
 * There's also support to give the model a name, but only the 
 * sampler service uses that at the moment.
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Model
{
    /** @var array measurements */
    private $measurements = array();

    /**
     * Sets a measurement.
     *
     * @param string $key Measurement key in the array
     * @param float $value Measurement value
     */
    public function setMeasurement($key, $value)
    {
        $this->measurements[$key] = $value;
    }

    /**
     * Gets a measurement.
     *
     * @param string $key The key in the measurements array
     *
     * @return float The measurement value
     */
    public function getMeasurement($key)
    {
        return $this->measurements[$key];
    }

    /**
     * Gets all measurement. FIXME: remove this later
     *
     * @param string $key The key in the measurements array
     *
     * @return float The measurement value
     */
    public function getMeasurements()
    {
        return $this->measurements;
    }

    /**
     * Alias for getMeasurement()
     *
     * @param string $key The key in the measurements array
     *
     * @return float The measurement value
     */
    public function m($key)
    {
        return $this->getMeasurement($key);
    }

    /**
     * Adds measurements.
     *
     * Rater than adding measurements individually, this adds 
     * an array of measurements.
     *
     * @param array measurements
     */
    public function addMeasurements($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->measurements[$key] = $value;
            }
        }
    }

}
