<?php

namespace SierraTecnologia;

/**
 * Class CreditNote
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $created
 * @property string $currency
 * @property string $customer
 * @property string $invoice
 * @property bool $livemode
 * @property string $memo
 * @property SierraTecnologiaObject $metadata
 * @property string $number
 * @property string $pdf
 * @property string $reason
 * @property string $refund
 * @property string $status
 * @property string $type
 *
 * @package SierraTecnologia
 */
class CreditNote extends ApiResource
{

    const OBJECT_NAME = "credit_note";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * Possible string representations of the credit note reason.
     *
     * @link https://sierratecnologia.com.br/docs/api/credit_notes/object#credit_note_object-reason
     */
    const REASON_DUPLICATE              = 'duplicate';
    const REASON_FRAUDULENT             = 'fraudulent';
    const REASON_ORDER_CHANGE           = 'order_change';
    const REASON_PRODUCT_UNSATISFACTORY = 'product_unsatisfactory';

    /**
     * Possible string representations of the credit note status.
     *
     * @link https://sierratecnologia.com.br/docs/api/credit_notes/object#credit_note_object-status
     */
    const STATUS_ISSUED = 'issued';
    const STATUS_VOID   = 'void';

    /**
     * Possible string representations of the credit note type.
     *
     * @link https://sierratecnologia.com.br/docs/api/credit_notes/object#credit_note_object-status
     */
    const TYPE_POST_PAYMENT = 'post_payment';
    const TYPE_PRE_PAYMENT  = 'pre_payment';
}
