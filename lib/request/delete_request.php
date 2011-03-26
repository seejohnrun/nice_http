<?php

require_once 'request.php';
require_once dirname(__FILE__) . '/../exceptions/bad_format_exception.php';

class DeleteRequest extends Request {

    public function __construct($url = null, $headers = array()) {
        parent::__construct('DELETE', $url, null, $headers);
    }

    public function setBody($body) {
        throw new BadFormatException('DELETE calls should not have bodies.  use ->body= to override');
    }

}
