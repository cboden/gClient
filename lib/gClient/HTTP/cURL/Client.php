<?php
namespace gClient\HTTP\cURL;
use gClient\HTTP\ClientInterface as CI;
use gClient\HTTP;

/**
 * A \gClient\HTTP\ClientInterface implementation using the cURL library
 * @link http://code.google.com/apis/gdata/articles/using_cURL.html
 */
class Client implements CI {
    protected static $default_opts = Array(
        CURLOPT_HEADER         => true
      , CURLOPT_RETURNTRANSFER => true
      , CURLOPT_FOLLOWLOCATION => false
      , CURLOPT_USERAGENT      => 'Mozilla/5.0'
    );

    /**
     * @internal
     */
    protected $opts = Array();

    /**
     * @internal
     */
    protected $headers = Array();

    /**
     * @internal
     */
    protected $params  = Array();

    protected $method  =  'GET';

    /**
     * @param string Valid URL to call
     * @throws \InvalidArgumentException If an invalid URL is passed
     */
    public function __construct($url) {
        if (!(boolean)filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("{$url} is not a valid URL");
        }

        $this->opts = self::$default_opts;
        $this->opts[CURLOPT_URL] = $url;
    }

    /**
     * @param string (GET|POST|PUT|DELETE) Method to call
     * @return Client $this instance to enable a Fluent interface
     * @throws \UnexpectedArgumentException If an invalid method is passed
     */
    public function setMethod($method) {
        $method = strtoupper((string)$method);
        if (!in_array($method, Array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new \UnexpectedArgumentException("Invalid method {$method}");
        }
        $this->method = $method;

        return $this;
    }

    /**
     * @param array|object|string Data to be set as client body
     * @return Client $this instance to enable a Fluent interface
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
     * @return Client $this instance to enable a Fluent interface
     */
    public function setParameter($key, $val) {
        $this->params[$key] = $val;
        return $this;
    }

    /**
     * @param string Add a header to the HTTP request
     * @return Client $this instance to enable a Fluent interface
     */
    public function addHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * @param Array An associative array with key/val parings to be sent to setParameter()
     * @return Client $this instance to enable a Fluent interface
     */
    public function setParameters(Array $parameters = Array()) {
        foreach ($parameters as $key => $val) {
            $this->setParameter($key, $val);
        }

        return $this;
    }

    /**
     * @param Array Headers to be added to the request
     * @return Client $this instance to enable a Fluent interface
     */
    public function addHeaders(Array $headers = Array()) {
        $this->headers += $headers;
        return $this;
    }

    /**
     * Make the HTTP request
     * @return Response
     * @throws \gClient\HTTP\Exception If the server returns a status code of 300 or greater
     * @throws \UnexpectedValueException If an invalid HTTP Method was set
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
                //$this->opts[CURLOPT_POSTFIELDS] = http_build_query($this->params, null, '&');
                $this->opts[CURLOPT_POSTFIELDS] = $this->params;
                $this->addHeader('Content-Length: ' . strlen($this->opts[CURLOPT_POSTFIELDS]));
                $this->opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
                break;
            case 'DELETE':
                $this->opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
            default:
                throw new \UnexpectedValueException("Invalid method: {$this->method}");
        }

        $req = curl_init();
        curl_setopt_array($req, $this->opts);

        $response = new Response($req);

        if ($response->getStatusCode() >= 300) {
            throw new HTTP\Exception($response);
        }

        return $response;
    }
}