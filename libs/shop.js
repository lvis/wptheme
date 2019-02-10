/* global wc_cart_params */
var vmShop;
jQuery(function ($) {
    function ShopViewModel() {
        var self = this;
        var urlAjaxWC = '';
        var nonceCouponApply = '';
        var nonceCouponRemove = '';
        var params = undefined;
        var pendingAlerts = '';
        var $alertContainer = $('.woocommerce-notices-wrapper:first') || $('.cart-empty').closest('.woocommerce');
        if (typeof wc_cart_params !== 'undefined') {
            params = wc_cart_params;
        }
        if (typeof wc_checkout_params !== 'undefined') {
            params = wc_checkout_params;
        }
        console.log(params);
        console.log('cart params', params);
        if (params) {
            urlAjaxWC = params.wc_ajax_url;
            nonceCouponApply = params.apply_coupon_nonce;
            nonceCouponRemove = params.remove_coupon_nonce;
        }
        self.getUrl = function (endpoint) {
            return urlAjaxWC.replace('%%endpoint%%', endpoint);
        };
        self.urlActionCart = ko.observable();
        self.isBlocked = ko.observable(false);
        self.cssForm = ko.pureComputed(function () {
            return this.isBlocked() ? 'loader-overlay' : '';
        }, this);
        self.couponCode = ko.observable('');
        self.hasCouponCode = ko.pureComputed(function () {
            var hasCoupon = false;
            if (this.couponCode()){
                hasCoupon = true;
            }
           return hasCoupon;
        }, this);
        var addAlert = function (code) {
            if (code) {
                if (pendingAlerts) {
                    pendingAlerts = code + pendingAlerts;
                } else {
                    pendingAlerts = code;
                }
            }
        };
        var sendRequest = function (request) {
            $alertContainer.empty();
            self.isBlocked(true);
            $.ajax(request);
        };
        var handleCartUpdateSuccess = function (response) {
            var $htmlResponse = $.parseHTML(response);
            var $htmlCart = $('.cart', $htmlResponse);
            if ($htmlCart.length !== 0) {
                var $htmlAlerts = $('.woocommerce-error, .woocommerce-message, .woocommerce-info', $htmlResponse);
                if ($alertContainer) {
                    if (pendingAlerts) {
                        $alertContainer.prepend(pendingAlerts);
                        pendingAlerts = '';
                    }
                    if ($htmlAlerts) {
                        $alertContainer.prepend($htmlAlerts);
                    }
                }
                $('.cart').replaceWith($htmlCart);
                $htmlResponse = $('.cart_totals', $htmlResponse);
                $('.cart_totals').replaceWith($htmlResponse);
                $( document.body ).trigger( 'updated_cart_totals' );
                ko.cleanNode(document.body);
                ko.applyBindings(self, document.body);
            } else {
                $htmlCart = $('.cart-empty', $htmlResponse).closest('.woocommerce');
                $('.woocommerce-cart-form').closest('.woocommerce').replaceWith($htmlCart);
            }
            console.log('trigger updated_wc_div');
            $( document.body ).trigger( 'updated_wc_div' );
        };
        var handleCartUpdateComplete = function () {
            self.isBlocked(false);
            $.scroll_to_notices($('[role="alert"]'));
        };
        self.handleChangeCartProductQuantity = function (data, event) {
            var form = event.currentTarget.form;
            if (params && form) {
                var $form = $(form);
                $( '<input />' ).attr( 'type', 'hidden' )
                    .attr( 'name', 'update_cart' )
                    .attr( 'value', 'Update Cart' )
                    .appendTo( $form );
                var formData = $form.serialize();
                sendRequest({
                    type: form.method,
                    url: form.action,
                    data: formData,
                    dataType: 'html',
                    success: handleCartUpdateSuccess,
                    complete: handleCartUpdateComplete
                });
            }
        };
        self.handleClickCouponAdd = function (data, event) {
            var form = event.currentTarget.form;
            if (params && form) {
                self.isBlocked(true);
                var requestData = {
                    security: nonceCouponApply,
                    coupon_code: self.couponCode()
                };
                sendRequest({
                    url: self.getUrl('apply_coupon'),
                    data: requestData,
                    type: 'POST',
                    dataType: 'html',
                    success: function (response) {
                        addAlert(response);
                    },
                    complete: function () {
                        self.couponCode('');
                        var formData = $(form).serialize();

                        sendRequest({
                            type: form.method,
                            url: form.action,
                            data: formData,
                            dataType: 'html',
                            success: handleCartUpdateSuccess,
                            complete: handleCartUpdateComplete
                        });
                    }
                });
            }
        };
        self.handleKeyPressAddCoupon = function (data, event) {
            var keyCode = (event.which ? event.which : event.keyCode);
            if (keyCode === 13) {
                self.handleClickCouponAdd(data, event);
            } else {
                return true
            }
        };
        self.handleClickCouponRemove = function (data, event) {
            sendRequest({
                url: event.currentTarget.href,
                type: 'GET',
                dataType: 'html',
                success:  handleCartUpdateSuccess,
                complete: handleCartUpdateComplete
            });
        };
        self.handleClickCartProductRemove = function (data, event) {
            sendRequest({
                url: event.currentTarget.href,
                type: 'GET',
                dataType: 'html',
                success: handleCartUpdateSuccess,
                complete: handleCartUpdateComplete
            });
        };
        $(document).on('click', '.restore-item', function(event){
            sendRequest({
                url: event.currentTarget.href,
                type: 'GET',
                dataType: 'html',
                success: handleCartUpdateSuccess,
                complete: handleCartUpdateComplete
            });
        });
    }

    vmShop = new ShopViewModel();
    ko.applyBindings(vmShop);
    $(document.body).trigger( 'updated_wc_div' );
});