<?php
namespace gClient\Tests\Auth;
use gClient\Auth\OAuth;

/**
 * @covers \gClient\Auth\OAuth
 */
class OAuthTest extends \PHPUnit_Framework_TestCase {
    protected $_creds = Array('id', 'secret');
    protected $_oauth;

    public function setUp() {
        $this->_oauth = new OAuth($this->_creds[0], $this->_creds[1]);
    }

    public function testProtectedConstructVariables() {
        $this->assertAttributeEquals($this->_creds[0], 'client_id', $this->_oauth);
        $this->assertAttributeEquals($this->_creds[1], 'client_secret', $this->_oauth);
    }

    public function testSetAccessToken() {
        $token = 'the access token';

        $this->_oauth->setAccessToken($token);
        $this->assertAttributeEquals($token, 'auth_token', $this->_oauth);
    }

    public function testSetRefreshToken() {
        $token = 'the refresh token';

        $this->_oauth->setRefreshToken($token);
        $this->assertAttributeEquals($token, 'ref_token', $this->_oauth);
    }
}