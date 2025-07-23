// sticky-side-buttons.js

// SSB UI jQuery
var ssb_panel;
jQuery(function ($) {

    // Animation Slide
    ssb_panel = $('#ssb-container');
    const ssb_panel_w = ssb_panel.width();
    const sbb_display_margin = 35;
    const window_width = jQuery(window).width();

    if (ssb_panel.hasClass('ssb-btns-left') &&
        (ssb_panel.hasClass('ssb-anim-slide') || ssb_panel.hasClass('ssb-anim-icons')))
    {
        ssb_panel.css('left', '-' + (ssb_panel_w - sbb_display_margin) + 'px');
    }
    else if (ssb_panel.hasClass('ssb-btns-right') &&
        (ssb_panel.hasClass('ssb-anim-slide') || ssb_panel.hasClass('ssb-anim-icons')))
    {
        ssb_panel.css('right', '-' + (ssb_panel_w - sbb_display_margin) + 'px');
    }

    // Slide when hover
    if (window_width >= 768) {
        ssb_panel.hover(function () {
            if (ssb_panel.hasClass('ssb-btns-left') && ssb_panel.hasClass('ssb-anim-slide')) {
                ssb_panel.stop().delay(1000).animate({'left': 0}, 300);
            }
            else if (ssb_panel.hasClass('ssb-btns-right') && ssb_panel.hasClass('ssb-anim-slide')) {
                ssb_panel.stop().delay(1000).animate({'right': 0}, 300);
            }
        }, function () {
			ssb_panel.stop(true, true);
            if (ssb_panel.hasClass('ssb-btns-left') && ssb_panel.hasClass('ssb-anim-slide')) {
                ssb_panel.animate({'left': '-' + (ssb_panel_w - sbb_display_margin) + 'px'}, 300);
            }
            else if (ssb_panel.hasClass('ssb-btns-right') && ssb_panel.hasClass('ssb-anim-slide')) {
                ssb_panel.animate({'right': '-' + (ssb_panel_w - sbb_display_margin) + 'px'}, 300);
            }
        });
    }
    else {
        //stay closed
    }
});