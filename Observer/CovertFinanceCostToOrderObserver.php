<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MercadoPago\PaymentMagento\Api\Data\FinanceCostInterface;

/**
 * Observer Class from Covert Finance Cost To Order.
 */
class CovertFinanceCostToOrderObserver implements ObserverInterface
{
    /**
     * Excecute convert finance cost.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $financeCost = $quote->getData(FinanceCostInterface::FINANCE_COST_AMOUNT);
        $baseFinanceCost = $quote->getData(FinanceCostInterface::BASE_FINANCE_COST_AMOUNT);
        $order->setData(FinanceCostInterface::FINANCE_COST_AMOUNT, $financeCost);
        $order->setData(FinanceCostInterface::BASE_FINANCE_COST_AMOUNT, $baseFinanceCost);
    }
}
