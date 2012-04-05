vivvo.admin.assets = Class.create();
Object.extend(vivvo.admin.assets.prototype, vivvo.controls.list.prototype);
Object.extend(vivvo.admin.assets.prototype, vivvo.controls.fileUpload.prototype);

window.VIVVO_FILE_PICKER_LIST_VIEW = 0;
window.VIVVO_FILE_PICKER_THUMB_VIEW = 1;

Object.extend(vivvo.admin.assets.prototype, {

	selected_dir: 'files',

	initialize: function () {

		this.searchForm = 'file_search_form';
		this.orderForm = 'assets_list_options_form';
		this.cleanUp = null;
		this.sourceScript = 'assets.php';
		this.actionName = 'asset';
		this.selected_dir = window.selected_dir;

		vivvo.mainNav.select('assets');

		new Control.Tabs('assets_tab_list');

		this.options = {multiple: true, search_ext: vivvo.allowed_extensions};

		if (vivvo.user.can('UPLOAD')) {
			this.initFileUpload();
			$('file_upload_location').observe('change', this._selectDestination);
		}

		Event.observe('file_search_form', 'submit', this.addSearchParams.bind(this));

		var personal_last_view = vivvo.personalSetting.get('file_picker_view');

		if (typeof(personal_last_view) != 'object') {
			this.active_view = personal_last_view;
		} else {
			this.active_view = VIVVO_FILE_PICKER_LIST_VIEW;
		}

		this.initSwitch();
		this.selectDir(this.selected_dir);
	},

	initSwitch: function() {

		$$('#file_view a.file_view_list')[0].observe('click', (function(){
			this.switchView(VIVVO_FILE_PICKER_LIST_VIEW);
		}).bind(this));

		$$('#file_view a.file_view_thumb')[0].observe('click', (function(){
			this.switchView(VIVVO_FILE_PICKER_THUMB_VIEW);
		}).bind(this));

		this.switchView(this.active_view);
	},

	switchView: function(view) {

		$$('#file_view a').invoke('removeClassName', 'selected');
		$$('#file_view_contents > div').invoke('hide');

		if ((this.active_view = view) != VIVVO_FILE_PICKER_THUMB_VIEW) {
			$$('#file_view a.file_view_list')[0].addClassName('selected');
			$$('#file_list_view')[0].show();
		} else {
			$$('#file_view a.file_view_thumb')[0].addClassName('selected');
			$$('#thumb_list_view')[0].show();
		}

		vivvo.personalSetting.set('file_picker_view', view);
	},

	chekBoxChange: function(input) {

		if (this.allMatching) {
			$$('.dump').first().hide();
			this.allMatching = false;
			this.listOptions.all_matching = 0;
		}

		input = $(input);
		var checkbox;

		this.onSelectionChanged(checkbox = input.toggleClassName('selected').down('input[type="checkbox"]'));

		try {

			if (this.active_view == VIVVO_FILE_PICKER_THUMB_VIEW) {
				$(input.identify().replace('thumb', 'file')).toggleClassName('selected')
					.down('input[type="checkbox"]').checked = checkbox.checked;
			} else {
				$(input.identify().replace('file', 'thumb')).toggleClassName('selected')
					.down('input[type="checkbox"]').checked = checkbox.checked;
			}
		} catch (e) {}
	},

	selectAll: function(skip) {

		if (!skip && this.allMatching){
			$$('.dump').first().hide()
			this.allMatching = false;
			this.listOptions.all_matching = 0;
		}

		$$('#content .item, #content .thumb').each(function(item) {
			input = item.addClassName('selected').down('input[type="checkbox"]');
			if (!input.checked) {
				input.checked = true;
				this.onSelectionChanged(input);
			}
		}.bind(this));
	},

	invertSelection: function() {

		if (this.allMatching){
			$$('.dump').first().hide()
			this.allMatching = false;
			this.listOptions.all_matching = 0;
		}

		$$('#file_list_view input[type="checkbox"]').each(function(input) {
			input.checked = !input.checked;
			if (input.checked) {
				input.up('.item').addClassName('selected');
			} else {
				input.up('.item').removeClassName('selected');
			}
			this.onSelectionChanged(input);
		}.bind(this));

		$$('#thumb_list_view input[type="checkbox"]').each(function(input) {
			input.checked = !input.checked;
			if (input.checked) {
				input.up('.thumb').addClassName('selected');
			} else {
				input.up('.thumb').removeClassName('selected');
			}
			this.onSelectionChanged(input);
		}.bind(this));
	},

	serializeList: function() {
		var ids = [];
		$$('#file_list_view input[type="checkbox"]').each(function(input) {
			if (input.checked) {
				ids.push(input.value);
			}
		});
		return ids;
	},

	openModal: function(filename) {

		Control.Modal.open('&nbsp;');

		new Ajax.Updater('modal_container', 'assets.php', {
			method: 'get',
			parameters: {
				modal_output: 1,
				file: filename,
				search_dir: this.selected_dir
			},
			evalScripts: true,

			onComplete: function() {

				var container = $('modal_container'),
					preview = $('asset_preview');

				if (preview.match('.video')) {

					preview.update(this.openVideoPreview(preview.readAttribute('rel')));
				}

				Control.Modal.center(container);

			}.bind(this)
		});
	},

	onComplete: function () {
		this.selected_items = 0;
		this.initSwitch();
	},

	openVideoPreview: function (file) {

		var s1 = new SWFObject("../flash/mediaplayer.swf",'video_mediaplayer','300','237',"7");
		s1.addParam("allowfullscreen","true");
		s1.addVariable("width",'300');
		s1.addVariable("height",'237');
		s1.addVariable("wmode","transparent");
		s1.addVariable("displaywidth",'300');
		s1.addVariable("displayheight",'218');
		s1.addVariable("autoscroll","true");
		s1.addVariable('backcolor','0xEEEEEE');
		s1.addVariable('frontcolor','0x333333');
		s1.addVariable('lightcolor','0x333333');
		s1.addVariable('screencolor','0x000000');
		s1.addVariable('enablejs','true');
		s1.addVariable('file',file);
		return s1.getSWFHTML();
	},

	resetParams: function(){
		this.listOptions = {list_output: true};
	},

	setField: function(opt){
		var _ = this;
		if (opt){
			if (opt.match(/delete/i)){
				if (!confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
					return;
				}
			}
			if (opt.match(/rename/i)){
				var newname = prompt(vivvo.admin.lang.get('LNG_ADMIN_PROMPT_NEWNAME'));
				if (!newname){
					return;
				}
			}
			var ids = this.serializeList();
			if (this.sourceScript && this.actionName){
				if (this.cleanUp){
					this.cleanUp();
				}

				document.body.style.cursor = 'wait';
				var searchOptions = Object.clone(this.listOptions);

				searchOptions.SECURITY_TOKEN = vivvo.token;
				searchOptions.action = this.actionName;
				if (opt.match(/delete/i)){
					searchOptions.cmd = 'delete';
				} else if(opt.match(/rename/i)) {
					searchOptions.cmd = 'rename';
					searchOptions.FILE_newname = newname;
				} else {
					searchOptions.cmd = 'move';
					searchOptions.FILE_destination = opt;
				}

				searchOptions['FILE_filename[]'] = ids;

				if (vivvo.admin.debugMode){
					searchOptions.dump_info = 1;
				}

				new Ajax.Updater('content', this.sourceScript, {
					parameters: searchOptions,
					evalScripts: true,
					onComplete: function () {
						if (_.onComplete){
							_.onComplete();
						}
						document.body.style.cursor = 'default';
					}
				});

				this.showLoadingSign();
			}
		}
	},

	queueComplete: function(numFilesUploaded) {
		var status = document.getElementById("upload_divStatus");
		if (status) status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
		this.isUploading = false;
		this.selectDir('files/' + ((this.destination && this.destination.replace(/\/$/, '')) || '') );
	},

	showLoadingSign: function() {
		try {
			$$('#content .content_list_items')[0].insert({ top: '<div id="filelist_loading_sign"></div>' });
		} catch(e) {}
	},

	scanDir: function (dir) {
		var _ = this;
		var searchOptions = Object.clone(this.listOptions);

		searchOptions.SECURITY_TOKEN = vivvo.token;
		searchOptions.action = this.actionName;
		searchOptions.cmd = 'scan_dir';
		searchOptions.FILE_dir = dir;
		searchOptions.search_dir = dir;

		if (vivvo.admin.debugMode){
			searchOptions.dump_info = 1;
		}

		document.body.style.cursor = 'wait';

		new Ajax.Updater('content', this.sourceScript, {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				if (_.onComplete){
					_.onComplete();
				}
				document.body.style.cursor = 'default';
			}
		});

		this.showLoadingSign();
	},

	newDir: function () {
		var _ = this;
		var searchOptions = Object.clone(this.listOptions);

		searchOptions.SECURITY_TOKEN = vivvo.token;
		searchOptions.action = this.actionName;

		searchOptions.cmd = 'new_dir';
		searchOptions.leftside_output = 1;
		delete searchOptions.list_output;
		searchOptions.FILE_current = this.selected_dir;
		searchOptions.FILE_newdir = $('FILE_newdir').value;

		if (vivvo.admin.debugMode){
			searchOptions.dump_info = 1;
		}

		document.body.style.cursor = 'wait';

		new Ajax.Updater('navigation', this.sourceScript, {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
				if (_.onComplete){
					_.onComplete();
				}
				var dir = _.selected_dir;
				$$('#file_location .selected').invoke('removeClassName', 'selected');
				if ($('dir_' + dir + '/')){
					$('dir_' + dir + '/').addClassName('selected');
					$('dir_' + dir + '/').up('li').addClassName('selected');
				}
				new Control.Tabs('assets_tab_list');
				if (vivvo.user.can('UPLOAD')) {
					_.initFileUpload();
				}
				$('file_upload_location').observe('change', this.selectDestination.bindAsEventListener(this));
				Event.observe('file_search_form','submit', this.addSearchParams.bind(this));

			}.bind(this)
		});
	},

	deleteDir: function (dir) {
		if (!confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
			return;
		}

		var _ = this;
		var searchOptions = Object.clone(this.listOptions);

		searchOptions.SECURITY_TOKEN = vivvo.token;
		searchOptions.action = this.actionName;

		searchOptions.cmd = 'delete_dir';
		searchOptions.leftside_output = 1;
		delete searchOptions.list_output;
		searchOptions.FILE_rmdir = dir;

		if (vivvo.admin.debugMode){
			searchOptions.dump_info = 1;
		}

		document.body.style.cursor = 'wait';

		new Ajax.Updater('navigation', this.sourceScript, {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';
				if (_.onComplete){
					_.onComplete();
				}
				_.selectDir('files');

				new Control.Tabs('assets_tab_list');
				if (vivvo.user.can('UPLOAD')) {
					_.initFileUpload();
				}

				$('file_upload_location').observe('change', this.selectDestination.bindAsEventListener(this));
				Event.observe('file_search_form','submit', this.addSearchParams.bind(this));

			}.bind(this)
		});
	},

	selectDir: function (dir){
		if (dir.substr(dir.length - 1, 1) != '/') {
			dir += '/';
		}
		$$('#file_location .selected').invoke('removeClassName', 'selected');
		if ($('dir_' + dir)) {
			$('dir_' + dir).addClassName('selected').up('li').addClassName('selected');
		}
		if (dir == this.selected_dir) {
			return;
		}
		this.selected_dir = dir;
		this.goTo('search_dir', dir);
		this.showLoadingSign();
	},

	refreshDirList: function () {
		var _ = this;
		var searchOptions = Object.clone(this.listOptions);

		searchOptions.SECURITY_TOKEN = vivvo.token;
		searchOptions.action = this.actionName;

		searchOptions.cmd = '';
		searchOptions.leftside_output = 1;
		delete searchOptions.list_output;
		searchOptions.FILE_current = this.selected_dir;

		if (vivvo.admin.debugMode){
			searchOptions.dump_info = 1;
		}

		document.body.style.cursor = 'wait';

		new Ajax.Updater('navigation', this.sourceScript, {
			parameters: searchOptions,
			evalScripts: true,
			onComplete: function () {
				document.body.style.cursor = 'default';

				if (_.onComplete){
					_.onComplete();
				}
				var dir = _.selected_dir;
				$$('#file_location .selected').invoke('removeClassName', 'selected');
				if ($('dir_' + dir + '/')){
					$('dir_' + dir + '/').addClassName('selected');
					$('dir_' + dir + '/').up('li').addClassName('selected');
				}
				new Control.Tabs('assets_tab_list');
				if (vivvo.user.can('UPLOAD')) {
					_.initFileUpload();
				}
			}
		});
	},

	saveFileProperties: function() {
		var _ = this;

		if ($('file_properties_form')){
			$A($('file_properties_form').getElementsByTagName('input')).each(
				function (inp){
					_.listOptions[inp.name] = null;
				}
			)
			this.addParams($(this.searchForm).serialize(true));
			this.listUpdate();
		}
	}

});

var adminList = {};

Event.observe(window, 'load', function() {
	adminList = new vivvo.admin.assets();
});