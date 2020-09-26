<?php

namespace SierraTecnologia;

/**
 * Class Capability
 *
 * @package SierraTecnologia
 *
 * @property string $id
 * @property string $object
 * @property string $account
 * @property bool $requested
 * @property int $requested_at
 * @property mixed $requirements
 * @property string $status
 */
class Capability extends ApiResource
{

    const OBJECT_NAME = "capability";

    use ApiOperations\Update;

    /**
     * Possible string representations of a capability's status.
     *
     * @link https://sierratecnologia.com.br/docs/api/capabilities/object#capability_object-status
     */
    const STATUS_ACTIVE      = 'active';
    const STATUS_INACTIVE    = 'inactive';
    const STATUS_PENDING     = 'pending';
    const STATUS_UNREQUESTED = 'unrequested';

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
        return "$base/$accountExtn/capabilities/$extn";
    }
}
