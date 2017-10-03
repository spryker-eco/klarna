/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var $ = require('jquery');
var paymentMethodKlarna = require('./payment-method');

$(document).ready(function() {
    paymentMethodKlarna.init({
        formSelector: '#payment-form',
        paymentMethodSelector: '#paymentForm_paymentSelection input[type="radio"]',
        currentPaymentMethodSelector: '#paymentForm_paymentSelection input[type="radio"]:checked'
    });
});
