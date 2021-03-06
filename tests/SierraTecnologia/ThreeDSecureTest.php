<?php

namespace SierraTecnologia;

class ThreeDSecureTest extends TestCase
{
    const TEST_RESOURCE_ID = 'tdsrc_123';

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/3d_secure/' . self::TEST_RESOURCE_ID
        );
        $resource = ThreeDSecure::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\ThreeDSecure", $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/3d_secure'
        );
        $resource = ThreeDSecure::create(
            [
            "amount" => 100,
            "currency" => "usd",
            "return_url" => "url"
            ]
        );
        $this->assertInstanceOf("SierraTecnologia\\ThreeDSecure", $resource);
    }
}
