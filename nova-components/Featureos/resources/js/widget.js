;(function (T, E) {
  typeof exports == 'object' && typeof module < 'u'
    ? (module.exports = E())
    : typeof define == 'function' && define.amd
    ? define(E)
    : ((T = typeof globalThis < 'u' ? globalThis : T || self), (T.HellonextWidget = E()))
})(this, function () {
  'use strict'
  var rt = Object.defineProperty
  var ot = (T, E, L) => (E in T ? rt(T, E, { enumerable: !0, configurable: !0, writable: !0, value: L }) : (T[E] = L))
  var W = (T, E, L) => (ot(T, typeof E != 'symbol' ? E + '' : E, L), L)
  function T(e) {
    return e.split('-')[1]
  }
  function E(e) {
    return e === 'y' ? 'height' : 'width'
  }
  function L(e) {
    return e.split('-')[0]
  }
  function Z(e) {
    return ['top', 'bottom'].includes(L(e)) ? 'x' : 'y'
  }
  function ge(e, t, r) {
    let { reference: i, floating: l } = e
    const o = i.x + i.width / 2 - l.width / 2,
      a = i.y + i.height / 2 - l.height / 2,
      n = Z(t),
      f = E(n),
      c = i[f] / 2 - l[f] / 2,
      d = n === 'x'
    let s
    switch (L(t)) {
      case 'top':
        s = { x: o, y: i.y - l.height }
        break
      case 'bottom':
        s = { x: o, y: i.y + i.height }
        break
      case 'right':
        s = { x: i.x + i.width, y: a }
        break
      case 'left':
        s = { x: i.x - l.width, y: a }
        break
      default:
        s = { x: i.x, y: i.y }
    }
    switch (T(t)) {
      case 'start':
        s[n] -= c * (r && d ? -1 : 1)
        break
      case 'end':
        s[n] += c * (r && d ? -1 : 1)
    }
    return s
  }
  const Me = async (e, t, r) => {
    const { placement: i = 'bottom', strategy: l = 'absolute', middleware: o = [], platform: a } = r,
      n = o.filter(Boolean),
      f = await (a.isRTL == null ? void 0 : a.isRTL(t))
    let c = await a.getElementRects({ reference: e, floating: t, strategy: l }),
      { x: d, y: s } = ge(c, i, f),
      p = i,
      g = {},
      w = 0
    for (let h = 0; h < n.length; h++) {
      const { name: m, fn: y } = n[h],
        {
          x: b,
          y: v,
          data: C,
          reset: F
        } = await y({
          x: d,
          y: s,
          initialPlacement: i,
          placement: p,
          strategy: l,
          middlewareData: g,
          rects: c,
          platform: a,
          elements: { reference: e, floating: t }
        })
      ;(d = b ?? d),
        (s = v ?? s),
        (g = { ...g, [m]: { ...g[m], ...C } }),
        F &&
          w <= 50 &&
          (w++,
          typeof F == 'object' &&
            (F.placement && (p = F.placement),
            F.rects && (c = F.rects === !0 ? await a.getElementRects({ reference: e, floating: t, strategy: l }) : F.rects),
            ({ x: d, y: s } = ge(c, p, f))),
          (h = -1))
    }
    return { x: d, y: s, placement: p, strategy: l, middlewareData: g }
  }
  function Oe(e) {
    return typeof e != 'number'
      ? (function (t) {
          return { top: 0, right: 0, bottom: 0, left: 0, ...t }
        })(e)
      : { top: e, right: e, bottom: e, left: e }
  }
  function ee(e) {
    return { ...e, top: e.y, left: e.x, right: e.x + e.width, bottom: e.y + e.height }
  }
  async function le(e, t) {
    var r
    t === void 0 && (t = {})
    const { x: i, y: l, platform: o, rects: a, elements: n, strategy: f } = e,
      {
        boundary: c = 'clippingAncestors',
        rootBoundary: d = 'viewport',
        elementContext: s = 'floating',
        altBoundary: p = !1,
        padding: g = 0
      } = t,
      w = Oe(g),
      h = n[p ? (s === 'floating' ? 'reference' : 'floating') : s],
      m = ee(
        await o.getClippingRect({
          element:
            (r = await (o.isElement == null ? void 0 : o.isElement(h))) == null || r
              ? h
              : h.contextElement || (await (o.getDocumentElement == null ? void 0 : o.getDocumentElement(n.floating))),
          boundary: c,
          rootBoundary: d,
          strategy: f
        })
      ),
      y = s === 'floating' ? { ...a.floating, x: i, y: l } : a.reference,
      b = await (o.getOffsetParent == null ? void 0 : o.getOffsetParent(n.floating)),
      v = ((await (o.isElement == null ? void 0 : o.isElement(b))) && (await (o.getScale == null ? void 0 : o.getScale(b)))) || {
        x: 1,
        y: 1
      },
      C = ee(
        o.convertOffsetParentRelativeRectToViewportRelativeRect
          ? await o.convertOffsetParentRelativeRectToViewportRelativeRect({ rect: y, offsetParent: b, strategy: f })
          : y
      )
    return {
      top: (m.top - C.top + w.top) / v.y,
      bottom: (C.bottom - m.bottom + w.bottom) / v.y,
      left: (m.left - C.left + w.left) / v.x,
      right: (C.right - m.right + w.right) / v.x
    }
  }
  const ze = Math.min,
    De = Math.max
  function pe(e, t, r) {
    return De(e, ze(t, r))
  }
  const $e = ['top', 'right', 'bottom', 'left'],
    he = $e.reduce((e, t) => e.concat(t, t + '-start', t + '-end'), []),
    Pe = { left: 'right', right: 'left', bottom: 'top', top: 'bottom' }
  function te(e) {
    return e.replace(/left|right|bottom|top/g, (t) => Pe[t])
  }
  function ue(e, t, r) {
    r === void 0 && (r = !1)
    const i = T(e),
      l = Z(e),
      o = E(l)
    let a = l === 'x' ? (i === (r ? 'end' : 'start') ? 'right' : 'left') : i === 'start' ? 'bottom' : 'top'
    return t.reference[o] > t.floating[o] && (a = te(a)), { main: a, cross: te(a) }
  }
  const Ne = { start: 'end', end: 'start' }
  function ie(e) {
    return e.replace(/start|end/g, (t) => Ne[t])
  }
  const He = function (e) {
      return (
        e === void 0 && (e = {}),
        {
          name: 'autoPlacement',
          options: e,
          async fn(t) {
            var r, i, l
            const { rects: o, middlewareData: a, placement: n, platform: f, elements: c } = t,
              { crossAxis: d = !1, alignment: s, allowedPlacements: p = he, autoAlignment: g = !0, ...w } = e,
              h =
                s !== void 0 || p === he
                  ? (function (k, x, S) {
                      return (k ? [...S.filter((R) => T(R) === k), ...S.filter((R) => T(R) !== k)] : S.filter((R) => L(R) === R)).filter(
                        (R) => !k || T(R) === k || (!!x && ie(R) !== R)
                      )
                    })(s || null, g, p)
                  : p,
              m = await le(t, w),
              y = ((r = a.autoPlacement) == null ? void 0 : r.index) || 0,
              b = h[y]
            if (b == null) return {}
            const { main: v, cross: C } = ue(b, o, await (f.isRTL == null ? void 0 : f.isRTL(c.floating)))
            if (n !== b) return { reset: { placement: h[0] } }
            const F = [m[L(b)], m[v], m[C]],
              N = [...(((i = a.autoPlacement) == null ? void 0 : i.overflows) || []), { placement: b, overflows: F }],
              H = h[y + 1]
            if (H) return { data: { index: y + 1, overflows: N }, reset: { placement: H } }
            const K = N.map((k) => {
                const x = T(k.placement)
                return [k.placement, x && d ? k.overflows.slice(0, 2).reduce((S, R) => S + R, 0) : k.overflows[0], k.overflows]
              }).sort((k, x) => k[1] - x[1]),
              _ = ((l = K.filter((k) => k[2].slice(0, T(k[0]) ? 2 : 3).every((x) => x <= 0))[0]) == null ? void 0 : l[0]) || K[0][0]
            return _ !== n ? { data: { index: y + 1, overflows: N }, reset: { placement: _ } } : {}
          }
        }
      )
    },
    Be = function (e) {
      return (
        e === void 0 && (e = {}),
        {
          name: 'flip',
          options: e,
          async fn(t) {
            var r
            const { placement: i, middlewareData: l, rects: o, initialPlacement: a, platform: n, elements: f } = t,
              {
                mainAxis: c = !0,
                crossAxis: d = !0,
                fallbackPlacements: s,
                fallbackStrategy: p = 'bestFit',
                fallbackAxisSideDirection: g = 'none',
                flipAlignment: w = !0,
                ...h
              } = e,
              m = L(i),
              y = L(a) === a,
              b = await (n.isRTL == null ? void 0 : n.isRTL(f.floating)),
              v =
                s ||
                (y || !w
                  ? [te(a)]
                  : (function (x) {
                      const S = te(x)
                      return [ie(x), S, ie(S)]
                    })(a))
            s ||
              g === 'none' ||
              v.push(
                ...(function (x, S, R, B) {
                  const D = T(x)
                  let M = (function (Q, de, et) {
                    const Fe = ['left', 'right'],
                      We = ['right', 'left'],
                      tt = ['top', 'bottom'],
                      it = ['bottom', 'top']
                    switch (Q) {
                      case 'top':
                      case 'bottom':
                        return et ? (de ? We : Fe) : de ? Fe : We
                      case 'left':
                      case 'right':
                        return de ? tt : it
                      default:
                        return []
                    }
                  })(L(x), R === 'start', B)
                  return D && ((M = M.map((Q) => Q + '-' + D)), S && (M = M.concat(M.map(ie)))), M
                })(a, w, g, b)
              )
            const C = [a, ...v],
              F = await le(t, h),
              N = []
            let H = ((r = l.flip) == null ? void 0 : r.overflows) || []
            if ((c && N.push(F[m]), d)) {
              const { main: x, cross: S } = ue(i, o, b)
              N.push(F[x], F[S])
            }
            if (((H = [...H, { placement: i, overflows: N }]), !N.every((x) => x <= 0))) {
              var K, _
              const x = (((K = l.flip) == null ? void 0 : K.index) || 0) + 1,
                S = C[x]
              if (S) return { data: { index: x, overflows: H }, reset: { placement: S } }
              let R =
                (_ = H.filter((B) => B.overflows[0] <= 0).sort((B, D) => B.overflows[1] - D.overflows[1])[0]) == null ? void 0 : _.placement
              if (!R)
                switch (p) {
                  case 'bestFit': {
                    var k
                    const B =
                      (k = H.map((D) => [D.placement, D.overflows.filter((M) => M > 0).reduce((M, Q) => M + Q, 0)]).sort(
                        (D, M) => D[1] - M[1]
                      )[0]) == null
                        ? void 0
                        : k[0]
                    B && (R = B)
                    break
                  }
                  case 'initialPlacement':
                    R = a
                }
              if (i !== R) return { reset: { placement: R } }
            }
            return {}
          }
        }
      )
    },
    Ve = function (e) {
      return (
        e === void 0 && (e = 0),
        {
          name: 'offset',
          options: e,
          async fn(t) {
            const { x: r, y: i } = t,
              l = await (async function (o, a) {
                const { placement: n, platform: f, elements: c } = o,
                  d = await (f.isRTL == null ? void 0 : f.isRTL(c.floating)),
                  s = L(n),
                  p = T(n),
                  g = Z(n) === 'x',
                  w = ['left', 'top'].includes(s) ? -1 : 1,
                  h = d && g ? -1 : 1,
                  m = typeof a == 'function' ? a(o) : a
                let {
                  mainAxis: y,
                  crossAxis: b,
                  alignmentAxis: v
                } = typeof m == 'number'
                  ? { mainAxis: m, crossAxis: 0, alignmentAxis: null }
                  : { mainAxis: 0, crossAxis: 0, alignmentAxis: null, ...m }
                return p && typeof v == 'number' && (b = p === 'end' ? -1 * v : v), g ? { x: b * h, y: y * w } : { x: y * w, y: b * h }
              })(t, e)
            return { x: r + l.x, y: i + l.y, data: l }
          }
        }
      )
    }
  function je(e) {
    return e === 'x' ? 'y' : 'x'
  }
  const Ue = function (e) {
    return (
      e === void 0 && (e = {}),
      {
        name: 'shift',
        options: e,
        async fn(t) {
          const { x: r, y: i, placement: l } = t,
            {
              mainAxis: o = !0,
              crossAxis: a = !1,
              limiter: n = {
                fn: (m) => {
                  let { x: y, y: b } = m
                  return { x: y, y: b }
                }
              },
              ...f
            } = e,
            c = { x: r, y: i },
            d = await le(t, f),
            s = Z(L(l)),
            p = je(s)
          let g = c[s],
            w = c[p]
          if (o) {
            const m = s === 'y' ? 'bottom' : 'right'
            g = pe(g + d[s === 'y' ? 'top' : 'left'], g, g - d[m])
          }
          if (a) {
            const m = p === 'y' ? 'bottom' : 'right'
            w = pe(w + d[p === 'y' ? 'top' : 'left'], w, w - d[m])
          }
          const h = n.fn({ ...t, [s]: g, [p]: w })
          return { ...h, data: { x: h.x - r, y: h.y - i } }
        }
      }
    )
  }
  function A(e) {
    var t
    return ((t = e.ownerDocument) == null ? void 0 : t.defaultView) || window
  }
  function O(e) {
    return A(e).getComputedStyle(e)
  }
  function me(e) {
    return e instanceof A(e).Node
  }
  function $(e) {
    return me(e) ? (e.nodeName || '').toLowerCase() : ''
  }
  let re
  function we() {
    if (re) return re
    const e = navigator.userAgentData
    return e && Array.isArray(e.brands) ? ((re = e.brands.map((t) => t.brand + '/' + t.version).join(' ')), re) : navigator.userAgent
  }
  function z(e) {
    return e instanceof A(e).HTMLElement
  }
  function I(e) {
    return e instanceof A(e).Element
  }
  function ye(e) {
    return typeof ShadowRoot > 'u' ? !1 : e instanceof A(e).ShadowRoot || e instanceof ShadowRoot
  }
  function X(e) {
    const { overflow: t, overflowX: r, overflowY: i, display: l } = O(e)
    return /auto|scroll|overlay|hidden|clip/.test(t + i + r) && !['inline', 'contents'].includes(l)
  }
  function qe(e) {
    return ['table', 'td', 'th'].includes($(e))
  }
  function se(e) {
    const t = /firefox/i.test(we()),
      r = O(e),
      i = r.backdropFilter || r.WebkitBackdropFilter
    return (
      r.transform !== 'none' ||
      r.perspective !== 'none' ||
      (!!i && i !== 'none') ||
      (t && r.willChange === 'filter') ||
      (t && !!r.filter && r.filter !== 'none') ||
      ['transform', 'perspective'].some((l) => r.willChange.includes(l)) ||
      ['paint', 'layout', 'strict', 'content'].some((l) => {
        const o = r.contain
        return o != null && o.includes(l)
      })
    )
  }
  function ce() {
    return /^((?!chrome|android).)*safari/i.test(we())
  }
  function oe(e) {
    return ['html', 'body', '#document'].includes($(e))
  }
  const be = Math.min,
    Y = Math.max,
    ne = Math.round
  function ve(e) {
    const t = O(e)
    let r = parseFloat(t.width) || 0,
      i = parseFloat(t.height) || 0
    const l = z(e),
      o = l ? e.offsetWidth : r,
      a = l ? e.offsetHeight : i,
      n = ne(r) !== o || ne(i) !== a
    return n && ((r = o), (i = a)), { width: r, height: i, fallback: n }
  }
  function xe(e) {
    return I(e) ? e : e.contextElement
  }
  const Re = { x: 1, y: 1 }
  function j(e) {
    const t = xe(e)
    if (!z(t)) return Re
    const r = t.getBoundingClientRect(),
      { width: i, height: l, fallback: o } = ve(t)
    let a = (o ? ne(r.width) : r.width) / i,
      n = (o ? ne(r.height) : r.height) / l
    return (a && Number.isFinite(a)) || (a = 1), (n && Number.isFinite(n)) || (n = 1), { x: a, y: n }
  }
  function V(e, t, r, i) {
    var l, o
    t === void 0 && (t = !1), r === void 0 && (r = !1)
    const a = e.getBoundingClientRect(),
      n = xe(e)
    let f = Re
    t && (i ? I(i) && (f = j(i)) : (f = j(e)))
    const c = n ? A(n) : window,
      d = ce() && r
    let s = (a.left + ((d && ((l = c.visualViewport) == null ? void 0 : l.offsetLeft)) || 0)) / f.x,
      p = (a.top + ((d && ((o = c.visualViewport) == null ? void 0 : o.offsetTop)) || 0)) / f.y,
      g = a.width / f.x,
      w = a.height / f.y
    if (n) {
      const h = A(n),
        m = i && I(i) ? A(i) : i
      let y = h.frameElement
      for (; y && i && m !== h; ) {
        const b = j(y),
          v = y.getBoundingClientRect(),
          C = getComputedStyle(y)
        ;(v.x += (y.clientLeft + parseFloat(C.paddingLeft)) * b.x),
          (v.y += (y.clientTop + parseFloat(C.paddingTop)) * b.y),
          (s *= b.x),
          (p *= b.y),
          (g *= b.x),
          (w *= b.y),
          (s += v.x),
          (p += v.y),
          (y = A(y).frameElement)
      }
    }
    return ee({ width: g, height: w, x: s, y: p })
  }
  function P(e) {
    return ((me(e) ? e.ownerDocument : e.document) || window.document).documentElement
  }
  function ae(e) {
    return I(e) ? { scrollLeft: e.scrollLeft, scrollTop: e.scrollTop } : { scrollLeft: e.pageXOffset, scrollTop: e.pageYOffset }
  }
  function Te(e) {
    return V(P(e)).left + ae(e).scrollLeft
  }
  function U(e) {
    if ($(e) === 'html') return e
    const t = e.assignedSlot || e.parentNode || (ye(e) && e.host) || P(e)
    return ye(t) ? t.host : t
  }
  function ke(e) {
    const t = U(e)
    return oe(t) ? t.ownerDocument.body : z(t) && X(t) ? t : ke(t)
  }
  function G(e, t) {
    var r
    t === void 0 && (t = [])
    const i = ke(e),
      l = i === ((r = e.ownerDocument) == null ? void 0 : r.body),
      o = A(i)
    return l ? t.concat(o, o.visualViewport || [], X(i) ? i : []) : t.concat(i, G(i))
  }
  function Le(e, t, r) {
    let i
    if (t === 'viewport')
      i = (function (a, n) {
        const f = A(a),
          c = P(a),
          d = f.visualViewport
        let s = c.clientWidth,
          p = c.clientHeight,
          g = 0,
          w = 0
        if (d) {
          ;(s = d.width), (p = d.height)
          const h = ce()
          ;(!h || (h && n === 'fixed')) && ((g = d.offsetLeft), (w = d.offsetTop))
        }
        return { width: s, height: p, x: g, y: w }
      })(e, r)
    else if (t === 'document')
      i = (function (a) {
        const n = P(a),
          f = ae(a),
          c = a.ownerDocument.body,
          d = Y(n.scrollWidth, n.clientWidth, c.scrollWidth, c.clientWidth),
          s = Y(n.scrollHeight, n.clientHeight, c.scrollHeight, c.clientHeight)
        let p = -f.scrollLeft + Te(a)
        const g = -f.scrollTop
        return O(c).direction === 'rtl' && (p += Y(n.clientWidth, c.clientWidth) - d), { width: d, height: s, x: p, y: g }
      })(P(e))
    else if (I(t))
      i = (function (a, n) {
        const f = V(a, !0, n === 'fixed'),
          c = f.top + a.clientTop,
          d = f.left + a.clientLeft,
          s = z(a) ? j(a) : { x: 1, y: 1 }
        return { width: a.clientWidth * s.x, height: a.clientHeight * s.y, x: d * s.x, y: c * s.y }
      })(t, r)
    else {
      const a = { ...t }
      if (ce()) {
        var l, o
        const n = A(e)
        ;(a.x -= ((l = n.visualViewport) == null ? void 0 : l.offsetLeft) || 0),
          (a.y -= ((o = n.visualViewport) == null ? void 0 : o.offsetTop) || 0)
      }
      i = a
    }
    return ee(i)
  }
  function Se(e, t) {
    const r = U(e)
    return !(r === t || !I(r) || oe(r)) && (O(r).position === 'fixed' || Se(r, t))
  }
  function Ee(e, t) {
    return z(e) && O(e).position !== 'fixed' ? (t ? t(e) : e.offsetParent) : null
  }
  function Ae(e, t) {
    const r = A(e)
    if (!z(e)) return r
    let i = Ee(e, t)
    for (; i && qe(i) && O(i).position === 'static'; ) i = Ee(i, t)
    return i && ($(i) === 'html' || ($(i) === 'body' && O(i).position === 'static' && !se(i)))
      ? r
      : i ||
          (function (l) {
            let o = U(l)
            for (; z(o) && !oe(o); ) {
              if (se(o)) return o
              o = U(o)
            }
            return null
          })(e) ||
          r
  }
  function Xe(e, t, r) {
    const i = z(t),
      l = P(t),
      o = V(e, !0, r === 'fixed', t)
    let a = { scrollLeft: 0, scrollTop: 0 }
    const n = { x: 0, y: 0 }
    if (i || (!i && r !== 'fixed'))
      if ((($(t) !== 'body' || X(l)) && (a = ae(t)), z(t))) {
        const f = V(t, !0)
        ;(n.x = f.x + t.clientLeft), (n.y = f.y + t.clientTop)
      } else l && (n.x = Te(l))
    return { x: o.left + a.scrollLeft - n.x, y: o.top + a.scrollTop - n.y, width: o.width, height: o.height }
  }
  const Ye = {
    getClippingRect: function (e) {
      let { element: t, boundary: r, rootBoundary: i, strategy: l } = e
      const o =
          r === 'clippingAncestors'
            ? (function (c, d) {
                const s = d.get(c)
                if (s) return s
                let p = G(c).filter((m) => I(m) && $(m) !== 'body'),
                  g = null
                const w = O(c).position === 'fixed'
                let h = w ? U(c) : c
                for (; I(h) && !oe(h); ) {
                  const m = O(h),
                    y = se(h)
                  y || m.position !== 'fixed' || (g = null),
                    (
                      w
                        ? !y && !g
                        : (!y && m.position === 'static' && g && ['absolute', 'fixed'].includes(g.position)) || (X(h) && !y && Se(c, h))
                    )
                      ? (p = p.filter((b) => b !== h))
                      : (g = m),
                    (h = U(h))
                }
                return d.set(c, p), p
              })(t, this._c)
            : [].concat(r),
        a = [...o, i],
        n = a[0],
        f = a.reduce(
          (c, d) => {
            const s = Le(t, d, l)
            return (
              (c.top = Y(s.top, c.top)),
              (c.right = be(s.right, c.right)),
              (c.bottom = be(s.bottom, c.bottom)),
              (c.left = Y(s.left, c.left)),
              c
            )
          },
          Le(t, n, l)
        )
      return { width: f.right - f.left, height: f.bottom - f.top, x: f.left, y: f.top }
    },
    convertOffsetParentRelativeRectToViewportRelativeRect: function (e) {
      let { rect: t, offsetParent: r, strategy: i } = e
      const l = z(r),
        o = P(r)
      if (r === o) return t
      let a = { scrollLeft: 0, scrollTop: 0 },
        n = { x: 1, y: 1 }
      const f = { x: 0, y: 0 }
      if ((l || (!l && i !== 'fixed')) && (($(r) !== 'body' || X(o)) && (a = ae(r)), z(r))) {
        const c = V(r)
        ;(n = j(r)), (f.x = c.x + r.clientLeft), (f.y = c.y + r.clientTop)
      }
      return {
        width: t.width * n.x,
        height: t.height * n.y,
        x: t.x * n.x - a.scrollLeft * n.x + f.x,
        y: t.y * n.y - a.scrollTop * n.y + f.y
      }
    },
    isElement: I,
    getDimensions: function (e) {
      return ve(e)
    },
    getOffsetParent: Ae,
    getDocumentElement: P,
    getScale: j,
    async getElementRects(e) {
      let { reference: t, floating: r, strategy: i } = e
      const l = this.getOffsetParent || Ae,
        o = this.getDimensions
      return { reference: Xe(t, await l(r), i), floating: { x: 0, y: 0, ...(await o(r)) } }
    },
    getClientRects: (e) => Array.from(e.getClientRects()),
    isRTL: (e) => O(e).direction === 'rtl'
  }
  function Ge(e, t, r, i) {
    i === void 0 && (i = {})
    const { ancestorScroll: l = !0, ancestorResize: o = !0, elementResize: a = !0, animationFrame: n = !1 } = i,
      f = l || o ? [...(I(e) ? G(e) : e.contextElement ? G(e.contextElement) : []), ...G(t)] : []
    f.forEach((p) => {
      const g = !I(p) && p.toString().includes('V')
      !l || (n && !g) || p.addEventListener('scroll', r, { passive: !0 }), o && p.addEventListener('resize', r)
    })
    let c,
      d = null
    a &&
      ((d = new ResizeObserver(() => {
        r()
      })),
      I(e) && !n && d.observe(e),
      I(e) || !e.contextElement || n || d.observe(e.contextElement),
      d.observe(t))
    let s = n ? V(e) : null
    return (
      n &&
        (function p() {
          const g = V(e)
          !s || (g.x === s.x && g.y === s.y && g.width === s.width && g.height === s.height) || r(), (s = g), (c = requestAnimationFrame(p))
        })(),
      r(),
      () => {
        var p
        f.forEach((g) => {
          l && g.removeEventListener('scroll', r), o && g.removeEventListener('resize', r)
        }),
          (p = d) == null || p.disconnect(),
          (d = null),
          n && cancelAnimationFrame(c)
      }
    )
  }
  const Je = (e, t, r) => {
      const i = new Map(),
        l = { platform: Ye, ...r },
        o = { ...l.platform, _c: i }
      return Me(e, t, { ...l, platform: o })
    },
    Ie = 'hn-new-changelog-seen',
    Ke = () => {
      try {
        localStorage && localStorage.setItem(Ie, 'true')
      } catch {
        return null
      }
    },
    J = () => {
      try {
        return localStorage ? localStorage.getItem(Ie) : null
      } catch {
        return null
      }
    },
    _e = (e) => {
      switch (e) {
        case 'top':
          return 'bottom'
        case 'bottom':
          return 'top'
        case 'left':
          return 'right'
        case 'right':
          return 'left'
        default:
          return e
      }
    },
    u = {
      overlay: 'widget-overlay',
      wrapper: 'widget-wrapper',
      iframe: 'widget-iframe',
      customSelectorIndicator: 'widget-custom-selector--indicate',
      fallbackTriggerWrapper: 'widget-trigger-wrapper',
      fallbackTriggerIframe: 'widget-trigger-iframe',
      fallbackTrigger: 'widget-trigger',
      overlayBlurred: 'widget-overlay--blurred',
      modal: 'widget-type--modal',
      popover: 'widget-type--popover',
      popoverExpanded: 'widget-type--popover--expanded',
      modelRight: 'widget-type--modal--right',
      modelLeft: 'widget-type--modal--left',
      fallbackTriggerIndicate: 'widget-trigger-wrapper--indicate',
      fallbackTriggerText: 'widget-trigger-wrapper--text',
      fallbackTriggerCard: 'widget-trigger-wrapper--card',
      fallbackTriggerRight: 'widget-trigger--right',
      fallbackTriggerLeft: 'widget-trigger--left',
      bodyModalOpen: 'body-modal_open',
      overlayOpen: 'widget-overlay_open',
      open: 'widget_open'
    },
    Ce = {
      widget: `
    .widget-overlay {
      pointer-events: auto;
      z-index: 9999999;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: none;
      background-color: transparent;
      transition: background-color 200ms ease, backdrop-filter 200ms ease;
    }
    .widget-overlay_open.widget-overlay--blurred {
      background-color: hsl(0deg, 0%, 0%, 0.2);
      backdrop-filter: blur(4px);
    }

    .widget-wrapper {
      pointer-events: none;

      z-index: 9999999;
      
      opacity: 0;

      overflow: clip;
      
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

      transition: transform 200ms cubic-bezier(0, 1.2, 1, 1), opacity 50ms ease, width 100ms ease, height 100ms ease, max-height 100ms ease;
    }
    
    .widget-iframe {
      all: unset;
      width: 100%;
      min-height: 100%;
    }

    .widget-type--popover {
      transform: scale(0);
      transform-origin: center;

      position: absolute;

      border-radius: 0.75rem;

      width: 400px;
      height: min(680px, 100% - 100px);
      max-height: 680px;
      min-height: 80px;
      overflow-y: hidden;
    }

    .widget-type--popover--expanded {
      width: 440px;
    }

    .widget-type--modal {
      position: fixed;

      top: 0;
      width: 460px;
      height: 100vh;
      z-index: 99999999;
    }

    @media (max-width: 640px) {
      .widget-type--modal {
        width: 100%;
        height: 100%;
      }
      .widget-type--popover {
        width: calc(100% - 20px);
      }
    }

    .widget-type--modal--left {
      transform: translateX(-100%);
      transform-origin: left;
      left: 0;
    }
    .widget-type--modal--right {
      transform: translateX(100%);
      transform-origin: right;
      right: 0;
    }
    
    .widget-overlay_open {
      display: block;
    }
    .widget-wrapper.widget_open {
      pointer-events: auto;
      transform: none;
      opacity: 1;
    }
    
    .widget-trigger-wrapper {
      z-index: 99999999;
      position: fixed;
      bottom: 15px;
      border-radius: 16px;
      width: 42px;
      height: 42px;
      
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

      transition: width 200ms ease, height 200ms ease;
    }
    .widget-trigger-wrapper--card {
      width: 360px;
      height: 120px;
    }
    
    .widget-custom-selector--indicate { position: relative; }
    .widget-custom-selector--indicate::before, .widget-trigger-wrapper.widget-trigger-wrapper--indicate::before {
      content: "";
      position: absolute;
      top: 0;
      right: 0;
      width: 12px;
      height: 12px;
      background-color: hsla(0deg, 0%, 0%, 80%);
      border-radius: 9999px;
      animation: ping 1150ms ease infinite;
    }
    .widget-trigger-wrapper.widget-trigger-wrapper--indicate.widget_open::before {
      display: none;
    }
    .widget-trigger-wrapper.widget_open {
      pointer-events: auto;
    }
    .widget-trigger-iframe {
      all: unset;
      width: 100%;
      height: 100%;
    }
    .widget-trigger--left { left: 15px; }
    .widget-trigger--right { right: 15px; }

    .body-modal_open {
      pointer-events: none;
      overflow: hidden;
    }

    @keyframes ping {
      75%, 100% {
        transform: scale(1.2);
        opacity: 0;
      }
    }
  `,
      fallbackTrigger: `
    html, body {
      box-sizing: border-box;
      margin: 0;
      font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      font-weight: 500;
      font-size: 14px;
    }

    p { margin: 0; }

    .trigger-card-wrapper {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      justify-content: space-between;
    }
    .trigger-card-main {
      flex: 1 1 0;
      padding: 10px;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      justify-content: center;
      gap: 10px;
    }
    .trigger-card-title {
      font-size: 20px;
      font-weight: 700;
    }
    .trigger-card-description {
      font-size: 12px;
      opacity: 75%;  
    }
    .trigger-card-action {
      padding: 10px 0px;
      text-align: center;
      background: black;
      color: white;
    }

    button {
      all: unset;

      box-sizing: border-box;
      cursor: pointer;

      overflow: hidden;

      width: 100%;
      height: 100%;

      display: grid;
      place-items: center;

      background-color: hsla(220deg, 60%, 95%, 1);
      color: hsla(220deg, 60%, 40%, 0.8);

      line-height: 1;

      border: solid 1px hsla(220deg, 0%, 0%, 0.1);
      border-radius: 16px;

      transition-property: color, background-color, opacity;
      transition-timing-function: ease;
      transition-duration: 200ms;
    }

    button:hover {
      opacity: 80%;
    }
  `
    },
    Qe = 'https://widgets-v3.hellonext.co',
    fe = {
      triggerOpenIcon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
</svg>
`,
      triggerCloseIcon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
</svg>
`
    },
    q = () => Math.random().toString(36).slice(2)
  class Ze {
    constructor(t) {
      W(this, 'config')
      W(this, 'overlayRef')
      W(this, 'wrapperRef')
      W(this, 'iFrameRef')
      W(this, 'fallbackTriggerWrapperRef')
      W(this, 'fallbackTriggerIFrameRef')
      W(this, 'fallbackTriggerRef')
      W(this, 'stylesRef')
      W(this, 'fallbackTriggerStylesRef')
      W(this, 'isOpen')
      W(this, 'initialized')
      W(this, 'autoUpdateCleanup')
      ;(this.config = {
        id: Math.random().toString(32),
        selector: t.selector || void 0,
        type: t.type || 'modal',
        placement: t.placement || 'right',
        openFrom: t.openFrom || 'auto',
        neverExpand: t.neverExpand || !1,
        theme: t.theme || 'light',
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
      }),
        (this.isOpen = !1),
        (this.initialized = !1),
        (this.autoUpdateCleanup = () => {}),
        window.matchMedia('(max-width: 640px)').matches &&
          this.config.type === 'popover' &&
          document.querySelector(this.config.selector) &&
          (this.config.type = 'modal')
    }
    init() {
      const t = document.querySelector(this.config.selector)
      if (t) t.setAttribute('disabled', 'true')
      else {
        ;(this.fallbackTriggerWrapperRef = document.createElement('div')),
          this.fallbackTriggerWrapperRef.setAttribute('id', q()),
          (this.fallbackTriggerWrapperRef.hidden = !0),
          this.fallbackTriggerWrapperRef.classList.add(
            u.fallbackTriggerWrapper,
            this.config.placement === 'left' ? u.fallbackTriggerLeft : u.fallbackTriggerRight
          ),
          this.config.triggerText &&
            (this.fallbackTriggerWrapperRef.style.width = `calc(${this.config.triggerText.length.toString()}ch + 42px)`),
          (this.fallbackTriggerIFrameRef = document.createElement('iframe')),
          this.fallbackTriggerIFrameRef.setAttribute('id', q()),
          this.fallbackTriggerIFrameRef.setAttribute('src', 'about:blank'),
          this.fallbackTriggerIFrameRef.classList.add(u.fallbackTriggerIframe),
          (this.fallbackTriggerStylesRef = document.createElement('style')),
          (this.fallbackTriggerStylesRef.innerHTML = Ce.fallbackTrigger),
          (this.fallbackTriggerRef = document.createElement('button')),
          this.fallbackTriggerRef.setAttribute('id', q()),
          this.fallbackTriggerRef.setAttribute('type', 'button'),
          this.fallbackTriggerRef.setAttribute('disabled', 'true'),
          this.fallbackTriggerRef.classList.add(u.fallbackTrigger),
          (this.fallbackTriggerRef.innerHTML = this.config.triggerText ?? fe.triggerOpenIcon),
          this.fallbackTriggerRef.addEventListener('click', () => this.toggle(this.fallbackTriggerWrapperRef)),
          this.fallbackTriggerWrapperRef.appendChild(this.fallbackTriggerIFrameRef),
          document.body.appendChild(this.fallbackTriggerWrapperRef)
        const i = () => {
          this.fallbackTriggerIFrameRef.contentDocument &&
            (this.fallbackTriggerIFrameRef.contentDocument.head.appendChild(this.fallbackTriggerStylesRef),
            this.fallbackTriggerIFrameRef.contentDocument.body.appendChild(this.fallbackTriggerRef))
        }
        ;(this.fallbackTriggerIFrameRef.onload = i), i()
      }
      ;(this.overlayRef = document.createElement('div')),
        this.overlayRef.setAttribute('id', q()),
        this.overlayRef.classList.add(u.overlay),
        this.config.type === 'modal' && this.overlayRef.classList.add(u.overlayBlurred),
        this.overlayRef.addEventListener('click', () => this.close()),
        (this.wrapperRef = document.createElement('div')),
        this.wrapperRef.setAttribute('id', q()),
        this.wrapperRef.classList.add(u.wrapper, u[this.config.type]),
        this.config.type === 'modal' && this.wrapperRef.classList.add(this.config.openFrom === 'left' ? u.modelLeft : u.modelRight),
        (this.iFrameRef = document.createElement('iframe')),
        this.iFrameRef.setAttribute('id', q()),
        this.iFrameRef.classList.add(u.iframe),
        this.iFrameRef.setAttribute('title', 'featureOS widget'),
        this.iFrameRef.setAttribute('src', Qe),
        this.iFrameRef.setAttribute('referrerPolicy', 'origin'),
        this.iFrameRef.setAttribute(
          'sandbox',
          'allow-scripts allow-forms allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-top-navigation allow-top-navigation-by-user-activation allow-modals'
        ),
        (this.stylesRef = document.createElement('style')),
        (this.stylesRef.innerHTML = Ce.widget),
        this.wrapperRef.appendChild(this.stylesRef),
        this.wrapperRef.appendChild(this.iFrameRef),
        document.body.appendChild(this.overlayRef),
        document.body.appendChild(this.wrapperRef),
        window.addEventListener('message', (i) => {
          this.handleNewMessagesToHost(i)
        })
      const r = document.querySelectorAll(this.config.selector)
      document.addEventListener('click', (i) => {
        var o, a
        let l = !1
        for (let n = 0; n < r.length; n++)
          if (i.target === r[n] || r[n].contains(i.target)) {
            l = !0
            break
          }
        if (!l) {
          const n = (a = (o = i.target) == null ? void 0 : o.dataset) == null ? void 0 : a.helposId
          if (!n) return
          this.postMessageToServer({ action: 'SET_ARTICLE_ID', payload: { articleId: n } }), this.open(i.target)
          return
        }
        this.postMessageToServer({ action: 'SET_ARTICLE_ID', payload: { articleId: null, page: 'home' } }), this.open(i.target)
      })
    }
    updateConfig(t) {
      var r, i, l, o, a, n, f, c
      ;(r = this.overlayRef) == null || r.remove(),
        (i = this.wrapperRef) == null || i.remove(),
        (l = this.iFrameRef) == null || l.remove(),
        (o = this.fallbackTriggerWrapperRef) == null || o.remove(),
        (a = this.fallbackTriggerIFrameRef) == null || a.remove(),
        (n = this.fallbackTriggerRef) == null || n.remove(),
        (f = this.stylesRef) == null || f.remove(),
        (c = this.fallbackTriggerStylesRef) == null || c.remove(),
        (this.config = { ...this.config, ...t }),
        this.init()
    }
    open(t = null) {
      this.initialized &&
        (document.addEventListener('keydown', (r) => {
          r.key === 'Escape' && this.close()
        }),
        (this.isOpen = !0),
        this.postMessageToServer({ action: 'open', payload: {} }),
        this.overlayRef.classList.add(u.overlayOpen),
        this.wrapperRef.classList.add(u.open),
        this.config.type === 'modal' && document.body.classList.add(u.bodyModalOpen),
        this.config.type === 'popover' &&
          (this.autoUpdateCleanup(),
          this.updatePopoverPosition(t || this.fallbackTriggerWrapperRef || document.querySelector(this.config.selector))),
        this.fallbackTriggerRef &&
          (this.fallbackTriggerWrapperRef.classList.add(u.open),
          this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerCard),
          this.config.triggerText || (this.fallbackTriggerRef.innerHTML = fe.triggerCloseIcon)))
    }
    close() {
      document.removeEventListener('keydown', () => {}),
        (this.isOpen = !1),
        this.autoUpdateCleanup(),
        this.postMessageToServer({ action: 'close', payload: {} }),
        this.overlayRef.classList.remove(u.overlayOpen),
        this.wrapperRef.classList.remove(u.open),
        this.config.type === 'modal' && document.body.classList.remove(u.bodyModalOpen),
        this.fallbackTriggerRef &&
          (this.fallbackTriggerWrapperRef.classList.remove(u.open),
          this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerCard),
          this.config.triggerText || (this.fallbackTriggerRef.innerHTML = fe.triggerOpenIcon))
    }
    toggle(t = null) {
      this.isOpen ? this.close() : this.open(t)
    }
    updatePopoverPosition(t) {
      var i, l
      const r = { middleware: [Ue({ padding: 10 }), Ve(10)] }
      this.config.openFrom === 'auto'
        ? (i = r == null ? void 0 : r.middleware) == null || i.push(He())
        : ((r.placement = this.config.openFrom), (l = r == null ? void 0 : r.middleware) == null || l.push(Be())),
        (this.autoUpdateCleanup = Ge(t, this.wrapperRef, () =>
          Je(t, this.wrapperRef, r).then(({ x: o, y: a, placement: n }) => {
            Object.assign(this.wrapperRef.style, { left: `${o}px`, top: `${a}px`, transformOrigin: `${_e(n)}` })
          })
        ))
    }
    postMessageToServer(t) {
      var r, i, l
      ;(l = (i = (r = this.iFrameRef) == null ? void 0 : r.contentWindow) == null ? void 0 : i.postMessage) == null ||
        l.call(i, JSON.stringify(t), '*')
    }
    handleNewMessagesToHost(t) {
      var r, i, l
      if ((t.preventDefault(), t.data && typeof t.data == 'string'))
        try {
          const o = JSON.parse(t.data)
          switch (o.action) {
            case 'init':
              this.postMessageToServer({ action: 'config', payload: { ...this.config, open: this.isOpen } })
              break
            case 'initialized':
              this.initialized = !0
              const a = document.querySelector(this.config.selector)
              a && a.removeAttribute('disabled'),
                this.fallbackTriggerRef && this.fallbackTriggerRef.removeAttribute('disabled'),
                (i = (r = this.config).onInitialized) == null || i.call(r)
              break
            case 'trigger-ready':
              o.payload.for === this.config.id &&
                this.fallbackTriggerWrapperRef &&
                this.fallbackTriggerRef &&
                (Object.assign(this.fallbackTriggerRef.style, o.payload.styles), (this.fallbackTriggerWrapperRef.hidden = !1))
              break
            case 'close':
              o.payload.for === this.config.id && this.close()
              break
            case 'change-popover-size':
              o.payload.for === this.config.id &&
                this.config.type === 'popover' &&
                !this.config.neverExpand &&
                (o.payload.size === 'expanded' && this.wrapperRef.classList.add(u.popoverExpanded),
                o.payload.size === 'normal' && this.wrapperRef.classList.remove(u.popoverExpanded))
              break
            case 'set-changelogs-seen':
              o.payload.for === this.config.id &&
                (Ke(),
                (l = document.querySelector(this.config.selector)) == null || l.classList.remove(u.customSelectorIndicator),
                this.fallbackTriggerWrapperRef && this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerIndicate))
              break
            case 'indicate':
              if (o.payload.for === this.config.id && o.payload.indicate) {
                if (this.config.onNewChangelogIndicator) J() || this.config.onNewChangelogIndicator()
                else if (this.config.enableIndicator) {
                  const n = document.querySelector(this.config.selector)
                  n && (J() ? n.classList.remove(u.customSelectorIndicator) : n.classList.add(u.customSelectorIndicator))
                }
                this.config.enableIndicator &&
                  this.fallbackTriggerRef &&
                  (J()
                    ? this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerIndicate)
                    : this.fallbackTriggerWrapperRef.classList.add(u.fallbackTriggerIndicate))
              }
              break
            case 'show-latest-changelog':
              if (o.payload.for === this.config.id && this.fallbackTriggerRef && !this.config.triggerText && !this.isOpen && !J()) {
                const n = o.payload.changelog
                n &&
                  (this.fallbackTriggerWrapperRef.classList.add(u.fallbackTriggerCard),
                  this.fallbackTriggerWrapperRef.classList.remove(u.fallbackTriggerIndicate),
                  (this.fallbackTriggerRef.innerHTML = `
                  <div class="trigger-card-wrapper">
                    <div class="trigger-card-main">
                      <p class="trigger-card-title">${n.title}</p>
                      <p class="trigger-card-description">${n.published_at}</p>
                    </div>
                    <div class="trigger-card-action">View update!</div>
                  </div>
                `))
              }
              break
            default:
              break
          }
        } catch (o) {
          console.error(o)
        }
    }
  }
  return Ze
})
