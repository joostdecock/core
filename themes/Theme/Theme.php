<?php

namespace Freesewing\Themes;

/**
 * Freesewing\Themes\Theme class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
abstract class Theme
{
    public $messages = array();

    public function renderAs() 
    {
        return [
            'svg' => true,
            'js' => false,
        ];
    }

    public function themePattern($pattern)
    {
        $this->messages = $pattern->getMessages();
        $pattern->replace('__SCALEBOX_METRIC__', $pattern->t('__SCALEBOX_METRIC__'));
        $pattern->replace('__SCALEBOX_IMPERIAL__', $pattern->t('__SCALEBOX_IMPERIAL__'));
    }

    public function themeSvg(\Freesewing\SvgDocument $svgDocument)
    {
        $this->loadTemplates($svgDocument);
    }

    public function loadTemplates($svgDocument) 
    {
        $templates = $this->loadTemplateHierarchy();
        if(isset($templates['js'])) foreach($templates['js'] as $js) $svgDocument->script->add($js);
        if(isset($templates['css']))foreach($templates['css'] as $css) $svgDocument->css->add($css);
        if(isset($templates['defs']))foreach($templates['defs'] as $defs) $svgDocument->defs->add($defs);
        if(isset($templates['header']))foreach($templates['header'] as $comments) $svgDocument->headerComments->add($comments);
        if(isset($templates['footer']))foreach($templates['footer'] as $comments) $svgDocument->headerComments->add($comments);
        if(isset($templates['attributes']))foreach($templates['attributes'] as $attr) $svgDocument->svgAttributes->add($attr);
        
        $svgDocument->footerComments->add(implode("\n",$this->messages));
    }

    public function themeResponse($apiHandler)
    {
        $response = new \Freesewing\Response();
        $response->setFormat('raw');
        $response->setBody("{$apiHandler->svgDocument}");

        return $response;
    }

    public function cleanUp()
    {
    }

    public function loadTemplateHierarchy() 
    {
        $locations = $this->getClassChain();
        $templates = array();
        foreach($locations as $location) {
            if(is_readable("$location/config.yml")) {
                $dir = "$location/templates";
                $config = \Freesewing\Yamlr::loadConfig("$location/config.yml");
                foreach($config['templates'] as $type => $entries) {
                    foreach($entries as $entry) {
                        if(!isset($templates[$type][$entry])) {
                            $template = "$location/templates/$entry";
                            if(is_readable($template)) $templates[$type][$entry] = file_get_contents($template);
                        }
                    }
                }
            }
        }
        
        return $templates;
    }
    
    public function getClassChain() 
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filename = $reflector->getFileName();
        $locations[] = dirname($filename);
        do {
            $parent = $reflector->getParentClass();
            $reflector = new \ReflectionClass($parent->name);
            $filename = $reflector->getFileName();
            $locations[] = dirname($filename);
        } while ($parent->name != 'Freesewing\Themes\Theme');
        
        return $locations;
    }

    public function getTemplateDir() 
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filename = $reflector->getFileName();
        return dirname($filename).'/templates';
    }

    public function getTranslationFiles($locale, $altloc) 
    {
        $locations = $this->getClassChain();
        $translations = array();
        foreach($locations as $location) {
            $locfile = "$location/translations/messages.$locale.yml";
            $altfile = "$location/translations/messages.$altloc.yml";
            if(is_readable($locfile)) $translations[$locale][] = $locfile;
            if(is_readable($altfile)) $translations[$altloc][] = $altfile;
        }
        return $translations; 
    }

}
