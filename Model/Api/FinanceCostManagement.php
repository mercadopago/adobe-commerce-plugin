<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Model\Api;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use MercadoPago\PaymentMagento\Api\Data\FinanceCostInterface;
use MercadoPago\PaymentMagento\Api\Data\RulesForFinanceCostInterface;
use MercadoPago\PaymentMagento\Api\FinanceCostManagementInterface;

/**
 * Model for application of Financing Cost in Order totals.
 */
class FinanceCostManagement implements FinanceCostManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteCartRepository;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $quoteTotalRepository;

    /**
     * FinanceCostManagement constructor.
     *
     * @param CartRepositoryInterface      $quoteCartRepository
     * @param CartTotalRepositoryInterface $quoteTotalRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteCartRepository,
        CartTotalRepositoryInterface $quoteTotalRepository
    ) {
        $this->quoteCartRepository = $quoteCartRepository;
        $this->quoteTotalRepository = $quoteTotalRepository;
    }

    /**
     * Create Vault Card Id.
     *
     * @param int                          $cartId
     * @param FinanceCostInterface         $userSelect
     * @param RulesForFinanceCostInterface $rules
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     *
     * @return array
     */
    public function saveFinanceCost(
        $cartId,
        FinanceCostInterface $userSelect,
        RulesForFinanceCostInterface $rules
    ) {
        $calculate = [];
        $quoteCart = $this->quoteCartRepository->getActive($cartId);

        if (!$quoteCart->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        $quoteTotal = $this->quoteTotalRepository->get($cartId);

        $grandTotal = $quoteTotal->getGrandTotal() - $quoteCart->getData(FinanceCostInterface::FINANCE_COST_AMOUNT);
        $installment = $userSelect->getSelectedInstallment();
        $totalAmount = round($rules->getTotalAmount(), 2);
        $financeCost = $totalAmount - $grandTotal;

        if ($installment <= 1) {
            $financeCost = null;
        }

        try {
            $quoteCart->setData(FinanceCostInterface::FINANCE_COST_AMOUNT, $financeCost);
            $quoteCart->setData(FinanceCostInterface::BASE_FINANCE_COST_AMOUNT, $financeCost);
            $this->quoteCartRepository->save($quoteCart);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('It was not possible to save the value of the financing cost'));
        }

        $calculate = [
            'finance_cost' => [
                'debug'         => $totalAmount,
                'installment'   => $installment,
                'finance_cost'  => $financeCost,
                'grand_total'   => $grandTotal,
            ],
        ];

        return $calculate;
    }
}
