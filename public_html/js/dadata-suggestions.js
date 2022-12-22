"use strict";

function _classCallCheck(e, t) {
    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
}

function _defineProperties(e, t) {
    for (var n = 0; n < t.length; n++) {
        var a = t[n];
        a.enumerable = a.enumerable || !1, a.configurable = !0, "value" in a && (a.writable = !0), Object.defineProperty(e, a.key, a)
    }
}

function _createClass(e, t, n) {
    return t && _defineProperties(e.prototype, t), n && _defineProperties(e, n), e
}

function _defineProperty(e, t, n) {
    return t in e ? Object.defineProperty(e, t, {
        value: n,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[t] = n, e
}

var DaDataSuggestions = function () {
    function n(e) {
        var t = this;
        _classCallCheck(this, n), _defineProperty(this, "daDataConfig", {
            serviceUrl: "https://dadata.Vzr.ru:8080/suggestions/api/4_1/rs",
            token: "9112ed2176490c86a1ca8dc186bc2d371cf19311",
            type: "ADDRESS",
            count: 5,
            minChars: 1,
            scrollOnFocus: !1,
            beforeRender: function (e) {
                t.wrapper || (t.wrapper = e.parents(".suggestions-wrapper").get(0), t.$scrollContainer = $('<div class="suggestions-scroll" />'), t.$scrollContainer.css({display: "none"}), t.$scrollContainer.appendTo(t.wrapper), Scrollbar.init(t.$scrollContainer[0], {
                    damping: .06,
                    thumbMinSize: 33,
                    renderByPixels: !0,
                    alwaysShowTracks: !0,
                    continuousScrolling: !1
                }))
            }
        }), this.$element = $(e)
    }

    return _createClass(n, [{
        key: "setOnSelectCallback", value: function (e) {
            this.daDataConfig.onSelect = e
        }
    }, {
        key: "setFormatSelected", value: function (e) {
            this.daDataConfig.formatSelected = e
        }
    }, {
        key: "setOnSelectNothing", value: function (e) {
            this.daDataConfig.onSelectNothing = e
        }
    }, {
        key: "init", value: function () {
            void 0 !== this.$element.data("bounds") && (this.daDataConfig.bounds = this.$element.data("bounds")), void 0 !== this.$element.data("constraints") && (this.daDataConfig.constraints = this.$element.data("constraints")), this.$element.suggestions(this.daDataConfig)
        }
    }, {
        key: "fixData", value: function () {
            this.$element.suggestions().fixData()
        }
    }]), n
}();
$(function () {
    $(".js-dadata-suggestion").each(function (e, t) {
        new DaDataSuggestions(t).init()
    })
});