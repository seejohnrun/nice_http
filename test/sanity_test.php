<?php

require_once dirname(__FILE__) . '/../lib/nice_http.php';

class BasicTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        // Disallow all connections to the *real* network
        // I'm on a plane, boyeeeee
        NiceHTTP::disallowExternalConnections();
    }

    public function testGetVersion() {
        $this->assertNotEquals(NiceHTTP::version(), '');
    }

    public function testSimpleGet() {
        // Respond 'hello root path' to anything that's a GET request to the hostname example.com
        NiceHTTP::register(function($request) {
            if ($request->method == 'GET' && $request->getHost() == 'example.com' && $request->getPath() == '/') {
                return new NiceHTTP\BasicResponse(200, 'hello root path');
            }
        });
        // And test the response
        $response = NiceHTTP::get('http://example.com/');
        $this->assertEquals($response->status, 200);
        $this->assertEquals($response->body, 'hello root path');
    }

    public function testWithPath() {
        // Respond 'hello other path' to anything that's a GET request to the hostname example.com
        NiceHTTP::register(function($request) {
            if ($request->method == 'GET' && $request->getHost() == 'example.com' && $request->getPath() != '/') {
                return new NiceHTTP\BasicResponse(200, 'hello other path');
            }
        });
        $response = NiceHTTP::get('http://example.com/some_path');
        $this->assertEquals($response->status, 200);
        $this->assertEquals($response->body, 'hello other path');
    }

    public function testPost() {
        // Response to anything that's a POST request to the hostname example.com
        NiceHTTP::register(function($request) {
            if ($request->method == 'POST' && $request->url['host'] == 'example.com') {
                return new NiceHTTP\BasicResponse(200, "The number is: $request->body");
            }
        });
        // Test we get the correct response
        $number = rand(0, 100);
        $response = NiceHTTP::post('http://example.com/some_path', $number);
        $this->assertEquals($response->body, "The number is: $number");
        $this->assertEquals($response->status, 200);
    }

    public function testDeleteBody() {
        $request = new NiceHTTP\DeleteRequest();
        try {
            $request->setBody('hello');
        } catch(NiceHTTP\BadFormatException $ex) {
            return; // for assertion below
        }
        $this->fail('DELETE with no body did not raise error');
    }

}
