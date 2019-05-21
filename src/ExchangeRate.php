<?php

namespace SierraTecnologia;

/**
 * Class ExchangeRate
 *
 * @package SierraTecnologia
 */
class ExchangeRate extends ApiResource
{

    const OBJECT_NAME = "exchange_rate";

    use ApiOperations\All;
    use ApiOperations\Retrieve;
}
