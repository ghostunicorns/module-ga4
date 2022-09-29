define([
    'jquery',
    'mage/url'
], function ($, url) {
    'use strict';

    return function () {
        $.ajax({
            url: url.build('ga4/showminicart'),
            dataType: 'json',
            cache: false,
            success: function (res) {
                if (
                    !res.hasOwnProperty('event') ||
                    res.event !== 'view_cart'
                ) {
                    return;
                }
                window.dataLayer.push(res);
            },
            error: console.log
        });
    };
});
