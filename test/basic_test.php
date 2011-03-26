<?php

require dirname(__FILE__) . '/../lib/nice_http.php';

class BasicTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        // Disallow all connections to the *real* network
        // I'm on a plane, boyeeeee
        NiceHTTP::disallowExternalConnections();
    }

    public function testSimpleGet() {
        // Respond 'hello root path' to anything that's a GET request to the hostname example.com
        NiceHTTP::register(function($request) {
            if ($request->method == 'GET' && $request->url['host'] == 'example.com' && $request->url['path'] == '/') {
                return new BasicResponse(200, 'hello root path');
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
            if ($request->method == 'GET' && $request->url['host'] == 'example.com' && $request->url['path'] != '/') {
                return new BasicResponse(200, 'hello other path');
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
                return new BasicResponse(200, "The number is: $request->body");
            }
        });
        // Test we get the correct response
        $number = rand(0, 100);
        $response = NiceHTTP::post('http://example.com/some_path', $number);
        $this->assertEquals($response->body, "The number is: $number");
        $this->assertEquals($response->status, 200);
    }

    public function testDeleteBody() {
        $request = new DeleteRequest();
        try {
            $request->setBody('hello');
        } catch(BadFormatException $ex) {
            return; // for assertion below
        }
        $this->fail('DELETE with no body did not raise error');
    }

    public function testGetMethod() {
        $request = new GetRequest();
        $this->assertEquals($request->method, 'GET');
    }

    public function testDeleteMethod() {
        $request = new DeleteRequest();
        $this->assertEquals($request->method, 'DELETE');
    }

    public function testPostMethod() {
        $request = new PostRequest();
        $this->assertEquals($request->method, 'POST');
    }

    public function testPutMethod() {
        $request = new PutRequest();
        $this->assertEquals($request->method, 'PUT');
    }

    public function testHeaderOne() {
        $request = new GetRequest();
        $request->addHeader('one', 'one');
        $request->addHeader('two', 'two');

        $this->assertEquals(array_keys($request->headers), array('one', 'two'));
    }

    public function testMergeHeaders() {
        $request = new GetRequest();
        $request->addHeaders(array('one' => 'one'));
        $request->addHeaders(array('two' => 'two'));

        $this->assertEquals(array_keys($request->headers), array('one', 'two'));
    }

    public function testOverwriteHeaders() {
        $request = new GetRequest();
        $request->addHeaders(array('one' => 1));
        $request->addHeaders(array('one' => 2));
    
        $this->assertEquals($request->headers, array('one' => 2));
    }

}
