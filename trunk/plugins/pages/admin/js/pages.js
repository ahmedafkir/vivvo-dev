vivvo.admin.pages = Class.create ();

Object.extend (vivvo.admin.pages.prototype, vivvo.controls.list.prototype);

Object.extend (
	vivvo.admin.pages.prototype,
	{
		initialize: function (){
			this.searchForm = '';
			this.orderForm = '';
			this.sourceScript = 'plugins.php?plugin=pages';
			this.actionName = 'page';

			vivvo.mainNav.select('sub_plugin_pages').pinSubNav('sub_plugin_pages');

			this.onSubmitHandel = this.onSubmit.bindAsEventListener(this);
			this.initForm();
			this.onComplete = this.initForm;
			this.createSortable();
		},

		goTo: function (param, value){
			this.resetParams();
			this.addParams({search_do_advanced: 1});
			this.listOptions[param] = value;
			this.listUpdate();
		},

		onSubmit: function (e) {
			tinyMCE.triggerSave();
			var params = $('page_form_element').serialize(true);
			vivvo.admin.utils.ajaxAction(params,
				function(transport){
                    if (!transport.responseText.match(/error/)){
						if (params.cmd == 'add'){
							this.goTo('search_pid', 0);
						}
						this.leftNavUpdate();
					}
				}.bind(this)
			);
            Event.stop(e);            
        },

		deletePage: function (id){
    		if (confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
	    		if (id){
					var searchOptions = {};
					searchOptions.action = this.actionName;
					searchOptions.cmd = 'delete';
					searchOptions.PAGE_id = id;

					vivvo.admin.utils.ajaxAction(searchOptions,
						function(transport){
							if (!transport.responseText.match(/error/)){
								this.goTo('search_pid', 0);
								this.leftNavUpdate();
							}
						}.bind(this)
					);
	    		}
    		}
		},

		submitPageOrder: function(element){
			var params = {};
			params.action = 'page';
			params.cmd = 'reorder';
			params['page_list[]'] = Sortable.sequence(element);
			vivvo.admin.utils.ajaxAction(params);
		},

		cleanUp: function () {
			tinyMCE.execCommand('mceRemoveControl', false, 'page_body');
		},

		createSortable: function () {
			Sortable.create('page_list',{constraint: 'vertical', onUpdate: this.submitPageOrder.bind(this)});
		},

		initForm: function () {
			tinyMCE.execCommand('mceAddControl', false, 'page_body');
			$('page_form_element').observe('submit', this.onSubmitHandel);
		},

		leftNavUpdate: function() {
			Sortable.destroy('page_list');
			new Ajax.Updater('pages_quick_link', 'plugins.php?plugin=pages&leftnav=1', {
				parameters: {leftnav: 1},
				evalScripts: true,
				onComplete: this.createSortable.bind(this)
			});
		}
	}
);

var adminList = {};
document.observe("dom:loaded", function() {
	adminList = new vivvo.admin.pages();
});