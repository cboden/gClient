<?php
namespace gClient\HTTP;

interface ClientInterface {
    /**
     * @param string Valid URL to call
     * @return $this
     */
    public function __construct($url);

    /**
     * @param string Method to call
     * @return $this
     */
    public function method($method);

    /**
     * @param mixed(array|object|string) Data to be set as client body
     * @return $this
     */
    public function setRawData($data);

    /**
     * @param string
     * @param string
     * @return $this
     */
    public function setParameter($key, $val);

    /**
     * @param string Add a header to the HTTP request
     * @return $this
     */
    public function addHeader($header);

    /**
     * @param Array An associative array with key/val parings to be sent to setParameter()
     * @return $this
     */
    public function setParameters(Array $parameters = Array());

    /**
     * @param Array Headers to be added to the request
     * @return $this
     */
    public function addHeaders(Array $headers = Array());

    /**
     * Make the HTTP request
     * @throws \gClient\HTTP\Exception If the server returns a status code of 300 or greater
     * @throws \UnexpectedValueException If an invalid HTTP Method was set
     * @return ResponseInterface
     */
    public function request();
}