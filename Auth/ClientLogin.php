<?php
namespace cB\gData\Auth\ClientLogin;

const URL     = 'https://www.google.com/accounts/ClientLogin';
const SERVICE = 'cl';

namespace cB\gData\Auth;
use cB\Common, cB\gData\Auth\ClientLogin as CL;

class ClientLogin extends Adapter {
    public function __construct($username, $password, $client) {
// is __construct() called when __wakeup() is called?
//        if (empty($this->token)) {

// change this to call parent::request();

        $conn = new Common\cURL(
            new Common\TypeCast\URL(CL\URL)
          , 'POST'
          , Array(
                'Email'       => $username
              , 'accountType' => 'HOSTED_OR_GOOGLE'
              , 'Passwd'      => $password
              , 'source'      => $client
              , 'service'     => CL\SERVICE
            )
          , Array(PROTOCOL_VERSION)
        );
        $body = $conn->getResponse();

        if ($conn->getStatusCode() == 200) {
            $needle = 'Auth=';
            $this->token = trim(substr($body, strpos($body, $needle) + strlen($needle)));
        } else {
            echo $conn->getHeader();
            throw new \Exception('Error');
        }
//        }
    }

    // public function call() ??? 
}
?>