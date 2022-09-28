define([
    'jquery',
    'Magento_GoogleTagManager/js/google-tag-manager'
], function ($) {
    'use strict';

    /**
     * Dispatch product detail event to GA
     *
     * @param {Object} data - product data
     *
     * @private
     */
    function notify(data) {
        window.dataLayer.push({
            'event': 'view_item',
            'ecommerce': {
                'currency': GA4.currencyCode,
                'items': [data]
            }
        });
    }

    return function (productData) {
        window.dataLayer ?
            notify(productData) :
            $(document).on('ga:inited', notify.bind(this, productData));
    };
});
