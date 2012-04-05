vivvo.admin.newsletter = Class.create();

Object.extend (vivvo.admin.newsletter.prototype, vivvo.controls.list.prototype);

Object.extend (vivvo.admin.newsletter.prototype, {

	sourceScript : 'plugins.php?plugin=newsletter',
	formElement : null,
	orderForm : null,
	newNewsletterObserver : null,
	quickLinksObserver : null,
	savePreferencesObserver : null,
	loadSubscribersObserver : null,
	deleteNewsletterObserver : null,
	listOptions : {
		maillist : true
	},

	initialize : function(){
		if ($('menu_newsletter')){
			$('menu_newsletter').addClassName('selected');
		}

		this.actionName = 'newsletter';
		this.formElement = $('feed_form');
		this.orderForm = 'maillist_list_options_form';

		this.newNewsletterObserver = this.newNewsletter.bindAsEventListener(this);
		this.quickLinksObserver = this.loadNewsletter.bindAsEventListener(this);
		this.savePreferencesObserver = this.savePreferences.bindAsEventListener(this);
		this.loadSubscribersObserver = this.loadSubscribers.bind(this);
		this.deleteNewsletterObserver = this.deleteNewsletter.bind(this);

		this.initLeftnavEvents();
	},
	initLeftnavEvents : function(){
		Event.observe($('preferences_form'), 'submit',  this.savePreferencesObserver);
		Event.observe($('new_newsletter_link'), 'click', this.newNewsletterObserver);
		$$('.newsletter_quick_link').each(
		function(elem) {
			Event.observe(elem, 'click', this.quickLinksObserver);
		}, this);
		$$('.actions_small_left').each(function(elem){
			if (elem.down('img')){
				Event.observe(elem.down('img'), 'click',  this.deleteNewsletterObserver);
			}
		}, this);
	},
	removeLeftnavEvents : function(){
		Event.stopObserving($('preferences_form'), 'submit',  this.savePreferencesObserver);
		Event.stopObserving($('new_newsletter_link'), 'click', this.newNewsletterObserver);
		$$('.newsletter_quick_link').each(
		function(elem) {
			Event.stopObserving(elem, 'click', this.quickLinksObserver);
		}, this);
		$$('.actions_small_left').each(function(elem){
			if (elem.down('img')){
				Event.stopObserving(elem.down('img'), 'click',  this.deleteNewsletterObserver);
			}
		}, this);
	},
	loadNewsletter : function(e){
		elem = Event.element(e);
		search_id = elem.id.replace(/newsletter_quick_link_/, '');
		var _ = this;
		  new Ajax.Updater('content', this.sourceScript, {
			parameters: {
				search_id : search_id,
				list_output : '1'
			},
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
			}
		});
	},
	newNewsletter : function(){
		  searchOptions = '&list_output=1';
		  var _ = this
		  new Ajax.Updater('content', this.sourceScript, {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
			}
		});
	},
	loadSubscribers : function(search_confirm){
		  if (search_confirm){
		  	searchOptions = '&maillist=1&search_confirm='+search_confirm;
		  } else {
		  	searchOptions = '&maillist=1';
		  }
		  new Ajax.Updater('content', this.sourceScript, {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
			}
		});
	},
	deleteSubscriber : function(id){
		if (!id && !(id || false).match(/\d+/)){
			return false;
		}
		if (!confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
			return false;
		}
		document.body.style.cursor = 'wait';
		new Ajax.Updater('content', this.sourceScript+'&maillist', {
			parameters: $H({
				SUBSCRIBER_id : id,
				action : 'newsletter',
				cmd : 'subscriberDelete',
				maillist:'1'
			}),
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
			}
		});
	},
	savePreferences : function(){
		searchOptions= $('preferences_form').serialize();
		var _ = this;
		_.removeLeftnavEvents();
		new Ajax.Updater('navigation', this.sourceScript+'&leftnav=1', {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
				_.initLeftnavEvents();
				newsletter_tabs = new Control.Tabs('newsletter_tab_list');
			}
		});

	},
	reloadLeftnav : function(){
		var _ = this;
		_.removeLeftnavEvents();
		new Ajax.Updater('navigation', this.sourceScript, {
			evalScripts: true,
			parameters : {
				leftnav : '1'
			},
			onComplete: function () {
				document.body.style.cursor = 'default';
				_.initLeftnavEvents();
				newsletter_tabs = new Control.Tabs('newsletter_tab_list');
			}
		});

	},
	newsletterAction : function(cmd){
		if (!cmd) {cmd = '';}
		searchOptions = $('newsletter_form_element').serialize()+'&list_output=1&cmd='+cmd;
		var _ = this;
		document.body.style.cursor = 'wait';
		new Ajax.Updater('content', this.sourceScript, {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
				_.reloadLeftnav();
			}
		});
	},
	deleteNewsletter : function(e){
		element = Event.element(e);
		cmd='delete';
		if (!element.id){
			return false;
		} else {
			search_id=element.id.replace(/delete_newsletter_image_/, '');
		}
		if (confirm(vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))) {
			var _ = this;
			document.body.style.cursor = 'wait';
			new Ajax.Updater('content', _.sourceScript, {
				parameters: {
						action : 'newsletter',
						list_output : '1',
						cmd : 'delete',
						PLUGIN_NEWSLETTER_id : search_id
				},
				evalScripts: true,
				onComplete: function () {
					document.body.style.cursor = 'default';
					_.reloadLeftnav();
				}
			});
		}
	},
	setField: function(opt){
    		if (this.allMatching){
	    		$$('.dump').first().hide()
	    		this.allMatching = false;
	    		this.listOptions.all_matching = 1;
    		}else{
    			this.listOptions.all_matching = 0;
    		}
    		var _ = this;
    		if (opt){
    			var field = opt.split('.').first();
    			if (field.match(/delete/i)){
					if (!confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
						return;
					}
    			}
    			var value = opt.split('.').last();
    			var ids = this.serializeList();
			if (this.sourceScript && this.actionName){
				if (this.cleanUp){
					this.cleanUp();
				}

				$$('body')[0].style.cursor = 'wait';
				var searchOptions = Object.clone(this.listOptions);

				searchOptions.action = this.actionName;
				if (field.match(/delete/i)){
					searchOptions.cmd = field;
			}else{
					searchOptions.cmd = 'setField';
					searchOptions.field_name = field;
					searchOptions.field_value = value;
					searchOptions.maillist = '1';
					searchOptions.plugin = 'newsletter';
			}
				searchOptions['ids[]'] = ids;

				if (vivvo.admin.debugMode){
					searchOptions.dump_info = 1;
				}

			new Ajax.Updater('content', 'plugins.php?plugin=newsletter&maillist', {
					parameters: searchOptions,
					evalScripts: true,
					onComplete: function () {
						if (_.onComplete){
							_.onComplete();
						}
						$$('body')[0].style.cursor = 'default';
					}
				});
			}
    		}
    	}
});

var newsletter_tabs;
var adminList;
document.observe("dom:loaded",
	function(){
		newsletter_tabs = new Control.Tabs('newsletter_tab_list');
		adminList = new vivvo.admin.newsletter();
		$$(".info_help").each( function(link) {
			new Tooltip(link, {
				mouseFollow: false
			});
		});

	}
);