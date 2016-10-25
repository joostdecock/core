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
    /** @var string $status Status of the response */
    public $status = 'ok';

    /** @var string $body Response body */
    public $body = null;

    /** @var string $format Response format */
    public $format = 'json';

    /**
     * Sets the response status 
     *
     * This only sets the status if it's in the array
     * of allowed response statuses 
     *
     * @param string $status The status to set
     *
     * @throws InvalidArgumentException If the status is not allowed
     */
    public function setStatus($status)
    {
        $allowedStatuses = [
            'ok',
            'bad_request',
            'unauthorized',
            'forbidden',
            'not_found',
            'not_acceptable',
            'api_down',
            'server_error',
            ];
        if (in_array($status, $allowedStatuses)) {
            $this->status = strtolower($status);
        } else {
            throw new \InvalidArgumentException($status.' is not a supported response status');
        }
    }

    /**
     * Returns the status property
     *
     * @return string The response status
     */
    public function getStatus()
    {
        return $this->status;
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
        $this->sendHeader($this->status);
        switch ($this->format) {
            case 'json':
                $body = $this->asJson($this->body);
                break;
            default:
                $body = $this->body;
                break;
        }
        echo $body;
    }

    /**
     * Sends a header to the browser for a given response status
     *
     * @param string $status The response status
     */
    private function sendHeader($status)
    {
        switch ($status) {
            case 'ok':
                $statuscode = 200;
                break;
            case 'bad_request':
                $statuscode = 400;
                break;
            case 'unauthorized':
                $statuscode = 401;
                break;
            case 'forbidden':
                $statuscode = 403;
                break;
            case 'not_found':
                $statuscode = 404;
                break;
            case 'not_acceptable':
                $statuscode = 406;
                break;
            case 'api_down':
                $statuscode = 503;
                break;
            case 'server_error':
                $statuscode = 500;
                break;
        }
        $text = $this->statusToMessage($status);
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        $this->sendHeaderToBrowser($protocol.' '.$statuscode.' '.$text);
    }

    /**
     * Pushes a ready-to-go header to the browser
     *
     * @param string $header The header ready to send
     */
    private function sendHeaderToBrowser($header)
    {
        header($header);
    }

    /**
     * Turns a status string into text
     *
     * @param string $status The response status
     */
    private function statusToMessage($status)
    {
        return ucwords(str_replace('_', ' ', $status));
    }

    /**
     * Returns data passed to it as JSON
     *
     * @param string $data The data to encode
     */
    private function asJson($data)
    {
        return json_encode($data,  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }
}
