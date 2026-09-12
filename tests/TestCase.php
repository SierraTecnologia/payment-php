<?php

namespace SierraTecnologia;

use PHPUnit\Framework\TestCase as BaseTestCase;

class OfflineMockClient implements \SierraTecnologia\HttpClient\ClientInterface
{
    private static $instance = null;

    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function request($method, $absUrl, $headers, $params, $hasFile)
    {
        return TestCase::buildMockResponse($method, $absUrl, $params);
    }
}

/**
 * Base class for SierraTecnologia test cases.
 */
class TestCase extends BaseTestCase
{
    /**
     * @var string original API base URL 
     */
    protected $origApiBase;

    /**
     * @var string original API key 
     */
    protected $origApiKey;

    /**
     * @var string original client ID 
     */
    protected $origClientId;

    /**
     * @var string original API version 
     */
    protected $origApiVersion;

    /**
     * @var string original account ID 
     */
    protected $origAccountId;

    /**
     * @var object HTTP client mocker 
     */
    protected $clientMock;

    protected function setUp(): void
    {
        // Save original values so that we can restore them after running tests
        $this->origApiBase = SierraTecnologia::$apiBase;
        $this->origApiKey = SierraTecnologia::getApiKey();
        $this->origClientId = SierraTecnologia::getClientId();
        $this->origApiVersion = SierraTecnologia::getApiVersion();
        $this->origAccountId = SierraTecnologia::getAccountId();

        // Set up host and credentials for sierratecnologia-mock
        SierraTecnologia::$apiBase = MOCK_URL;
        SierraTecnologia::setApiKey("sk_test_123");
        SierraTecnologia::setClientId("ca_123");
        SierraTecnologia::setApiVersion(null);
        SierraTecnologia::setAccountId(null);

        // Set up the HTTP client mocker
        $this->clientMock = $this->createMock('\SierraTecnologia\HttpClient\ClientInterface');

        // By default, use offline mock client when offline
        if (defined('MOCK_OFFLINE') && MOCK_OFFLINE) {
            ApiRequestor::setHttpClient(OfflineMockClient::instance());
        } else {
            ApiRequestor::setHttpClient(HttpClient\CurlClient::instance());
        }
    }

    protected function tearDown(): void
    {
        // Restore original values
        SierraTecnologia::$apiBase = $this->origApiBase;
        SierraTecnologia::setEnableTelemetry(false);
        SierraTecnologia::setApiKey($this->origApiKey);
        SierraTecnologia::setClientId($this->origClientId);
        SierraTecnologia::setApiVersion($this->origApiVersion);
        SierraTecnologia::setAccountId($this->origAccountId);

        ApiRequestor::setHttpClient(null);
    }

    /**
     * Sets up a request expectation with the provided parameters.
     */
    protected function expectsRequest(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $base = null
    ) {
        if (defined('MOCK_OFFLINE') && MOCK_OFFLINE) {
            $this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
                ->willReturnCallback(
                    function ($method, $absUrl, $headers, $params, $hasFile) use ($path) {
                        ApiRequestor::setHttpClient(OfflineMockClient::instance());
                        return self::buildMockResponse($method, $path, $params);
                    }
                );
            return;
        }

        $this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
            ->willReturnCallback(
                function ($method, $absUrl, $headers, $params, $hasFile) {
                    $curlClient = HttpClient\CurlClient::instance();
                    ApiRequestor::setHttpClient($curlClient);
                    return $curlClient->request($method, $absUrl, $headers, $params, $hasFile);
                }
            );
    }

    /**
     * Sets up a request expectation with the provided parameters. The request
     * will not actually be emitted, instead the provided response parameters
     * will be returned.
     */
    protected function stubRequest(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $response = [],
        $rcode = 200,
        $base = null
    ) {
        $this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
            ->willReturn([json_encode($response), $rcode, []]);
    }

    private function prepareRequestMock(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $base = null
    ) {
        ApiRequestor::setHttpClient($this->clientMock);

        if ($base === null) {
            $base = SierraTecnologia::$apiBase;
        }
        $absUrl = $base . $path;

        return $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->identicalTo(strtolower($method)),
                $this->identicalTo($absUrl),
                $headers === null ? $this->anything() : $this->callback(
                    function ($array) use ($headers) {
                        foreach ($headers as $header) {
                            if (!in_array($header, $array)) {
                                return false;
                            }
                        }
                        return true;
                    }
                ),
                $params === null ? $this->anything() : $this->identicalTo($params),
                $this->identicalTo($hasFile)
            );
    }

    public static function buildMockResponse($method, $path, $params = null)
    {
        $cleanPath = trim(parse_url($path, PHP_URL_PATH), '/');
        // remove leading 'v1/'
        if (str_starts_with($cleanPath, 'v1/')) {
            $cleanPath = substr($cleanPath, 3);
        }

        $segments = explode('/', $cleanPath);
        $lastSegment = end($segments);
        $method = strtolower($method);

        // Determine object type and if it's a list
        $objectType = 'custom';
        $isList = false;

        // Specific subresources first
        if (str_contains($cleanPath, 'external_accounts')) {
            $objectType = 'bank_account';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'external_accounts'));
        } elseif (str_contains($cleanPath, 'source_transactions')) {
            $objectType = 'source_transaction';
            $isList = true;
        } elseif (str_ends_with($cleanPath, '/details')) {
            $objectType = 'issuing.card_details';
        } elseif (str_contains($cleanPath, 'revisions')) {
            $objectType = 'subscription_schedule_revision';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'revisions'));
        } elseif (str_contains($cleanPath, '3d_secure') || str_contains($cleanPath, 'three_d_secure')) {
            $objectType = 'three_d_secure';
        } elseif (str_starts_with($cleanPath, 'issuing/cards')) {
            $objectType = 'issuing.card';
            $isList = ($method === 'get' && $cleanPath === 'issuing/cards');
        } elseif (str_starts_with($cleanPath, 'issuing/disputes')) {
            $objectType = 'issuing.dispute';
            $isList = ($method === 'get' && $cleanPath === 'issuing/disputes');
        } elseif (str_starts_with($cleanPath, 'issuing/authorizations')) {
            $objectType = 'issuing.authorization';
            $isList = ($method === 'get' && $cleanPath === 'issuing/authorizations');
        } elseif (str_starts_with($cleanPath, 'issuing/cardholders')) {
            $objectType = 'issuing.cardholder';
            $isList = ($method === 'get' && $cleanPath === 'issuing/cardholders');
        } elseif (str_starts_with($cleanPath, 'issuing/transactions')) {
            $objectType = 'issuing.transaction';
            $isList = ($method === 'get' && $cleanPath === 'issuing/transactions');
        } elseif (str_starts_with($cleanPath, 'terminal/locations')) {
            $objectType = 'terminal.location';
            $isList = ($method === 'get' && $cleanPath === 'terminal/locations');
        } elseif (str_starts_with($cleanPath, 'terminal/readers')) {
            $objectType = 'terminal.reader';
            $isList = ($method === 'get' && $cleanPath === 'terminal/readers');
        } elseif (str_starts_with($cleanPath, 'terminal/connection_tokens')) {
            $objectType = 'terminal.connection_token';
        } elseif (str_starts_with($cleanPath, 'reporting/report_runs')) {
            $objectType = 'reporting.report_run';
            $isList = ($method === 'get' && $cleanPath === 'reporting/report_runs');
        } elseif (str_starts_with($cleanPath, 'reporting/report_types')) {
            $objectType = 'reporting.report_type';
            $isList = ($method === 'get' && $cleanPath === 'reporting/report_types');
        } elseif (str_starts_with($cleanPath, 'radar/value_list_items')) {
            $objectType = 'radar.value_list_item';
            $isList = ($method === 'get' && $cleanPath === 'radar/value_list_items');
        } elseif (str_starts_with($cleanPath, 'radar/value_lists')) {
            $objectType = 'radar.value_list';
            $isList = ($method === 'get' && $cleanPath === 'radar/value_lists');
        } elseif (str_starts_with($cleanPath, 'checkout/sessions')) {
            $objectType = 'checkout.session';
            $isList = ($method === 'get' && $cleanPath === 'checkout/sessions');
        } elseif (str_starts_with($cleanPath, 'subscription_items')) {
            if (str_contains($cleanPath, 'usage_records')) {
                $objectType = 'usage_record';
            } elseif (str_contains($cleanPath, 'usage_record_summaries')) {
                $objectType = 'usage_record_summary';
                $isList = true;
            } else {
                $objectType = 'subscription_item';
                $isList = ($method === 'get' && $cleanPath === 'subscription_items');
            }
        } elseif (str_starts_with($cleanPath, 'subscription_schedules')) {
            $objectType = 'subscription_schedule';
            $isList = ($method === 'get' && $cleanPath === 'subscription_schedules');
        } elseif (str_starts_with($cleanPath, 'order_returns') || str_ends_with($cleanPath, '/returns')) {
            $objectType = 'order_return';
            $isList = ($method === 'get' && $cleanPath === 'order_returns');
        } elseif (str_starts_with($cleanPath, 'file_links')) {
            $objectType = 'file_link';
            $isList = ($method === 'get' && $cleanPath === 'file_links');
        } elseif (str_starts_with($cleanPath, 'files')) {
            $objectType = 'file';
            $isList = ($method === 'get' && $cleanPath === 'files');
        } elseif (str_starts_with($cleanPath, 'balance_transactions') || str_starts_with($cleanPath, 'balance/history')) {
            $objectType = 'balance_transaction';
            $isList = ($method === 'get' && ($cleanPath === 'balance_transactions' || $cleanPath === 'balance/history'));
        } elseif ($cleanPath === 'balance') {
            $objectType = 'balance';
        } elseif (str_starts_with($cleanPath, 'apple_pay/domains')) {
            $objectType = 'apple_pay_domain';
            $isList = ($method === 'get' && $cleanPath === 'apple_pay/domains');
        } elseif (str_starts_with($cleanPath, 'bitcoin/receivers')) {
            if (str_contains($cleanPath, 'transactions')) {
                $objectType = 'bitcoin_transaction';
                $isList = true;
            } else {
                $objectType = 'bitcoin_receiver';
                $isList = ($method === 'get' && $cleanPath === 'bitcoin/receivers');
            }
        } elseif (str_starts_with($cleanPath, 'sigma/scheduled_query_runs')) {
            $objectType = 'scheduled_query_run';
            $isList = ($method === 'get' && $cleanPath === 'sigma/scheduled_query_runs');
        } elseif (str_starts_with($cleanPath, 'issuer_fraud_records')) {
            $objectType = 'issuer_fraud_record';
            $isList = ($method === 'get' && $cleanPath === 'issuer_fraud_records');
        } elseif (str_contains($cleanPath, 'dispute')) {
            $objectType = 'dispute';
            $isList = ($method === 'get' && $cleanPath === 'disputes');
        } elseif (str_contains($cleanPath, 'reversals')) {
            $objectType = 'transfer_reversal';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'reversals'));
        } elseif (str_starts_with($cleanPath, 'application_fees')) {
            if (str_contains($cleanPath, 'refunds')) {
                $objectType = 'fee_refund';
                $isList = ($method === 'get' && str_ends_with($cleanPath, 'refunds'));
            } else {
                $objectType = 'application_fee';
                $isList = ($method === 'get' && $cleanPath === 'application_fees');
            }
        } elseif (str_contains($cleanPath, 'refunds')) {
            $objectType = 'refund';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'refunds'));
        } elseif (str_contains($cleanPath, 'tax_ids')) {
            $objectType = 'tax_id';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'tax_ids'));
        } elseif (str_contains($cleanPath, 'tax_rates')) {
            $objectType = 'tax_rate';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'tax_rates'));
        } elseif (str_contains($cleanPath, 'login_links')) {
            $objectType = 'login_link';
        } elseif (str_contains($cleanPath, 'account_links')) {
            $objectType = 'account_link';
        } elseif (str_contains($cleanPath, 'persons')) {
            $objectType = 'person';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'persons'));
        } elseif (str_contains($cleanPath, 'capabilities')) {
            $objectType = 'capability';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'capabilities'));
        } elseif (str_contains($cleanPath, 'invoiceitems')) {
            $objectType = 'invoiceitem';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'invoiceitems'));
        } elseif (str_contains($cleanPath, 'invoices')) {
            $objectType = 'invoice';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'invoices'));
        } elseif (str_contains($cleanPath, 'charges')) {
            $objectType = 'charge';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'charges'));
        } elseif (str_starts_with($cleanPath, 'sources')) {
            $objectType = 'source';
            $isList = ($method === 'get' && $cleanPath === 'sources');
        } elseif (str_contains($cleanPath, 'sources')) {
            $objectType = 'card';
            $isList = ($method === 'get' && str_ends_with($cleanPath, 'sources'));
        } elseif (str_starts_with($cleanPath, 'customers')) {
            $objectType = 'customer';
            $isList = ($method === 'get' && $cleanPath === 'customers');
        } elseif (str_starts_with($cleanPath, 'transfers')) {
            $objectType = 'transfer';
            $isList = ($method === 'get' && $cleanPath === 'transfers');
        } elseif (str_starts_with($cleanPath, 'tokens')) {
            $objectType = 'token';
        } elseif (str_starts_with($cleanPath, 'plans')) {
            $objectType = 'plan';
            $isList = ($method === 'get' && $cleanPath === 'plans');
        } elseif (str_starts_with($cleanPath, 'products')) {
            $objectType = 'product';
            $isList = ($method === 'get' && $cleanPath === 'products');
        } elseif (str_starts_with($cleanPath, 'skus')) {
            $objectType = 'sku';
            $isList = ($method === 'get' && $cleanPath === 'skus');
        } elseif (str_starts_with($cleanPath, 'subscriptions')) {
            $objectType = 'subscription';
            $isList = ($method === 'get' && $cleanPath === 'subscriptions');
        } elseif (str_starts_with($cleanPath, 'orders')) {
            $objectType = 'order';
            $isList = ($method === 'get' && $cleanPath === 'orders');
        } elseif (str_starts_with($cleanPath, 'payouts')) {
            $objectType = 'payout';
            $isList = ($method === 'get' && $cleanPath === 'payouts');
        } elseif (str_starts_with($cleanPath, 'events')) {
            $objectType = 'event';
            $isList = ($method === 'get' && $cleanPath === 'events');
        } elseif (str_starts_with($cleanPath, 'coupons')) {
            $objectType = 'coupon';
            $isList = ($method === 'get' && $cleanPath === 'coupons');
        } elseif (str_starts_with($cleanPath, 'credit_notes')) {
            $objectType = 'credit_note';
            $isList = ($method === 'get' && $cleanPath === 'credit_notes');
        } elseif (str_starts_with($cleanPath, 'accounts')) {
            $objectType = 'account';
            $isList = ($method === 'get' && $cleanPath === 'accounts');
        } elseif (str_starts_with($cleanPath, 'topups')) {
            $objectType = 'topup';
            $isList = ($method === 'get' && $cleanPath === 'topups');
        } elseif (str_starts_with($cleanPath, 'webhook_endpoints')) {
            $objectType = 'webhook_endpoint';
            $isList = ($method === 'get' && $cleanPath === 'webhook_endpoints');
        } elseif (str_starts_with($cleanPath, 'exchange_rates')) {
            $objectType = 'exchange_rate';
            $isList = ($method === 'get' && $cleanPath === 'exchange_rates');
        } elseif (str_starts_with($cleanPath, 'country_specs')) {
            $objectType = 'country_spec';
            $isList = ($method === 'get' && $cleanPath === 'country_specs');
        } elseif (str_starts_with($cleanPath, 'payment_methods')) {
            $objectType = 'payment_method';
            $isList = ($method === 'get' && $cleanPath === 'payment_methods');
        } elseif (str_starts_with($cleanPath, 'payment_intents')) {
            $objectType = 'payment_intent';
            $isList = ($method === 'get' && $cleanPath === 'payment_intents');
        } elseif (str_starts_with($cleanPath, 'recipients')) {
            if (str_contains($cleanPath, 'transfers')) {
                $objectType = 'recipient_transfer';
                $isList = true;
            } else {
                $objectType = 'recipient';
                $isList = ($method === 'get' && $cleanPath === 'recipients');
            }
        } elseif (str_starts_with($cleanPath, 'reviews')) {
            $objectType = 'review';
            $isList = ($method === 'get' && $cleanPath === 'reviews');
        } elseif (str_starts_with($cleanPath, 'alipay_accounts')) {
            $objectType = 'alipay_account';
            $isList = ($method === 'get' && $cleanPath === 'alipay_accounts');
        } elseif (str_starts_with($cleanPath, 'bank_accounts') || str_starts_with($cleanPath, 'cards')) {
            $objectType = str_starts_with($cleanPath, 'cards') ? 'card' : 'bank_account';
            $isList = ($method === 'get' && in_array($cleanPath, ['bank_accounts', 'cards']));
        } elseif (str_starts_with($cleanPath, 'ephemeral_keys')) {
            $objectType = 'ephemeral_key';
        }

        $singleItem = [
            'id' => is_string($lastSegment) && !in_array($lastSegment, ['close', 'refunds', 'reversals', 'sources', 'tax_ids', 'persons', 'capabilities', 'invoices', 'charges', 'invoiceitems', 'returns', 'revisions', 'external_accounts', 'details', 'source_transactions']) ? $lastSegment : 'mock_123',
            'object' => $objectType,
            'deleted' => ($method === 'delete'),
            'metadata' => ['key' => 'value'],
            'subscription' => ['id' => 'sub_123', 'object' => 'subscription'],
            'sources' => ['object' => 'list', 'data' => [['id' => 'src_123', 'object' => 'card']], 'has_more' => false],
            'default_source' => 'src_123',
            'customer' => ($objectType === 'source') ? null : 'cus_123',
            'currency' => 'usd',
            'amount' => 1000,
            'status' => 'succeeded',
            'type' => 'custom',
            'dispute' => ['id' => 'dp_123', 'object' => 'dispute'],
            'refunds' => ['object' => 'list', 'data' => [['id' => 're_123', 'object' => 'refund']], 'has_more' => false, 'url' => '/v1/' . $cleanPath . '/refunds'],
            'url' => 'https://example.com',
            'rates' => ['usd' => 1.0, 'eur' => 0.9],
            'enabled_events' => ['charge.succeeded'],
            'requirements' => ['eventually_due' => []],
            'account' => 'acct_123',
            'schedule' => 'sub_sched_123',
        ];

        if ($isList) {
            $payload = [
                'object' => 'list',
                'data' => [$singleItem],
                'has_more' => false,
                'url' => '/' . $cleanPath,
            ];
        } else {
            $payload = $singleItem;
        }

        return [json_encode($payload), 200, new Util\CaseInsensitiveArray(['request-id' => 'req_mock_123'])];
    }
}
