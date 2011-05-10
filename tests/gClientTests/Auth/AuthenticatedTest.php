<?php
namespace gClientTests\Auth;
use gClientTests\Auth\TestAssets\MockAdapter;

class AuthenticatedTest extends \PHPUnit_Framework_TestCase {
    protected $_adapter;

    public function setUp() {
        $this->_adapter = new MockAdapter();
    }

    public function testHeader() {
        $this->assertEquals($this->_adapter->getHeaderString(), 'Hello World!');
    }
}