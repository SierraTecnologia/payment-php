<?php

namespace SierraTecnologia\Terminal;

/**
 * Class Reader
 *
 * @property string $id
 * @property string $object
 * @property bool $deleted
 * @property string $device_sw_version
 * @property string $device_type
 * @property string $ip_address
 * @property string $label
 * @property string $location
 * @property string $serial_number
 * @property string $status
 *
 * @package SierraTecnologia\Terminal
 */
class Reader extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "terminal.reader";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Delete;
    use \SierraTecnologia\ApiOperations\Retrieve;
    use \SierraTecnologia\ApiOperations\Update;
}
