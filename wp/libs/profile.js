(function ($) {
    'use strict';
    $(function () {
        $('.user-profile-picture').insertBefore('.user-user-login-wrap');
        $('.user-language-wrap').insertBefore('.user-pass1-wrap');
        $('.user-role-wrap').insertBefore('.user-pass1-wrap');
        $('.user-admin-color-wrap').insertBefore('.user-pass1-wrap');
        $('.user-admin-bar-front-wrap').insertBefore('.user-pass1-wrap');
        $('.user-rich-editing-wrap').insertBefore('.user-pass1-wrap');
        $('.user-syntax-highlighting-wrap').insertBefore('.user-pass1-wrap');
        $('.user-comment-shortcuts-wrap').insertBefore('.user-pass1-wrap');
        $('.rwmb-meta-box').insertAfter($('.user-description-wrap').closest('.form-table'));
        var $btnSetImage = $('#btnSetImage');
        $btnSetImage.on('click', function () {
            var fileFrame = wp.media({frame: 'select', title: "Insert Media", multiple: false});
            fileFrame.on('select', function () {
                var imageData = fileFrame.state().get('selection').first().toJSON();
                //console.log(image_data);
                var imgAvatar = $btnSetImage.children('img');
                imgAvatar.attr('src', imageData.url);
                imgAvatar.attr('srcset', imageData.url);
                //Here is selected form field that handle current avatar image id. Selector use phh const value of META_AVATAR
                jQuery('#avatar').val(imageData.id).show();
            });
            fileFrame.open();
        });
    });
})(jQuery);