<?php
namespace cB\gClient;

interface Requester {
    /**
     * @param {String} $url Valid URL to call
     * @returns {Object} $this For chaining responses
     */
    public function __construct($url);

    /**
     * @param {String} $header Method to call
     * @returns {Object} $this For chaining responses
     */
    public function method($method);

    /**
     * @param {String} $key
     * @param {String} $val
     * @returns {Object} $this For chaining responses
     */
    public function setParameter($key, $val);

    /**
     * @param {String} $header Add a header to the HTTP request
     * @returns {Object} $this For chaining responses
     */
    public function addHeader($header);

    /**
     * @param {Array} $parameters An associative array with key/val parings to be sent to setParameter()
     * @returns {Object} $this For chaining responses
     */
    public function setParameters(Array $parameters = Array());

    /**
     * @param {Array} $headers Headers to be added to the request
     * @returns {Object} $this For chaining responses
     */
    public function addHeaders(Array $headers = Array());

    /**
     * Make the HTTP request
     * @returns {Response}
     */
    public function request();
}

interface Response {
    /**
     * @param {Mixed} $response Response from Requester
     */
    public function __construct($response);

    /**
     * @returns {Integer} 3 digit HTTP status code
     */
    public function getStatusCode();

    /**
     * @returns {String} Header lines from request
     */
    public function getHeader();

    /**
     * @returns {String} Body of HTTP request reesponse
     */
    public function getResponse();
}