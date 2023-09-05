/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/asset.js":
/*!*******************************!*\
  !*** ./resources/js/asset.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

var featureOsWidget = __webpack_require__(/*! ./widget */ "./resources/js/widget.js");
Nova.request().get('/nova-vendor/featureos/login').then(function (response) {
  var _response$data$jwt;
  var widget = new featureOsWidget({
    "modules": ["feature_requests"],
    "type": "modal",
    "openFrom": "right",
    "theme": "light",
    "accent": "#2563eb",
    "selector": "no",
    "jwtToken": (_response$data$jwt = response.data.jwt) !== null && _response$data$jwt !== void 0 ? _response$data$jwt : null,
    "token": "hOeHRslcz67LehEwbFdJyQ",
    "submissionBucketIds": [14131, 14130],
    "showOnlySubmission": true
  });
  widget.init();
});

/***/ }),

/***/ "./resources/js/widget.js":
/*!********************************!*\
  !*** ./resources/js/widget.js ***!
  \********************************/
/***/ (function(module, exports, __webpack_require__) {

/* module decorator */ module = __webpack_require__.nmd(module);
var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;var _excluded = ["crossAxis", "alignment", "allowedPlacements", "autoAlignment"],
  _excluded2 = ["mainAxis", "crossAxis", "fallbackPlacements", "fallbackStrategy", "fallbackAxisSideDirection", "flipAlignment"],
  _excluded3 = ["mainAxis", "crossAxis", "limiter"];
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }
function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw new Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw new Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, "catch": function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
(function (T, E) {
  ( false ? 0 : _typeof(exports)) == "object" && ( false ? 0 : _typeof(module)) < "u" ? module.exports = E() :  true ? !(__WEBPACK_AMD_DEFINE_FACTORY__ = (E),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
		__WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : (0);
})(this, function () {
  "use strict";

  var rt = Object.defineProperty;
  var ot = function ot(T, E, L) {
    return E in T ? rt(T, E, {
      enumerable: !0,
      configurable: !0,
      writable: !0,
      value: L
    }) : T[E] = L;
  };
  var W = function W(T, E, L) {
    return ot(T, _typeof(E) != "symbol" ? E + "" : E, L), L;
  };
  function T(e) {
    return e.split("-")[1];
  }
  function E(e) {
    return e === "y" ? "height" : "width";
  }
  function L(e) {
    return e.split("-")[0];
  }
  function Z(e) {
    return ["top", "bottom"].includes(L(e)) ? "x" : "y";
  }
  function ge(e, t, r) {
    var i = e.reference,
      l = e.floating;
    var o = i.x + i.width / 2 - l.width / 2,
      a = i.y + i.height / 2 - l.height / 2,
      n = Z(t),
      f = E(n),
      c = i[f] / 2 - l[f] / 2,
      d = n === "x";
    var s;
    switch (L(t)) {
      case "top":
        s = {
          x: o,
          y: i.y - l.height
        };
        break;
      case "bottom":
        s = {
          x: o,
          y: i.y + i.height
        };
        break;
      case "right":
        s = {
          x: i.x + i.width,
          y: a
        };
        break;
      case "left":
        s = {
          x: i.x - l.width,
          y: a
        };
        break;
      default:
        s = {
          x: i.x,
          y: i.y
        };
    }
    switch (T(t)) {
      case "start":
        s[n] -= c * (r && d ? -1 : 1);
        break;
      case "end":
        s[n] += c * (r && d ? -1 : 1);
    }
    return s;
  }
  var Me = /*#__PURE__*/function () {
    var _ref = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(e, t, r) {
      var _r$placement, i, _r$strategy, l, _r$middleware, o, a, n, f, c, _ge, d, s, p, g, w, h, _ge2, _n$h, m, y, _yield$y, b, v, C, F;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            _r$placement = r.placement;
            i = _r$placement === void 0 ? "bottom" : _r$placement;
            _r$strategy = r.strategy;
            l = _r$strategy === void 0 ? "absolute" : _r$strategy;
            _r$middleware = r.middleware;
            o = _r$middleware === void 0 ? [] : _r$middleware;
            a = r.platform;
            n = o.filter(Boolean);
            _context.next = 10;
            return a.isRTL == null ? void 0 : a.isRTL(t);
          case 10:
            f = _context.sent;
            _context.next = 13;
            return a.getElementRects({
              reference: e,
              floating: t,
              strategy: l
            });
          case 13:
            c = _context.sent;
            _ge = ge(c, i, f);
            d = _ge.x;
            s = _ge.y;
            p = i;
            g = {};
            w = 0;
            h = 0;
          case 21:
            if (!(h < n.length)) {
              _context.next = 56;
              break;
            }
            _n$h = n[h];
            m = _n$h.name;
            y = _n$h.fn;
            _context.next = 27;
            return y({
              x: d,
              y: s,
              initialPlacement: i,
              placement: p,
              strategy: l,
              middlewareData: g,
              rects: c,
              platform: a,
              elements: {
                reference: e,
                floating: t
              }
            });
          case 27:
            _yield$y = _context.sent;
            b = _yield$y.x;
            v = _yield$y.y;
            C = _yield$y.data;
            F = _yield$y.reset;
            d = b !== null && b !== void 0 ? b : d;
            s = v !== null && v !== void 0 ? v : s;
            g = _objectSpread(_objectSpread({}, g), {}, _defineProperty({}, m, _objectSpread(_objectSpread({}, g[m]), C)));
            _context.t0 = F && w <= 50;
            if (!_context.t0) {
              _context.next = 53;
              break;
            }
            w++;
            _context.t1 = _typeof(F) == "object";
            if (!_context.t1) {
              _context.next = 52;
              break;
            }
            F.placement && (p = F.placement);
            _context.t2 = F.rects;
            if (!_context.t2) {
              _context.next = 51;
              break;
            }
            if (!(F.rects === !0)) {
              _context.next = 49;
              break;
            }
            _context.next = 46;
            return a.getElementRects({
              reference: e,
              floating: t,
              strategy: l
            });
          case 46:
            _context.t3 = _context.sent;
            _context.next = 50;
            break;
          case 49:
            _context.t3 = F.rects;
          case 50:
            c = _context.t3;
          case 51:
            _ge2 = ge(c, p, f), d = _ge2.x, s = _ge2.y;
          case 52:
            h = -1;
          case 53:
            h++;
            _context.next = 21;
            break;
          case 56:
            return _context.abrupt("return", {
              x: d,
              y: s,
              placement: p,
              strategy: l,
              middlewareData: g
            });
          case 57:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function Me(_x, _x2, _x3) {
      return _ref.apply(this, arguments);
    };
  }();
  function Oe(e) {
    return typeof e != "number" ? function (t) {
      return _objectSpread({
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
      }, t);
    }(e) : {
      top: e,
      right: e,
      bottom: e,
      left: e
    };
  }
  function ee(e) {
    return _objectSpread(_objectSpread({}, e), {}, {
      top: e.y,
      left: e.x,
      right: e.x + e.width,
      bottom: e.y + e.height
    });
  }
  function le(_x4, _x5) {
    return _le.apply(this, arguments);
  }
  function _le() {
    _le = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee8(e, t) {
      var r, i, l, o, a, n, f, _t, _t$boundary, c, _t$rootBoundary, d, _t$elementContext, s, _t$altBoundary, p, _t$padding, g, w, h, m, y, b, v, C;
      return _regeneratorRuntime().wrap(function _callee8$(_context8) {
        while (1) switch (_context8.prev = _context8.next) {
          case 0:
            t === void 0 && (t = {});
            i = e.x;
            l = e.y;
            o = e.platform;
            a = e.rects;
            n = e.elements;
            f = e.strategy;
            _t = t;
            _t$boundary = _t.boundary;
            c = _t$boundary === void 0 ? "clippingAncestors" : _t$boundary;
            _t$rootBoundary = _t.rootBoundary;
            d = _t$rootBoundary === void 0 ? "viewport" : _t$rootBoundary;
            _t$elementContext = _t.elementContext;
            s = _t$elementContext === void 0 ? "floating" : _t$elementContext;
            _t$altBoundary = _t.altBoundary;
            p = _t$altBoundary === void 0 ? !1 : _t$altBoundary;
            _t$padding = _t.padding;
            g = _t$padding === void 0 ? 0 : _t$padding;
            w = Oe(g);
            h = n[p ? s === "floating" ? "reference" : "floating" : s];
            _context8.t0 = ee;
            _context8.t1 = o;
            _context8.next = 24;
            return o.isElement == null ? void 0 : o.isElement(h);
          case 24:
            _context8.t3 = r = _context8.sent;
            _context8.t2 = _context8.t3 == null;
            if (_context8.t2) {
              _context8.next = 28;
              break;
            }
            _context8.t2 = r;
          case 28:
            if (!_context8.t2) {
              _context8.next = 32;
              break;
            }
            _context8.t4 = h;
            _context8.next = 38;
            break;
          case 32:
            _context8.t5 = h.contextElement;
            if (_context8.t5) {
              _context8.next = 37;
              break;
            }
            _context8.next = 36;
            return o.getDocumentElement == null ? void 0 : o.getDocumentElement(n.floating);
          case 36:
            _context8.t5 = _context8.sent;
          case 37:
            _context8.t4 = _context8.t5;
          case 38:
            _context8.t6 = _context8.t4;
            _context8.t7 = c;
            _context8.t8 = d;
            _context8.t9 = f;
            _context8.t10 = {
              element: _context8.t6,
              boundary: _context8.t7,
              rootBoundary: _context8.t8,
              strategy: _context8.t9
            };
            _context8.next = 45;
            return _context8.t1.getClippingRect.call(_context8.t1, _context8.t10);
          case 45:
            _context8.t11 = _context8.sent;
            m = (0, _context8.t0)(_context8.t11);
            y = s === "floating" ? _objectSpread(_objectSpread({}, a.floating), {}, {
              x: i,
              y: l
            }) : a.reference;
            _context8.next = 50;
            return o.getOffsetParent == null ? void 0 : o.getOffsetParent(n.floating);
          case 50:
            b = _context8.sent;
            _context8.next = 53;
            return o.isElement == null ? void 0 : o.isElement(b);
          case 53:
            _context8.t13 = _context8.sent;
            if (!_context8.t13) {
              _context8.next = 58;
              break;
            }
            _context8.next = 57;
            return o.getScale == null ? void 0 : o.getScale(b);
          case 57:
            _context8.t13 = _context8.sent;
          case 58:
            _context8.t12 = _context8.t13;
            if (_context8.t12) {
              _context8.next = 61;
              break;
            }
            _context8.t12 = {
              x: 1,
              y: 1
            };
          case 61:
            v = _context8.t12;
            _context8.t14 = ee;
            if (!o.convertOffsetParentRelativeRectToViewportRelativeRect) {
              _context8.next = 69;
              break;
            }
            _context8.next = 66;
            return o.convertOffsetParentRelativeRectToViewportRelativeRect({
              rect: y,
              offsetParent: b,
              strategy: f
            });
          case 66:
            _context8.t15 = _context8.sent;
            _context8.next = 70;
            break;
          case 69:
            _context8.t15 = y;
          case 70:
            _context8.t16 = _context8.t15;
            C = (0, _context8.t14)(_context8.t16);
            return _context8.abrupt("return", {
              top: (m.top - C.top + w.top) / v.y,
              bottom: (C.bottom - m.bottom + w.bottom) / v.y,
              left: (m.left - C.left + w.left) / v.x,
              right: (C.right - m.right + w.right) / v.x
            });
          case 73:
          case "end":
            return _context8.stop();
        }
      }, _callee8);
    }));
    return _le.apply(this, arguments);
  }
  var ze = Math.min,
    De = Math.max;
  function pe(e, t, r) {
    return De(e, ze(t, r));
  }
  var $e = ["top", "right", "bottom", "left"],
    he = $e.reduce(function (e, t) {
      return e.concat(t, t + "-start", t + "-end");
    }, []),
    Pe = {
      left: "right",
      right: "left",
      bottom: "top",
      top: "bottom"
    };
  function te(e) {
    return e.replace(/left|right|bottom|top/g, function (t) {
      return Pe[t];
    });
  }
  function ue(e, t, r) {
    r === void 0 && (r = !1);
    var i = T(e),
      l = Z(e),
      o = E(l);
    var a = l === "x" ? i === (r ? "end" : "start") ? "right" : "left" : i === "start" ? "bottom" : "top";
    return t.reference[o] > t.floating[o] && (a = te(a)), {
      main: a,
      cross: te(a)
    };
  }
  var Ne = {
    start: "end",
    end: "start"
  };
  function ie(e) {
    return e.replace(/start|end/g, function (t) {
      return Ne[t];
    });
  }
  var He = function He(e) {
      return e === void 0 && (e = {}), {
        name: "autoPlacement",
        options: e,
        fn: function fn(t) {
          return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
            var r, i, l, o, a, n, f, c, _e2, _e2$crossAxis, d, s, _e2$allowedPlacements, p, _e2$autoAlignment, g, w, h, m, y, b, _ue, v, C, F, N, H, K, _;
            return _regeneratorRuntime().wrap(function _callee2$(_context2) {
              while (1) switch (_context2.prev = _context2.next) {
                case 0:
                  o = t.rects;
                  a = t.middlewareData;
                  n = t.placement;
                  f = t.platform;
                  c = t.elements;
                  _e2 = e;
                  _e2$crossAxis = _e2.crossAxis;
                  d = _e2$crossAxis === void 0 ? !1 : _e2$crossAxis;
                  s = _e2.alignment;
                  _e2$allowedPlacements = _e2.allowedPlacements;
                  p = _e2$allowedPlacements === void 0 ? he : _e2$allowedPlacements;
                  _e2$autoAlignment = _e2.autoAlignment;
                  g = _e2$autoAlignment === void 0 ? !0 : _e2$autoAlignment;
                  w = _objectWithoutProperties(_e2, _excluded);
                  h = s !== void 0 || p === he ? function (k, x, S) {
                    return (k ? [].concat(_toConsumableArray(S.filter(function (R) {
                      return T(R) === k;
                    })), _toConsumableArray(S.filter(function (R) {
                      return T(R) !== k;
                    }))) : S.filter(function (R) {
                      return L(R) === R;
                    })).filter(function (R) {
                      return !k || T(R) === k || !!x && ie(R) !== R;
                    });
                  }(s || null, g, p) : p;
                  _context2.next = 17;
                  return le(t, w);
                case 17:
                  m = _context2.sent;
                  y = ((r = a.autoPlacement) == null ? void 0 : r.index) || 0;
                  b = h[y];
                  if (!(b == null)) {
                    _context2.next = 22;
                    break;
                  }
                  return _context2.abrupt("return", {});
                case 22:
                  _context2.t0 = ue;
                  _context2.t1 = b;
                  _context2.t2 = o;
                  _context2.next = 27;
                  return f.isRTL == null ? void 0 : f.isRTL(c.floating);
                case 27:
                  _context2.t3 = _context2.sent;
                  _ue = (0, _context2.t0)(_context2.t1, _context2.t2, _context2.t3);
                  v = _ue.main;
                  C = _ue.cross;
                  if (!(n !== b)) {
                    _context2.next = 33;
                    break;
                  }
                  return _context2.abrupt("return", {
                    reset: {
                      placement: h[0]
                    }
                  });
                case 33:
                  F = [m[L(b)], m[v], m[C]], N = [].concat(_toConsumableArray(((i = a.autoPlacement) == null ? void 0 : i.overflows) || []), [{
                    placement: b,
                    overflows: F
                  }]), H = h[y + 1];
                  if (!H) {
                    _context2.next = 36;
                    break;
                  }
                  return _context2.abrupt("return", {
                    data: {
                      index: y + 1,
                      overflows: N
                    },
                    reset: {
                      placement: H
                    }
                  });
                case 36:
                  K = N.map(function (k) {
                    var x = T(k.placement);
                    return [k.placement, x && d ? k.overflows.slice(0, 2).reduce(function (S, R) {
                      return S + R;
                    }, 0) : k.overflows[0], k.overflows];
                  }).sort(function (k, x) {
                    return k[1] - x[1];
                  }), _ = ((l = K.filter(function (k) {
                    return k[2].slice(0, T(k[0]) ? 2 : 3).every(function (x) {
                      return x <= 0;
                    });
                  })[0]) == null ? void 0 : l[0]) || K[0][0];
                  return _context2.abrupt("return", _ !== n ? {
                    data: {
                      index: y + 1,
                      overflows: N
                    },
                    reset: {
                      placement: _
                    }
                  } : {});
                case 38:
                case "end":
                  return _context2.stop();
              }
            }, _callee2);
          }))();
        }
      };
    },
    Be = function Be(e) {
      return e === void 0 && (e = {}), {
        name: "flip",
        options: e,
        fn: function fn(t) {
          return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
            var r, i, l, o, a, n, f, _e3, _e3$mainAxis, c, _e3$crossAxis, d, s, _e3$fallbackStrategy, p, _e3$fallbackAxisSideD, g, _e3$flipAlignment, w, h, m, y, b, v, C, F, N, H, _ue2, x, S, K, _, _x6, _S, R, k, B;
            return _regeneratorRuntime().wrap(function _callee3$(_context3) {
              while (1) switch (_context3.prev = _context3.next) {
                case 0:
                  i = t.placement;
                  l = t.middlewareData;
                  o = t.rects;
                  a = t.initialPlacement;
                  n = t.platform;
                  f = t.elements;
                  _e3 = e;
                  _e3$mainAxis = _e3.mainAxis;
                  c = _e3$mainAxis === void 0 ? !0 : _e3$mainAxis;
                  _e3$crossAxis = _e3.crossAxis;
                  d = _e3$crossAxis === void 0 ? !0 : _e3$crossAxis;
                  s = _e3.fallbackPlacements;
                  _e3$fallbackStrategy = _e3.fallbackStrategy;
                  p = _e3$fallbackStrategy === void 0 ? "bestFit" : _e3$fallbackStrategy;
                  _e3$fallbackAxisSideD = _e3.fallbackAxisSideDirection;
                  g = _e3$fallbackAxisSideD === void 0 ? "none" : _e3$fallbackAxisSideD;
                  _e3$flipAlignment = _e3.flipAlignment;
                  w = _e3$flipAlignment === void 0 ? !0 : _e3$flipAlignment;
                  h = _objectWithoutProperties(_e3, _excluded2);
                  m = L(i);
                  y = L(a) === a;
                  _context3.next = 23;
                  return n.isRTL == null ? void 0 : n.isRTL(f.floating);
                case 23:
                  b = _context3.sent;
                  v = s || (y || !w ? [te(a)] : function (x) {
                    var S = te(x);
                    return [ie(x), S, ie(S)];
                  }(a));
                  s || g === "none" || v.push.apply(v, _toConsumableArray(function (x, S, R, B) {
                    var D = T(x);
                    var M = function (Q, de, et) {
                      var Fe = ["left", "right"],
                        We = ["right", "left"],
                        tt = ["top", "bottom"],
                        it = ["bottom", "top"];
                      switch (Q) {
                        case "top":
                        case "bottom":
                          return et ? de ? We : Fe : de ? Fe : We;
                        case "left":
                        case "right":
                          return de ? tt : it;
                        default:
                          return [];
                      }
                    }(L(x), R === "start", B);
                    return D && (M = M.map(function (Q) {
                      return Q + "-" + D;
                    }), S && (M = M.concat(M.map(ie)))), M;
                  }(a, w, g, b)));
                  C = [a].concat(_toConsumableArray(v));
                  _context3.next = 29;
                  return le(t, h);
                case 29:
                  F = _context3.sent;
                  N = [];
                  H = ((r = l.flip) == null ? void 0 : r.overflows) || [];
                  if (c && N.push(F[m]), d) {
                    _ue2 = ue(i, o, b), x = _ue2.main, S = _ue2.cross;
                    N.push(F[x], F[S]);
                  }
                  if (!(H = [].concat(_toConsumableArray(H), [{
                    placement: i,
                    overflows: N
                  }]), !N.every(function (x) {
                    return x <= 0;
                  }))) {
                    _context3.next = 48;
                    break;
                  }
                  _x6 = (((K = l.flip) == null ? void 0 : K.index) || 0) + 1, _S = C[_x6];
                  if (!_S) {
                    _context3.next = 37;
                    break;
                  }
                  return _context3.abrupt("return", {
                    data: {
                      index: _x6,
                      overflows: H
                    },
                    reset: {
                      placement: _S
                    }
                  });
                case 37:
                  R = (_ = H.filter(function (B) {
                    return B.overflows[0] <= 0;
                  }).sort(function (B, D) {
                    return B.overflows[1] - D.overflows[1];
                  })[0]) == null ? void 0 : _.placement;
                  if (R) {
                    _context3.next = 46;
                    break;
                  }
                  _context3.t0 = p;
                  _context3.next = _context3.t0 === "bestFit" ? 42 : _context3.t0 === "initialPlacement" ? 45 : 46;
                  break;
                case 42:
                  B = (k = H.map(function (D) {
                    return [D.placement, D.overflows.filter(function (M) {
                      return M > 0;
                    }).reduce(function (M, Q) {
                      return M + Q;
                    }, 0)];
                  }).sort(function (D, M) {
                    return D[1] - M[1];
                  })[0]) == null ? void 0 : k[0];
                  B && (R = B);
                  return _context3.abrupt("break", 46);
                case 45:
                  R = a;
                case 46:
                  if (!(i !== R)) {
                    _context3.next = 48;
                    break;
                  }
                  return _context3.abrupt("return", {
                    reset: {
                      placement: R
                    }
                  });
                case 48:
                  return _context3.abrupt("return", {});
                case 49:
                case "end":
                  return _context3.stop();
              }
            }, _callee3);
          }))();
        }
      };
    },
    Ve = function Ve(e) {
      return e === void 0 && (e = 0), {
        name: "offset",
        options: e,
        fn: function fn(t) {
          return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee5() {
            var r, i, l;
            return _regeneratorRuntime().wrap(function _callee5$(_context5) {
              while (1) switch (_context5.prev = _context5.next) {
                case 0:
                  r = t.x;
                  i = t.y;
                  _context5.next = 4;
                  return function () {
                    var _ref2 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4(o, a) {
                      var n, f, c, d, s, p, g, w, h, m, _ref3, y, b, v;
                      return _regeneratorRuntime().wrap(function _callee4$(_context4) {
                        while (1) switch (_context4.prev = _context4.next) {
                          case 0:
                            n = o.placement;
                            f = o.platform;
                            c = o.elements;
                            _context4.next = 5;
                            return f.isRTL == null ? void 0 : f.isRTL(c.floating);
                          case 5:
                            d = _context4.sent;
                            s = L(n);
                            p = T(n);
                            g = Z(n) === "x";
                            w = ["left", "top"].includes(s) ? -1 : 1;
                            h = d && g ? -1 : 1;
                            m = typeof a == "function" ? a(o) : a;
                            _ref3 = typeof m == "number" ? {
                              mainAxis: m,
                              crossAxis: 0,
                              alignmentAxis: null
                            } : _objectSpread({
                              mainAxis: 0,
                              crossAxis: 0,
                              alignmentAxis: null
                            }, m), y = _ref3.mainAxis, b = _ref3.crossAxis, v = _ref3.alignmentAxis;
                            return _context4.abrupt("return", (p && typeof v == "number" && (b = p === "end" ? -1 * v : v), g ? {
                              x: b * h,
                              y: y * w
                            } : {
                              x: y * w,
                              y: b * h
                            }));
                          case 14:
                          case "end":
                            return _context4.stop();
                        }
                      }, _callee4);
                    }));
                    return function (_x7, _x8) {
                      return _ref2.apply(this, arguments);
                    };
                  }()(t, e);
                case 4:
                  l = _context5.sent;
                  return _context5.abrupt("return", {
                    x: r + l.x,
                    y: i + l.y,
                    data: l
                  });
                case 6:
                case "end":
                  return _context5.stop();
              }
            }, _callee5);
          }))();
        }
      };
    };
  function je(e) {
    return e === "x" ? "y" : "x";
  }
  var Ue = function Ue(e) {
    return e === void 0 && (e = {}), {
      name: "shift",
      options: e,
      fn: function fn(t) {
        return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee6() {
          var _objectSpread3;
          var r, i, l, _e4, _e4$mainAxis, o, _e4$crossAxis, a, _e4$limiter, n, f, c, d, s, p, g, w, m, _m, h;
          return _regeneratorRuntime().wrap(function _callee6$(_context6) {
            while (1) switch (_context6.prev = _context6.next) {
              case 0:
                r = t.x;
                i = t.y;
                l = t.placement;
                _e4 = e;
                _e4$mainAxis = _e4.mainAxis;
                o = _e4$mainAxis === void 0 ? !0 : _e4$mainAxis;
                _e4$crossAxis = _e4.crossAxis;
                a = _e4$crossAxis === void 0 ? !1 : _e4$crossAxis;
                _e4$limiter = _e4.limiter;
                n = _e4$limiter === void 0 ? {
                  fn: function fn(m) {
                    var y = m.x,
                      b = m.y;
                    return {
                      x: y,
                      y: b
                    };
                  }
                } : _e4$limiter;
                f = _objectWithoutProperties(_e4, _excluded3);
                c = {
                  x: r,
                  y: i
                };
                _context6.next = 14;
                return le(t, f);
              case 14:
                d = _context6.sent;
                s = Z(L(l));
                p = je(s);
                g = c[s], w = c[p];
                if (o) {
                  m = s === "y" ? "bottom" : "right";
                  g = pe(g + d[s === "y" ? "top" : "left"], g, g - d[m]);
                }
                if (a) {
                  _m = p === "y" ? "bottom" : "right";
                  w = pe(w + d[p === "y" ? "top" : "left"], w, w - d[_m]);
                }
                h = n.fn(_objectSpread(_objectSpread({}, t), {}, (_objectSpread3 = {}, _defineProperty(_objectSpread3, s, g), _defineProperty(_objectSpread3, p, w), _objectSpread3)));
                return _context6.abrupt("return", _objectSpread(_objectSpread({}, h), {}, {
                  data: {
                    x: h.x - r,
                    y: h.y - i
                  }
                }));
              case 22:
              case "end":
                return _context6.stop();
            }
          }, _callee6);
        }))();
      }
    };
  };
  function A(e) {
    var t;
    return ((t = e.ownerDocument) == null ? void 0 : t.defaultView) || window;
  }
  function O(e) {
    return A(e).getComputedStyle(e);
  }
  function me(e) {
    return e instanceof A(e).Node;
  }
  function $(e) {
    return me(e) ? (e.nodeName || "").toLowerCase() : "";
  }
  var re;
  function we() {
    if (re) return re;
    var e = navigator.userAgentData;
    return e && Array.isArray(e.brands) ? (re = e.brands.map(function (t) {
      return t.brand + "/" + t.version;
    }).join(" "), re) : navigator.userAgent;
  }
  function z(e) {
    return e instanceof A(e).HTMLElement;
  }
  function I(e) {
    return e instanceof A(e).Element;
  }
  function ye(e) {
    return (typeof ShadowRoot === "undefined" ? "undefined" : _typeof(ShadowRoot)) > "u" ? !1 : e instanceof A(e).ShadowRoot || e instanceof ShadowRoot;
  }
  function X(e) {
    var _O = O(e),
      t = _O.overflow,
      r = _O.overflowX,
      i = _O.overflowY,
      l = _O.display;
    return /auto|scroll|overlay|hidden|clip/.test(t + i + r) && !["inline", "contents"].includes(l);
  }
  function qe(e) {
    return ["table", "td", "th"].includes($(e));
  }
  function se(e) {
    var t = /firefox/i.test(we()),
      r = O(e),
      i = r.backdropFilter || r.WebkitBackdropFilter;
    return r.transform !== "none" || r.perspective !== "none" || !!i && i !== "none" || t && r.willChange === "filter" || t && !!r.filter && r.filter !== "none" || ["transform", "perspective"].some(function (l) {
      return r.willChange.includes(l);
    }) || ["paint", "layout", "strict", "content"].some(function (l) {
      var o = r.contain;
      return o != null && o.includes(l);
    });
  }
  function ce() {
    return /^((?!chrome|android).)*safari/i.test(we());
  }
  function oe(e) {
    return ["html", "body", "#document"].includes($(e));
  }
  var be = Math.min,
    Y = Math.max,
    ne = Math.round;
  function ve(e) {
    var t = O(e);
    var r = parseFloat(t.width) || 0,
      i = parseFloat(t.height) || 0;
    var l = z(e),
      o = l ? e.offsetWidth : r,
      a = l ? e.offsetHeight : i,
      n = ne(r) !== o || ne(i) !== a;
    return n && (r = o, i = a), {
      width: r,
      height: i,
      fallback: n
    };
  }
  function xe(e) {
    return I(e) ? e : e.contextElement;
  }
  var Re = {
    x: 1,
    y: 1
  };
  function j(e) {
    var t = xe(e);
    if (!z(t)) return Re;
    var r = t.getBoundingClientRect(),
      _ve = ve(t),
      i = _ve.width,
      l = _ve.height,
      o = _ve.fallback;
    var a = (o ? ne(r.width) : r.width) / i,
      n = (o ? ne(r.height) : r.height) / l;
    return a && Number.isFinite(a) || (a = 1), n && Number.isFinite(n) || (n = 1), {
      x: a,
      y: n
    };
  }
  function V(e, t, r, i) {
    var l, o;
    t === void 0 && (t = !1), r === void 0 && (r = !1);
    var a = e.getBoundingClientRect(),
      n = xe(e);
    var f = Re;
    t && (i ? I(i) && (f = j(i)) : f = j(e));
    var c = n ? A(n) : window,
      d = ce() && r;
    var s = (a.left + (d && ((l = c.visualViewport) == null ? void 0 : l.offsetLeft) || 0)) / f.x,
      p = (a.top + (d && ((o = c.visualViewport) == null ? void 0 : o.offsetTop) || 0)) / f.y,
      g = a.width / f.x,
      w = a.height / f.y;
    if (n) {
      var h = A(n),
        m = i && I(i) ? A(i) : i;
      var y = h.frameElement;
      for (; y && i && m !== h;) {
        var b = j(y),
          v = y.getBoundingClientRect(),
          C = getComputedStyle(y);
        v.x += (y.clientLeft + parseFloat(C.paddingLeft)) * b.x, v.y += (y.clientTop + parseFloat(C.paddingTop)) * b.y, s *= b.x, p *= b.y, g *= b.x, w *= b.y, s += v.x, p += v.y, y = A(y).frameElement;
      }
    }
    return ee({
      width: g,
      height: w,
      x: s,
      y: p
    });
  }
  function P(e) {
    return ((me(e) ? e.ownerDocument : e.document) || window.document).documentElement;
  }
  function ae(e) {
    return I(e) ? {
      scrollLeft: e.scrollLeft,
      scrollTop: e.scrollTop
    } : {
      scrollLeft: e.pageXOffset,
      scrollTop: e.pageYOffset
    };
  }
  function Te(e) {
    return V(P(e)).left + ae(e).scrollLeft;
  }
  function U(e) {
    if ($(e) === "html") return e;
    var t = e.assignedSlot || e.parentNode || ye(e) && e.host || P(e);
    return ye(t) ? t.host : t;
  }
  function ke(e) {
    var t = U(e);
    return oe(t) ? t.ownerDocument.body : z(t) && X(t) ? t : ke(t);
  }
  function G(e, t) {
    var r;
    t === void 0 && (t = []);
    var i = ke(e),
      l = i === ((r = e.ownerDocument) == null ? void 0 : r.body),
      o = A(i);
    return l ? t.concat(o, o.visualViewport || [], X(i) ? i : []) : t.concat(i, G(i));
  }
  function Le(e, t, r) {
    var i;
    if (t === "viewport") i = function (a, n) {
      var f = A(a),
        c = P(a),
        d = f.visualViewport;
      var s = c.clientWidth,
        p = c.clientHeight,
        g = 0,
        w = 0;
      if (d) {
        s = d.width, p = d.height;
        var h = ce();
        (!h || h && n === "fixed") && (g = d.offsetLeft, w = d.offsetTop);
      }
      return {
        width: s,
        height: p,
        x: g,
        y: w
      };
    }(e, r);else if (t === "document") i = function (a) {
      var n = P(a),
        f = ae(a),
        c = a.ownerDocument.body,
        d = Y(n.scrollWidth, n.clientWidth, c.scrollWidth, c.clientWidth),
        s = Y(n.scrollHeight, n.clientHeight, c.scrollHeight, c.clientHeight);
      var p = -f.scrollLeft + Te(a);
      var g = -f.scrollTop;
      return O(c).direction === "rtl" && (p += Y(n.clientWidth, c.clientWidth) - d), {
        width: d,
        height: s,
        x: p,
        y: g
      };
    }(P(e));else if (I(t)) i = function (a, n) {
      var f = V(a, !0, n === "fixed"),
        c = f.top + a.clientTop,
        d = f.left + a.clientLeft,
        s = z(a) ? j(a) : {
          x: 1,
          y: 1
        };
      return {
        width: a.clientWidth * s.x,
        height: a.clientHeight * s.y,
        x: d * s.x,
        y: c * s.y
      };
    }(t, r);else {
      var a = _objectSpread({}, t);
      if (ce()) {
        var l, o;
        var n = A(e);
        a.x -= ((l = n.visualViewport) == null ? void 0 : l.offsetLeft) || 0, a.y -= ((o = n.visualViewport) == null ? void 0 : o.offsetTop) || 0;
      }
      i = a;
    }
    return ee(i);
  }
  function Se(e, t) {
    var r = U(e);
    return !(r === t || !I(r) || oe(r)) && (O(r).position === "fixed" || Se(r, t));
  }
  function Ee(e, t) {
    return z(e) && O(e).position !== "fixed" ? t ? t(e) : e.offsetParent : null;
  }
  function Ae(e, t) {
    var r = A(e);
    if (!z(e)) return r;
    var i = Ee(e, t);
    for (; i && qe(i) && O(i).position === "static";) i = Ee(i, t);
    return i && ($(i) === "html" || $(i) === "body" && O(i).position === "static" && !se(i)) ? r : i || function (l) {
      var o = U(l);
      for (; z(o) && !oe(o);) {
        if (se(o)) return o;
        o = U(o);
      }
      return null;
    }(e) || r;
  }
  function Xe(e, t, r) {
    var i = z(t),
      l = P(t),
      o = V(e, !0, r === "fixed", t);
    var a = {
      scrollLeft: 0,
      scrollTop: 0
    };
    var n = {
      x: 0,
      y: 0
    };
    if (i || !i && r !== "fixed") if (($(t) !== "body" || X(l)) && (a = ae(t)), z(t)) {
      var f = V(t, !0);
      n.x = f.x + t.clientLeft, n.y = f.y + t.clientTop;
    } else l && (n.x = Te(l));
    return {
      x: o.left + a.scrollLeft - n.x,
      y: o.top + a.scrollTop - n.y,
      width: o.width,
      height: o.height
    };
  }
  var Ye = {
    getClippingRect: function getClippingRect(e) {
      var t = e.element,
        r = e.boundary,
        i = e.rootBoundary,
        l = e.strategy;
      var o = r === "clippingAncestors" ? function (c, d) {
          var s = d.get(c);
          if (s) return s;
          var p = G(c).filter(function (m) {
              return I(m) && $(m) !== "body";
            }),
            g = null;
          var w = O(c).position === "fixed";
          var h = w ? U(c) : c;
          for (; I(h) && !oe(h);) {
            var m = O(h),
              y = se(h);
            y || m.position !== "fixed" || (g = null), (w ? !y && !g : !y && m.position === "static" && g && ["absolute", "fixed"].includes(g.position) || X(h) && !y && Se(c, h)) ? p = p.filter(function (b) {
              return b !== h;
            }) : g = m, h = U(h);
          }
          return d.set(c, p), p;
        }(t, this._c) : [].concat(r),
        a = [].concat(_toConsumableArray(o), [i]),
        n = a[0],
        f = a.reduce(function (c, d) {
          var s = Le(t, d, l);
          return c.top = Y(s.top, c.top), c.right = be(s.right, c.right), c.bottom = be(s.bottom, c.bottom), c.left = Y(s.left, c.left), c;
        }, Le(t, n, l));
      return {
        width: f.right - f.left,
        height: f.bottom - f.top,
        x: f.left,
        y: f.top
      };
    },
    convertOffsetParentRelativeRectToViewportRelativeRect: function convertOffsetParentRelativeRectToViewportRelativeRect(e) {
      var t = e.rect,
        r = e.offsetParent,
        i = e.strategy;
      var l = z(r),
        o = P(r);
      if (r === o) return t;
      var a = {
          scrollLeft: 0,
          scrollTop: 0
        },
        n = {
          x: 1,
          y: 1
        };
      var f = {
        x: 0,
        y: 0
      };
      if ((l || !l && i !== "fixed") && (($(r) !== "body" || X(o)) && (a = ae(r)), z(r))) {
        var c = V(r);
        n = j(r), f.x = c.x + r.clientLeft, f.y = c.y + r.clientTop;
      }
      return {
        width: t.width * n.x,
        height: t.height * n.y,
        x: t.x * n.x - a.scrollLeft * n.x + f.x,
        y: t.y * n.y - a.scrollTop * n.y + f.y
      };
    },
    isElement: I,
    getDimensions: function getDimensions(e) {
      return ve(e);
    },
    getOffsetParent: Ae,
    getDocumentElement: P,
    getScale: j,
    getElementRects: function getElementRects(e) {
      var _this = this;
      return _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee7() {
        var t, r, i, l, o;
        return _regeneratorRuntime().wrap(function _callee7$(_context7) {
          while (1) switch (_context7.prev = _context7.next) {
            case 0:
              t = e.reference, r = e.floating, i = e.strategy;
              l = _this.getOffsetParent || Ae, o = _this.getDimensions;
              _context7.t0 = Xe;
              _context7.t1 = t;
              _context7.next = 6;
              return l(r);
            case 6:
              _context7.t2 = _context7.sent;
              _context7.t3 = i;
              _context7.t4 = (0, _context7.t0)(_context7.t1, _context7.t2, _context7.t3);
              _context7.t5 = _objectSpread;
              _context7.t6 = {
                x: 0,
                y: 0
              };
              _context7.next = 13;
              return o(r);
            case 13:
              _context7.t7 = _context7.sent;
              _context7.t8 = (0, _context7.t5)(_context7.t6, _context7.t7);
              return _context7.abrupt("return", {
                reference: _context7.t4,
                floating: _context7.t8
              });
            case 16:
            case "end":
              return _context7.stop();
          }
        }, _callee7);
      }))();
    },
    getClientRects: function getClientRects(e) {
      return Array.from(e.getClientRects());
    },
    isRTL: function isRTL(e) {
      return O(e).direction === "rtl";
    }
  };
  function Ge(e, t, r, i) {
    i === void 0 && (i = {});
    var _i = i,
      _i$ancestorScroll = _i.ancestorScroll,
      l = _i$ancestorScroll === void 0 ? !0 : _i$ancestorScroll,
      _i$ancestorResize = _i.ancestorResize,
      o = _i$ancestorResize === void 0 ? !0 : _i$ancestorResize,
      _i$elementResize = _i.elementResize,
      a = _i$elementResize === void 0 ? !0 : _i$elementResize,
      _i$animationFrame = _i.animationFrame,
      n = _i$animationFrame === void 0 ? !1 : _i$animationFrame,
      f = l || o ? [].concat(_toConsumableArray(I(e) ? G(e) : e.contextElement ? G(e.contextElement) : []), _toConsumableArray(G(t))) : [];
    f.forEach(function (p) {
      var g = !I(p) && p.toString().includes("V");
      !l || n && !g || p.addEventListener("scroll", r, {
        passive: !0
      }), o && p.addEventListener("resize", r);
    });
    var c,
      d = null;
    a && (d = new ResizeObserver(function () {
      r();
    }), I(e) && !n && d.observe(e), I(e) || !e.contextElement || n || d.observe(e.contextElement), d.observe(t));
    var s = n ? V(e) : null;
    return n && function p() {
      var g = V(e);
      !s || g.x === s.x && g.y === s.y && g.width === s.width && g.height === s.height || r(), s = g, c = requestAnimationFrame(p);
    }(), r(), function () {
      var p;
      f.forEach(function (g) {
        l && g.removeEventListener("scroll", r), o && g.removeEventListener("resize", r);
      }), (p = d) == null || p.disconnect(), d = null, n && cancelAnimationFrame(c);
    };
  }
  var Je = function Je(e, t, r) {
      var i = new Map(),
        l = _objectSpread({
          platform: Ye
        }, r),
        o = _objectSpread(_objectSpread({}, l.platform), {}, {
          _c: i
        });
      return Me(e, t, _objectSpread(_objectSpread({}, l), {}, {
        platform: o
      }));
    },
    Ie = "hn-new-changelog-seen",
    Ke = function Ke() {
      try {
        localStorage && localStorage.setItem(Ie, "true");
      } catch (_unused) {
        return null;
      }
    },
    J = function J() {
      try {
        return localStorage ? localStorage.getItem(Ie) : null;
      } catch (_unused2) {
        return null;
      }
    },
    _e = function _e(e) {
      switch (e) {
        case "top":
          return "bottom";
        case "bottom":
          return "top";
        case "left":
          return "right";
        case "right":
          return "left";
        default:
          return e;
      }
    },
    u = {
      overlay: "widget-overlay",
      wrapper: "widget-wrapper",
      iframe: "widget-iframe",
      customSelectorIndicator: "widget-custom-selector--indicate",
      fallbackTriggerWrapper: "widget-trigger-wrapper",
      fallbackTriggerIframe: "widget-trigger-iframe",
      fallbackTrigger: "widget-trigger",
      overlayBlurred: "widget-overlay--blurred",
      modal: "widget-type--modal",
      popover: "widget-type--popover",
      popoverExpanded: "widget-type--popover--expanded",
      modelRight: "widget-type--modal--right",
      modelLeft: "widget-type--modal--left",
      fallbackTriggerIndicate: "widget-trigger-wrapper--indicate",
      fallbackTriggerText: "widget-trigger-wrapper--text",
      fallbackTriggerCard: "widget-trigger-wrapper--card",
      fallbackTriggerRight: "widget-trigger--right",
      fallbackTriggerLeft: "widget-trigger--left",
      bodyModalOpen: "body-modal_open",
      overlayOpen: "widget-overlay_open",
      open: "widget_open"
    },
    Ce = {
      widget: "\n    .widget-overlay {\n      pointer-events: auto;\n      z-index: 9999999;\n      position: fixed;\n      top: 0;\n      left: 0;\n      width: 100%;\n      height: 100%;\n      display: none;\n      background-color: transparent;\n      transition: background-color 200ms ease, backdrop-filter 200ms ease;\n    }\n    .widget-overlay_open.widget-overlay--blurred {\n      background-color: hsl(0deg, 0%, 0%, 0.2);\n      backdrop-filter: blur(4px);\n    }\n\n    .widget-wrapper {\n      pointer-events: none;\n\n      z-index: 9999999;\n      \n      opacity: 0;\n\n      overflow: clip;\n      \n      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);\n\n      transition: transform 200ms cubic-bezier(0, 1.2, 1, 1), opacity 50ms ease, width 100ms ease, height 100ms ease, max-height 100ms ease;\n    }\n    \n    .widget-iframe {\n      all: unset;\n      width: 100%;\n      min-height: 100%;\n    }\n\n    .widget-type--popover {\n      transform: scale(0);\n      transform-origin: center;\n\n      position: absolute;\n\n      border-radius: 0.75rem;\n\n      width: 400px;\n      height: min(680px, 100% - 100px);\n      max-height: 680px;\n      min-height: 80px;\n      overflow-y: hidden;\n    }\n\n    .widget-type--popover--expanded {\n      width: 440px;\n    }\n\n    .widget-type--modal {\n      position: fixed;\n\n      top: 0;\n      width: 460px;\n      height: 100vh;\n      z-index: 99999999;\n    }\n\n    @media (max-width: 640px) {\n      .widget-type--modal {\n        width: 100%;\n        height: 100%;\n      }\n      .widget-type--popover {\n        width: calc(100% - 20px);\n      }\n    }\n\n    .widget-type--modal--left {\n      transform: translateX(-100%);\n      transform-origin: left;\n      left: 0;\n    }\n    .widget-type--modal--right {\n      transform: translateX(100%);\n      transform-origin: right;\n      right: 0;\n    }\n    \n    .widget-overlay_open {\n      display: block;\n    }\n    .widget-wrapper.widget_open {\n      pointer-events: auto;\n      transform: none;\n      opacity: 1;\n    }\n    \n    .widget-trigger-wrapper {\n      z-index: 99999999;\n      position: fixed;\n      bottom: 15px;\n      border-radius: 16px;\n      width: 42px;\n      height: 42px;\n      \n      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);\n\n      transition: width 200ms ease, height 200ms ease;\n    }\n    .widget-trigger-wrapper--card {\n      width: 360px;\n      height: 120px;\n    }\n    \n    .widget-custom-selector--indicate { position: relative; }\n    .widget-custom-selector--indicate::before, .widget-trigger-wrapper.widget-trigger-wrapper--indicate::before {\n      content: \"\";\n      position: absolute;\n      top: 0;\n      right: 0;\n      width: 12px;\n      height: 12px;\n      background-color: hsla(0deg, 0%, 0%, 80%);\n      border-radius: 9999px;\n      animation: ping 1150ms ease infinite;\n    }\n    .widget-trigger-wrapper.widget-trigger-wrapper--indicate.widget_open::before {\n      display: none;\n    }\n    .widget-trigger-wrapper.widget_open {\n      pointer-events: auto;\n    }\n    .widget-trigger-iframe {\n      all: unset;\n      width: 100%;\n      height: 100%;\n    }\n    .widget-trigger--left { left: 15px; }\n    .widget-trigger--right { right: 15px; }\n\n    .body-modal_open {\n      pointer-events: none;\n      overflow: hidden;\n    }\n\n    @keyframes ping {\n      75%, 100% {\n        transform: scale(1.2);\n        opacity: 0;\n      }\n    }\n  ",
      fallbackTrigger: "\n    html, body {\n      box-sizing: border-box;\n      margin: 0;\n      font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, \"Noto Sans\", sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\";\n      font-weight: 500;\n      font-size: 14px;\n    }\n\n    p { margin: 0; }\n\n    .trigger-card-wrapper {\n      width: 100%;\n      height: 100%;\n      display: flex;\n      flex-direction: column;\n      align-items: stretch;\n      justify-content: space-between;\n    }\n    .trigger-card-main {\n      flex: 1 1 0;\n      padding: 10px;\n      display: flex;\n      flex-direction: column;\n      align-items: stretch;\n      justify-content: center;\n      gap: 10px;\n    }\n    .trigger-card-title {\n      font-size: 20px;\n      font-weight: 700;\n    }\n    .trigger-card-description {\n      font-size: 12px;\n      opacity: 75%;  \n    }\n    .trigger-card-action {\n      padding: 10px 0px;\n      text-align: center;\n      background: black;\n      color: white;\n    }\n\n    button {\n      all: unset;\n\n      box-sizing: border-box;\n      cursor: pointer;\n\n      overflow: hidden;\n\n      width: 100%;\n      height: 100%;\n\n      display: grid;\n      place-items: center;\n\n      background-color: hsla(220deg, 60%, 95%, 1);\n      color: hsla(220deg, 60%, 40%, 0.8);\n\n      line-height: 1;\n\n      border: solid 1px hsla(220deg, 0%, 0%, 0.1);\n      border-radius: 16px;\n\n      transition-property: color, background-color, opacity;\n      transition-timing-function: ease;\n      transition-duration: 200ms;\n    }\n\n    button:hover {\n      opacity: 80%;\n    }\n  "
    },
    Qe = "https://widgets-v3.hellonext.co",
    fe = {
      triggerOpenIcon: "<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" width=\"24\" height=\"24\">\n  <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z\" />\n</svg>\n",
      triggerCloseIcon: "<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" width=\"24\" height=\"24\">\n  <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 18L18 6M6 6l12 12\" />\n</svg>\n"
    },
    q = function q() {
      return Math.random().toString(36).slice(2);
    };
  var Ze = /*#__PURE__*/function () {
    function Ze(t) {
      _classCallCheck(this, Ze);
      W(this, "config");
      W(this, "overlayRef");
      W(this, "wrapperRef");
      W(this, "iFrameRef");
      W(this, "fallbackTriggerWrapperRef");
      W(this, "fallbackTriggerIFrameRef");
      W(this, "fallbackTriggerRef");
      W(this, "stylesRef");
      W(this, "fallbackTriggerStylesRef");
      W(this, "isOpen");
      W(this, "initialized");
      W(this, "autoUpdateCleanup");
      this.config = {
        id: Math.random().toString(32),
        selector: t.selector || void 0,
        type: t.type || "modal",
        placement: t.placement || "right",
        openFrom: t.openFrom || "auto",
        neverExpand: t.neverExpand || !1,
        theme: t.theme || "light",
        accent: t.accent || void 0,
        triggerText: t.triggerText || null,
        onInitialized: t.onInitialized || void 0,
        enableIndicator: t.enableIndicator || !1,
        onNewChangelogIndicator: t.onNewChangelogIndicator || void 0,
        token: t.token || void 0,
        jwtToken: t.jwtToken || void 0,
        sessionToken: t.sessionToken || void 0,
        submissionBucketIds: t.submissionBucketIds || void 0,
        modules: t.modules || [],
        showOnlySubmission: t.showOnlySubmission || !1,
        changelogFilters: t.changelogFilters || {},
        postFilters: t.postFilters || {},
        orgDomain: t.orgDomain || void 0,
        latestChangelogSeen: !!J()
      }, this.isOpen = !1, this.initialized = !1, this.autoUpdateCleanup = function () {}, window.matchMedia("(max-width: 640px)").matches && this.config.type === "popover" && document.querySelector(this.config.selector) && (this.config.type = "modal");
    }
    _createClass(Ze, [{
      key: "init",
      value: function init() {
        var _this2 = this;
        var t = document.querySelector(this.config.selector);
        if (t) t.setAttribute("disabled", "true");else {
          var _this$config$triggerT;
          this.fallbackTriggerWrapperRef = document.createElement("div"), this.fallbackTriggerWrapperRef.setAttribute("id", q()), this.fallbackTriggerWrapperRef.hidden = !0, this.fallbackTriggerWrapperRef.classList.add(u.fallbackTriggerWrapper, this.config.placement === "left" ? u.fallbackTriggerLeft : u.fallbackTriggerRight), this.config.triggerText && (this.fallbackTriggerWrapperRef.style.width = "calc(".concat(this.config.triggerText.length.toString(), "ch + 42px)")), this.fallbackTriggerIFrameRef = document.createElement("iframe"), this.fallbackTriggerIFrameRef.setAttribute("id", q()), this.fallbackTriggerIFrameRef.setAttribute("src", "about:blank"), this.fallbackTriggerIFrameRef.classList.add(u.fallbackTriggerIframe), this.fallbackTriggerStylesRef = document.createElement("style"), this.fallbackTriggerStylesRef.innerHTML = Ce.fallbackTrigger, this.fallbackTriggerRef = document.createElement("button"), this.fallbackTriggerRef.setAttribute("id", q()), this.fallbackTriggerRef.setAttribute("type", "button"), this.fallbackTriggerRef.setAttribute("disabled", "true"), this.fallbackTriggerRef.classList.add(u.fallbackTrigger), this.fallbackTriggerRef.innerHTML = (_this$config$triggerT = this.config.triggerText) !== null && _this$config$triggerT !== void 0 ? _this$config$triggerT : fe.triggerOpenIcon, this.fallbackTriggerRef.addEventListener("click", function () {
            return _this2.toggle(_this2.fallbackTriggerWrapperRef);
          }), this.fallbackTriggerWrapperRef.appendChild(this.fallbackTriggerIFrameRef), document.body.appendChild(this.fallbackTriggerWrapperRef);
          var i = function i() {
            _this2.fallbackTriggerIFrameRef.contentDocument && (_this2.fallbackTriggerIFrameRef.contentDocument.head.appendChild(_this2.fallbackTriggerStylesRef), _this2.fallbackTriggerIFrameRef.contentDocument.body.appendChild(_this2.fallbackTriggerRef));
          };
          this.fallbackTriggerIFrameRef.onload = i, i();
        }
        this.overlayRef = document.createElement("div"), this.overlayRef.setAttribute("id", q()), this.overlayRef.classList.add(u.overlay), this.config.type === "modal" && this.overlayRef.classList.add(u.overlayBlurred), this.overlayRef.addEventListener("click", function () {
          return _this2.close();
        }), this.wrapperRef = document.createElement("div"), this.wrapperRef.setAttribute("id", q()), this.wrapperRef.classList.add(u.wrapper, u[this.config.type]), this.config.type === "modal" && this.wrapperRef.classList.add(this.config.openFrom === "left" ? u.modelLeft : u.modelRight), this.iFrameRef = document.createElement("iframe"), this.iFrameRef.setAttribute("id", q()), this.iFrameRef.classList.add(u.iframe), this.iFrameRef.setAttribute("title", "featureOS widget"), this.iFrameRef.setAttribute("src", Qe), this.iFrameRef.setAttribute("referrerPolicy", "origin"), this.iFrameRef.setAttribute("sandbox", "allow-scripts allow-forms allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-top-navigation allow-top-navigation-by-user-activation allow-modals"), this.stylesRef = document.createElement("style"), this.stylesRef.innerHTML = Ce.widget, this.wrapperRef.appendChild(this.stylesRef), this.wrapperRef.appendChild(this.iFrameRef), document.body.appendChild(this.overlayRef), document.body.appendChild(this.wrapperRef), window.addEventListener("message", function (i) {
          _this2.handleNewMessagesToHost(i);
        });
        var r = document.querySelectorAll(this.config.selector);
        document.addEventListener("click", function (i) {
          var o, a;
          var l = !1;
          for (var n = 0; n < r.length; n++) if (i.target === r[n] || r[n].contains(i.target)) {
            l = !0;
            break;
          }
          if (!l) {
            var _n = (a = (o = i.target) == null ? void 0 : o.dataset) == null ? void 0 : a.helposId;
            if (!_n) return;
            _this2.postMessageToServer({
              action: "SET_ARTICLE_ID",
              payload: {
                articleId: _n
              }
            }), _this2.open(i.target);
            return;
          }
          _this2.postMessageToServer({
            action: "SET_ARTICLE_ID",
            payload: {
              articleId: null,
              page: "home"
            }
          }), _this2.open(i.target);
        });
      }
    }, {
      key: "updateConfig",
      value: function updateConfig(t) {
        var r, i, l, o, a, n, f, c;
        (r = this.overlayRef) == null || r.remove(), (i = this.wrapperRef) == null || i.remove(), (l = this.iFrameRef) == null || l.remove(), (o = this.fallbackTriggerWrapperRef) == null || o.remove(), (a = this.fallbackTriggerIFrameRef) == null || a.remove(), (n = this.fallbackTriggerRef) == null || n.remove(), (f = this.stylesRef) == null || f.remove(), (c = this.fallbackTriggerStylesRef) == null || c.remove(), this.config = _objectSpread(_objectSpread({}, this.config), t), this.init();
      }
    }, {
      key: "open",
      value: function open() {
        var _this3 = this;
        var t = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        this.initialized && (document.addEventListener("keydown", function (r) {
          r.key === "Escape" && _this3.close();
        }), this.isOpen = !0, this.postMessageToServer({
          action: "open",
          payload: {}
        }), this.overlayRef.classList.add(u.overlayOpen), this.wrapperRef.classList.add(u.open), this.config.type === "modal" && document.body.classList.add(u.bodyModalOpen), this.config.type === "popover" && (this.autoUpdateCleanup(), this.updatePopoverPosition(t || this.fallbackTriggerWrapperRef || document.querySelector(this.config.selector))), this.fallbackTriggerRef && (this.fallbackTriggerWrapperRef.classList.add(u.open), this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerCard), this.config.triggerText || (this.fallbackTriggerRef.innerHTML = fe.triggerCloseIcon)));
      }
    }, {
      key: "close",
      value: function close() {
        document.removeEventListener("keydown", function () {}), this.isOpen = !1, this.autoUpdateCleanup(), this.postMessageToServer({
          action: "close",
          payload: {}
        }), this.overlayRef.classList.remove(u.overlayOpen), this.wrapperRef.classList.remove(u.open), this.config.type === "modal" && document.body.classList.remove(u.bodyModalOpen), this.fallbackTriggerRef && (this.fallbackTriggerWrapperRef.classList.remove(u.open), this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerCard), this.config.triggerText || (this.fallbackTriggerRef.innerHTML = fe.triggerOpenIcon));
      }
    }, {
      key: "toggle",
      value: function toggle() {
        var t = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        this.isOpen ? this.close() : this.open(t);
      }
    }, {
      key: "updatePopoverPosition",
      value: function updatePopoverPosition(t) {
        var _this4 = this;
        var i, l;
        var r = {
          middleware: [Ue({
            padding: 10
          }), Ve(10)]
        };
        this.config.openFrom === "auto" ? (i = r == null ? void 0 : r.middleware) == null || i.push(He()) : (r.placement = this.config.openFrom, (l = r == null ? void 0 : r.middleware) == null || l.push(Be())), this.autoUpdateCleanup = Ge(t, this.wrapperRef, function () {
          return Je(t, _this4.wrapperRef, r).then(function (_ref4) {
            var o = _ref4.x,
              a = _ref4.y,
              n = _ref4.placement;
            Object.assign(_this4.wrapperRef.style, {
              left: "".concat(o, "px"),
              top: "".concat(a, "px"),
              transformOrigin: "".concat(_e(n))
            });
          });
        });
      }
    }, {
      key: "postMessageToServer",
      value: function postMessageToServer(t) {
        var r, i, l;
        (l = (i = (r = this.iFrameRef) == null ? void 0 : r.contentWindow) == null ? void 0 : i.postMessage) == null || l.call(i, JSON.stringify(t), "*");
      }
    }, {
      key: "handleNewMessagesToHost",
      value: function handleNewMessagesToHost(t) {
        var r, i, l;
        if (t.preventDefault(), t.data && typeof t.data == "string") try {
          var o = JSON.parse(t.data);
          switch (o.action) {
            case "init":
              this.postMessageToServer({
                action: "config",
                payload: _objectSpread(_objectSpread({}, this.config), {}, {
                  open: this.isOpen
                })
              });
              break;
            case "initialized":
              this.initialized = !0;
              var a = document.querySelector(this.config.selector);
              a && a.removeAttribute("disabled"), this.fallbackTriggerRef && this.fallbackTriggerRef.removeAttribute("disabled"), (i = (r = this.config).onInitialized) == null || i.call(r);
              break;
            case "trigger-ready":
              o.payload["for"] === this.config.id && this.fallbackTriggerWrapperRef && this.fallbackTriggerRef && (Object.assign(this.fallbackTriggerRef.style, o.payload.styles), this.fallbackTriggerWrapperRef.hidden = !1);
              break;
            case "close":
              o.payload["for"] === this.config.id && this.close();
              break;
            case "change-popover-size":
              o.payload["for"] === this.config.id && this.config.type === "popover" && !this.config.neverExpand && (o.payload.size === "expanded" && this.wrapperRef.classList.add(u.popoverExpanded), o.payload.size === "normal" && this.wrapperRef.classList.remove(u.popoverExpanded));
              break;
            case "set-changelogs-seen":
              o.payload["for"] === this.config.id && (Ke(), (l = document.querySelector(this.config.selector)) == null || l.classList.remove(u.customSelectorIndicator), this.fallbackTriggerWrapperRef && this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerIndicate));
              break;
            case "indicate":
              if (o.payload["for"] === this.config.id && o.payload.indicate) {
                if (this.config.onNewChangelogIndicator) J() || this.config.onNewChangelogIndicator();else if (this.config.enableIndicator) {
                  var n = document.querySelector(this.config.selector);
                  n && (J() ? n.classList.remove(u.customSelectorIndicator) : n.classList.add(u.customSelectorIndicator));
                }
                this.config.enableIndicator && this.fallbackTriggerRef && (J() ? this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerIndicate) : this.fallbackTriggerWrapperRef.classList.add(u.fallbackTriggerIndicate));
              }
              break;
            case "show-latest-changelog":
              if (o.payload["for"] === this.config.id && this.fallbackTriggerRef && !this.config.triggerText && !this.isOpen && !J()) {
                var _n2 = o.payload.changelog;
                _n2 && (this.fallbackTriggerWrapperRef.classList.add(u.fallbackTriggerCard), this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerIndicate), this.fallbackTriggerRef.innerHTML = "\n                  <div class=\"trigger-card-wrapper\">\n                    <div class=\"trigger-card-main\">\n                      <p class=\"trigger-card-title\">".concat(_n2.title, "</p>\n                      <p class=\"trigger-card-description\">").concat(_n2.published_at, "</p>\n                    </div>\n                    <div class=\"trigger-card-action\">View update!</div>\n                  </div>\n                "));
              }
              break;
            default:
              break;
          }
        } catch (o) {
          console.error(o);
        }
      }
    }]);
    return Ze;
  }();
  return Ze;
});

/***/ }),

/***/ "./resources/css/asset.css":
/*!*********************************!*\
  !*** ./resources/css/asset.css ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			id: moduleId,
/******/ 			loaded: false,
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/node module decorator */
/******/ 	(() => {
/******/ 		__webpack_require__.nmd = (module) => {
/******/ 			module.paths = [];
/******/ 			if (!module.children) module.children = [];
/******/ 			return module;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/asset": 0,
/******/ 			"css/asset": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkperscom_featureos"] = self["webpackChunkperscom_featureos"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["css/asset"], () => (__webpack_require__("./resources/js/asset.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["css/asset"], () => (__webpack_require__("./resources/css/asset.css")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;