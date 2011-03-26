<?php namespace NiceHTTP;

class Request {

    // These are left public for convenience
    var $method, $url, $body, $headers;

    function __construct($method, $url, $body = null, $headers = array()) {
        $this->method = $method;
        $this->setUrl($url);
        if ($body !== null) $this->setBody($body);
        $this->setHeaders($headers);
    }

    // Set the URL for this request.
    // This can take either an array (in parse_url format)
    // or a String of the URL
    function setUrl($url) {
        $this->url = is_array($url) ? $url : parse_url($url);
        return $this;
    }

    // Set the body for this request.  Expects a String
    function setBody($body) {
        $this->body = $body;
        return $this;
    }

    // Set the headers for this request.
    // Expects an associative array of headers
    function setHeaders($headers) {
        $this->headers = $headers;
        return $this;
    }

    // Add a given header - overwriting the existing one if it already
    // exists
    function addHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }

    // Add a given set of headers, overwriting values that already
    // exist
    function addHeaders($headers) {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    // Send this request and return a $response object
    function send() {
        $response = \NiceHTTP::match($this);
        if ($response !== null) return $response;

        if (\NiceHTTP::doesAllowExternalConnections()) return $this->actuallySend();
    }

    // Get the host of the current URL
    function getHost() {
        return is_array($this->url) ? $this->url['host'] : null;
    }
    
    // Get the path of the current URL
    function getPath() {
        if (is_array($this->url)) {
            if (array_key_exists('path', $this->url)) return $this->url['path'];
        }
    }

    private static function receive($fp) {
        $result = '';
        while (!feof($fp)) $result .= fgets($fp, 128);
        return $result;
    }

    // Actually make the request (using sockets) - 
    // Add certain missing headers like Content-Length if needed
    private function actuallySend() {
        $fp = fsockopen($this->getHost(), 80);
        fputs($fp, "$this->method {$this->getPath()} HTTP/1.1\r\n");     
        fputs($fp, "HOST: {$this->getHost()}\r\n");
        foreach ($this->headers as $key=> $value) fputs($fp, "$key: $value\r\n");

        if ($this->body !== null) {
            if (!array_key_exists('Content-Length', $this->headers)) {
                fputs($fp, 'Content-Length: ' . strlen($this->body) . "\r\n");
            }
            fputs($fp, $this->body);
        }

        // Receive the data
        fputs($fp, "Connection: close\r\n\r\n");
        $raw_data = self::receive($fp);
        fclose($fp);

        // Wrap it all in a response
        return new Response($this, $raw_data);
    }

}
