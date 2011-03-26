<?php

require_once dirname(__FILE__) . '/../lib/nice_http.php';

class PublicTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        NiceHTTP::allowExternalConnections();
    }

    public function testSimpleGet() {
        $response = NiceHTTP::get('http://localhost/');
        $this->assertEquals($response->status, 200);
        $this->assertEquals($response->headers['Content-Type'], 'text/html;charset=UTF-8');
    }

    public function testNotFound() {
        $response = NiceHTTP::get('http://localhost/nonexistent');
        $this->assertEquals($response->status, 404);
    }

}
