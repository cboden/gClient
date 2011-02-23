<?php
namespace cB\gData\Auth;
use cB\Common, cB\Common\TypeCast, cB\gData;

const PROTOCOL_VERSION = 'GData-Version: 2.0';
const BASE_URL         = 'https://www.google.com/';

interface Authenticator {
    public function getHeaderString();
}

abstract class Adapter implements Authenticator {
    protected $token;

    public function __sleep() {
        return Array('token');
    }

    public function __wakeup() {
        echo "Saved: {$this->token}\n";
        // make verification call to Google to determine if Auth Token is still valid
        // throw special exception if not, so reconnect with ClientLogin (or whatever)
    }

    public function request(TypeCast\URL $url, $method, Array $data = Array(), Array $headers = Array()) {
        $conn = new Common\cURL(
            $url
          , $method
          , Array('alt' => 'json')
          , Array(sprintf(static::getHeaderString(), $this->token), PROTOCOL_VERSION)
        );

        $body = $conn->getResponse();
        return json_decode($body, true);
    }
}

class InvalidCredentialsException extends gData\Exception {}
?>