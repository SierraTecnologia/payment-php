<?php

namespace SierraTecnologia;

/**
 * Class Invoice
 *
 * @property string $id
 * @property string $object
 * @property string $account_country
 * @property string $account_name
 * @property int $amount_due
 * @property int $amount_paid
 * @property int $amount_remaining
 * @property int $application_fee_amount
 * @property int $attempt_count
 * @property bool $attempted
 * @property bool $auto_advance
 * @property string $billing
 * @property string $billing_reason
 * @property string $charge
 * @property int $created
 * @property string $currency
 * @property array $custom_fields
 * @property string $customer
 * @property mixed $customer_address
 * @property string $customer_email
 * @property string $customer_name
 * @property string $customer_phone
 * @property mixed $customer_shipping
 * @property array $customer_tax_ids
 * @property string $default_payment_method
 * @property string $default_source
 * @property array $default_tax_rates
 * @property string $description
 * @property Discount $discount
 * @property int $due_date
 * @property int $ending_balance
 * @property string $footer
 * @property string $hosted_invoice_url
 * @property string $invoice_pdf
 * @property Collection $lines
 * @property bool $livemode
 * @property SierraTecnologiaObject $metadata
 * @property int $next_payment_attempt
 * @property string $number
 * @property bool $paid
 * @property string $payment_intent
 * @property int $period_end
 * @property int $period_start
 * @property int $post_payment_credit_notes_amount
 * @property int $pre_payment_credit_notes_amount
 * @property string $receipt_number
 * @property int $starting_balance
 * @property string $statement_descriptor
 * @property string $status
 * @property mixed $status_transitions
 * @property string $subscription
 * @property int $subscription_proration_date
 * @property int $subtotal
 * @property int $tax
 * @property mixed $threshold_reason
 * @property int $total
 * @property array $total_tax_amounts
 * @property int $webhooks_delivered_at
 *
 * @package SierraTecnologia
 */
class Invoice extends ApiResource
{

    const OBJECT_NAME = "invoice";

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * Possible string representations of the invoice status.
     *
     * @link https://sierratecnologia.com.br/docs/api/invoices/object#invoice_object-status
     */
    const STATUS_DRAFT         = 'draft';
    const STATUS_OPEN          = 'open';
    const STATUS_PAID          = 'paid';
    const STATUS_UNCOLLECTIBLE = 'uncollectible';
    const STATUS_VOID          = 'void';

    /**
     * Possible string representations of the billing.
     *
     * @link https://sierratecnologia.com.br/docs/api/invoices/object#invoice_object-billing
     */
    const BILLING_SEND_INVOICE         = 'send_invoice';
    const BILLING_CHARGE_AUTOMATICALLY = 'charge_automatically';

    /**
     * Possible string representations of the billing reason.
     *
     * @link https://sierratecnologia.com.br/docs/api/invoices/object#invoice_object-billing_reason
     */
    const BILLING_REASON_SUBSCRIPTION           = 'subscription';
    const BILLING_REASON_SUBSCRIPTION_CREATE    = 'subscription_create';
    const BILLING_REASON_SUBSCRIPTION_CYCLE     = 'subscription_cycle';
    const BILLING_REASON_SUBSCRIPTION_UPDATE    = 'subscription_update';
    const BILLING_REASON_SUBSCRIPTION_THRESHOLD = 'subscription_threshold';
    const BILLING_REASON_MANUAL                 = 'manual';
    const BILLING_REASON_UPCOMING               = 'upcoming';
}
