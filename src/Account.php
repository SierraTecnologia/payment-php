<?php

namespace SierraTecnologia;

/**
 * Class Account
 *
 * @property string $id
 * @property string $object
 * @property mixed $business_profile
 * @property string $business_type
 * @property mixed $capabilities
 * @property bool $charges_enabled
 * @property mixed $company
 * @property string $country
 * @property int $created
 * @property string $default_currency
 * @property bool $details_submitted
 * @property string $email
 * @property Collection $external_accounts
 * @property mixed $individual
 * @property SierraTecnologiaObject $metadata
 * @property bool $payouts_enabled
 * @property mixed $requirements
 * @property mixed $settings
 * @property mixed $tos_acceptance
 * @property string $type
 *
 * @package SierraTecnologia
 */
class Account extends ApiResource
{
    const OBJECT_NAME = "account";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\NestedResource;
    use ApiOperations\Retrieve {
        retrieve as protected _retrieve;
    }
    use ApiOperations\Update;

    /**
     * Possible string representations of an account's business type.
     *
     * @link https://sierratecnologia.com.br/docs/api/accounts/object#account_object-business_type
     */
    const BUSINESS_TYPE_COMPANY    = 'company';
    const BUSINESS_TYPE_INDIVIDUAL = 'individual';

    /**
     * Possible string representations of an account's capabilities.
     *
     * @link https://sierratecnologia.com.br/docs/api/accounts/object#account_object-capabilities
     */
    const CAPABILITY_CARD_PAYMENTS     = 'card_payments';
    const CAPABILITY_LEGACY_PAYMENTS   = 'legacy_payments';
    const CAPABILITY_PLATFORM_PAYMENTS = 'platform_payments';

    /**
     * Possible string representations of an account's capability status.
     *
     * @link https://sierratecnologia.com.br/docs/api/accounts/object#account_object-capabilities
     */
    const CAPABILITY_STATUS_ACTIVE   = 'active';
    const CAPABILITY_STATUS_INACTIVE = 'inactive';
    const CAPABILITY_STATUS_PENDING  = 'pending';

    /**
     * Possible string representations of an account's type.
     *
     * @link https://sierratecnologia.com.br/docs/api/accounts/object#account_object-type
     */
    const TYPE_CUSTOM   = 'custom';
    const TYPE_EXPRESS  = 'express';
    const TYPE_STANDARD = 'standard';

    public static function getSavedNestedResources()
    {
        static $savedNestedResources = null;
        if ($savedNestedResources === null) {
            $savedNestedResources = new Util\Set(
                [
                'external_account',
                'bank_account',
                ]
            );
        }
        return $savedNestedResources;
    }

    const PATH_CAPABILITIES = '/capabilities';
    const PATH_EXTERNAL_ACCOUNTS = '/external_accounts';
    const PATH_LOGIN_LINKS = '/login_links';
    const PATH_PERSONS = '/persons';

    public function instanceUrl()
    {
        if ($this['id'] === null) {
            return '/v1/account';
        } else {
            return parent::instanceUrl();
        }
    }

    /**
     * @param array|string|null $id   The ID of the account to retrieve, or an
     *                                options array containing an `id` key.
     * @param array|string|null $opts
     *
     * @return self
     */
    public static function retrieve($id = null, $opts = null): self
    {
        if (!$opts && is_string($id) && substr($id, 0, 3) === 'sk_') {
            $opts = $id;
            $id = null;
        }
        return self::_retrieve($id, $opts);
    }

    /*
     * Capabilities methods
     * We can not add the capabilities() method today as the Account object already has a
     * capabilities property which is a hash and not the sub-list of capabilities.
     */


    public function serializeParameters($force = false)
    {
        $update = parent::serializeParameters($force);
        if (isset($this->_values['legal_entity'])) {
            $entity = $this['legal_entity'];
            if (isset($entity->_values['additional_owners'])) {
                $owners = $entity['additional_owners'];
                $entityUpdate = isset($update['legal_entity']) ? $update['legal_entity'] : [];
                $entityUpdate['additional_owners'] = $this->serializeAdditionalOwners($entity, $owners);
                $update['legal_entity'] = $entityUpdate;
            }
        }
        if (isset($this->_values['individual'])) {
            $individual = $this['individual'];
            if (($individual instanceof Person) && !isset($update['individual'])) {
                $update['individual'] = $individual->serializeParameters($force);
            }
        }
        return $update;
    }

    /**
     * @return (array|mixed)[]
     *
     * @psalm-return array<array-key, array|mixed>
     */
    private function serializeAdditionalOwners($legalEntity, $additionalOwners): array
    {
        if (isset($legalEntity->_originalValues['additional_owners'])) {
            $originalValue = $legalEntity->_originalValues['additional_owners'];
        } else {
            $originalValue = [];
        }
        if (($originalValue) && (count($originalValue) > count($additionalOwners))) {
            throw new \InvalidArgumentException(
                "You cannot delete an item from an array, you must instead set a new array"
            );
        }

        $updateArr = [];
        foreach ($additionalOwners as $i => $v) {
            $update = ($v instanceof SierraTecnologiaObject) ? $v->serializeParameters() : $v;

            if ($update !== []) {
                if (!$originalValue
                    || !property_exists($originalValue, $i)
                    || ($update != $legalEntity->serializeParamsValue($originalValue[$i], null, false, true))
                ) {
                    $updateArr[$i] = $update;
                }
            }
        }
        return $updateArr;
    }
}
