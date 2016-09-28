<?php

namespace Freesewing;

/**
 * Freesewing\ApiHandler class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class ApiHandler
{
    const configFile = __DIR__.'/../../config.yml';
    public $config;
    /**
     * @var \Freesewing\Model
     */
    public $model;

    /**
     * @var \Freesewing\SvgDocument
     */
    public $svgDocument;

    /**
     * @var array
     */
    private $context;

    /**
     * @var array
     */
    private $renderAs;

    /**
     * @var array
     */
    public $requestData;

    public function __construct($data)
    {
        $this->config = \Freesewing\Yamlr::loadConfig($this::configFile);
        $this->requestData = $data;
        $this->setContext();
    }

    public function handle()
    {
        $this->channel = $this->instantiateFromContext('channel');
        $this->pattern = $this->instantiateFromContext('pattern');
        $this->theme   = $this->instantiateFromContext('theme');

        if (
            !isset($this->response)
            &&
            $this->channel->isValidRequest($this->requestData) === true
        ) :

            $this->model = new \Freesewing\Model();
            $this->model->addMeasurements(
                $this->channel->standardizeModelMeasurements($this->requestData)
            );

            $this->pattern->addOptions(
                $this->channel->standardizePatternOptions($this->requestData)
            );

            $this->pattern->draft($this->model);

            $this->theme->themePattern($this->pattern);

            $this->pattern->layout();

            $this->renderAs = $this->theme->RenderAs();
            if ($this->renderAs['svg'] === true) $this->svgRender();
            if ($this->renderAs['js'] === true) $this->jsRender();
            
            $this->response = $this->theme->themeResponse($this); 
        
        else: // channel->isValidRequest() !== true
            $this->response = $this->bailOut(
                'bad_request',
                'Request not valid for channel '.$this->context['channel']
            );
        endif;

        $this->response->send();
        $this->channel->cleanUp();
    }

    public function getContext()
    {
        return $this->context;
    }

    private function svgRender() 
    {
        $this->svgRenderbot = new \Freesewing\SvgRenderbot();
        $this->svgDocument = new \Freesewing\SvgDocument(
            new \Freesewing\SvgComments(),
            new \Freesewing\SvgAttributes(),
            new \Freesewing\SvgCss(),
            new \Freesewing\SvgScript(),
            new \Freesewing\SvgDefs(),
            new \Freesewing\SvgComments()
        );
        // FIXME
        $this->svgDocument->svgAttributes->add("width =\"5000\"\nheight = \"5000\"");
        // format specific themeing
        $this->theme->themeSvg($this->svgDocument);
        
        // render SVG
        $this->svgDocument->setSvgBody($this->svgRenderbot->render($this->pattern));
    }

    private function bailOut($status, $info)
    {
        if (isset($this->response)) return $this->response;
        else {
            $response = new \Freesewing\Response();
            $response->setStatus($status);
            $response->setBody([
                'error' => ucwords(str_replace('_', ' ', $status)),
                'info' => $info,
            ]);
            $response->setFormat('json');

            return $response;
        }
    }

    private function instantiateFromContext($type)
    {
        $namespace = ucwords($type).'s';
        $class = '\Freesewing\\'.$namespace.'\\'.$this->context[$type];

        if (class_exists($class)) {
            return new $class();
        } else {
            $this->response = $this->bailOut(
                'bad_request',
                ucwords($type).' '.$this->context[$type].' not found'
            );
            return false;
        }
    }

    private function setContext()
    {
        foreach (['channel', 'pattern', 'theme'] as $type) {
            if (isset($this->requestData[$type])) $this->context[$type] = $this->requestData[$type];
            else {
                $this->context[$type] = $this->config['defaults'][$type];
            }
        }
    }
}
