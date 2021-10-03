<?php

namespace SierraTecnologia\Util;

use SierraTecnologia\Error;

class RequestOptions
{
    /**
     * @var array A list of headers that should be persisted across requests.
     */
    public static $HEADERS_TO_PERSIST = [
        'SierraTecnologia-Account',
        'SierraTecnologia-Version',
    ];

    public $headers;
    public $apiKey;
    public $apiBase;

    public function __construct($key = null, $headers = [], $base = null)
    {
        $this->apiKey = $key;
        $this->headers = $headers;
        $this->apiBase = $base;
    }

    /**
     * Unpacks an options array and merges it into the existing RequestOptions
     * object.
     *
     * @param array|string|null $options a key => value array
     *
     * @return RequestOptions
     */
    public function merge($options)
    {
        $other_options = self::parse($options);
        if ($other_options->apiKey === null) {
            $other_options->apiKey = $this->apiKey;
        }
        if ($other_options->apiBase === null) {
            $other_options->apiBase = $this->apiBase;
        }
        $other_options->headers = array_merge($this->headers, $other_options->headers);
        return $other_options;
    }

    /**
     * Discards all headers that we don't want to persist across requests.
     *
     * @return void
     */
    public function discardNonPersistentHeaders()
    {
        foreach ($this->headers as $k => $v) {
            if (!in_array($k, self::$HEADERS_TO_PERSIST)) {
                unset($this->headers[$k]);
            }
        }
    }

    /**
     * Unpacks an options array into an RequestOptions object
     *
     * @param array|string|null $options a key => value array
     *
     * @return RequestOptions
     */
    public static function parse($options)
    {
        if ($options instanceof self) {
            return $options;
        }

        if (is_null($options)) {
            return new RequestOptions(null, [], null);
        }

        if (is_string($options)) {
            return new RequestOptions($options, [], null);
        }

        if (is_array($options)) {
            $headers = [];
            $key = null;
            $base = null;
            if (isset($options['api_key'])) {
                $key = $options['api_key'];
            }
            if (isset($options['idempotency_key'])) {
                $headers['Idempotency-Key'] = $options['idempotency_key'];
            }
            if (isset($options['sitecpayment_account'])) {
                $headers['SierraTecnologia-Account'] = $options['sitecpayment_account'];
            }
            if (isset($options['sitecpayment_version'])) {
                $headers['SierraTecnologia-Version'] = $options['sitecpayment_version'];
            }
            if (isset($options['api_base'])) {
                $base = $options['api_base'];
            }
            return new RequestOptions($key, $headers, $base);
        }

        $message = 'The second argument to SierraTecnologia API method calls is an '
           . 'optional per-request apiKey, which must be a string, or '
           . 'per-request options, which must be an array. (HINT: you can set '
           . 'a global apiKey by "SierraTecnologia::setApiKey(<apiKey>)")';
        throw new Error\Api($message);
    }
}
