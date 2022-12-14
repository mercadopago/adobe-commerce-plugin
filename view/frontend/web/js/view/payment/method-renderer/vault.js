/**
 * Copyright © MercadoPago. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See LICENSE for license details.
 */
define([
    'underscore',
    'jquery',
    'ko',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/quote',
    'Magento_Payment/js/model/credit-card-validation/credit-card-data',
    'Magento_Vault/js/view/payment/method-renderer/vault',
    'mage/translate',
    'MercadoPago_PaymentMagento/js/action/checkout/set-finance-cost'
], function (
    _,
    $,
    _ko,
    fullScreenLoader,
    quote,
    creditCardData,
    VaultComponent,
    $t,
    setFinanceCost
) {
    'use strict';

    return VaultComponent.extend({
        defaults: {
            active: false,
            template: 'MercadoPago_PaymentMagento/payment/vault',
            vaultForm: 'MercadoPago_PaymentMagento/payment/vault-form',
            amount:  quote.totals().grand_total,
            creditCardListInstallments: '',
            creditCardVerificationNumber: '',
            creditCardInstallment: '',
            creditCardNumberToken: '',
            creditCardType: '',
            isLoading: true
        },

        /**
         * Initializes model instance.
         *
         * @returns {Object}
         */
        initObservable() {
            this._super().observe([
                'amount',
                'active',
                'creditCardListInstallments',
                'creditCardVerificationNumber',
                'creditCardInstallment',
                'creditCardNumberToken',
                'creditCardType',
                'isLoading'
            ]);
            return this;
        },

        /**
         * Get auxiliary code
         * @returns {String}
         */
        getAuxiliaryCode() {
            return 'mercadopago_paymentmagento_cc';
        },

        /**
         * Get code
         * @returns {String}
         */
        getCode() {
            return 'mercadopago_paymentmagento_cc_vault';
        },

        /**
         * Init component
         */
        initialize() {
            var self = this;

            this._super();

            self.active.subscribe((value) => {
                if (value === true) {
                    this.getListOptionsToInstallmentsVault();
                    creditCardData.creditCardInstallment =  null;

                    setTimeout(() => {
                        self.mountCardForm();
                    }, 3000);
                }

                if (value === false) {
                    self.isLoading(true);
                    self.unMountCardForm();
                    creditCardData.creditCardInstallment =  null;
                    self.creditCardInstallment(null);
                }
            });

            self.creditCardInstallment.subscribe((value) => {
                self.addFinanceCost();
                creditCardData.creditCardInstallment = value;
            });

            self.creditCardVerificationNumber.subscribe((value) => {
                self.getCardIdDetails();
                creditCardData.creditCardVerificationNumber = value;
            });

            self.creditCardNumberToken.subscribe((value) => {
                creditCardData.creditCardNumberToken = value;
            });

            self.creditCardType.subscribe((value) => {
                creditCardData.creditCardType = value;
            });

            self.creditCardListInstallments.subscribe((value) => {
                creditCardData.creditCardListInstallments = value;
            });

            quote.totals.subscribe((value) => {
                var financeCostAmount = 0;

                _.map(quote.totals()['total_segments'], (segment) => {
                    if (segment['code'] === 'finance_cost_amount') {
                        financeCostAmount = segment['value'];
                    }
                });
                self.amount(value.grand_total - financeCostAmount);
            });

            self.amount.subscribe((value) => {
                creditCardData.amount = value;
                self.getListOptionsToInstallmentsVault();
            });
        },

        /**
         * Un Mount Cart Form
         * @return {void}
         */
        unMountCardForm() {
            let self = this,
                vaultId = self.getId();

            console.log(vaultId);
            window.vaultSecurityCode.vaultId.unmount();
        },

        /**
         * Mount Cart Form
         * @return {void}
         */
        mountCardForm() {
            let self = this,
                vaultId = self.getId(),
                fieldSecurityCode = vaultId + '_cc_id',
                styleField = {
                    height: '100%',
                    padding: '30px 15px'
                };

            window.vaultSecurityCode = { vaultId : window.mp.fields.create('securityCode', { style: styleField }) };
            window.vaultSecurityCode.vaultId
                .mount(fieldSecurityCode)
                .on('error', () => { self.mountCardForm(); })
                .on('blur', () => { self.removeClassesIfEmpyt(fieldSecurityCode); })
                .on('focus', () => { self.toogleFocusStyle(fieldSecurityCode); })
                .on('validityChange', (event) => { self.toogleValidityState(fieldSecurityCode, event.errorMessages); })
                .on('ready', () => { self.isLoading(false); });
        },

        /**
         * Remove Classes if Empyt
         * @param {String} element
         * @returns {void}
         */
        removeClassesIfEmpyt(element) {
            let hasError = $('#' + element).closest('.control-mp-iframe.has-error').length,
                isValid = $('#' + element).closest('.control-mp-iframe.is-valid').length;

            if (!hasError) {
                if (!isValid) {
                    $('#' + element).closest('.control-mp-iframe').removeClass('in-focus');
                }
            }
        },

        /**
         * Toogle Validity State
         * @param {String} element
         * @returns {Jquery}
         */
        toogleValidityState(element, errorMessages) {
            var target = $('#' + element).closest('.mercadopago-input-group'),
                infoErro = $('#' + element).closest('.mercadopago-input-group').find('.field-error'),
                msg;

            if (infoErro.length) {
                infoErro.remove();
            }

            if (errorMessages.length)
            {
                _.map(errorMessages, (error) => {
                    msg = $t(error.message);
                });

                target.append('<div class="field-error"><span>' + msg + '</span></div>');
                return $('#' + element).closest('.control-mp-iframe').addClass('has-error').removeClass('is-valid');
            }
            return $('#' + element).closest('.control-mp-iframe').addClass('is-valid').removeClass('has-error');
        },

        /**
         * Toogle Focus Style
         * @param {String} element
         * @returns {void}
         */
        toogleFocusStyle(element) {
            $('#' + element).closest('.control-mp-iframe').addClass('in-focus');
        },

        /**
         * Is Active
         * @returns {Boolean}
         */
        isActive() {
            var active = this.getId() === this.isChecked();

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
            this.getCardIdDetails();
        },

        /**
         * Get card id details
         * @returns {void}
         */
        getCardIdDetails() {
            var self = this,
                payload;

            fullScreenLoader.startLoader();

            payload = {
                cardId: this.getMpPublicId()
            };

            window.mp.fields.createCardToken(payload).then((token) => {
                self.creditCardNumberToken(token.id);
                this.placeOrder();
            }).catch(() => {
                fullScreenLoader.startLoader();
            });

            console.log(payload);
        },

        /**
         * Add Finance Cost in totals
         * @returns {void}
         */
        addFinanceCost() {
            var self = this,
                selectInstallment = self.creditCardInstallment(),
                rulesForFinanceCost = self.creditCardListInstallments();

            setFinanceCost.financeCost(selectInstallment, rulesForFinanceCost);
        },

        /**
         * Get data
         * @returns {Object}
         */
        getData() {
            var self = this,
                data;

            data = {
                'method': self.getCode(),
                'additional_data': {
                    'payer_document_type': self.getPayerDocumentType(),
                    'payer_document_identification': self.getPayerDocumentNumber(),
                    'card_installments': self.creditCardInstallment(),
                    'card_number_token': self.creditCardNumberToken(),
                    'card_holder_name': self.getMpHolderName(),
                    'card_number': self.getMaskedCard(),
                    'card_type': self.getCardType(),
                    'public_hash': self.getToken(),
                    'mp_user_id': self.getMpUserId()
                }
            };

            return data;
        },

        /**
         * Get Code Cc Type
         * @returns {String}
         */
        getCodeCcType() {
            return this.creditCardType();
        },

        /**
         * Is show legend
         * @returns {Boolean}
         */
        isShowLegend() {
            return true;
        },

        /**
         * Get Token
         * @returns {String}
         */
        getToken() {
            return this.publicHash;
        },

        /**
         * Get Payer Document Type
         * @returns {String}
         */
        getPayerDocumentType() {
            return this.details['payer_document_type'];
        },

        /**
         * Get Payer Document Type
         * @returns {String}
         */
        getPayerDocumentNumber() {
            return this.details['payer_document_number'];
        },

        /**
         * Get Mp Public Id
         * @returns {String}
         */
        getMpPublicId() {
            return this.details['mp_public_id'];
        },

        /**
         * Get Mp User Id
         * @returns {String}
         */
        getMpUserId() {
            return this.details['mp_user_id'];
        },

        /**
         * Get Mp Holder Name
         * @returns {String}
         */
        getMpHolderName() {
            return this.details['card_holder_name'];
        },

        /**
         * Get masked card
         * @returns {String}
         */
        getMaskedCard() {
            return this.getCardFirstSix() + 'xxxxxx' + this.getCardLastFour();
        },

        /**
         * Get card type
         * @returns {String}
         */
        getCardType() {
            return this.details['card_type'];
        },

        /**
         * Get Card Last Four
         * @returns {String}
         */
        getCardLastFour() {
            return this.details['card_last4'];
        },

        /**
         * Get Card First Six
         * @returns {String}
         */
        getCardFirstSix() {
            return this.details['card_first6'];
        },

        /**
         * Has verification
         * @returns {Boolean}
         */
        hasVerification() {
            return window.checkoutConfig.payment[this.getCode()].useCvv;
        },

        /**
         * Get payment icons
         * @param {String} type
         * @returns {Boolean}
         */
        getIcons(type) {
            return window.checkoutConfig.payment[this.getCode()].icons.hasOwnProperty(type) ?
                window.checkoutConfig.payment[this.getCode()].icons[type]
                : false;
        },

        /**
         * Get List Options to Instalments
         * @returns {Array}
         */
        getListOptionsToInstallmentsVault() {
            var self = this,
                installments = {},
                bin = this.getCardFirstSix(),
                amount = self.amount();

            window.mp.getInstallments({
                amount: String(amount),
                bin: bin
            }).then((result) => {
                self.creditCardListInstallments(result[0].payer_costs);
            });

            return installments;
        }

    });
});
