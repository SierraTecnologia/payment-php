<?php

namespace SierraTecnologia;

/**
 * Class PaymentMethod
 *
 * @property string $id
 * @property string $object
 * @property mixed $billing_details
 * @property mixed $card
 * @property mixed $card_present
 * @property int $created
 * @property string $customer
 * @property mixed $ideal
 * @property bool $livemode
 * @property SierraTecnologiaObject $metadata
 * @property mixed $sepa_debit
 * @property string $type
 *
 * @package SierraTecnologia
 */
class PaymentMethod extends ApiResource
{

    const OBJECT_NAME = "payment_method";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
}
