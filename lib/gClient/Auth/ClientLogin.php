<?php
namespace gClient\Auth;
use gClient\HTTP;

/**
 * This Authentication class allows the developer to connect to the Google API with a standard username/password combination
 */
class ClientLogin extends Adapter {
    const URL = '/accounts/ClientLogin';

    /**
     * CL stands for Calendar...http://code.google.com/apis/gClient/faq.html#clientlogin
     * @note this needs to be changed to be injected
     * @var string
     */
    const SERVICE = 'cl';

    /**
     * @param string Your Google Account account name (email address)
     * @param string
     * @param string Short string identifying your application, for Google's logging purposes. This string should take the form: "companyName-applicationName-versionID".
     * @throws \gClient\HTTP\Exception
     */
    public function __construct($username, $password, $client) {
        parent::__construct();

        $req = new $this->req_class(static::BASE_URL . static::URL);
        $res = $req
            ->method('POST')
            ->setParameters(Array(
                'Email'       => $username
              , 'accountType' => 'HOSTED_OR_GOOGLE'
              , 'Passwd'      => $password
              , 'source'      => $client
              , 'service'     => static::SERVICE
            ))
        ->request();

        if ($res->getStatusCode() == 200) {
            $needle = 'Auth=';
            $body   = $res->getContent();
            $this->token = trim(substr($body, strpos($body, $needle) + strlen($needle))); // should I do this through a parent fn?
        } else {
            throw new HTTP\Exception($res);
        }
    }

    /**
     * Used by the parent Adapter class to pass authentication token
     * @internal
     * @return string
     */
    public function getHeaderString() {
        return 'Authorization: GoogleLogin auth=%s';
    }
}