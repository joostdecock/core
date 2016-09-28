<?php

namespace Freesewing;

/**
 * Freesewing\Transform class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Transform
{
    private $x = null;
    private $y = null;
    private $angle = null;
    private $type = null;

    public function __construct($type, $x = null, $y = null, $angle = null)
    {
        $allowedTypes = [ 
            'translate', 
            'scale', 
            'rotate'
        ];

        if(in_array($type, $allowedTypes)) $this->type = $type;
        else throw new \InvalidArgumentException($type.' is not a supported transform type');

        $numericParameters = [
            'x',
            'y',
            'angle',
        ];
        
        foreach($numericParameters as $parameter) {
            if (${$parameter} !== null) {
                if (is_numeric(${$parameter})) {
                    $this->{$parameter} = ${$parameter};
                } else {
                    throw new \InvalidArgumentException('Parameter '.$parameter.' must be numeric');
                }
            }
        }

        // We need at least x to be set
        if(!isset($this->x)) throw new \InvalidArgumentException('Missing parameter x');

        // Extra check for rotate, requires all four parameters to be set
        if (
            $this->type == 'rotate' 
            and 
            !(isset($this->y) and isset($this->angle))
        ) {
            throw new \InvalidArgumentException('Missing parameter for rotate transform');
        }
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getAngle()
    {
        return $this->angle;
    }

    public function asSvgTransform()
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

    public function asSvgParameter($array)
    {
        $svg = '';
        foreach ($array as $transform) {
            $svg .= $transform->asSvgTransform();
        }

        return " transform=\"$svg\" ";
    }
}
