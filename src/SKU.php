<?php

namespace SierraTecnologia;

/**
 * Class SKU
 *
 * @property string $id
 * @property string $object
 * @property bool $active
 * @property mixed $attributes
 * @property int $created
 * @property string $currency
 * @property string $image
 * @property mixed $inventory
 * @property bool $livemode
 * @property SierraTecnologiaObject $metadata
 * @property mixed $package_dimensions
 * @property int $price
 * @property string $product
 * @property int $updated
 *
 * @package SierraTecnologia
 */
class SKU extends ApiResource
{

    const OBJECT_NAME = "sku";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
}
