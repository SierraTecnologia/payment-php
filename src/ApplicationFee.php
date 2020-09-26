<?php

namespace SierraTecnologia;

/**
 * Class ApplicationFee
 *
 * @property string $id
 * @property string $object
 * @property string $account
 * @property int $amount
 * @property int $amount_refunded
 * @property string $application
 * @property string $balance_transaction
 * @property string $charge
 * @property int $created
 * @property string $currency
 * @property bool $livemode
 * @property string $originating_transaction
 * @property bool $refunded
 * @property Collection $refunds
 *
 * @package SierraTecnologia
 */
class ApplicationFee extends ApiResource
{

    const OBJECT_NAME = "application_fee";

    use ApiOperations\All;
    use ApiOperations\NestedResource;
    use ApiOperations\Retrieve;

    const PATH_REFUNDS = '/refunds';
}
