<?php namespace NiceHTTP;

require_once 'response.php';

class BasicResponse extends Response {

    // Construct a response object with the given 
    // status, body, headers
    // default status -> 200
    // default body -> null
    // default headers -> array()
    function __construct($status = 200, $body = null, $headers = array()) {
        $this->status = $status;
        $this->body = $body;
        $this->headers = $headers;
    }

}
