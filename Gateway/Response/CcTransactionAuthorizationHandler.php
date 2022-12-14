<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Gateway\Response;

use InvalidArgumentException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

/**
 * Gateway response Authorizing a Card Payment.
 */
class CcTransactionAuthorizationHandler implements HandlerInterface
{
    /**
     * Response Pay Credit - Block name.
     */
    public const CREDIT = 'credit';

    /**
     * Response Pay Payment Id - Block name.
     */
    public const RESPONSE_PAYMENT_ID = 'id';

    /**
     * Response Pay Status - Block name.
     */
    public const STATUS = 'status';

    /**
     * Response Pay Approved - Block name.
     */
    public const APPROVED = 'approved';

    /**
     * Response Pay In Process - Block name.
     */
    public const IN_PROCCESS = 'in_process';

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Json $json
     */
    public function __construct(
        Json $json
    ) {
        $this->json = $json;
    }

    /**
     * Handles.
     *
     * @param array $handlingSubject
     * @param array $response
     *
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new InvalidArgumentException('Payment data object should be provided');
        }
        $isApproved = false;

        $isDenied = true;

        $paymentDO = $handlingSubject['payment'];

        $payment = $paymentDO->getPayment();

        $order = $payment->getOrder();

        $amount = $order->getBaseGrandTotal();

        if ($response[self::STATUS] === self::APPROVED ||
            $response[self::STATUS] === self::IN_PROCCESS
        ) {
            $isApproved = true;
            $isDenied = false;
        }

        $payment->registerAuthorizationNotification($amount);
        $payment->setAmountAuthorized($amount);
        $payment->setIsTransactionApproved($isApproved);
        $payment->setIsTransactionDenied($isDenied);
        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);
        $payment->setTransactionId($response[self::RESPONSE_PAYMENT_ID]);
        $payment->setTransactionDetails($this->json->serialize($response));
        $payment->setAdditionalData($this->json->serialize($response));
    }
}
