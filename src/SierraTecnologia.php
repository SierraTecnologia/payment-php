<?php

namespace SierraTecnologia;

/**
 * Class SierraTecnologia
 *
 * @package SierraTecnologia
 */
class SierraTecnologia
{
    // @var string The SierraTecnologia API key to be used for requests.
    /**
     * @var null|string
     */
    public static $apiKey;

    // @var string The SierraTecnologia client_id to be used for Connect requests.
    /**
     * @var null|string
     */
    public static $clientId;

    // @var string The base URL for the SierraTecnologia API.
    public static string $apiBase = 'https://payment.sierratecnologia.com.br';

    // @var string The base URL for the OAuth API.
    public static string $connectBase = 'https://connect.sierratecnologia.com.br';

    // @var string The base URL for the SierraTecnologia API uploads endpoint.
    public static string $apiUploadBase = 'https://files.sierratecnologia.com.br';

    // @var string|null The version of the SierraTecnologia API to use for requests.
    /**
     * @var null|string
     */
    public static $apiVersion = null;

    // @var string|null The account ID for connected accounts requests.
    /**
     * @var null|string
     */
    public static $accountId = null;

    // @var string Path to the CA bundle used to verify SSL certificates
    /**
     * @var null|string
     */
    public static $caBundlePath = null;

    // @var boolean Defaults to true.
    /**
     * @var bool
     */
    public static $verifySslCerts = true;

    // @var array The application's information (name, version, URL)
    public static $appInfo = null;

    // @var Util\LoggerInterface|null The logger to which the library will
    //   produce messages.
    /**
     * @var Util\LoggerInterface|null
     */
    public static $logger = null;

    // @var int Maximum number of request retries
    /**
     * @var int
     */
    public static $maxNetworkRetries = 0;

    // @var boolean Whether client telemetry is enabled. Defaults to false.
    /**
     * @var bool
     */
    public static $enableTelemetry = false;

    // @var float Maximum delay between retries, in seconds
    private static float $maxNetworkRetryDelay = 2.0;

    // @var float Initial delay between retries, in seconds
    private static float $initialNetworkRetryDelay = 0.5;

    const VERSION = '6.35.0';

    public static function setApiKey($apiKey)
    {
        return self::$apiKey = $apiKey;
    }

    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * @return string The client_id used for Connect requests.
     */
    public static function getClientId()
    {
        return self::$clientId;
    }

    /**
     * @return Util\LoggerInterface The logger to which the library will
     *   produce messages.
     */
    public static function getLogger()
    {
        if (self::$logger == null) {
            return new Util\DefaultLogger();
        }
        return self::$logger;
    }

    /**
     * @return string
     */
    private static function getDefaultCABundlePath()
    {
        return realpath(dirname(__FILE__) . '/../data/ca-certificates.crt');
    }

    /**
     * @return string
     */
    public static function getCABundlePath()
    {
        return self::$caBundlePath ?: self::getDefaultCABundlePath();
    }

    /**
     * @return boolean
     */
    public static function getVerifySslCerts()
    {
        return self::$verifySslCerts;
    }

    /**
     * @return array | null The application's information
     */
    public static function getAppInfo()
    {
        return self::$appInfo;
    }

    /**
     * @return int Maximum number of request retries
     */
    public static function getMaxNetworkRetries()
    {
        return self::$maxNetworkRetries;
    }

    /**
     * @return float Maximum delay between retries, in seconds
     */
    public static function getMaxNetworkRetryDelay()
    {
        return self::$maxNetworkRetryDelay;
    }

    /**
     * @return float Initial delay between retries, in seconds
     */
    public static function getInitialNetworkRetryDelay()
    {
        return self::$initialNetworkRetryDelay;
    }
}
