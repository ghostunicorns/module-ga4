define(['GhostUnicorns_Ga4/js/actions/update-events'], function (updateEvents) {
    'use strict';

    return function (target) {
        return function (callbacks, deferred) {
            target(callbacks, deferred);
            updateEvents();
        }
    };
});
