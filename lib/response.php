<?php

require_once 'nice_http.php';

class Response {

    // These are left public for convenience
    var $status, $body, $headers;    

    function __construct($request, $data) {
        $this->request = $request;
        $this->parse($data);
    }

    // Parse the given response data and fill out this object
    private function parse($data) {
        $p = explode("\r\n\r\n", $data, 2);
        $this->body = isset($p[1]) ? $p[1] : '';

        $header_data = isset($p[0]) ? $p[0] : '';
        $this->headers = array();
        foreach (explode("\r\n", $header_data) as $data) {
            if (substr($data, 0, 4) == 'HTTP') { $this->parseStatus($data); continue; } // status line
            $p = explode(':', $data, 2);
            if (count($p) == 2) $this->headers[$p[0]] = trim($p[1]);
            else $this->headers[$p[0]] = null;
        }
    }

    // Parse the http status line and grab the status number
    private function parseStatus($status) {
        preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $status, $match);
        if (count($match) == 2) $this->status = (int) $match[1];
    }

}
