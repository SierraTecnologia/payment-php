<?php

namespace SierraTecnologia;

/**
 * Class EphemeralKey
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property int $expires
 * @property bool $livemode
 * @property string $secret
 * @property array $associated_objects
 *
 * @package SierraTecnologia
 */
class EphemeralKey extends ApiResource
{

    const OBJECT_NAME = "ephemeral_key";

    use ApiOperations\Create {
        create as protected _create;
    }
    use ApiOperations\Delete;

    /**
     * @param array|null        $params
     * @param array|string|null $opts
     *
     * @return EphemeralKey The created key.
     */
    public static function create($params = null, $opts = null)
    {
        if (!$opts['sitecpayment_version']) {
            throw new \InvalidArgumentException('sitecpayment_version must be specified to create an ephemeral key');
        }
        return self::_create($params, $opts);
    }
}
