class MatadorTraffic {

	constructor(domain,options="{}") {

		this.domain = domain;
		this.options = JSON.parse(options);
		this.referrer = document.referrer;

		this.defaults = {
			host: this.domain, // Set upon initialization. Do not pass this as argument.
			cookieName: 'matador_visitor', // Don't change. Hard-coded into PHP.
			separator: '.', // Don't change. Hard-coded into PHP cookie parser.
			expires: 172800000, // 2 days in seconds
			labels: {
				none: 'direct(none)',
				social: 'social',
				referral: 'referral',
				organic: 'organic',
			},
			queryParams: {
				campaign: 'utm_campaign',
				source: 'utm_source',
				medium: 'utm_medium',
				term: 'utm_term',
				content: 'utm_content',
			},
			socialNetworks: {
				facebook: ['/(.+?)\.facebook\./', '/(.+?)\.fb\.me/'],
				linkedin: ['/(.+?)\.linkedin\./'],
				twitter: ['/(.+?)\.twitter\./', '/(.+?)\.t\.co/'],
				reddit: ['/(.+?)\.reddit\./'],
				instagram: ['/(.+?)\.instagram\./'],
				youtube: ['/(.+?)\.youtube\./'],
			},
			searchEngines: {
				google: ['/(.+?)\.google\./'],
				bing: ['/(.+?)\.bing\./'],
				yahoo: ['/(.+?)\.yahoo\./'],
				aol: ['/(.+?)\.aol\./'],
				baidu: ['/(.+?)\.baidu\./'],
				duckduckgo: ['/(.+?)\.duckduckgo\./'],
			},
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

	get query() {
		let query = {};

		query.campaign = this.queryParameter(this.options.queryParams.campaign);
		query.source = this.queryParameter(this.options.queryParams.source);
		query.medium = this.queryParameter(this.options.queryParams.medium);
		query.term = this.queryParameter(this.options.queryParams.term);
		query.content = this.queryParameter(this.options.queryParams.content);

		return query;
	}

	initialize() {

		let cookie;

		// Step 1: Check if we have a new session. If our referrer is not the same site, we assume so.
		if (this.hasReferrer()) {

			if(this.hasCookie(this.options.cookieName)) {
				cookie = this.getCookie(this.options.cookieName);
				this.parseMatadorCookie(cookie);
			} else if (this.hasCookie("__utmz")) {
				cookie = this.getCookie("__utmz");
				this.parseUTMZCookie(cookie);
			}

			// Set/Update the campaign if there is a new UTM query
			if ( this.query.campaign ) {
				if ( this.query.campaign === this.campaign.campaign ) {
					this.campaign.sessions++;
				} else {
					this.campaign.campaign = this.query.campaign;
					if ( this.query.source && this.query.source !== this.campaign.source ) {
						this.campaign.source = this.query.source;
					} else {
						this.campaign.source = '';
					}
					if ( this.query.medium && this.query.medium !== this.campaign.medium ) {
						this.campaign.medium = this.query.medium;
					} else {
						this.campaign.medium = '';
					}
					if ( this.query.content && this.query.content !== this.campaign.content ) {
						this.campaign.content = this.query.content;
					} else {
						this.campaign.content = "";
					}
					if ( this.query.term && this.query.term !== this.campaign.term ) {
						this.campaign.term = this.query.term;
					} else {
						this.campaign.term = '';
					}
					this.campaign.sessions = 1;
					this.campaign.campaigns++;
				}
			// When no UTM vars, set/update the campaign if there is new referrer
			} else if ( this.referrer ) {
				if (this.campaign.source === MatadorTraffic.removeProtocol(this.referrer)) {
					this.campaign.sessions++;
				} else {

					this.campaign.campaign = '';

					this.campaign.source = MatadorTraffic.removeProtocol(this.referrer);

					if ( this.referrerIsA(this.options.socialNetworks) ) {
						this.campaign.source = this.referrerIsA(this.options.socialNetworks);
						this.campaign.medium = this.options.labels.social;
					} else if ( this.referrerIsA(this.options.searchEngines) ) {
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

	queryParameter(param) {
		let pair, key, value;

		for (pair of window.location.search.substring(1).split('&') ) {

			[key, value = ""] = pair.split('=');

			if (key === param) {
				break;
			}

			value = "";
		}

		return value;
	}

	hasCookie(cookieName) {
		return !(document.cookie.indexOf(cookieName) === -1)
	}

	getCookie(cookieName) {

		let cookie, name, value;

		for (cookie of document.cookie.split('; ')) {

			[name, value = ""] = cookie.split(/=(.+)/);

			if (name === cookieName) {
				return value;
			}
		}

		return null;
	}

	setCookie() {

		let prop, data, value, expires = new Date();

		const c = this.campaign;
		const o = this.options;

		//PHP expects U in seconds, JS provides it in miliseconds
		c.timestamp = Math.floor( expires.getTime() / 1000 );

		expires.setTime(expires.getTime() + o.expires);

		value = `${c.timestamp}${o.separator}${c.sessions}${o.separator}${c.campaigns}`;

		delete c.timestamp;
		delete c.sessions;
		delete c.campaigns;

		data = '';

		for ( prop in c ) {
			if ( c[prop] ) {
				data += (data) ? `|` : ``;
				data += `${prop}=${c[prop]}`;
			}
		}

		value += `${o.separator}${data}`;

		document.cookie = o.cookieName + "=" + value.replace(/ /g,"_") + "; expires=" + expires.toUTCString() + "; domain=" + this.domain + "; path=/; samesite=strict; ";
	}

	hasReferrer() {
		return this.referrer.split('/')[2] !== location.hostname;
	}

	referrerIsA(rules={}) {

		let rule, label, regex, value = "";

		if ( Object.keys(rules).length === 0 && rules.constructor === Object ) {
			return false;
		}

		for ( label in rules ) {

			for ( regex of rules[label] ) {

				// In Javascript, new RegExp adds the beginning and ending forward
				// slash characters that are included in the PHP array (passed to args)
				// for its RegExp evaluation. So remove the first and last char before
				// calling a new RegExp
				rule = new RegExp(regex.slice(1,-1));

				if (this.referrer.match(rule)) {
					value = label;
					break;
				}
			}
			if ( value ) {
				// Wish I didn't need to break twice, but alas.
				break;
			}
		}
		
		return value;
	}

	static removeProtocol(href) {
		return href.replace(/.*?:\/\//g, "");
	}

	parseMatadorCookie(cookie) {

		let chunks, crumbs, parts;

		chunks = cookie.split(".", 4);

		this.campaign.timestamp = chunks[0];
		this.campaign.sessions = parseInt( chunks[1], 10 );
		this.campaign.campaigns = parseInt( chunks[2], 10);

		for (crumbs of chunks[3].split("|")) {
			parts = crumbs.split("=");
			this.campaign[parts[0]] = parts[1];
		}
	}

	parseUTMZCookie(utmz) {

		let mappings, chunks, crumbs, parts;

		mappings = {utmccn: "campaign", utmcmd: "medium", utmcsr: "source", utmctr: "term", utmcct: "content"};

		chunks = utmz.split(".", 5);

		this.campaign.timestamp = chunks[1];
		this.campaign.sessions = parseInt( chunks[2], 10);
		this.campaign.campaigns = parseInt( chunks[3] );

		for (crumbs of chunks[4].split("|")) {
			parts = crumbs.split("=");
			this.campaign[mappings[parts[0]]] = parts[1]
		}
	}
}