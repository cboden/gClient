<?php
namespace cB\gData\Auth\ClientLogin;

const URL     = 'https://www.google.com/accounts/ClientLogin';
const SERVICE = 'cl'; // CL stands for Calendar...http://code.google.com/apis/gdata/faq.html#clientlogin -> this needs to be changed to be injected

namespace cB\gData\Auth;
use cB\gData\Auth\ClientLogin as CL;

class ClientLogin extends Adapter {
    public function __construct($username, $password, $client) {
        $req = $this->reqFactory(CL\URL)
            ->method('POST')
            ->addParameters(Array(
                'Email'       => $username
              , 'accountType' => 'HOSTED_OR_GOOGLE'
              , 'Passwd'      => $password
              , 'source'      => $client
              , 'service'     => CL\SERVICE
            ))
            ->addHeader(PROTOCOL_VERSION)
        ;

        $body = $req->getResponse();

        if ($req->getStatusCode() == 200) {
            $needle = 'Auth=';
            $this->token = trim(substr($body, strpos($body, $needle) + strlen($needle))); // should I do this through a parent fn?
        } else {
            echo $req->getHeader();
            throw new Exception('Error');
        }
    }

    public function getHeaderString() {
        return 'Authorization: GoogleLogin auth=%s';
    }
}
?>