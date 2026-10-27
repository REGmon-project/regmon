/* Keep Bootstrap 3 popover editors usable without disabling HTML sanitization. */
(function ($, DOMPurify) {
    'use strict';

    function sanitize(html) {
        return DOMPurify.sanitize(html);
    }

    $.fn.tooltip.Constructor.DEFAULTS.sanitizeFn = sanitize;
    $.fn.popover.Constructor.DEFAULTS.sanitizeFn = sanitize;

    // Form editor controls are cloned into sanitized popovers. Use an event
    // listener instead of inline JavaScript, which the sanitizer removes.
    $(document).on('change', 'input[type="checkbox"][data-regmon-sync-next-value]', function () {
        var next = this.nextElementSibling;
        if (next && next.tagName === 'INPUT' && next.type === 'hidden') {
            next.value = this.checked ? '1' : '0';
        }
        var scope = $(this).closest('.c_content, .form_popup');
        if (this.getAttribute('data-regmon-toggle') === 'color') {
            scope.find('.c_color_a, .c_color_b').prop('disabled', !this.checked);
        } else if (this.getAttribute('data-regmon-toggle') === 'title') {
            scope.find('.c_title').prop('disabled', !this.checked);
            scope.find('input[type=radio][name$="_talign"]').closest('label').toggleClass('disabled', !this.checked);
        }
    });
})(jQuery, DOMPurify);
