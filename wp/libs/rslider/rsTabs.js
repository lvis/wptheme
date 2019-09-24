(function ($) {

    "use strict";

    /**
     * RS Module: Tabs
     * Useful for http://dimsemenov.com/plugins/royal-slider/content-slider/
     * @version 1.0.2:
     */
    $.extend($.rsProto, {
        _initTabs: function () {
            var self = this;
            if (self.settings.controlNavigation === 'tabs') {
                self.ev.on('rsBeforeParseNode', function (e, content, obj) {
                    content = $(content);
                    obj.thumbnail = content.find('.rsTmb').remove();
                    if (!obj.thumbnail.length) {

                        obj.thumbnail = content.attr('data-rsTmb');
                        if (!obj.thumbnail) {
                            obj.thumbnail = content.find('.rsImg').attr('data-rsTmb');
                        }
                        if (!obj.thumbnail) {
                            obj.thumbnail = '';
                        } else {
                            obj.thumbnail = '<img src="' + obj.thumbnail + '"/>';
                        }
                    } else {
                        obj.thumbnail = $(document.createElement('div')).append(obj.thumbnail).html();
                    }
                });
                self.ev.one('rsAfterPropsSetup', function () {
                    self._createTabs();
                });
                self.ev.on('rsOnAppendSlide', function (e, parsedSlide, index) {
                    if (index >= self.numSlides) {
                        self._controlNav.append('<div class="rsNavItem rsTab">' + parsedSlide.thumbnail + '</div>');
                    } else {
                        self._controlNavItems.eq(index).before('<div class="rsNavItem rsTab">' + item.thumbnail + '</div>');
                    }
                    self._controlNavItems = self._controlNav.children();
                });
                self.ev.on('rsOnRemoveSlide', function (e, index) {
                    var itemToRemove = self._controlNavItems.eq(index);
                    if (itemToRemove) {
                        itemToRemove.remove();
                        self._controlNavItems = self._controlNav.children();
                    }
                });
                self.ev.on('rsOnUpdateNav', function () {
                    var id = self.currSlideId,
                        currItem,
                        prevItem;
                    if (self._prevNavItem) {
                        self._prevNavItem.removeClass('rsNavSelected');
                    }

                    currItem = self._controlNavItems.eq(id);

                    currItem.addClass('rsNavSelected');
                    self._prevNavItem = currItem;
                });
            }

        },
        _createTabs: function () {
            var self = this;
            self._controlNavEnabled = true;
            var markupTabs = '<div class="rsNav rsTabs">';
            for (var i = 0; i < self.numSlides; i++) {
                markupTabs += '<div class="rsNavItem rsTab">' + self.slides[i].thumbnail + '</div>';
            }
            markupTabs += '</div>';
            markupTabs = $(markupTabs);
            self._controlNav = markupTabs;
            self._controlNavItems = markupTabs.children('.rsNavItem');
            self.slider.append(markupTabs);
            self._controlNav.click(function (event) {
                var item = $(event.target).closest('.rsNavItem');
                if (item.length) {
                    self.goTo(item.index());
                }
            });
        }
    });
    $.rsModules.tabs = $.rsProto._initTabs;
})(jQuery);
