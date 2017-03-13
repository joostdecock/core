<?php
/** Freesewing\Themes\Sampler class */
namespace Freesewing\Themes;

use Freesewing\Context;

/**
 * Default theme for the sample service
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Sampler extends Theme
{
    /**
     * Adds SVG and attributes to the SvgDocument
     *
     * @param \Freesewing\SvgDocument $svgDocument The SVG document
     */
    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $dir = $this->getTemplateDir();
        $svgDocument->css->add(file_get_contents("$dir/style.css"));
        $svgDocument->svgAttributes->add(file_get_contents("$dir/svg-attributes"));
    }

    /**
     * Returns a stroke attribute based on the current step and total steps
     *
     * @param int $step Current step (in the sampler)
     * @param int $steps Total steps (in the sampler)
     *
     * @return string $attr The stroke attribute
     */
    public function samplerPathStyle($step, $totalSteps)
    {
        $color = $this->pickColor($step, $totalSteps);

        return "stroke: $color;";
    }

    /**
     * Returns a color based on the current step and total steps
     *
     * @param int $step Current step (in the sampler)
     * @param int $steps Total steps (in the sampler)
     *
     * @return string $color A HSL color notation
     */
    private function pickColor($step, $steps)
    {
        if ($steps == 1) {
            $hue = 269;
        } else {
            $hue = $step * (269 / ($steps - 1));
        }
        $saturation = 55;
        $lightness = 50;

        return "hsl($hue, $saturation%, $lightness%)";
    }
}
