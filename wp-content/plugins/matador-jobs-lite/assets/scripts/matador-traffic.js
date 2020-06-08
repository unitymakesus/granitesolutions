"use strict";

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) { return; } var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var MatadorTraffic =
/*#__PURE__*/
function () {
  function MatadorTraffic(domain) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "{}";

    _classCallCheck(this, MatadorTraffic);

    this.domain = domain;
    this.options = JSON.parse(options);
    this.referrer = document.referrer;
    this.defaults = {
      host: this.domain,
      // Set upon initialization. Do not pass this as argument.
      cookieName: 'matador_visitor',
      // Don't change. Hard-coded into PHP.
      separator: '.',
      // Don't change. Hard-coded into PHP cookie parser.
      expires: 172800000,
      // 2 days in seconds
      labels: {
        none: 'direct(none)',
        social: 'social',
        referral: 'referral',
        organic: 'organic'
      },
      queryParams: {
        campaign: 'utm_campaign',
        source: 'utm_source',
        medium: 'utm_medium',
        term: 'utm_term',
        content: 'utm_content'
      },
      socialNetworks: {
        facebook: ['/(.+?)\.facebook\./', '/(.+?)\.fb\.me/'],
        linkedin: ['/(.+?)\.linkedin\./'],
        twitter: ['/(.+?)\.twitter\./', '/(.+?)\.t\.co/'],
        reddit: ['/(.+?)\.reddit\./'],
        instagram: ['/(.+?)\.instagram\./'],
        youtube: ['/(.+?)\.youtube\./']
      },
      searchEngines: {
        google: ['/(.+?)\.google\./'],
        bing: ['/(.+?)\.bing\./'],
        yahoo: ['/(.+?)\.yahoo\./'],
        aol: ['/(.+?)\.aol\./'],
        baidu: ['/(.+?)\.baidu\./'],
        duckduckgo: ['/(.+?)\.duckduckgo\./']
      }
    };
    this.options = Object.assign(this.defaults, this.options);
    this.campaign = {
      timestamp: "",
      sessions: 1,
      campaigns: 0,
      campaign: "",
      medium: "",
      source: "",
      term: "",
      content: ""
    };
    this.initialize();
  }

  _createClass(MatadorTraffic, [{
    key: "initialize",
    value: function initialize() {
      var cookie; // Step 1: Check if we have a new session. If our referrer is not the same site, we assume so.

      if (this.hasReferrer()) {
        if (this.hasCookie(this.options.cookieName)) {
          cookie = this.getCookie(this.options.cookieName);
          this.parseMatadorCookie(cookie);
        } else if (this.hasCookie("__utmz")) {
          cookie = this.getCookie("__utmz");
          this.parseUTMZCookie(cookie);
        } // Set/Update the campaign if there is a new UTM query


        if (this.query.campaign) {
          if (this.query.campaign === this.campaign.campaign) {
            this.campaign.sessions++;
          } else {
            this.campaign.campaign = this.query.campaign;

            if (this.query.source && this.query.source !== this.campaign.source) {
              this.campaign.source = this.query.source;
            } else {
              this.campaign.source = '';
            }

            if (this.query.medium && this.query.medium !== this.campaign.medium) {
              this.campaign.medium = this.query.medium;
            } else {
              this.campaign.medium = '';
            }

            if (this.query.content && this.query.content !== this.campaign.content) {
              this.campaign.content = this.query.content;
            } else {
              this.campaign.content = "";
            }

            if (this.query.term && this.query.term !== this.campaign.term) {
              this.campaign.term = this.query.term;
            } else {
              this.campaign.term = '';
            }

            this.campaign.sessions = 1;
            this.campaign.campaigns++;
          } // When no UTM vars, set/update the campaign if there is new referrer

        } else if (this.referrer) {
          if (this.campaign.source === MatadorTraffic.removeProtocol(this.referrer)) {
            this.campaign.sessions++;
          } else {
            this.campaign.campaign = '';
            this.campaign.source = MatadorTraffic.removeProtocol(this.referrer);

            if (this.referrerIsA(this.options.socialNetworks)) {
              this.campaign.source = this.referrerIsA(this.options.socialNetworks);
              this.campaign.medium = this.options.labels.social;
            } else if (this.referrerIsA(this.options.searchEngines)) {
              this.campaign.source = this.referrerIsA(this.options.searchEngines);
              this.campaign.medium = this.options.labels.organic;
            } else {
              this.campaign.medium = this.options.labels.referral;
            }

            this.campaign.term = '';
            this.campaign.content = '';
            this.campaign.sessions = 1;
            this.campaign.campaigns++;
          }
        } else {
          if (this.campaign.source === this.options.labels.none) {
            this.campaign.sessions++;
          } else {
            this.campaign.source = this.options.labels.none;
            this.campaign.campaign = '';
            this.campaign.medium = '';
            this.campaign.term = '';
            this.campaign.content = '';
            this.campaign.sessions = 1;
            this.campaign.campaigns++;
          }
        }

        this.setCookie();
      }
    }
  }, {
    key: "queryParameter",
    value: function queryParameter(param) {
      var pair, key, value;
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (var _iterator = window.location.search.substring(1).split('&')[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          pair = _step.value;

          var _pair$split = pair.split('=');

          var _pair$split2 = _slicedToArray(_pair$split, 2);

          key = _pair$split2[0];
          var _pair$split2$ = _pair$split2[1];
          value = _pair$split2$ === void 0 ? "" : _pair$split2$;

          if (key === param) {
            break;
          }

          value = "";
        }
      } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion && _iterator["return"] != null) {
            _iterator["return"]();
          }
        } finally {
          if (_didIteratorError) {
            throw _iteratorError;
          }
        }
      }

      return value;
    }
  }, {
    key: "hasCookie",
    value: function hasCookie(cookieName) {
      return !(document.cookie.indexOf(cookieName) === -1);
    }
  }, {
    key: "getCookie",
    value: function getCookie(cookieName) {
      var cookie, name, value;
      var _iteratorNormalCompletion2 = true;
      var _didIteratorError2 = false;
      var _iteratorError2 = undefined;

      try {
        for (var _iterator2 = document.cookie.split('; ')[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
          cookie = _step2.value;

          var _cookie$split = cookie.split(/=(.+)/);

          var _cookie$split2 = _slicedToArray(_cookie$split, 2);

          name = _cookie$split2[0];
          var _cookie$split2$ = _cookie$split2[1];
          value = _cookie$split2$ === void 0 ? "" : _cookie$split2$;

          if (name === cookieName) {
            return value;
          }
        }
      } catch (err) {
        _didIteratorError2 = true;
        _iteratorError2 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion2 && _iterator2["return"] != null) {
            _iterator2["return"]();
          }
        } finally {
          if (_didIteratorError2) {
            throw _iteratorError2;
          }
        }
      }

      return null;
    }
  }, {
    key: "setCookie",
    value: function setCookie() {
      var prop,
          data,
          value,
          expires = new Date();
      var c = this.campaign;
      var o = this.options; //PHP expects U in seconds, JS provides it in miliseconds

      c.timestamp = Math.floor(expires.getTime() / 1000);
      expires.setTime(expires.getTime() + o.expires);
      value = "".concat(c.timestamp).concat(o.separator).concat(c.sessions).concat(o.separator).concat(c.campaigns);
      delete c.timestamp;
      delete c.sessions;
      delete c.campaigns;
      data = '';

      for (prop in c) {
        if (c[prop]) {
          data += data ? "|" : "";
          data += "".concat(prop, "=").concat(c[prop]);
        }
      }

      value += "".concat(o.separator).concat(data);
      document.cookie = o.cookieName + "=" + value.replace(/ /g, "_") + "; expires=" + expires.toUTCString() + "; domain=" + this.domain + "; path=/; samesite=strict; ";
    }
  }, {
    key: "hasReferrer",
    value: function hasReferrer() {
      return this.referrer.split('/')[2] !== location.hostname;
    }
  }, {
    key: "referrerIsA",
    value: function referrerIsA() {
      var rules = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var rule,
          label,
          regex,
          value = "";

      if (Object.keys(rules).length === 0 && rules.constructor === Object) {
        return false;
      }

      for (label in rules) {
        var _iteratorNormalCompletion3 = true;
        var _didIteratorError3 = false;
        var _iteratorError3 = undefined;

        try {
          for (var _iterator3 = rules[label][Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
            regex = _step3.value;
            // In Javascript, new RegExp adds the beginning and ending forward
            // slash characters that are included in the PHP array (passed to args)
            // for its RegExp evaluation. So remove the first and last char before
            // calling a new RegExp
            rule = new RegExp(regex.slice(1, -1));

            if (this.referrer.match(rule)) {
              value = label;
              break;
            }
          }
        } catch (err) {
          _didIteratorError3 = true;
          _iteratorError3 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion3 && _iterator3["return"] != null) {
              _iterator3["return"]();
            }
          } finally {
            if (_didIteratorError3) {
              throw _iteratorError3;
            }
          }
        }

        if (value) {
          // Wish I didn't need to break twice, but alas.
          break;
        }
      }

      return value;
    }
  }, {
    key: "parseMatadorCookie",
    value: function parseMatadorCookie(cookie) {
      var chunks, crumbs, parts;
      chunks = cookie.split(".", 4);
      this.campaign.timestamp = chunks[0];
      this.campaign.sessions = parseInt(chunks[1], 10);
      this.campaign.campaigns = parseInt(chunks[2], 10);
      var _iteratorNormalCompletion4 = true;
      var _didIteratorError4 = false;
      var _iteratorError4 = undefined;

      try {
        for (var _iterator4 = chunks[3].split("|")[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
          crumbs = _step4.value;
          parts = crumbs.split("=");
          this.campaign[parts[0]] = parts[1];
        }
      } catch (err) {
        _didIteratorError4 = true;
        _iteratorError4 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion4 && _iterator4["return"] != null) {
            _iterator4["return"]();
          }
        } finally {
          if (_didIteratorError4) {
            throw _iteratorError4;
          }
        }
      }
    }
  }, {
    key: "parseUTMZCookie",
    value: function parseUTMZCookie(utmz) {
      var mappings, chunks, crumbs, parts;
      mappings = {
        utmccn: "campaign",
        utmcmd: "medium",
        utmcsr: "source",
        utmctr: "term",
        utmcct: "content"
      };
      chunks = utmz.split(".", 5);
      this.campaign.timestamp = chunks[1];
      this.campaign.sessions = parseInt(chunks[2], 10);
      this.campaign.campaigns = parseInt(chunks[3]);
      var _iteratorNormalCompletion5 = true;
      var _didIteratorError5 = false;
      var _iteratorError5 = undefined;

      try {
        for (var _iterator5 = chunks[4].split("|")[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
          crumbs = _step5.value;
          parts = crumbs.split("=");
          this.campaign[mappings[parts[0]]] = parts[1];
        }
      } catch (err) {
        _didIteratorError5 = true;
        _iteratorError5 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion5 && _iterator5["return"] != null) {
            _iterator5["return"]();
          }
        } finally {
          if (_didIteratorError5) {
            throw _iteratorError5;
          }
        }
      }
    }
  }, {
    key: "query",
    get: function get() {
      var query = {};
      query.campaign = this.queryParameter(this.options.queryParams.campaign);
      query.source = this.queryParameter(this.options.queryParams.source);
      query.medium = this.queryParameter(this.options.queryParams.medium);
      query.term = this.queryParameter(this.options.queryParams.term);
      query.content = this.queryParameter(this.options.queryParams.content);
      return query;
    }
  }], [{
    key: "removeProtocol",
    value: function removeProtocol(href) {
      return href.replace(/.*?:\/\//g, "");
    }
  }]);

  return MatadorTraffic;
}();