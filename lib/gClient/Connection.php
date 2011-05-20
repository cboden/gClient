<?php
namespace gClient;

/**
 * Base Google Connection class.  All services require an instance of this class
 * @link http://code.google.com/apis/gdata/docs/developers-guide.html Developer's Guide Overview
 * @link http://code.google.com/apis/gdata/docs/auth/overview.html Detailing various Authentication methods
 */
class Connection {
    const BASE_URL = 'https://www.google.com';

    /**
     * The authentication token used by classes extending this one to communicate with Google
     * @var String
     */
    protected $auth_token = null;

    /**
     * Upon __wakeup() being called, if true, verifies with Google
     *  that the saved token is still valid
     * The should only be false if the application frequently sleeps
     * @var boolean
     * @todo Make this functional - or remove it
     */
    public $verify_token_on_restoration = true;

    /**
     * List of services the connection is to work with
     * @var Array
     */
    protected $services = Array();

    /**
     * The instances of Services the connection can communicate with
     * @var Array of \gClient\ServiceInterface
     */
    protected $instances = Array();

    /**
     * Classname of the requestor class to use when making REST calls
     * Must implment \gClient\HTTP\ClientInterface
     * @var string
     */
    public $req_class = '\gClient\HTTP\cURL\Client';

    /**
     * Not extening this call creates an Anonymous connection
     */
    public function __construct() {
        $this->auth_token = '';
    }

    /**
     * Save this class in order to not re-authenticate with Google
     * <code>
     * <?php
     *     $savable_string = base64_encode(serialize($obj));
     * </code>
     */
    public function __sleep() {
        return Array('auth_token', 'services', 'req_class', 'verify_token_on_restoration');
    }

    public function __wakeup() {
        echo "Saved: {$this->auth_token}\n";

        if ($this->verify_token_on_restoration) {
            // todo
        }
    }

    /**
     * Before authenticating Google needs to know what Services you want to interact with - add them here
     * @param Name of the service to connect to...maps to \gClient\{$name}\Service class
     * @return $this
     * @todo Perhaps change this to a class that has methods/constants for Scope/Service/getClass
     */
    public function addService($name) {
        $this->services[$name] = $this->getServiceClass($name);
        return $this;
    }

    /**
     * @return $this
     */
    public function authenticate() {
        return $this;
    }

    /**
     * @param string Name of the service to retreive - make sure you added this service via addService() before authenticating
     * @return \gClient\ServiceInterface
     */
    public function getService($name) {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $class_path = $this->getServiceClass($name);
        $this->instances[$name] = new $class_path($this);
        return $this->instances[$name];
    }

    /**
     * @param name Verify the service exists
     */
    protected function getServiceClass($name) {
        $class_path = __NAMESPACE__ . '\\' . $name . '\\Service';
        if (!class_exists($class_path)) {
            throw new \RuntimeException("{$name} is not a valid service");
        }

        return $class_path;
    }

    /**
     * Determine if the Connection has been authenticated
     * @return boolean
     */
    public function isAuthenticated() {
        return !(boolean)is_null($this->auth_token);
    }

    /**
     * Create an HTTP request class
     * @param string The URL to request
     * @return \gClient\HTTP\ResponseInterface Instance of previously set requestor class
     */
    public function prepareCall($url) {
        if (!(boolean)filter_var($url, FILTER_VALIDATE_URL)) {
            $url = static::BASE_URL . $url;
        }

        $class  = $this->req_class;
        $client = new $class($url);

        if (!($client instanceof \gClient\HTTP\ClientInterface)) {
            throw new \Exception('Requester class must be instance of HTTP\ClientInterface');
        }

        return $client;

/*
            ->setParameter('alt', 'jsonc') // Needs to be put in Service
            ->addHeader('Content-Type: application/json') // Needs to be put in service
            ->addHeader(sprintf(static::getHeaderString(), $this->token)) // This is ok here
            ->addHeader('Host: accounts.google.com') // This can be put in OAuth maybe
            ->addHeader(static::PROTOCOL_VERSION) // This needs to be put in service
        ;
*/
    }
}