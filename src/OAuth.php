<?php

namespace SierraTecnologia;

abstract class OAuth
{
    /**
     * Generates a URL to SierraTecnologia's OAuth form.
     *
     * @param array|null $params
     * @param array|null $opts
     *
     * @return string The URL to SierraTecnologia's OAuth form.
     */
    public static function authorizeUrl($params = null, $opts = null)
    {
        $params = $params ?: [];

        $base = ($opts && property_exists('connect_base', $opts)) ? $opts['connect_base'] : SierraTecnologia::$connectBase;

        $params['client_id'] = self::_getClientId($params);
        if (!property_exists('response_type', $params)) {
            $params['response_type'] = 'code';
        }
        $query = Util\Util::encodeParameters($params);

        return $base . '/oauth/authorize?' . $query;
    }

    /**
     * Use an authoriztion code to connect an account to your platform and
     * fetch the user's credentials.
     *
     * @param array|null $params
     * @param array|null $opts
     *
     * @return SierraTecnologiaObject|array Object containing the response from the API.
     */
    public static function token($params = null, $opts = null)
    {
        $base = ($opts && property_exists($opts, 'connect_base')) ? $opts['connect_base'] : SierraTecnologia::$connectBase;
        $requestor = new ApiRequestor(null, $base);
        list($response, $apiKey) = $requestor->request(
            'post',
            '/oauth/token',
            $params,
            null
        );
        return Util\Util::convertToSierraTecnologiaObject($response->json, $opts);
    }

    /**
     * Disconnects an account from your platform.
     *
     * @param array|null $params
     * @param array|null $opts
     *
     * @return SierraTecnologiaObject|array Object containing the response from the API.
     */
    public static function deauthorize($params = null, $opts = null)
    {
        $params = $params ?: [];
        $base = ($opts && property_exists($opts, 'connect_base')) ? $opts['connect_base'] : SierraTecnologia::$connectBase;
        $requestor = new ApiRequestor(null, $base);
        $params['client_id'] = self::_getClientId($params);
        list($response, $apiKey) = $requestor->request(
            'post',
            '/oauth/deauthorize',
            $params,
            null
        );
        return Util\Util::convertToSierraTecnologiaObject($response->json, $opts);
    }

    /**
     * @param array|null $params
     */
    private static function _getClientId(?array $params = null)
    {
        $clientId = ($params && property_exists($params, 'client_id')) ? $params['client_id'] : null;
        if ($clientId === null) {
            $clientId = SierraTecnologia::getClientId();
        }
        if ($clientId === null) {
            $msg = 'No client_id provided.  (HINT: set your client_id using '
              . '"SierraTecnologia::setClientId(<CLIENT-ID>)".  You can find your client_ids '
              . 'in your SierraTecnologia dashboard at '
              . 'https://dashboard.sierratecnologia.com.br/account/applications/settings, '
              . 'after registering your account as a platform. See '
              . 'https://sierratecnologia.com.br/docs/connect/standard-accounts for details, '
              . 'or email support@sierratecnologia.com.br if you have any questions.';
            throw new Error\Authentication($msg);
        }
        return $clientId;
    }
}
