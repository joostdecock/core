<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\Sampler class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Sampler extends Theme
{
    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $dir = $this->getTemplateDir();
        $svgDocument->css->add(file_get_contents("$dir/style.css"));
        $svgDocument->svgAttributes->add(file_get_contents("$dir/svg-attributes"));
    }

    public function themeResponse($apiHandler)
    {
        $response = new \Freesewing\Response();
        $response->setFormat('svg');
        $response->setBody("{$apiHandler->svgDocument}");

        return $response;
    }

    public function samplerPathStyle($step, $totalSteps)
    {
        $color = $this->pickColor($step, $totalSteps);
        $dashes = $this->pickDashes($step, $totalSteps);
        return "stroke: $color; stroke-dasharray: $dashes;";
    }

    private function pickColor($step, $steps)
    {
        $hue = $step*(200/($steps-1));
        $saturation = 55;
        $lightness = 50;
        return "hsl($hue, $saturation%, $lightness%)";
    } 

    private function pickDashes($step, $steps)
    {
        $line = 5+$step*2;
        if($step % 2 == 0) return "$line, 1";
        else return "$line, 1, 1, 1";
    } 
}
