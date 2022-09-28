var config = {
    map: {
        '*': {
            'Magento_GoogleTagManager/js/actions/checkout': 'GhostUnicorns_Ga4/js/actions/checkout'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'GhostUnicorns_Ga4/js/swatch-renderer-mixin': true
            },
            'Magento_Checkout/js/action/get-totals': {
                'GhostUnicorns_Ga4/js/actions/get-totals-mixin': true
            }
        }
    }
};
