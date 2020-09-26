<?php

namespace SierraTecnologia;

/**
 * Class Customer
 *
 * @property string $id
 * @property string $object
 * @property int $account_balance
 * @property mixed $address
 * @property string $created
 * @property string $currency
 * @property string $default_source
 * @property bool $delinquent
 * @property string $description
 * @property Discount $discount
 * @property string $email
 * @property string $invoice_prefix
 * @property mixed $invoice_settings
 * @property bool $livemode
 * @property SierraTecnologiaObject $metadata
 * @property string $name
 * @property string $phone
 * @property string[] preferred_locales
 * @property mixed $shipping
 * @property Collection $sources
 * @property Collection $subscriptions
 * @property Collection $tax_ids
 *
 * @package SierraTecnologia
 */
class Customer extends ApiResource
{

    const OBJECT_NAME = "customer";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\NestedResource;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * Possible string representations of the customer's type of tax exemption.
     *
     * @link https://sierratecnologia.com.br/docs/api/customers/object#customer_object-tax_exempt
     */
    const TAX_EXEMPT_NONE    = 'none';
    const TAX_EXEMPT_EXEMPT  = 'exempt';
    const TAX_EXEMPT_REVERSE = 'reverse';

    public static function getSavedNestedResources()
    {
        static $savedNestedResources = null;
        if ($savedNestedResources === null) {
            $savedNestedResources = new Util\Set(
                [
                'source',
                ]
            );
        }
        return $savedNestedResources;
    }

    const PATH_SOURCES = '/sources';
    const PATH_TAX_IDS = '/tax_ids';
}
