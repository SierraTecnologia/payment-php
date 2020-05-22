<?php

namespace SierraTecnologia;

class RecipientTest extends TestCase
{
    const TEST_RESOURCE_ID = 'rp_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/recipients'
        );
        $resources = Recipient::all();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\Recipient", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/recipients/' . self::TEST_RESOURCE_ID
        );
        $resource = Recipient::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\Recipient", $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/recipients'
        );
        $resource = Recipient::create(
            [
            "name" => "name",
            "type" => "individual"
            ]
        );
        $this->assertInstanceOf("SierraTecnologia\\Recipient", $resource);
    }

    public function testIsSaveable()
    {
        $resource = Recipient::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata["key"] = "value";
        $this->expectsRequest(
            'post',
            '/v1/recipients/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf("SierraTecnologia\\Recipient", $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/recipients/' . self::TEST_RESOURCE_ID
        );
        $resource = Recipient::update(
            self::TEST_RESOURCE_ID, [
            "metadata" => ["key" => "value"],
            ]
        );
        $this->assertInstanceOf("SierraTecnologia\\Recipient", $resource);
    }

    public function testIsDeletable()
    {
        $resource = Recipient::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v1/recipients/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf("SierraTecnologia\\Recipient", $resource);
    }

    public function testCanListTransfers()
    {
        $recipient = Recipient::retrieve(self::TEST_RESOURCE_ID);

        // sierratecnologia-mock does not support this anymore so we stub it
        $this->stubRequest(
            'get',
            '/v1/transfers',
            ["recipient" => $recipient->id],
            null,
            false,
            [
                "object" => "list",
                "data" => [["id" => "tr_123", "object" => "transfer"]]
            ]
        );
        $resources = $recipient->transfers();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\Transfer", $resources->data[0]);
    }
}
