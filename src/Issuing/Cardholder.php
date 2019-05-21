<?php

namespace SierraTecnologia\Issuing;

/**
 * Class Cardholder
 *
 * @property string $id
 * @property string $object
 * @property mixed $billing
 * @property int $created
 * @property string $email
 * @property bool $livemode
 * @property \SierraTecnologia\SierraTecnologiaObject $metadata
 * @property string $name
 * @property string $phone_number
 * @property string $status
 * @property string $type
 *
 * @package SierraTecnologia\Issuing
 */
class Cardholder extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "issuing.cardholder";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Retrieve;
    use \SierraTecnologia\ApiOperations\Update;
}
