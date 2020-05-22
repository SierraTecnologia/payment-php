<?php

namespace SierraTecnologia\Terminal;

class ReaderTest extends \SierraTecnologia\TestCase
{
    const TEST_RESOURCE_ID = 'rdr_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/terminal/readers'
        );
        $resources = Reader::all();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\Terminal\\Reader", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID
        );
        $resource = Reader::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\Terminal\\Reader", $resource);
    }

    public function testIsSaveable()
    {
        $resource = Reader::retrieve(self::TEST_RESOURCE_ID);
        $resource->label = "new-name";

        $this->expectsRequest(
            'post',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID
        );
        $resource->save();
        $this->assertInstanceOf("SierraTecnologia\\Terminal\\Reader", $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID,
            ["label" => "new-name"]
        );
        $resource = Reader::update(
            self::TEST_RESOURCE_ID, [
            "label" => "new-name",
            ]
        );
        $this->assertInstanceOf("SierraTecnologia\\Terminal\\Reader", $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/terminal/readers',
            ["registration_code" => "a-b-c"]
        );
        $resource = Reader::create(['registration_code' => 'a-b-c']);
        $this->assertInstanceOf("SierraTecnologia\\Terminal\\Reader", $resource);
    }

    public function testIsDeletable()
    {
        $resource = Reader::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v1/terminal/readers/' . self::TEST_RESOURCE_ID
        );
        $resource->delete();
        $this->assertInstanceOf("SierraTecnologia\\Terminal\\Reader", $resource);
    }
}
