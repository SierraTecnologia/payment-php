<?php

namespace SierraTecnologia;

/**
 * Class Order
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $amount_returned
 * @property string $application
 * @property int $application_fee
 * @property string $charge
 * @property int $created
 * @property string $currency
 * @property string $customer
 * @property string $email
 * @property string $external_coupon_code
 * @property OrderItem[] $items
 * @property bool $livemode
 * @property SierraTecnologiaObject $metadata
 * @property Collection $returns
 * @property string $selected_shipping_method
 * @property mixed $shipping
 * @property mixed $shipping_methods
 * @property string $status
 * @property mixed $status_transitions
 * @property int $updated
 * @property string $upstream_id
 *
 * @package SierraTecnologia
 */
class Order extends ApiResource
{

    const OBJECT_NAME = "order";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
}
