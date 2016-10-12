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

    public function themePattern($pattern)
    {   
        $units = $pattern->getUnits();
        $templateDir = $this->getTemplateDir();
        $this->defs = file_get_contents("$templateDir/defs/grid.".$units['out']);
        foreach($pattern->parts as $key => $part) {
            if($part->render) {
                $id = $part->newId('grid');
                $this->defs .= "\n".'<pattern id="grid-'.$key.'" xlink:href="grid"></pattern>';
                $this->addGridToPart($part);
            }
        }
    }

    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
        {
            $this->loadTemplates($svgDocument);
            $svgDocument->defs->add($this->defs);
    }

    private function addGridToPart($part, $units='metric')
    {
        $topLeft = $part->boundary->getTopLeft(); 
        $w = $part->boundary->width;
        $h = $part->boundary->height;
        if(isset($part->points['gridAnchor'])) {
            $anchorX = $part->points['gridAnchor']->getX();
            $topLeftX = $topLeft->getX();
            $anchorY = $part->points['gridAnchor']->getY();
            $topLeftY = $topLeft->getY();
            $x = $anchorX*-1 +  $topLeftX;
            $y = $anchorY*-1 +  $topLeftY;
            $transX = $anchorX;
            $transY = $anchorY;
        } else {
            $x = 0;
            $y = 0;
            $transX = $topLeft->getX();
            $transY = $topLeft->getY();
        }
        // Grid margin
        $x +=2;
        $y +=2;
        $h -=4;
        $w -=4;
        $part->newInclude('gridrect', '<rect x="'.$x.'" y="'.$y.'" height="'.$h.'" width="'.$w.'" class="part grid" transform="translate('.$transX.','.$transY.')"/>');
    }
}

