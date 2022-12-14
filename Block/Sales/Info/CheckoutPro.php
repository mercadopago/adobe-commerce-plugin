<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Block\Sales\Info;

use Magento\Payment\Block\ConfigurableInfo;

/**
 * Payment details form block by Checkout Pro.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class CheckoutPro extends ConfigurableInfo
{
    /**
     * Checkout Pro Info template.
     *
     * @var string
     */
    protected $_template = 'MercadoPago_PaymentMagento::info/checkout-pro/instructions.phtml';
}
