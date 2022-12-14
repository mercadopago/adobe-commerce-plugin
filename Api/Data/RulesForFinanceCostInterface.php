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
 * Interface Rules for Finance Cost - Data to calculate the cost of financing.
 *
 * @api
 *
 * @since 100.0.0
 */
interface RulesForFinanceCostInterface extends ExtensibleDataInterface
{
    /**
     * Installments in rules.
     */
    public const INSTALLMENTS = 'installments';

    /**
     * Installments rate.
     */
    public const INSTALLMENT_RATE = 'installment_rate';

    /**
     * Discount rate.
     */
    public const DISCOUNT_RATE = 'discount_rate';

    /**
     * Reimbursement Rate.
     */
    public const REIMBURSEMENT_RATE = 'reimbursement_rate';

    /**
     * Total Amount.
     */
    public const TOTAL_AMOUNT = 'total_amount';

    /**
     * Get installments for rule.
     *
     * @return int|null
     */
    public function getInstallments();

    /**
     * Set installments for rule.
     *
     * @param int $installments
     *
     * @return $this
     */
    public function setInstallments($installments);

    /**
     * Get installment rate.
     *
     * @return float|null
     */
    public function getInstallmentRate();

    /**
     * Set installment rate.
     *
     * @param float $installmentRate
     *
     * @return $this
     */
    public function setInstallmentRate($installmentRate);

    /**
     * Get discount rate.
     *
     * @return float|null
     */
    public function getDiscountRate();

    /**
     * Set discount rate.
     *
     * @param float $discountRate
     *
     * @return $this
     */
    public function setDiscountRate($discountRate);

    /**
     * Get reimbursement rate.
     *
     * @return bool|null
     */
    public function getReimbursementRate();

    /**
     * Set reimbursement rate.
     *
     * @param bool $reimbursementRate
     *
     * @return $this
     */
    public function setReimbursementRate($reimbursementRate);

    /**
     * Get Total Amount.
     *
     * @return float|null
     */
    public function getTotalAmount();

    /**
     * Set Total Amount.
     *
     * @param bool $totalAmount
     *
     * @return $this
     */
    public function setTotalAmount($totalAmount);
}
