<?php

namespace SierraTecnologia;

/**
 * Class TaxId
 *
 * @package SierraTecnologia
 *
 * @property string $id
 * @property string $object
 * @property string $country
 * @property int $created
 * @property string $customer
 * @property bool $deleted
 * @property bool $livemode
 * @property string $type
 * @property string $value
 * @property mixed $verification
 */
class TaxId extends ApiResource
{

    const OBJECT_NAME = "tax_id";

    use ApiOperations\Delete;

    /**
     * Possible string representations of a tax id's type.
     * @link https://sierratecnologia.com.br/docs/api/customers/tax_id_object#tax_id_object-type
     */
    const TYPE_AU_ABN  = 'au_abn';
    const TYPE_EU_VAT  = 'eu_vat';
    const TYPE_NZ_GST  = 'nz_gst';
    const TYPE_UNKNOWN = 'unknown';

    /**
     * Possible string representations of the verification status.
     * @link https://sierratecnologia.com.br/docs/api/customer_tax_ids/object#tax_id_object-verification
     */
    const VERIFICATION_STATUS_PENDING     = 'pending';
    const VERIFICATION_STATUS_UNAVAILABLE = 'unavailable';
    const VERIFICATION_STATUS_UNVERIFIED  = 'unverified';
    const VERIFICATION_STATUS_VERIFIED    = 'verified';

    /**
     * @return string The API URL for this tax id.
     */
    public function instanceUrl()
    {
        $id = $this['id'];
        $customer = $this['customer'];
        if (!$id) {
            throw new Error\InvalidRequest(
                "Could not determine which URL to request: class instance has invalid ID: $id",
                null
            );
        }
        $id = Util\Util::utf8($id);
        $customer = Util\Util::utf8($customer);

        $base = Customer::classUrl();
        $customerExtn = urlencode($customer);
        $extn = urlencode($id);
        return "$base/$customerExtn/tax_ids/$extn";
    }

    /**
     * @param array|string $_id
     * @param array|string|null $_opts
     *
     * @throws \SierraTecnologia\Error\InvalidRequest
     */
    public static function retrieve($_id, $_opts = null)
    {
        $msg = "Tax Ids cannot be accessed without a customer ID. " .
               "Retrieve a Tax Id using Customer::retrieveTaxId('tax_id') instead.";
        throw new Error\InvalidRequest($msg, null);
    }
}
