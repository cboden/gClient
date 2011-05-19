<?php
namespace gClient\Auth;
use gClient\HTTP;

/**
 * OAuth2 Protocol
 * @link https://code.google.com/apis/console
 */
class OAuth extends Adapter {
    const TOKEN_REQ_URL  = 'https://accounts.google.com/o/oauth2/auth';
    const TOKEN_AUTH_URL = 'https://accounts.google.com/o/oauth2/token';

    const TYPE  = 'code';

    const GRANT_AUTH = 'authorization_code';
    const GRANT_REF  = 'refresh_token';

    protected $client_id;
    protected $client_secret;
    protected $refresh;

    /**
     * @param string Your Google API Project Client ID
     */
    public function __construct($client_id, $client_secret) {
        parent::__construct();

        $this->client_id     = $client_id;
        $this->client_secret = $client_secret;
    }

    public function __sleep() {
        return Array('token', 'refresh', 'client_id', 'client_secret', 'req_class', 'verify_token_on_restoration');
    }

    /**
     * Get the URL to redirect your user to in order to approve your application to access their account
     * @param string The scope(s) of the user's account you wish to access
     * @param string The registered URL (to your server) to redirect the user to
     * @return string
     */
    public function getTokenRequestURL($scope, $redirect_uri) {
        return static::TOKEN_REQ_URL . '?' . http_build_query(Array(
            'client_id'     => $this->client_id
          , 'redirect_uri'  => $redirect_uri
          , 'scope'         => $scope
          , 'response_type' => static::TYPE
        ), null, '&');
    }

    /**
     * After the user approves your application turn the temporary token into a permenant token
     * @param string The user code (query string parameter) given back to your redirect URI
     * @param string Your Google API Project Client secret
     * @throws \gData\HTTP\Exception
     */
    public function requestAuthToken($user_code) {
        $req = new $this->req_class(static::TOKEN_AUTH_URL);
        $res = $req
            ->method('POST')
            ->setParameters(Array(
                'code'          => $user_code
              , 'client_id'     => $this->client_id
              , 'client_secret' => $this->client_secret
              , 'grant_type'    => static::GRANT_AUTH
            ))
        ->request();

        if ($res->getStatusCode() != 200) {
            throw new HTTP\Exception($res);
        }

        $token_data    = json_decode($res->getContent(), true);
        $this->token   = $token_data['access_token'];
        $this->refresh = $token_data['refresh_token'];
    }

    /**
     */
    public function refreshToken() {
        $req = new $this->req_class(static::TOKEN_AUTH_URL);
        $res = $req
            ->method('POST')
            ->setParameters(Array(
                'client_id'     => $this->client_id
              , 'client_secret' => $this->client_secret
              , 'refresh_token' => $this->refresh
              , 'grant_type'    => static::GRANT_REF
            ))
        ->request();

        $token_data  = json_decode($res->getContent(), true);
        $this->token = $token_data['access_token'];
    }

    public function getHeaderString() {
        return 'Authorization: OAuth %s';
    }

    /**
     * You will only need to use this if you do not use the __sleep functionality
     * @param string Access Token given back from Google after a successful requestAuthToken()
     */
    public function setAccessToken($token) {
        $this->token = $token;
    }

    /**
     * You will only need to use this if you do not use the __sleep functionality
     * @param string Refresh Token given back from Google after a successful requestAuthToken()
     */
    public function setRefreshToken($token) {
        $this->refresh = $token;
    }
}