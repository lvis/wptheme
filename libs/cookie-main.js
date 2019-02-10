(function ($) {
    // ready event
    $(document).ready(function () {
        var notice = $('#ocn-cookie-wrap');
        var overlay = $('#ocn-cookie-overlay');
        var cookie = $.fn.ocnGetCookieNotice();
        // handle set-cookie button click
        $(document).on('click', '#ocn-cookie-wrap .ocn-close', function (e) {
            e.preventDefault();
            var date = new Date(),
                later_date = new Date(),
                notice = $('#ocn-cookie-wrap'),
                overlay = $('#ocn-cookie-overlay');
            // set expiry time in seconds
            later_date.setTime(parseInt(date.getTime()) + parseInt(oceanwpLocalize.cookieTime) * 1000);
            // set cookie
            document.cookie = oceanwpLocalize.cookieName + '=true;expires=' + later_date.toUTCString() + ';' + (oceanwpLocalize.cookieDomain !== undefined && oceanwpLocalize.cookieDomain !== '' ? 'domain=' + oceanwpLocalize.cookieDomain + ';' : '') + (oceanwpLocalize.cookiePath !== undefined && oceanwpLocalize.cookiePath !== '' ? 'path=' + oceanwpLocalize.cookiePath + ';' : '') + (oceanwpLocalize.secure === '1' ? 'secure;' : '');
            // trigger custom event
            $.event.trigger({
                type: 'ocnSetCookieNotice',
                value: 'true',
                time: date,
                expires: later_date
            });
            notice.ocnHideCookieNotice();
            if (oceanwpLocalize.overlay === 'yes') {
                overlay.ocnHideCookieOverlay();
                $('html').css('overflow-y', 'auto');
            }
            if (oceanwpLocalize.reload === 'yes') {
                var url = window.location.protocol + '//',
                    hostname = window.location.host + '/' + window.location.pathname;
                if (oceanwpLocalize.cache === '1') {
                    url = url + hostname.replace('//', '/') + (window.location.search === '' ? '?' : window.location.search + '&') + 'cn-reloaded=1' + window.location.hash;
                    window.location.href = url;
                } else {
                    url = url + hostname.replace('//', '/') + window.location.search + window.location.hash;
                    window.location.reload(true);
                }
                return;
            }
        });
        // Cookie is not set
        if (typeof cookie === 'undefined') {
            notice.ocnShowCookieNotice();
            if (oceanwpLocalize.overlay === 'yes') {
                overlay.ocnShowCookieOverlay();
                $('html').css('overflow-y', 'hidden');
            }
        }
    });
    // Get cookie value
    $.fn.ocnGetCookieNotice = function () {
        var value = "; " + document.cookie;
        var parts = value.split('; ocn_accepted=');
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        } else {
            return;
        }
    };
    // Display cookie notice
    $.fn.ocnShowCookieNotice = function () {
        this.slideDown(300);
    };
    // Hide cookie notice
    $.fn.ocnHideCookieNotice = function () {
        this.slideUp(300);
    };
    // Display cookie overlay
    $.fn.ocnShowCookieOverlay = function () {
        this.fadeIn(300);
    };
    // Hide cookie overlay
    $.fn.ocnHideCookieOverlay = function () {
        this.fadeOut(300);
    }
})(jQuery);