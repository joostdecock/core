<?php
/** Freesewing\Themes\Designer class */
namespace Freesewing\Themes;

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

        foreach ($pattern->parts as $partKey => $part) {
            if ($part->getRender() == true) {
                // @todo Add a way to highlight a point (Like the old 'only' request parameter)
                $this->debugPaths($partKey, $part);
                $this->debugPoints($partKey, $part);
                $this->highlightPoints($partKey, $part);
            }
        }

        $this->debug = $pattern->getDebug();
    }

    /**
     * Adds debug info to points in a part
     *
     * @param string $partKey Key of the part in the pattern parts array
     * @param \Freesewing\Part $part The pattern part
     */
    private function debugPoints($partKey, $part)
    {
        foreach ($part->points as $key => $point) {
            $this->debugPoint($key, $point, $part, $partKey);
        }
    }

    /**
     * Adds debug info for a single point
     *
     * @param string $key Key of the point in the part's points array
     * @param \Freesewing\Point $point The point to add debug for
     * @param \Freesewing\Part $part The pattern part
     * @param string $partKey Key of the part in the pattern parts array
     */
    private function debugPoint($key, \Freesewing\Point $point, \Freesewing\Part $part, $partKey)
    {
        if (!isset($part->tmp['pointsThemed'][$key])) {
            $title = $this->debugPointDescription($key, $point);
            $attr = ['id' => "$partKey-$key", 'onmouseover' => "pointHover('$partKey-$key')"];
            if (substr($key, 0, 1) == '.' || strpos($key, 'volatile')) {
                $type = 'volatile-point';
            } else {
                $type = 'point';
            }
            $part->newSnippet($key, $type, $key, $attr, $title);
            $attr = ['id' => "$partKey-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
            $part->newText($key, $key, $title, $attr);
        }
    }

    /**
     * Adds highlighted points in a part
     *
     * @param string $partKey Key of the part in the pattern parts array
     * @param \Freesewing\Part $part The pattern part
     */
    private function highlightPoints($partKey, $part)
    {
        if (isset($_REQUEST['highlightPoints'])) {
            $toHighlight = \Freesewing\Utils::asScrubbedArray($_REQUEST['highlightPoints'], ',');
            if (isset($toHighlight) && is_array($toHighlight)) {
                foreach ($toHighlight as $key) {
                    if (isset( $part->points[$key])) {
                        $this->highlightPoint($key, $part->points[$key], $part, $partKey);
                    }
                }
            }
        }
    }

    /**
     * Adds hightlight to a single point
     *
     * @param string $key Key of the point in the part's points array
     * @param \Freesewing\Point $point The point to highlight
     * @param \Freesewing\Part $part The pattern part
     * @param string $partKey Key of the part in the pattern parts array
     */
    private function highlightPoint($key, \Freesewing\Point $point, \Freesewing\Part $part, $partKey)
    {
        $title = $this->debugPointDescription($key, $point);
        $attr = ['id' => "$partKey-$key", 'onmouseover' => "pointHover('$partKey-$key')"];
        $part->newSnippet($key, 'highlight-point', $key, $attr, $title);
        $attr = ['id' => "$partKey-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
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
     * @param string $partKey Key of the part in the pattern parts array
     * @param \Freesewing\Part $part The pattern part
     */
    private function debugPaths($partKey, $part)
    {
        foreach ($part->paths as $path) {
            $this->debugPath($path, $part, $partKey);
        }
    }

    /**
     * Adds debug info for a path
     *
     * @param \Freesewing\Path $path The path to add debug for
     * @param \Freesewing\Part $part The pattern part
     * @param string $partKey Key of the part in the pattern parts array
     */
    private function debugPath($path, $part, $partKey)
    {
        foreach (explode(' ', $path->getPath()) as $key) {
            $key = rtrim($key);

            if ($key != '' && \Freesewing\Utils::isAllowedPathCommand($key)) {
                $command = $key;
                if ($command == 'C') {
                    $curveSteps = 1;
                }
            } elseif (is_object($part->points[$key])) {
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
                $title = $this->debugPointDescription($key, $part->points[$key]);
                $attr = ['id' => "$partKey-$key", 'onmouseover' => "pointHover('$partKey-$key')"];
                $part->newSnippet("$partKey-$key", $type, $key, $attr, $title);
                $attr = ['id' => "$partKey-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
                $part->newText($key, $key, $title, $attr);
                $previous = $key;
            }
        }
    }
    
    /**
     * Determines whether to show debug messages or not
     *
     * This is false in the theme we extend, so we set it true here
     *
     * @return true Here, always true
     */
    protected function showDebug()
    {
        return true;
    }
}
