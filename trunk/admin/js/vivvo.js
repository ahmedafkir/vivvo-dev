if(typeof(vivvo) == "undefined") var vivvo = {};
if(typeof(vivvo.admin) == "undefined") vivvo.admin = {};
if(typeof(vivvo.controls) == "undefined") vivvo.controls = {};
if(typeof(vivvo.utils) == "undefined") vivvo.admin.utils = {};
vivvo.admin.debugMode = false;

vivvo.admin.utils.userAutocompleter = Class.create ();
Object.extend (
	vivvo.admin.utils.userAutocompleter.prototype,
	{
		initialize: function (id){
			new Ajax.Autocompleter(
				id + '_autocomplete',
				id+ '_autocomplete_choices',
				'users.php',
				{
					afterUpdateElement : function(text, li) {
						$(id).value = li.id.replace(/user_id_/,'');
					},
					paramName: 'name',
					parameters: 'user_list=1'
				}
			);
			$(id + '_autocomplete').observe('change', function (e) {
				if (Event.element(e).value == ''){
					$(id).value = '';
				}
			});
		}
	}
);

vivvo.admin.utils.timezoneAutocompleter = Class.create ();
Object.extend (
	vivvo.admin.utils.timezoneAutocompleter.prototype,
	{
		initialize: function (id){
			new Ajax.Autocompleter(
				id + '_autocomplete',
				id+ '_autocomplete_choices',
				'ajax.php?timezone=1',
				{
					afterUpdateElement : function(text, li) {
						$(id).value = li.innerHTML;
					},
					paramName: 'name'
				}
			);
			$(id + '_autocomplete').observe('change', function (e) {
				$(id).value = Event.element(e).value;
			});
		}
	}
);

vivvo.admin.utils.hideDump = Prototype.emptyFunction; /// @deprecated
vivvo.admin.utils.addDump = function (message, type, error, additional) {
	vivvo.admin.utils.dump.add($$('.dump').first(), message, type, error, additional);
};

vivvo.admin.utils.dump = {

	count: {},

	add: function(container, message, type, error, info) {

		if (!(container = $(container))) {
			return;
		}

		if (error << 0) {
			message += ' (' + error + ')';
		}

		if (info) {
			message += ': ' + info;
		}

		var id = container.identify(),
			dump = new Element('span', {'class': type || 'info'}).update(message);

		if (!(id in vivvo.admin.utils.dump.count)) {
			vivvo.admin.utils.dump.count[id] = 0;
		}

		vivvo.admin.utils.dump.count[id]++;
		container.show().insert(dump);

		(function(span, id) {

			var container = $(id);

			span.remove();

			if (--vivvo.admin.utils.dump.count[id] <= 0) {
				container.hide();
			}

		}).delay(10, dump, id);
	}
};

vivvo.admin.utils.ajaxAction = function (params, callBack){
	vivvo.admin.utils.ajaxExecute(params, callBack, 'ajax.php', $$('.dump')[0]);
}

vivvo.admin.utils.refreshSession = function (){
    new Ajax.Request('ajax.php', {
        method: 'get',
        onSuccess: function(transport) {
            
        }
    });
}

vivvo.admin.utils.ajaxExecute = function (params, callBack, url, dump){
	$$('body')[0].style.cursor = 'wait';
	if (vivvo.admin.debugMode){
		params.dump_info = 1;
	}

	params.SECURITY_TOKEN = vivvo.token;

	new Ajax.Updater(dump,url, {
		method: 'post',
		parameters: params,
		evalScripts: true,
		onComplete: function (transport) {
			$$('body')[0].style.cursor = 'default';
			if (callBack){
				callBack(transport);
			}
		},
		insertion: 'bottom'
	});
}

vivvo.user = {
	_privileges: {},
	setPrivileges: function(priv) {
		this._privileges = priv.evalJSON();
	},
	is: function (privName, privType, privId){
		var _ = this;
		return Try.these(
			function() { return !privType && (privName in _._privileges) && _._privileges[privName] && _._privileges[privName] !== '0'; },
			function() { return ((_._privileges[privName][privType].indexOf(privId) != -1) || (_._privileges[privName][privType].indexOf(0) != -1)) },
			function() { return (_._privileges[privName] == 1)}
		) || false;
	},
	can: function (privName, privType, privId){
		return this.is(privName, privType, privId);
	},
	has: function (privName, privType, privId){
		return this.is(privName, privType, privId);
	}
};

vivvo.personalSetting = {
	cookieName: 'adminPersonalSettings',
	jar : false,

	initJar: function () {
		this.jar =  new CookieJar({
			expires:2592000,
			path: '/'
		})
	},

	createCookie: function(){
		var preferences = this.jar.get(this.cookieName + vivvo.user.id);
		if (preferences == null){
			preferences = {};
			this.jar.put(this.cookieName + vivvo.user.id, preferences);
		}
	},

	set: function (name, value){
		if (this.jar == false){
			this.initJar();
		}
		var preferences = this.jar.get(this.cookieName + vivvo.user.id);
		if (preferences == null){
			preferences = {};
		}
		preferences[name] = value;
		this.jar.put(this.cookieName + vivvo.user.id, preferences);
	},

	get: function (name){
		if (this.jar == false){
			this.initJar();
		}
		var preferences = this.jar.get(this.cookieName + vivvo.user.id);
		if (preferences){
			if (preferences[name]){
				return preferences[name];
			}
		}
		return {};
	},

	unset: function (name){
		if (this.jar == false){
			this.initJar();
		}
		var preferences = this.jar.get(this.cookieName + vivvo.user.id);
		preferences[name] = null;
		this.jar.put(this.cookieName + vivvo.user.id, preferences);
	}
}

vivvo.admin.quickNav = {	/// @deprecated
    emptyAndHide: Prototype.emptyFunction,
    addToMenu: Prototype.emptyFunction,
    removeFromMenu: Prototype.emptyFunction,
    toggleMenuItem: Prototype.emptyFunction,
    filterTogglePin: Prototype.emptyFunction
}

vivvo.mainNav = {

	menu_content: null,
	subnav: null,
	initialized: false,
	stack: $A(),

	initialize: function() {

		this.menu_content = $('menu_content');
		this.subnav = $('sub_nav');

		this.initialized = true;

		this.stack.each(function(call) {
			vivvo.mainNav[call[0]].apply(vivvo.mainNav, call[1]);
		})
		.clear();

		if (this.menu_content && this.subnav) {
			this.setup();
		}
	},

	pinSubNav: function(condition) {

		if (!this.initialized) {
			this.stack.push(['pinSubNav', arguments]);
		} else if (!condition || $('menu_' + condition)) {
			this.menu_content.addClassName('selected_sub').addClassName('active_sub');
			this.subnav.show();
		}

		return this;
	},

	select: function(item) {

		if (!this.initialized) {
			this.stack.push(['select', arguments]);
		} else {
			var elem = $('menu_' + item);
			if (!elem) {
				return false;
			}
			elem.addClassName('selected');
		}
		return this;
	},

	setup: function() {

		var subnav_hovered = false, subnav_timer, content_timer;

		this.subnav.hover(
			function() {
				subnav_hovered = true;
				clearTimeout(content_timer);
				clearTimeout(subnav_timer);
			}.bind(this),
			function() {
				subnav_timer = setTimeout(function(){
					subnav_hovered = false;
					if (!this.menu_content.hasClassName('active_sub')) {
						Effect.Queues.get('navscope').invoke('cancel');
						this.subnav.slideUp({duration: .2,  queue: {position: 'end', scope: 'navscope', limit: 2}, transition: Effect.Transitions.linear, afterFinish: function(){ this.subnav.setStyle({height: '22px'});}.bind(this)});
						this.menu_content.removeClassName('selected_sub');
					}
				}.bind(this), 1000);
			}.bind(this)
		);

		this.menu_content.hover(
			function() {
				clearTimeout(content_timer);
				clearTimeout(subnav_timer);
				if (!subnav_hovered && !this.menu_content.hasClassName('active_sub')) {
					this.menu_content.addClassName('selected_sub');
					Effect.Queues.get('navscope').invoke('cancel');
					this.subnav.slideDown({duration: .2,  queue: {position: 'end', scope: 'navscope', limit: 2}, transition: Effect.Transitions.linear, afterFinish: function(){ this.subnav.setStyle({height: '22px'});}.bind(this)});
				}
			}.bind(this),
			function() {
				content_timer = setTimeout(function(){
					subnav_hovered = false;
					if (!this.menu_content.hasClassName('active_sub')) {
						Effect.Queues.get('navscope').invoke('cancel');
						this.subnav.slideUp({duration: .2,  queue: {position: 'end', scope: 'navscope', limit: 2}, transition: Effect.Transitions.linear, afterFinish: function(){ this.subnav.setStyle({height: '22px'});}.bind(this)});
						this.menu_content.removeClassName('selected_sub');
					}
				}.bind(this), 1000);
			}.bind(this)
		);
	}
};

Event.observe(window, 'load', vivvo.mainNav.initialize.bind(vivvo.mainNav));

Date.prototype.toMYSQLFormat = function(include_time) {
  function pad(n) { return n < 10 ? '0' + n : n + ''; }
  var string = this.getFullYear() + '-' + pad(this.getMonth() + 1) + '-' + pad(this.getDate());
  if (include_time) {
    string += ' ' + pad(this.getHours()) + ':' + pad(this.getMinutes()) + ':' + pad(this.getSeconds());
  }
  return string;
};

setInterval (vivvo.admin.utils.refreshSession, 600000);