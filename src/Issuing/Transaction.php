<?php

namespace SierraTecnologia\Issuing;

/**
 * Class Transaction
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property string $authorization
 * @property string $balance_transaction
 * @property string $card
 * @property string $cardholder
 * @property int $created
 * @property string $currency
 * @property string $dispute
 * @property bool $livemode
 * @property mixed $merchant_data
 * @property \SierraTecnologia\SierraTecnologiaObject $metadata
 * @property string $type
 *
 * @package SierraTecnologia\Issuing
 */
class Transaction extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "issuing.transaction";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Retrieve;
    use \SierraTecnologia\ApiOperations\Update;
}
