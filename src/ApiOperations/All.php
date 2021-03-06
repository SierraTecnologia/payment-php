<?php

namespace SierraTecnologia\ApiOperations;

/**
 * Trait for listable resources. Adds a `all()` static method to the class.
 *
 * This trait should only be applied to classes that derive from SierraTecnologiaObject.
 */
trait All
{
    /**
     * @param array|null        $params
     * @param array|string|null $opts
     *
     * @return \SierraTecnologia\Collection of ApiResources
     */
    public static function all($params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('get', $url, $params, $opts);

        $obj = \SierraTecnologia\Util\Util::convertToSierraTecnologiaObject($response->json, $opts);
        if (!is_a($obj, 'SierraTecnologia\\Collection')) {
            $class = get_class($obj);
            $message = "Expected type \"SierraTecnologia\\Collection\", got \"$class\" instead";
            throw new \SierraTecnologia\Error\Api($message);
        }
        $obj->setLastResponse($response);
        $obj->setRequestParams($params);
        return $obj;
    }
}
