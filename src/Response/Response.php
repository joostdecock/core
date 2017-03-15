<?php
/** Freesewing\Response class */
namespace Freesewing;

/**
 * Holds response status and data, and sends response
 *
 * This class holds the response format and status along with the response body
 * It can send the response, and provides a method for sending response headers
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Response
{
    /** Cache time in seconds (if caching is used) */
    const CACHETIME = 15552000;

    /** @var string $body Response body */
    public $body = null;

    /** @var string $format Response format */
    public $format = 'json';

    /** @var array $headers Array of headers to send */
    private $headers = array();

    /**
     * Adds caching headers
     *
     * @param \Freesewing\Request $request The request object
     */
    public function addCacheHeaders($request)
    {
        if ($request->getData('cache') === NULL) {
            $this->addHeader('cache', "Cache-Control: public, no-cache");
        } else {
            $this->addHeader('cache', "Cache-Control: public, max-age=".self::CACHETIME);
        }
    }

    /**
     * Sets the repsonse body
     *
     * @param string $body The response body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Returns the response body
     *
     * @return string The response body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the repsonse format
     *
     * Currently, only the format 'json' causes
     * anything else but the response being
     * send to the browser as-is.
     *
     * @param string $body The response body
     */
    public function setFormat($format)
    {
        $this->format = strtolower($format);
    }

    /**
     * Returns the response format
     *
     * @return string The response format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sends the response to the browser
     */
    public function send()
    {
        $this->sendHeaders();
        switch ($this->format) {
            case 'json':
                $body = $this->asJson($this->body);
                break;
            default:
                $body = $this->body;
                break;
        }
        printf("%s",$body);
    }

    /**
     * Adds headers to the headers property
     *
     * This adds headers to an array on position $key
     * This allows you to overwrite a header at a later stage
     *
     * @param string $key The id in the headers array
     * @param string $header The header to add
     */
    public function addHeader($key, $header)
    {
        $this->headers[$key] = $header;
    }

    /**
     * Pushes a ready-to-go header to the browser
     *
     * @param string $header The header ready to send
     */
    private function sendHeaders()
    {
        foreach ($this->headers as $header) {
            header($header);
        }
    }

    /**
     * Returns data passed to it as JSON
     *
     * @param string $data The data to encode
     */
    private function asJson($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }
}
