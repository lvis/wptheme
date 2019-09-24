(function ($) {

    "use strict";

    /**
     * RS Module: ActiveClass
     * @version 1.0.1:
     */
    $.extend($.rsProto, {
        _initActiveClass: function () {
            var self = this;
            if (self.settings.addActiveClass) {
                var idTimerUpdateClass;
                var classActiveSlide = 'rsActiveSlide';
                self.ev.on('rsOnUpdateNav', function () {
                    if (idTimerUpdateClass) {
                        clearTimeout(idTimerUpdateClass);
                    }
                    idTimerUpdateClass = setTimeout(function () {
                        if (self._oldHolder) {
                            self._oldHolder.removeClass(classActiveSlide);
                        }
                        if (self._currHolder) {
                            self._currHolder.addClass(classActiveSlide);
                        }
                        idTimerUpdateClass = null;
                    }, 50);
                });
            }
        }
    });
    $.rsModules.activeClass = $.rsProto._initActiveClass;
})(jQuery);
