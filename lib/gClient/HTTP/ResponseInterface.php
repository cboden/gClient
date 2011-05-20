<?php
namespace gClient\HTTP;

interface ResponseInterface {
    /**
     * @param mixed Response from Requester
     */
    public function __construct($response);

    /**
     * @return int 3 digit HTTP status code
     */
    public function getStatusCode();

    /**
     * @param string
     * @return string|boolean
     */
    public function getHeaderItem($key);

    /**
     * @return string Header lines from request
     */
    public function getHeader();

    /**
     * @return string Body of HTTP request reesponse
     */
    public function getContent();
}