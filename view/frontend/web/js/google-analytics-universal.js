define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    /**
     * Google analytics universal class
     *
     * @param {Object} config
     */
    function GoogleAnalyticsUniversal(config) {
        this.blockNames = config.blockNames;
        this.dlCurrencyCode = config.dlCurrencyCode;
        this.dataLayer = config.dataLayer;
        this.staticImpressions = config.staticImpressions;
        this.staticPromotions = config.staticPromotions;
        this.updatedImpressions = config.updatedImpressions;
        this.updatedPromotions = config.updatedPromotions;
    }

    GoogleAnalyticsUniversal.prototype = {

        /**
         * Active on category action
         *
         * @param {String} id
         * @param {String} name
         * @param {String} category
         * @param {Object} list
         * @param {String} position
         */
        activeOnCategory: function (id, name, category, list, position) {
            this.dataLayer.push({
                'event': 'select_item',
                'ecommerce': {
                    'currency': GA4.currencyCode,
                    'items': [{
                        'item_id': id,
                        'item_name': name,
                        'item_category': category,
                        'list': list,
                        'position': position
                    }]
                }
            });
        },

        /**
         * Active on products action
         *
         * @param {String} id
         * @param {String} name
         * @param {Object} list
         * @param {String} position
         * @param {String} category
         */
        activeOnProducts: function (id, name, list, position, category) {
            this.dataLayer.push({
                'event': 'select_item',
                'ecommerce': {
                    'currency': GA4.currencyCode,
                    'items': [{
                        'item_id': id,
                        'item_name': name,
                        'list': list,
                        'position': position,
                        'item_category': category
                    }]
                }
            });
        },

        /**
         * Add to cart action
         *
         * @param {String} id
         * @param {String} name
         * @param {Number} price
         * @param {String} discount
         * @param {String} quantity
         * @param {String} manufacturer
         * @param {String} item_variant
         * @param {String} item_size
         * @param {String} item_category
         * @param {String} item_category2
         * @param {String} item_category3
         * @param {String} item_category4
         * @param {String} item_category5
         */
        addToCart: function (id,
                             name,
                             price,
                             discount,
                             quantity,
                             manufacturer,
                             item_variant,
                             item_size,
                             item_category,
                             item_category2,
                             item_category3,
                             item_category4,
                             item_category5) {
            this.dataLayer.push({
                'event': 'add_to_cart',
                'ecommerce': {
                    'currency': this.dlCurrencyCode,
                    'items': [{
                        'item_id': id,
                        'item_name': name,
                        'price': parseFloat(price),
                        'discount': discount,
                        'quantity': quantity,
                        'item_brand': manufacturer,
                        'item_variant': item_variant,
                        'item_size': item_size,
                        'item_category': item_category,
                        'item_category2': item_category2,
                        'item_category3': item_category3,
                        'item_category4': item_category4,
                        'item_category5': item_category5
                    }]
                }
            });
        },

        /**
         * Remove from cart action
         *
         * @param {String} id
         * @param {String} name
         * @param {String} price
         * @param {String} quantity
         */
        removeFromCart: function (id, name, price, quantity) {
            this.dataLayer.push({
                'event': 'remove_from_cart',
                'ecommerce': {
                    'currencyCode': this.dlCurrencyCode,
                    'items': [{
                        'item_id': id,
                        'item_name': name,
                        'price': parseFloat(price),
                        'quantity': quantity
                    }]
                }
            });
        },

        /**
         * Click banner action
         *
         * @param {String} id
         * @param {String} name
         * @param {String} creative
         * @param {String} position
         */
        clickBanner: function (id, name, creative, position) {
            this.dataLayer.push({
                'event': 'select_promotion',
                'ecommerce': {
                    'currency': GA4.currencyCode,
                    'promoClick': {
                        'promotions': [{
                            'promotion_id': id,
                            'promotion_name': name,
                            'creative_name': creative,
                            'creative_slot': position
                        }]
                    }
                }
            });
        },

        /**
         * Bind impression click
         *
         * @param {String} id
         * @param {String} type
         * @param {String} name
         * @param {String} category
         * @param {Object} list
         * @param {String} position
         * @param {String} blockType
         * @param {String} listPosition
         */
        bindImpressionClick: function (id, type, name, category, list, position, blockType, listPosition) {
            var productLink = [],
                eventBlock;

            switch (blockType)  {
                case 'catalog.product.related':
                    eventBlock = '.products-related .products';
                    break;

                case 'product.info.upsell':
                    eventBlock = '.products-upsell .products';
                    break;

                case 'checkout.cart.crosssell':
                    eventBlock = '.products-crosssell .products';
                    break;

                case 'category.products.list':
                case 'search_result_list':
                    eventBlock = '.products .products';
                    break;

            }

            productLink = $(eventBlock + ' .item:nth(' + listPosition + ') a');

            if (type === 'configurable' || type === 'bundle' || type === 'grouped') {
                productLink = $(
                    eventBlock + ' .item:nth(' + listPosition + ') .tocart,' +
                    eventBlock + ' .item:nth(' + listPosition + ') a'
                );
            }

            productLink.each(function (index, element) {
                $(element).on('click', function () {
                    // Product category cannot be detected properly if customer is not on category page
                    if (blockType !== 'category.products.list') {
                        category = '';
                    }

                    this.activeOnProducts(
                        id,
                        name,
                        list,
                        position,
                        category);
                }.bind(this));
            }.bind(this));
        },

        /**
         * Update impressions
         */
        updateImpressions: function () {
            var pageImpressions = this.mergeImpressions(),
                dlImpressions = {
                    'event': 'view_item_list',
                    'ecommerce': {
                        'currency': GA4.currencyCode,
                        'items': []
                    }
                },
                i = 0,
                impressionCounter = 0,
                impression,
                blockName;

            for (blockName in pageImpressions) {
                // jscs:disable disallowKeywords
                if (blockName === 'length' || !pageImpressions.hasOwnProperty(blockName)) {
                    continue;
                }

                // jscs:enable disallowKeywords

                for (i; i < pageImpressions[blockName].length; i++) {
                    impression = pageImpressions[blockName][i];
                    dlImpressions.ecommerce.items.push({
                        'item_id': impression.id,
                        'item_name': impression.name,
                        'item_category': impression.category,
                        'list': impression.list,
                        'position': impression.position
                    });
                    impressionCounter++;
                    this.bindImpressionClick(
                        impression.id,
                        impression.type,
                        impression.name,
                        impression.category,
                        impression.list,
                        impression.position,
                        blockName,
                        impression.listPosition
                    );
                }
            }

            if (impressionCounter > 0) {
                this.dataLayer.push(dlImpressions);
            }
        },

        /**
         * Merge impressions
         */
        mergeImpressions: function () {
            var pageImpressions = [];

            this.blockNames.forEach(function (blockName) {
                // check if there is a new block generated by FPC placeholder update
                if (blockName in this.updatedImpressions) {
                    pageImpressions[blockName] = this.updatedImpressions[blockName];
                } else if (blockName in this.staticImpressions) { // use the static data otherwise
                    pageImpressions[blockName] = this.staticImpressions[blockName];
                }
            }, this);

            return pageImpressions;
        },

        /**
         * Update promotions
         */
        updatePromotions: function () {
            var dlPromotions = {
                    'event': 'view_promotion',
                    'ecommerce': {
                        'currency': GA4.currencyCode,
                        'promoView': {
                            'promotions': []
                        }
                    }
                },
                pagePromotions = [],
                promotionCounter = 0,
                bannerIds = [],
                i = 0,
                promotion,
                self = this;

            // check if there is a new block generated by FPC placeholder update
            if (this.updatedPromotions.length) {
                pagePromotions = this.updatedPromotions;
            }

            // use the static data otherwise
            if (!pagePromotions.length && this.staticPromotions.length) {
                pagePromotions = this.staticPromotions;
            }

            if ($('[data-banner-id]').length) {
                _.each($('[data-banner-id]'), function (banner) {
                    var $banner = $(banner),
                        ids = ($banner.data('ids') + '').split(',');

                    bannerIds = $.merge(bannerIds, ids);
                });
            }

            bannerIds = $.unique(bannerIds);

            for (i; i < pagePromotions.length; i++) {
                promotion = pagePromotions[i];

                // jscs:disable disallowKeywords
                /* eslint-disable eqeqeq */
                if ($.inArray(promotion.id, bannerIds) == -1 || promotion.activated == '0') {
                    continue;
                }

                // jscs:enable disallowKeywords
                /* eslint-enable eqeqeq */

                dlPromotions.ecommerce.promoView.promotions.push({
                    'item_id': promotion.id,
                    'item_name': promotion.name,
                    'creative': promotion.creative,
                    'position': promotion.position
                });
                promotionCounter++;
            }

            if (promotionCounter > 0) {
                this.dataLayer.push(dlPromotions);
            }

            $('[data-banner-id]').on('click', '[data-banner-id]', function () {
                var bannerId = $(this).attr('data-banner-id'),
                    promotions = _.filter(pagePromotions, function (item) {
                        return item.id === bannerId;
                    });

                _.each(promotions, function (promotionItem) {
                    self.clickBanner(
                        promotionItem.id,
                        promotionItem.name,
                        promotionItem.creative,
                        promotionItem.position
                    );
                });
            });
        }
    };

    return GoogleAnalyticsUniversal;
});
