<?php
namespace cB\gClient\Requester;
use cB\gClient\Requester as ReqI;
use cB\gClient\Response  as ResI;

class cURL implements ReqI {
    protected static $default_opts = Array(
        CURLOPT_HEADER         => true
      , CURLOPT_RETURNTRANSFER => true
      , CURLOPT_FOLLOWLOCATION => true
      , CURLOPT_USERAGENT      => 'Mozilla/5.0'
    );
    protected $opts = Array();

    protected $headers = Array();
    protected $params  = Array();

    protected $method  =  'GET';

    public function __construct($url) {
        if (!(boolean)filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("{$url} is not a valid URL");
        }

        $this->opts = self::$default_opts;
        $this->opts[CURLOPT_URL] = $url;
    }

    public function method($method) {
        $method = strtoupper((string)$method);
        if (!in_array($method, Array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new Exception("Invalid method {$method}");
        }
        $this->method = $method;

        return $this;
    }

    public function setParameter($key, $val) {
        $this->params[$key] = $val;
        return $this;
    }

    public function addHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    public function setParameters(Array $parameters = Array()) {
        foreach ($parameters as $key => $val) {
            $this->setParameter($key, $val);
        }

        return $this;
    }

    public function addHeaders(Array $headers = Array()) {
        $this->headers += $headers;
        return $this;
    }

    public function request() {
        $this->opts[CURLOPT_HTTPHEADER] = $this->headers;

        switch ($this->method) {
            case 'GET':
                if (count($this->params) > 0) {
                    $this->opts[CURLOPT_URL] .= (false === strpos($this->opts[CURLOPT_URL], '?') ? '?' : '&');
                    $this->opts[CURLOPT_URL] .= http_build_query($this->params, null, '&');
                }
                break;
            case 'POST':
                $this->opts[CURLOPT_POST]       = true;
                $this->opts[CURLOPT_POSTFIELDS] = $this->params;
                break;
            case 'PUT':
                $this->opts[CURLOPT_POSTFIELDS] = http_build_query($this->params, null, '&');
                $this->addHeader('Content-Length: ' . strlen($this->opts[CURLOPT_POSTFIELDS]));
                $this->opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
                break;
            case 'DELETE':
                break;
            default:
                throw new Exception("Invalid method: {$this->method}");
        }

        $req = curl_init();
        curl_setopt_array($req, $this->opts);
        return new cURLResponse($req);
    }
}

class cURLResponse implements ResI {
    protected $xfer_info;
    protected $header;
    protected $response;

    public function __construct($req) {
        if (false === ($response = curl_exec($req))) {
            throw new cURLException(curl_error($req), curl_errno($req));
        }

        $this->xfer_info = curl_getinfo($req);

        $this->header   = substr($response, 0, $this->xfer_info['header_size']);
        $this->response = trim(substr($response, $this->xfer_info['header_size']));

        curl_close($req);
    }

    public function getStatusCode() {
        return $this->xfer_info['http_code'];
    }

    public function getHeader() {
        return $this->header;
    }

    public function getResponse() {
        return $this->response;
    }
}

class cURLException extends \Exception {}