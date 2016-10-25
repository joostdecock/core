<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\Info class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Info
{
    public function themeInfo($list, $format)
    {
        $response = new \Freesewing\Response();
        if ($format == 'php') {
            $response->setBody(serialize($list));
            $response->setFormat('raw');
        } elseif ($format == 'html') {
            $response->setBody($this->renderInfo($list));
            $response->setFormat('raw');
        } else {
            $response->setBody($list);
            $response->setFormat('json');
        }

        return $response;
    }

    public function themePatternInfo($pattern, $format)
    {
        $response = new \Freesewing\Response();
        if ($format == 'php') {
            $response->setBody(serialize($pattern));
            $response->setFormat('raw');
        } elseif ($format == 'html') {
            $response->setBody($this->renderPatternInfo($pattern));
            $response->setFormat('raw');
        } else {
            $response->setBody($pattern);
            $response->setFormat('json');
        }

        return $response;
    }

    private function renderInfo($list)
    {
        $html = "<h3>Services</h3>\n";
        $html .= '<ul class="servicelist">';
        foreach ($list['services'] as $name) {
            $html .= "\n<li class=\"service\">$name</li>";
        }
        $html .= "\n</ul>";
        $html .= "<h3>Patterns</h3>\n";
        $html .= '<ul class="patternlist">';
        foreach ($list['patterns'] as $name => $title) {
            $html .= "\n<li class=\"pattern\"><a data-info=\"/info/$name/html\">$title</a></li>";
        }
        $html .= "\n</ul>";
        $html .= "<h3>Channels</h3>\n";
        $html .= '<ul class="channellist">';
        foreach ($list['channels'] as $name) {
            $html .= "\n<li class=\"channel\">$name</li>";
        }
        $html .= "\n</ul>";
        $html .= "<h3>Themes</h3>\n";
        $html .= '<ul class="patternlist">';
        foreach ($list['themes'] as $name) {
            $html .= "\n<li class=\"theme\">$name</li>";
        }
        $html .= "\n</ul>";

        return $html;
    }

    private function renderPatternInfo($list)
    {
        $pattern = $list['pattern'];
        $html = "<div class='pattern'>";
        $html .= "\n\t<h3>".$list['info']['name'].'</h3>';

        $html .= "\n\t<h4>Info</h4>";
        $html .= "\n\t<ul>";
        foreach ($list['info'] as $key => $value) {
            $html .= "\n\t\t<li><b>$key</b> &raquo; $value</li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h4>Parts</h4>";
        $html .= "\n\t\t<ul>";
        foreach ($list['parts'] as $key => $value) {
            $html .= "\n\t\t<li><b>$key</b> &raquo; $value</li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h4>Measurements list</h4>";
        $html .= "\n\t<ul>";
        foreach ($list['measurements'] as $value) {
            $html .= "\n\t\t<li>$value</li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h4>Options list</h4>";
        $html .= "\n\t<ul>";
        foreach ($list['options'] as $value) {
            $html .= "\n\t\t<li>$value</li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h4>Sampler</h4>";
        $html .= "\n\t<h5>Measurements</h5>";
        $html .= "\n\t<ul>";
        foreach ($list['sampler']['measurements']['groups'] as $key => $value) {
            $html .= "\n\t\t<li><a data-sample=\"/sample/$pattern/measurements/$key\">$key</a></li>";
        }
        $html .= "\n\t</ul>";
        $html .= "\n\t<h5>Options</h5>";
        $html .= "\n\t<ul>";
        foreach ($list['sampler']['options'] as $key => $value) {
            $html .= "\n\t\t<li><a data-sample=\"/sample/$pattern/options/$key/\">$key</a></li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n</div>";

        return $html;
    }

    public function cleanUp()
    {
    }
    
    public function getThemeName()
    {
        return \Freesewing\Utils::getClassDir($this); 
    }
}
