define([
    'jquery',
    'mage/url',
    'Magento_GoogleTagManager/js/google-tag-manager'
], function ($, url) {
    'use strict';

    function update() {
        $.ajax({
            url: url.build('ga4/events'),
            dataType: 'json',
            cache: false,
            success: function (res) {
                if (!res.hasOwnProperty('events')) {
                    return;
                }

                var events = res.events;
                events.forEach((event) => {
                    window.dataLayer.push(JSON.parse(event));
                });
            },
            error: console.log
        });
    }

    return function () {
        window.dataLayer ?
            update() :
            $(document).on('ga:inited', update.bind(this));
    };
});
