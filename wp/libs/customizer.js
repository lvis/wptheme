(function ($) {
    // Update the site title in real time...
    wp.customize('blogname', function (value) {
        value.bind(function (newval) {
            $('span.site-title').html(newval);
        });
    });
    //Update the site description in real time...
    wp.customize('blogdescription', function (value) {
        value.bind(function (newval) {
            $('small.site-description').html(newval);
        });
    });

    //Update site title color in real time...
    wp.customize('header_textcolor', function (value) {
        value.bind(function (newval) {
            $('#site-title a').css('color', newval);
        });
    });

    //Update site background color...
    wp.customize('background_color', function (value) {
        value.bind(function (newval) {
            $('body').css('background-color', newval);
        });
    });

    //Update site link color in real time...
    wp.customize('link_textcolor', function (value) {
        value.bind(function (newval) {
            $('a').css('color', newval);
        });
    });
})(jQuery);