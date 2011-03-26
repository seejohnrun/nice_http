<?php

require_once dirname(__FILE__) . '/../lib/nice_http.php';

// These tests are just to make sure the right methods are being used
class VerbTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        NiceHTTP::disallowExternalConnections();
    }

    public function testGetMethod() {
        $request = new NiceHTTP\GetRequest();
        $this->assertEquals($request->method, 'GET');
    }

    public function testSimpleGetMethod() {
        $request = NiceHTTP::get('http://example.com')->request;
        $this->assertEquals($request->method, 'GET');
    }

    public function testDeleteMethod() {
        NiceHTTP::register(function($request) { if ($request->method == 'DELETE') return new NiceHTTP\BasicResponse(200); });
        $request = new NiceHTTP\DeleteRequest();
        $this->assertEquals($request->method, 'DELETE');
    }

    public function testSimpleDeleteMethod() {
        $request = NiceHTTP::delete('http://example.com')->request;
        $this->assertEquals($request->method, 'DELETE');
    }

    public function testPostMethod() {
        $request = new NiceHTTP\PostRequest();
        $this->assertEquals($request->method, 'POST');
    }

    public function testSimplePostMethod() {
        $request = NiceHTTP::post('http://example.com')->request;
        $this->assertEquals($request->method, 'POST');
    }

    public function testPutMethod() {
        $request = new NiceHTTP\PutRequest();
        $this->assertEquals($request->method, 'PUT');
    }

    public function testSimplePutMethod() {
        NiceHTTP::register(function($request) { if ($request->method == 'PUT') return new NiceHTTP\BasicResponse(200); });
        $request = NiceHTTP::put('http://example.com')->request;
        $this->assertEquals($request->method, 'PUT'); 
    }

}
