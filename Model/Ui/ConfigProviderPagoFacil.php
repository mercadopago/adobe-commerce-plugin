<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Phrase;
use Magento\Framework\View\Asset\Source;
use Magento\Payment\Model\CcConfig;
use Magento\Quote\Api\Data\CartInterface;
use MercadoPago\PaymentMagento\Gateway\Config\ConfigPagoFacil;

/**
 * User interface model for settings Pago Fácil.
 */
class ConfigProviderPagoFacil implements ConfigProviderInterface
{
    /**
     * Mercado Pago Payment Magento Pago Fácil Code.
     */
    public const CODE = 'mercadopago_paymentmagento_pagofacil';

    /**
     * @var ConfigPagoFacil
     */
    protected $config;

    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * @var CcConfig
     */
    protected $ccConfig;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Source
     */
    protected $assetSource;

    /**
     * @param ConfigPagoFacil $config
     * @param CartInterface   $cart
     * @param CcConfig        $ccConfig
     * @param Escaper         $escaper
     * @param Source          $assetSource
     */
    public function __construct(
        ConfigPagoFacil $config,
        CartInterface $cart,
        CcConfig $ccConfig,
        Escaper $escaper,
        Source $assetSource
    ) {
        $this->config = $config;
        $this->cart = $cart;
        $this->escaper = $escaper;
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
    }

    /**
     * Retrieve assoc array of checkout configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        $storeId = $this->cart->getStoreId();

        return [
            'payment' => [
                ConfigPagoFacil::METHOD => [
                    'isActive'                        => $this->config->isActive($storeId),
                    'title'                           => $this->config->getTitle($storeId),
                    'document_identification_capture' => $this->config->hasUseDocumentIdentificationCapture($storeId),
                    'expiration'                      => $this->config->getExpirationFormat($storeId),
                    'instruction_checkout_pagofacil'  => nl2br($this->getDescriptions($storeId)),
                    'logo'                            => $this->getLogo(),
                    'payment_method_id'               => ConfigPagoFacil::PAYMENT_METHOD_ID,
                ],
            ],
        ];
    }

    /**
     * Get icons for available payment methods.
     *
     * @return array
     */
    public function getLogo()
    {
        $logo = [];
        $asset = $this->ccConfig->createAsset('MercadoPago_PaymentMagento::images/pago-facil/logo.svg');
        $placeholder = $this->assetSource->findSource($asset);
        if ($placeholder) {
            list($width, $height) = getimagesizefromstring($asset->getSourceFile());
            $logo = [
                'url'    => $asset->getUrl(),
                'width'  => $width,
                'height' => $height,
                'title'  => __('PagoFacil - MercadoPago'),
            ];
        }

        return $logo;
    }

    /**
     * Get Descriptions.
     *
     * @param int|null $storeId
     *
     * @return Phrase
     */
    public function getDescriptions($storeId)
    {
        $text = $this->config->getInstructionCheckoutPagoFacil($storeId);

        return __($text);
    }
}
