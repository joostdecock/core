<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\Paperless class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Paperless extends Svg
{
    private $defs;

    public function isPaperless()
    {
        return true;
    }

    public function themePattern($pattern)
    {   
        $units = $pattern->getUnits();
        $templateDir = $this->getTemplateDir();
        $this->defs = file_get_contents("$templateDir/grid.".$units['out']);
        foreach($pattern->parts as $key => $part) {
            $id = $part->newId('grid');
            $this->defs .= "\n".'<pattern id="grid-'.$key.'" xlink:href="grid"></pattern>';
            $this->addGridToPart($part);
        }
    }

    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
        {
            $templateDir = $this->getTemplateDir();
            $svgDocument->headerComments->add(file_get_contents("$templateDir/header.comments"));
            $svgDocument->svgAttributes->add(file_get_contents( "$templateDir/svg.attributes"));
            $svgDocument->css->add(file_get_contents(           "$templateDir/svg.css"));
            $svgDocument->defs->add(file_get_contents(          "$templateDir/svg.defs"));
            $svgDocument->defs->add($this->defs);
            $svgDocument->footerComments->add(file_get_contents("$templateDir/footer.comments"));
    }

    private function addGridToPart($part, $units='metric')
    {
        $topLeft = $part->boundary->getTopLeft(); 
        $w = $part->boundary->width;
        $h = $part->boundary->height;
        $part->newInclude('gridrect', '<rect x="'.$topLeft->getX().'" y="'.$topLeft->getY().'" height="'.$h.'" width="'.$w.'" class="part grid" />');
    }
}

