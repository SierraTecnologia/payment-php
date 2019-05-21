<?php

namespace SierraTecnologia\Terminal;

/**
 * Class ConnectionToken
 *
 * @property string $secret
 *
 * @package SierraTecnologia\Terminal
 */
class ConnectionToken extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "terminal.connection_token";

    use \SierraTecnologia\ApiOperations\Create;
}
