<?php
/** Freesewing\Themes\Sampler class */
namespace Freesewing\Themes;

/**
 * A theme that aims to save trees.
 *
 * This theme adds a grid to the pattern.
 * That in itself is probably not enough to keep
 * people from printing their pattern. So it's
 * up to pattern makes to add instructions to the
 * pattern that allows people to draft it into the
 * fabric without printing it.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Paperless extends Basic
{
    /** @var string $defs A grid to be added to the SVG defs */
    private $defs;

    /**
     * Adds the grid to the pattern, and stores pattern message in messages property
     *
     * @param \Freesewing\Patterns\* $pattern The pattern object
     */
    public function themePattern($pattern)
    {
        parent::themePattern($pattern);

        $units = $pattern->getUnits();
        $templateDir = $this->getTemplateDir();
        $this->defs = file_get_contents("$templateDir/defs/grid.".$units['out']);
        foreach ($pattern->parts as $key => $part) {
            if ($part->getRender() === true && $part->hasPathToRender()) {
                $id = $part->newId('grid');
                $this->defs .= "\n".'<pattern id="grid-'.$key.'" xlink:href="grid"></pattern>';
                $this->addGridToPart($part);
            }
        }
    }

    /**
     * Adds templates to the SvgDocument, including extra grid svg def
     *
     * @param \Freesewing\SvgDocument $svgDocument The SvgDocument
     */
    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $this->loadTemplates($svgDocument);
        $svgDocument->defs->add($this->defs);
    }

    /**
     * Adds a grid overlay to a pattern part
     *
     * @param \Freesewing\Part $part The pattern part
     * @param string $units metric|imperial
     *
     */
    private function addGridToPart($part, $units = 'metric')
    {
        if(is_array($part->boundary)) print_r($part);
        $topLeft = $part->boundary->getTopLeft();
        $w = $part->boundary->width;
        $h = $part->boundary->height;
        if (isset($part->points['gridAnchor'])) {
            $anchorX = $part->points['gridAnchor']->getX();
            $topLeftX = $topLeft->getX();
            $anchorY = $part->points['gridAnchor']->getY();
            $topLeftY = $topLeft->getY();
            $x = $anchorX * -1 + $topLeftX;
            $y = $anchorY * -1 + $topLeftY;
            $transX = $anchorX;
            $transY = $anchorY;
        } else {
            $x = 0;
            $y = 0;
            $transX = $topLeft->getX();
            $transY = $topLeft->getY();
        }
        // Grid margin from config
        $gridMargin = $this->config['settings']['gridMargin'];
        $x += $gridMargin/2;
        $y += $gridMargin/2;
        $h -= $gridMargin;
        $w -= $gridMargin;
        $part->newInclude('gridrect', '<rect x="'.$x.'" y="'.$y.'" height="'.$h.'" width="'.$w.'" class="part grid" transform="translate('.$transX.','.$transY.')"/>');
    }
}
