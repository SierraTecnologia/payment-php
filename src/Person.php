<?php

namespace SierraTecnologia;

/**
 * Class Person
 *
 * @package SierraTecnologia
 *
 * @property string $id
 * @property string $object
 * @property string $account
 * @property mixed $address
 * @property mixed $address_kana
 * @property mixed $address_kanji
 * @property int $created
 * @property bool $deleted
 * @property mixed $dob
 * @property string $email
 * @property string $first_name
 * @property string $first_name_kana
 * @property string $first_name_kanji
 * @property string $gender
 * @property bool $id_number_provided
 * @property string $last_name
 * @property string $last_name_kana
 * @property string $last_name_kanji
 * @property string $maiden_name
 * @property SierraTecnologiaObject $metadata
 * @property string $phone
 * @property mixed $relationship
 * @property mixed $requirements
 * @property bool $ssn_last_4_provided
 * @property mixed $verification
 */
class Person extends ApiResource
{

    const OBJECT_NAME = "person";

    use ApiOperations\Delete;
    use ApiOperations\Update;

    /**
     * Possible string representations of a person's gender.
     *
     * @link https://sierratecnologia.com.br/docs/api/persons/object#person_object-gender
     */
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * Possible string representations of a person's verification status.
     *
     * @link https://sierratecnologia.com.br/docs/api/persons/object#person_object-verification-status
     */
    const VERIFICATION_STATUS_PENDING    = 'pending';
    const VERIFICATION_STATUS_UNVERIFIED = 'unverified';
    const VERIFICATION_STATUS_VERIFIED   = 'verified';

    /**
     * @return string The API URL for this SierraTecnologia account reversal.
     */
    public function instanceUrl()
    {
        $id = $this['id'];
        $account = $this['account'];
        if (!$id) {
            throw new Error\InvalidRequest(
                "Could not determine which URL to request: " .
                "class instance has invalid ID: $id",
                null
            );
        }
        $id = Util\Util::utf8($id);
        $account = Util\Util::utf8($account);

        $base = Account::classUrl();
        $accountExtn = urlencode($account);
        $extn = urlencode($id);
        return "$base/$accountExtn/persons/$extn";
    }
}
