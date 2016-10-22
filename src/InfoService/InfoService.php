<?php

namespace Freesewing;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * Freesewing\InfoService class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class InfoService
{
    public function getServiceName()
    {
        return 'info';
    }

    public function run($context)
    {
        $format = $context->request->getData('format');
        if($context->request->getData('pattern') !== null)  {
            $context->addPattern();
            $context->setResponse($context->theme->themePatternInfo($this->getPatternInfo($context->pattern), $format));
        } else {
            $info['services'] = $context->config['services'];
            $info['patterns'] = $this->getPatternList($context);
            $info['channels'] = $this->getChannelList($context);
            $info['themes']   = $this->getThemeList($context);
            
            $context->setResponse($context->theme->themeInfo($info, $format));
        }
        
        $context->response->send();
        
        $context->cleanUp();
    }
    
    private function getPatternList($context)
    {
       foreach(glob($context->getApiDir() . '/patterns/*' , GLOB_ONLYDIR) as $dir) {
            $name = basename($dir);
            if($name != 'Pattern') {
                $config = $this->loadPatternConfig($name);
                $list[$name] = $config['info']['name'];
            }
        }
        return $list;
    }

    private function loadPatternConfig($pattern)
    {
        $class = '\Freesewing\Patterns\\'.$pattern;
        $pattern =  new $class();
        return $pattern->config;
    }
    
    private function getChannelList($context)
    {
        foreach(glob($context->getApiDir() . '/channels/*' , GLOB_ONLYDIR) as $dir) {
            $name = basename($dir);
            if($name != 'Channel' && $name != 'Info') $list[] = $name;
        }
        return $list;
    }

    private function getThemeList($context)
    {
        foreach(glob($context->getApiDir() . '/themes/*' , GLOB_ONLYDIR) as $dir) {
            $name = basename($dir);
            if($name != 'Theme' && $name != 'Info'&& $name != 'Sampler') $list[] = $name;
        }
        return $list;
    }
    
    private function getPatternInfo($pattern)
    {
        $info = $pattern->getConfig();
        $sampler = new \Freesewing\Sampler;
        $info['sampler']['measurements'] = \Freesewing\Yamlr::loadYamlFile($sampler->getSamplerConfigFile($pattern, 'measurements'));
        $info['sampler']['options'] = \Freesewing\Yamlr::loadYamlFile($sampler->getSamplerConfigFile($pattern, 'options'));
        $reflector = new \ReflectionClass(get_class($pattern));
        $filename = $reflector->getFileName();
        $info['pattern'] = basename(dirname($filename));
        return $info;
    }

}
