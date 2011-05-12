<?php
namespace gClient\Auth;

/**
 * Header telling Google what version of their API we're using
 * @var string
 */
const PROTOCOL_VERSION = 'GData-Version: 2';

/**
 * Base URL of Google
 * @var string
 */
const BASE_URL = 'https://www.google.com/';

/**
 * Base Google Authentication class.  All methods of authenticating
 * to Google should extend this class
 */
abstract class Adapter implements AuthenticatorInterface {
    /**
     * Upon __wakeup() being called, if true, verifies with Google
     *  that the saved token is still valid
     * The should only be false if the application frequently sleeps
     * @var boolean
     */
    public $verify_token_on_restoration = true;

    /**
     * Default requestor class
     * @var string
     */
    const DEFAULT_CLIENT_CLASS = '\gClient\HTTP\cURL\Client';

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

    public function __construct() {
        $this->req_class = static::DEFAULT_CLIENT_CLASS;
    }

    public function __sleep() {
        return Array('token', 'req_class', 'verify_token_on_restoration');
    }

    public function __wakeup() {
        echo "Saved: {$this->token}\n";

        if ($this->verify_token_on_restoration {
            // todo
        }
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
    public function reqFactory($url) {
        $class  = $this->req_class;
        $client = new $class($url);

        return $client
            ->setParameter('alt', 'jsonc')
            ->addHeader(sprintf(static::getHeaderString(), $this->token))
            ->addHeader(PROTOCOL_VERSION)
        ;
    }
}