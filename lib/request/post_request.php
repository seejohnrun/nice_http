<?php

require_once 'request.php';

class PostRequest extends Request {

    public function __construct($url = null, $body = null, $headers = array()) {
        parent::__construct('POST', $url, $body, $headers);
    } 

}
