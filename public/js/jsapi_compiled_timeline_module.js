var gvjs_c4 = "focusedTracks",
    gvjs_Qpa = "px;overflow-x:",
    gvjs_d4 = "scrollArea",
    gvjs_Rpa = "selectedTracks";
gvjs_rB.prototype.IC = gvjs_V(56, function() {
    return 0
});
gvjs_PB.prototype.IC = gvjs_V(55, function() {
    if (null != this.Kw)
        return this.Kw;
    var a = gvjs_dh(gvjs_b);
    a.style.cssText = "overflow:auto;position:absolute;top:0;width:100px;height:100px";
    var b = gvjs_dh(gvjs_b);
    gvjs_Cz(b, "200px", "200px");
    a.appendChild(b);
    document.body.appendChild(a);
    b = a.offsetWidth - a.clientWidth;
    gvjs_kh(a);
    return this.Kw = b
});
gvjs_TB.prototype.IC = gvjs_V(54, function() {
    if (null != this.Kw)
        return this.Kw;
    var a = gvjs_4(gvjs_b, {
        style: "width:100px;height:100px;overflow:scroll;position:absolute;visibility:hidden;"
    });
    this.jF.appendChild(a);
    this.jF.style.display = gvjs_xb;
    this.Kw = a.offsetWidth - a.clientWidth;
    this.jF.style.display = gvjs_f;
    gvjs_kh(a);
    return this.Kw
});
gvjs_rB.prototype.AD = gvjs_V(47, function() {
    return {
        append: [],
        events: [gvjs_uB(this)]
    }
});
gvjs_PB.prototype.AD = gvjs_V(46, function(a, b, c, d, e, f, g) {
    b = "height:" + c + gvjs_Qpa + (f ? gvjs_Gw : gvjs_0u) + ";overflow-y:" + (g ? gvjs_Gw : gvjs_0u) + ";width:" + b + "px;position: absolute;top:0;left:0;";
    d = this.bO(d, e);
    if (gvjs_ne(a))
        for (e = 0,
            c = a.length; e < c; e++)
            this.appendChild(d, a[e]);
    else
        this.appendChild(d, a);
    a = gvjs_4(gvjs_b, {
        style: b
    }, d.j());
    this.container.appendChild(a);
    return {
        append: [],
        events: [d.j()]
    }
});
gvjs_TB.prototype.AD = gvjs_V(45, function(a, b, c, d, e, f, g) {
    b = gvjs_4(gvjs_b, {
        style: "height:" + c + gvjs_Qpa + (f ? gvjs_Gw : gvjs_0u) + ";overflow-y:" + (g ? gvjs_Gw : gvjs_0u) + ";width:" + b + "px;"
    });
    b = new gvjs_QA(b);
    if (gvjs_ne(a))
        for (c = 0,
            d = a.length; c < d; c++)
            this.appendChild(b, a[c]);
    else
        this.appendChild(b, a);
    return {
        append: [b.j()],
        events: []
    }
});

function gvjs_Spa(a, b, c) {
    return gvjs_Hy(a, b, !0, void 0, c)
}

function gvjs_e4(a) {
    this.Yc = new gvjs_Xi(a)
}
gvjs_e4.prototype.format = function(a, b) {
    return [this.Yc.format(a), "-", this.Yc.format(b)].join(" ")
};
gvjs_e4.prototype.tv = function() {
    throw Error("Custom durations not supported");
};

function gvjs_Tpa(a, b) {
    var c = this;
    this.ms = a.rect;
    this.Qm = !0;
    this.dba = [];
    this.Ora = gvjs_9z("#b7b7b7", 1);
    var d = a.background.rka,
        e = a.background.brush.fill,
        f = gvjs_uj(gvjs_1z(gvjs_vj(e), .1));
    this.Pra = gvjs_9z(f, 1);
    this.Qra = gvjs_9z(d ? e : f, 1);
    this.Aga = b;
    this.sya = gvjs_8z(e, 1);
    this.tya = gvjs_8z(d ? f : e, 1);
    this.Lga = [];
    this.Mga = [];
    this.Jua = gvjs_9z("#9a9a9a", 1);
    this.Zya = a.xsa;
    gvjs_u(a.yF, function(g, h) {
        0 < h && c.dba.push(g.rect.top);
        c.Mga.push(g.rect);
        g.label && c.Lga.push(g.label)
    })
}
gvjs_Tpa.prototype.draw = function(a) {
    var b = this;
    if (this.Qm) {
        var c = a.Oa(),
            d = this.ms;
        gvjs_u(this.Mga, function(g, h) {
            var k = c.Bl(g.left, g.top, g.width, g.height, h % 2 ? b.tya : b.sya);
            a.we(k, null, {
                type: gvjs_Pu,
                data: {
                    grid: gvjs_Qp,
                    index: h
                }
            }, gvjs_Pu);
            k = 0;
            for (var l = b.Aga.length; k < l; k++) {
                var m = b.Aga[k];
                m < b.Zya || (m = gvjs_ZA(c, m, g.top, m, g.top + g.height, h % 2 ? b.Qra : b.Pra),
                    a.we(m, null, {
                        type: gvjs_Pu,
                        data: {
                            grid: "vert",
                            index: h
                        }
                    }, gvjs_Pu))
            }
        });
        var e = d.left,
            f = e + d.width;
        gvjs_u(this.dba, function(g, h) {
            g = gvjs_ZA(c, e, g, f, g, b.Ora);
            a.we(g, null, {
                type: gvjs_Pu,
                data: {
                    grid: "horiz",
                    index: h
                }
            }, gvjs_Pu)
        });
        gvjs_u(this.Lga, function(g, h) {
            var k = g.anchor ? g.anchor : {
                x: 0,
                y: 0
            };
            g = c.by(g.text, k.x, k.y, g.lines[0].length, g.ld, g.Pc, g.ja);
            a.we(g, null, {
                type: gvjs_Pu,
                data: {
                    grid: gvjs_8c,
                    index: h
                }
            }, gvjs_Pu)
        });
        d = c.Bl(d.left, d.top, d.width, d.height, this.Jua);
        a.we(d, null, {
            type: gvjs_Pu,
            data: {
                grid: "outer",
                index: null
            }
        }, gvjs_Pu);
        this.Qm = !1
    }
};

function gvjs_f4(a, b, c, d, e, f, g) {
    this.E7 = c;
    this.hN = null;
    this.Y1 = this.oN = !1;
    this.eb = d;
    this.$W = a.RN;
    this.ua = a;
    this.Qm = !0;
    this.Qb = a.G7;
    this.HQ = null;
    this.Ci = gvjs_lt;
    this.zn = a.rect;
    this.Ln = !1;
    this.Qn = this.uL = null;
    this.Kga = b;
    this.ona = e;
    this.H9 = g;
    this.ana = f
}
gvjs_f4.prototype.draw = function(a, b) {
    if (this.Qm) {
        var c = a.Oa(),
            d = new gvjs_3(this.$W || void 0);
        if (this.oN) {
            var e = gvjs_vj(d.fill);
            d.Te(gvjs_uj(gvjs_2z(e, .3)))
        }
        this.Y1 && (d.rd(gvjs_kr),
            d.hl(3));
        e = c.Bl(this.zn.left, this.zn.top, this.zn.width, this.zn.height, d);
        var f = {
                type: gvjs_wb,
                data: {
                    EU: this.Kga,
                    iN: this.E7
                }
            },
            g = this.Qb,
            h = null,
            k = null;
        g && g.text && (h = g.anchor ? g.anchor : {
                x: 0,
                y: 0
            },
            k = gvjs_x(g.ja),
            this.$W.text && (k.color = this.$W.text),
            this.oN && (d = gvjs_vj(d.fill),
                k.color = gvjs_uj(gvjs_4z(d, [
                    [0, 0, 0],
                    [255, 255, 255]
                ]))),
            h = c.by(g.text, h.x, h.y, 100, g.ld, g.Pc, k),
            k = {
                type: gvjs_wb,
                data: {
                    EU: this.Kga,
                    iN: this.E7,
                    label: 1
                }
            });
        this.uL && (gvjs_u(this.uL, function(l) {
                l && c.Re(l)
            }),
            this.uL = null);
        this.Ci == gvjs_lt ? (a.we(e, this.hN, f, gvjs_lt),
            h && k && a.we(h, this.HQ, k, gvjs_lt),
            this.hN = e,
            this.HQ = h) : (this.hN && gvjs_6(this.hN, !1),
            this.HQ && gvjs_6(this.HQ, !1),
            a.$n(e, f, this.Ci),
            this.uL = [e],
            h && k && (a.$n(h, k, this.Ci),
                this.uL.push(h)));
        gvjs_Upa(this, a, b);
        this.Qm = !1
    }
};

function gvjs_Upa(a, b, c) {
    var d = b.Oa();
    if (!a.Ln && a.Qn)
        d.Re(a.Qn),
        a.Qn = null;
    else if (a.Ln && !a.Qn) {
        var e = new gvjs_B(0, a.eb.width, a.eb.height, 0),
            f = a.ua.Dp,
            g = gvjs_x(f);
        g.bold = !0;
        var h = null;
        h = new gvjs_z(a.ua.rect.left + a.ua.rect.width / 2, a.ua.rect.top + a.ua.rect.height / 2);
        var k = gvjs_ez(h, new gvjs_z(-1, 1));
        if (a.ua.tooltip && a.ua.$f)
            h = {
                html: gvjs_5f(gvjs_Ob, {
                    "class": gvjs_Nu
                }, gvjs_OA(a.ua.tooltip.content)),
                hO: a.ua.tooltip.Nh,
                pivot: k,
                anchor: h,
                HG: e,
                spacing: 20,
                margin: 5
            };
        else {
            var l = new Date(a.ua.Pt.start),
                m = new Date(a.ua.Pt.end),
                n = gvjs_7S(gvjs_$S(a.ona)),
                p = a.H9 && new gvjs_e4(a.H9),
                q = a.ua.name,
                r = a.ua.uya;
            0 === q.length && (q = r,
                r = null);
            var t = {
                entries: []
            };
            a.ua.tooltip ? t.entries.push(gvjs_hG(a.ua.tooltip.content, a.ua.Dp)) : (t.entries.push(gvjs_hG(q, g), gvjs_jG(), gvjs_hG((p || n).format(l, m), f, r, g)),
                a.ana && t.entries.push(gvjs_hG(n.tv(l, m), f, "Duration", g)));
            h = gvjs_kG(t, function(u, v) {
                return d.me(u, v)
            }, !1, h, e, k, void 0, a.ua.$f)
        }
        a.ua.$f ? a.Qn = gvjs_IG(h, c.getContainer()) : (c = gvjs_KG(h, d).j(),
            b.we(c, a.Qn, {
                type: gvjs_Pd,
                data: null
            }, gvjs_Pd),
            a.Qn = c);
        gvjs_C(a.Qn, gvjs_lw, gvjs_f)
    }
}
gvjs_f4.prototype.nm = function(a, b) {
    switch (a.type) {
        case gvjs_st:
            b != this.oN && (this.oN = b,
                this.Qm = !0);
            break;
        case "setLayer":
            this.nL(b ? a.data.Kta : gvjs_lt);
            break;
        case gvjs_Uw:
            b != this.Ln && (this.Ln = b,
                this.Qm = !0);
            break;
        case "outlined":
            b != this.Y1 && (this.Y1 = b,
                this.Qm = !0)
    }
};
gvjs_f4.prototype.nL = function(a) {
    a != this.Ci && (this.Ci = a,
        this.Qm = !0)
};

function gvjs_Vpa(a, b, c, d, e, f) {
    var g = this;
    this.Mb = [];
    gvjs_u(a.Mb, function(h, k) {
        g.Mb.push(new gvjs_f4(h, b, k, c, d, e, f))
    })
}
gvjs_Vpa.prototype.draw = function(a, b) {
    gvjs_u(this.Mb, function(c) {
        c.draw(a, b)
    })
};

function gvjs_g4(a, b) {
    this.FU = [];
    for (var c = Infinity, d = -Infinity, e = 0, f = a.yF.length; e < f; e++) {
        for (var g = a.yF[e], h = 0, k = g.Mb.length; h < k; h++) {
            var l = g.Mb[h].Pt;
            c > l.start && (c = l.start);
            d < l.end && (d = l.end)
        }
        g = new gvjs_Vpa(g, e, a.size, a.nY, a.$ma, a.nya);
        this.FU.push(g)
    }
    this.Gka = b;
    this.QC = new gvjs_Tpa(a, b.vh);
    this.Ym = !0;
    this.ga = a.rect.width;
    this.kfa = a.rect.height;
    this.oha = a.size.height - 50;
    this.q3 = [];
    this.sxa = !a.cna
}
gvjs_g4.prototype.draw = function(a, b) {
    if (this.Ym && this.oha <= this.kfa) {
        var c = gvjs_Wpa(a, this.ga, this.oha, this.kfa),
            d = c.append;
        c = c.events;
        for (var e = 0, f = d.length; e < f; e++)
            a.$n(d[e], {
                type: gvjs_d4,
                data: null
            }, gvjs_d4);
        d = 0;
        for (e = c.length; d < e; d++)
            this.q3.push(c[d])
    }
    this.QC.draw(a);
    c = 0;
    for (d = this.FU.length; c < d; c++)
        this.FU[c].draw(a, b);
    var g = a.Oa();
    this.Gka.draw(function(h, k) {
        return g.Wl(h, k)
    }, g.by.bind(g), gvjs_Xpa(a));
    this.Ym = !1
};
gvjs_g4.prototype.nm = function(a, b, c) {
    if (b.type != gvjs_Uw || !this.sxa)
        switch (a.type) {
            case gvjs_wb:
                this.FU[a.data.EU].Mb[a.data.iN].nm(b, c)
        }
};

function gvjs_Ypa(a, b, c, d) {
    this.F = a;
    this.Hi = b;
    this.PQ = null;
    this.ea = c;
    this.Bk = d
}
gvjs_ = gvjs_Ypa.prototype;
gvjs_.draw = function(a, b, c) {
    this.PQ = {};
    var d = this.F;
    d.clear();
    for (var e = d.Lm(a.size.width, a.size.height), f = 0; f < gvjs_Zpa.length; f++) {
        var g = gvjs_Zpa[f],
            h = d.Sa();
        d.appendChild(e, h);
        this.PQ[g] = h
    }
    this.da = new gvjs_g4(a, c);
    gvjs_h4(this, b, !0);
    this.da.draw(this, this.Hi);
    gvjs__pa(this, e);
    a = 0;
    for (b = this.da.q3.length; a < b; a++)
        gvjs__pa(this, this.da.q3[a])
};

function gvjs_h4(a, b, c) {
    for (var d = 0; d < b.length; d++)
        for (var e = b[d], f = e.targets, g = 0; g < f.length; g++)
            a.da.nm(f[g], e.effect, c)
}
gvjs_.refresh = function(a) {
    gvjs_h4(this, a.Ps, !1);
    gvjs_h4(this, a.Os, !0);
    this.da.draw(this, this.Hi)
};
gvjs_.Oa = function() {
    return this.F
};
gvjs_.we = function(a, b, c, d) {
    null != b ? this.e3(a, b) : this.$n(a, c, d)
};
gvjs_.$n = function(a, b, c) {
    this.F.appendChild(this.PQ[c], a);
    b = gvjs_Hi(b);
    this.F.kp(a, b)
};
gvjs_.e3 = function(a, b) {
    gvjs_qh(b).replaceChild(a, b);
    b = this.F.xv(b);
    this.F.kp(a, b)
};

function gvjs_Xpa(a) {
    return function(b, c, d) {
        return a.we(b, c, {
            type: "axis",
            data: d
        }, "axis")
    }
}

function gvjs_Wpa(a, b, c, d) {
    for (var e = [gvjs_Pu, "tracks", gvjs_lt, gvjs_Rpa, gvjs_c4], f = [], g = 0, h = e.length; g < h; g++)
        f.push(a.PQ[e[g]]);
    return a.F.AD(f, b, c, b, d, !1, !0)
}

function gvjs__pa(a, b) {
    var c = a.F;
    c.ic(b, gvjs_3t, gvjs_Lz);
    c.ic(b, gvjs_ld, a.Bk(a.Hv.bind(a, gvjs_9u)));
    c.ic(b, gvjs_kd, a.Bk(a.Hv.bind(a, gvjs_$u)));
    c.ic(b, gvjs_Wt, a.Bk(a.Hv.bind(a, gvjs_Wt)))
}
gvjs_.Hv = function(a, b) {
    b.stopPropagation && b.stopPropagation();
    var c = this.F.xv(b.target);
    c != gvjs_Bs && (c = JSON.parse(c),
        a == gvjs_Wt && b.shiftKey && (a = "shiftclick"),
        this.ea(c, a))
};
var gvjs_Zpa = [gvjs_Pu, "axis", "tracks", gvjs_lt, gvjs_Rpa, gvjs_c4, gvjs_d4, gvjs_Pd];

function gvjs_i4(a, b, c, d, e) {
    this.Z = a;
    this.m = b;
    this.Zd = c;
    this.jwa = e;
    this.eb = d;
    this.gb = (new gvjs_3l(b)).Ac(a);
    this.c0 = gvjs_ry(b, "timeline.rowLabelStyle", {
        bb: gvjs_2r,
        fontSize: 13,
        color: "#4d4d4d"
    });
    this.zG = gvjs_ry(b, "timeline.barLabelStyle", {
        bb: gvjs_2r,
        fontSize: 12,
        color: gvjs_ca
    });
    this.gW = 1.916 * this.zG.fontSize;
    this.H7 = .75 * this.zG.fontSize;
    this.Jla = {};
    this.q5 = 1 * this.c0.fontSize;
    this.Nga = .583 * this.zG.fontSize;
    this.Oga = .75 * this.zG.fontSize;
    this.Nta = gvjs_K(this.m, ["nightingale", gvjs_Fv], !0);
    this.Kb = gvjs_0pa(this);
    this.aE = null;
    !this.Kb && this.Nta && (this.aE = new gvjs__R(!1, null));
    this.Kb || this.aE || (this.Kb = gvjs_MF);
    a = this.m.fa("timeline.singleColor");
    typeof a === gvjs_l && (a = {
        color: a
    });
    this.Vfa = a;
    this.s8 = gvjs_K(this.m, "timeline.colorByRowLabel", !1);
    this.axis = null;
    this.WS = 0
}
gvjs_i4.prototype.$g = function() {
    var a = this.gb.WH.index,
        b = this.Z.Sj(this.gb.vL.index).min,
        c = this.Z.Sj(a).max;
    a = this.m.fa("hAxis.minValue", b);
    var d = this.m.fa("hAxis.maxValue", c);
    a = Math.min(b, a);
    c = Math.max(c, d);
    d = this.Z.ca();
    var e = {};
    b = [];
    for (var f = new Set, g = gvjs_K(this.m, "timeline.groupByRowLabel", !0), h = gvjs_K(this.m, gvjs_nx, !0), k = gvjs_ry(this.m, gvjs_px), l = 0; l < d; l++) {
        var m = l;
        var n = this.Z;
        var p = this.gb,
            q = n.getValue(m, p.qw.index),
            r = null === p.tt ? "" : n.getValue(m, p.tt.index),
            t = n.getValue(m, p.vL.index),
            u = n.getValue(m, p.WH.index),
            v = "",
            w = null;
        if (null == t || null == u)
            throw Error("Missing value in row " + m + ".");
        p.tt && p.tt.Nf.style && (v = n.getValue(m, p.tt.Nf.style));
        p.tt && p.tt.Nf.tooltip && (p = p.tt.Nf.tooltip,
            n = n.getStringValue(m, p),
            null != n && (w = {
                Nh: !(!this.Z.getProperty(m, p, gvjs_av) && !this.Z.Bd(p, gvjs_av)),
                content: n
            }));
        if (t > u)
            throw Error("Invalid data at row #" + m + ": start(" + t + ") > end(" + u + ").");
        g && gvjs_Ze(e, q) ? n = gvjs_Sy(e, q) : (n = {
                name: q,
                label: null,
                vJ: [],
                Mb: [],
                rect: new gvjs_5(0, 0, 0, 0)
            },
            gvjs_Ze(e, q) || gvjs_Ry(e, q, n),
            b.push(n));
        m = {
            name: r,
            uya: q,
            G7: null,
            RN: null,
            rect: new gvjs_5(0, 0, 0, 0),
            Pt: {
                start: t,
                end: u
            },
            row: m,
            oga: v,
            tooltip: w,
            $f: h,
            Dp: k
        };
        n.Mb.push(m);
        m = !m.name || this.s8 ? n.name : m.name;
        f.add(m);
        null != this.aE && this.aE.Au(m)
    }
    gvjs_1pa(b);
    d = gvjs_2pa(this, b);
    e = 0;
    0 < b.length && (e = b[b.length - 1].rect,
        e = e.top + e.height);
    f = Math.min(e, this.eb.height - 50);
    f === this.eb.height - 50 && (this.WS = this.jwa());
    this.axis = new gvjs_1S(this.eb.width - this.WS - d, a, c, d, d, this.WS, gvjs_J(this.m, "hAxis.format"));
    gvjs_3pa(this, b, d);
    gvjs_K(this.m, "timeline.showBarLabels", !0) && gvjs_4pa(this, b, d);
    this.axis.xp = f;
    a = gvjs_J(this.m, gvjs_qx, gvjs_xu) === gvjs_xu;
    c = gvjs_K(this.m, "timeline.displayDuration", !0);
    f = this.m.cb("timeline.tooltipDateFormat");
    return {
        size: this.eb,
        background: {
            rect: new gvjs_5(0, 0, this.eb.width, this.eb.height),
            brush: gvjs_qy(this.m, gvjs_ht),
            rka: gvjs_K(this.m, gvjs_Vs, !0)
        },
        yF: b,
        rect: new gvjs_5(0, 0, this.eb.width, e),
        xsa: d,
        nY: this.axis.PC,
        cna: a,
        $ma: c,
        nya: f
    }
};

function gvjs_1pa(a) {
    for (var b = 0, c = a.length; b < c; ++b) {
        var d = a[b],
            e = gvjs_Le(d.Mb).sort(function(h, k) {
                h = h.Pt.start;
                k = k.Pt.start;
                return gvjs_oe(h) ? gvjs_Nz(h, k) : h < k ? -1 : h > k ? 1 : 0
            });
        d = d.vJ;
        for (var f = 0, g = e.length; f < g; ++f)
            gvjs_5pa(d, e[f])
    }
}

function gvjs_5pa(a, b) {
    for (var c = 0, d = a.length; c < d; ++c) {
        var e = a[c];
        if (b.Pt.start >= e.M0) {
            e.Mb.push(b);
            e.M0 = Math.max(b.Pt.end, e.M0);
            return
        }
    }
    a.push({
        Mb: [b],
        M0: b.Pt.end
    })
}

function gvjs_2pa(a, b) {
    var c = .2 * a.eb.width;
    c -= 2 * a.q5;
    var d = gvjs_K(a.m, "timeline.showRowLabels", !0),
        e = [],
        f = 0,
        g = 0;
    b.forEach(function(h) {
        var k = h.rect;
        k.left = 0;
        k.top = g;
        k.width = a.eb.width;
        k.height = 2 * a.Oga + a.gW * h.vJ.length + a.Nga * (h.vJ.length - 1);
        g += k.height;
        d && (h = gvjs_DG(a.Zd, h.name, a.c0, c, 1),
            f = Math.max(f, h.Oq),
            e.push(h))
    }, a);
    if (!d)
        return 0;
    b.forEach(function(h, k) {
        k = e[k];
        var l = h.rect;
        l = {
            text: k.lines[0],
            ja: a.c0,
            lines: [],
            ld: gvjs_R,
            Pc: gvjs_0,
            tooltip: null,
            anchor: {
                x: f + a.q5,
                y: l.top + l.height / 2
            }
        };
        l.lines.push({
            x: 0,
            y: 0,
            length: k.Oq,
            text: k.lines[0]
        });
        h.label = l
    });
    return f + 2 * a.q5
}

function gvjs_3pa(a, b, c) {
    var d = a.axis.sm.map(function(f) {
            return a.axis.scale(f.v)
        }),
        e = null != a.aE ? a.aE.cd() : null;
    b.forEach(function(f) {
        f.vJ.forEach(function(g, h) {
            g.Mb.forEach(function(k) {
                var l = k.Pt,
                    m = a.axis,
                    n = l.start;
                var p = c + (n - m.Gc) * m.W5;
                l = (l.end - n) * m.W5;
                3 > l && (m = (3 - l) / 2,
                    l = 3,
                    p -= m);
                var q = l;
                l = f.rect.top + a.Oga + h * (a.Nga + a.gW);
                m = a.gW;
                gvjs_K(a.m, "avoidOverlappingGridLines", !0) ? (n = gvjs_6pa(a, d, p, 1),
                    p = gvjs_6pa(a, d, p + q, -1),
                    p = new gvjs_5(n, l, p - n, m)) : p = new gvjs_5(p, l, q, m);
                k.rect = p;
                p = !k.name || a.s8 ? f.name : k.name;
                a.Vfa ? p = {
                    fill: a.Vfa.color
                } : null != e ? p = {
                    fill: e.Cq(p)
                } : (l = a.Jla,
                    gvjs_Ze(l, p) ? p = gvjs_x(gvjs_Sy(l, p)) : (m = a.Kb,
                        m = {
                            fill: m[gvjs_We(l) % m.length].color
                        },
                        l[p] = gvjs_x(m),
                        p = m));
                k.RN = p;
                if (k.oga) {
                    p = k.oga;
                    p = gvjs_kf(p);
                    if (gvjs_0z(p))
                        var r = {
                            fill: {
                                color: p
                            },
                            stroke: {
                                color: p
                            }
                        };
                    else if ("{" === p.charAt(0)) {
                        try {
                            var t = gvjs_Gi(p)
                        } catch (u) {}
                        null != t && (r = t)
                    }
                    null == r && (r = a.hla.bind(a),
                        gvjs_sf(p, "{") ? (r = gvjs_Ny(gvjs_$I(p), r),
                            gvjs_Ze(r, "") && (gvjs_2e(r, r[""]),
                                gvjs_Qy(r, "")),
                            gvjs_Ze(r, "*") && (gvjs_2e(r, r["*"]),
                                gvjs_Qy(r, "*"))) : r = r(gvjs_Jz(p)));
                    r && (k = k.RN,
                        null != r.fill && (null != r.fill.color && (k.fill = gvjs_qj(r.fill.color).hex),
                            null != r.fill.opacity && (k.fillOpacity = r.fill.opacity)),
                        null != r.stroke && (null != r.stroke.color && (k.stroke = gvjs_qj(r.stroke.color).hex),
                            null != r.stroke.width && (k.strokeWidth = r.stroke.width),
                            null != r.stroke.opacity && (k.strokeOpacity = r.stroke.opacity)))
                }
            })
        })
    })
}

function gvjs_6pa(a, b, c, d) {
    a = gvjs_Spa(b, function(e, f, g) {
        var h = Math.abs(c - e),
            k = f;
        return 1 > h ? ((e = (e = g[f - 1]) ? Math.abs(c - e) : void 0) && e < h && (h = e,
                k = f - 1),
            (g = (g = g[f + 1]) ? Math.abs(c - g) : void 0) && g < h && (k = f + 1),
            gvjs_Re(k, f)) : gvjs_Re(c, e)
    }, a);
    return 0 > a ? c : b[a] + 1 * d
}

function gvjs_4pa(a, b, c) {
    gvjs_u(b, function(d) {
        gvjs_u(d.vJ, function(e) {
            e.Mb.sort(function(g, h) {
                return g.rect.left - h.rect.left
            });
            var f = c + a.H7;
            e.Mb.forEach(function(g, h) {
                var k = f;
                var l = {
                        text: g.name,
                        ja: a.zG,
                        lines: [],
                        ld: gvjs_2,
                        Pc: gvjs_0,
                        tooltip: null,
                        anchor: {
                            x: c,
                            y: g.rect.top + g.rect.height / 2
                        }
                    },
                    m = g.rect,
                    n = g.RN,
                    p = l.ja,
                    q = a.H7,
                    r = !0,
                    t = gvjs_K(a.m, "timeline.forceBarLabelInside", !1),
                    u = a.Zd(l.text, p).width;
                if (u < m.width - 2 * q)
                    l.anchor.x = m.left + q;
                else if (!t && m.left - q - k > u)
                    l.anchor.x = m.left - q,
                    l.ld = gvjs_R,
                    r = !1;
                else {
                    var v = a.eb.width - q - a.WS;
                    h < e.Mb.length - 1 && (v = e.Mb[h + 1].rect.left - q);
                    k = m.left + m.width + q;
                    !t && v - k > u ? (l.anchor.x = k,
                        k += u,
                        r = !1) : (h = gvjs_DG(a.Zd, l.text, p, m.width - 2 * q, 1, !0),
                        h.lines[0] ? (l.anchor.x = m.left + q,
                            l.text = h.lines[0]) : l = null)
                }
                r = r && n.fill ? gvjs_vj(gvjs_qj(n.fill).hex) : [255, 255, 255];
                n.text = gvjs_uj(gvjs_4z(r, [
                    [32, 32, 32],
                    [128, 128, 128],
                    [255, 255, 255]
                ]));
                n.bold = gvjs_uj(gvjs_4z(r, [
                    [0, 0, 0],
                    [255, 255, 255]
                ]));
                f = k = Math.max(k, m.left + m.width + q);
                g.G7 = l
            })
        })
    })
}

function gvjs_0pa(a) {
    var b = a.m.fa(gvjs_2t);
    return null != b && Array.isArray(b) ? gvjs_v(b, function(c) {
        return typeof c === gvjs_l ? {
            color: c
        } : c
    }, a) : null
}
gvjs_i4.prototype.hla = function(a) {
    var b = {
        fill: {},
        stroke: {}
    };
    null != a && (null != a.color && (b.fill.color = a.color,
            b.stroke.color = a.color),
        null != a.opacity && (b.fill.opacity = a.opacity,
            b.stroke.opacity = a.opacity),
        null != a.fillColor && (b.fill.color = a.fillColor),
        null != a.fillOpacity && (b.fill.opacity = a.fillOpacity),
        null != a.strokeColor && (b.stroke.color = a.strokeColor),
        null != a.strokeOpacity && (b.stroke.opacity = a.strokeOpacity),
        null != a.strokeWidth && (b.stroke.width = a.strokeWidth));
    return b
};

function gvjs_j4(a) {
    this.el = this.lr = this.mr = this.jI = this.Nl = this.Zm = null;
    null != a && (a = new gvjs_Aj([a]),
        this.Zm = a.Aa("focusedTrack"),
        this.Nl = a.Aa("focusedBar"))
}
gvjs_j4.prototype.clone = function() {
    var a = new gvjs_j4;
    a.Zm = this.Zm;
    a.Nl = this.Nl;
    a.jI = this.jI;
    a.mr = this.mr;
    a.lr = this.lr;
    a.el = this.el;
    return a
};
gvjs_j4.prototype.equals = function(a) {
    return this.Zm == a.Zm && this.Nl == a.Nl && this.mr == a.mr && this.lr == a.lr
};

function gvjs_k4(a) {
    this.bI = a;
    this.K = null
}
gvjs_k4.prototype.setState = function(a) {
    this.K = a.clone()
};
gvjs_k4.prototype.Is = function(a) {
    var b = this.K;
    a.el !== b.el && this.dispatchEvent(gvjs_k, null);
    a.Nl === b.Nl && a.Zm === b.Zm || this.dispatchEvent(null === a.Nl ? gvjs_6v : gvjs_7v, {
        row: a.jI
    });
    this.K = a.clone()
};
gvjs_k4.prototype.dispatchEvent = function(a, b) {
    gvjs_I(this.bI, a, b)
};

function gvjs_l4(a) {
    gvjs_F.call(this);
    this.K = this.ua = null;
    this.zb = new gvjs_KK(a);
    gvjs_6x(this, this.zb)
}
gvjs_o(gvjs_l4, gvjs_F);
gvjs_l4.prototype.jL = function(a) {
    this.ua = a
};
gvjs_l4.prototype.setState = function(a) {
    this.K = a
};
gvjs_l4.prototype.sI = function() {
    return this.Hv.bind(this)
};
gvjs_l4.prototype.Hv = function(a, b) {
    switch (a.type) {
        case gvjs_wb:
            var c = a.data.EU;
            a = a.data.iN;
            switch (b) {
                case gvjs_9u:
                    this.K.Zm = c;
                    this.K.Nl = a;
                    this.K.jI = this.ua.yF[c].Mb[a].row;
                    break;
                case gvjs_$u:
                    this.K.Zm = null;
                    this.K.Nl = null;
                    this.K.jI = this.ua.yF[c].Mb[a].row;
                    break;
                case gvjs_Wt:
                    this.K.mr = c;
                    this.K.lr = a;
                    this.K.el = this.ua.yF[c].Mb[a].row;
                    break;
                case "shiftclick":
                    this.K.mr = null,
                        this.K.lr = null,
                        this.K.el = null
            }
            gvjs_LK(this.zb, 50)
    }
};

function gvjs_7pa(a, b) {
    this.ua = a;
    this.K = null;
    this.nv = [];
    this.L9 = b
}
gvjs_ = gvjs_7pa.prototype;
gvjs_.JG = function(a) {
    if (!this.L9 || this.K.equals(a))
        return {
            Os: [],
            Ps: []
        };
    var b = this.nv;
    a = this.Dk(a);
    return this.fH(a, b)
};
gvjs_.Dk = function(a) {
    var b = [];
    if (this.L9) {
        var c = new Set;
        null !== a.Zm && null !== a.Nl && c.add([a.Zm, a.Nl]);
        null !== a.mr && null !== a.lr && c.add([a.mr, a.lr]);
        gvjs_nj(c).forEach(function(d) {
            var e = d[0],
                f = d[1];
            d = [];
            var g = {
                    type: gvjs_wb,
                    data: {
                        EU: e,
                        iN: f
                    }
                },
                h = !1,
                k = a.Zm,
                l = a.Nl;
            null != k && null != l && k == e && l == f && (h = !0,
                d.push({
                    targets: [g],
                    effect: {
                        type: gvjs_st,
                        data: null
                    }
                }),
                d.push({
                    targets: [g],
                    effect: {
                        type: gvjs_Uw,
                        data: null
                    }
                }));
            k = a.mr;
            l = a.lr;
            null != k && null != l && k == e && l == f && (h = !0,
                d.push({
                    targets: [g],
                    effect: {
                        type: "outlined",
                        data: null
                    }
                }));
            (e = h ? gvjs_c4 : null) && d.push({
                targets: [g],
                effect: {
                    type: "setLayer",
                    data: {
                        Kta: e
                    }
                }
            });
            b.push.apply(b, gvjs_9d(d))
        });
        this.nv = b;
        this.K = a.clone()
    }
    return b
};
gvjs_.fH = function(a, b) {
    a = this.Nu(a);
    var c = this.Nu(b);
    b = gvjs_Yz(a, c);
    a = gvjs_Yz(c, a);
    return {
        Os: this.Aw(b),
        Ps: this.Aw(a)
    }
};
gvjs_.Nu = function(a) {
    a = a.map(function(b) {
        return gvjs_Hi(b)
    });
    return new Set(a)
};
gvjs_.Aw = function(a) {
    return gvjs_nj(a).map(function(b) {
        return gvjs_Gi(b)
    })
};

function gvjs_m4(a, b, c, d) {
    gvjs_F.call(this);
    var e = this;
    this.Zf = this.Vm = this.K = null;
    var f = d(function() {
        return e.hk(!0)
    });
    this.ea = new gvjs_l4(f);
    gvjs_6x(this, this.ea);
    this.Tc = new gvjs_Ypa(a, b, this.ea.sI(), d);
    this.Xm = new gvjs_k4(c)
}
gvjs_o(gvjs_m4, gvjs_F);
gvjs_m4.prototype.draw = function(a, b, c, d) {
    this.K = b.clone();
    this.ea.setState(this.K);
    this.ea.jL(a);
    this.Xm.setState(this.K);
    this.Zf = new gvjs_7pa(a, d);
    b = this.Zf.Dk(this.K);
    this.Tc.draw(a, b, c);
    this.Vm = this.K.clone();
    this.Xm.dispatchEvent(gvjs_i, null)
};
gvjs_m4.prototype.hk = function(a) {
    var b = this.Zf.JG(this.K);
    this.Tc.refresh(b);
    this.Vm = this.K.clone();
    a && this.Xm.Is(this.K)
};
gvjs_m4.prototype.setSelection = function(a) {
    this.hk(!0);
    0 === a.length ? (this.K.lr = null,
        this.K.mr = null,
        this.K.el = null) : this.K.el = a[0].row;
    this.hk(!1)
};
gvjs_m4.prototype.getSelection = function() {
    return [{
        row: this.Vm.el,
        column: null
    }]
};

function gvjs_n4(a) {
    gvjs_Qn.call(this, a);
    this.uh = this.ab = null
}
gvjs_o(gvjs_n4, gvjs_Qn);
gvjs_ = gvjs_n4.prototype;
gvjs_.Rd = function(a, b, c) {
    var d = this;
    (0,
        gvjs_D.removeAll)(this.container);
    var e = new gvjs_Aj([c || {}, gvjs_8pa]);
    c = this.La(e);
    var f = this.getHeight(e),
        g = new gvjs_A(c, f);
    c = gvjs_K(e, gvjs_Eu);
    gvjs_9pa(this, g, a, c);
    this.ab.rl(function() {
        return d.no(b, e, g, a)
    }, a)
};

function gvjs_9pa(a, b, c, d) {
    null == a.ab ? a.ab = new gvjs_3B(a.container, b, c, d) : a.ab.update(b, c)
}
gvjs_.no = function(a, b, c, d) {
    var e = this.ab.Oa(),
        f = this.ab.yq(),
        g = new gvjs_i4(a, b, function(h, k) {
            return e.me(h, k)
        }, c, function() {
            return e.IC()
        });
    c = g.$g();
    g = g.axis;
    if (null == g)
        throw Error("Error! The axis was unable to be calculated.");
    gvjs_E(this.uh);
    this.uh = new gvjs_m4(e, f, this, d);
    d = new gvjs_j4(b.pb("state"));
    b = gvjs_K(b, gvjs_ru, !0);
    this.uh.draw(c, d, g, b);
    this.fY(a)
};
gvjs_.setSelection = function(a) {
    this.uh && this.uh.setSelection(a)
};
gvjs_.getSelection = function() {
    return this.uh ? this.uh.getSelection() : []
};
gvjs_.He = function() {
    gvjs_E(this.ab);
    this.ab = null;
    gvjs_E(this.uh);
    this.uh = null
};
gvjs_.fY = function(a) {
    var b = this.ab.Oa();
    setTimeout(function() {
        if (b && b.ws) {
            var c = b.ws();
            if (c && a) {
                var d = gvjs_gL(a);
                if (null !== c) {
                    if ("script" === c.tagName.toLowerCase())
                        throw Error("Use setTextContent with a SafeScript.");
                    if (c.tagName.toLowerCase() === gvjs_Jd)
                        throw Error("Use setTextContent with a SafeStyleSheet.");
                }
                c.innerHTML = gvjs_1f(d)
            }
        }
    }, 0)
};
var gvjs_8pa = {
    backgroundColor: {
        fill: gvjs_Ox,
        stroke: gvjs_rt,
        strokeWidth: 10,
        strokeOpacity: .2
    },
    tooltip: {
        textStyle: {
            fontSize: 12,
            fontName: gvjs_2r,
            color: gvjs_rt
        }
    }
};
gvjs_q(gvjs_Xc, gvjs_n4, void 0);
gvjs_n4.prototype.draw = gvjs_n4.prototype.draw;
gvjs_n4.prototype.setSelection = gvjs_n4.prototype.setSelection;
gvjs_n4.prototype.getSelection = gvjs_n4.prototype.getSelection;
gvjs_n4.prototype.clearChart = gvjs_n4.prototype.Jb;