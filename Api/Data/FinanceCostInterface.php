<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface Finance Cost - Data to calculate the cost of financing.
 *
 * @api
 *
 * @since 100.0.0
 */
interface FinanceCostInterface extends ExtensibleDataInterface
{
    /**
     * Finance Cost Amount.
     *
     * @var string
     */
    public const FINANCE_COST_AMOUNT = 'finance_cost_amount';

    /**
     * Base Finance Cost Amount.
     *
     * @var string
     */
    public const BASE_FINANCE_COST_AMOUNT = 'base_finance_cost_amount';

    /**
     * Get selected installment.
     *
     * @return int
     */
    public function getSelectedInstallment();

    /**
     * Set selected installment.
     *
     * @param int $selectedInstallment
     *
     * @return void
     */
    public function setSelectedInstallment($selectedInstallment);
}
