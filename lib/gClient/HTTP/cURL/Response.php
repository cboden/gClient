<?php
namespace gClient\HTTP\cURL;
use gClient\HTTP\ResponseInterface as RI;

class Response implements RI {
    /**
     * @uses curl_getinfo
     * @var Array
     */
    protected $xfer_info;

    /**
     * Header from HTTP response
     * @var string
     */
    protected $header;

    /**
     * Body from HTTP response
     * @var string
     */
    protected $content;

    /**
     * @param mixed Response from Requester
     */
    public function __construct($req) {
        $i = 0;
        do {
            $i++;

            if (false === ($response = curl_exec($req))) {
                throw new Exception(curl_error($req), curl_errno($req));
            }

            $this->xfer_info = curl_getinfo($req);

            $this->header  = substr($response, 0, $this->xfer_info['header_size']);
            $this->content = trim(substr($response, $this->xfer_info['header_size']));

            $code = $this->getStatusCode();
            if ($code < 300 || $code > 399) {
                break;
            }

            if (false === ($loc = $this->getHeaderItem('Location'))) {
                break;
            }

            curl_setopt($req, CURLOPT_URL, $loc);
        } while ($i <= 5);

        curl_close($req);
    }

    /**
     * @return int 3 digit HTTP status code
     */
    public function getStatusCode() {
        return (int)$this->xfer_info['http_code'];
    }

    /**
     * @param string
     * @return mixed(string|boolean)
     */
    public function getHeaderItem($key) {
        $needle = "\n{$key}: ";
        if (false === $start = strpos($this->header, $needle)) {
            return false;
        }
        $start += strlen($needle);
        $end    = strpos($this->header, "\n", $start);

        return trim(substr($this->header, $start, $end - $start));
    }

    /**
     * @return string Header lines from request
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @return string Body of HTTP request reesponse
     */
    public function getContent() {
        return $this->content;
    }
}