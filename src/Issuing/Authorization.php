<?php

namespace SierraTecnologia\Issuing;

/**
 * Class Authorization
 *
 * @property string $id
 * @property string $object
 * @property bool $approved
 * @property string $authorization_method
 * @property int $authorized_amount
 * @property string $authorized_currency
 * @property \SierraTecnologia\Collection $balance_transactions
 * @property Card $card
 * @property Cardholder $cardholder
 * @property int $created
 * @property int $held_amount
 * @property string $held_currency
 * @property bool $is_held_amount_controllable
 * @property bool $livemode
 * @property mixed $merchant_data
 * @property \SierraTecnologia\SierraTecnologiaObject $metadata
 * @property int $pending_authorized_amount
 * @property int $pending_held_amount
 * @property mixed $request_history
 * @property string $status
 * @property \SierraTecnologia\Collection $transactions
 * @property mixed $verification_data
 *
 * @package SierraTecnologia\Issuing
 */
class Authorization extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "issuing.authorization";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Retrieve;
    use \SierraTecnologia\ApiOperations\Update;

    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return Authorization The approved authorization.
     */
    public function approve($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/approve';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }

    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return Authorization The declined authorization.
     */
    public function decline($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/decline';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }
}
