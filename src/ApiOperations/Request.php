<?php

namespace SierraTecnologia\ApiOperations;

/**
 * Trait for resources that need to make API requests.
 *
 * This trait should only be applied to classes that derive from SierraTecnologiaObject.
 */
trait Request
{
    /**
     * @param array|null|mixed $params The list of parameters to validate
     *
     * @throws \SierraTecnologia\Error\Api if $params exists and is not an array
     *
     * @return void
     */
    protected static function _validateParams($params = null)
    {
        if ($params && !is_array($params)) {
            $message = "You must pass an array as the first argument to SierraTecnologia API "
               . "method calls.  (HINT: an example call to create a charge "
               . "would be: \"SierraTecnologia\\Charge::create(['amount' => 100, "
               . "'currency' => 'usd', 'source' => 'tok_1234'])\")";
            throw new \SierraTecnologia\Error\Api($message);
        }
    }

    /**
     * @param string            $method  HTTP method ('get', 'post', etc.)
     * @param string            $url     URL for the request
     * @param array             $params  list of parameters for the request
     * @param array|string|null $options
     *
     * @return array tuple containing (the JSON response, $options)
     */
    protected function _request($method, $url, $params = [], $options = null)
    {
        $opts = $this->_opts->merge($options);
        list($resp, $options) = static::_staticRequest($method, $url, $params, $opts);
        $this->setLastResponse($resp);
        return [$resp->json, $options];
    }

    /**
     * @param string            $method  HTTP method ('get', 'post', etc.)
     * @param string            $url     URL for the request
     * @param array             $params  list of parameters for the request
     * @param array|string|null $options
     *
     * @return array tuple containing (the JSON response, $options)
     */
    protected static function _staticRequest($method, $url, $params, $options)
    {
        $opts = \SierraTecnologia\Util\RequestOptions::parse($options);
        $baseUrl = isset($opts->apiBase) ? $opts->apiBase : static::baseUrl();
        $requestor = new \SierraTecnologia\ApiRequestor($opts->apiKey, $baseUrl);

        list($response, $opts->apiKey) = $requestor->request($method, $url, $params, $opts->headers);
        $opts->discardNonPersistentHeaders();
        return [$response, $opts];
    }
}
