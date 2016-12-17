<?php
/** Freesewing\Path class */
namespace Freesewing;

/**
 * Holds SVG paths
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Path
{
    /**
     * @var \Freesewing\Boundary $boundary The path boundary
     *
     * This is public so we can call the boundary methods
     * from the path, like: $path->boundary->getX()
     **/
    public $boundary = null;

    /** @var bool $sample To sample this path or not */
    private $sample = false;

    /** @var bool $render To render this path or not */
    private $render = true;
    
    /** @var array $attributes The path attributest */
    private $attributes = array();
    
    /** @var string $path The SVG pathstring */
    private $path = null;

    /**
     * Marks path to be sampled by the sample service
     *
     * @param bool $bool True to sample this path. False to not sample it.
     */
    public function setSample($bool = true)
    {
        $this->sample = $bool;
    }

    /**
     * Returns the sample property
     *
     * @return bool
     */
    public function getSample()
    {
        return $this->sample;
    }

    /**
     * Stores path boundary
     *
     * @param \Freesewing\Boundary $boundary The path boundary object.
     */
    public function setBoundary($boundary)
    {
        $this->boundary = $boundary;
    }

    /**
     * Returns the boundary property
     *
     * @return \Freesewing\Boundary
     */
    public function getBoundary()
    {
        return $this->boundary;
    }

    /**
     * Marks path to be rendered by the draft service
     *
     * @param bool $bool True to render this path. False to not render it.
     */
    public function setRender($bool)
    {
        $this->render = $bool;
    }

    /**
     * Returns the render property
     *
     * @return bool
     */
    public function getRender()
    {
        return $this->render;
    }

    /**
     * Sanitizes input and sets the path property
     *
     * @param string $path The pathstring
     */
    public function setPath($path)
    {
        $this->path = trim(preg_replace('/ {2,}/', ' ', $path));
    }

    /**
     * Returns the path property, which is the pathstring
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Stores path attributes
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns the attributes property
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets a path attribute
     *
     * @param string $key
     * @param scalar $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns an attribute value
     *
     * @param string $key
     *
     * @return scalar
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Calculates and returns bounding box of a path
     *
     * We need the bounding box of a path to figure out
     * how large a pattern piece is. Something we need to know
     * in order to lay them out in the pattern later.
     *
     * @param \Freesewing\Part $part The part that this path is defined in
     *
     * @throws InvalidArgumentException When the path references a non-existing point
     *
     * @return \Freesewing\Boundary
     */
    public function findBoundary($part)
    {
        /* Break path into array */
        $pathAsArray = Utils::asScrubbedArray($this->getPath());
        foreach ($pathAsArray as $index => $data) {
            /* Are we dealing with a command or point index? */
            if (Utils::isAllowedPathCommand($data)) {
// Command
                $command = $data;
            }
            if (!Utils::isAllowedPathCommand($data)) {
// Point index
                $pointIndex = $data;
                if (!isset($part->points[$pointIndex])) {
// Reference to non-existing point. Bail out
                    throw new \InvalidArgumentException('SVG path references non-existing point '.$pointIndex);
                }
                if (!@is_object($topLeft)) {
                    /*
                     * Topleft is not set. In other words, this is the first point we look at
                     * store it as both the topLeft and bottomRight point of our path boundary
                     **/
                    $topLeft = new \Freesewing\Point();
                    $topLeft->setX($part->points[$pointIndex]->x);
                    $topLeft->setY($part->points[$pointIndex]->y);
                    $bottomRight = new \Freesewing\Point();
                    $bottomRight->setX($part->points[$pointIndex]->x);
                    $bottomRight->setY($part->points[$pointIndex]->y);
                } else {
                    /*
                     * Topleft has been set. Let's compare this point to the current topLeft and bottomRight
                     **/
                    switch ($command) {
                        case 'M':
                        case 'L':
                            /*
                             * MoveTo and LineTo (M and L) are simple
                             **/
                            if ($part->points[$pointIndex]->getX() < $topLeft->getX()) {
                                $topLeft->setX($part->points[$pointIndex]->getX());
                            }
                            if ($part->points[$pointIndex]->getY() < $topLeft->getY()) {
                                $topLeft->setY($part->points[$pointIndex]->getY());
                            }
                            if ($part->points[$pointIndex]->getX() > $bottomRight->getX()) {
                                $bottomRight->setX($part->points[$pointIndex]->getX());
                            }
                            if ($part->points[$pointIndex]->getY() > $bottomRight->getY()) {
                                $bottomRight->setY($part->points[$pointIndex]->getY());
                            }
                            break;
                        case 'C':
                            /*
                             * Bezier curves need a bit more work. We calculate their bounding box by stepping through them.
                             * We need their start and finish point + control points, to pass to $this->findBezierBoundary()
                             **/
                            if ($pathAsArray[$index - 1] == 'C') {
                                /*
                                 * Only run this once per CurveTo command, which uses 4 points
                                 * They are disributed in our array like this
                                 *     [keyIndex - 2] = Start point
                                 *     [keyIndex - 1] = 'C' command
                                 *     [keyIndex] = First control point <-- We are here in our foreach loop
                                 *     [keyIndex + 1 ] = Second control point
                                 *     [keyIndex + 2 ] = End point
                                 *
                                 **/
                                $curveStart = $part->points[$pathAsArray[$index - 2]];
                                $curveControlPoint1 = $part->points[$pathAsArray[$index]];
                                $curveControlPoint2 = $part->points[$pathAsArray[$index + 1]];
                                $curveEnd = $part->points[$pathAsArray[$index + 2]];
                                $bezierBoundary = BezierToolbox::findBezierBoundary($curveStart, $curveControlPoint1, $curveControlPoint2, $curveEnd);
                                if ($bezierBoundary->topLeft->getX() < $topLeft->getX()) {
                                    $topLeft->setX($bezierBoundary->topLeft->getX());
                                }
                                if ($bezierBoundary->topLeft->getY() < $topLeft->getY()) {
                                    $topLeft->setY($bezierBoundary->topLeft->getY());
                                }
                                if ($bezierBoundary->bottomRight->getX() > $bottomRight->getX()) {
                                    $bottomRight->setX($bezierBoundary->bottomRight->getX());
                                }
                                if ($bezierBoundary->bottomRight->getY() > $bottomRight->getY()) {
                                    $bottomRight->setY($bezierBoundary->bottomRight->getY());
                                }
                            }
                            break;
                    }
                }
            }
        }
        $boundary = new \Freesewing\Boundary();
        $boundary->setTopLeft($topLeft);
        $boundary->setBottomRight($bottomRight);

        return $boundary;
    }

    /**
     * Determines whether a path is closed
     *
     * The SVG path command 'z' closes a path.
     * So this simply checks to see if the last step
     * in the pathstring is z or Z.
     *
     * @return bool True if it is. False if it is not closed.
     */
    public function isClosed()
    {
        if (substr(trim(strtolower($this->getPath())), -2) == ' z') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Breaks up a path into an array of steps
     *
     * A path can be made up of multiple draw operations.
     * This breaks the path apart to a single operation (step)
     * like a line or curve.
     *
     * @return array Array of pathstrings for the individual steps.
     */
    public function breakUp()
    {
        $array = Utils::asScrubbedArray($this->getPath());
        foreach ($array as $i => $step) {
            if ($step == 'M') {
                $ongoing = 'M '.$array[$i + 1];
                $move = true;
            } else {
                if ($step == 'L') {
                    if (!$move) {
                        $ongoing = "M $previous";
                    }
                    $paths[] = ['type' => 'L', 'path' => $ongoing.' L '.$array[$i + 1]];
                } elseif ($step == 'C') {
                    if (!$move) {
                        $ongoing = "M $previous";
                    }
                    $paths[] = ['type' => 'C', 'path' => $ongoing.' C '.$array[$i + 1].' '.$array[$i + 2].' '.$array[$i + 3]];
                } elseif (strtolower($step) == 'z') {
                    if ($previous != $array[1]) {
                        $paths[] = ['type' => 'L', 'path' => "M $previous L ".$array[1]];
                    }
                }
                unset($ongoing);
                $move = false;
            }
            $previous = $step;
        }

        return $paths;
    }
}
