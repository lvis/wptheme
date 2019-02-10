/**
 * Customizer enhancements for a better user experience.
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
(function ($) {
    // Declare vars
    var api = wp.customize,
        style = [
            'flyin',
            'floating'
        ];

    api('ocn_content', function (value) {
        value.bind(function (newval) {
            $('#ocn-cookie-wrap .ocn-cookie-content').html(newval);
        });
    });

    api('ocn_button_text', function (value) {
        value.bind(function (newval) {
            $('#ocn-cookie-wrap .ocn-btn').text(newval);
        });
    });

    api('ocn_style', function (value) {
        value.bind(function (newval) {
            var ocnNotice = $('#ocn-cookie-wrap');
            if (ocnNotice.length) {
                $.each(style, function (i, v) {
                    ocnNotice.removeClass(v);
                });
                ocnNotice.addClass(newval);
            }
        });
    });

    api('ocn_width', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_width');
            if (to) {
                var style = '<style class="customizer-ocn_width">#ocn-cookie-wrap.flyin, #ocn-cookie-wrap.floating #ocn-cookie-inner { width: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_background', function (value) {
        value.bind(function (to) {
            $('#ocn-cookie-wrap').css('background-color', to);
        });
    });

    api('ocn_text_color', function (value) {
        value.bind(function (to) {
            $('#ocn-cookie-wrap').css('color', to);
        });
    });

    api('ocn_btn_background', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_background');
            if (to) {
                var style = '<style class="customizer-ocn_btn_background">#ocn-cookie-wrap .ocn-btn { background-color: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_color', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_color');
            if (to) {
                var style = '<style class="customizer-ocn_btn_color">#ocn-cookie-wrap .ocn-btn { color: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_hover_background', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_hover_background');
            if (to) {
                var style = '<style class="customizer-ocn_btn_hover_background">#ocn-cookie-wrap .ocn-btn:hover { background-color: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_hover_color', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_hover_color');
            if (to) {
                var style = '<style class="customizer-ocn_btn_hover_color">#ocn-cookie-wrap .ocn-btn:hover { color: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_content_typo_font_family', function (value) {
        value.bind(function (to) {
            if (to) {
                var idfirst = (to.trim().toLowerCase().replace(' ', '-'), 'customizer-ocn_content_typo_font_family');
                var font = to.replace(' ', '%20');
                font = font.replace(',', '%2C');
                font = ocn_cookie.googleFontsUrl + '/css?family=' + to + ':' + ocn_cookie.googleFontsWeight;

                if ($('#' + idfirst).length) {
                    $('#' + idfirst).attr('href', font);
                } else {
                    $('head').append('<link id="' + idfirst + '" rel="stylesheet" type="text/css" href="' + font + '">');
                }
            }
            var $child = $('.customizer-ocn_content_typo_font_family');
            if (to) {
                var style = '<style class="customizer-ocn_content_typo_font_family">#ocn-cookie-wrap .ocn-cookie-content{font-family: ' + to + ';}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_content_typo_font_size', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-cookie-content').css('font-size', newval);
            }
        });
    });

    api('ocn_content_typo_font_weight', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-cookie-content').css('font-weight', newval);
            }
        });
    });

    api('ocn_content_typo_font_style', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-cookie-content').css('font-style', newval);
            }
        });
    });

    api('ocn_content_typo_transform', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-cookie-content').css('text-transform', newval);
            }
        });
    });

    api('ocn_content_typo_line_height', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-cookie-content').css('line-height', newval);
            }
        });
    });

    api('ocn_content_typo_spacing', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-cookie-content').css('letter-spacing', newval);
            }
        });
    });

    api('ocn_btn_typo_font_family', function (value) {
        value.bind(function (to) {
            if (to) {
                var idfirst = (to.trim().toLowerCase().replace(' ', '-'), 'customizer-ocn_btn_typo_font_family');
                var font = to.replace(' ', '%20');
                font = font.replace(',', '%2C');
                font = ocn_cookie.googleFontsUrl + '/css?family=' + to + ':' + ocn_cookie.googleFontsWeight;

                if ($('#' + idfirst).length) {
                    $('#' + idfirst).attr('href', font);
                } else {
                    $('head').append('<link id="' + idfirst + '" rel="stylesheet" type="text/css" href="' + font + '">');
                }
            }
            var $child = $('.customizer-ocn_btn_typo_font_family');
            if (to) {
                var style = '<style class="customizer-ocn_btn_typo_font_family">#ocn-cookie-wrap .ocn-btn{font-family: ' + to + ';}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_typo_font_size', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-btn').css('font-size', newval);
            }
        });
    });

    api('ocn_btn_typo_font_weight', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-btn').css('font-weight', newval);
            }
        });
    });

    api('ocn_btn_typo_font_style', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-btn').css('font-style', newval);
            }
        });
    });

    api('ocn_btn_typo_transform', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-btn').css('text-transform', newval);
            }
        });
    });

    api('ocn_btn_typo_line_height', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-btn').css('line-height', newval);
            }
        });
    });

    api('ocn_btn_typo_spacing', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('#ocn-cookie-wrap .ocn-btn').css('letter-spacing', newval);
            }
        });
    });

    api('ocn_top_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_top_padding');
            if (to) {
                var style = '<style class="customizer-ocn_top_padding">#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-top: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_right_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_right_padding');
            if (to) {
                var style = '<style class="customizer-ocn_right_padding">#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-right: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_bottom_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_bottom_padding');
            if (to) {
                var style = '<style class="customizer-ocn_bottom_padding">#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-bottom: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_left_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_left_padding');
            if (to) {
                var style = '<style class="customizer-ocn_left_padding">#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-left: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_tablet_top_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_tablet_top_padding');
            if (to) {
                var style = '<style class="customizer-ocn_tablet_top_padding">@media (max-width: 768px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-top: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_tablet_right_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_tablet_right_padding');
            if (to) {
                var style = '<style class="customizer-ocn_tablet_right_padding">@media (max-width: 768px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-right: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_tablet_bottom_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_tablet_bottom_padding');
            if (to) {
                var style = '<style class="customizer-ocn_tablet_bottom_padding">@media (max-width: 768px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-bottom: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_tablet_left_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_tablet_left_padding');
            if (to) {
                var style = '<style class="customizer-ocn_tablet_left_padding">@media (max-width: 768px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-left: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_mobile_top_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_mobile_top_padding');
            if (to) {
                var style = '<style class="customizer-ocn_mobile_top_padding">@media (max-width: 480px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-top: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_mobile_right_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_mobile_right_padding');
            if (to) {
                var style = '<style class="customizer-ocn_mobile_right_padding">@media (max-width: 480px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-right: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_mobile_bottom_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_mobile_bottom_padding');
            if (to) {
                var style = '<style class="customizer-ocn_mobile_bottom_padding">@media (max-width: 480px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-bottom: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_mobile_left_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_mobile_left_padding');
            if (to) {
                var style = '<style class="customizer-ocn_mobile_left_padding">@media (max-width: 480px){#ocn-cookie-wrap.flyin,#ocn-cookie-wrap.floating { padding-left: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_border_width', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_border_width');
            if (to) {
                var style = '<style class="customizer-ocn_border_width">#ocn-cookie-wrap { border-width: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_border_style', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_border_style');
            if (to) {
                var style = '<style class="customizer-ocn_border_style">#ocn-cookie-wrap { border-style: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_border_color', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_border_color');
            if (to) {
                var style = '<style class="customizer-ocn_border_color">#ocn-cookie-wrap { border-color: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_top_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_top_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_top_padding">#ocn-cookie-wrap .ocn-btn { padding-top: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_right_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_right_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_right_padding">#ocn-cookie-wrap .ocn-btn { padding-right: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_bottom_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_bottom_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_bottom_padding">#ocn-cookie-wrap .ocn-btn { padding-bottom: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_left_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_left_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_left_padding">#ocn-cookie-wrap .ocn-btn { padding-left: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_top_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_top_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_top_padding">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { padding-top: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_right_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_right_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_right_padding">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { padding-right: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_bottom_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_bottom_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_bottom_padding">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { padding-bottom: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_left_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_left_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_left_padding">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { padding-left: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_top_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_top_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_top_padding">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { padding-top: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_right_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_right_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_right_padding">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { padding-right: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_bottom_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_bottom_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_bottom_padding">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { padding-bottom: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_left_padding', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_left_padding');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_left_padding">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { padding-left: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_top_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_top_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_top_border_radius">#ocn-cookie-wrap .ocn-btn { border-top-left-radius: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_right_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_right_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_right_border_radius">#ocn-cookie-wrap .ocn-btn { border-top-right-radius: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_bottom_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_bottom_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_bottom_border_radius">#ocn-cookie-wrap .ocn-btn { border-bottom-right-radius: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_left_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_left_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_left_border_radius">#ocn-cookie-wrap .ocn-btn { border-bottom-left-radius: ' + to + 'px; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_top_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_top_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_top_border_radius">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { border-top-left-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_right_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_right_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_right_border_radius">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { border-top-right-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_bottom_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_bottom_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_bottom_border_radius">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { border-bottom-right-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_tablet_left_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_tablet_left_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_tablet_left_border_radius">@media (max-width: 768px){#ocn-cookie-wrap .ocn-btn { border-bottom-left-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_top_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_top_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_top_border_radius">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { border-top-left-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_right_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_right_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_right_border_radius">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { border-top-right-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_bottom_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_bottom_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_bottom_border_radius">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { border-bottom-right-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_btn_mobile_left_border_radius', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_btn_mobile_left_border_radius');
            if (to) {
                var style = '<style class="customizer-ocn_btn_mobile_left_border_radius">@media (max-width: 480px){#ocn-cookie-wrap .ocn-btn { border-bottom-left-radius: ' + to + 'px; }}</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_close_color', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_close_color');
            if (to) {
                var style = '<style class="customizer-ocn_close_color">#ocn-cookie-wrap .ocn-icon svg { fill: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });

    api('ocn_close_hover_color', function (value) {
        value.bind(function (to) {
            var $child = $('.customizer-ocn_close_hover_color');
            if (to) {
                var style = '<style class="customizer-ocn_close_hover_color">#ocn-cookie-wrap .ocn-icon:hover svg { fill: ' + to + '; }</style>';
                if ($child.length) {
                    $child.replaceWith(style);
                } else {
                    $('head').append(style);
                }
            } else {
                $child.remove();
            }
        });
    });
})(jQuery);