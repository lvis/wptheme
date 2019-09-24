// Script run inside a Customizer control sidebar
(function ($) {
    wp.customize.bind('ready', function () {
        var slider = $('.range-slider-container'),
            range = $('.range-slider'),
            value = $('.range-slider-value'),
            reset = $('.range-slider-reset');
        slider.each(function () {
            value.each(function () {
                var eachVal = $(this).prev().attr('value');
                $(this).val(eachVal);
            });
            value.on('keyup', function () {
                var keyupVal = $(this).val();
                $(this).prev().attr('value', keyupVal);
                $(this).prev().trigger('input');
            });
            range.on('input', function () {
                $(this).next(value).val(this.value);
            });
            reset.on('click', function () {
                var rangeVal = $(this).parent().next().data('default-value');
                $(this).parent().next().val(rangeVal);
                $(this).parent().next().trigger('input');
            });
        });
    });
})(jQuery);
