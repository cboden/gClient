<?php
namespace gClient\Auth;
use gClient;

/**
 * Header telling Google what version of their API we're using
 * @var string
 */
const PROTOCOL_VERSION = 'gClient-Version: 2.0';

/**
 * Base URL of Google
 * @var string
 */
const BASE_URL         = 'https://www.google.com/';

/**
 * Base Google Authentication class.  All methods of authenticating
 * to Google should extend this class
 */
abstract class Adapter implements AuthenticatorInterface {
    /**
     * Authentication token received from Google
     * @var string
     */
    protected $token;

    /**
     * Classname of the requestor class to use when making REST calls
     * @var string
     */
    protected $req_class;

    /**
     * Default requestor class
     * @var string
     */
    const DEFAULT_CLIENT_CLASS = '\gClient\HTTP\cURL\Client';

    public function __construct() {
        $this->req_class = static::DEFAULT_CLIENT_CLASS;
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
     * @param string $requester_class Classname of Client HTTP caller - must be instance of Requester
     */
    public function setRequester($requester_class) {
        if (!class_exists($requester_class)) {
            throw new Exception("{$requester_class} does not exist");
        }

        $test = new $requester_class();
        if (!($test instanceof \gClient\HTTP\Client)) {
            throw new Exception('Requester class must be instance of HTTP\Client');
        }

        $this->req_class = $requester_class;
    }

    /**
     * @param string $url
     * @returns mixed Instance of previousl set requestor class
     */
    protected function reqFactory($url) {
        $class = $this->req_class;
        return new $class($url);
    }

    /**
     * Make an HTTP request to the Google API
     * @param string $url Valid URL to call
     * @param string $method (GET|POST|PUT|DELETE) HTTP method
     * @param Array $data Associative array of variables
     * @param Array $headers Additional headers to send
     * @returns Array
     */
    public function request($url, $method, Array $data = Array(), Array $headers = Array()) {
        $res = $this->reqFactory($url)
            ->method($method)
            ->setParameter('alt', 'json')
            ->setParameters($data)
            ->addHeader(sprintf(static::getHeaderString(), $this->token))
            ->addHeader(PROTOCOL_VERSION)
            ->addHeaders($headers)
        ->request();

        return json_decode($res->getResponse(), true);
    }
}