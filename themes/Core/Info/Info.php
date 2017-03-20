<?php
/** Freesewing\Themes\Core\Info class */
namespace Freesewing\Themes\Core;

/**
 * The Info theme is used by the Info service
 *
 * Unlike your typical pattern-drawing theme, this
 * one does not extend the abstract Theme class.
 * That's because the info service just returns information
 * and doesn't create a pattern.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Info
{
    /**
     * Returns a themed response with API information
     *
     * @param array $list The data to theme
     * @param string $format The output format
     *
     * @return \Freesewing\Response $resonse A response object
     */
    public function themeInfo($list, $format)
    {
        $response = new \Freesewing\Response();
        if ($format == 'php') {
            $response->setBody(serialize($list));
            $response->setFormat('raw');
        } elseif ($format == 'html') {
            $response->setBody($this->renderInfo($list));
            $response->setFormat('raw');
        } elseif ($format == 'text') {
            $response->setBody($this->printInfo($list));
            $response->setFormat('raw');
        } else {
            $response->setBody($list);
            $response->setFormat('json');
        }

        return $response;
    }

    /**
     * Returns a themed response with pattern information
     *
     * @param array $pattern The data to theme
     * @param string $format The output format
     *
     * @return \Freesewing\Response $resonse A response object
     */
    public function themePatternInfo($pattern, $format)
    {
        $response = new \Freesewing\Response();
        if ($format == 'php') {
            $response->setBody(serialize($pattern));
            $response->setFormat('raw');
        } elseif ($format == 'html') {
            $response->setBody($this->renderPatternInfo($pattern));
            $response->setFormat('raw');
        } elseif ($format == 'text') {
            $response->setBody($this->printPatternInfo($pattern));
            $response->setFormat('raw');
        } else {
            $response->setBody($pattern);
            $response->setFormat('json');
        }

        return $response;
    }

    /**
     * Returns HTML-formatted API information
     *
     * This is only called when the format is HTML
     *
     * @param array $list The data to theme
     *
     * @return string $html The themed HTML
     */
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
        foreach ($list['patterns'] as $ns => $patterns) {
            $html .= "\n<li class=\"pattern\">$ns\n<ul>";
            foreach ($patterns as $name => $title) {
                $html .= "\n<li class=\"pattern\">$title</li>";
            }
            $html .= "\n</ul>\n</li>";
        }
        $html .= "\n</ul>";
        $html .= "<h3>Channels</h3>\n";
        $html .= '<ul class="channellist">';
        foreach ($list['channels'] as $ns => $channels) {
            $html .= "\n<li class=\"channel\">$ns\n<ul>";
            foreach ($channels as $name) {
                $html .= "\n<li class=\"channel\">$name</li>";
            }
            $html .= "\n</ul>\n</li>";
        }
        $html .= "\n</ul>";
        $html .= "<h3>Themes</h3>\n";
        $html .= '<ul class="themelist">';
        foreach ($list['themes'] as $ns => $themes) {
            $html .= "\n<li class=\"theme\">$ns\n<ul>";
            foreach ($themes as $name) {
                $html .= "\n<li class=\"theme\">$name</li>";
            }
            $html .= "\n</ul>\n</li>";
        }
        $html .= "\n</ul>";

        return $html;
    }

    /**
     * Returns text-formatted API information
     *
     * This is only called when the format is text
     *
     * @param array $list The data to theme
     *
     * @return string $text The themed text
     */
    private function printInfo($list)
    {
        $text = "\n    Services:";
        foreach ($list['services'] as $name) {
            $text .= "\n      $name";
        }
        $text .= "\n    Patterns:";
        foreach ($list['patterns'] as $ns => $patterns) {
            $text .= "\n      $ns:";
            foreach ($patterns as $name => $title) {
                $text .= "\n        $title";
            }
        }
        $text .= "\n    Channels:";
        foreach ($list['channels'] as $ns => $channels) {
            $text .= "\n      $ns:";
            foreach ($channels as $name) {
                $text .= "\n        $name";
            }
        }
        $text .= "\n    Themes:";
        foreach ($list['themes'] as $ns => $themes) {
            $text .= "\n      $ns:";
            foreach ($themes as $name) {
                $text .= "\n        $name";
            }
        }

        return "$text\n\n";
    }

    /**
     * Returns HTML-formatted pattern information
     *
     * This is only called when the format is HTML
     *
     * @param array $list The data to theme
     *
     * @return string $html The themed HTML
     */
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

        $html .= "\n\t<h4>Measurements</h4>";
        $html .= "\n\t<ul>";
        foreach ($list['measurements'] as $key => $value) {
            $html .= "\n\t\t<li><b>$key</b>  <small>(default  &raquo; $value"."mm)</small></li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h4>Options</h4>";
        $html .= "\n\t<ul>";
        foreach ($list['options'] as $key => $value) {
            $html .= "\n\t\t<li>$key<ul><li>Type &raquo; ".$value['type']."</li>";
            switch($value['type']) {
                case 'measure':
                    $html .= "<li>Min &raquo; ".$value['min']."</li>
                        <li>Max &raquo; ".$value['max']."</li>
                        <li>Default &raquo; ".$value['default']."</li>";
                    break;
                case 'percent':
                    $html .= "<li>Default &raquo; ".$value['default']."</li>";
                    break;
            }
            $html .= "</ul></li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h4>Sampler models</h4>";
        $html .= "\n\t<h5>Defaults</h5>";
        $html .= "\n\t<ul>";
        foreach ($list['models']['default'] as $key => $value) {
            $html .= "\n\t\t<li>$key &raquo; $value</li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h5>Groups</h5>";
        $html .= "\n\t<ul>";
        foreach ($list['models']['groups'] as $key => $value) {
            $html .= "\n\t\t<li>$key<ul>";
            foreach ($value as $mkey => $mvalue) {
                $html .= "\n\t\t\t<li>$mvalue</li>";
            }
            $html .= "</ul></li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n\t<h5>Model measurements</h5>";
        $html .= "\n\t<ul>";
        foreach ($list['models']['measurements'] as $key => $value) {
            $html .= "\n\t\t<li>$key<ul>";
            foreach ($value as $mkey => $mvalue) {
                $html .= "\n\t\t\t<li>$mkey &raquo; $mvalue</li>";
            }
            $html .= "</ul></li>";
        }
        $html .= "\n\t</ul>";

        $html .= "\n</div>";

        return $html;
    }

    /**
     * Returns text-formatted pattern information
     *
     * This is only called when the format is text
     *
     * @param array $list The data to theme
     *
     * @return string $text The themed text
     */
    private function printPatternInfo($list)
    {
        $pattern = $list['pattern'];
        $text .= "\n".$list['info']['name'];
        $text .= "\n  Info";
        foreach ($list['info'] as $key => $value) {
            $text .= "\n    $key: $value";
        }
        $text .= "\n  Parts";
        foreach ($list['parts'] as $key => $value) {
            $text .= "\n    $key: $value";
        }

        $text .= "\n  Measurements";
        foreach ($list['measurements'] as $key => $value) {
            $text .= "\n    $key (default: $value mm)";
        }

        $text .= "\n  Options";
        foreach ($list['options'] as $key => $value) {
            $text .= "\n    $key: ".$value['type'];
            switch($value['type']) {
                case 'chooseOne':

                    $text .= "\n      Default: ".$value['default'];
                    $text .= "\n      Options:";
                    foreach($value['options'] as $optionid => $option) {
                        $text .= "\n        $optionid: $option";
                    }
                    break;
                default:
                    $text .= "\n      Min: ".$value['min'];
                    $text .= "\n      Max: ".$value['max'];
                    $text .= "\n      Default: ".$value['default'];
                    break;
            }
        }

        $text .= "\n  Sampler models";
        $text .= "\n    Defaults";
        foreach ($list['models']['default'] as $key => $value) {
            $text .= "\n      $key: $value";
        }

        $text .= "\n    Groups";
        foreach ($list['models']['groups'] as $key => $value) {
            $text .= "\n      $key";
            foreach ($value as $mkey => $mvalue) {
                $text .= "\n        $mvalue";
            }
        }

        $text .= "\n    Model measurements";
        foreach ($list['models']['measurements'] as $key => $value) {
            $text .= "\n      $key";
            foreach ($value as $mkey => $mvalue) {
                $text .= "\n        $mkey: $mvalue";
            }
        }


        return "$text\n\n";
    }

    /**
     * Does nothing, but we need to implement this.
     */
    public function cleanUp()
    {
    }
    
    /**
     * Returns false as it's not relevant for the info service, but we need it to be callable
     *
     * @return bool false Always false
     */
    public function isPaperless()
    {
        return false;
    }
}
