<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\Config\Config as PaymentConfig;
use Magento\Store\Model\ScopeInterface;

/**
 * Gateway setting for the payment method for Ticket Boleto.
 */
class ConfigBoleto extends PaymentConfig
{
    /**
     * Method Name.
     */
    public const METHOD = 'mercadopago_paymentmagento_boleto';

    /**
     * Payment Method Id Boleto.
     */
    public const PAYMENT_METHOD_ID = 'bolbradesco';

    /**
     * Active.
     */
    public const ACTIVE = 'active';

    /**
     * Title.
     */
    public const TITLE = 'title';

    /**
     * Instruction Checkout Boleto.
     */
    public const INSTRUCTION_CHECKOUT_BOLETO = 'instruction_checkout';

    /**
     * Expiration.
     */
    public const EXPIRATION = 'expiration';

    /**
     * Get Document Identification.
     */
    public const USE_GET_DOCUMENT_IDENTIFICATION = 'get_document_identification';

    /**
     * Get Name.
     */
    public const USE_GET_NAME = 'get_name';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param DateTime             $date
     * @param Config               $config
     * @param string               $methodCode
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DateTime $date,
        Config $config,
        $methodCode = self::METHOD
    ) {
        parent::__construct($scopeConfig, $methodCode);
        $this->scopeConfig = $scopeConfig;
        $this->date = $date;
        $this->config = $config;
    }

    /**
     * Get Payment configuration status.
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isActive($storeId = null): bool
    {
        $pathPattern = 'payment/%s/%s';

        return (bool) $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::ACTIVE),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get title of payment.
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getTitle($storeId = null): string
    {
        $pathPattern = 'payment/%s/%s';

        return $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::TITLE),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Expiration Formatted.
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getExpirationFormatted($storeId = null): string
    {
        $pathPattern = 'payment/%s/%s';
        $due = $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::EXPIRATION),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->date->gmtDate('Y-m-d\T23:59:59.000O', strtotime("+{$due} days"));
    }

    /**
     * Get Instruction Checkoout for Ticket.
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getInstructionCheckoutBoleto($storeId = null): string
    {
        $pathPattern = 'payment/%s/%s';

        return $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::INSTRUCTION_CHECKOUT_BOLETO),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Expiration.
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getExpiration($storeId = null): string
    {
        $pathPattern = 'payment/%s/%s';
        $due = $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::EXPIRATION),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->date->gmtDate('d/m/Y', strtotime("+{$due} days"));
    }

    /**
     * Get Expiration Formart.
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getExpirationFormat($storeId = null): string
    {
        $pathPattern = 'payment/%s/%s';
        $due = $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::EXPIRATION),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->date->gmtDate('d/m/Y', strtotime("+{$due} days"));
    }

    /**
     * Get if you use document capture on the form.
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function hasUseDocumentIdentificationCapture($storeId = null): bool
    {
        $pathPattern = 'payment/%s/%s';

        return (bool) $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::USE_GET_DOCUMENT_IDENTIFICATION),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get if you use name capture on the form.
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function hasUseNameCapture($storeId = null): bool
    {
        $pathPattern = 'payment/%s/%s';

        return (bool) $this->scopeConfig->getValue(
            sprintf($pathPattern, self::METHOD, self::USE_GET_NAME),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Calc Digit for Line Code.
     *
     * @param string $number
     *
     * @return int
     */
    public function calcDigit($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        $numbertotal10 = 0;
        $parcial10 = [];
        $fator = 2;

        for ($i = strlen($number); $i > 0; $i--) {
            $number[$i] = substr($number, $i - 1, 1);

            $temp = $number[$i] * $fator;
            $temp0 = 0;

            foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $vetor) {
                $temp0 += $vetor;
            }

            $parcial10[$i] = $temp0;

            $numbertotal10 += $parcial10[$i];

            if ($fator === 2) {
                $fator = 1;
            } elseif ($fator !== 2) {
                $fator = 2;
            }
        }

        $resto = $numbertotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }

        return $digito;
    }

    /**
     * Get Line Code.
     *
     * @param string $barcode
     *
     * @return string
     */
    public function getLineCode($barcode)
    {
        $field1 = substr($barcode, 0, 4).substr($barcode, 19, 1).'.'.substr($barcode, 20, 4);
        $digit1 = $this->calcDigit($field1);

        $field2 = substr($barcode, 24, 5).'.'.substr($barcode, 29, 5);
        $digit2 = $this->calcDigit($field2);

        $field3 = substr($barcode, 34, 5).'.'.substr($barcode, 39, 5);
        $digit3 = $this->calcDigit($field3);

        $field4 = substr($barcode, 4, 1);

        $field5 = substr($barcode, 5, 14);

        return $field1.$digit1.' '.$field2.$digit2.' '.$field3.$digit3.' '.$field4.' '.$field5;
    }
}
