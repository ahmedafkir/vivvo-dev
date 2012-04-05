vivvo.admin.users = Class.create ();

Object.extend (vivvo.admin.users.prototype, vivvo.controls.list.prototype);

Object.extend (
	vivvo.admin.users.prototype,
	{
		initialize: function (){
			this.searchForm = 'users_search_form';
			this.orderForm = 'user_list_options_form';
			this.cleanUp = null;
			this.onComplete = null;
			this.sourceScript = 'users.php';
			this.actionName = 'user';

			this.groupPattern = {};
			this.groupPattern.editor = '{"MANAGE_TAGS": 1, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 1, "ACCESS_ADMIN": 1, "MANAGE_FILES": 0, "UPLOAD": 1, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [{"from": ["*"], "to": ["*"]}], "READ": {"Categories": [0]}, "WRITE": {"Categories": [0]}, "EDITOR": {"Categories": [0]}}';
			this.groupPattern.writer = '{"MANAGE_TAGS": 0, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 1, "MANAGE_FILES": 0, "UPLOAD": 0, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [], "READ": {"Categories": [0]}, "WRITE": {"Categories": [0]}, "EDITOR": {"Categories": []}}';
			this.groupPattern.trustedWriter = '{"MANAGE_TAGS": 1, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 1, "MANAGE_FILES": 0, "UPLOAD": 1, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [{"from": [0, 1], "to": [0, 1]}], "READ": {"Categories": [0]},"WRITE": {"Categories": [0]}, "EDITOR": {"Categories": []}}';
			this.groupPattern.member = '{"MANAGE_TAGS": 0, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 0, "MANAGE_FILES": 0, "UPLOAD": 0, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [], "READ": {"Categories": [0]}, "WRITE": {"Categories": []}, "EDITOR": {"Categories": []}}';
			this.groupPattern.premiumMember = '{"MANAGE_TAGS": 0, "MANAGE_TRASHBIN": 0, "MODERATE_COMMENTS": 0, "ACCESS_ADMIN": 0, "MANAGE_FILES": 0, "UPLOAD": 0, "ARTICLE_COMMENT": 1, "ARTICLE_VOTE": 1, "MANAGE_PLUGIN": [], "CHANGE_STATUS": [], "READ": {"Categories": [0]}, "WRITE": {"Categories": []}, "EDITOR": {"Categories": []}}';

			new Control.Tabs('users_tab_list');

			vivvo.mainNav.select('users');

			Event.observe('users_search_form','submit', this.usersAddSearchParams.bind(this));
			Event.observe('users_filter_form','submit', this.saveList.bind(this));
		},

		usersAddSearchParams: function () {
			this.listOptions.list_output = true;
			delete this.listOptions.group_output;
			this.addSearchParams();
		},

		goTo: function (param, value){
			this.onComplete = Prototype.emptyFunction;
			this.resetParams();
	        this.addParams({search_do_advanced: 1});
	        this.listOptions[param] = value;
	        this.listUpdate();
		},

		resetParams: function(){
			this.listOptions = {list_output: true};
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

					searchOptions.SECURITY_TOKEN = vivvo.token;
					searchOptions.action = this.actionName;
					if (field.match(/delete/i)){
						searchOptions.cmd = field;
					}else if (field.match(/addToGroup/i)){
						searchOptions.cmd = field;
						searchOptions.USER_group = value;
					}else if (field.match(/removeFromGroup/i)){
						searchOptions.cmd = field;
						searchOptions.USER_group = value;
	    			}else{
						searchOptions.cmd = 'setField';
						searchOptions.field_name = field;
						searchOptions.field_value = value;
	    			}
					searchOptions['ids[]'] = ids;

					if (vivvo.admin.debugMode){
						searchOptions.dump_info = 1;
					}

			        new Ajax.Updater('content', this.sourceScript, {
						parameters: searchOptions,
						evalScripts: true,
						onComplete: function () {
							_.selected_items = 0;
							if (_.onComplete){
								_.onComplete();
							}
							$$('body')[0].style.cursor = 'default';
						}
					});
				}
    		}
    	},

		loadGroupEdit: function (id){
			this.listOptions = {group_output: true};
			this.listOptions.search_gid = id;
			this.onComplete = this.onLoadGroupEdit;
	        this.listUpdate();
		},

		onLoadGroupEdit: function(){
			this.groupWizard = new Control.Tabs('group_main_tabs');
			this.onSubmitGroupEditHandle = this.onSubmitGroupEdit.bindAsEventListener (this);
			$('group_privileges').observe('submit', this.onSubmitGroupEditHandle);
			this.onAddGroupWorkflowHandle = this.onAddGroupWorkflow.bindAsEventListener (this);
			$('group_workflow_add').observe('click', this.onAddGroupWorkflowHandle);
			this.onRemoveGroupWorkflowHandle = this.onRemoveGroupWorkflow.bindAsEventListener (this);
			$$('#group_workflow_edit .remove_workflow').invoke('observe', 'click', this.onRemoveGroupWorkflowHandle);
			$$("#content .info_help").each( function(link) {
				new Tooltip(link, {
					mouseFollow: false
				});
			});
			this.onComplete = Prototype.emptyFunction;
		},

		wizardFirstStep: function () {
			if ($('wizard_method')){
				var method = $('wizard_method').value;
				if (method == ''){
					alert(vivvo.admin.lang.get('LNG_ADMIN_CHOOSE_METHOD'));
				}else if (method.match(/^duplicate\./)){
					var groupName = window.prompt(vivvo.admin.lang.get('LNG_ADMIN_NEW_GROUP_NAME'),"");
					if (groupName == ''){
						alert(vivvo.admin.lang.get('LNG_ADMIN_ENTER_GROUP_NAME'));
						return;
					}
					var params = {};
					params.action = 'vivvoCore';
					params.cmd = 'groupAdd';
					params.GROUP_name = groupName;
					params.GROUP_privileges = this.groupPattern[method.replace(/^duplicate\./,'')];

					vivvo.admin.utils.ajaxAction(params, this.onDeleteGroup.bind(this));
				}else if (method.match(/^apply\./)){
					this.applyPattern(method.replace(/^apply\./,''));
				}else if (method == 'continue'){
					this.groupWizard.next();
				}
			}
		},

		applyPattern: function (pattern) {
			var groupPattern = this.groupPattern[pattern].evalJSON();
			var checkBoxes = ['MANAGE_TAGS','MANAGE_TRASHBIN','MODERATE_COMMENTS','ACCESS_ADMIN','MANAGE_FILES','UPLOAD','ARTICLE_COMMENT','ARTICLE_VOTE'];
			var selectBoxed = ['READ','WRITE','EDITOR'];

			checkBoxes.each(
				function (box){
					if (groupPattern[box] == 1){
						$$('#group_privileges input[name=' + box + ']').first().checked = true;
					}
				}
			);

			selectBoxed.each(
				function (box){
					if (groupPattern[box].Categories.length > 0){
						$('priv_'+box.toLowerCase()).setValue(groupPattern[box].Categories).options[0].selected = false;
					} else {
						$('priv_'+box.toLowerCase()).options[0].selected = true;
					}
				}
			);

			$$('#group_privileges .change_status').each(
				function (div){
					var selects = div.select('select');
					if (groupPattern.CHANGE_STATUS.length > 0){
						selects[0].setValue(groupPattern.CHANGE_STATUS[0].from);
						selects[1].setValue(groupPattern.CHANGE_STATUS[0].to);
					}
				}
			)

			this.groupWizard.next();
		},

		onSubmitGroupEdit: function (){
			var privileges = $('group_privileges').serialize(true);
			privileges.CHANGE_STATUS = [];
			$$('#group_privileges .change_status').each(
				function (div){
					var selects = div.select('select');
					privileges.CHANGE_STATUS.push({from:selects[0].getValue(), to:selects[1].getValue()});
				}
			)
			privileges.READ = {};
			privileges.READ.Categories = $('priv_read').getValue().without('');

			privileges.WRITE = {};
			privileges.WRITE.Categories = $('priv_write').getValue().without('');

			privileges.EDITOR = {};
			privileges.EDITOR.Categories = $('priv_editor').getValue().without('');

			privileges.MANAGE_PLUGIN = privileges.MANAGE_PLUGIN.without('');

			var params = {};
			params.action = 'vivvoCore';
			if ($('GROUP_id').value){
				params.cmd = 'groupEdit';
			}else{
				params.cmd = 'groupAdd';
			}
			params.GROUP_id = $('GROUP_id').value;
			if ($('GROUP_name')){
				params.GROUP_name = $('GROUP_name').value;
			}
			params.GROUP_privileges = Object.toJSON(privileges);

			vivvo.admin.utils.ajaxAction(params, this.reloadGroups.bind(this));
		},

		onAddGroupWorkflow: function () {
			var oldNode = $$('#group_workflow_edit .change_status').last();
			var newNode = oldNode.cloneNode(true);
			newNode.select('select').each(function(node){node.selectedIndex = -1;});
			oldNode.insert({after: newNode});
			newNode.down('.remove_workflow').observe('click', this.onRemoveGroupWorkflowHandle);
		},

		onRemoveGroupWorkflow: function (e) {
			var el = Event.element(e);
			var changeStatusNode = el.up('.change_status');
			if ($('group_workflow_edit').select('.change_status').length == 1){
				changeStatusNode.select('select').each(function(node){node.selectedIndex = -1;});
			}else{
				changeStatusNode.remove();
			}
		},

		deleteGroup: function (id){
			if (confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
				var params = {};
				params.action = 'vivvoCore';
				params.cmd = 'groupDelete';
				params.GROUP_id = id;

				vivvo.admin.utils.ajaxAction(params, this.onDeleteGroup.bind(this));
			}
		},

		onDeleteGroup: function () {
			this.reloadGroups();
			this.goTo('search_user_type', '');
		},

		reloadGroups: function () {
			$$('body')[0].style.cursor = 'wait';
			new Ajax.Updater('group_list','users.php', {
				method: 'post',
				parameters: {group_list:1},
				evalScripts: true,
				onComplete: function (transport) {
					$$('body')[0].style.cursor = 'default';
				},
				insertion: Element.replace
			});
		}
	}
);

var adminList = {}, adminListOptions = [];

Event.observe(window, 'load', function() {

	adminList = new vivvo.admin.users();

	var listOptions = $A(adminListOptions);

	adminListOptions = {
		push: function(params) {
			Object.extend(adminList.listOptions, params);
		}
	};

	listOptions.each(adminListOptions.push);
});
