<?php

namespace SierraTecnologia\Issuing;

/**
 * Class Card
 *
 * @property string $id
 * @property string $object
 * @property mixed $authorization_controls
 * @property mixed $billing
 * @property string $brand
 * @property Cardholder $cardholder
 * @property int $created
 * @property string $currency
 * @property int $exp_month
 * @property int $exp_year
 * @property string $last4
 * @property bool $livemode
 * @property \SierraTecnologia\SierraTecnologiaObject $metadata
 * @property string $name
 * @property mixed $shipping
 * @property string $status
 * @property string $type
 *
 * @package SierraTecnologia\Issuing
 */
class Card extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "issuing.card";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Retrieve;
    use \SierraTecnologia\ApiOperations\Update;

    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return \SierraTecnologia\SierraTecnologiaObject|array The card details associated with that issuing card.
     */
    public function details($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/details';
        list($response, $opts) = $this->_request('get', $url, $params, $options);
        $obj = \SierraTecnologia\Util\Util::convertToSierraTecnologiaObject($response, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }
}
