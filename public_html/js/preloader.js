"use strict";
!function (d) {
    window.preloader = {
        loaderShow: function (e, o) {
            var r = d("body"), i = d(".remodal-bg");
            void 0 === o && (o = d("#preloaderText").val()), void 0 === e && (e = d("#preloaderPath").val()), void 0 === window.isPreloaderVisible && (window.isPreloaderVisible = !1, r.append('<div class="preloader_overlay"></div><div class="preloader"><div class="preloader__content"><div class="preloader__image"></div><div class="preloader__text js-loader-text"></div></div></div>')), d(".js-loader-text").html(""), window.isPreloaderVisible || (d(".preloader_overlay").show(), d(".preloader").show(), r.addClass("scroll-lock"), i.addClass("remodal-is-opened"), window.isPreloaderVisible = !0), "string" == typeof o && 0 < o.length ? d(".js-loader-text").html(o).show() : "string" == typeof e && 0 < e.length && d.ajax({
                url: e,
                type: "GET",
                success: function (e) {
                    d(".js-loader-text").html(e).show()
                }
            })
        }, loaderHide: function () {
            var e, o;
            void 0 !== window.isPreloaderVisible && window.isPreloaderVisible && (e = d("body"), o = d(".remodal-bg"), d(".preloader_overlay").hide(), d(".preloader").hide(), e.removeClass("scroll-lock"), o.removeClass("remodal-is-opened"), window.isPreloaderVisible = !1)
        }, isLoaderVisible: function () {
            return window.isPreloaderVisible
        }, preloadImages: function (e) {
            d(e).each(function () {
                d("<img/>")[0].src = this
            })
        }
    }
}(jQuery);