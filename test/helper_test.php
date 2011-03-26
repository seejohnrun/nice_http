<?php

require_once dirname(__FILE__) . '/../lib/nice_http.php';

class HelperTest extends PHPUnit_Framework_TestCase {

    function testIsGetHappy() {
        $request = new NiceHTTP\GetRequest();
        $this->assertTrue($request->isGet());
    }

    function testIsGetSad() {
        $request = new NiceHTTP\PostRequest();
        $this->assertFalse($request->isGet());
    }

    function testIsPostHappy() {
        $request = new NiceHTTP\PostRequest();
        $this->assertTrue($request->isPost());
    }

    function testIsPostSad() {
        $request = new NiceHTTP\GetRequest();
        $this->assertFalse($request->isPost());
    }

    function testIsPutHappy() {
        $request = new NiceHTTP\PutRequest();
        $this->assertTrue($request->isPut());
    }

    function testIsPutSad() {
        $request = new NiceHTTP\PostRequest();
        $this->assertFalse($request->isPut());
    }

    function testIsDeleteHappy() {
        $request = new NiceHTTP\DeleteRequest();
        $this->assertTrue($request->isDelete());
    }

    function testIsDeleteSad() {
        $request = new NiceHTTP\PostRequest();
        $this->assertFalse($request->isDelete());
    }

    function testHasPath() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertTrue($request->hasPath('/hello'));
    }

    function testNotHasPath() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertFalse($request->hasPath('/goodbye'));
    }

    function testHasPathLike() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertTrue($request->hasPathLike('/\/he.+/'));
    }

    function testNotHasPathLike() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertFalse($request->hasPathLike('/\/her.+/'));
    }

    function testHasHost() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertTrue($request->hasHost('localhost'));
    }

    function testNotHasHost() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertFalse($request->hasHost('example.com'));
    }

    function testHasHostLike() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertTrue($request->hasHostLike('/lo.+/'));
    }

    function testNotHasHostLike() {
        $request = new NiceHTTP\GetRequest('http://localhost/hello');
        $this->assertFalse($request->hasHostLike('/no.+/'));
    }

    function testHasPortDefault() {
        $request = new NiceHTTP\GetRequest('http://localhost');
        $this->assertTrue($request->hasPort(80));
    }

    function testNoHasPortDefault() {
        $request = new NiceHTTP\GetRequest('http://localhost');
        $this->assertFalse($request->hasPort(3000));
    }

    function testHasNonDefaultPort() {
        $request = new NiceHTTP\GetRequest('http://localhost:3000');
        $this->assertTrue($request->hasPort(3000));
    }

    function testHasHeader() {
        $request = new NiceHTTP\GetRequest('http://localhost', null, array('john' => 'ishere'));
        $this->assertTrue($request->hasHeader('john'));
    }

    function testNotHasHeader() {
        $request = new NiceHTTP\GetRequest('http://localhost', null, array('john' => 'ishere'));
        $this->assertFalse($request->hasHeader('john2'));
    }

    function testNotHasHeaderWithValue() {
        $request = new NiceHTTP\GetRequest('http://localhost', null, array('john' => 'ishere'));
        $this->assertFalse($request->hasHeader('john2', 'value'));
    }

    function testHasHeaderGoodValue() {
        $request = new NiceHTTP\GetRequest('http://localhost', null, array('john' => 'ishere'));
        $this->assertTrue($request->hasHeader('john', 'ishere'));
    }

    function testHasHeaderBadValue() {
        $request = new NiceHTTP\GetRequest('http://localhost', null, array('john' => 'ishere'));
        $this->assertFalse($request->hasHeader('john', 'isnothere'));
    }

}
