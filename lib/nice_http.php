<?php

require 'response/response.php';
require 'response/basic_response.php';

require 'exceptions/bad_format_exception.php';

require 'request/request.php';
require 'request/get_request.php';
require 'request/post_request.php';
require 'request/delete_request.php';
require 'request/put_request.php';

class NiceHTTP {

    // A flag to indicate whether or not external connections are allowed
    // Should be set with disallow_external_connections() and allow_external_connections
    // By default, external connections are allowed
    private static $allow_external_connections = true;

    // The array of matchers to use before making a real request
    // Use the method #register to add these
    private static $matchers = array();

    // Set whether or not to allow external connections
    // This is useful for testing to see responses, and stub out services
    static function setAllowExternalConnections($b) {
        self::$allow_external_connections = $b;
    }

    // Disallow externalConnections
    static function disallowExternalConnections() {
        self::setAllowExternalConnections(false);
    }

    // Allow external connections
    static function allowExternalConnections() {
        self::setAllowExternalConnections(true);
    }

    // get whether or not we are allowing external connections
    static function doesAllowExternalConnections() {
        return self::$allow_external_connections;
    }

    // This function allows you to register a responder.
    // If there is a responder registered for a given request,
    // Instead of hitting the outside world, its return value will be returned.
    // These are evaluated in the order they are registered
    static function register($formula) {
        self::$matchers[] = $formula;
    }

    // Perform (construct and send) a GET request on the given url, 
    // with optional body and headers
    static function get($url, $body = null, $headers = array()) {
        $request = new GetRequest($url, $body, $headers);
        return $request->send();
    }

    // Perform (construct and send) a POST request on the given url 
    // with optional body and headers
    static function post($url, $body = null, $headers = array()) {
        $request = new PostRequest($url, $body, $headers);
        return $request->send();
    }

    // Search for a matcher for the given Request.
    // If we find one, return the proper response, otherwise return null
    static function match($request) {
        foreach (self::$matchers as $matcher) {
            $response = $matcher($request);
            if ($response !== null) return $response;
        } 
        return null;
    }

}
