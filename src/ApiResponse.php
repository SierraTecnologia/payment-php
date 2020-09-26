<?php

namespace SierraTecnologia;

/**
 * Class ApiResponse
 *
 * @package SierraTecnologia
 */
class ApiResponse
{
    /**
     * @var array|null
     */
    public $headers;

    /**
     * @var string
     */
    public $body;

    /**
     * @var array|null
     */
    public $json;

    /**
     * @var int
     */
    public $code;

    /**
     * @param string     $body
     * @param integer    $code
     * @param array|null $headers
     * @param array|null $json
     *
     * @return obj An APIResponse
     */
    public function __construct($body, $code, $headers, $json)
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;
        $this->json = $json;
    }
}
