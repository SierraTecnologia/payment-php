<?php

namespace SierraTecnologia\Radar;

/**
 * Class ValueListItem
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property string $created_by
 * @property string $list
 * @property bool $livemode
 * @property string $value
 *
 * @package SierraTecnologia\Radar
 */
class ValueListItem extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "radar.value_list_item";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Delete;
    use \SierraTecnologia\ApiOperations\Retrieve;
}
