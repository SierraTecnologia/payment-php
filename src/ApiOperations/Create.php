<?php

namespace SierraTecnologia\ApiOperations;

/**
 * Trait for creatable resources. Adds a `create()` static method to the class.
 *
 * This trait should only be applied to classes that derive from SierraTecnologiaObject.
 */
trait Create
{
    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return static The created resource.
     */
    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = \SierraTecnologia\Util\Util::convertToSierraTecnologiaObject($response->json, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }
}
