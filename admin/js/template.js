vivvo.admin.templates = Class.create ();
		
Object.extend (vivvo.admin.templates.prototype,
	{
		listOptions: {
			list_output: true
		},
		initialize: function (){
			this.initLeftNav();
			if ($('template_edit_form')){
				this.onSubmitHandel = this.onSubmit.bindAsEventListener(this);
			}
		},
		
		initLeftNav: function (){
			if ($('temlpates_tab_list')){
				this.tabs = new Control.Tabs('temlpates_tab_list');
			}
			
			if ($('templates_templates')){
				new vivvo.controls.tree($('templates_templates').down('ul'));
			}
			if ($('templates_css')){
				this.tabs.setActiveTab('templates_css');
				new vivvo.controls.tree($('templates_css').down('ul'));
			}
			if ($('FILE_content_textarea')){
				CodePress.run();
			}
			if ($('template_edit_form')){
				this.onSubmitHandle = this.onSubmit.bindAsEventListener(this);
				$('template_edit_form').observe('submit', this.onSubmitHandle);
			}
		},
		
		onSubmit: function (e){
			$('FILE_content').value = FILE_content_textarea.getCode();
			params = $('template_edit_form').serialize(true);
			var _ = this;
			if (params.cmd == 'duplicate'){
                if (!params.FILE_new_filename) {
                    alert(vivvo.admin.lang.get('LNG_ADMIN_PLEASE_SELECT_NEW_FILE_NAME'));
                    return;
                }
                
				vivvo.admin.utils.ajaxAction(params, 
					function(transport){
						if (!transport.responseText.match(/error/)){
							_.listUpdate(params.FILE_dir, params.FILE_new_filename + '.' + params.FILE_extension);
							_.leftNavUpdate();
						}
					}
				);
			}else if (params.cmd == 'revert'){
				vivvo.admin.utils.ajaxAction(params, 
					function(transport){
						if (!transport.responseText.match(/error/)){
							_.listUpdate(params.FILE_dir, params.FILE_filename + '.' + params.FILE_extension);
						}
					}
				);
			}else if (params.cmd == 'delete'){
				vivvo.admin.utils.ajaxAction(params, 
					function(transport){
						if (!transport.responseText.match(/error/)){
							_.listUpdate('', '');
							_.leftNavUpdate();
						}
					}
				);
			}else{
				vivvo.admin.utils.ajaxAction(params);
			}
		},
		
		leftNavUpdate: function() {
			var url = document.location.toString();
			var _ = this;
			new Ajax.Updater('navigation', url, {
				parameters: {leftnav: 1},
				evalScripts: true,
				onComplete: function () { 
					_.initLeftNav();
				}
			});
		},
		
		listUpdate: function (dir, file){
			$$('body')[0].style.cursor = 'wait';
			this.listOptions.dir = dir;
			this.listOptions.file = file;
			var _ = this;
			new Ajax.Updater('content', 'templates.php', {
				parameters: this.listOptions,
				evalScripts: true,
				onComplete: function () { 
					$$('body')[0].style.cursor = 'default';
					if ($('FILE_content_textarea')){
						CodePress.run();
					}
					if ($('template_edit_form')){
						_.onSubmitHandle = _.onSubmit.bindAsEventListener(_);
						$('template_edit_form').observe('submit', _.onSubmitHandle);
					}
				}
			});
    	}
	}
);

var adminTemplate = {};

document.observe("dom:loaded", function() {
	adminTemplate = new vivvo.admin.templates();
});