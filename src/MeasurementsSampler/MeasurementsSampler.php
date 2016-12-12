<?php
/** Freesewing\MeasurementsSampler class */
namespace Freesewing;

/**
 * Samples a pattern for a group for models
 *
 * This takes a group of models (with different measurements) and generates
 * the pattern parts for them, aligning them properly.
 * This allows you to verify that your pattern grades nicely over a range of
 * sizes/measurements.
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class MeasurementsSampler extends Sampler
{

    public function setModelConfig($config)
    {
        $this->modelConfig = $config;
    }

    /**
     * Loads models for which we'll sampler the pattern.
     *
     * @param string group Name of the model group as defined in sampler config
     *
     * @return array|null Array of models or null if we can't read the config file
     *
     * @throws InvalidArgumentException if the config file cannot be read
     */
    public function loadPatternModels($group)
    {
        // Does this group even exist?
        if (!is_array($this->modelConfig['groups'][$group]) || count($this->modelConfig['groups'][$group]) == 0) {
            // It doesn't
            // Do we have multiple defaults from extended patterns?
            if (is_array($this->modelConfig['default']['group'])) {
                $group = $this->modelConfig['default']['group'][0];
            } else {
                $group = $this->modelConfig['default']['group'];
            }
        }

        $this->models = $this->loadModelGroup($group);

        return $this->models;
    }

    /**
     * Add a additional measurement to the sample-models.
     *
     * @param array  $measurements
     * @param string $name
     */
    public function addPatternModel(array $measurements, $name = 'userSize')
    {
        $model = new \Freesewing\Model();
        $model->addMeasurements($measurements);
        $this->models[$name] = $model;
    }

    /**
     * Samples the pattern for each model (set of measurements)
     *
     * For each model, this clones the pattern and calls the sample() method
     * with the model as parameter.
     * It then itterates over the parts and calls sampleParts() on them
     *
     * @param \Freesewing\Themes\Theme or similar
     *
     * @return \Freesewing\Patterns\Pattern or similar
     */
    public function sampleMeasurements($theme)
    {
        $renderBot = new \Freesewing\SvgRenderbot();
        $steps = count($this->models);
        $i = 0;
        foreach ($this->models as $modelKey => $model) {
            if($modelKey == 'compareModel') $mode = 'compare';
            else $mode = 'sample';
            $p = clone $this->pattern;
            $p->loadParts();
            $p->sample($model);
            foreach ($p->parts as $partKey => $part) {
                $this->sampleParts($i, $steps, $p, $theme, $renderBot, $mode);
            }
            ++$i;
        }
        $this->addSampledPartsToPattern();
        $theme->applyRenderMask($this->pattern);
        $this->pattern->layout();
        return $this->pattern;
    }

    /**
     * Structures models for consumption by the sampler
     *
     * @param string $group Name of the model group as defined in sampler config
     *
     * @return array Array of models
     */
    private function loadModelGroup($group)
    {
        $models = [];
        foreach ($this->modelConfig['groups'][$group] as $member) {
            $model = new \Freesewing\Model();
            foreach ($this->modelConfig['measurements'] as $mKey => $mModels) {
                $measurements[$mKey] = $mModels[$member];
            }
            $model->addMeasurements($measurements);
            unset($measurements);
            $models[$member] = $model;
        }

        return $models;
    }

    /**
     * Figures out what model group to load.
     *
     * This checks whether the passed argument is a group
     * It it is, it returns it.
     * If not, it returns the default group
     *
     * @param string group Name of the model group to look for
     *
     * @return string Name of the group to load
     */
    private function modelGroupToLoad($group)
    {
        if (is_array($this->measurementsConfig['groups'][$group])) {
            return $group;
        } else {
            return $this->measurementsConfig['default']['group'];
        }
    }
}
