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

    // Determine if this was a GET request
    function isGet() { return $this->method == 'GET'; }
    
    // Determine if this was a POST request
    function isPost() { return $this->method == 'POST'; }

    // Determine if this was a PUT request
    function isPut() { return $this->method == 'PUT'; }

    // Determine if this was a DELETE request
    function isDelete() { return $this->method == 'DELETE'; }

    // Return a boolean as to whether or not this request has a given header
    // Optionally, supply a second value which is the value it must have to return true
    function hasHeader($name, $value = null) {
        if (!array_key_exists($name, $this->headers)) return false;
        if ($value === null) return true;
        return $value == $this->headers[$name];
    }

    // Determine if this request has a certain path
    function hasPath($path) {
        return $path == $this->getPath();
    }

    // Determine if the request has a host that matches a certain regex
    function hasPathLike($regex) {
        $path = $this->getPath();
        if ($path === null) return false;
        return preg_match($regex, $path) != 0;
    }

    // Determine if this request has a certain host
    function hasHost($host) {
        return $host == $this->getHost();
    }

    // Determine if the request has a host that matches a certain regex
    function hasHostLike($regex) {
        $host = $this->getHost();
        if ($host === null) return false;
        return preg_match($regex, $host) != 0;
    }

    // Get the host of the current URL
    function getHost() {
        return is_array($this->url) ? $this->url['host'] : null;
    }

    // Return the HTTP method of the request
    function getMethod() {
        return $this->method;
    }

    function hasPort($port) {
        return $this->getPort() == $port;
    }

    // Get the port of the current url
    function getPort() {
        if (is_array($this->url)) {
            return array_key_exists('port', $this->url) ? (int)$this->url['port'] : 80;
        }
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
