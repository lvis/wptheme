/**
 * Created by Vitalie Lupu on 4/30/17.
 */
(function (api, $) {
    $(document).ready(function () {
        //api.state.bind('change', initDropDown);
    });
}(wp.customize, jQuery));

function addMedia(fieldId, fieldName) {
    var $mediaContainer = document.getElementById(fieldId + "-container");
    var inputName = 'input[name^="' + fieldName + '"]';
    var imageLinks = {};
    var mediaLibrary = wp.media({
        title: 'Select or Upload image',
        library: {type: 'image'},
        button: {text: 'Select'},
        multiple: true
    });
    mediaLibrary.on('open', function () {
        var selection = mediaLibrary.state().get('selection');
        jQuery(inputName).map(function () {
            var input = jQuery(this);
            var imgId = input.data('id');
            imageLinks[imgId] = input.val();
            var attachment = wp.media.attachment(imgId);
            attachment.fetch();
            selection.add(attachment ? [attachment] : []);
        });
    });
    mediaLibrary.on('select', function () {
        $mediaContainer.innerHTML = "";
        mediaLibrary.state().get('selection').map(function (item) {
            var attachment = item.toJSON();
            var imgId = attachment.id;
            var imgLink = "";
            if (imageLinks[imgId]) {
                imgLink = imageLinks[imgId];
            }
            $mediaContainer.innerHTML += '<p><label class="wide-title"><span class="fa fa-link fa-lg"></span> Link' +
                '<input name="' + fieldName + '[' + imgId + ']" value="' + imgLink + '" class="widefat" data-id="' + imgId + '">' +
                '<img class="attachment-thumb" src="' + attachment.url + '"></label></p>';
        });
        // console.log(inputName,jQuery(inputName).map(function(){return jQuery(this).val();}).get());
        jQuery(inputName).map(function () {
            jQuery(this).trigger("change");
        });
    });
    mediaLibrary.open();
}