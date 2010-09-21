<?php
namespace cB\gData\Auth;
use cB\Common, cB\Common\TypeCast;

const PROTOCOL_VERSION = 'GData-Version: 2';
const BASE_URL         = 'https://www.google.com/';

abstract class Adapter {
    protected $token;

    public function __sleep() {
        return Array('token');
    }

    public function request(TypeCast\URL $url, $method, Array $data = Array(), Array $headers = Array()) {
        $conn = new Common\cURL(
            $url
          , 'get'
          , Array('alt' => 'json')
          , Array('Authorization: GoogleLogin auth=' . $this->token, PROTOCOL_VERSION)
        );

        // This fixed protocol_version error, still not working as intended though...
/*
        $conn->addOptions(Array(
            CURLOPT_COOKIEFILE => '/tmp/gdata.cookie'
          , CURLOPT_COOKIEJAR => '/tmp/gdata.cookie'
        ));
/**/

        $body = $conn->getResponse();
        return json_decode($body, true);
    }
}
?>