<?php

namespace SierraTecnologia;

/**
 * Class ApiRequestor
 *
 * @package SierraTecnologia
 */
class ApiRequestor
{
    /**
     * @var string|null
     */
    private $_apiKey;

    /**
     * @var string
     */
    private $_apiBase;

    /**
     * @var HttpClient\ClientInterface
     */
    private static $_httpClient;

    /**
     * @var RequestTelemetry
     */
    private static $requestTelemetry;

    /**
     * ApiRequestor constructor.
     *
     * @param string|null $apiKey
     * @param string|null $apiBase
     */
    public function __construct($apiKey = null, $apiBase = null)
    {
        $this->_apiKey = $apiKey;
        if (!$apiBase) {
            $apiBase = SierraTecnologia::$apiBase;
        }
        $this->_apiBase = $apiBase;
    }

    /**
     * Creates a telemetry json blob for use in 'X-SierraTecnologia-Client-Telemetry' headers
     *
     * @static
     *
     * @param  RequestTelemetry $requestTelemetry
     * @return string
     */
    private static function _telemetryJson($requestTelemetry)
    {
        $payload = array(
            'last_request_metrics' => array(
                'request_id' => $requestTelemetry->requestId,
                'request_duration_ms' => $requestTelemetry->requestDuration,
        ));

        $result = json_encode($payload);
        if ($result != false) {
            return $result;
        } else {
            SierraTecnologia::getLogger()->error("Serializing telemetry payload failed!");
            return "{}";
        }
    }

    /**
     * @static
     *
     * @param ApiResource|bool|array|mixed $d
     *
     * @return ApiResource|array|string|mixed
     */
    private static function _encodeObjects($d)
    {
        if ($d instanceof ApiResource) {
            return Util\Util::utf8($d->id);
        } elseif ($d === true) {
            return 'true';
        } elseif ($d === false) {
            return 'false';
        } elseif (is_array($d)) {
            $res = [];
            foreach ($d as $k => $v) {
                $res[$k] = self::_encodeObjects($v);
            }
            return $res;
        } else {
            return Util\Util::utf8($d);
        }
    }

    /**
     * @param string     $method
     * @param string     $url
     * @param array|null $params
     * @param array|null $headers
     *
     * @return (ApiResponse|mixed)[] An array whose first element is an API response and second element is the API key used to make the request.
     *
     * @throws Error\Api
     * @throws Error\Authentication
     * @throws Error\Card
     * @throws Error\InvalidRequest
     * @throws Error\OAuth\InvalidClient
     * @throws Error\OAuth\InvalidGrant
     * @throws Error\OAuth\InvalidRequest
     * @throws Error\OAuth\InvalidScope
     * @throws Error\OAuth\UnsupportedGrantType
     * @throws Error\OAuth\UnsupportedResponseType
     * @throws Error\Permission
     * @throws Error\RateLimit
     * @throws Error\Idempotency
     * @throws Error\ApiConnection
     *
     * @psalm-return array{0: ApiResponse, 1: mixed}
     */
    public function request($method, $url, $params = null, $headers = null): array
    {
        $params = $params ?: [];
        $headers = $headers ?: [];
        list($rbody, $rcode, $rheaders, $myApiKey) =
        $this->_requestRaw($method, $url, $params, $headers);
        $json = $this->_interpretResponse($rbody, $rcode, $rheaders);
        $resp = new ApiResponse($rbody, $rcode, $rheaders, $json);
        return [$resp, $myApiKey];
    }

    /**
     * @param string $rbody    A JSON string.
     * @param int    $rcode
     * @param array  $rheaders
     * @param array  $resp
     *
     * @throws Error\InvalidRequest if the error is caused by the user.
     * @throws Error\Authentication if the error is caused by a lack of
     *    permissions.
     * @throws Error\Permission if the error is caused by insufficient
     *    permissions.
     * @throws Error\Card if the error is the error code is 402 (payment
     *    required)
     * @throws Error\InvalidRequest if the error is caused by the user.
     * @throws Error\Idempotency if the error is caused by an idempotency key.
     * @throws Error\OAuth\InvalidClient
     * @throws Error\OAuth\InvalidGrant
     * @throws Error\OAuth\InvalidRequest
     * @throws Error\OAuth\InvalidScope
     * @throws Error\OAuth\UnsupportedGrantType
     * @throws Error\OAuth\UnsupportedResponseType
     * @throws Error\Permission if the error is caused by insufficient
     *    permissions.
     * @throws Error\RateLimit if the error is caused by too many requests
     *    hitting the API.
     * @throws Error\Api otherwise.
     *
     * @return void
     */
    public function handleErrorResponse($rbody, $rcode, $rheaders, $resp): void
    {
        if (!is_array($resp) || !isset($resp['error'])) {
            $msg = "Invalid response object from API: $rbody "
              . "(HTTP response code was $rcode)";
            throw new Error\Api($msg, $rcode, $rbody, $resp, $rheaders);
        }

        $errorData = $resp['error'];

        $error = null;
        if (is_string($errorData)) {
            $error = self::_specificOAuthError($rbody, $rcode, $rheaders, $resp, $errorData);
        }
        if (!$error) {
            $error = self::_specificAPIError($rbody, $rcode, $rheaders, $resp, $errorData);
        }

        throw $error;
    }

    /**
     * @static
     *
     * @param string $rbody
     * @param int    $rcode
     * @param array  $rheaders
     * @param array  $resp
     * @param array  $errorData
     *
     * @return Error\RateLimit|Error\Idempotency|Error\InvalidRequest|Error\Authentication|Error\Card|Error\Permission|Error\Api
     */
    private static function _specificAPIError($rbody, $rcode, $rheaders, $resp, $errorData)
    {
        $msg = isset($errorData['message']) ? $errorData['message'] : null;
        $param = isset($errorData['param']) ? $errorData['param'] : null;
        $code = isset($errorData['code']) ? $errorData['code'] : null;
        $type = isset($errorData['type']) ? $errorData['type'] : null;

        switch ($rcode) {
        case 400:
            // 'rate_limit' code is deprecated, but left here for backwards compatibility
            // for API versions earlier than 2015-09-08
            if ($code == 'rate_limit') {
                return new Error\RateLimit($msg, $param, $rcode, $rbody, $resp, $rheaders);
            }
            if ($type == 'idempotency_error') {
                return new Error\Idempotency($msg, $rcode, $rbody, $resp, $rheaders);
            }

            // intentional fall-through
        case 404:
            return new Error\InvalidRequest($msg, $param, $rcode, $rbody, $resp, $rheaders);
        case 401:
            return new Error\Authentication($msg, $rcode, $rbody, $resp, $rheaders);
        case 402:
            return new Error\Card($msg, $param, $code, $rcode, $rbody, $resp, $rheaders);
        case 403:
            return new Error\Permission($msg, $rcode, $rbody, $resp, $rheaders);
        case 429:
            return new Error\RateLimit($msg, $param, $rcode, $rbody, $resp, $rheaders);
        default:
            return new Error\Api($msg, $rcode, $rbody, $resp, $rheaders);
        }
    }

    /**
     * @static
     *
     * @param string|bool $rbody
     * @param int         $rcode
     * @param array       $rheaders
     * @param array       $resp
     * @param string      $errorCode
     *
     * @return null|Error\OAuth\InvalidClient|Error\OAuth\InvalidGrant|Error\OAuth\InvalidRequest|Error\OAuth\InvalidScope|Error\OAuth\UnsupportedGrantType|Error\OAuth\UnsupportedResponseType
     */
    private static function _specificOAuthError($rbody, $rcode, $rheaders, $resp, $errorCode)
    {
        $description = isset($resp['error_description']) ? $resp['error_description'] : $errorCode;

        switch ($errorCode) {
        case 'invalid_client':
            return new Error\OAuth\InvalidClient($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
        case 'invalid_grant':
            return new Error\OAuth\InvalidGrant($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
        case 'invalid_request':
            return new Error\OAuth\InvalidRequest($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
        case 'invalid_scope':
            return new Error\OAuth\InvalidScope($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
        case 'unsupported_grant_type':
            return new Error\OAuth\UnsupportedGrantType($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
        case 'unsupported_response_type':
            return new Error\OAuth\UnsupportedResponseType($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
        }

        return null;
    }

    /**
     * @static
     *
     * @param null|array $appInfo
     *
     * @return null|string
     */
    private static function _formatAppInfo($appInfo)
    {
        if ($appInfo !== null) {
            $string = $appInfo['name'];
            if ($appInfo['version'] !== null) {
                $string .= '/' . $appInfo['version'];
            }
            if ($appInfo['url'] !== null) {
                $string .= ' (' . $appInfo['url'] . ')';
            }
            return $string;
        } else {
            return null;
        }
    }

    /**
     * @static
     *
     * @param string $apiKey
     * @param null   $clientInfo
     *
     * @return (false|string)[]
     *
     * @psalm-return array{X-SierraTecnologia-Client-User-Agent: false|string, User-Agent: string, Authorization: string}
     */
    private static function _defaultHeaders($apiKey, $clientInfo = null): array
    {
        $uaString = 'SierraTecnologia/v1 PhpBindings/' . SierraTecnologia::VERSION;

        $langVersion = phpversion();
        $uname = php_uname();

        $appInfo = SierraTecnologia::getAppInfo();
        $ua = [
            'bindings_version' => SierraTecnologia::VERSION,
            'lang' => 'php',
            'lang_version' => $langVersion,
            'publisher' => 'sitecpayment',
            'uname' => $uname,
        ];
        if ($clientInfo) {
            $ua = array_merge($clientInfo, $ua);
        }
        if ($appInfo !== null) {
            $uaString .= ' ' . self::_formatAppInfo($appInfo);
            $ua['application'] = $appInfo;
        }

        $defaultHeaders = [
            'X-SierraTecnologia-Client-User-Agent' => json_encode($ua),
            'User-Agent' => $uaString,
            'Authorization' => 'Bearer ' . $apiKey,
        ];
        return $defaultHeaders;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     * @param array  $headers
     *
     * @return (mixed|string)[]
     *
     * @throws Error\Api
     * @throws Error\ApiConnection
     * @throws Error\Authentication
     *
     * @psalm-return array{0: mixed, 1: mixed, 2: mixed, 3: mixed|string}
     */
    private function _requestRaw($method, $url, $params, $headers): array
    {
        $myApiKey = $this->_apiKey;
        if (!$myApiKey) {
            $myApiKey = SierraTecnologia::$apiKey;
        }

        if (!$myApiKey) {
            $msg = 'No API key provided.  (HINT: set your API key using '
              . '"SierraTecnologia::setApiKey(<API-KEY>)".  You can generate API keys from '
              . 'the SierraTecnologia web interface.  See https://sierratecnologia.com.br/api for '
              . 'details, or email support@sierratecnologia.com.br if you have any questions.';
            throw new Error\Authentication($msg);
        }

        // Clients can supply arbitrary additional keys to be included in the
        // X-SierraTecnologia-Client-User-Agent header via the optional getUserAgentInfo()
        // method
        $clientUAInfo = null;
        if (method_exists($this->httpClient(), 'getUserAgentInfo')) {
            $clientUAInfo = $this->httpClient()->getUserAgentInfo();
        }

        $absUrl = $this->_apiBase.$url;
        $params = self::_encodeObjects($params);
        $defaultHeaders = $this->_defaultHeaders($myApiKey, $clientUAInfo);
        if (SierraTecnologia::$apiVersion) {
            $defaultHeaders['SierraTecnologia-Version'] = SierraTecnologia::$apiVersion;
        }

        if (SierraTecnologia::$accountId) {
            $defaultHeaders['SierraTecnologia-Account'] = SierraTecnologia::$accountId;
        }

        if (SierraTecnologia::$enableTelemetry && self::$requestTelemetry != null) {
            $defaultHeaders["X-SierraTecnologia-Client-Telemetry"] = self::_telemetryJson(self::$requestTelemetry);
        }

        $hasFile = false;
        $hasCurlFile = class_exists('\CURLFile', false);
        foreach ($params as $k => $v) {
            if (is_resource($v)) {
                $hasFile = true;
                $params[$k] = self::_processResourceParam($v, $hasCurlFile);
            } elseif ($hasCurlFile && $v instanceof \CURLFile) {
                $hasFile = true;
            }
        }

        if ($hasFile) {
            $defaultHeaders['Content-Type'] = 'multipart/form-data';
        } else {
            $defaultHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $combinedHeaders = array_merge($defaultHeaders, $headers);
        $rawHeaders = [];

        foreach ($combinedHeaders as $header => $value) {
            $rawHeaders[] = $header . ': ' . $value;
        }

        $requestStartMs = Util\Util::currentTimeMillis();

        list($rbody, $rcode, $rheaders) = $this->httpClient()->request(
            $method,
            $absUrl,
            $rawHeaders,
            $params,
            $hasFile
        );

        if (property_exists($rheaders, 'request-id')) {
            self::$requestTelemetry = new RequestTelemetry(
                $rheaders['request-id'],
                Util\Util::currentTimeMillis() - $requestStartMs
            );
        }

        return [$rbody, $rcode, $rheaders, $myApiKey];
    }

    /**
     * @param resource $resource
     * @param bool     $hasCurlFile
     *
     * @return \CURLFile|string
     * @throws Error\Api
     */
    private function _processResourceParam($resource, $hasCurlFile)
    {
        if (get_resource_type($resource) !== 'stream') {
            throw new Error\Api(
                'Attempted to upload a resource that is not a stream'
            );
        }

        $metaData = stream_get_meta_data($resource);
        if ($metaData['wrapper_type'] !== 'plainfile') {
            throw new Error\Api(
                'Only plainfile resource streams are supported'
            );
        }

        if ($hasCurlFile) {
            // We don't have the filename or mimetype, but the API doesn't care
            return new \CURLFile($metaData['uri']);
        } else {
            return '@'.$metaData['uri'];
        }
    }

    /**
     * @param string $rbody
     * @param int    $rcode
     * @param array  $rheaders
     *
     * @return mixed
     * @throws Error\Api
     * @throws Error\Authentication
     * @throws Error\Card
     * @throws Error\InvalidRequest
     * @throws Error\OAuth\InvalidClient
     * @throws Error\OAuth\InvalidGrant
     * @throws Error\OAuth\InvalidRequest
     * @throws Error\OAuth\InvalidScope
     * @throws Error\OAuth\UnsupportedGrantType
     * @throws Error\OAuth\UnsupportedResponseType
     * @throws Error\Permission
     * @throws Error\RateLimit
     * @throws Error\Idempotency
     */
    private function _interpretResponse($rbody, $rcode, $rheaders)
    {
        $resp = json_decode($rbody, true);
        $jsonError = json_last_error();
        if ($resp === null && $jsonError !== JSON_ERROR_NONE) {
            $msg = "Invalid response body from API: $rbody "
              . "(HTTP response code was $rcode, json_last_error() was $jsonError)";
            throw new Error\Api($msg, $rcode, $rbody);
        }

        if ($rcode < 200 || $rcode >= 300) {
            $this->handleErrorResponse($rbody, $rcode, $rheaders, $resp);
        }
        return $resp;
    }

    /**
     * @return HttpClient\ClientInterface
     */
    private function httpClient()
    {
        if (!self::$_httpClient) {
            self::$_httpClient = HttpClient\CurlClient::instance();
        }
        return self::$_httpClient;
    }
}
