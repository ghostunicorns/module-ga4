define([
    'jquery',
    'Magento_Checkout/js/view/payment',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/quote'
], function ($, payment, totals, quote) {
    'use strict';

    function notify(cart, stepIndex, stepDescription) {
        var i = 0,
            product,
            subtotal = parseFloat(totals.totals()['subtotal']),
            dlUpdate = {
                'event': stepDescription,
                'ecommerce': {
                    'currency': window.dlCurrencyCode,
                    'value': subtotal,
                    'items': [ ]
                }
            };

        if (stepIndex === '2') {
            var shippingTier = quote.shippingMethod().method_code;
            if (!shippingTier) {
                return false;
            }
            dlUpdate.ecommerce.shipping_tier = shippingTier;
        }

        if (stepIndex === '3') {
            var paymentMethod = quote.paymentMethod().method;
            if (!paymentMethod) {
                return false;
            }
            dlUpdate.ecommerce.payment_type = paymentMethod;
        }

        for (i; i < cart.length; i++) {
            product = cart[i];
            dlUpdate.ecommerce.items.push({
                'item_id': product.item_id,
                'item_name': product.name,
                'price': product.price,
                'quantity': product.qty,
                'item_brand': product.manufacturer,
                'item_variant': product.item_variant,
                'item_size': product.item_size,
                'item_category': product.item_category,
                'item_category2': product.item_category2,
                'item_category3': product.item_category3,
                'item_category4': product.item_category4,
                'item_category5': product.item_category5
            });
        }

        window.dataLayer.push(dlUpdate);
        return true;
    }

    return function (data) {
        var events = {
                begin: {
                    description: 'begin_checkout',
                    index: '1'
                },
                shipping: {
                    description: 'add_shipping_info',
                    index: '2'
                },
                payment: {
                    description: 'add_payment_info',
                    index: '3'
                }
            },
            subscriptionShipping = payment.prototype.isVisible.subscribe(function (value) {
                if (value && window.dataLayer) {
                    if (notify(data.cart, events.shipping.index, events.shipping.description)) {
                        subscriptionShipping.dispose();
                    }
                }
            }),
            subscriptionPayment = quote.paymentMethod.subscribe(function (value) {
                if (value && window.dataLayer) {
                    if (notify(data.cart, events.payment.index, events.payment.description)) {
                        subscriptionPayment.dispose();
                    }
                }
            }, null, 'change');


        window.dataLayer ?
            notify(data.cart, events.begin.index, events.begin.description) :
            $(document).on(
                'ga:inited',
                notify.bind(this, data.cart, events.begin.index, events.begin.description)
            );
    };

});
