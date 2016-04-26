/*
 Highcharts JS v4.1.9 (2015-10-07)

 Standalone Highcharts Framework

 License: MIT License
 */
var HighchartsAdapter = function () {
    function o(c) {
        function a(a, b, d) {
            a.removeEventListener(b, d, !1)
        }

        function d(a, b, d) {
            d = a.HCProxiedMethods[d.toString()];
            a.detachEvent("on" + b, d)
        }

        function b(b, c) {
            var f = b.HCEvents, k, h, l, g;
            if (b.removeEventListener)k = a; else if (b.attachEvent)k = d; else return;
            c ? (h = {}, h[c] = !0) : h = f;
            for (g in h)if (f[g])for (l = f[g].length; l--;)k(b, g, f[g][l])
        }

        c.HCExtended || Highcharts.extend(c, {
            HCExtended: !0, HCEvents: {}, bind: function (a, b) {
                var d = this, c = this.HCEvents, h;
                if (d.addEventListener)d.addEventListener(a,
                    b, !1); else if (d.attachEvent) {
                    h = function (a) {
                        a.target = a.srcElement || window;
                        b.call(d, a)
                    };
                    if (!d.HCProxiedMethods)d.HCProxiedMethods = {};
                    d.HCProxiedMethods[b.toString()] = h;
                    d.attachEvent("on" + a, h)
                }
                c[a] === q && (c[a] = []);
                c[a].push(b)
            }, unbind: function (c, i) {
                var f, k;
                c ? (f = this.HCEvents[c] || [], i ? (k = HighchartsAdapter.inArray(i, f), k > -1 && (f.splice(k, 1), this.HCEvents[c] = f), this.removeEventListener ? a(this, c, i) : this.attachEvent && d(this, c, i)) : (b(this, c), this.HCEvents[c] = [])) : (b(this), this.HCEvents = {})
            }, trigger: function (a,
                                  b) {
                var d = this.HCEvents[a] || [], c = d.length, h, g, j;
                g = function () {
                    b.defaultPrevented = !0
                };
                for (h = 0; h < c; h++) {
                    j = d[h];
                    if (b.stopped)break;
                    b.preventDefault = g;
                    b.target = this;
                    if (!b.type)b.type = a;
                    j.call(this, b) === !1 && b.preventDefault()
                }
            }
        });
        return c
    }

    var q, j = document, p = [], g = [], m = {}, n;
    Math.easeInOutSine = function (c, a, d, b) {
        return -d / 2 * (Math.cos(Math.PI * c / b) - 1) + a
    };
    return {
        init: function (c) {
            if (!j.defaultView)this._getStyle = function (a, d) {
                var b;
                return a.style[d] ? a.style[d] : (d === "opacity" && (d = "filter"), b = a.currentStyle[d.replace(/\-(\w)/g,
                    function (a, b) {
                        return b.toUpperCase()
                    })], d === "filter" && (b = b.replace(/alpha\(opacity=([0-9]+)\)/, function (a, b) {
                    return b / 100
                })), b === "" ? 1 : b)
            }, this.adapterRun = function (a, d) {
                var b = {width: "clientWidth", height: "clientHeight"}[d];
                if (b)return a.style.zoom = 1, a[b] - 2 * parseInt(HighchartsAdapter._getStyle(a, "padding"), 10)
            };
            if (!Array.prototype.forEach)this.each = function (a, d) {
                for (var b = 0, c = a.length; b < c; b++)if (d.call(a[b], a[b], b, a) === !1)return b
            };
            if (!Array.prototype.indexOf)this.inArray = function (a, d) {
                var b, c = 0;
                if (d)for (b =
                               d.length; c < b; c++)if (d[c] === a)return c;
                return -1
            };
            if (!Array.prototype.filter)this.grep = function (a, d) {
                for (var b = [], c = 0, i = a.length; c < i; c++)d(a[c], c) && b.push(a[c]);
                return b
            };
            n = function (a, c, b) {
                this.options = c;
                this.elem = a;
                this.prop = b
            };
            n.prototype = {
                update: function () {
                    var a;
                    a = this.paths;
                    var d = this.elem, b = d.element;
                    if (m[this.prop])m[this.prop](this); else a && b ? d.attr("d", c.step(a[0], a[1], this.now, this.toD)) : d.attr ? b && d.attr(this.prop, this.now) : (a = {}, a[this.prop] = this.now + this.unit, Highcharts.css(d, a));
                    this.options.step &&
                    this.options.step.call(this.elem, this.now, this)
                }, custom: function (a, c, b) {
                    var e = this, i = function (a) {
                        return e.step(a)
                    }, f;
                    this.startTime = +new Date;
                    this.start = a;
                    this.end = c;
                    this.unit = b;
                    this.now = this.start;
                    this.pos = this.state = 0;
                    i.elem = this.elem;
                    if (i() && g.push(i) === 1)i.timerId = setInterval(function () {
                        for (f = 0; f < g.length; f++)g[f]() || g.splice(f--, 1);
                        g.length || clearInterval(i.timerId)
                    }, 13)
                }, step: function (a) {
                    var c = +new Date, b;
                    b = this.options;
                    var e = this.elem, i;
                    if (e.attr && !e.element)b = !1; else if (a || c >= b.duration + this.startTime) {
                        this.now =
                            this.end;
                        this.pos = this.state = 1;
                        this.update();
                        a = this.options.curAnim[this.prop] = !0;
                        for (i in b.curAnim)b.curAnim[i] !== !0 && (a = !1);
                        a && b.complete && b.complete.call(e);
                        b = !1
                    } else e = c - this.startTime, this.state = e / b.duration, this.pos = b.easing(e, 0, 1, b.duration), this.now = this.start + (this.end - this.start) * this.pos, this.update(), b = !0;
                    return b
                }
            };
            this.animate = function (a, d, b) {
                var e, i = "", f, g, h;
                if (typeof b !== "object" || b === null)e = arguments, b = {
                    duration: e[2],
                    easing: e[3],
                    complete: e[4]
                };
                if (typeof b.duration !== "number")b.duration =
                    400;
                b.easing = Math[b.easing] || Math.easeInOutSine;
                b.curAnim = Highcharts.extend({}, d);
                for (h in d)g = new n(a, b, h), f = null, h === "d" ? (g.paths = c.init(a, a.d, d.d), g.toD = d.d, e = 0, f = 1) : a.attr ? e = a.attr(h) : (e = parseFloat(HighchartsAdapter._getStyle(a, h)) || 0, h !== "opacity" && (i = "px")), f || (f = d[h]), f.match && f.match("px") && (f = f.replace(/px/g, "")), g.custom(e, f, i)
            }
        }, _getStyle: function (c, a) {
            return window.getComputedStyle(c, void 0).getPropertyValue(a)
        }, addAnimSetter: function (c, a) {
            m[c] = a
        }, getScript: function (c, a) {
            var d = j.getElementsByTagName("head")[0],
                b = j.createElement("script");
            b.type = "text/javascript";
            b.src = c;
            b.onload = a;
            d.appendChild(b)
        }, inArray: function (c, a) {
            return a.indexOf ? a.indexOf(c) : p.indexOf.call(a, c)
        }, adapterRun: function (c, a) {
            return parseInt(HighchartsAdapter._getStyle(c, a), 10)
        }, grep: function (c, a) {
            return p.filter.call(c, a)
        }, map: function (c, a) {
            for (var d = [], b = 0, e = c.length; b < e; b++)d[b] = a.call(c[b], c[b], b, c);
            return d
        }, offset: function (c) {
            var a = document.documentElement, c = c.getBoundingClientRect();
            return {
                top: c.top + (window.pageYOffset || a.scrollTop) -
                (a.clientTop || 0), left: c.left + (window.pageXOffset || a.scrollLeft) - (a.clientLeft || 0)
            }
        }, addEvent: function (c, a, d) {
            o(c).bind(a, d)
        }, removeEvent: function (c, a, d) {
            o(c).unbind(a, d)
        }, fireEvent: function (c, a, d, b) {
            var e;
            j.createEvent && (c.dispatchEvent || c.fireEvent) ? (e = j.createEvent("Events"), e.initEvent(a, !0, !0), e.target = c, Highcharts.extend(e, d), c.dispatchEvent ? c.dispatchEvent(e) : c.fireEvent(a, e)) : c.HCExtended === !0 && (d = d || {}, c.trigger(a, d));
            d && d.defaultPrevented && (b = null);
            b && b(d)
        }, washMouseEvent: function (c) {
            return c
        },
        stop: function (c) {
            for (var a = g.length, d; a--;)d = g[a], d.elem === c && g.splice(a, 1)
        }, each: function (c, a) {
            return Array.prototype.forEach.call(c, a)
        }
    }
}();