<?php

namespace SierraTecnologia;

/**
 * Class BankAccount
 *
 * @property string $id
 * @property string $object
 * @property string $account
 * @property string $account_holder_name
 * @property string $account_holder_type
 * @property string $bank_name
 * @property string $country
 * @property string $currency
 * @property string $customer
 * @property bool $default_for_currency
 * @property string $fingerprint
 * @property string $last4
 * @property SierraTecnologiaObject $metadata
 * @property string $routing_number
 * @property string $status
 *
 * @package SierraTecnologia
 */
class BankAccount extends ApiResource
{

    const OBJECT_NAME = "bank_account";

    use ApiOperations\Delete;
    use ApiOperations\Update;

    /**
     * @return string The instance URL for this resource. It needs to be special
     *    cased because it doesn't fit into the standard resource pattern.
     */
    public function instanceUrl()
    {
        if ($this['customer']) {
            $base = Customer::classUrl();
            $parent = $this['customer'];
            $path = 'sources';
        } elseif ($this['account']) {
            $base = Account::classUrl();
            $parent = $this['account'];
            $path = 'external_accounts';
        } else {
            $msg = "Bank accounts cannot be accessed without a customer ID or account ID.";
            throw new Error\InvalidRequest($msg, null);
        }
        $parentExtn = urlencode(Util\Util::utf8($parent));
        $extn = urlencode(Util\Util::utf8($this['id']));
        return "$base/$parentExtn/$path/$extn";
    }
}
