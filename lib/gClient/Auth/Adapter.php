<?php
namespace gClient\Auth;

/**
 * Base Google Authentication class.  All methods of authenticating to Google should extend this class
 * @link http://code.google.com/apis/gdata/docs/developers-guide.html
 * @link http://code.google.com/apis/calendar/data/2.0/developers_guide_protocol.html
 */
abstract class Adapter implements AuthenticatorInterface {
    const BASE_URL = 'https://www.google.com';
    const PROTOCOL_VERSION = 'GData-Version: 2';
    const DEFAULT_CLIENT_CLASS = '\gClient\HTTP\cURL\Client';

    /**
     * Upon __wakeup() being called, if true, verifies with Google
     *  that the saved token is still valid
     * The should only be false if the application frequently sleeps
     * @var boolean
     */
    public $verify_token_on_restoration = true;

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

    /**
     * Save this class in order to not re-authenticate with Google
     * <code>
     * <?php
     *     $savable_string = base64_encode(serialize($obj));
     * </code>
     */
    public function __sleep() {
        return Array('token', 'req_class', 'verify_token_on_restoration');
    }

    public function __wakeup() {
        echo "Saved: {$this->token}\n";

        if ($this->verify_token_on_restoration) {
            // todo
        }
    }

    /**
     * Overwrite the default HTTP Client Request class.
     * Class MUST be an instance of \gClient\HTTP\ClientInterface
     * @param string Classname of Client HTTP caller
     * @throws Exception
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
     * Create an HTTP request class
     * @param string The URL to request
     * @return \gClient\HTTP\ResponseInterface Instance of previously set requestor class
     */
    public function reqFactory($url) {
        static $base_len = 0;
        if ($base_len === 0) {
            $base_len = strlen(static::BASE_URL);
        }

        if (substr($url, 0, $base_len) != static::BASE_URL) {
            if (substr($url, 0, 1) != '/') {
                $url = '/' . $url;
            }

            $url = static::BASE_URL . $url;
        }

        $class  = $this->req_class;
        $client = new $class($url);

        return $client
            ->setParameter('alt', 'jsonc')
            ->addHeader('Content-Type: application/json')
            ->addHeader(sprintf(static::getHeaderString(), $this->token))
            ->addHeader(static::PROTOCOL_VERSION)
        ;
    }
}