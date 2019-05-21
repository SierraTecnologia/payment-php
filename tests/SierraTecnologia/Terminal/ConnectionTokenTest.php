<?php

namespace SierraTecnologia\Terminal;

class ConnectionTokenTest extends \SierraTecnologia\TestCase
{
    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/terminal/connection_tokens'
        );
        $resource = ConnectionToken::create();
        $this->assertInstanceOf("SierraTecnologia\\Terminal\\ConnectionToken", $resource);
    }
}
