<?php

namespace SierraTecnologia\Util;

use SierraTecnologia\SierraTecnologiaObject;

abstract class Util
{
    private static $isMbstringAvailable = null;
    private static $isHashEqualsAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     * @return boolean true if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }
        if ($array === []) {
            return true;
        }
        if (array_keys($array) !== range(0, count($array) - 1)) {
            return false;
        }
        return true;
    }

    /**
     * Recursively converts the PHP SierraTecnologia object to an array.
     *
     * @param array $values The PHP SierraTecnologia object to convert.
     * @return array
     */
    public static function convertSierraTecnologiaObjectToArray($values)
    {
        $results = [];
        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if ($k[0] == '_') {
                continue;
            }
            if ($v instanceof SierraTecnologiaObject) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertSierraTecnologiaObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }
        return $results;
    }

    /**
     * Converts a response from the SierraTecnologia API to the corresponding PHP object.
     *
     * @param array $resp The response from the SierraTecnologia API.
     * @param array $opts
     * @return SierraTecnologiaObject|array
     */
    public static function convertToSierraTecnologiaObject($resp, $opts)
    {
        $types = [
            // data structures
            \SierraTecnologia\Collection::OBJECT_NAME => 'SierraTecnologia\\Collection',

            // business objects
            \SierraTecnologia\Account::OBJECT_NAME => 'SierraTecnologia\\Account',
            \SierraTecnologia\AccountLink::OBJECT_NAME => 'SierraTecnologia\\AccountLink',
            \SierraTecnologia\AlipayAccount::OBJECT_NAME => 'SierraTecnologia\\AlipayAccount',
            \SierraTecnologia\ApplePayDomain::OBJECT_NAME => 'SierraTecnologia\\ApplePayDomain',
            \SierraTecnologia\ApplicationFee::OBJECT_NAME => 'SierraTecnologia\\ApplicationFee',
            \SierraTecnologia\Balance::OBJECT_NAME => 'SierraTecnologia\\Balance',
            \SierraTecnologia\BalanceTransaction::OBJECT_NAME => 'SierraTecnologia\\BalanceTransaction',
            \SierraTecnologia\BankAccount::OBJECT_NAME => 'SierraTecnologia\\BankAccount',
            \SierraTecnologia\BitcoinReceiver::OBJECT_NAME => 'SierraTecnologia\\BitcoinReceiver',
            \SierraTecnologia\BitcoinTransaction::OBJECT_NAME => 'SierraTecnologia\\BitcoinTransaction',
            \SierraTecnologia\Capability::OBJECT_NAME => 'SierraTecnologia\\Capability',
            \SierraTecnologia\Card::OBJECT_NAME => 'SierraTecnologia\\Card',
            \SierraTecnologia\Charge::OBJECT_NAME => 'SierraTecnologia\\Charge',
            \SierraTecnologia\Checkout\Session::OBJECT_NAME => 'SierraTecnologia\\Checkout\\Session',
            \SierraTecnologia\CountrySpec::OBJECT_NAME => 'SierraTecnologia\\CountrySpec',
            \SierraTecnologia\Coupon::OBJECT_NAME => 'SierraTecnologia\\Coupon',
            \SierraTecnologia\CreditNote::OBJECT_NAME => 'SierraTecnologia\\CreditNote',
            \SierraTecnologia\Customer::OBJECT_NAME => 'SierraTecnologia\\Customer',
            \SierraTecnologia\Discount::OBJECT_NAME => 'SierraTecnologia\\Discount',
            \SierraTecnologia\Dispute::OBJECT_NAME => 'SierraTecnologia\\Dispute',
            \SierraTecnologia\EphemeralKey::OBJECT_NAME => 'SierraTecnologia\\EphemeralKey',
            \SierraTecnologia\Event::OBJECT_NAME => 'SierraTecnologia\\Event',
            \SierraTecnologia\ExchangeRate::OBJECT_NAME => 'SierraTecnologia\\ExchangeRate',
            \SierraTecnologia\ApplicationFeeRefund::OBJECT_NAME => 'SierraTecnologia\\ApplicationFeeRefund',
            \SierraTecnologia\File::OBJECT_NAME => 'SierraTecnologia\\File',
            \SierraTecnologia\File::OBJECT_NAME_ALT => 'SierraTecnologia\\File',
            \SierraTecnologia\FileLink::OBJECT_NAME => 'SierraTecnologia\\FileLink',
            \SierraTecnologia\Invoice::OBJECT_NAME => 'SierraTecnologia\\Invoice',
            \SierraTecnologia\InvoiceItem::OBJECT_NAME => 'SierraTecnologia\\InvoiceItem',
            \SierraTecnologia\InvoiceLineItem::OBJECT_NAME => 'SierraTecnologia\\InvoiceLineItem',
            \SierraTecnologia\IssuerFraudRecord::OBJECT_NAME => 'SierraTecnologia\\IssuerFraudRecord',
            \SierraTecnologia\Issuing\Authorization::OBJECT_NAME => 'SierraTecnologia\\Issuing\\Authorization',
            \SierraTecnologia\Issuing\Card::OBJECT_NAME => 'SierraTecnologia\\Issuing\\Card',
            \SierraTecnologia\Issuing\CardDetails::OBJECT_NAME => 'SierraTecnologia\\Issuing\\CardDetails',
            \SierraTecnologia\Issuing\Cardholder::OBJECT_NAME => 'SierraTecnologia\\Issuing\\Cardholder',
            \SierraTecnologia\Issuing\Dispute::OBJECT_NAME => 'SierraTecnologia\\Issuing\\Dispute',
            \SierraTecnologia\Issuing\Transaction::OBJECT_NAME => 'SierraTecnologia\\Issuing\\Transaction',
            \SierraTecnologia\LoginLink::OBJECT_NAME => 'SierraTecnologia\\LoginLink',
            \SierraTecnologia\Order::OBJECT_NAME => 'SierraTecnologia\\Order',
            \SierraTecnologia\OrderItem::OBJECT_NAME => 'SierraTecnologia\\OrderItem',
            \SierraTecnologia\OrderReturn::OBJECT_NAME => 'SierraTecnologia\\OrderReturn',
            \SierraTecnologia\PaymentIntent::OBJECT_NAME => 'SierraTecnologia\\PaymentIntent',
            \SierraTecnologia\PaymentMethod::OBJECT_NAME => 'SierraTecnologia\\PaymentMethod',
            \SierraTecnologia\Payout::OBJECT_NAME => 'SierraTecnologia\\Payout',
            \SierraTecnologia\Person::OBJECT_NAME => 'SierraTecnologia\\Person',
            \SierraTecnologia\Plan::OBJECT_NAME => 'SierraTecnologia\\Plan',
            \SierraTecnologia\Product::OBJECT_NAME => 'SierraTecnologia\\Product',
            \SierraTecnologia\Radar\ValueList::OBJECT_NAME => 'SierraTecnologia\\Radar\\ValueList',
            \SierraTecnologia\Radar\ValueListItem::OBJECT_NAME => 'SierraTecnologia\\Radar\\ValueListItem',
            \SierraTecnologia\Recipient::OBJECT_NAME => 'SierraTecnologia\\Recipient',
            \SierraTecnologia\RecipientTransfer::OBJECT_NAME => 'SierraTecnologia\\RecipientTransfer',
            \SierraTecnologia\Refund::OBJECT_NAME => 'SierraTecnologia\\Refund',
            \SierraTecnologia\Reporting\ReportRun::OBJECT_NAME => 'SierraTecnologia\\Reporting\\ReportRun',
            \SierraTecnologia\Reporting\ReportType::OBJECT_NAME => 'SierraTecnologia\\Reporting\\ReportType',
            \SierraTecnologia\Review::OBJECT_NAME => 'SierraTecnologia\\Review',
            \SierraTecnologia\SKU::OBJECT_NAME => 'SierraTecnologia\\SKU',
            \SierraTecnologia\Sigma\ScheduledQueryRun::OBJECT_NAME => 'SierraTecnologia\\Sigma\\ScheduledQueryRun',
            \SierraTecnologia\Source::OBJECT_NAME => 'SierraTecnologia\\Source',
            \SierraTecnologia\SourceTransaction::OBJECT_NAME => 'SierraTecnologia\\SourceTransaction',
            \SierraTecnologia\Subscription::OBJECT_NAME => 'SierraTecnologia\\Subscription',
            \SierraTecnologia\SubscriptionItem::OBJECT_NAME => 'SierraTecnologia\\SubscriptionItem',
            \SierraTecnologia\SubscriptionSchedule::OBJECT_NAME => 'SierraTecnologia\\SubscriptionSchedule',
            \SierraTecnologia\SubscriptionScheduleRevision::OBJECT_NAME => 'SierraTecnologia\\SubscriptionScheduleRevision',
            \SierraTecnologia\TaxId::OBJECT_NAME => 'SierraTecnologia\\TaxId',
            \SierraTecnologia\TaxRate::OBJECT_NAME => 'SierraTecnologia\\TaxRate',
            \SierraTecnologia\ThreeDSecure::OBJECT_NAME => 'SierraTecnologia\\ThreeDSecure',
            \SierraTecnologia\Terminal\ConnectionToken::OBJECT_NAME => 'SierraTecnologia\\Terminal\\ConnectionToken',
            \SierraTecnologia\Terminal\Location::OBJECT_NAME => 'SierraTecnologia\\Terminal\\Location',
            \SierraTecnologia\Terminal\Reader::OBJECT_NAME => 'SierraTecnologia\\Terminal\\Reader',
            \SierraTecnologia\Token::OBJECT_NAME => 'SierraTecnologia\\Token',
            \SierraTecnologia\Topup::OBJECT_NAME => 'SierraTecnologia\\Topup',
            \SierraTecnologia\Transfer::OBJECT_NAME => 'SierraTecnologia\\Transfer',
            \SierraTecnologia\TransferReversal::OBJECT_NAME => 'SierraTecnologia\\TransferReversal',
            \SierraTecnologia\UsageRecord::OBJECT_NAME => 'SierraTecnologia\\UsageRecord',
            \SierraTecnologia\UsageRecordSummary::OBJECT_NAME => 'SierraTecnologia\\UsageRecordSummary',
            \SierraTecnologia\WebhookEndpoint::OBJECT_NAME => 'SierraTecnologia\\WebhookEndpoint',
        ];
        if (self::isList($resp)) {
            $mapped = [];
            foreach ($resp as $i) {
                array_push($mapped, self::convertToSierraTecnologiaObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = 'SierraTecnologia\\SierraTecnologiaObject';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (self::$isMbstringAvailable === null) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                trigger_error("It looks like the mbstring extension is not enabled. " .
                    "UTF-8 strings will not properly be encoded. Ask your system " .
                    "administrator to enable the mbstring extension, or write to " .
                    "support@sierratecnologia.com.br if you have any questions.", E_USER_WARNING);
            }
        }

        if (is_string($value) && self::$isMbstringAvailable && mb_detect_encoding($value, "UTF-8", true) != "UTF-8") {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }

    /**
     * Compares two strings for equality. The time taken is independent of the
     * number of characters that match.
     *
     * @param string $a one of the strings to compare.
     * @param string $b the other string to compare.
     * @return bool true if the strings are equal, false otherwise.
     */
    public static function secureCompare($a, $b)
    {
        if (self::$isHashEqualsAvailable === null) {
            self::$isHashEqualsAvailable = function_exists('hash_equals');
        }

        if (self::$isHashEqualsAvailable) {
            return hash_equals($a, $b);
        } else {
            if (strlen($a) != strlen($b)) {
                return false;
            }

            $result = 0;
            for ($i = 0; $i < strlen($a); $i++) {
                $result |= ord($a[$i]) ^ ord($b[$i]);
            }
            return ($result == 0);
        }
    }

    /**
     * Recursively goes through an array of parameters. If a parameter is an instance of
     * ApiResource, then it is replaced by the resource's ID.
     * Also clears out null values.
     *
     * @param mixed $h
     * @return mixed
     */
    public static function objectsToIds($h)
    {
        if ($h instanceof \SierraTecnologia\ApiResource) {
            return $h->id;
        } elseif (static::isList($h)) {
            $results = [];
            foreach ($h as $v) {
                array_push($results, static::objectsToIds($v));
            }
            return $results;
        } elseif (is_array($h)) {
            $results = [];
            foreach ($h as $k => $v) {
                if (is_null($v)) {
                    continue;
                }
                $results[$k] = static::objectsToIds($v);
            }
            return $results;
        } else {
            return $h;
        }
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function encodeParameters($params)
    {
        $flattenedParams = self::flattenParams($params);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            list($k, $v) = $param;
            array_push($pieces, self::urlEncode($k) . '=' . self::urlEncode($v));
        }
        return implode('&', $pieces);
    }

    /**
     * @param array $params
     * @param string|null $parentKey
     *
     * @return array
     */
    public static function flattenParams($params, $parentKey = null)
    {
        $result = [];

        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? "{$parentKey}[{$key}]" : $key;

            if (self::isList($value)) {
                $result = array_merge($result, self::flattenParamsList($value, $calculatedKey));
            } elseif (is_array($value)) {
                $result = array_merge($result, self::flattenParams($value, $calculatedKey));
            } else {
                array_push($result, [$calculatedKey, $value]);
            }
        }

        return $result;
    }

    /**
     * @param array $value
     * @param string $calculatedKey
     *
     * @return array
     */
    public static function flattenParamsList($value, $calculatedKey)
    {
        $result = [];

        foreach ($value as $i => $elem) {
            if (self::isList($elem)) {
                $result = array_merge($result, self::flattenParamsList($elem, $calculatedKey));
            } elseif (is_array($elem)) {
                $result = array_merge($result, self::flattenParams($elem, "{$calculatedKey}[{$i}]"));
            } else {
                array_push($result, ["{$calculatedKey}[{$i}]", $elem]);
            }
        }

        return $result;
    }

    /**
     * @param string $key A string to URL-encode.
     *
     * @return string The URL-encoded string.
     */
    public static function urlEncode($key)
    {
        $s = urlencode($key);

        // Don't use strict form encoding by changing the square bracket control
        // characters back to their literals. This is fine by the server, and
        // makes these parameter strings easier to read.
        $s = str_replace('%5B', '[', $s);
        $s = str_replace('%5D', ']', $s);

        return $s;
    }

    public static function normalizeId($id)
    {
        if (is_array($id)) {
            $params = $id;
            $id = $params['id'];
            unset($params['id']);
        } else {
            $params = [];
        }
        return [$id, $params];
    }

    /**
     * Returns UNIX timestamp in milliseconds
     *
     * @return integer current time in millis
     */
    public static function currentTimeMillis()
    {
        return (int) round(microtime(true) * 1000);
    }
}
