<?php
namespace gClient\Auth;
use gClient\HTTP;

/**
 * This Authentication class allows the developer to connect to the Google API with a standard username/password combination
 */
class ClientLogin extends \gClient\Connection {
    const URI = '/accounts/ClientLogin';

    protected $credentials = Array();

    /**
     * @param string Your Google Account account name (email address)
     * @param string
     * @param string Short string identifying your application, for Google's logging purposes. This string should take the form: "companyName-applicationName-versionID".
     * @throws \gClient\HTTP\Exception
     */
    public function __construct($username, $password, $client) {
        $this->credentials = compact('username', 'password', 'client');
    }

    /**
     * Authenticate against Google to make private calls
     * @throws \RuntimeException If more than one (or no) services have been added via \gClient\Connection::addService()
     * @throws \gClient\HTTP\Exception If authentication against Google fails
     * @return ClientLogin $this
     */
    public function authenticate() {
        if (count($this->services) !== 1) {
            throw new \RuntimeException('ClientLogin requires exactly one service');
        }

        $service = $this->getServiceClass(current(array_keys($this->services)));
        extract($this->credentials);

        $res = parent::prepareCall(static::URI)
            ->setMethod('POST')
            ->setParameters(Array(
                'Email'       => $username
              , 'accountType' => 'HOSTED_OR_GOOGLE'
              , 'Passwd'      => $password
              , 'source'      => $client
              , 'service'     => $service::getClientLoginService()
            ))
        ->request();

        if ($res->getStatusCode() != 200) {
            throw new HTTP\Exception($res);
        }

        $needle = 'Auth=';
        $body   = $res->getContent();
        $this->auth_token = trim(substr($body, strpos($body, $needle) + strlen($needle))); // should I do this through a parent fn?

        return $this;
    }

    public function prepareCall($url) {
        return parent::prepareCall($url)->addHeader("Authorization: GoogleLogin auth={$this->auth_token}");
    }
}