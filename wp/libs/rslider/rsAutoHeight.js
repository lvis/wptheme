(function ($) {

    "use strict";

    /**
     * RS Module: AutoHeight
     * Useful for http://dimsemenov.com/plugins/royal-slider/content-slider/
     * @version 1.0.3:
     */
    $.extend($.rsProto, {
        _initAutoHeight: function () {
            var self = this;
            if (self.settings.autoHeight) {
                var firstTime = true;
                var updateHeight = function (animate) {
                    var currentSlide = self.slides[self.currSlideId];
                    var holder = currentSlide.holder;
                    if (holder) {
                        var holderHeight = holder.height();
                        if (holderHeight && holderHeight > (self.settings.minAutoHeight || 30)) {
                            self._wrapHeight = holderHeight;
                            if (self._useCSS3Transitions || !animate) {
                                self._sliderOverflow.css('height', holderHeight);
                            } else {
                                self._sliderOverflow.stop(true, true).animate({height: holderHeight}, self.settings.transitionSpeed);
                            }
                            self.ev.trigger('rsAutoHeightChange', holderHeight);
                            if (firstTime) {
                                // Apply CSS transitons
                                if (self._useCSS3Transitions) {
                                    // force reflow
                                    setTimeout(function () {
                                        var cssPropName = self._vendorPref + 'transition';
                                        var cssPropValue = 'height ' + self.settings.transitionSpeed + 'ms ease-in-out';
                                        self._sliderOverflow.css(cssPropName, cssPropValue);
                                    }, 16);
                                }
                                firstTime = false;
                            }
                        }
                    }
                };

                self.ev.on('rsMaybeSizeReady.rsAutoHeight', function (e, slideObject) {
                    if (currentSlide === slideObject) {
                        updateHeight();
                    }
                });
                self.ev.on('rsAfterContentSet.rsAutoHeight', function (e, slideObject) {
                    if (currentSlide === slideObject) {
                        updateHeight();
                    }
                });
                self.slider.addClass('rsAutoHeight');
                self.ev.one('rsAfterInit', function () {
                    setTimeout(function () {
                        updateHeight(false);
                        setTimeout(function () {
                            self.slider.append('<div style="clear:both; float: none;"></div>');
                        }, 16);
                    }, 16);
                });
                self.ev.on('rsBeforeAnimStart', function () {
                    updateHeight(true);
                });
                self.ev.on('rsBeforeSizeSet', function () {
                    setTimeout(function () {
                        updateHeight(false);
                    }, 16);
                });
            }
        }
    });
    $.rsModules.autoHeight = $.rsProto._initAutoHeight;
})(jQuery);