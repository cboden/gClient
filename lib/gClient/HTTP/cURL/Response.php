<?php
namespace gClient\HTTP\cURL;
use gClient\HTTP\ResponseInterface as RI;

class Response implements RI {
    protected $xfer_info;
    protected $header;
    protected $response;

    /**
     * @param mixed $response Response from Requester
     */
    public function __construct($req) {
        if (false === ($response = curl_exec($req))) {
            throw new Exception(curl_error($req), curl_errno($req));
        }

        $this->xfer_info = curl_getinfo($req);

        $this->header   = substr($response, 0, $this->xfer_info['header_size']);
        $this->response = trim(substr($response, $this->xfer_info['header_size']));

        curl_close($req);
    }

    /**
     * @returns int 3 digit HTTP status code
     */
    public function getStatusCode() {
        return $this->xfer_info['http_code'];
    }

    /**
     * @returns string Header lines from request
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @returns string Body of HTTP request reesponse
     */
    public function getResponse() {
        return $this->response;
    }
}