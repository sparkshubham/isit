//-------- -------- -------- --------
//-------- included js libs end
//-------- -------- -------- --------
//-------- -------- -------- --------
//-------- js custom start
//-------- -------- -------- --------
function initSteps() {
    if ($(".prgs").length) {
        // dom
        var step, curentEl, curentElLeft, containerElLeft, progress = $(".prgs"), progressBars = (progress.find(".prgs-dot"), 
        progress.find(".prgs-bar")), progressBg = progress.find(".prgs-bg");
        $(window).on("orientationchange resize", function() {
            // After orientationchange, add a one-time resize event
            $(window).one("resize", setProggressValue);
        }), setProggressValue();
    }
    function setProggressValue() {
        step = progress.data("step"), curentEl = progressBars.eq(step - 1), 4 != step ? (
        // size
        curentElHalf = curentEl.innerWidth() / 2, curentElLeft = curentEl.offset().left, 
        containerElLeft = progress.offset().left, progressBg.css("width", curentElLeft - containerElLeft + curentElHalf)) : progressBg.css("width", "100%");
    }
}

function initTooltip() {
    $('[data-toggle="tooltip"]').tooltip({
        trigger: "hover"
    });
}

function initFormValidation() {
    $(".form").length && $(".form form").validate({
        focusInvalid: !1,
        // fields
        rules: {
            name: {
                minlength: 3
            },
            lastname: {
                minlength: 3
            },
            fullname: {
                minlength: 3
            },
            email: {
                email: !0
            },
            phone: {
                phone: !0
            },
            message: {
                minlength: 6
            },
            card: {
                creditcard: !0
            },
            month: {
                min: 1,
                max: 12,
                number: !0
            },
            year: {
                number: !0,
                min: 1,
                max: 99
            },
            cw: {
                number: !0,
                minlength: 3,
                maxlength: 3
            }
        },
        messages: {
            month: "Enter month.",
            year: "Enter year.",
            cvv: "Enter CVV."
        }
    });
}

function initCollapseHandler() {
    var collapse = $(".collapse"), header = $(".header");
    function scrollToTop() {
        var destination = $(this).closest(".card").offset().top;
        $("html, body").animate({
            scrollTop: destination - header.innerHeight()
        }, 400);
    }
    $(window).resize(function() {
        $(window).outerWidth() < 576 && collapse.on("shown.bs.collapse", scrollToTop);
    });
}

function initValidationPlugin() {
    // For Bootstrap 4
    $.validator.setDefaults({
        errorElement: "span",
        errorPlacement: function(error, element) {
            // show messages
            // error.addClass('invalid-feedback');
            // element.parent().append(error);
            // no show messages
            return !1;
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("is-invalid");
        }
    }), $.validator.addMethod("phone", function(value, element) {
        var phoneNumber = value.replace(/\s+/g, ""), reg = new RegExp("^(([0-9]{3})|[0-9]{3})[0-9]{3}[0-9]{4}$");
        return this.optional(element) || 9 < phoneNumber.length && reg.test(phoneNumber);
    }, "Please specify a valid phone number");
}

function initLoginValidation() {
    $(".login").length && $(".login-form").validate({
        focusInvalid: !1,
        // fields
        rules: {
            email: {
                required: !0,
                email: !0
            },
            password: {
                required: !0,
                minlength: 6
            }
        }
    });
}

function pageAnimation() {
    AOS.init({
        offset: 150,
        easing: "ease-in-out",
        once: !0,
        // delay: 100,
        //disable: "mobile"
    });
}

function offCanvasInit() {
    var offcanvas = $(".offcanvas-collapse"), header = $(".header");
    function setDefault() {
        offcanvas.css({
            top: header.innerHeight()
        });
    }
    $(window).on("orientationchange resize", setDefault), setDefault(), $('[data-toggle="offcanvas"]').on("click", function() {
        $(this).toggleClass("is-active"), offcanvas.toggleClass("is-open"), $("body").toggleClass("offcanvas-open");
    });
}

//-------- -------- -------- --------
//-------- js custom end
//-------- -------- -------- --------
jQuery(document).ready(function() {
    // Auto hide alert (flase) messages
    setTimeout(function() {
    // $('.').delay(4000).slideUp(300);
    $('.alert').delay(4000).slideUp("normal", function() { $('.alert-dismissible').remove(); } );

    }, 5000);
    offCanvasInit(), pageAnimation(), /*initValidationPlugin(), initLoginValidation(),*/ 
    initCollapseHandler(), /*initFormValidation(),*/ initTooltip(), initSteps();
}), function(e, t) {
    "object" == typeof exports && "object" == typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define([], t) : "object" == typeof exports ? exports.AOS = t() : e.AOS = t();
}(this, function() {
    return e = [ function(e, t, n) {
        "use strict";
        function o(e) {
            return e && e.__esModule ? e : {
                default: e
            };
        }
        function j() {
            if (0 < arguments.length && void 0 !== arguments[0] && arguments[0] && (k = !0), 
            k) return w = (0, y.default)(w, x), (0, b.default)(w, x.once), w;
        }
        function O() {
            w = (0, h.default)(), j();
        }
        var i = Object.assign || function(e) {
            for (var t = 1; t < arguments.length; t++) {
                var n = arguments[t];
                for (var o in n) Object.prototype.hasOwnProperty.call(n, o) && (e[o] = n[o]);
            }
            return e;
        }, u = o((o(n(1)), n(6))), f = o(n(7)), d = o(n(8)), p = o(n(9)), b = o(n(10)), y = o(n(11)), h = o(n(14)), w = [], k = !1, x = {
            offset: 120,
            delay: 0,
            easing: "ease",
            duration: 400,
            disable: !1,
            once: !1,
            startEvent: "DOMContentLoaded",
            throttleDelay: 99,
            debounceDelay: 50,
            disableMutationObserver: !1
        };
        e.exports = {
            init: function(e) {
                x = i(x, e), w = (0, h.default)();
                var t = document.all && !window.atob;
                return function(e) {
                    return !0 === e || "mobile" === e && p.default.mobile() || "phone" === e && p.default.phone() || "tablet" === e && p.default.tablet() || "function" == typeof e && !0 === e();
                }(x.disable) || t ? void w.forEach(function(e, t) {
                    e.node.removeAttribute("data-aos"), e.node.removeAttribute("data-aos-easing"), e.node.removeAttribute("data-aos-duration"), 
                    e.node.removeAttribute("data-aos-delay");
                }) : (document.querySelector("body").setAttribute("data-aos-easing", x.easing), 
                document.querySelector("body").setAttribute("data-aos-duration", x.duration), document.querySelector("body").setAttribute("data-aos-delay", x.delay), 
                "DOMContentLoaded" === x.startEvent && -1 < [ "complete", "interactive" ].indexOf(document.readyState) ? j(!0) : "load" === x.startEvent ? window.addEventListener(x.startEvent, function() {
                    j(!0);
                }) : document.addEventListener(x.startEvent, function() {
                    j(!0);
                }), window.addEventListener("resize", (0, f.default)(j, x.debounceDelay, !0)), window.addEventListener("orientationchange", (0, 
                f.default)(j, x.debounceDelay, !0)), window.addEventListener("scroll", (0, u.default)(function() {
                    (0, b.default)(w, x.once);
                }, x.throttleDelay)), x.disableMutationObserver || (0, d.default)("[data-aos]", O), 
                w);
            },
            refresh: j,
            refreshHard: O
        };
    }, function(e, t) {}, , , , , function(e, t) {
        (function(t) {
            "use strict";
            function n(e, t, n) {
                function o(t) {
                    var n = b, o = v;
                    return b = v = void 0, k = t, g = e.apply(o, n);
                }
                function c(e) {
                    var n = e - w;
                    return void 0 === w || t <= n || n < 0 || S && y <= e - k;
                }
                function s() {
                    var e = O();
                    return c(e) ? d(e) : void (h = setTimeout(s, function(e) {
                        var i = t - (e - w);
                        return S ? j(i, y - (e - k)) : i;
                    }(e)));
                }
                function d(e) {
                    return h = void 0, z && b ? o(e) : (b = v = void 0, g);
                }
                function m() {
                    var e = O(), n = c(e);
                    if (b = arguments, v = this, w = e, n) {
                        if (void 0 === h) return function(e) {
                            return k = e, h = setTimeout(s, t), _ ? o(e) : g;
                        }(w);
                        if (S) return h = setTimeout(s, t), o(w);
                    }
                    return void 0 === h && (h = setTimeout(s, t)), g;
                }
                var b, v, y, g, h, w, k = 0, _ = !1, S = !1, z = !0;
                if ("function" != typeof e) throw new TypeError(f);
                return t = u(t) || 0, i(n) && (_ = !!n.leading, y = (S = "maxWait" in n) ? x(u(n.maxWait) || 0, t) : y, 
                z = "trailing" in n ? !!n.trailing : z), m.cancel = function() {
                    void 0 !== h && clearTimeout(h), b = w = v = h = void (k = 0);
                }, m.flush = function() {
                    return void 0 === h ? g : d(O());
                }, m;
            }
            function i(e) {
                var t = void 0 === e ? "undefined" : c(e);
                return !!e && ("object" == t || "function" == t);
            }
            function a(e) {
                return "symbol" == (void 0 === e ? "undefined" : c(e)) || function(e) {
                    return !!e && "object" == (void 0 === e ? "undefined" : c(e));
                }(e) && k.call(e) == d;
            }
            function u(e) {
                if ("number" == typeof e) return e;
                if (a(e)) return s;
                if (i(e)) {
                    var t = "function" == typeof e.valueOf ? e.valueOf() : e;
                    e = i(t) ? t + "" : t;
                }
                if ("string" != typeof e) return 0 === e ? e : +e;
                e = e.replace(l, "");
                var n = m.test(e);
                return n || b.test(e) ? v(e.slice(2), n ? 2 : 8) : p.test(e) ? s : +e;
            }
            var c = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
                return typeof e;
            } : function(e) {
                return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
            }, f = "Expected a function", s = NaN, d = "[object Symbol]", l = /^\s+|\s+$/g, p = /^[-+]0x[0-9a-f]+$/i, m = /^0b[01]+$/i, b = /^0o[0-7]+$/i, v = parseInt, y = "object" == (void 0 === t ? "undefined" : c(t)) && t && t.Object === Object && t, g = "object" == ("undefined" == typeof self ? "undefined" : c(self)) && self && self.Object === Object && self, h = y || g || Function("return this")(), k = Object.prototype.toString, x = Math.max, j = Math.min, O = function() {
                return h.Date.now();
            };
            e.exports = function(e, t, o) {
                var r = !0, a = !0;
                if ("function" != typeof e) throw new TypeError(f);
                return i(o) && (r = "leading" in o ? !!o.leading : r, a = "trailing" in o ? !!o.trailing : a), 
                n(e, t, {
                    leading: r,
                    maxWait: t,
                    trailing: a
                });
            };
        }).call(t, function() {
            return this;
        }());
    }, function(e, t) {
        (function(t) {
            "use strict";
            function o(e) {
                var t = void 0 === e ? "undefined" : u(e);
                return !!e && ("object" == t || "function" == t);
            }
            function r(e) {
                return "symbol" == (void 0 === e ? "undefined" : u(e)) || function(e) {
                    return !!e && "object" == (void 0 === e ? "undefined" : u(e));
                }(e) && w.call(e) == s;
            }
            function a(e) {
                if ("number" == typeof e) return e;
                if (r(e)) return f;
                if (o(e)) {
                    var t = "function" == typeof e.valueOf ? e.valueOf() : e;
                    e = o(t) ? t + "" : t;
                }
                if ("string" != typeof e) return 0 === e ? e : +e;
                e = e.replace(d, "");
                var n = p.test(e);
                return n || m.test(e) ? b(e.slice(2), n ? 2 : 8) : l.test(e) ? f : +e;
            }
            function j() {
                return g.Date.now();
            }
            var u = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
                return typeof e;
            } : function(e) {
                return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
            }, f = NaN, s = "[object Symbol]", d = /^\s+|\s+$/g, l = /^[-+]0x[0-9a-f]+$/i, p = /^0b[01]+$/i, m = /^0o[0-7]+$/i, b = parseInt, v = "object" == (void 0 === t ? "undefined" : u(t)) && t && t.Object === Object && t, y = "object" == ("undefined" == typeof self ? "undefined" : u(self)) && self && self.Object === Object && self, g = v || y || Function("return this")(), w = Object.prototype.toString, k = Math.max, x = Math.min;
            e.exports = function(e, t, n) {
                function i(t) {
                    var n = b, o = v;
                    return b = v = void 0, O = t, g = e.apply(o, n);
                }
                function f(e) {
                    var n = e - w;
                    return void 0 === w || t <= n || n < 0 || S && y <= e - O;
                }
                function s() {
                    var e = j();
                    return f(e) ? d(e) : void (h = setTimeout(s, function(e) {
                        var i = t - (e - w);
                        return S ? x(i, y - (e - O)) : i;
                    }(e)));
                }
                function d(e) {
                    return h = void 0, z && b ? i(e) : (b = v = void 0, g);
                }
                function m() {
                    var e = j(), n = f(e);
                    if (b = arguments, v = this, w = e, n) {
                        if (void 0 === h) return function(e) {
                            return O = e, h = setTimeout(s, t), _ ? i(e) : g;
                        }(w);
                        if (S) return h = setTimeout(s, t), i(w);
                    }
                    return void 0 === h && (h = setTimeout(s, t)), g;
                }
                var b, v, y, g, h, w, O = 0, _ = !1, S = !1, z = !0;
                if ("function" != typeof e) throw new TypeError("Expected a function");
                return t = a(t) || 0, o(n) && (_ = !!n.leading, y = (S = "maxWait" in n) ? k(a(n.maxWait) || 0, t) : y, 
                z = "trailing" in n ? !!n.trailing : z), m.cancel = function() {
                    void 0 !== h && clearTimeout(h), b = w = v = h = void (O = 0);
                }, m.flush = function() {
                    return void 0 === h ? g : d(j());
                }, m;
            };
        }).call(t, function() {
            return this;
        }());
    }, function(e, t) {
        "use strict";
        function o(e) {
            e && e.forEach(function(e) {
                var t = Array.prototype.slice.call(e.addedNodes), n = Array.prototype.slice.call(e.removedNodes);
                t.concat(n).filter(function(e) {
                    return e.hasAttribute && e.hasAttribute("data-aos");
                }).length && i();
            });
        }
        Object.defineProperty(t, "__esModule", {
            value: !0
        });
        var i = function() {};
        t.default = function(e, t) {
            var n = window.document, a = new (window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver)(o);
            i = t, a.observe(n.documentElement, {
                childList: !0,
                subtree: !0,
                removedNodes: !0
            });
        };
    }, function(e, t) {
        "use strict";
        function o() {
            return navigator.userAgent || navigator.vendor || window.opera || "";
        }
        Object.defineProperty(t, "__esModule", {
            value: !0
        });
        var i = function() {
            function e(e, t) {
                for (var n = 0; n < t.length; n++) {
                    var o = t[n];
                    o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), 
                    Object.defineProperty(e, o.key, o);
                }
            }
            return function(t, n, o) {
                return n && e(t.prototype, n), o && e(t, o), t;
            };
        }(), r = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i, a = /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i, u = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i, c = /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i, f = function() {
            function e() {
                !function(e, t) {
                    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
                }(this, e);
            }
            return i(e, [ {
                key: "phone",
                value: function() {
                    var e = o();
                    return !(!r.test(e) && !a.test(e.substr(0, 4)));
                }
            }, {
                key: "mobile",
                value: function() {
                    var e = o();
                    return !(!u.test(e) && !c.test(e.substr(0, 4)));
                }
            }, {
                key: "tablet",
                value: function() {
                    return this.mobile() && !this.phone();
                }
            } ]), e;
        }();
        t.default = new f();
    }, function(e, t) {
        "use strict";
        Object.defineProperty(t, "__esModule", {
            value: !0
        });
        t.default = function(e, t) {
            var o = window.pageYOffset, i = window.innerHeight;
            e.forEach(function(e, r) {
                !function(e, t, n) {
                    var o = e.node.getAttribute("data-aos-once");
                    t > e.position ? e.node.classList.add("aos-animate") : void 0 === o || "false" !== o && (n || "true" === o) || e.node.classList.remove("aos-animate");
                }(e, i + o, t);
            });
        };
    }, function(e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {
            value: !0
        });
        var r = function(e) {
            return e && e.__esModule ? e : {
                default: e
            };
        }(n(12));
        t.default = function(e, t) {
            return e.forEach(function(e, n) {
                e.node.classList.add("aos-init"), e.position = (0, r.default)(e.node, t.offset);
            }), e;
        };
    }, function(e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {
            value: !0
        });
        var r = function(e) {
            return e && e.__esModule ? e : {
                default: e
            };
        }(n(13));
        t.default = function(e, t) {
            var n = 0, o = 0, i = window.innerHeight, a = {
                offset: e.getAttribute("data-aos-offset"),
                anchor: e.getAttribute("data-aos-anchor"),
                anchorPlacement: e.getAttribute("data-aos-anchor-placement")
            };
            switch (a.offset && !isNaN(a.offset) && (o = parseInt(a.offset)), a.anchor && document.querySelectorAll(a.anchor) && (e = document.querySelectorAll(a.anchor)[0]), 
            n = (0, r.default)(e).top, a.anchorPlacement) {
              case "top-bottom":
                break;

              case "center-bottom":
                n += e.offsetHeight / 2;
                break;

              case "bottom-bottom":
                n += e.offsetHeight;
                break;

              case "top-center":
                n += i / 2;
                break;

              case "bottom-center":
                n += i / 2 + e.offsetHeight;
                break;

              case "center-center":
                n += i / 2 + e.offsetHeight / 2;
                break;

              case "top-top":
                n += i;
                break;

              case "bottom-top":
                n += e.offsetHeight + i;
                break;

              case "center-top":
                n += e.offsetHeight / 2 + i;
            }
            return a.anchorPlacement || a.offset || isNaN(t) || (o = t), n + o;
        };
    }, function(e, t) {
        "use strict";
        Object.defineProperty(t, "__esModule", {
            value: !0
        });
        t.default = function(e) {
            for (var t = 0, n = 0; e && !isNaN(e.offsetLeft) && !isNaN(e.offsetTop); ) t += e.offsetLeft - ("BODY" != e.tagName ? e.scrollLeft : 0), 
            n += e.offsetTop - ("BODY" != e.tagName ? e.scrollTop : 0), e = e.offsetParent;
            return {
                top: n,
                left: t
            };
        };
    }, function(e, t) {
        "use strict";
        Object.defineProperty(t, "__esModule", {
            value: !0
        });
        t.default = function(e) {
            return e = e || document.querySelectorAll("[data-aos]"), Array.prototype.map.call(e, function(e) {
                return {
                    node: e
                };
            });
        };
    } ], n = {}, t.m = e, t.c = n, t.p = "dist/", t(0);
    function t(o) {
        if (n[o]) return n[o].exports;
        var i = n[o] = {
            exports: {},
            id: o,
            loaded: !1
        };
        return e[o].call(i.exports, i, i.exports, t), i.loaded = !0, i.exports;
    }
    var e, n;
}), function(a) {
    "function" == typeof define && define.amd ? define([ "jquery" ], a) : "object" == typeof module && module.exports ? module.exports = a(require("jquery")) : a(jQuery);
}(function(a) {
    a.extend(a.fn, {
        validate: function(b) {
            if (this.length) {
                var c = a.data(this[0], "validator");
                return c || (this.attr("novalidate", "novalidate"), c = new a.validator(b, this[0]), 
                a.data(this[0], "validator", c), c.settings.onsubmit && (this.on("click.validate", ":submit", function(b) {
                    c.submitButton = b.currentTarget, a(this).hasClass("cancel") && (c.cancelSubmit = !0), 
                    void 0 !== a(this).attr("formnovalidate") && (c.cancelSubmit = !0);
                }), this.on("submit.validate", function(b) {
                    function d() {
                        var d, e;
                        return c.submitButton && (c.settings.submitHandler || c.formSubmitted) && (d = a("<input type='hidden'/>").attr("name", c.submitButton.name).val(a(c.submitButton).val()).appendTo(c.currentForm)), 
                        !(c.settings.submitHandler && !c.settings.debug) || (e = c.settings.submitHandler.call(c, c.currentForm, b), 
                        d && d.remove(), void 0 !== e && e);
                    }
                    return c.settings.debug && b.preventDefault(), c.cancelSubmit ? (c.cancelSubmit = !1, 
                    d()) : c.form() ? c.pendingRequest ? !(c.formSubmitted = !0) : d() : (c.focusInvalid(), 
                    !1);
                })), c);
            }
            b && b.debug && window.console && console.warn("Nothing selected, can't validate, returning nothing.");
        },
        valid: function() {
            var b, c, d;
            return a(this[0]).is("form") ? b = this.validate().form() : (d = [], b = !0, c = a(this[0].form).validate(), 
            this.each(function() {
                (b = c.element(this) && b) || (d = d.concat(c.errorList));
            }), c.errorList = d), b;
        },
        rules: function(b, c) {
            var d, e, f, g, h, i, j = this[0], k = void 0 !== this.attr("contenteditable") && "false" !== this.attr("contenteditable");
            if (null != j && (!j.form && k && (j.form = this.closest("form")[0], j.name = this.attr("name")), 
            null != j.form)) {
                if (b) switch (d = a.data(j.form, "validator").settings, e = d.rules, f = a.validator.staticRules(j), 
                b) {
                  case "add":
                    a.extend(f, a.validator.normalizeRule(c)), delete f.messages, e[j.name] = f, c.messages && (d.messages[j.name] = a.extend(d.messages[j.name], c.messages));
                    break;

                  case "remove":
                    return c ? (i = {}, a.each(c.split(/\s/), function(a, b) {
                        i[b] = f[b], delete f[b];
                    }), i) : (delete e[j.name], f);
                }
                return (g = a.validator.normalizeRules(a.extend({}, a.validator.classRules(j), a.validator.attributeRules(j), a.validator.dataRules(j), a.validator.staticRules(j)), j)).required && (h = g.required, 
                delete g.required, g = a.extend({
                    required: h
                }, g)), g.remote && (h = g.remote, delete g.remote, g = a.extend(g, {
                    remote: h
                })), g;
            }
        }
    }), a.extend(a.expr.pseudos || a.expr[":"], {
        blank: function(b) {
            return !a.trim("" + a(b).val());
        },
        filled: function(b) {
            var c = a(b).val();
            return null !== c && !!a.trim("" + c);
        },
        unchecked: function(b) {
            return !a(b).prop("checked");
        }
    }), a.validator = function(b, c) {
        this.settings = a.extend(!0, {}, a.validator.defaults, b), this.currentForm = c, 
        this.init();
    }, a.validator.format = function(b, c) {
        return 1 === arguments.length ? function() {
            var c = a.makeArray(arguments);
            return c.unshift(b), a.validator.format.apply(this, c);
        } : (void 0 === c || (2 < arguments.length && c.constructor !== Array && (c = a.makeArray(arguments).slice(1)), 
        c.constructor !== Array && (c = [ c ]), a.each(c, function(a, c) {
            b = b.replace(new RegExp("\\{" + a + "\\}", "g"), function() {
                return c;
            });
        })), b);
    }, a.extend(a.validator, {
        defaults: {
            messages: {},
            groups: {},
            rules: {},
            errorClass: "error",
            pendingClass: "pending",
            validClass: "valid",
            errorElement: "label",
            focusCleanup: !1,
            focusInvalid: !0,
            errorContainer: a([]),
            errorLabelContainer: a([]),
            onsubmit: !0,
            ignore: ":hidden",
            ignoreTitle: !1,
            onfocusin: function(a) {
                this.lastActive = a, this.settings.focusCleanup && (this.settings.unhighlight && this.settings.unhighlight.call(this, a, this.settings.errorClass, this.settings.validClass), 
                this.hideThese(this.errorsFor(a)));
            },
            onfocusout: function(a) {
                this.checkable(a) || !(a.name in this.submitted) && this.optional(a) || this.element(a);
            },
            onkeyup: function(b, c) {
                9 === c.which && "" === this.elementValue(b) || -1 !== a.inArray(c.keyCode, [ 16, 17, 18, 20, 35, 36, 37, 38, 39, 40, 45, 144, 225 ]) || (b.name in this.submitted || b.name in this.invalid) && this.element(b);
            },
            onclick: function(a) {
                a.name in this.submitted ? this.element(a) : a.parentNode.name in this.submitted && this.element(a.parentNode);
            },
            highlight: function(b, c, d) {
                "radio" === b.type ? this.findByName(b.name).addClass(c).removeClass(d) : a(b).addClass(c).removeClass(d);
            },
            unhighlight: function(b, c, d) {
                "radio" === b.type ? this.findByName(b.name).removeClass(c).addClass(d) : a(b).removeClass(c).addClass(d);
            }
        },
        setDefaults: function(b) {
            a.extend(a.validator.defaults, b);
        },
        messages: {
            required: "This field is required.",
            remote: "Please fix this field.",
            email: "Please enter a valid email address.",
            url: "Please enter a valid URL.",
            date: "Please enter a valid date.",
            dateISO: "Please enter a valid date (ISO).",
            number: "Please enter a valid number.",
            digits: "Please enter only digits.",
            equalTo: "Please enter the same value again.",
            maxlength: a.validator.format("Please enter no more than {0} characters."),
            minlength: a.validator.format("Please enter at least {0} characters."),
            rangelength: a.validator.format("Please enter a value between {0} and {1} characters long."),
            range: a.validator.format("Please enter a value between {0} and {1}."),
            max: a.validator.format("Please enter a value less than or equal to {0}."),
            min: a.validator.format("Please enter a value greater than or equal to {0}."),
            step: a.validator.format("Please enter a multiple of {0}.")
        },
        autoCreateRanges: !1,
        prototype: {
            init: function() {
                function b(b) {
                    var c = void 0 !== a(this).attr("contenteditable") && "false" !== a(this).attr("contenteditable");
                    if (!this.form && c && (this.form = a(this).closest("form")[0], this.name = a(this).attr("name")), 
                    d === this.form) {
                        var e = a.data(this.form, "validator"), f = "on" + b.type.replace(/^validate/, ""), g = e.settings;
                        g[f] && !a(this).is(g.ignore) && g[f].call(e, this, b);
                    }
                }
                this.labelContainer = a(this.settings.errorLabelContainer), this.errorContext = this.labelContainer.length && this.labelContainer || a(this.currentForm), 
                this.containers = a(this.settings.errorContainer).add(this.settings.errorLabelContainer), 
                this.submitted = {}, this.valueCache = {}, this.pendingRequest = 0, this.pending = {}, 
                this.invalid = {}, this.reset();
                var c, d = this.currentForm, e = this.groups = {};
                a.each(this.settings.groups, function(b, c) {
                    "string" == typeof c && (c = c.split(/\s/)), a.each(c, function(a, c) {
                        e[c] = b;
                    });
                }), c = this.settings.rules, a.each(c, function(b, d) {
                    c[b] = a.validator.normalizeRule(d);
                }), a(this.currentForm).on("focusin.validate focusout.validate keyup.validate", ":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'], [type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], [type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], [type='radio'], [type='checkbox'], [contenteditable], [type='button']", b).on("click.validate", "select, option, [type='radio'], [type='checkbox']", b), 
                this.settings.invalidHandler && a(this.currentForm).on("invalid-form.validate", this.settings.invalidHandler);
            },
            form: function() {
                return this.checkForm(), a.extend(this.submitted, this.errorMap), this.invalid = a.extend({}, this.errorMap), 
                this.valid() || a(this.currentForm).triggerHandler("invalid-form", [ this ]), this.showErrors(), 
                this.valid();
            },
            checkForm: function() {
                this.prepareForm();
                for (var a = 0, b = this.currentElements = this.elements(); b[a]; a++) this.check(b[a]);
                return this.valid();
            },
            element: function(b) {
                var c, d, e = this.clean(b), f = this.validationTargetFor(e), g = this, h = !0;
                return void 0 === f ? delete this.invalid[e.name] : (this.prepareElement(f), this.currentElements = a(f), 
                (d = this.groups[f.name]) && a.each(this.groups, function(a, b) {
                    b === d && a !== f.name && ((e = g.validationTargetFor(g.clean(g.findByName(a)))) && e.name in g.invalid && (g.currentElements.push(e), 
                    h = g.check(e) && h));
                }), c = !1 !== this.check(f), h = h && c, this.invalid[f.name] = !c, this.numberOfInvalids() || (this.toHide = this.toHide.add(this.containers)), 
                this.showErrors(), a(b).attr("aria-invalid", !c)), h;
            },
            showErrors: function(b) {
                if (b) {
                    var c = this;
                    a.extend(this.errorMap, b), this.errorList = a.map(this.errorMap, function(a, b) {
                        return {
                            message: a,
                            element: c.findByName(b)[0]
                        };
                    }), this.successList = a.grep(this.successList, function(a) {
                        return !(a.name in b);
                    });
                }
                this.settings.showErrors ? this.settings.showErrors.call(this, this.errorMap, this.errorList) : this.defaultShowErrors();
            },
            resetForm: function() {
                a.fn.resetForm && a(this.currentForm).resetForm(), this.invalid = {}, this.submitted = {}, 
                this.prepareForm(), this.hideErrors();
                var b = this.elements().removeData("previousValue").removeAttr("aria-invalid");
                this.resetElements(b);
            },
            resetElements: function(a) {
                var b;
                if (this.settings.unhighlight) for (b = 0; a[b]; b++) this.settings.unhighlight.call(this, a[b], this.settings.errorClass, ""), 
                this.findByName(a[b].name).removeClass(this.settings.validClass); else a.removeClass(this.settings.errorClass).removeClass(this.settings.validClass);
            },
            numberOfInvalids: function() {
                return this.objectLength(this.invalid);
            },
            objectLength: function(a) {
                var b, c = 0;
                for (b in a) void 0 !== a[b] && null !== a[b] && !1 !== a[b] && c++;
                return c;
            },
            hideErrors: function() {
                this.hideThese(this.toHide);
            },
            hideThese: function(a) {
                a.not(this.containers).text(""), this.addWrapper(a).hide();
            },
            valid: function() {
                return 0 === this.size();
            },
            size: function() {
                return this.errorList.length;
            },
            focusInvalid: function() {
                if (this.settings.focusInvalid) try {
                    a(this.findLastActive() || this.errorList.length && this.errorList[0].element || []).filter(":visible").trigger("focus").trigger("focusin");
                } catch (b) {}
            },
            findLastActive: function() {
                var b = this.lastActive;
                return b && 1 === a.grep(this.errorList, function(a) {
                    return a.element.name === b.name;
                }).length && b;
            },
            elements: function() {
                var b = this, c = {};
                return a(this.currentForm).find("input, select, textarea, [contenteditable]").not(":submit, :reset, :image, :disabled").not(this.settings.ignore).filter(function() {
                    var d = this.name || a(this).attr("name"), e = void 0 !== a(this).attr("contenteditable") && "false" !== a(this).attr("contenteditable");
                    return !d && b.settings.debug && window.console && console.error("%o has no name assigned", this), 
                    e && (this.form = a(this).closest("form")[0], this.name = d), !(this.form !== b.currentForm || d in c || !b.objectLength(a(this).rules()) || (c[d] = !0, 
                    0));
                });
            },
            clean: function(b) {
                return a(b)[0];
            },
            errors: function() {
                var b = this.settings.errorClass.split(" ").join(".");
                return a(this.settings.errorElement + "." + b, this.errorContext);
            },
            resetInternals: function() {
                this.successList = [], this.errorList = [], this.errorMap = {}, this.toShow = a([]), 
                this.toHide = a([]);
            },
            reset: function() {
                this.resetInternals(), this.currentElements = a([]);
            },
            prepareForm: function() {
                this.reset(), this.toHide = this.errors().add(this.containers);
            },
            prepareElement: function(a) {
                this.reset(), this.toHide = this.errorsFor(a);
            },
            elementValue: function(b) {
                var c, d, e = a(b), f = b.type, g = void 0 !== e.attr("contenteditable") && "false" !== e.attr("contenteditable");
                return "radio" === f || "checkbox" === f ? this.findByName(b.name).filter(":checked").val() : "number" === f && void 0 !== b.validity ? b.validity.badInput ? "NaN" : e.val() : (c = g ? e.text() : e.val(), 
                "file" === f ? "C:\\fakepath\\" === c.substr(0, 12) ? c.substr(12) : 0 <= (d = c.lastIndexOf("/")) ? c.substr(d + 1) : 0 <= (d = c.lastIndexOf("\\")) ? c.substr(d + 1) : c : "string" == typeof c ? c.replace(/\r/g, "") : c);
            },
            check: function(b) {
                b = this.validationTargetFor(this.clean(b));
                var c, d, e, f, g = a(b).rules(), h = a.map(g, function(a, b) {
                    return b;
                }).length, i = !1, j = this.elementValue(b);
                for (d in "function" == typeof g.normalizer ? f = g.normalizer : "function" == typeof this.settings.normalizer && (f = this.settings.normalizer), 
                f && (j = f.call(b, j), delete g.normalizer), g) {
                    e = {
                        method: d,
                        parameters: g[d]
                    };
                    try {
                        if ("dependency-mismatch" === (c = a.validator.methods[d].call(this, j, b, e.parameters)) && 1 === h) {
                            i = !0;
                            continue;
                        }
                        if (i = !1, "pending" === c) return void (this.toHide = this.toHide.not(this.errorsFor(b)));
                        if (!c) return this.formatAndAdd(b, e), !1;
                    } catch (k) {
                        throw this.settings.debug && window.console && console.log("Exception occurred when checking element " + b.id + ", check the '" + e.method + "' method.", k), 
                        k instanceof TypeError && (k.message += ".  Exception occurred when checking element " + b.id + ", check the '" + e.method + "' method."), 
                        k;
                    }
                }
                if (!i) return this.objectLength(g) && this.successList.push(b), !0;
            },
            customDataMessage: function(b, c) {
                return a(b).data("msg" + c.charAt(0).toUpperCase() + c.substring(1).toLowerCase()) || a(b).data("msg");
            },
            customMessage: function(a, b) {
                var c = this.settings.messages[a];
                return c && (c.constructor === String ? c : c[b]);
            },
            findDefined: function() {
                for (var a = 0; a < arguments.length; a++) if (void 0 !== arguments[a]) return arguments[a];
            },
            defaultMessage: function(b, c) {
                "string" == typeof c && (c = {
                    method: c
                });
                var d = this.findDefined(this.customMessage(b.name, c.method), this.customDataMessage(b, c.method), !this.settings.ignoreTitle && b.title || void 0, a.validator.messages[c.method], "<strong>Warning: No message defined for " + b.name + "</strong>"), e = /\$?\{(\d+)\}/g;
                return "function" == typeof d ? d = d.call(this, c.parameters, b) : e.test(d) && (d = a.validator.format(d.replace(e, "{$1}"), c.parameters)), 
                d;
            },
            formatAndAdd: function(a, b) {
                var c = this.defaultMessage(a, b);
                this.errorList.push({
                    message: c,
                    element: a,
                    method: b.method
                }), this.errorMap[a.name] = c, this.submitted[a.name] = c;
            },
            addWrapper: function(a) {
                return this.settings.wrapper && (a = a.add(a.parent(this.settings.wrapper))), a;
            },
            defaultShowErrors: function() {
                var a, b, c;
                for (a = 0; this.errorList[a]; a++) c = this.errorList[a], this.settings.highlight && this.settings.highlight.call(this, c.element, this.settings.errorClass, this.settings.validClass), 
                this.showLabel(c.element, c.message);
                if (this.errorList.length && (this.toShow = this.toShow.add(this.containers)), this.settings.success) for (a = 0; this.successList[a]; a++) this.showLabel(this.successList[a]);
                if (this.settings.unhighlight) for (a = 0, b = this.validElements(); b[a]; a++) this.settings.unhighlight.call(this, b[a], this.settings.errorClass, this.settings.validClass);
                this.toHide = this.toHide.not(this.toShow), this.hideErrors(), this.addWrapper(this.toShow).show();
            },
            validElements: function() {
                return this.currentElements.not(this.invalidElements());
            },
            invalidElements: function() {
                return a(this.errorList).map(function() {
                    return this.element;
                });
            },
            showLabel: function(b, c) {
                var d, e, f, g, h = this.errorsFor(b), i = this.idOrName(b), j = a(b).attr("aria-describedby");
                h.length ? (h.removeClass(this.settings.validClass).addClass(this.settings.errorClass), 
                h.html(c)) : (d = h = a("<" + this.settings.errorElement + ">").attr("id", i + "-error").addClass(this.settings.errorClass).html(c || ""), 
                this.settings.wrapper && (d = h.hide().show().wrap("<" + this.settings.wrapper + "/>").parent()), 
                this.labelContainer.length ? this.labelContainer.append(d) : this.settings.errorPlacement ? this.settings.errorPlacement.call(this, d, a(b)) : d.insertAfter(b), 
                h.is("label") ? h.attr("for", i) : 0 === h.parents("label[for='" + this.escapeCssMeta(i) + "']").length && (f = h.attr("id"), 
                j ? j.match(new RegExp("\\b" + this.escapeCssMeta(f) + "\\b")) || (j += " " + f) : j = f, 
                a(b).attr("aria-describedby", j), (e = this.groups[b.name]) && (g = this, a.each(g.groups, function(b, c) {
                    c === e && a("[name='" + g.escapeCssMeta(b) + "']", g.currentForm).attr("aria-describedby", h.attr("id"));
                })))), !c && this.settings.success && (h.text(""), "string" == typeof this.settings.success ? h.addClass(this.settings.success) : this.settings.success(h, b)), 
                this.toShow = this.toShow.add(h);
            },
            errorsFor: function(b) {
                var c = this.escapeCssMeta(this.idOrName(b)), d = a(b).attr("aria-describedby"), e = "label[for='" + c + "'], label[for='" + c + "'] *";
                return d && (e = e + ", #" + this.escapeCssMeta(d).replace(/\s+/g, ", #")), this.errors().filter(e);
            },
            escapeCssMeta: function(a) {
                return a.replace(/([\\!"#$%&'()*+,.\/:;<=>?@\[\]^`{|}~])/g, "\\$1");
            },
            idOrName: function(a) {
                return this.groups[a.name] || (this.checkable(a) ? a.name : a.id || a.name);
            },
            validationTargetFor: function(b) {
                return this.checkable(b) && (b = this.findByName(b.name)), a(b).not(this.settings.ignore)[0];
            },
            checkable: function(a) {
                return /radio|checkbox/i.test(a.type);
            },
            findByName: function(b) {
                return a(this.currentForm).find("[name='" + this.escapeCssMeta(b) + "']");
            },
            getLength: function(b, c) {
                switch (c.nodeName.toLowerCase()) {
                  case "select":
                    return a("option:selected", c).length;

                  case "input":
                    if (this.checkable(c)) return this.findByName(c.name).filter(":checked").length;
                }
                return b.length;
            },
            depend: function(a, b) {
                return !this.dependTypes[typeof a] || this.dependTypes[typeof a](a, b);
            },
            dependTypes: {
                boolean: function(a) {
                    return a;
                },
                string: function(b, c) {
                    return !!a(b, c.form).length;
                },
                function: function(a, b) {
                    return a(b);
                }
            },
            optional: function(b) {
                var c = this.elementValue(b);
                return !a.validator.methods.required.call(this, c, b) && "dependency-mismatch";
            },
            startRequest: function(b) {
                this.pending[b.name] || (this.pendingRequest++, a(b).addClass(this.settings.pendingClass), 
                this.pending[b.name] = !0);
            },
            stopRequest: function(b, c) {
                this.pendingRequest--, this.pendingRequest < 0 && (this.pendingRequest = 0), delete this.pending[b.name], 
                a(b).removeClass(this.settings.pendingClass), c && 0 === this.pendingRequest && this.formSubmitted && this.form() ? (a(this.currentForm).submit(), 
                this.submitButton && a("input:hidden[name='" + this.submitButton.name + "']", this.currentForm).remove(), 
                this.formSubmitted = !1) : !c && 0 === this.pendingRequest && this.formSubmitted && (a(this.currentForm).triggerHandler("invalid-form", [ this ]), 
                this.formSubmitted = !1);
            },
            previousValue: function(b, c) {
                return c = "string" == typeof c && c || "remote", a.data(b, "previousValue") || a.data(b, "previousValue", {
                    old: null,
                    valid: !0,
                    message: this.defaultMessage(b, {
                        method: c
                    })
                });
            },
            destroy: function() {
                this.resetForm(), a(this.currentForm).off(".validate").removeData("validator").find(".validate-equalTo-blur").off(".validate-equalTo").removeClass("validate-equalTo-blur").find(".validate-lessThan-blur").off(".validate-lessThan").removeClass("validate-lessThan-blur").find(".validate-lessThanEqual-blur").off(".validate-lessThanEqual").removeClass("validate-lessThanEqual-blur").find(".validate-greaterThanEqual-blur").off(".validate-greaterThanEqual").removeClass("validate-greaterThanEqual-blur").find(".validate-greaterThan-blur").off(".validate-greaterThan").removeClass("validate-greaterThan-blur");
            }
        },
        classRuleSettings: {
            required: {
                required: !0
            },
            email: {
                email: !0
            },
            url: {
                url: !0
            },
            date: {
                date: !0
            },
            dateISO: {
                dateISO: !0
            },
            number: {
                number: !0
            },
            digits: {
                digits: !0
            },
            creditcard: {
                creditcard: !0
            }
        },
        addClassRules: function(b, c) {
            b.constructor === String ? this.classRuleSettings[b] = c : a.extend(this.classRuleSettings, b);
        },
        classRules: function(b) {
            var c = {}, d = a(b).attr("class");
            return d && a.each(d.split(" "), function() {
                this in a.validator.classRuleSettings && a.extend(c, a.validator.classRuleSettings[this]);
            }), c;
        },
        normalizeAttributeRule: function(a, b, c, d) {
            /min|max|step/.test(c) && (null === b || /number|range|text/.test(b)) && (d = Number(d), 
            isNaN(d) && (d = void 0)), d || 0 === d ? a[c] = d : b === c && "range" !== b && (a[c] = !0);
        },
        attributeRules: function(b) {
            var c, d, e = {}, f = a(b), g = b.getAttribute("type");
            for (c in a.validator.methods) d = "required" === c ? ("" === (d = b.getAttribute(c)) && (d = !0), 
            !!d) : f.attr(c), this.normalizeAttributeRule(e, g, c, d);
            return e.maxlength && /-1|2147483647|524288/.test(e.maxlength) && delete e.maxlength, 
            e;
        },
        dataRules: function(b) {
            var c, d, e = {}, f = a(b), g = b.getAttribute("type");
            for (c in a.validator.methods) "" === (d = f.data("rule" + c.charAt(0).toUpperCase() + c.substring(1).toLowerCase())) && (d = !0), 
            this.normalizeAttributeRule(e, g, c, d);
            return e;
        },
        staticRules: function(b) {
            var c = {}, d = a.data(b.form, "validator");
            return d.settings.rules && (c = a.validator.normalizeRule(d.settings.rules[b.name]) || {}), 
            c;
        },
        normalizeRules: function(b, c) {
            return a.each(b, function(d, e) {
                if (!1 !== e) {
                    if (e.param || e.depends) {
                        var f = !0;
                        switch (typeof e.depends) {
                          case "string":
                            f = !!a(e.depends, c.form).length;
                            break;

                          case "function":
                            f = e.depends.call(c, c);
                        }
                        f ? b[d] = void 0 === e.param || e.param : (a.data(c.form, "validator").resetElements(a(c)), 
                        delete b[d]);
                    }
                } else delete b[d];
            }), a.each(b, function(d, e) {
                b[d] = a.isFunction(e) && "normalizer" !== d ? e(c) : e;
            }), a.each([ "minlength", "maxlength" ], function() {
                b[this] && (b[this] = Number(b[this]));
            }), a.each([ "rangelength", "range" ], function() {
                var c;
                b[this] && (a.isArray(b[this]) ? b[this] = [ Number(b[this][0]), Number(b[this][1]) ] : "string" == typeof b[this] && (c = b[this].replace(/[\[\]]/g, "").split(/[\s,]+/), 
                b[this] = [ Number(c[0]), Number(c[1]) ]));
            }), a.validator.autoCreateRanges && (null != b.min && null != b.max && (b.range = [ b.min, b.max ], 
            delete b.min, delete b.max), null != b.minlength && null != b.maxlength && (b.rangelength = [ b.minlength, b.maxlength ], 
            delete b.minlength, delete b.maxlength)), b;
        },
        normalizeRule: function(b) {
            if ("string" == typeof b) {
                var c = {};
                a.each(b.split(/\s/), function() {
                    c[this] = !0;
                }), b = c;
            }
            return b;
        },
        addMethod: function(b, c, d) {
            a.validator.methods[b] = c, a.validator.messages[b] = void 0 !== d ? d : a.validator.messages[b], 
            c.length < 3 && a.validator.addClassRules(b, a.validator.normalizeRule(b));
        },
        methods: {
            required: function(b, c, d) {
                if (!this.depend(d, c)) return "dependency-mismatch";
                if ("select" !== c.nodeName.toLowerCase()) return this.checkable(c) ? 0 < this.getLength(b, c) : null != b && 0 < b.length;
                var e = a(c).val();
                return e && 0 < e.length;
            },
            email: function(a, b) {
                return this.optional(b) || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(a);
            },
            url: function(a, b) {
                return this.optional(b) || /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[\/?#]\S*)?$/i.test(a);
            },
            date: function() {
                var a = !1;
                return function(b, c) {
                    return a || (a = !0, this.settings.debug && window.console && console.warn("The `date` method is deprecated and will be removed in version '2.0.0'.\nPlease don't use it, since it relies on the Date constructor, which\nbehaves very differently across browsers and locales. Use `dateISO`\ninstead or one of the locale specific methods in `localizations/`\nand `additional-methods.js`.")), 
                    this.optional(c) || !/Invalid|NaN/.test(new Date(b).toString());
                };
            }(),
            dateISO: function(a, b) {
                return this.optional(b) || /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(a);
            },
            number: function(a, b) {
                return this.optional(b) || /^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(a);
            },
            digits: function(a, b) {
                return this.optional(b) || /^\d+$/.test(a);
            },
            minlength: function(b, c, d) {
                var e = a.isArray(b) ? b.length : this.getLength(b, c);
                return this.optional(c) || d <= e;
            },
            maxlength: function(b, c, d) {
                var e = a.isArray(b) ? b.length : this.getLength(b, c);
                return this.optional(c) || e <= d;
            },
            rangelength: function(b, c, d) {
                var e = a.isArray(b) ? b.length : this.getLength(b, c);
                return this.optional(c) || e >= d[0] && e <= d[1];
            },
            min: function(a, b, c) {
                return this.optional(b) || c <= a;
            },
            max: function(a, b, c) {
                return this.optional(b) || a <= c;
            },
            range: function(a, b, c) {
                return this.optional(b) || a >= c[0] && a <= c[1];
            },
            step: function(b, c, d) {
                function k(a) {
                    var b = ("" + a).match(/(?:\.(\d+))?$/);
                    return b && b[1] ? b[1].length : 0;
                }
                function l(a) {
                    return Math.round(a * Math.pow(10, e));
                }
                var e, f = a(c).attr("type"), g = "Step attribute on input type " + f + " is not supported.", i = new RegExp("\\b" + f + "\\b"), m = !0;
                if (f && !i.test([ "text", "number", "range" ].join())) throw new Error(g);
                return e = k(d), (k(b) > e || l(b) % l(d) != 0) && (m = !1), this.optional(c) || m;
            },
            equalTo: function(b, c, d) {
                var e = a(d);
                return this.settings.onfocusout && e.not(".validate-equalTo-blur").length && e.addClass("validate-equalTo-blur").on("blur.validate-equalTo", function() {
                    a(c).valid();
                }), b === e.val();
            },
            remote: function(b, c, d, e) {
                if (this.optional(c)) return "dependency-mismatch";
                e = "string" == typeof e && e || "remote";
                var f, g, h, i = this.previousValue(c, e);
                return this.settings.messages[c.name] || (this.settings.messages[c.name] = {}), 
                i.originalMessage = i.originalMessage || this.settings.messages[c.name][e], this.settings.messages[c.name][e] = i.message, 
                d = "string" == typeof d && {
                    url: d
                } || d, h = a.param(a.extend({
                    data: b
                }, d.data)), i.old === h ? i.valid : (i.old = h, (f = this).startRequest(c), (g = {})[c.name] = b, 
                a.ajax(a.extend(!0, {
                    mode: "abort",
                    port: "validate" + c.name,
                    dataType: "json",
                    data: g,
                    context: f.currentForm,
                    success: function(a) {
                        var d, g, h, j = !0 === a || "true" === a;
                        f.settings.messages[c.name][e] = i.originalMessage, j ? (h = f.formSubmitted, f.resetInternals(), 
                        f.toHide = f.errorsFor(c), f.formSubmitted = h, f.successList.push(c), f.invalid[c.name] = !1, 
                        f.showErrors()) : (d = {}, g = a || f.defaultMessage(c, {
                            method: e,
                            parameters: b
                        }), d[c.name] = i.message = g, f.invalid[c.name] = !0, f.showErrors(d)), i.valid = j, 
                        f.stopRequest(c, j);
                    }
                }, d)), "pending");
            }
        }
    });
    var b, c = {};
    return a.ajaxPrefilter ? a.ajaxPrefilter(function(a, b, d) {
        var e = a.port;
        "abort" === a.mode && (c[e] && c[e].abort(), c[e] = d);
    }) : (b = a.ajax, a.ajax = function(d) {
        var e = ("mode" in d ? d : a.ajaxSettings).mode, f = ("port" in d ? d : a.ajaxSettings).port;
        return "abort" === e ? (c[f] && c[f].abort(), c[f] = b.apply(this, arguments), c[f]) : b.apply(this, arguments);
    }), a;
}), function(a) {
    "function" == typeof define && define.amd ? define([ "jquery", "./jquery.validate.min" ], a) : "object" == typeof module && module.exports ? module.exports = a(require("jquery")) : a(jQuery);
}(function(a) {
    return a.validator.addMethod("maxWords", function(a, c, d) {
        return this.optional(c) || b(a).match(/\b\w+\b/g).length <= d;
    }, a.validator.format("Please enter {0} words or less.")), a.validator.addMethod("minWords", function(a, c, d) {
        return this.optional(c) || b(a).match(/\b\w+\b/g).length >= d;
    }, a.validator.format("Please enter at least {0} words.")), a.validator.addMethod("rangeWords", function(a, c, d) {
        var e = b(a), f = /\b\w+\b/g;
        return this.optional(c) || e.match(f).length >= d[0] && e.match(f).length <= d[1];
    }, a.validator.format("Please enter between {0} and {1} words.")), a.validator.addMethod("abaRoutingNumber", function(a) {
        var b = 0, c = a.split(""), d = c.length;
        if (9 !== d) return !1;
        for (var e = 0; e < d; e += 3) b += 3 * parseInt(c[e], 10) + 7 * parseInt(c[e + 1], 10) + parseInt(c[e + 2], 10);
        return 0 !== b && b % 10 == 0;
    }, "Please enter a valid routing number."), a.validator.addMethod("accept", function(b, c, d) {
        var e, g, h = "string" == typeof d ? d.replace(/\s/g, "") : "image/*", i = this.optional(c);
        if (i) return i;
        if ("file" === a(c).attr("type") && (h = h.replace(/[\-\[\]\/\{\}\(\)\+\?\.\\\^\$\|]/g, "\\$&").replace(/,/g, "|").replace(/\/\*/g, "/.*"), 
        c.files && c.files.length)) for (g = new RegExp(".?(" + h + ")$", "i"), e = 0; e < c.files.length; e++) if (!c.files[e].type.match(g)) return !1;
        return !0;
    }, a.validator.format("Please enter a value with a valid mimetype.")), a.validator.addMethod("alphanumeric", function(a, b) {
        return this.optional(b) || /^\w+$/i.test(a);
    }, "Letters, numbers, and underscores only please"), a.validator.addMethod("bankaccountNL", function(a, b) {
        if (this.optional(b)) return !0;
        if (!/^[0-9]{9}|([0-9]{2} ){3}[0-9]{3}$/.test(a)) return !1;
        var c, f = a.replace(/ /g, ""), g = 0, h = f.length;
        for (c = 0; c < h; c++) g += (h - c) * f.substring(c, c + 1);
        return g % 11 == 0;
    }, "Please specify a valid bank account number"), a.validator.addMethod("bankorgiroaccountNL", function(b, c) {
        return this.optional(c) || a.validator.methods.bankaccountNL.call(this, b, c) || a.validator.methods.giroaccountNL.call(this, b, c);
    }, "Please specify a valid bank or giro account number"), a.validator.addMethod("bic", function(a, b) {
        return this.optional(b) || /^([A-Z]{6}[A-Z2-9][A-NP-Z1-9])(X{3}|[A-WY-Z0-9][A-Z0-9]{2})?$/.test(a.toUpperCase());
    }, "Please specify a valid BIC code"), a.validator.addMethod("cifES", function(a, b) {
        "use strict";
        function c(a) {
            return a % 2 == 0;
        }
        if (this.optional(b)) return !0;
        var d, e, f, g, h = new RegExp(/^([ABCDEFGHJKLMNPQRSUVW])(\d{7})([0-9A-J])$/gi), i = a.substring(0, 1), j = a.substring(1, 8), k = a.substring(8, 9), m = 0, n = 0;
        if (9 !== a.length || !h.test(a)) return !1;
        for (d = 0; d < j.length; d++) e = parseInt(j[d], 10), c(d) ? n += (e *= 2) < 10 ? e : e - 9 : m += e;
        return f = (10 - (m + n).toString().substr(-1)).toString(), f = 9 < parseInt(f, 10) ? "0" : f, 
        g = "JABCDEFGHI".substr(f, 1).toString(), i.match(/[ABEH]/) ? k === f : i.match(/[KPQS]/) ? k === g : k === f || k === g;
    }, "Please specify a valid CIF number."), a.validator.addMethod("cnhBR", function(a) {
        if (11 !== (a = a.replace(/([~!@#$%^&*()_+=`{}\[\]\-|\\:;'<>,.\/? ])+/g, "")).length) return !1;
        var b, c, d, e, f, h = 0, i = 0;
        if (b = a.charAt(0), new Array(12).join(b) === a) return !1;
        for (f = 9, e = 0; e < 9; ++e, --f) h += +a.charAt(e) * f;
        for (10 <= (c = h % 11) && (c = 0, i = 2), f = 1, e = h = 0; e < 9; ++e, ++f) h += +a.charAt(e) * f;
        return 10 <= (d = h % 11) ? d = 0 : d -= i, String(c).concat(d) === a.substr(-2);
    }, "Please specify a valid CNH number"), a.validator.addMethod("cnpjBR", function(a, b) {
        "use strict";
        if (this.optional(b)) return !0;
        if (14 !== (a = a.replace(/[^\d]+/g, "")).length) return !1;
        if ("00000000000000" === a || "11111111111111" === a || "22222222222222" === a || "33333333333333" === a || "44444444444444" === a || "55555555555555" === a || "66666666666666" === a || "77777777777777" === a || "88888888888888" === a || "99999999999999" === a) return !1;
        for (var c = a.length - 2, d = a.substring(0, c), e = a.substring(c), f = 0, g = c - 7, h = c; 1 <= h; h--) f += d.charAt(c - h) * g--, 
        g < 2 && (g = 9);
        var i = f % 11 < 2 ? 0 : 11 - f % 11;
        if (i !== parseInt(e.charAt(0), 10)) return !1;
        c += 1, d = a.substring(0, c), f = 0, g = c - 7;
        for (var j = c; 1 <= j; j--) f += d.charAt(c - j) * g--, g < 2 && (g = 9);
        return (i = f % 11 < 2 ? 0 : 11 - f % 11) === parseInt(e.charAt(1), 10);
    }, "Please specify a CNPJ value number"), a.validator.addMethod("cpfBR", function(a, b) {
        "use strict";
        if (this.optional(b)) return !0;
        if (11 !== (a = a.replace(/([~!@#$%^&*()_+=`{}\[\]\-|\\:;'<>,.\/? ])+/g, "")).length) return !1;
        var c, d, e, f, g = 0;
        if (c = parseInt(a.substring(9, 10), 10), d = parseInt(a.substring(10, 11), 10), 
        e = function(a, b) {
            var c = 10 * a % 11;
            return 10 !== c && 11 !== c || (c = 0), c === b;
        }, "" === a || "00000000000" === a || "11111111111" === a || "22222222222" === a || "33333333333" === a || "44444444444" === a || "55555555555" === a || "66666666666" === a || "77777777777" === a || "88888888888" === a || "99999999999" === a) return !1;
        for (f = 1; f <= 9; f++) g += parseInt(a.substring(f - 1, f), 10) * (11 - f);
        if (e(g, c)) {
            for (g = 0, f = 1; f <= 10; f++) g += parseInt(a.substring(f - 1, f), 10) * (12 - f);
            return e(g, d);
        }
        return !1;
    }, "Please specify a valid CPF number"), a.validator.addMethod("creditcard", function(a, b) {
        if (this.optional(b)) return "dependency-mismatch";
        if (/[^0-9 \-]+/.test(a)) return !1;
        var c, d, e = 0, f = 0, g = !1;
        if ((a = a.replace(/\D/g, "")).length < 13 || 19 < a.length) return !1;
        for (c = a.length - 1; 0 <= c; c--) d = a.charAt(c), f = parseInt(d, 10), g && 9 < (f *= 2) && (f -= 9), 
        e += f, g = !g;
        return e % 10 == 0;
    }, "Please enter a valid credit card number."), a.validator.addMethod("creditcardtypes", function(a, b, c) {
        if (/[^0-9\-]+/.test(a)) return !1;
        a = a.replace(/\D/g, "");
        var d = 0;
        return c.mastercard && (d |= 1), c.visa && (d |= 2), c.amex && (d |= 4), c.dinersclub && (d |= 8), 
        c.enroute && (d |= 16), c.discover && (d |= 32), c.jcb && (d |= 64), c.unknown && (d |= 128), 
        c.all && (d = 255), 1 & d && (/^(5[12345])/.test(a) || /^(2[234567])/.test(a)) ? 16 === a.length : 2 & d && /^(4)/.test(a) ? 16 === a.length : 4 & d && /^(3[47])/.test(a) ? 15 === a.length : 8 & d && /^(3(0[012345]|[68]))/.test(a) ? 14 === a.length : 16 & d && /^(2(014|149))/.test(a) ? 15 === a.length : 32 & d && /^(6011)/.test(a) ? 16 === a.length : 64 & d && /^(3)/.test(a) ? 16 === a.length : 64 & d && /^(2131|1800)/.test(a) ? 15 === a.length : !!(128 & d);
    }, "Please enter a valid credit card number."), a.validator.addMethod("currency", function(a, b, c) {
        var d, e = "string" == typeof c, f = e ? c : c[0], g = !!e || c[1];
        return f = f.replace(/,/g, ""), d = "^[" + (f = g ? f + "]" : f + "]?") + "([1-9]{1}[0-9]{0,2}(\\,[0-9]{3})*(\\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\\.[0-9]{0,2})?|0(\\.[0-9]{0,2})?|(\\.[0-9]{1,2})?)$", 
        d = new RegExp(d), this.optional(b) || d.test(a);
    }, "Please specify a valid currency"), a.validator.addMethod("dateFA", function(a, b) {
        return this.optional(b) || /^[1-4]\d{3}\/((0?[1-6]\/((3[0-1])|([1-2][0-9])|(0?[1-9])))|((1[0-2]|(0?[7-9]))\/(30|([1-2][0-9])|(0?[1-9]))))$/.test(a);
    }, a.validator.messages.date), a.validator.addMethod("dateITA", function(a, b) {
        var c, d, e, f, g, h = !1;
        return h = !!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(a) && (c = a.split("/"), d = parseInt(c[0], 10), 
        e = parseInt(c[1], 10), f = parseInt(c[2], 10), (g = new Date(Date.UTC(f, e - 1, d, 12, 0, 0, 0))).getUTCFullYear() === f && g.getUTCMonth() === e - 1 && g.getUTCDate() === d), 
        this.optional(b) || h;
    }, a.validator.messages.date), a.validator.addMethod("dateNL", function(a, b) {
        return this.optional(b) || /^(0?[1-9]|[12]\d|3[01])[\.\/\-](0?[1-9]|1[012])[\.\/\-]([12]\d)?(\d\d)$/.test(a);
    }, a.validator.messages.date), a.validator.addMethod("extension", function(a, b, c) {
        return c = "string" == typeof c ? c.replace(/,/g, "|") : "png|jpe?g|gif", this.optional(b) || a.match(new RegExp("\\.(" + c + ")$", "i"));
    }, a.validator.format("Please enter a value with a valid extension.")), a.validator.addMethod("giroaccountNL", function(a, b) {
        return this.optional(b) || /^[0-9]{1,7}$/.test(a);
    }, "Please specify a valid giro account number"), a.validator.addMethod("greaterThan", function(b, c, d) {
        var e = a(d);
        return this.settings.onfocusout && e.not(".validate-greaterThan-blur").length && e.addClass("validate-greaterThan-blur").on("blur.validate-greaterThan", function() {
            a(c).valid();
        }), b > e.val();
    }, "Please enter a greater value."), a.validator.addMethod("greaterThanEqual", function(b, c, d) {
        var e = a(d);
        return this.settings.onfocusout && e.not(".validate-greaterThanEqual-blur").length && e.addClass("validate-greaterThanEqual-blur").on("blur.validate-greaterThanEqual", function() {
            a(c).valid();
        }), b >= e.val();
    }, "Please enter a greater value."), a.validator.addMethod("iban", function(a, b) {
        if (this.optional(b)) return !0;
        var d, e, g, j, k, l = a.replace(/ /g, "").toUpperCase(), m = "", n = !0, o = "";
        if (l.length < 5) return !1;
        if (void 0 !== (g = {
            AL: "\\d{8}[\\dA-Z]{16}",
            AD: "\\d{8}[\\dA-Z]{12}",
            AT: "\\d{16}",
            AZ: "[\\dA-Z]{4}\\d{20}",
            BE: "\\d{12}",
            BH: "[A-Z]{4}[\\dA-Z]{14}",
            BA: "\\d{16}",
            BR: "\\d{23}[A-Z][\\dA-Z]",
            BG: "[A-Z]{4}\\d{6}[\\dA-Z]{8}",
            CR: "\\d{17}",
            HR: "\\d{17}",
            CY: "\\d{8}[\\dA-Z]{16}",
            CZ: "\\d{20}",
            DK: "\\d{14}",
            DO: "[A-Z]{4}\\d{20}",
            EE: "\\d{16}",
            FO: "\\d{14}",
            FI: "\\d{14}",
            FR: "\\d{10}[\\dA-Z]{11}\\d{2}",
            GE: "[\\dA-Z]{2}\\d{16}",
            DE: "\\d{18}",
            GI: "[A-Z]{4}[\\dA-Z]{15}",
            GR: "\\d{7}[\\dA-Z]{16}",
            GL: "\\d{14}",
            GT: "[\\dA-Z]{4}[\\dA-Z]{20}",
            HU: "\\d{24}",
            IS: "\\d{22}",
            IE: "[\\dA-Z]{4}\\d{14}",
            IL: "\\d{19}",
            IT: "[A-Z]\\d{10}[\\dA-Z]{12}",
            KZ: "\\d{3}[\\dA-Z]{13}",
            KW: "[A-Z]{4}[\\dA-Z]{22}",
            LV: "[A-Z]{4}[\\dA-Z]{13}",
            LB: "\\d{4}[\\dA-Z]{20}",
            LI: "\\d{5}[\\dA-Z]{12}",
            LT: "\\d{16}",
            LU: "\\d{3}[\\dA-Z]{13}",
            MK: "\\d{3}[\\dA-Z]{10}\\d{2}",
            MT: "[A-Z]{4}\\d{5}[\\dA-Z]{18}",
            MR: "\\d{23}",
            MU: "[A-Z]{4}\\d{19}[A-Z]{3}",
            MC: "\\d{10}[\\dA-Z]{11}\\d{2}",
            MD: "[\\dA-Z]{2}\\d{18}",
            ME: "\\d{18}",
            NL: "[A-Z]{4}\\d{10}",
            NO: "\\d{11}",
            PK: "[\\dA-Z]{4}\\d{16}",
            PS: "[\\dA-Z]{4}\\d{21}",
            PL: "\\d{24}",
            PT: "\\d{21}",
            RO: "[A-Z]{4}[\\dA-Z]{16}",
            SM: "[A-Z]\\d{10}[\\dA-Z]{12}",
            SA: "\\d{2}[\\dA-Z]{18}",
            RS: "\\d{18}",
            SK: "\\d{20}",
            SI: "\\d{15}",
            ES: "\\d{20}",
            SE: "\\d{20}",
            CH: "\\d{5}[\\dA-Z]{12}",
            TN: "\\d{20}",
            TR: "\\d{5}[\\dA-Z]{17}",
            AE: "\\d{3}\\d{16}",
            GB: "[A-Z]{4}\\d{14}",
            VG: "[\\dA-Z]{4}\\d{16}"
        }[l.substring(0, 2)]) && !new RegExp("^[A-Z]{2}\\d{2}" + g + "$", "").test(l)) return !1;
        for (d = l.substring(4, l.length) + l.substring(0, 4), j = 0; j < d.length; j++) "0" !== (e = d.charAt(j)) && (n = !1), 
        n || (m += "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ".indexOf(e));
        for (k = 0; k < m.length; k++) o = ("" + o + m.charAt(k)) % 97;
        return 1 === o;
    }, "Please specify a valid IBAN"), a.validator.addMethod("integer", function(a, b) {
        return this.optional(b) || /^-?\d+$/.test(a);
    }, "A positive or negative non-decimal number please"), a.validator.addMethod("ipv4", function(a, b) {
        return this.optional(b) || /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(a);
    }, "Please enter a valid IP v4 address."), a.validator.addMethod("ipv6", function(a, b) {
        return this.optional(b) || /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(a);
    }, "Please enter a valid IP v6 address."), a.validator.addMethod("lessThan", function(b, c, d) {
        var e = a(d);
        return this.settings.onfocusout && e.not(".validate-lessThan-blur").length && e.addClass("validate-lessThan-blur").on("blur.validate-lessThan", function() {
            a(c).valid();
        }), b < e.val();
    }, "Please enter a lesser value."), a.validator.addMethod("lessThanEqual", function(b, c, d) {
        var e = a(d);
        return this.settings.onfocusout && e.not(".validate-lessThanEqual-blur").length && e.addClass("validate-lessThanEqual-blur").on("blur.validate-lessThanEqual", function() {
            a(c).valid();
        }), b <= e.val();
    }, "Please enter a lesser value."), a.validator.addMethod("lettersonly", function(a, b) {
        return this.optional(b) || /^[a-z]+$/i.test(a);
    }, "Letters only please"), a.validator.addMethod("letterswithbasicpunc", function(a, b) {
        return this.optional(b) || /^[a-z\-.,()'"\s]+$/i.test(a);
    }, "Letters or punctuation only please"), a.validator.addMethod("maxfiles", function(b, c, d) {
        return !!this.optional(c) || !("file" === a(c).attr("type") && c.files && c.files.length > d);
    }, a.validator.format("Please select no more than {0} files.")), a.validator.addMethod("maxsize", function(b, c, d) {
        if (this.optional(c)) return !0;
        if ("file" === a(c).attr("type") && c.files && c.files.length) for (var e = 0; e < c.files.length; e++) if (c.files[e].size > d) return !1;
        return !0;
    }, a.validator.format("File size must not exceed {0} bytes each.")), a.validator.addMethod("maxsizetotal", function(b, c, d) {
        if (this.optional(c)) return !0;
        if ("file" === a(c).attr("type") && c.files && c.files.length) for (var e = 0, f = 0; f < c.files.length; f++) if (d < (e += c.files[f].size)) return !1;
        return !0;
    }, a.validator.format("Total size of all files must not exceed {0} bytes.")), a.validator.addMethod("mobileNL", function(a, b) {
        return this.optional(b) || /^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)6((\s|\s?\-\s?)?[0-9]){8}$/.test(a);
    }, "Please specify a valid mobile number"), a.validator.addMethod("mobileRU", function(a, b) {
        var c = a.replace(/\(|\)|\s+|-/g, "");
        return this.optional(b) || 9 < c.length && /^((\+7|7|8)+([0-9]){10})$/.test(c);
    }, "Please specify a valid mobile number"), a.validator.addMethod("mobileUK", function(a, b) {
        return a = a.replace(/\(|\)|\s+|-/g, ""), this.optional(b) || 9 < a.length && a.match(/^(?:(?:(?:00\s?|\+)44\s?|0)7(?:[1345789]\d{2}|624)\s?\d{3}\s?\d{3})$/);
    }, "Please specify a valid mobile number"), a.validator.addMethod("netmask", function(a, b) {
        return this.optional(b) || /^(254|252|248|240|224|192|128)\.0\.0\.0|255\.(254|252|248|240|224|192|128|0)\.0\.0|255\.255\.(254|252|248|240|224|192|128|0)\.0|255\.255\.255\.(254|252|248|240|224|192|128|0)/i.test(a);
    }, "Please enter a valid netmask."), a.validator.addMethod("nieES", function(a, b) {
        "use strict";
        if (this.optional(b)) return !0;
        var c, d = new RegExp(/^[MXYZ]{1}[0-9]{7,8}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/gi), f = a.substr(a.length - 1).toUpperCase();
        return !(10 < (a = a.toString().toUpperCase()).length || a.length < 9 || !d.test(a)) && (c = 9 === (a = a.replace(/^[X]/, "0").replace(/^[Y]/, "1").replace(/^[Z]/, "2")).length ? a.substr(0, 8) : a.substr(0, 9), 
        "TRWAGMYFPDXBNJZSQVHLCKET".charAt(parseInt(c, 10) % 23) === f);
    }, "Please specify a valid NIE number."), a.validator.addMethod("nifES", function(a, b) {
        "use strict";
        return !!this.optional(b) || !!(a = a.toUpperCase()).match("((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)") && (/^[0-9]{8}[A-Z]{1}$/.test(a) ? "TRWAGMYFPDXBNJZSQVHLCKE".charAt(a.substring(8, 0) % 23) === a.charAt(8) : !!/^[KLM]{1}/.test(a) && a[8] === "TRWAGMYFPDXBNJZSQVHLCKE".charAt(a.substring(8, 1) % 23));
    }, "Please specify a valid NIF number."), a.validator.addMethod("nipPL", function(a) {
        "use strict";
        if (10 !== (a = a.replace(/[^0-9]/g, "")).length) return !1;
        for (var b = [ 6, 5, 7, 2, 3, 4, 5, 6, 7 ], c = 0, d = 0; d < 9; d++) c += b[d] * a[d];
        var e = c % 11;
        return (10 == e ? 0 : e) === parseInt(a[9], 10);
    }, "Please specify a valid NIP number."), a.validator.addMethod("nisBR", function(a) {
        var b, c, d, e, f, g = 0;
        if (11 !== (a = a.replace(/([~!@#$%^&*()_+=`{}\[\]\-|\\:;'<>,.\/? ])+/g, "")).length) return !1;
        for (c = parseInt(a.substring(10, 11), 10), b = parseInt(a.substring(0, 10), 10), 
        e = 2; e < 12; e++) 10 === (f = e) && (f = 2), 11 === e && (f = 3), g += b % 10 * f, 
        b = parseInt(b / 10, 10);
        return c === (d = 1 < (d = g % 11) ? 11 - d : 0);
    }, "Please specify a valid NIS/PIS number"), a.validator.addMethod("notEqualTo", function(b, c, d) {
        return this.optional(c) || !a.validator.methods.equalTo.call(this, b, c, d);
    }, "Please enter a different value, values must not be the same."), a.validator.addMethod("nowhitespace", function(a, b) {
        return this.optional(b) || /^\S+$/i.test(a);
    }, "No white space please"), a.validator.addMethod("pattern", function(a, b, c) {
        return !!this.optional(b) || ("string" == typeof c && (c = new RegExp("^(?:" + c + ")$")), 
        c.test(a));
    }, "Invalid format."), a.validator.addMethod("phoneNL", function(a, b) {
        return this.optional(b) || /^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)[1-9]((\s|\s?\-\s?)?[0-9]){8}$/.test(a);
    }, "Please specify a valid phone number."), a.validator.addMethod("phonePL", function(a, b) {
        a = a.replace(/\s+/g, "");
        return this.optional(b) || /^(?:(?:(?:\+|00)?48)|(?:\(\+?48\)))?(?:1[2-8]|2[2-69]|3[2-49]|4[1-68]|5[0-9]|6[0-35-9]|[7-8][1-9]|9[145])\d{7}$/.test(a);
    }, "Please specify a valid phone number"), a.validator.addMethod("phonesUK", function(a, b) {
        return a = a.replace(/\(|\)|\s+|-/g, ""), this.optional(b) || 9 < a.length && a.match(/^(?:(?:(?:00\s?|\+)44\s?|0)(?:1\d{8,9}|[23]\d{9}|7(?:[1345789]\d{8}|624\d{6})))$/);
    }, "Please specify a valid uk phone number"), a.validator.addMethod("phoneUK", function(a, b) {
        return a = a.replace(/\(|\)|\s+|-/g, ""), this.optional(b) || 9 < a.length && a.match(/^(?:(?:(?:00\s?|\+)44\s?)|(?:\(?0))(?:\d{2}\)?\s?\d{4}\s?\d{4}|\d{3}\)?\s?\d{3}\s?\d{3,4}|\d{4}\)?\s?(?:\d{5}|\d{3}\s?\d{3})|\d{5}\)?\s?\d{4,5})$/);
    }, "Please specify a valid phone number"), a.validator.addMethod("phoneUS", function(a, b) {
        return a = a.replace(/\s+/g, ""), this.optional(b) || 9 < a.length && a.match(/^(\+?1-?)?(\([2-9]([02-9]\d|1[02-9])\)|[2-9]([02-9]\d|1[02-9]))-?[2-9]\d{2}-?\d{4}$/);
    }, "Please specify a valid phone number"), a.validator.addMethod("postalcodeBR", function(a, b) {
        return this.optional(b) || /^\d{2}.\d{3}-\d{3}?$|^\d{5}-?\d{3}?$/.test(a);
    }, "Informe um CEP válido."), a.validator.addMethod("postalCodeCA", function(a, b) {
        return this.optional(b) || /^[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ] *\d[ABCEGHJKLMNPRSTVWXYZ]\d$/i.test(a);
    }, "Please specify a valid postal code"), a.validator.addMethod("postalcodeIT", function(a, b) {
        return this.optional(b) || /^\d{5}$/.test(a);
    }, "Please specify a valid postal code"), a.validator.addMethod("postalcodeNL", function(a, b) {
        return this.optional(b) || /^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/.test(a);
    }, "Please specify a valid postal code"), a.validator.addMethod("postcodeUK", function(a, b) {
        return this.optional(b) || /^((([A-PR-UWYZ][0-9])|([A-PR-UWYZ][0-9][0-9])|([A-PR-UWYZ][A-HK-Y][0-9])|([A-PR-UWYZ][A-HK-Y][0-9][0-9])|([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY]))\s?([0-9][ABD-HJLNP-UW-Z]{2})|(GIR)\s?(0AA))$/i.test(a);
    }, "Please specify a valid UK postcode"), a.validator.addMethod("require_from_group", function(b, c, d) {
        var e = a(d[1], c.form), f = e.eq(0), g = f.data("valid_req_grp") ? f.data("valid_req_grp") : a.extend({}, this), h = e.filter(function() {
            return g.elementValue(this);
        }).length >= d[0];
        return f.data("valid_req_grp", g), a(c).data("being_validated") || (e.data("being_validated", !0), 
        e.each(function() {
            g.element(this);
        }), e.data("being_validated", !1)), h;
    }, a.validator.format("Please fill at least {0} of these fields.")), a.validator.addMethod("skip_or_fill_minimum", function(b, c, d) {
        var e = a(d[1], c.form), f = e.eq(0), g = f.data("valid_skip") ? f.data("valid_skip") : a.extend({}, this), h = e.filter(function() {
            return g.elementValue(this);
        }).length, i = 0 === h || h >= d[0];
        return f.data("valid_skip", g), a(c).data("being_validated") || (e.data("being_validated", !0), 
        e.each(function() {
            g.element(this);
        }), e.data("being_validated", !1)), i;
    }, a.validator.format("Please either skip these fields or fill at least {0} of them.")), 
    a.validator.addMethod("stateUS", function(a, b, c) {
        var d, e = void 0 === c, f = !e && void 0 !== c.caseSensitive && c.caseSensitive, g = !e && void 0 !== c.includeTerritories && c.includeTerritories, h = !e && void 0 !== c.includeMilitary && c.includeMilitary;
        return d = g || h ? g && h ? "^(A[AEKLPRSZ]|C[AOT]|D[CE]|FL|G[AU]|HI|I[ADLN]|K[SY]|LA|M[ADEINOPST]|N[CDEHJMVY]|O[HKR]|P[AR]|RI|S[CD]|T[NX]|UT|V[AIT]|W[AIVY])$" : g ? "^(A[KLRSZ]|C[AOT]|D[CE]|FL|G[AU]|HI|I[ADLN]|K[SY]|LA|M[ADEINOPST]|N[CDEHJMVY]|O[HKR]|P[AR]|RI|S[CD]|T[NX]|UT|V[AIT]|W[AIVY])$" : "^(A[AEKLPRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])$" : "^(A[KLRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])$", 
        d = f ? new RegExp(d) : new RegExp(d, "i"), this.optional(b) || d.test(a);
    }, "Please specify a valid state"), a.validator.addMethod("strippedminlength", function(b, c, d) {
        return a(b).text().length >= d;
    }, a.validator.format("Please enter at least {0} characters")), a.validator.addMethod("time", function(a, b) {
        return this.optional(b) || /^([01]\d|2[0-3]|[0-9])(:[0-5]\d){1,2}$/.test(a);
    }, "Please enter a valid time, between 00:00 and 23:59"), a.validator.addMethod("time12h", function(a, b) {
        return this.optional(b) || /^((0?[1-9]|1[012])(:[0-5]\d){1,2}(\ ?[AP]M))$/i.test(a);
    }, "Please enter a valid time in 12-hour am/pm format"), a.validator.addMethod("url2", function(a, b) {
        return this.optional(b) || /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(a);
    }, a.validator.messages.url), a.validator.addMethod("vinUS", function(a) {
        if (17 !== a.length) return !1;
        var b, c, d, e, f, g, h = [ "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" ], i = [ 1, 2, 3, 4, 5, 6, 7, 8, 1, 2, 3, 4, 5, 7, 9, 2, 3, 4, 5, 6, 7, 8, 9 ], j = [ 8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2 ], k = 0;
        for (b = 0; b < 17; b++) {
            if (e = j[b], d = a.slice(b, b + 1), 8 === b && (g = d), isNaN(d)) {
                for (c = 0; c < h.length; c++) if (d.toUpperCase() === h[c]) {
                    d = i[c], d *= e, isNaN(g) && 8 === c && (g = h[c]);
                    break;
                }
            } else d *= e;
            k += d;
        }
        return 10 === (f = k % 11) && (f = "X"), f === g;
    }, "The specified vehicle identification number (VIN) is invalid."), a.validator.addMethod("zipcodeUS", function(a, b) {
        return this.optional(b) || /^\d{5}(-\d{4})?$/.test(a);
    }, "The specified US ZIP Code is invalid"), a.validator.addMethod("ziprange", function(a, b) {
        return this.optional(b) || /^90[2-5]\d\{2\}-\d{4}$/.test(a);
    }, "Your ZIP-code must be in the range 902xx-xxxx to 905xx-xxxx"), a;
    function b(a) {
        return a.replace(/<.[^<>]*?>/g, " ").replace(/&nbsp;|&#160;/gi, " ").replace(/[.(),;:!?%#$'\"_+=\/\-“”’]*/g, "");
    }
});