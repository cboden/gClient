<?php
namespace gClient\HTTP;

/**
 * @property ResponseInterface $response HTTP Response object - useful for fetching headers
 */
class Exception extends \Exception {
    protected $readonly = Array();

    public function __construct(ResponseInterface $response) {
        $msg = $response->getContent();
        if (substr($msg, 0, 1) == '{') {
            $data = @json_decode($msg, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $msg = $data['error']['message'];
            }
        }

        parent::__construct($msg, $response->getStatusCode());
        $this->readonly['response'] = $response;
    }

    public function &__get($name) {
        if (!isset($this->readonly[$name])) {
            $this->readonly[$name] = '';
        }

        return $this->readonly[$name];
    }
}