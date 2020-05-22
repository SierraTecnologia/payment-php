<?php

namespace SierraTecnologia\Checkout;

class SessionTest extends \SierraTecnologia\TestCase
{
    const TEST_RESOURCE_ID = 'cs_123';

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/checkout/sessions'
        );
        $resource = Session::create(
            [
            'cancel_url' => 'https://sierratecnologia.com.br/cancel',
            'client_reference_id' => '1234',
            'line_items' => [
                [
                    'amount' => 123,
                    'currency' => 'usd',
                    'description' => 'item 1',
                    'images' => [
                        'https://sierratecnologia.com.br/img1',
                    ],
                    'name' => 'name',
                    'quantity' => 2,
                ],
            ],
            'payment_intent_data' => [
                'receipt_email' => 'test@sierratecnologia.com.br',
            ],
            'payment_method_types' => ['card'],
            'success_url' => 'https://sierratecnologia.com.br/success'
            ]
        );
        $this->assertInstanceOf('SierraTecnologia\\Checkout\\Session', $resource);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/checkout/sessions/' . self::TEST_RESOURCE_ID
        );
        $resource = Session::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\Checkout\\Session", $resource);
    }
}
