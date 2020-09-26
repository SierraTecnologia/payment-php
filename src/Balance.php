<?php

namespace SierraTecnologia;

/**
 * Class Balance
 *
 * @property string $object
 * @property array $available
 * @property array $connect_reserved
 * @property bool $livemode
 * @property array $pending
 *
 * @package SierraTecnologia
 */
class Balance extends SingletonApiResource
{

    const OBJECT_NAME = "balance";
}
