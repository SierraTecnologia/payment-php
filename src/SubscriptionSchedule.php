<?php

namespace SierraTecnologia;

/**
 * Class SubscriptionSchedule
 *
 * @property string $id
 * @property string $object
 * @property string $billing
 * @property mixed $billing_thresholds
 * @property int $canceled_at
 * @property int $completed_at
 * @property int $created
 * @property mixed $current_phase
 * @property string $customer
 * @property mixed $invoice_settings
 * @property boolean $livemode
 * @property SierraTecnologiaObject $metadata
 * @property mixed $phases
 * @property int $released_at
 * @property string $released_subscription
 * @property string $renewal_behavior
 * @property mixed $renewal_interval
 * @property string $revision
 * @property string $status
 * @property string $subscription
 *
 * @package SierraTecnologia
 */
class SubscriptionSchedule extends ApiResource
{

    const OBJECT_NAME = "subscription_schedule";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;
    use ApiOperations\NestedResource;

    const PATH_REVISIONS = '/revisions';
}
