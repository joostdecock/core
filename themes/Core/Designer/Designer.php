<?php
/** Freesewing\Themes\Core\Designer class */
namespace Freesewing\Themes\Core;

use \Freesewing\Utils;
use \Freesewing\Patterns\Pattern;

/**
 * Designer theme adds extra info for pattern designers.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Designer extends Theme
{
    /**
     * Adds debug info to pattern
     *
     * @param \Freesewing\Pattern\* $pattern The pattern object
     */
    public function themePattern($pattern)
    {
        parent::themePattern($pattern);

        foreach ($pattern->parts as $part) {
            if ($part->getRender() == true) {
                $this->debugPaths($part);
                $this->debugPoints($part);
                if($this->getOption('markPoints')) {
                    $this->markPoints(Utils::asScrubbedArray($this->getOption('markPoints')), $part);
                }
            }
        }

        $this->debug = $pattern->getDebug();
    }

    /**
     * Adds debug info to points in a part
     *
     * @param \Freesewing\Part $part The pattern part
     */
    private function debugPoints($part)
    {
        $onlyPoints = $this->getOption('onlyPoints');
        if(!is_array($onlyPoints) && $onlyPoints != false) $onlyPoints = [$onlyPoints];
        if(is_array($onlyPoints)) {
            foreach ($onlyPoints as $key) {
                $this->debugPoint($key, $part->points[$key], $part);
            }
        } else {
            foreach ($part->points as $key => $point) {
                $this->debugPoint($key, $point, $part);
            }
        }
    }

    /**
     * Adds debug info for a single point
     *
     * @param string $key Key of the point in the part's points array
     * @param \Freesewing\Point $point The point to add debug for
     * @param \Freesewing\Part $part The pattern part
     */
    private function debugPoint($key, \Freesewing\Point $point, \Freesewing\Part $part)
    {
        $title = $this->debugPointDescription($key, $point);
        $partSlug = Utils::slug($part->getTitle());
        $attr = ['id' => "$partSlug-$key", 'onmouseover' => "pointHover('$partSlug-$key')"];
        if (substr($key, 0, 1) == '.' || strpos($key, 'volatile')) {
            $type = 'volatile-point';
        } else {
            $type = 'point';
        }
        $part->newSnippet('.debugPoint__'.$key, $type, $key, $attr, $title);
        $attr = ['id' => "$partSlug-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
        $part->newText($key, $key, $title, $attr);
    }
    
    /**
     * Adds marked points in a part
     *
     * @param array $points Array of points to mark
     * @param \Freesewing\Part $part The pattern part
     */
    private function markPoints($points, $part)
    {
        foreach($points as $id) {
            if($part->isPoint($id)) {
                $this->markPoint($id, $part->points[$id], $part);
            }
        }
    }

    /**
     * Adds mark to a single point
     *
     * @param string $key Key of the point in the part's points array
     * @param \Freesewing\Point $point The point to mark
     * @param \Freesewing\Part $part The pattern part
     */
    private function markPoint($key, \Freesewing\Point $point, \Freesewing\Part $part)
    {
        $title = $this->debugPointDescription($key, $point);
        $partSlug = Utils::slug($part->getTitle());
        $attr = ['id' => "$partSlug-$key", 'onmouseover' => "pointHover('$partSlug-$key')"];
        $part->newSnippet('.markPoint__'.$key, 'marked-point', $key, $attr, $title);
        $attr = ['id' => "$partSlug-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
        $part->newText($key, $key, $title, $attr);
        $part->tmp['pointsThemed'][$key] = true; // Store what points we've seen
    }

    /**
     * Adds extra debug info to a point description
     *
     * @param string $key Key of the point in the part's points array
     * @param \Freesewing\Point $point The point to add debug for
     */
    private function debugPointDescription($key, $point)
    {
        return $point->getDescription()." | Point $key (".$point->getX().','.$point->getY().')';
    }

    /**
     * Adds debug info for to paths in a part
     *
     * @param \Freesewing\Part $part The pattern part
     */
    private function debugPaths($part)
    {
        foreach ($part->paths as $path) {
            $this->debugPath($path, $part);
        }
    }

    /**
     * Adds debug info for a path
     *
     * @param \Freesewing\Path $path The path to add debug for
     * @param \Freesewing\Part $part The pattern part
     */
    private function debugPath($path, $part)
    {
        $onlyPoints = $this->getOption('onlyPoints');
        if(!is_array($onlyPoints) && $onlyPoints != false) $onlyPoints = [$onlyPoints];

        $partSlug = Utils::slug($part->getTitle());
        foreach (explode(' ', $path->getPathstring()) as $key) {
            $key = rtrim($key);

            if ($key != '' && \Freesewing\Utils::isAllowedPathCommand($key)) {
                $command = $key;
                if ($command == 'C') {
                    $curveSteps = 1;
                }
            } elseif (isset($key) && isset($part->points[$key]) && is_object($part->points[$key])) {
                $part->tmp['pointsThemed'][$key] = true; // Store what points we've seen
                if ($command == 'C') {
                    if ($curveSteps == 3) {
                        $type = 'path-point';
                    } else {
                        $type = 'path-curvecontrol';
                    }
                    if ($curveSteps == 1 or $curveSteps == 3) {
                        $part->newPath("svgDebug-pathcontrol-$key", "M $previous L $key", ['class' => 'curve-control']);
                    }
                    ++$curveSteps;
                } else {
                    $type = 'path-point';
                }
                if(!$onlyPoints || in_array($key,$onlyPoints)) {
                    $title = $this->debugPointDescription($key, $part->points[$key]);
                    $attr = ['id' => "$partSlug-$key", 'onmouseover' => "pointHover('$partSlug-$key')"];
                    $part->newSnippet(".debugPath__$partSlug-$key", $type, $key, $attr, $title);
                    $attr = ['id' => "$partSlug-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
                    $part->newText($key, $key, $title, $attr);
                }
                $previous = $key;
            }
        }
    }
    
}
