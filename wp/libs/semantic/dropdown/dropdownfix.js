function rebuildDropDown(selectDropDown) {
    var list = selectDropDown.getElementsByTagName("select")[0];
    if (list) {
        var optionsGroups = list.querySelectorAll('optgroup');
        var optionsGroupsCount = optionsGroups.length;
        if (optionsGroupsCount > 0) {
            var groupIndex, optionIndex, menuHtml = '';
            for (groupIndex = 0; groupIndex < optionsGroupsCount; groupIndex++) {
                var optionsGroupItem = optionsGroups[groupIndex];
                menuHtml += "<div class='header'>" + optionsGroupItem.label + "</div><div class='divider'></div>";
                var optionsInGroupCount = optionsGroupItem.children.length;
                for (optionIndex = 0; optionIndex < optionsInGroupCount; optionIndex++) {
                    var optionsInGroupItem = optionsGroupItem.children[optionIndex];
                    if (optionsInGroupItem.selected) {
                        menuHtml += "<div class='item active filtered' data-value='" + optionsInGroupItem.value + "'>" +
                            optionsInGroupItem.innerHTML + "</div>";
                    } else {
                        menuHtml += "<div class='item' data-value='" + optionsInGroupItem.value + "'>" +
                            optionsInGroupItem.innerHTML + "</div>";
                    }
                }
            }
            var menu = selectDropDown.getElementsByClassName('menu')[0];
            menu.innerHTML = menuHtml;
        }
    }
}

function initDropDown(selector) {
    if (selector && selector.length > 3) {
        var $element = jQuery(selector).dropdown({
            onShow: function () {
                var selectDropDown = $element[0];
                var headers = selectDropDown.querySelectorAll('.header');
                if (headers.length > 0) {
                    var menu = selectDropDown.getElementsByClassName('menu')[0];
                    setTimeout(function () {
                        menu.scrollTop = 0;
                    }, 200);
                }
            }
        });
        rebuildDropDown($element[0]);
    }
}