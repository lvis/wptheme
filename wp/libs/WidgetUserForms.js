/**
 * Created by Vitalie Lupu on 6/2/17.
 */
function WidgetUserForms() {
    var self = this;
    var formSubmitted = ko.observable(false);
    self.userName = ko.observable();
    self.userPassword = ko.observable();
    self.userFirstName = ko.observable();
    self.userLastName = ko.observable();
    self.userEmail = ko.observable();
    self.hasUserData = ko.pureComputed(function () {
        var userFirstName = self.userFirstName();
        if (!userFirstName){
            userFirstName = '';
        }
        var userLastName = self.userLastName();
        if (!userLastName){
            userLastName = '';
        }
        var userEmail = self.userEmail();
        if (!userEmail){
            userEmail = '';
        }
        var emailRegExp = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var isEmailAvailable = emailRegExp.test(String(userEmail).toLowerCase());
        return userFirstName.length > 1 && userLastName.length > 1 && isEmailAvailable && !formSubmitted();
    });
    self.hasUserName = ko.pureComputed(function () {
        var userName = self.userName();
        if (!userName){
            userName = '';
        }
        return userName.length > 1 && !formSubmitted();
    });
    self.hasCredentials = ko.pureComputed(function () {
        var userPassword = self.userPassword();
        if (!userPassword){
            userPassword = '';
        }
        return self.hasUserName() && userPassword.length > 6 && !formSubmitted();
    });
    self.handleOnSubmit = function handleOnSubmit(formElement) {
        if (jQuery !== undefined && jQuery().ajaxSubmit) {
            var $form = jQuery(formElement);
            $form.ajaxSubmit({
                beforeSubmit: function () {
                    formSubmitted(true);
                },
                success: function (ajax_response, statusText, xhr, form) {
                    var response = jQuery.parseJSON(ajax_response);
                    if (response.success) {
                        form.resetForm();
                    }
                    if (response.redirect) {
                        window.location.replace(response.redirect);
                    } else {
                        alert(response.message);
                    }
                    formSubmitted(false);
                }
            });
        }
    };
}