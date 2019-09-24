(function ($) {

    "use strict";

    /**
     * RS Module: Global Caption
     * UseFull for Visible Nearby module only
     * @version 1.0.1:
     */
    $.extend($.rsProto, {
        _initGlobalCaption: function () {
            var self = this;
            if (self.settings.globalCaption) {
                var setCurrCaptionHTML = function () {
                    self.globalCaption.html(self.currSlide.caption || '');
                };
                self.ev.on('rsAfterInit', function () {
                    var captionContainer = self.slider;
                    var captionMarkup = '<div class="rsGCaption"></div>';
                    if (self.settings.globalCaptionInside) {
                        var captionMarkup = '<div class="rsGCaption rsGCaptionInside"></div>';
                        var i;
                        for (i = 0; i < self.slidesJQ.length; i++) {
                            var currentSlide = self.slidesJQ[i];
                            var currentSlideCaption = self.slides[i].caption;
                            if (currentSlideCaption) {
                                captionContainer = $(captionMarkup).appendTo(currentSlide);
                                captionContainer.html(currentSlideCaption);
                            }
                        }
                    } else {
                        self.globalCaption = $(captionMarkup).appendTo(captionContainer);
                        setCurrCaptionHTML();
                        self.ev.on('rsBeforeAnimStart', function () {
                            setCurrCaptionHTML();
                        });
                    }
                });
            }
        }
    });
    $.rsModules.globalCaption = $.rsProto._initGlobalCaption;
})(jQuery);