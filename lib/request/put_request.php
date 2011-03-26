<?php namespace NiceHTTP;

require_once 'request.php';

class PutRequest extends Request {

    public function __construct($url = null, $body = null, $headers = array()) {
        parent::__construct('PUT', $url, $body, $headers);
    }

}
