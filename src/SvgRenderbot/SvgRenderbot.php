<?php

namespace Freesewing;

/**
 * Freesewing\SvgRenderbot class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class SvgRenderbot
{
    const TAB = '    ';
    private $tabs = 0;
    private $freeId = 1;
    private $openGroups = array();

    private function tab()
    {
        $space = '';
        for ($i = 0; $i < $this->tabs; ++$i) {
            $space .= self::TAB;
        }

        return $space;
    }

    private function nl()
    {
        return "\n".$this->tab();
    }

    private function indent()
    {
        $this->tabs += 1;
    }

    private function outdent()
    {
        $this->tabs -= 1;
    }

    private function getUid()
    {
        $this->freeId += 1;

        return $this->freeId;
    }

    private function openGroup($id = false, $options = null)
    {
        if ($id === false) $id = $this->getUid();
        $svg = $this->nl().
            "<!-- Start of group #$id -->".$this->nl().
            "<g id=\"$id\" $options>";
        $this->indent();
        array_push($this->openGroups, $id);

        return $svg;
    }

    private function closeGroup()
    {
        $id = array_pop($this->openGroups);
        $this->outdent();

        return $this->nl().'</g>'.$this->nl()."<!-- End of group #$id -->";
    }

    public function render($pattern)
    {
        $scale = new \Freesewing\Transform('scale', 3.54330709); // FIXME this seems to be slightly off. Should be 254/72, no?
        $svg = $this->openGroup(
            'patternScaleContainer',
            \Freesewing\Transform::asSvgParameter(array($scale))
        );

        if (isset($pattern->parts) && count($pattern->parts) > 0) :
            foreach ($pattern->parts as $partKey => $part) {
                if($part->render) {
                    $transforms = '';
                    if (is_array($part->transforms) && count($part->transforms) > 0) {
                        $transforms = \Freesewing\Transform::asSvgParameter($part->transforms);
                    }
                    $svg .= $this->openGroup($partKey, $transforms);
                    
                    foreach ($part->paths as $path) {
                        $svg .= $this->renderPath($path, $part);
                    }

                    foreach ($part->snippets as $snippet) {
                        $svg .= $this->renderSnippet($snippet, $part);
                    }

                    foreach ($part->texts as $text) {
                        $svg .= $this->renderText($text, $part);
                    }

                    $svg .= $this->closeGroup();
                }
            }
        endif;

        $svg .= $this->closeGroup();

        return $svg;
    }

    /*
     * Returns SVG code for a path
     */
    private function renderPath($path, $part)
    {
        $pathstring = $path->getPath();
        $points = $part->points;
        $patharray = explode(' ', $pathstring);
        $options = $path->getOptions();
        $svg = '';
        foreach ($patharray as $p) {
            $p = rtrim($p);
            if ($p != '' && $path->isAllowedPathCommand($p)) {
                $svg .= " $p ";
            } elseif (is_object($points[$p])) {
                $svg .= ' '.$points[$p]->x.','.$points[$p]->y.' ';
            }
        }

        return $this->nl().'<path id="'.$this->getUid().'" class="'.$options['class'].'" d="'.$svg.'" />';
    }
    
    /*
     * Returns SVG code for a snippet
     */
    private function renderSnippet($snippet, $part)
    {
        $anchor = $snippet->getAnchor();
        $svg = $this->nl();
        $svg .=  '<use x="'.$anchor->getX().'" y="'.$anchor->getY().'" xlink:href="#'.$snippet->getReference().'" ';
        if(!isset($snippet->attributes['id'])) $svg .= 'id="'.$this->getUid().'" ';
        $svg .= $this->flattenAttributes($snippet->getAttributes());
        $svg .= '>';
        $this->indent();
        $svg .= $this->nl();
        $svg .= '<title>'.$snippet->getDescription().'</title>';
        $this->outdent();
        $svg .= $this->nl();
        $svg .= '</use>';

        return $svg;
    }
    
    /*
     * Returns SVG code for text
     */
    private function renderText($text, $part)
    {
        $anchor = $text->getAnchor();
        $svg = $this->nl();
        $svg .=  '<text x="'.$anchor->getX().'" y="'.$anchor->getY().'" ';
        if(!isset($text->attributes['id'])) $svg .= 'id="'.$this->getUid().'" ';
        $svg .= $this->flattenAttributes($text->getAttributes());
        $svg .= '>'.$text->getText().'</text>';

        return $svg;
    }
    
    private function flattenAttributes($array)
    {
        $attributes = '';
        foreach($array as $key => $value) {
            $attributes .= "$key=\"$value\" ";
        }
        return $attributes;
    }
}
