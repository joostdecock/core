<?php
/** Freesewing\Transform class */
namespace Freesewing;

/**
 * Holds an SVG transform.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Transform
{
    /** @var float $x X value */
    private $x = null;

    /** @var float $r Y value */
    private $y = null;

    /** @var float $angle Rotation angle */
    private $angle = null;

    /** @var string $type Transform type */
    private $type = null;

    /**
     * Transform constructor with some sanity checks
     *
     * This creates a new transform, and checks that the values you pass it makes sense
     * It arguably does too much for a constructor
     *
     * @param string $type The type of transform. One of translate|scale|rotate
     * @param float $x X value for the transform
     * @param float $y Optional Y value for the transform
     * @param float $angle Optional angle for rotate transform
     *
     * @throws InvalidArgumentException If you specify an unsupported type, pass wrong parameters, or insufficient parameters.
     */
    public function __construct($type, $x, $y = null, $angle = null)
    {
        $allowedTypes = [
            'translate',
            'scale',
            'rotate',
        ];

        if (in_array($type, $allowedTypes)) {
            $this->type = $type;
        } else {
            throw new \InvalidArgumentException($type.' is not a supported transform type');
        }

        $numericParameters = [
            'x',
            'y',
            'angle',
        ];

        foreach ($numericParameters as $parameter) {
            if (${$parameter} !== null) {
                if (is_numeric(${$parameter})) {
                    $this->{$parameter} = ${$parameter};
                } else {
                    throw new \InvalidArgumentException('Parameter '.$parameter.' must be numeric');
                }
            }
        }

        // We need at least x to be set
        if (!isset($this->x)) {
            throw new \InvalidArgumentException('Missing parameter x');
        }

        // Extra check for rotate, requires all four parameters to be set
        if (
            $this->type == 'rotate'
            and
            !(isset($this->y) and isset($this->angle))
        ) {
            throw new \InvalidArgumentException('Missing parameter for rotate transform');
        }
    }

    /**
     * Returns a transform as the valiefor an SVG transform attribute
     *
     * @return string The SVG code for the transform
     */
    private function asSvgTransform()
    {
        switch ($this->type) {
            case 'translate':
                $transform = ' translate('.$this->x.' '.$this->y.') ';
                break;
            case 'scale':
                $transform = ' scale('.$this->x.' '.$this->y.') ';
                break;
            case 'rotate':
                $transform = ' rotate('.$this->angle.' '.$this->x.' '.$this->y.') ';
                break;
        }

        return $transform;
    }

    /**
     * Returns an array of transforms as an SVG transform attribute
     *
     * @param array $array An array of transforms
     *
     * @return string The SVG code for the transforms
     */
    public function asSvgParameter($array)
    {
        $svg = '';
        foreach ($array as $transform) {
            $svg .= $transform->asSvgTransform();
        }

        return " transform=\"$svg\" ";
    }
}
