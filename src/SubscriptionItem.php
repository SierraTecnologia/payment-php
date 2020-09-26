<?php

namespace SierraTecnologia;

/**
 * Class SubscriptionItem
 *
 * @property string $id
 * @property string $object
 * @property mixed $billing_thresholds
 * @property int $created
 * @property SierraTecnologiaObject $metadata
 * @property Plan $plan
 * @property int $quantity
 * @property string $subscription
 * @property array $tax_rates
 *
 * @package SierraTecnologia
 */
class SubscriptionItem extends ApiResource
{

    const OBJECT_NAME = "subscription_item";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
}
