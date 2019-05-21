<?php

namespace SierraTecnologia\Radar;

class ValueListItemTest extends \SierraTecnologia\TestCase
{
    const TEST_RESOURCE_ID = 'rsli_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/radar/value_list_items'
        );
        $resources = ValueListItem::all([
            "value_list" => "rsl_123",
        ]);
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\Radar\\ValueListItem", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/radar/value_list_items/' . self::TEST_RESOURCE_ID
        );
        $resource = ValueListItem::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\Radar\\ValueListItem", $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/radar/value_list_items'
        );
        $resource = ValueListItem::create([
            "value_list" => "rsl_123",
            "value" => "value",
        ]);
        $this->assertInstanceOf("SierraTecnologia\\Radar\\ValueListItem", $resource);
    }

    public function testIsDeletable()
    {
        $resource = ValueListItem::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v1/radar/value_list_items/' . self::TEST_RESOURCE_ID
        );
        $resource->delete();
        $this->assertInstanceOf("SierraTecnologia\\Radar\\ValueListItem", $resource);
    }
}
