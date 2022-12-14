/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */

define([
    'underscore',
    'jquery',
    'MercadoPago_PaymentMagento/js/view/payment/mp-security-form'
], function (
    _,
    $,
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            active: false,
            template: 'MercadoPago_PaymentMagento/payment/bancomer',
            bancomerForm: 'MercadoPago_PaymentMagento/payment/bancomer-form'
        },

        /**
         * Initializes model instance.
         *
         * @returns {Object}
         */
        initObservable() {
            this._super().observe([
                'active'
            ]);
            return this;
        },

        /**
         * Init component
         */
        initialize() {
            this._super();
        },

        /**
         * Get code
         * @returns {String}
         */
        getCode() {
            return 'mercadopago_paymentmagento_bancomer';
        },

        /**
         * Is Active
         * @returns {Boolean}
         */
        isActive() {
            var active = this.getCode() === this.isChecked();

            this.active(active);
            return active;
        },

        /**
         * Init Form Element
         * @returns {void}
         */
        initFormElement(element) {
            this.formElement = element;
            $(this.formElement).validation();
        },

        /**
         * Before Place Order
         * @returns {void}
         */
        beforePlaceOrder() {
            if (!$(this.formElement).valid()) {
                return;
            }
            this.placeOrder();
        },

        /**
         * Get data
         * @returns {Object}
         */
        getData() {
            let self = this;

            return {
                method: self.getCode(),
                'additional_data': {
                    'payment_method_id': self.getPaymentIdMethod(),
                    'payer_document_type': self.mpPayerType(),
                    'payer_document_identification': self.mpPayerDocument()
                }
            };
        },

        /**
         * Get instruction checkout for Bancomer
         * @returns {String}
         */
        getInstructionCheckoutBancomer() {
            return window.checkoutConfig.payment[this.getCode()].instruction_checkout_bancomer;
        }
    });
});
