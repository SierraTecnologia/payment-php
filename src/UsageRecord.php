<?php

namespace SierraTecnologia;

/**
 * Class UsageRecord
 *
 * @package SierraTecnologia
 *
 * @property string $id
 * @property string $object
 * @property bool $livemode
 * @property int $quantity
 * @property string $subscription_item
 * @property int $timestamp
 */
class UsageRecord extends ApiResource
{
    const OBJECT_NAME = "usage_record";
}
