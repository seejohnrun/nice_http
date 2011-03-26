<?php

require_once dirname(__FILE__) . '/../lib/nice_http.php';

class DeleteTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        NiceHTTP::disallowExternalConnections();
        NiceHTTP::register(function($request) {
            if ($request->method == 'DELETE' && $request->getHost() == 'localhost') {
                return new NiceHTTP\BasicResponse(204);
            }
        });
    }

    public function testDelete() {
        $response = NiceHTTP::delete('http://localhost'); 
        $this->assertEquals($response->status, 204);
    }

}
