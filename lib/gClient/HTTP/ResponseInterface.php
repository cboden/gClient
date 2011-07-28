<?php
namespace gClient\HTTP;

/**
 * An interface for parsing response received from ClientInterface
 */
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
     * @return string|bool
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