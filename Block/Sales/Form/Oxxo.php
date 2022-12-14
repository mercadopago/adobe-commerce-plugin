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
use MercadoPago\PaymentMagento\Gateway\Config\ConfigOxxo;

/**
 * Payment form block by Oxxo.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Oxxo extends \Magento\Payment\Block\Form
{
    /**
     * Oxxo template.
     *
     * @var string
     */
    protected $_template = 'MercadoPago_PaymentMagento::form/oxxo.phtml';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ConfigOxxo
     */
    protected $configOxxo;

    /**
     * @var Quote
     */
    protected $sessionQuote;

    /**
     * @param Context    $context
     * @param Config     $config
     * @param ConfigOxxo $configOxxo
     * @param Quote      $sessionQuote
     */
    public function __construct(
        Context $context,
        Config $config,
        ConfigOxxo $configOxxo,
        Quote $sessionQuote
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->configOxxo = $configOxxo;
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

        return $this->configOxxo->getTitle($storeId);
    }

    /**
     * Expiration.
     *
     * @return string
     */
    public function getExpiration()
    {
        $storeId = $this->getBackendSessionQuote()->getStoreId();

        return $this->configOxxo->getExpirationFormat($storeId);
    }

    /**
     * Instruction.
     *
     * @return Phrase
     */
    public function getInstruction()
    {
        $storeId = $this->getBackendSessionQuote()->getStoreId();

        $text = $this->configOxxo->getInstructionCheckoutOxxo($storeId);

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
        return ConfigOxxo::PAYMENT_METHOD_ID;
    }
}
