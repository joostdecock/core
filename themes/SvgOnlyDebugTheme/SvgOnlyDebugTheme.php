<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\SvgOnlyTheme class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgOnlyDebugTheme extends Theme
{
    public function themePattern($pattern)
    {
        foreach($pattern->parts as $partKey => $part) {
            if($part->render) {
                $this->debugPaths($part);
                $this->debugPoints($part);
            }
        }
    }

    private function debugPoints($part)
    {
        foreach($part->points as $key => $point) {
            $this->debugPoint($key, $point, $part);
        }
    }

    private function debugPoint($key, $point, $part)
    {
        if(!isset($this->pointsThemed[$key])) {
            $part->newSnippet("debugPoint$key", 'point', $point, $this->debugPointDescription($key,$point));
        }
    }

    private function debugPointDescription($key, $point)
    {
        return "Point $key (".$point->getX().','.$point->getY().')';
    }

    private function debugPaths($part)
    {
        foreach($part->paths as $path) {
            $this->debugPath($path, $part);
        }
    } 
        
    private function debugPath($path, $part)
    {
        foreach (explode(' ', $path->path) as $key) {
            $key = rtrim($key);
            
            if ($key != '' && $path->isAllowedPathCommand($key)) {
                $command = $key;
                if($command == 'C') $curveSteps=1;
            } 
            elseif (is_object($part->points[$key])) {
                $this->pointsThemed[$key] = true; // Store what points we've seen
                if($command == 'C') {
                    if($curveSteps == 3) $type = 'path-point';
                    else $type = 'path-curvecontrol';
                    if($curveSteps == 1 or $curveSteps == 3) {
                        $part->newPath("svgDebug-pathcontrol-$key", "M $previous L $key", ['class' => 'curvecontrol']);
                    } 
                    $curveSteps++;
                }
                else $type = 'path-point';
                $part->newSnippet("debugPath$key", $type, $part->points[$key], $this->debugPointDescription($key,$part->points[$key]));
                $previous = $key;
            }
        }

    }    
    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $svgDocument->headerComments->add(file_get_contents(__DIR__.'/templates/header.comments'));
        $svgDocument->svgAttributes->add(file_get_contents(__DIR__.'/templates/svg.attributes'));
        $svgDocument->css->add(file_get_contents(__DIR__.'/templates/svg.css'));
        $svgDocument->defs->add(file_get_contents(__DIR__.'/templates/svg.defs'));
        $svgDocument->footerComments->add(file_get_contents(__DIR__.'/templates/footer.comments'));
    }
    
    public function themeResponse($apiHandler)
    {

        $response = new \Freesewing\Response();
        $response->setFormat('svg');
        $response->setBody("{$apiHandler->svgDocument}");

        return $response;
    }
}
