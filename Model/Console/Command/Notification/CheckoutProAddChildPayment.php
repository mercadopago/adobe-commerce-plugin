<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Model\Console\Command\Notification;

use Exception;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use MercadoPago\PaymentMagento\Model\Console\Command\AbstractModel;

/**
 * Model for Command lines to add child transaction in Checkout Pro.
 */
class CheckoutProAddChildPayment extends AbstractModel
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @param Logger $logger
     * @param Order  $order
     */
    public function __construct(
        Logger $logger,
        Order $order
    ) {
        parent::__construct(
            $logger
        );
        $this->order = $order;
    }

    /**
     * Command Add.
     *
     * @param int    $orderId
     * @param string $childTransaction
     *
     * @return void
     */
    public function add($orderId, $childTransaction)
    {
        $this->writeln('Init Fetch Checkout Pro Payments');

        /** @var Order $order */
        $order = $this->order->load($orderId);

        $payment = $order->getPayment();

        $preferenceId = $order->getExtOrderId();

        try {
            $payment->setTransactionId($childTransaction);
            $payment->setParentTransactionId($preferenceId);
            $payment->setIsTransactionClosed(false);
            $payment->setShouldCloseParentTransaction(true);
            $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH);
            $order->save();
            $payment->update(true);
        } catch (Exception $exc) {
            $this->writeln('<error>'.$exc->getMessage().'</error>');
        }

        if ($order->getState() === Order::STATE_PAYMENT_REVIEW) {
            $order = $payment->getOrder();
            $order->setState(Order::STATE_NEW);
            $order->setStatus('pending');
        }

        $this->writeln(
            '<info>'.
            __(
                'Order %1 - Increment Id %2 - state %3',
                $orderId,
                $order->getIncrementId(),
                $order->getState(),
            )
            .'</info>'
        );

        $order->save();

        $this->writeln(__('Finished'));
    }
}
