<?php

namespace SierraTecnologia\Reporting;

/**
 * Class ReportType
 *
 * @property string $id
 * @property string $object
 * @property int $data_available_end
 * @property int $data_available_start
 * @property string $name
 * @property int $updated
 * @property string $version
 *
 * @package SierraTecnologia\Reporting
 */
class ReportType extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "reporting.report_type";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Retrieve;
}
