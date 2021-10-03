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

    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return SierraTecnologiaObject|array The created resource.
     */
    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        if (!property_exists($params, 'subscription_item')) {
            throw new Error\InvalidRequest("Missing subscription_item param in request", null);
        }
        $subscription_item = $params['subscription_item'];
        $url = "/v1/subscription_items/$subscription_item/usage_records";
        $request_params = $params;
        unset($request_params['subscription_item']);

        list($response, $opts) = static::_staticRequest('post', $url, $request_params, $options);
        $obj = \SierraTecnologia\Util\Util::convertToSierraTecnologiaObject($response->json, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }
}
