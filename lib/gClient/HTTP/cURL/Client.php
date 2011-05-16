<?php
namespace gClient\HTTP\cURL;
use gClient\HTTP\ClientInterface as CI;

class Client implements CI {
    protected static $default_opts = Array(
        CURLOPT_HEADER         => true
      , CURLOPT_RETURNTRANSFER => true
      , CURLOPT_FOLLOWLOCATION => false
      , CURLOPT_USERAGENT      => 'Mozilla/5.0'
    );
    protected $opts = Array();

    protected $headers = Array();
    protected $params  = Array();

    protected $method  =  'GET';

    /**
     * @param string Valid URL to call
     * @return $this
     */
    public function __construct($url) {
        if (!(boolean)filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("{$url} is not a valid URL");
        }

        $this->opts = self::$default_opts;
        $this->opts[CURLOPT_URL] = $url;
    }

    /**
     * @param string Method to call
     * @return $this
     */
    public function method($method) {
        $method = strtoupper((string)$method);
        if (!in_array($method, Array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new Exception("Invalid method {$method}");
        }
        $this->method = $method;

        return $this;
    }

    /**
     * @param mixed(array|object|string) Data to be set as client body
     * @return $this
     */
    public function setRawData($data) {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        $this->params = (string)$data;
        return $this;
    }

    /**
     * @param string
     * @param string
     * @return $this
     */
    public function setParameter($key, $val) {
        $this->params[$key] = $val;
        return $this;
    }

    /**
     * @param string Add a header to the HTTP request
     * @return $this
     */
    public function addHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * @param Array An associative array with key/val parings to be sent to setParameter()
     * @return $this
     */
    public function setParameters(Array $parameters = Array()) {
        foreach ($parameters as $key => $val) {
            $this->setParameter($key, $val);
        }

        return $this;
    }

    /**
     * @param Array Headers to be added to the request
     * @return $this
     */
    public function addHeaders(Array $headers = Array()) {
        $this->headers += $headers;
        return $this;
    }

    /**
     * Make the HTTP request
     * @return Response
     */
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
        return new Response($req);
    }
}