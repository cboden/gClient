<?php
namespace gClientTests\Auth;
use gClient\Auth;

class AnonymousTest extends \PHPUnit_Framework_TestCase {
    protected $_auth;

    public function setUp() {
        $this->_auth = new Auth\Anonymous();
    }

    public function testExtension() {
        $this->assertType('\gClient\Auth\Adapter', $this->_auth);
    }

    public function testAnonymousHeader() {
        $this->assertEquals($this->_auth->getHeaderString(), '');
    }
}