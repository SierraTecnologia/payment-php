<?php

namespace SierraTecnologia;

/**
 * Class OrderItem
 *
 * @property string $object
 * @property int $amount
 * @property string $currency
 * @property string $description
 * @property string $parent
 * @property int $quantity
 * @property string $type
 *
 * @package SierraTecnologia
 */
class OrderItem extends SierraTecnologiaObject
{

    const OBJECT_NAME = "order_item";
}
