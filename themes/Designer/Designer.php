<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\Designer class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Designer extends Theme
{
    public function themePattern($pattern)
    {
        foreach($pattern->parts as $partKey => $part) {
            if($part->render) {
                if(!isset($_REQUEST['only'])) $this->debugPaths($partKey, $part);
                $this->debugPoints($partKey, $part);
            }
        }
        $this->messages = $pattern->getMessages();
    }

    private function debugPoints($partKey, $part)
    {
        foreach($part->points as $key => $point) {
            if(isset($_REQUEST['only'])) {
                $only = \Freesewing\Utils::asScrubbedArray($_REQUEST['only'], ','); 
                if(in_array($key, $only)) $this->debugPoint($key, $point, $part, $partKey);
            }
            else $this->debugPoint($key, $point, $part, $partKey);
        }
    }

    private function debugPoint($key, \Freesewing\Point $point, \Freesewing\Part $part, $partKey)
    {
        if(!isset($part->tmp['pointsThemed'][$key])) {
            $title = $this->debugPointDescription($key,$point);
            $attr = ['id' => "$partKey-$key", 'onmouseover' => "pointHover('$partKey-$key')"];
            $part->newSnippet($key, 'point', $key, $attr, $title); $attr = ['id' => "$partKey-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
            $part->newText($key, $key, $title, $attr);
        }
    }

    private function debugPointDescription($key, $point)
    {
        return $point->getDescription()." | Point $key (".$point->getX().','.$point->getY().')';
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
                $part->tmp['pointsThemed'][$key] = true; // Store what points we've seen
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
                $part->newSnippet("$partKey-$key", $type, $key, $attr, $title);
                $attr = ['id' => "$partKey-$key-tooltip", 'class' => 'tooltip', 'visibility' => 'hidden'];
                $part->newText($key, $key, $title, $attr);
                $previous = $key;
            }
        }

    }    
    
}
