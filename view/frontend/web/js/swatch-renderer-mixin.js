define(['jquery'], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _UpdatePrice: function () {
                this._super();
                (function (callback) {})((function (context) {
                    if (context && 'undefined' !== typeof context.getProduct()) {
                        var simple = {}, key = context.getProduct().toString();

                        if (GA4.configurableProducts.hasOwnProperty(key)) {
                            simple = GA4.configurableProducts[key];
                        }

                        return function () {
                            if(!Object.keys(simple).length){
                                return;
                            }
                            dataLayer.push({
                                'event': 'view_item',
                                'ecommerce': {
                                    'currency': GA4.currencyCode,
                                    'items': [simple]
                                }
                            });
                        }
                    } else {
                        return function () {
                            dataLayer.push({'event': 'resets_swatch_selection'});
                        }
                    }

                })(this));
            }
        });

        return $.mage.SwatchRenderer;
    }
});
