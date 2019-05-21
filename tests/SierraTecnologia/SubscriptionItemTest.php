<?php

namespace SierraTecnologia;

class SubscriptionItemTest extends TestCase
{
    const TEST_RESOURCE_ID = 'si_123';

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v1/subscription_items',
            [
                "subscription" => "sub_123"
            ]
        );
        $resources = SubscriptionItem::all([
            "subscription" => "sub_123"
        ]);
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\SubscriptionItem", $resources->data[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v1/subscription_items/' . self::TEST_RESOURCE_ID
        );
        $resource = SubscriptionItem::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf("SierraTecnologia\\SubscriptionItem", $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/subscription_items'
        );
        $resource = SubscriptionItem::create([
            "plan" => "plan",
            "subscription" => "sub_123"
        ]);
        $this->assertInstanceOf("SierraTecnologia\\SubscriptionItem", $resource);
    }

    public function testIsSaveable()
    {
        $resource = SubscriptionItem::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata["key"] = "value";
        $this->expectsRequest(
            'post',
            '/v1/subscription_items/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf("SierraTecnologia\\SubscriptionItem", $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'post',
            '/v1/subscription_items/' . self::TEST_RESOURCE_ID
        );
        $resource = SubscriptionItem::update(self::TEST_RESOURCE_ID, [
            "metadata" => ["key" => "value"],
        ]);
        $this->assertInstanceOf("SierraTecnologia\\SubscriptionItem", $resource);
    }

    public function testIsDeletable()
    {
        $resource = SubscriptionItem::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v1/subscription_items/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf("SierraTecnologia\\SubscriptionItem", $resource);
    }

    public function testCanListUsageRecordSummaries()
    {
        $resource = SubscriptionItem::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'get',
            '/v1/subscription_items/' . $resource->id . "/usage_record_summaries"
        );
        $resources = $resource->usageRecordSummaries();
        $this->assertTrue(is_array($resources->data));
        $this->assertInstanceOf("SierraTecnologia\\UsageRecordSummary", $resources->data[0]);
    }
}
