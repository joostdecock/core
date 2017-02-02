<?php
/** Freesewing\Sampler class */
namespace Freesewing;

/**
 * Handles sampling of measurements or options.
 *
 * This contains functionality that is shared between
 * the MeasurementsSampler and OptionsSampler classes
 *
 * @author    Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license   http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Sampler
{

    /** @var array $partContainer Container for parts that created while sampling a pattern */
    public $partContainer = array();

    /** @var array $anchors Container for the anchors of parts that created while sampling a pattern */
    public $anchors = array();

    /** @var array $boundaries Container for the boundaries of parts that created while sampling a pattern */
    public $boundaries = array();

    /** @var  array */
    protected $modelConfig;

    /**
     * @var array
     */
    protected $models;

    /** @var  \Freesewing\Patterns\Pattern */
    protected $pattern;

    /**
     * Stores a pattern in the pattern property
     *
     * @param \Freesewing\Pattern or equivalent $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Returns the pattern property
     *
     * @return \Freesewing\Pattern or equivalent
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Returns file names of the sampler models files
     *
     * @param \Freesewing\Pattern or equivalent $pattern
     *
     * @return string the filename
     */
    public function getSamplerModelsFile($pattern)
    {
        return Utils::getClassDir($pattern) . '/sampler/models.yml';
    }

    /**
     * Returns the anchor point to be used for sampling
     *
     * This will check for:
     *  - a point with id 'samplerAnchor'
     *  - a point with id 'gridAnchor'
     * It will return the first one found.
     * If the part has neither, it will add a point
     * with coordinates (0,0) and return that.
     *
     * @param \Freesewing\Part $part The part
     *
     * @return \Freesewing\Point The anchor point
     */
    public function getSamplerAnchor($part)
    {
        if (isset($part->points['samplerAnchor'])) {
            return $part->loadPoint('samplerAnchor');
        } elseif (isset($part->points['gridAnchor'])) {
            return $part->loadPoint('gridAnchor');
        } else {
            $part->newPoint('samplerAnchor', 0, 0, 'Anchor point added by sampler');
            return $part->loadPoint('samplerAnchor');
        }
    }

    /**
     * Loads the sampler options configuration
     *
     * @return array The yaml options config as array
     */
    public function loadPatternOptions()
    {
        $config = $this->pattern->getConfig();
        $options = $config['options'];
        foreach ($options as $key => $option) {
            if ($option['type'] == 'percent') {
                $this->options[$key] = $option['default'] / 100;
            } else {
                $this->options[$key] = $option['default'];
            }
        }
        return $this->options;
    }

    /**
     * Samples parts for a given model and options
     *
     * The MeasurementsSampler itterates over models
     * The OptionsSampler itterates over an option value
     * When doing so, both call this function to do the actual sampling.
     * It does two seperate things:
     *  - It renders the paths marked for sampling
     *  - It finds a bounding box for the parts
     * This info is stored in $this->partContainer and will be added to a pattern later
     * The $step and $steps parameters are passed to the theme so different samplings can be
     * made to look different. (giving us that rainbow effect in the standard theme).
     *
     * @param int                      $step      The step out of total steps this is
     * @param int                      $step      The total amount of steps
     * @param \Freesewing\Pattern      $pattern   The pattern to sample
     * @param \Freesewing\Theme        $theme     The theme
     * @param \Freesewing\SvgRenderbot $mode      sample or compare
     * @param \Freesewing\SvgRenderbot $renderBot The SVG renderbot to render the path
     * @param string                   $mode      sample or compare
     */
    public function sampleParts($step, $steps, $pattern, $theme, $renderBot, $mode='sample')
    {
        foreach ($pattern->parts as $partKey => $part) {
            if ($part->getRender() === true) {
                if (!is_object($this->anchors[$partKey])) {
                    $this->anchors[$partKey] = $this->getSamplerAnchor($part);
                    $deltaX = 0;
                    $deltaY = 0;
                    $transform = 'translate( 0, 0 )';
                } else {
                    $anchor = $this->getSamplerAnchor($part);
                    $deltaX = $this->anchors[$partKey]->getX() - $anchor->getX();
                    $deltaY = $this->anchors[$partKey]->getY() - $anchor->getY();
                    $transform = "translate( $deltaX, $deltaY )";
                }
                foreach ($part->paths as $pathKey => $path) {
                    if ($path->getSample() === true) {
                        $path->boundary = $path->findBoundary($part);
                        if (!is_object($this->boundaries[$partKey]['topLeft'])) {
                            $this->boundaries[$partKey]['topLeft'] = new \Freesewing\Point();
                            $this->boundaries[$partKey]['topLeft']->setX($path->boundary->topLeft->x);
                            $this->boundaries[$partKey]['topLeft']->setY($path->boundary->topLeft->y);
                            $this->boundaries[$partKey]['bottomRight'] = new \Freesewing\Point();
                            $this->boundaries[$partKey]['bottomRight']->setX($path->boundary->bottomRight->x);
                            $this->boundaries[$partKey]['bottomRight']->setY($path->boundary->bottomRight->y);
                        } else {
                            if (($path->boundary->topLeft->x + $deltaX) < $this->boundaries[$partKey]['topLeft']->x) {
                                $this->boundaries[$partKey]['topLeft']->setX($path->boundary->topLeft->x + $deltaX);
                            }
                            if (($path->boundary->topLeft->y + $deltaY) < $this->boundaries[$partKey]['topLeft']->y) {
                                $this->boundaries[$partKey]['topLeft']->setY($path->boundary->topLeft->y + $deltaY);
                            }
                            if ($path->boundary->bottomRight->x + $deltaX > $this->boundaries[$partKey]['bottomRight']->x) {
                                $this->boundaries[$partKey]['bottomRight']->setX($path->boundary->bottomRight->x + $deltaX);
                            }
                            if ($path->boundary->bottomRight->y + $deltaY > $this->boundaries[$partKey]['bottomRight']->y) {
                                $this->boundaries[$partKey]['bottomRight']->setY($path->boundary->bottomRight->y + $deltaY);
                            }
                        }
                        if($mode == 'compare') $path->setAttributes(['transform' => $transform, 'class' => 'compare']);
                        else $path->setAttributes(['transform' => $transform, 'style' => $theme->samplerPathStyle($step, $steps)]);
                        $this->partContainer[$partKey]['includes']["$step-$pathKey"] = $renderBot->renderPath($path, $part);
                        $this->partContainer[$partKey]['topLeft'] = $this->boundaries[$partKey]['topLeft'];
                        $this->partContainer[$partKey]['bottomRight'] = $this->boundaries[$partKey]['bottomRight'];
                    }
                }
            }
        }
    }

    /**
     * Adds all sampled parts to the pattern
     *
     * This stored the parts in $this->partContainer in the pattern
     * but not before drawing a path in them along the boundary.
     * That's needed because the sampled paths are stored as rendered snippets
     * and thus we can no longer determine their boundary.
     */
    public function addSampledPartsToPattern()
    {
        foreach ($this->partContainer as $partKey => $part) {
            $this->pattern->addPart("sampler-$partKey");
            $p = $this->pattern->parts[$partKey];
            $p->newPoint(1, $part['topLeft']->getX(), $part['topLeft']->getY(), 'Top left');
            $p->newPoint(3, $part['bottomRight']->getX(), $part['bottomRight']->getY(), 'Bottom right');
            $p->newPoint(2, $p->x(3), $p->y(1), 'Top right');
            $p->newPoint(4, $p->x(1), $p->y(3), 'Bottom left');
            $p->newPath('border', 'M 1 L 2 L 3 L 4 z', ['class' => 'hidden']);
            foreach ($part['includes'] as $pathKey => $include) {
                $p->newInclude($pathKey, $include);
            }
        }
    }
}
