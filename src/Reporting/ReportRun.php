<?php

namespace SierraTecnologia\Reporting;

/**
 * Class ReportRun
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property string $error
 * @property bool $livemode
 * @property mixed $parameters
 * @property string $report_type
 * @property mixed $result
 * @property string $status
 * @property int $succeeded_at
 *
 * @package SierraTecnologia\Reporting
 */
class ReportRun extends \SierraTecnologia\ApiResource
{
    const OBJECT_NAME = "reporting.report_run";

    use \SierraTecnologia\ApiOperations\All;
    use \SierraTecnologia\ApiOperations\Create;
    use \SierraTecnologia\ApiOperations\Retrieve;
}
