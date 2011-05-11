<?php
namespace gClient\HTTP;

interface ResponseInterface {
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
    public function getContent();
}