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
                if(!isset($_REQUEST['only'])) $this->debugPaths($partKey, $part);
                $this->debugPoints($partKey, $part);
            }
        }
    }

    private function debugPoints($partKey, $part)
    {
        foreach($part->points as $key => $point) {
            if(isset($_REQUEST['only'])) {
                $only = \Freesewing\Utils::asScrubbedArray($_REQUEST['only']); 
                if(in_array($key, $only)) $this->debugPoint($key, $point, $part, $partKey);
            }
            else $this->debugPoint($key, $point, $part, $partKey);
        }
    }

    private function debugPoint($key, \Freesewing\Point $point, \Freesewing\Part $part, $partKey)
    {
        if(!isset($this->pointsThemed[$key])) {
            $title = $this->debugPointDescription($key,$point);
            $attr = ['id' => "$partKey-$key", 'onmouseover' => "pointHover('$partKey-$key')", 'onmouseout' => "pointUnhover('$partKey-$key')"];
            $part->newSnippet($key, 'point', $point, $attr, $title); $attr = ['id' => "$partKey-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
            $part->newText($key, $point, $title, $attr);
        }
    }

    private function debugPointDescription($key, $point)
    {
        return "Point $key (".$point->getX().','.$point->getY().')';
    }

    private function debugPaths($partKey, $part)
    {
        foreach($part->paths as $path) {
            $this->debugPath($path, $part, $partKey);
        }
    } 
        
    private function debugPath($path, $part, $partKey)
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
                $title = $this->debugPointDescription($key,$part->points[$key]);
                $attr = ['id' => "$partKey-$key", 'onmouseover' => "pointHover('$partKey-$key')", 'onmouseout' => "pointUnhover('$partKey-$key')"];
                $part->newSnippet("$partKey-$key", $type, $part->points[$key], $attr, $title);
                $attr = ['id' => "$partKey-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
                $part->newText($key, $part->points[$key], $title, $attr);
                $previous = $key;
            }
        }

    }    
    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $svgDocument->headerComments->add(file_get_contents(__DIR__.'/templates/header.comments'));
        $svgDocument->svgAttributes->add(file_get_contents(__DIR__.'/templates/svg.attributes'));
        $svgDocument->css->add(file_get_contents(__DIR__.'/templates/svg.css'));
        $svgDocument->script->add(file_get_contents(__DIR__.'/templates/svg.script'));
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
