<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\ExampleTheme class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class DocumentationDebugTheme extends DocumentationTheme
{
    public function themePattern($pattern)
    {
        foreach($pattern->parts as $partKey => $part) {
            if($part->render) {
                foreach($part->points as $pointKey => $point) {
                    $description = "Point $pointKey (".$point->getX().','.$point->getY().')';
                    $part->newSnippet("svgDebug-point-$pointKey", 'point', $point, $description);
                }
                foreach($part->paths as $pathKey => $path) {
                    $pathstring = $path->path;
                    $points = $part->points;
                    $patharray = explode(' ', $pathstring);
                    $svg = '';
                    $class = '';
                    foreach ($patharray as $p) {
                        $p = rtrim($p);
                        if ($p != '' && $path->isAllowedPathCommand($p)) {
                            $command = $p;
                            if($command == 'C') $curveSteps=1;
                        } elseif (is_object($points[$p])) {
                            if($command == 'M') $type = 'path-move';
                            else if($command == 'C') {
                                if($curveSteps == 3) $type = 'path-curve';
                                else $type = 'path-curvecontrol';
                                if($curveSteps == 1 or $curveSteps == 3) {
                                    $part->newPath("svgDebug-pathcontrol-$p", "M $previous L $p", ['class' => 'curvecontrol']);
                                } 
                                $curveSteps++;
                            }
                            else $type = 'path-point';
                            $description = 'desc';
                            $part->newSnippet("svgDebug-path-$p", $type, $points[$p], $description);
                            $previous = $p;
                        }
                    }

                }
            }
        }
    }
}
