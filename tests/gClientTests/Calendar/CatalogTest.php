<?php
namespace gClientTests\Calendar;
use gClient\Calendar;

class CatalogTest extends \PHPUnit_Framework_TestCase {
    protected $_adapter;
    protected $_catalog;

    public function setUp() {
        $this->_adapter = new \gClientTests\Auth\TestAssets\MockAdapter();
        $this->_catalog = new Calendar\Catalog($this->_adapter);
    }

    public function testCreateNull() {
        $this->setExpectedException('\UnexpectedValueException');
        $this->_catalog->createCalendar();
    }

    public function testCreateEmpty() {
        $this->setExpectedException('\UnexpectedValueException');
        $this->_catalog->createCalendar('');
    }

    public function testDeleteInvalid() {
        $this->setExpectedException('\UnexpectedValueException');
        $this->_catalog->deleteCalendar('invalid url');
    }
}