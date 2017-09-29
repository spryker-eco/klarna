/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved. 
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file. 
 */

'use strict';

var $ = require('jquery');

function init(config) {

    var form = $(config.formSelector);

    form.submit(function( event ) {
        var paymentMethod = getCurrentPaymentMethod();
        var hasErrorsGlobal = false;
        var element, hasErrors, errorMessage;

        $('.klarna-error').remove();
        $('#paymentForm_klarna_invoice input').css("background-color", "white");
        $('#paymentForm_klarna_installment input').css("background-color", "white");

        if (paymentMethod == 'klarnaInvoice') {

            element = "#paymentForm_klarna_invoice_date_of_birth";
            hasErrors = false; errorMessage = "";
            if (!isText(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgDateBirthMandatory);
            }
            if (!isDate(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgDateBirthWrong);
            }
            showErrors(element, hasErrors, errorMessage);

            element = "#paymentForm_klarna_invoice_pno_no";
            hasErrors = false; errorMessage = "";
            if (!isText(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgPNOMandatory);
            }
            showErrors(element, hasErrors, errorMessage);

            element = "#paymentForm_klarna_invoice_terms";
            hasErrors = false; errorMessage = "";
            if (!isCheckBox(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgTermsMandatory);
            }
            showErrors(element, hasErrors, errorMessage);


        } else if (paymentMethod == 'klarnaInstallment') {

            element = "#paymentForm_klarna_installment_installment_date_of_birth";
            hasErrors = false; errorMessage = "";
            if (!isText(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgDateBirthMandatory);
            }
            if (!isDate(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgDateBirthWrong);
            }
            showErrors(element, hasErrors, errorMessage);

            element = "#paymentForm_klarna_installment_pno_no";
            hasErrors = false; errorMessage = "";
            if (!isText(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgPNOMandatory);
            }
            showErrors(element, hasErrors, errorMessage);

            element = "#paymentForm_klarna_installment_installment_terms";
            hasErrors = false; errorMessage = "";
            if (!isCheckBox(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgTermsMandatory);
            }
            showErrors(element, hasErrors, errorMessage);

            element = "#paymentForm_klarna_installment_installment_pay_index";
            hasErrors = false; errorMessage = "";
            if (!isRadio(element)) {
                hasErrors = hasErrorsGlobal = true;
                errorMessage += addError(errorMsgPaymentTypeMandatory);
            }
            showErrors(element, hasErrors, errorMessage);

        }

        if (hasErrorsGlobal) {
            event.preventDefault();
            return false;
        }
        return true;
    });

    function showErrors(element, hasErrors, errorMessage) {
        if (hasErrors) {
            $(element).css("background-color", "red");
            $(element).parent().prepend('<div class="klarna-error field"><ul class="form-errors">' + errorMessage + '</ul></div>');
        }
    }

    function getCurrentPaymentMethod() {
        return $(config.currentPaymentMethodSelector).val();
    }

    function isText(selector) {
        var field = $(selector);

        if (!field.length) {
            return true;
        }

        return field.val().length > 0;
    }

    function isDate(selector) {
        var field = $(selector);

        if (!field.length) {
            return true;
        }

        return field.val().match(/\d{2}\.\d{2}\.\d{4}/gm)?true:false;
    }

    function isCheckBox(selector) {
        var field = $(selector);

        if (!field.length) {
            return true;
        }

        return field.is(':checked');
    }

    function isRadio(selector) {
        var field = $(selector);
        if (!field.length) {
            return true;
        }

        var selectedItem = $(selector + ' input[type="radio"]:checked');

        return selectedItem?true:false;
    }

    function addError(message) {
        return '<li>' + message + '</li>'
    }

}

module.exports = {
    init: init
};
