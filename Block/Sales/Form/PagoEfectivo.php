<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Block\Sales\Form;

use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template\Context;
use MercadoPago\PaymentMagento\Gateway\Config\Config;
use MercadoPago\PaymentMagento\Gateway\Config\ConfigPagoEfectivo;

/**
 * Payment form block by PagoEfectivo.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class PagoEfectivo extends \Magento\Payment\Block\Form
{
    /**
     * PagoEfectivo template.
     *
     * @var string
     */
    protected $_template = 'MercadoPago_PaymentMagento::form/pago-efectivo.phtml';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ConfigPagoEfectivo
     */
    protected $configPagoEfectivo;

    /**
     * @var Quote
     */
    protected $sessionQuote;

    /**
     * @param Context            $context
     * @param Config             $config
     * @param ConfigPagoEfectivo $configPagoEfectivo
     * @param Quote              $sessionQuote
     */
    public function __construct(
        Context $context,
        Config $config,
        ConfigPagoEfectivo $configPagoEfectivo,
        Quote $sessionQuote
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->configPagoEfectivo = $configPagoEfectivo;
        $this->sessionQuote = $sessionQuote;
    }

    /**
     * Get Backend Session Quote.
     */
    public function getBackendSessionQuote()
    {
        return $this->sessionQuote->getQuote();
    }

    /**
     * Title.
     *
     * @return string
     */
    public function getTitle()
    {
        $storeId = $this->getBackendSessionQuote()->getStoreId();

        return $this->configPagoEfectivo->getTitle($storeId);
    }

    /**
     * Expiration.
     *
     * @return string
     */
    public function getExpiration()
    {
        $storeId = $this->getBackendSessionQuote()->getStoreId();

        return $this->configPagoEfectivo->getExpirationFormat($storeId);
    }

    /**
     * Instruction.
     *
     * @return Phrase
     */
    public function getInstruction()
    {
        $storeId = $this->getBackendSessionQuote()->getStoreId();

        $text = $this->configPagoEfectivo->getInstructionCheckoutPagoEfectivo($storeId);

        return __($text);
    }

    /**
     * Mp Public Key.
     *
     * @return string
     */
    public function getMpPublicKey()
    {
        $storeId = $this->getBackendSessionQuote()->getStoreId();

        return $this->config->getMerchantGatewayClientId($storeId);
    }

    /**
     * Get Payment Method Id.
     *
     * @return string
     */
    public function getPaymentMethodId()
    {
        return ConfigPagoEfectivo::PAYMENT_METHOD_ID;
    }
}
