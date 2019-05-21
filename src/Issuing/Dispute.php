<?php

namespace SierraTecnologia\Issuing;

/**
 * Class Dispute
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $created
 * @property string $currency
 * @property mixed $evidence
 * @property bool $livemode
 * @property \SierraTecnologia\SierraTecnologiaObject $metadata
 * @property string $reason
 * @property string $status
 * @property Transaction $transaction
 *
 * @package SierraTecnologia\Issuing
 */
class Dispute extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "issuing.dispute";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Retrieve;
    use \SierraTecnologia\ApiOperations\Update;
}
