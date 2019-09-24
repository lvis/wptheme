(function ($) {

    "use strict";

    /**
     * RS Module: VisibleNearby
     * @version 1.0.2:
     */
    $.rsProto._initVisibleNearby = function () {
        var self = this;
        if (self.settings.visibleNearby && self.settings.visibleNearby.enabled) {
            self._vnDefaults = {
                enabled: true,
                centerArea: 0.6, // Area of center image. By default 60% will get center image, 20% for each image on side
                center: true,
                breakpoint: 0, // this option will be trigger change of centerArea parameter
                breakpointCenterArea: 0.8,
                hiddenOverflow: true,
                navigateByCenterClick: false
            };
            self.settings.visibleNearby = $.extend({}, self._vnDefaults, self.settings.visibleNearby);
            self.ev.one('rsAfterPropsSetup', function () {
                self._sliderVisibleNearbyWrap = self._sliderOverflow.css('overflow', 'visible').wrap('<div class="rsVisibleNearbyWrap"></div>').parent();
                if (!self.settings.visibleNearby.hiddenOverflow) {
                    self._sliderVisibleNearbyWrap.css('overflow', 'visible');
                }
                self._controlsContainer = self.settings.controlsInside ? self._sliderVisibleNearbyWrap : self.slider;
            });

            self.ev.on('rsAfterSizePropSet', function () {
                var optionVisibleNearby = self.settings.visibleNearby;
                var centerRatio = optionVisibleNearby.centerArea;
                if (optionVisibleNearby.breakpoint && self.width < optionVisibleNearby.breakpoint) {
                    centerRatio = optionVisibleNearby.breakpointCenterArea;
                }
                if (self._slidesHorizontal) {
                    self._wrapWidth = self._wrapWidth * centerRatio;
                    self._sliderVisibleNearbyWrap.css({
                        height: self._wrapHeight,
                        width: self._wrapWidth / centerRatio
                    });
                    self._minPosOffset = self._wrapWidth * (1 - centerRatio) / 2 / centerRatio;
                } else {
                    self._wrapHeight = self._wrapHeight * centerRatio;
                    self._sliderVisibleNearbyWrap.css({
                        height: self._wrapHeight / centerRatio,
                        width: self._wrapWidth
                    });
                    self._minPosOffset = self._wrapHeight * (1 - centerRatio) / 2 / centerRatio;
                }
                if (!optionVisibleNearby.navigateByCenterClick) {
                    self._nextSlidePos = self._slidesHorizontal ? self._wrapWidth : self._wrapHeight;
                }
                if (optionVisibleNearby.center) {
                    self._sliderOverflow.css('margin-' + (self._slidesHorizontal ? 'left' : 'top'), self._minPosOffset);
                }
            });

        }
    };
    $.rsModules.visibleNearby = $.rsProto._initVisibleNearby;
})(jQuery);
