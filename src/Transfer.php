<?php

namespace SierraTecnologia;

/**
 * Class Transfer
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $amount_reversed
 * @property string $balance_transaction
 * @property int $created
 * @property string $currency
 * @property string $description
 * @property string $destination
 * @property string $destination_payment
 * @property bool $livemode
 * @property SierraTecnologiaObject $metadata
 * @property Collection $reversals
 * @property bool $reversed
 * @property string $source_transaction
 * @property string $source_type
 * @property string $transfer_group
 *
 * @package SierraTecnologia
 */
class Transfer extends ApiResource
{

    const OBJECT_NAME = "transfer";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\NestedResource;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    const PATH_REVERSALS = '/reversals';

    /**
     * Possible string representations of the source type of the transfer.
     *
     * @link https://sierratecnologia.com.br/docs/api/transfers/object#transfer_object-source_type
     */
    const SOURCE_TYPE_ALIPAY_ACCOUNT = 'alipay_account';
    const SOURCE_TYPE_BANK_ACCOUNT   = 'bank_account';
    const SOURCE_TYPE_CARD           = 'card';
    const SOURCE_TYPE_FINANCING      = 'financing';
}
