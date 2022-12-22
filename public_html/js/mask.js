$(document).ready(function ($) {

    "use strict";

    function _classCallCheck(a, t) {
        if (!(a instanceof t)) throw new TypeError("Cannot call a class as a function")
    }

    function _defineProperties(a, t) {
        for (var e = 0; e < t.length; e++) {
            var n = t[e];
            n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(a, n.key, n)
        }
    }

    function _createClass(a, t, e) {
        return t && _defineProperties(a.prototype, t), e && _defineProperties(a, e), a
    }

    var Mask = function () {
        function a() {
            _classCallCheck(this, a), this.settingsMask()
        }

        return _createClass(a, [{
            key: "settingsMask", value: function () {
                $("[data-mask-cyrillic]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[а-яА-ЯЁё\- ]+/,
                            recursive: !0
                        }
                    }
                }), $("[data-mask-latin-number]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[^а-яА-ЯЁё]+/,
                            recursive: !0
                        }
                    }
                }), $("[data-mask-address]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[а-яА-ЯЁё\-\/ \d.,]+/,
                            recursive: !0
                        }
                    }
                }), $("[data-mask-agreement-number]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[a-zA-Zа-яА-ЯЁё\-.\d/\\]+/,
                            recursive: !0
                        }
                    }
                }), $("[data-mask-vin]").mask("RRRRRRRRRRRRRRRRR", {
                    translation: {R: {pattern: /[0-9abcdefghjklmnprstuvwxyzABCDEFGHJKLMNPRSTUVWXYZцукенгзфывапролдячсмитьЦУКЕНГЗФЫВАПРОЛДЯЧСМИТЬ]+/}},
                    onKeyPress: function (a, t, e) {
                        var n = function (a) {
                            var t,
                                e = "й ц у к е н г ш щ з х ъ ф ы в а п р о л д ж э я ч с м и т ь б ю Й Ц У К Е Н Г Ш Щ З Х Ъ Ф Ы В А П Р О Л Д Ж Э Я Ч С М И Т Ь Б Ю".split(" "),
                                n = "q w e r t y u i o p [ ] a s d f g h j k l ; ' z x c v b n m , . Q W E R T Y U I O P { } A S D F G H J K L : \" Z X C V B N M < >".split(" ");
                            for (t in e) -1 < a.indexOf(e[t]) && (a = a.replace(e[t], n[t]));
                            return a
                        }(a);
                        n !== a && e.val(n)
                    }
                }), $("[data-mask-date]").mask("dD.mM.yYYY", {
                    translation: {
                        d: {pattern: /[0-3]/},
                        D: {pattern: /[0-9]/},
                        m: {pattern: /[0-1]/},
                        M: {pattern: /[0-9]/},
                        y: {pattern: /[1-2]/},
                        Y: {pattern: /[0-9]/}
                    }
                }), window.MOBILE_REGEX = /mobile|tablet|ip(ad|hone|od)|android/i, window.isMobileDevice = window.MOBILE_REGEX.test(navigator.userAgent);
                $("[data-mask-auto-number]").mask("A NNN AA", {
                    translation: {
                        A: {pattern: window.isMobileDevice ? /[ABEKMHOPCTYXabekmhopctyxАВЕКМНОРСТУХавекмнорстух]/ : /[АВЕКМНОРСТУХавекмнорстухFDTRVYJHCNE{fdtrvyjhcne\[]/},
                        N: {pattern: /[0-9]/}
                    }, onKeyPress: function (a, t, e) {
                        var n = (window.isMobileDevice ? function (a) {
                            var t, e = "A B E K M H O P C T Y X a b e k m h o p c t y x".split(" "),
                                n = "А В Е К М Н О Р С Т У Х а в е к м н о р с т у х".split(" ");
                            for (t in e) -1 < a.indexOf(e[t]) && (a = a.replace(e[t], n[t]));
                            return a
                        } : function (a) {
                            var t,
                                e = "q w e r t y u i o p [ ] a s d f g h j k l ; ' z x c v b n m , . Q W E R T Y U I O P { } A S D F G H J K L : \" Z X C V B N M < >".split(" "),
                                n = "й ц у к е н г ш щ з х ъ ф ы в а п р о л д ж э я ч с м и т ь б ю Й Ц У К Е Н Г Ш Щ З Х Ъ Ф Ы В А П Р О Л Д Ж Э Я Ч С М И Т Ь Б Ю".split(" ");
                            for (t in e) -1 < a.indexOf(e[t]) && (a = a.replace(e[t], n[t]));
                            return a
                        })(a);
                        n !== a && e.val(n)
                    }
                }), $("[data-mask-pnr]").mask("RRRRRR", {translation: {R: {pattern: /[a-zA-Zа-яА-ЯЁё\d]/}}}), $("[data-mask-serial]").mask("RRRRR", {translation: {R: {pattern: /[0-9a-zA-Z\d]/}}}), $("[data-mask-serialmax]").mask("RRRRRRRR", {translation: {R: {pattern: /[0-9a-zA-Z\d]/}}}), $("[data-mask-serialnumber]").mask("RRRRR", {translation: {R: {pattern: /[0-9-\d]/}}}), $("[data-mask-number]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[0-9-\d]/,
                            recursive: !0
                        }
                    }
                }), $("[data-mask-float]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[0-9-\d.,]/,
                            recursive: !0
                        }
                    }
                }), $("[data-mask-serial-vu]").mask("00 RR", {
                    translation: {
                        A: {pattern: /[а-яА-Я\d]/},
                        R: {pattern: /[а-яА-Я]/}
                    }, onKeyPress: function (a, t, e, n) {
                        var r = "00 AR";
                        4 <= a.length && parseInt(a[3]) && (r = "00 00"), e.mask(r, n)
                    }
                }), $("[data-mask-serial-vu-no-space]").mask("00RR", {
                    translation: {
                        A: {pattern: /[а-яА-Я\d]/},
                        R: {pattern: /[а-яА-Я]/}
                    }, onKeyPress: function (a, t, e, n) {
                        var r = "00AR";
                        3 <= a.length && (parseInt(a[2]) || 0 === parseInt(a[2])) && (r = "0000"), e.mask(r, n)
                    }
                }), $("[data-mask-price]").mask("# ##0,00", {reverse: !0}), $("[data-mask-email]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[^\s]/,
                            recursive: !0
                        }
                    }
                }), $("[data-mask-apartment]").mask("R", {
                    translation: {
                        R: {
                            pattern: /[a-zA-Zа-яА-ЯЁё0-9]+/,
                            recursive: !0
                        }
                    }
                })
            }
        }]), a
    }();
    $(function () {
        new Mask
    });
})