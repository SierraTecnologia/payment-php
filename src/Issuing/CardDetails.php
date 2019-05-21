<?php

namespace SierraTecnologia\Issuing;

/**
 * Class CardDetails
 *
 * @property string $id
 * @property string $object
 * @property Card $card
 * @property string $cvc
 * @property int $exp_month
 * @property int $exp_year
 * @property string $number
 *
 * @package SierraTecnologia\Issuing
 */
class CardDetails extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "issuing.card_details";
}
