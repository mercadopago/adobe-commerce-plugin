<?php
/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

namespace MercadoPago\PaymentMagento\Block\Sales\Form;

use Magento\Payment\Block\Form\Cc as NativeCc;

/**
 * Payment form block by card.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Card extends NativeCc
{
    /**
     * Cc template.
     *
     * @var string
     */
    protected $_template = 'MercadoPago_PaymentMagento::form/cc.phtml';
}
