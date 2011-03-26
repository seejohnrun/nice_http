<?php namespace NiceHTTP;

require_once 'request.php';

class GetRequest extends Request {

    public function __construct($url = null, $body = null, $headers = array()) {
        parent::__construct('GET', $url, $body, $headers);
    }

}
