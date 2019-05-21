<?php

namespace SierraTecnologia\Radar;

/**
 * Class ValueList
 *
 * @property string $id
 * @property string $object
 * @property string $alias
 * @property int $created
 * @property string $created_by
 * @property string $item_type
 * @property Collection $list_items
 * @property bool $livemode
 * @property SierraTecnologiaObject $metadata
 * @property mixed $name
 * @property int $updated
 * @property string $updated_by
 *
 * @package SierraTecnologia\Radar
 */
class ValueList extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "radar.value_list";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Delete;
    use \SierraTecnologia\ApiOperations\Retrieve;
    use \SierraTecnologia\ApiOperations\Update;
}
