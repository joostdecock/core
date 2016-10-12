<?php

namespace Freesewing;

/**
 * Freesewing\Response class.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */
class Response
{
    public $status = 'ok';
    public $body = null;
    public $format = 'json';

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

    public function getStatus()
    {
        return $this->status;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setFormat($format)
    {
        $this->format = strtolower($format);
    }

    public function getFormat()
    {
        return $this->format;
    }

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

    private function sendHeaderToBrowser($header)
    {
        header($header);
    }

    private function statusToMessage($status)
    {
        return ucwords(str_replace('_', ' ', $status));
    }

    private function asJson($data)
    {
        return json_encode($data,  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }
}
