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
 * Payment details form block by Oxxo.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Oxxo extends ConfigurableInfo
{
    /**
     * Oxxo Info template.
     *
     * @var string
     */
    protected $_template = 'MercadoPago_PaymentMagento::info/oxxo/instructions.phtml';
}
