<?php

namespace SierraTecnologia;

class OAuthTest extends TestCase
{
    public function testAuthorizeUrl()
    {
        $uriStr = OAuth::authorizeUrl([
            'scope' => 'read_write',
            'state' => 'csrf_token',
            'sierratecnologia_user' => [
                'email' => 'test@example.com',
                'url' => 'https://example.com/profile/test',
                'country' => 'US',
            ],
        ]);

        $uri = parse_url($uriStr);
        parse_str($uri['query'], $params);

        $this->assertSame('https', $uri['scheme']);
        $this->assertSame('connect.sierratecnologia.com.br', $uri['host']);
        $this->assertSame('/oauth/authorize', $uri['path']);

        $this->assertSame('ca_123', $params['client_id']);
        $this->assertSame('read_write', $params['scope']);
        $this->assertSame('test@example.com', $params['sierratecnologia_user']['email']);
        $this->assertSame('https://example.com/profile/test', $params['sierratecnologia_user']['url']);
        $this->assertSame('US', $params['sierratecnologia_user']['country']);
    }

    /**
     * @expectedException \SierraTecnologia\Error\Authentication
     * @expectedExceptionMessageRegExp #No client_id provided#
     */
    public function testRaisesAuthenticationErrorWhenNoClientId()
    {
        SierraTecnologia::setClientId(null);
        OAuth::authorizeUrl();
    }

    public function testToken()
    {
        $this->stubRequest(
            'POST',
            '/oauth/token',
            [
                'grant_type' => 'authorization_code',
                'code' => 'this_is_an_authorization_code',
            ],
            null,
            false,
            [
                'access_token' => 'sk_access_token',
                'scope' => 'read_only',
                'livemode' => false,
                'token_type' => 'bearer',
                'refresh_token' => 'sk_refresh_token',
                'sierratecnologia_user_id' => 'acct_test',
                'sierratecnologia_publishable_key' => 'pk_test',
            ],
            200,
            SierraTecnologia::$connectBase
        );

        $resp = OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => 'this_is_an_authorization_code',
        ]);
        $this->assertSame('sk_access_token', $resp->access_token);
    }

    public function testDeauthorize()
    {
        $this->stubRequest(
            'POST',
            '/oauth/deauthorize',
            [
                'sierratecnologia_user_id' => 'acct_test_deauth',
                'client_id' => 'ca_123',
            ],
            null,
            false,
            [
                'sierratecnologia_user_id' => 'acct_test_deauth',
            ],
            200,
            SierraTecnologia::$connectBase
        );

        $resp = OAuth::deauthorize([
                'sierratecnologia_user_id' => 'acct_test_deauth',
        ]);
        $this->assertSame('acct_test_deauth', $resp->sierratecnologia_user_id);
    }
}
