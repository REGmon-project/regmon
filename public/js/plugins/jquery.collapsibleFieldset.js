/**
 * jQuery Collapsible Fieldset Plugin v1.00 (29-JUL-2012)
 *
 * Copyright (c) 2012 Rex McConnell <rex@rexmac.com>
 *
 * Project repository: https://github.com/rexmac/jquery-collapsibleFieldset
 * Licensed under the MIT license:
 *   http://rexmac.github.com/license/mit.txt
 *
 */
/*global jQuery */
(function ($) {
    var methods = {
        init: function (options) {
            if (typeof options === "undefined") {
                options = {};
            }
            // Maintain chainability
            return this.each(function () {
                var self = $(this),
                    data = self.data("collapsible");
                if (!self.is("fieldset")) {
                    return;
                }

                if (!data) {
                    self.addClass("collapsible"); //mad
					var t_title = self.hasClass("collapsed") ? "Expand" : "Collapse"; //mad
                    self.children("legend")
                        .attr("title", t_title)
                        .bind("click.collapsible", function (e) {
                            //mad
                            self.toggleClass("collapsed");
                            $(this).siblings().slideToggle("slow");
                            if ($(this).attr("title") === "Collapse") {
                                $(this).attr("title", "Expand");
                                if (typeof options.collapse === "function") {
                                    options.collapse();
                                }
                            } else {
                                $(this).attr("title", "Collapse");
                                if (typeof options.expand === "function") {
                                    options.expand();
                                }
                            }
                        })
                        .append('<span class="collapsible-indicator"></span>');

                    self.data("collapsible", { target: self });
                }
            });
        },
        destroy: function () {
            return this.each(function () {
                var self = $(this);
                //data = self.data('collapsible');
                if (!self.is("fieldset")) {
                    return;
                }
                self.removeClass("collapsible")
                    .children("legend")
                    .removeAttr("title")
                    .unbind(".collapsible")
                    .remove("span.collapsible-indicator");
                //data.collapsible.remove();
                self.removeData("collapsible");
            });
        },
        collapse: function () {
            return this.each(function () {
                var self = $(this);
                if (!self.hasClass("collapsed")) {
                    self.addClass("collapsed");
                    self.children("legend").attr("title", "Expand");
                    self.children().not("legend").slideToggle(0);
                }
            });
        },
        expand: function () {},
    };

    $.fn.collapsible = function (method) {
        if (methods[method]) {
            return methods[method].apply(
                this,
                Array.prototype.slice.call(arguments, 1)
            );
        } else if (typeof method === "object" || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error(
                "Method " + method + " does not exist on jQuery.collapsible"
            );
        }
    };
})(jQuery);
