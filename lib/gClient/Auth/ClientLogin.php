<?php
namespace gClient\Auth;

/**
 * This Authentication class allows the developer to connect
 * to the Google API with a standard username/password combination
 */
class ClientLogin extends Adapter {
    /**
     * The URL defining the ClientLogin protocol
     * @var string
     */
    const URL = '/accounts/ClientLogin';

    /**
     * CL stands for Calendar...http://code.google.com/apis/gClient/faq.html#clientlogin
     * @note this needs to be changed to be injected
     * @var string
     */
    const SERVICE = 'cl';

    /**
     * @param string $username
     * @param string $password
     * @param string $client
     * @throws Exception
     */
    public function __construct($username, $password, $client) {
        parent::__construct();

        $req = $this->reqFactory(static::BASE_URL . static::URL)
            ->method('POST')
            ->setParameters(Array(
                'Email'       => $username
              , 'accountType' => 'HOSTED_OR_GOOGLE'
              , 'Passwd'      => $password
              , 'source'      => $client
              , 'service'     => static::SERVICE
            ))
        ->request();

        if ($req->getStatusCode() == 200) {
            $needle = 'Auth=';
            $body   = $req->getContent();
            $this->token = trim(substr($body, strpos($body, $needle) + strlen($needle))); // should I do this through a parent fn?
        } else {
            echo $req->getHeader();
            throw new Exception('Error');
        }
    }

    /**
     * Used by the parent Adapter class to pass authentication token
     * @returns string
     */
    public function getHeaderString() {
        return 'Authorization: GoogleLogin auth=%s';
    }
}