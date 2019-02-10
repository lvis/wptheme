function handleBodyTouch(event) {
    var anchor = document.querySelector('.widget_nav_menu ul.menu > li.menu-item > a:first-of-type');
    if (anchor) {
        anchor.focus();
        event.stopPropagation();
        event.preventDefault();
    }
    document.body.removeEventListener('touchend', handleBodyTouch);
}
function handleFocus(event){
    if (event.target === event.currentTarget){
        document.body.addEventListener('touchend',handleBodyTouch);
    }
}
var deviceAgent = navigator.userAgent.toLowerCase();
var agentID = deviceAgent.match(/(iPad|iPhone|iPod)/i);
if (agentID) {
    window.addEventListener('DOMContentLoaded',function() {
        var anchors = document.querySelectorAll('.widget_nav_menu li.menu-item-has-children > a');
        for (var i = 0, len = anchors.length; i < len; i++) {
            var item = anchors[i];
            item.addEventListener('focus',handleFocus, false);
        }
    });
}
/*Sticky Menu*/
var headerTopElement = document.getElementById("header-main");
if (headerTopElement) {
    var headerTopElementHeight = headerTopElement.offsetHeight;
    var headerTopScrollOffset = headerTopElement.offsetTop + headerTopElementHeight;
    var cssFixedClassName = 'p-fixed-md';
    window.onscroll = function () {
        if (window.pageYOffset >= headerTopScrollOffset) {
            //TODO Add the case when parent element has a padding and calculate
            //window.getComputedStyle(headerTopElement.parentElement, null).getPropertyValue('padding-top')
            headerTopElement.parentElement.style.paddingTop = headerTopElementHeight + 'px';
            headerTopElement.classList.add(cssFixedClassName);
        } else {
            headerTopElement.parentElement.style.paddingTop = '0';
            headerTopElement.classList.remove(cssFixedClassName);
        }
    };
}