<?php
namespace gClient\Auth;
use gClient\HTTP;

/**
 * OAuth2 Protocol
 * @link https://code.google.com/apis/console Developer URL to generate OAuth2 Authorization
 * @link http://code.google.com/apis/accounts/docs/OAuth2.html Using OAuth2 to access Google APIs
 */
class OAuth extends \gClient\Connection {
    const TOKEN_REQ_URL  = 'https://accounts.google.com/o/oauth2/auth';
    const TOKEN_AUTH_URL = 'https://accounts.google.com/o/oauth2/token';

    const TYPE  = 'code';

    const GRANT_AUTH = 'authorization_code';
    const GRANT_REF  = 'refresh_token';

    /**
     * Google API Project Secret
     * @var string
     */
    protected $client_id;

    /**
     * Google API Project Client secret
     * @var string
     */
    protected $client_secret;

    protected $expires = null;

    /**
     * Authenticated refresh token received from Google
     * @var string
     */
    protected $ref_token;

    /**
     * @param string Your Google API Project Client ID
     * @param string Your Google API Project Client secret
     */
    public function __construct($client_id, $client_secret) {
        parent::__construct();

        $this->client_id     = $client_id;
        $this->client_secret = $client_secret;
    }

    public function __sleep() {
        return Array('auth_token', 'ref_token', 'expires', 'client_id', 'client_secret', 'services', 'req_class', 'verify_token_on_restoration');
    }

    /**
     * Get the URL to redirect your user to in order to approve your application to access their account
     * @param string The registered URL (to your server) to redirect the user to
     * @return string
     */
    public function getTokenRequestURL($redirect_uri) {
        $scope = implode(' ', array_map(function($service) { return $service::getOAuthScope(); }, $this->services));

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
     * @throws \gData\HTTP\Exception
     */
    public function requestAuthToken($user_code) {
        $res = parent::prepareCall(static::TOKEN_AUTH_URL)
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

        $token_data       = json_decode($res->getContent(), true);
        $this->auth_token = $token_data['access_token'];
        $this->ref_token  = $token_data['refresh_token'];
        $this->setExpiration($token_data['expires_in']);
    }

    /**
     * Refresh the short-lived Access Token
     * This will need to be called every hour
     */
    public function refreshToken() {
        $res = parent::prepareCall(static::TOKEN_AUTH_URL)
            ->method('POST')
            ->setParameters(Array(
                'client_id'     => $this->client_id
              , 'client_secret' => $this->client_secret
              , 'refresh_token' => $this->ref_token
              , 'grant_type'    => static::GRANT_REF
            ))
        ->request();

        $token_data       = json_decode($res->getContent(), true);
        $this->auth_token = $token_data['access_token'];
        $this->setExpiration($token_data['expires_in']);
    }

    protected function setExpiration($expires) {
        $zone = new \DateTimeZone('UTC');
        $now  = new \DateTime(null, $zone);
        $exp  = clone $now;
        $exp->add(new \DateInterval("PT{$expires}S"));

        $this->expires = $exp;
    }

    public function prepareCall($url) {
        // check token exparation - refresh if past time (or 30 seconds)
        // ->addHeader('Host: accounts.google.com') // This can be put in OAuth maybe

        return parent::prepareCall($url)->addHeader("Authorization: OAuth {$this->auth_token}");
    }

    /**
     * You will only need to use this if you do not use the __sleep functionality
     * @param string Access Token given back from Google after a successful requestAuthToken()
     */
    public function setAccessToken($token) {
        $this->auth_token = $token;
    }

    /**
     * You will only need to use this if you do not use the __sleep functionality
     * @param string Refresh Token given back from Google after a successful requestAuthToken()
     */
    public function setRefreshToken($token) {
        $this->ref_token = $token;
    }
}