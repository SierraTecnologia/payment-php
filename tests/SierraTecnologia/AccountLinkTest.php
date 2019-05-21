<?php

namespace SierraTecnologia;

class AccountLinkTest extends TestCase
{
    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/account_links'
        );
        $resource = AccountLink::create([
            "account" => "acct_123",
            "failure_url" => "https://sierratecnologia.com.br/failure",
            "success_url" => "https://sierratecnologia.com.br/success",
            "type" => "custom_account_verification",
        ]);
        $this->assertInstanceOf("SierraTecnologia\\AccountLink", $resource);
    }
}
