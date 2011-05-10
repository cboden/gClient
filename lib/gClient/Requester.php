<?php
namespace gClient;

interface Requester {
    /**
     * @param string $url Valid URL to call
     * @returns $this
     */
    public function __construct($url);

    /**
     * @param string $header Method to call
     * @returns $this
     */
    public function method($method);

    /**
     * @param string $key
     * @param string $val
     * @returns $this
     */
    public function setParameter($key, $val);

    /**
     * @param string $header Add a header to the HTTP request
     * @returns $this
     */
    public function addHeader($header);

    /**
     * @param Array $parameters An associative array with key/val parings to be sent to setParameter()
     * @returns $this
     */
    public function setParameters(Array $parameters = Array());

    /**
     * @param Array $headers Headers to be added to the request
     * @returns $this
     */
    public function addHeaders(Array $headers = Array());

    /**
     * Make the HTTP request
     * @returns Response
     */
    public function request();
}

interface Response {
    /**
     * @param mixed $response Response from Requester
     */
    public function __construct($response);

    /**
     * @returns int 3 digit HTTP status code
     */
    public function getStatusCode();

    /**
     * @returns string Header lines from request
     */
    public function getHeader();

    /**
     * @returns string Body of HTTP request reesponse
     */
    public function getResponse();
}