<?php

require_once dirname(__FILE__) . '/../lib/nice_http.php';

class HeaderTest extends PHPUnit_Framework_TestCase {

    public function testHeaderOne() {
        $request = new NiceHTTP\GetRequest();
        $request->addHeader('one', 'one');
        $request->addHeader('two', 'two');

        $this->assertEquals(array_keys($request->headers), array('one', 'two'));
    }

    public function testMergeHeaders() {
        $request = new NiceHTTP\GetRequest();
        $request->addHeaders(array('one' => 'one'));
        $request->addHeaders(array('two' => 'two'));

        $this->assertEquals(array_keys($request->headers), array('one', 'two'));
    }

    public function testOverwriteHeaders() {
        $request = new NiceHTTP\GetRequest();
        $request->addHeaders(array('one' => 1));
        $request->addHeaders(array('one' => 2));
    
        $this->assertEquals($request->headers, array('one' => 2));
    }

}
