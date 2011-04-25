<?php
namespace cB\gData\Auth;
use cB\gData;

const PROTOCOL_VERSION = 'GData-Version: 2.0';
const BASE_URL         = 'https://www.google.com/';

interface Authenticator {
    public function getHeaderString();
}

abstract class Adapter implements Authenticator {
    protected $token;
    protected $req_class;

    protected static $def_req_class = '\cB\gData\Requester\cURL';

    public function __construct() {
        $this->req_class = static::$def_req_class;
    }

    public function __sleep() {
        return Array('token', 'req_class');
    }

    public function __wakeup() {
        echo "Saved: {$this->token}\n";
        // make verification call to Google to determine if Auth Token is still valid
        // throw special exception if not, so reconnect with ClientLogin (or whatever)
    }

    /**
     * @param {String} $requester_class Classname of Client HTTP caller - must be instance of Requester
     */
    public function setRequester($requester_class) {
        if (!class_exists($requester_class)) {
            throw new Exception("{$requester_class} does not exist");
        }

        $test = new $requester_class();
        if (!($test instanceof \cB\gData\Requester)) {
            throw new Exception('Requester class must be instance of Requester');
        }

        $this->req_class = $requester_class;
    }

    protected function reqFactory($url) {
        $class = $this->req_class;
        return new $class($url);
    }

    public function request($url, $method, Array $data = Array(), Array $headers = Array()) {
        $res = $this->reqFactory($url)
            ->method($method)
            ->addParameter('alt', 'json')
            ->addParameters($data)
            ->addHeader(sprintf(static::getHeaderString(), $this->token))
            ->addHeader(PROTOCOL_VERSION)
            ->addHeaders($headers)
        ->request();

        return json_decode($res->getResponse(), true);
    }
}
?>