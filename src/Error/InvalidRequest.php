<?php

namespace SierraTecnologia\Error;

class InvalidRequest extends Base
{
    public function __construct(
        $message,
        $sierratecnologiaParam,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null
    ) {
        parent::__construct($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders);
        $this->sierratecnologiaParam = $sierratecnologiaParam;
    }
}
