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
 * Payment details form block by Pse.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Pse extends ConfigurableInfo
{
    /**
     * Pse Info template.
     *
     * @var string
     */
    protected $_template = 'MercadoPago_PaymentMagento::info/pse/instructions.phtml';
}
