var yWidget = {
    id: '11510',
    isNew: '1',
    href: 'https://y11510.yclients.com/',
    script_href: 'https://w11510.yclients.com/',
    lang: 'rus',
    showNewWidgetAutomatically: false,
    counters: {yaCounterId: '47358309'},
    formPosition: 'right',
    block: null,
    cover: null,
    iFrame: null,
    closeIcon: null,
    mobileEvent: ('ontouchstart' in window),
    isMobile: {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPod|iPad/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (yWidget.isMobile.Android() || yWidget.isMobile.BlackBerry() || yWidget.isMobile.iOS() || yWidget.isMobile.Opera() || yWidget.isMobile.Windows() || (window.innerWidth <= 600));
        }
    },
    clickevent: function() {
        //Check if it is mobile or tablet
        var check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
        return check ? 'touchend' : 'click';
    },
    addCSS: function() {
        var ss = document.createElement('link');
        ss.type = "text/css";
        ss.rel = "stylesheet";
        //ss.href =  yWidget.script_href+"https://w11510.yclients.com/css/ywidget/newweb.css";
        ss.href =  "https://mayfairbeauty.ru/wp-content/themes/wptheme/libs/widgetJS.css";
        document.getElementsByTagName('head')[0].appendChild(ss);
    },
    isHasCookie: function (cookie) {
        return cookie.indexOf('yextrafield_') !== -1;
    },
    getYextraCookie: function () {
        return document.cookie.split(';').reduce(function (cookies, cookie) {
            var cookiesAsArray = cookie.split('=').map(function (c) { return c.trim() });
            var name = cookiesAsArray[0];
            var value = cookiesAsArray[1];
            if (yWidget.isHasCookie(name) === true) {
                cookies[name] = value;
            }
            return cookies;
        }, {});
    },
    buildSearchQuery: function(button) {
        var cookie = yWidget.getCookie('_ga');
        var gcid = '';
        if (cookie !== undefined) {
            try {
                var clientId = cookie.split('.')[2] + '.' + cookie.split('.')[3];
                gcid =  'gcid=' + clientId;
            } catch (e) {
                console.error(e);
            }
        }
        var yextrafields = '';
        if (yWidget.isHasCookie(document.cookie) === true) {
            var cookies = yWidget.getYextraCookie();
            var i = 0;
            for (var k in cookies) {
                if(cookies.hasOwnProperty(k)) {
                    yextrafields += k + '=' + cookies[k] + (i !== (Object.keys(cookies).length - 1) ? '&' : '');
                }
                i += 1;
            }
        }
        return [gcid, yextrafields].filter(function(str) {
            return str && str.length;
        }).join('&')
    },
    createWidgetBlock: function() {
        var block = document.createElement('div');
        block.className = 'yWidgetBlock';
        block.style.zIndex = yWidget.getMaxZIndex('body');
        yWidget.addClass(yWidget.formPosition, block);
        document.getElementsByTagName('body')[0].appendChild(block);
        yWidget.iFrame = yWidget.createIFrame();
        block.appendChild(yWidget.iFrame);
        return block;
    },
    createWindowCover: function() {
        var cover = document.createElement('div');
        cover.className = 'yWidgetCover';
        cover.style.zIndex = yWidget.getMaxZIndex('body');
        cover.addEventListener('click', function(e) {
            e.preventDefault();
            yWidget.hide();
        }, false);
        document.getElementsByTagName('body')[0].appendChild(cover);
        return cover;
    },
    createIFrame: function() {
        var iFrame = document.createElement('iframe');
        iFrame.className = 'yWidgetIFrame';
        iFrame.setAttribute('frameborder', 0);
        iFrame.setAttribute('allowtransparency', 'true');
        iFrame.src = yWidget.href;
        return iFrame;
    },
    createCloseIcon: function() {
        var button = document.createElement('a');
        button.className = 'yCloseIcon';
        button.href = '#';
        button.style.zIndex = yWidget.getMaxZIndex();
        yWidget.addClass(yWidget.formPosition, button);
        button.addEventListener(yWidget.click, function(e) {
            e.preventDefault();
            yWidget.hide();
        }, false);
        document.getElementsByTagName('body')[0].appendChild(button);
        return button;
    },
    fixWindowScroll: function(type) {
        if (type === 'hidden') {
            yWidget.addClass('yBodyOverflowHidden', document.getElementsByTagName('body')[0]);
        } else {
            yWidget.removeClass('yBodyOverflowHidden', document.getElementsByTagName('body')[0]);
        }
    },
    setConfig: function() {
        if (typeof yWidgetSettings != 'undefined') {
            if (typeof yWidgetSettings.showNewWidgetAutomatically != 'undefined') {
                yWidget.showNewWidgetAutomatically = yWidgetSettings.showNewWidgetAutomatically;
            }
            if (typeof yWidgetSettings.formPosition != 'undefined') {
                yWidget.formPosition = yWidgetSettings.formPosition;
            }
            if (typeof yWidgetSettings.yaCounterId  != 'undefined' && yWidget.isInt(yWidgetSettings.yaCounterId)) {
                if(!yWidget.counters) {
                    yWidget.counters = {};
                }
                yWidget.counters.yaCounterId = yWidgetSettings.yaCounterId;
            }
        }
    },
    setButtons: function() {
        var buttons = document.getElementsByClassName('ms_booking');//'ms-button' - new button class
        for (index = 0; index < buttons.length; ++index) {
            var button = buttons[index];
            yWidget.addClickEventToButton(button);
        }
    },
    addClickEventToButton: function(button) {
        button.addEventListener(yWidget.click, function(e) {
            e.preventDefault();
            var url = yWidget.href;
            if (typeof this.dataset.url != 'undefined') {
                url = this.dataset.url;
            }
            if (button.search !== '') {
                url += url.indexOf('?') > -1 ? button.search.replace('?', '&') : button.search;
            }
            if (yWidget.isNew > 0) {
                url = url.replace('://w', '://n');
            }
            var searchQuery = yWidget.buildSearchQuery();
            var prefix = url.indexOf('?') > -1 ? '&' : '?';

            if(searchQuery) {
                url += prefix + searchQuery;
            }
            yWidget.show(url);
        }, false);
    },
    getCookie: function (name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length === 2) {
            return parts.pop().split(";").shift();
        }
        return undefined;
    },
    initCounters: function () {
        yWidget.initYandexMetrika();
    },
    isInt : function (n) {
        return Number(n) === n && n % 1 === 0;
    },
    reachButtonPressGoal : function() {
        if(yWidget.counters && yWidget.counters.yaCounter) {
            yWidget.counters.yaCounter.reachGoal('widget_button_pressed');
        }
    },
    initYandexMetrika: function () {
        var id = parseInt(yWidget.id);
        if(yWidget.isInt(id) && yWidget.counters && window.addEventListener) {
            var counterId = parseInt(yWidget.counters.yaCounterId);
            if (yWidget.isInt(counterId)) {
                (function (d, w, c) {
                    (w[c] = w[c] || []).push(function () {
                        try {

                            var counter = yWidget.counters.yaCounter = window.__widgetYaCounter = new Ya.Metrika({
                                id: counterId,
                                clickmap: true,
                                trackLinks: true,
                                accurateTrackBounce: true,
                                webvisor: true,
                                trackHash: true,
                                triggerEvent: true
                            });
                            counter.reachGoal('site_opened');
                            window.addEventListener("message", receiveMessage, false);
                            function receiveMessage(event) {
                                if (!event || !event.data || !event.origin || !event.origin.match) {
                                    return;
                                }
                                var eventOriginMatch = event.origin.match(/^https?\:\/\/n\d+\.yclients\.com/);
                                if (eventOriginMatch && eventOriginMatch.length && event.data.action) {
                                    counter.reachGoal(event.data.action, event.data.params);

                                }
                            }
                        } catch (e) {
                        }
                    });
                    var n = d.getElementsByTagName("script")[0],
                        s = d.createElement("script"),
                        f = function () {
                            n.parentNode.insertBefore(s, n);
                        };
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = "https://mc.yandex.ru/metrika/watch.js";

                    if (w.opera === "[object Opera]") {
                        d.addEventListener("DOMContentLoaded", f, false);
                    } else {
                        f();
                    }
                })(document, window, "yandex_metrika_callbacks");
            }
        }
    },
    init: function() {
        yWidget.setConfig();
        yWidget.addCSS();
        yWidget.click = yWidget.clickevent();
        yWidget.setButtons();
        yWidget.initCounters();
        yWidget.cover = yWidget.createWindowCover();
        yWidget.closeIcon = yWidget.createCloseIcon();
        if (yWidget.showNewWidgetAutomatically) {
            yWidget.show(yWidget.href);
        }
    },
    show: function(url) {
        yWidget.reachButtonPressGoal();
        if (yWidget.isMobile.any()) {
            //var str = '?from='+encode64(window.location.href);
            location.href = url;
            return false;
        }
        if (yWidget.block == null) {
            yWidget.block = yWidget.createWidgetBlock();
        }
        yWidget.removeClass('yWidgetHide', yWidget.block);
        yWidget.addClass('yWidgetShow', yWidget.block);
        yWidget.cover.style.display = 'block';
        yWidget.closeIcon.style.display = 'block';
        yWidget.fixWindowScroll('hidden');
        if (yWidget.iFrame.src !== url) {
            yWidget.iFrame.src = '';
            setTimeout(function() {
                yWidget.iFrame.src = url;
            }, 250);
        }
        return false;
    },
    hide: function() {
        yWidget.removeClass('yWidgetShow', yWidget.block);
        yWidget.addClass('yWidgetHide', yWidget.block);
        yWidget.cover.style.display = 'none';
        yWidget.closeIcon.style.display = 'none';
        yWidget.fixWindowScroll('auto');
    },
    getMaxZIndex: function() {
        var z = 0;
        var zIndex;
        var all = document.getElementsByTagName('*');
        for (var i = 0, n = all.length; i < n; i++) {
            zIndex = document.defaultView.getComputedStyle(all[i], null).getPropertyValue("z-index");
            zIndex = parseInt(zIndex, 10);
            z = (zIndex) ? Math.max(z, zIndex) : z;
        }
        if (z < 9999) {
            z = 9999;
        }
        return z+1;
    },
    addClass: function(classname, element) {
        var cn = element.className;
        if (cn.indexOf(classname) !== -1) {
            return;
        }
        if (cn !== '') {
            classname = ' ' + classname;
        }
        element.className = cn + classname;
    },
    removeClass: function(classname, element) {
        var cn = element.className;
        var rxp = new RegExp("\\s?\\b" + classname + "\\b", "g");
        cn = cn.replace(rxp, '');
        element.className = cn;
    }
};
document.addEventListener('DOMContentLoaded', yWidget.init(), false);