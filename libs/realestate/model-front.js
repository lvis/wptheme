/**
 * Created by Vitalie Lupu on 6/3/17.
 */
function AppViewModel() {
    var self = this;
    var settings = appViewSettings;
    self.areaSearch = ko.observable(0);
    self.priceSearch = ko.observable(0);

    var postRequest = function (sentData, handleResult) {
        jQuery.post(settings.url, sentData, handleResult, "json").fail(function (result) {
            console.log("Error " + sentData.action, result);
        });
    };

    self.favorites = ko.observableArray([]);

    self.cssFavorites = ko.pureComputed(function () {
        return self.hasFavorites() ? "fa fa-star" : "fa fa-star-o";
    });

    self.hasFavorites = ko.pureComputed(function () {
        return self.favorites().length > 0;
    });

    self.isInFavorites = function (propertyCode) {
        console.log(self.favorites(), propertyCode);
        return self.favorites.indexOf(propertyCode) !== -1;
    };

    self.getFavorites = function () {
        postRequest({action: "getFavorites"}, function (result) {
            self.favorites(result.result);
        });
    };

    var spin = function (target, value) {
        if (!target) return;
        if (value) {
            target.addClass("fa-spin");
        } else {
            target.removeClass("fa-spin");
        }
    };

    self.addToFavorites = function (propertyCode, data, event) {
        var target = jQuery(event.target);
        spin(target, true);
        postRequest({action: "addToFavorites", propertyCode: propertyCode}, function (result) {
            spin(target, false);
            if (result.result) {
                self.favorites.push(propertyCode);
                console.log(self.favorites());
            }
        });
    };
    self.removeFromFavorites = function (propertyCode, data, event) {
        var target = jQuery(event.target);
        var propertyItem = target.closest('.property-item');
        spin(target, true);
        postRequest({action: "removeFromFavorites", propertyCode: propertyCode}, function (result) {
            spin(target, false);
            if (result.result) {
                propertyItem.remove();
                self.favorites.remove(propertyCode);
                if (!self.hasFavorites()) {
                    window.location = document.referrer;
                }
            }
        });
    };

    self.getFavorites();
}
var vmApp = new AppViewModel();
if (window.frames.length > 0) {
    ko.applyBindings(vmApp, window.frames[0].document.body);
} else {
    ko.applyBindings(vmApp);
}