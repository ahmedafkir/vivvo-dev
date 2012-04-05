vivvo.controls.filePicker = Class.create ();

/*@cc_on window.onerror = function(){ return true; }; @*/

window.VIVVO_FILE_PICKER_LIST_VIEW = 0;
window.VIVVO_FILE_PICKER_THUMB_VIEW = 1;

Object.extend (vivvo.controls.filePicker.prototype, vivvo.controls.fileUpload.prototype);

Object.extend (vivvo.controls.filePicker.prototype,
	{
		initialize: function (link, options){

			this.options = {
				inputId: false,
				onSelect: this.onSelect.bind(this),
				pg : 1,
				relative: false,
				search_dir : 'files/',
				upload_destination : '',
				dir_tree_root: 'files/',
				search_ext: vivvo.allowed_extensions,
				multiple: false,
				upload: false,
				select: true,
				butonLabel: 'Add',
				confirmMessage: 'Add selected files',
				noneSelected: 'No file(s) selected.',
				active_view: VIVVO_FILE_PICKER_LIST_VIEW,
				onFileSelected: function(){}
			};

			var personal_last_view = vivvo.personalSetting.get('file_picker_view');

			if (typeof(personal_last_view) != 'object') {
				options.active_view = personal_last_view;
			}

			this.destination = '';

			this.isUploading = false;

			this.selected = [];

			Object.extend(this.options, options || {});

			if (!vivvo.user.can('UPLOAD')) this.options.upload = false;

			this.modal = new Control.Modal(link, {
				contents: this.create.bind(this),
				afterOpen: this.openModal.bind(this),
				beforeClose: this.closeModal.bind(this)
			});

		},

		onSelect: function (file){
			if (file){
				if (this.options.relative){
					file = file.replace(new RegExp('^' + this.options.dir_tree_root), '');
				}
				if (this.options.inputId) {
					$(this.options.inputId).value = file;
				}
				this.options.onFileSelected(file);
			}
			$$('div.tooltip').invoke('remove');
		},

		openModal: function(){
			$('modal_container').update('<div style="position:relative;width:170px;height:50px;margin-bottom:13px"><div id="filepicker_loading_indicator" /></div>');
		},

		closeModal: function(){
			if (this.isUploading){
				throw $break;
			}
		},

		initFilePicker: function (){
			this.selected = [];
			var _ = this;

			var file_picker = $('file_picker');

			//this.openModal();
			if (this.options.select){
				file_picker.select('.page_button').each(
					function (span){
						Event.observe(span, 'click', _.onPageChange.bind(_));
					}
				);
				file_picker.select('.dir').each(
					function (span){
						Event.observe(span, 'click', _.onDirChange.bind(_));
						span.up('li').removeClassName('selected');
						span.removeClassName('selected');
					}
				);

				file_picker.select('.selectable').each(
					function (elem) {

						elem.observe('click', this.onFileSelect.bind(this));

						if (!this.options.multiple) {
							elem.observe('dblclick', this.onFilePick.bind(this));
						}

						setTimeout(function(){
							new Tooltip(elem, {
								mouseFollow: false,

								onShow: function(tooltip){

									if (tooltip.loadedExtraInfo) return;

									tooltip.loadedExtraInfo = true;

									new Ajax.Request('ajax.php', {
										method: 'get',
										parameters: {
											file_tooltip: 1,
											file_id: elem.className.match(/(?:\s|^)id_(\d+)(?:\s|$)/)[1] * 1
										},
										evalScripts: false,
										onSuccess: function(response) {
											tooltip.setContent(response.responseText);
										}
									});
								}
							});
						}, 1);

					}.bind(this)
				);

				file_picker.select('a.file_view_list')[0].observe('click', (function(){

					this.switchView(VIVVO_FILE_PICKER_LIST_VIEW);

				}).bind(this));

				file_picker.select('a.file_view_thumb')[0].observe('click', (function(){

					this.switchView(VIVVO_FILE_PICKER_THUMB_VIEW);

				}).bind(this));

				this.switchView(this.options.active_view);

				if ($('dir_' + this.options.search_dir)){
					$('dir_' + this.options.search_dir).up('li').addClassName('selected');
					$('dir_' + this.options.search_dir).addClassName('selected');
				}

				$('file_insert').update(this.options.butonLabel);
				Event.observe($('file_insert'), 'click', _.onFileInsert.bind(_));
				Event.observe($('picker_close'), 'click', _.onClose.bind(_));
			}else{
				$('file_picker_holder').hide();
			}

			$$('#file_picker_tabs a').each(function(tab){
				tab.observe('click', function(e){
					if (!tab.hasClassName('active')){
						var active = $(tab.up('ul').select('.active')[0]);
						var href = active.removeClassName('active').readAttribute('href');
						$(href.substr(href.indexOf('#') + 1)).hide();
						href = tab.addClassName('active').readAttribute('href');
						$(href.substr(href.indexOf('#') + 1)).show();
					}
					e.preventDefault();
					return false;
				});
			});

			if (this.options.upload){
				if (!this.options.multiple){
					this.initFileUpload(this.options.upload_destination, 1);
				}else{
					this.initFileUpload(this.options.upload_destination);
				}
				var _ = this;

				var listParam = {};
				listParam.upload_dialog = 1;
				listParam.dir_tree_root = this.options.dir_tree_root;

				$('file_upload_holder').show().hide();
				Event.observe($('file_upload_location'), 'change', _._selectDestination);
			}else{
				this.swfUploader = false;
				$('file_upload_holder').hide();
			}
			this.modal.position();
		},

		switchView: function(view) {

			$$('#file_view a').invoke('removeClassName', 'selected');
			$$('#file_view_contents > div').invoke('hide');

			if ( (this.options.active_view = view) != VIVVO_FILE_PICKER_THUMB_VIEW ) {
				$$('#file_picker a.file_view_list')[0].addClassName('selected');
				$$('#file_list')[0].show();
			} else {
				$$('#file_picker a.file_view_thumb')[0].addClassName('selected');
				$$('#thumb_list')[0].show();
			}

			vivvo.personalSetting.set('file_picker_view', view);
		},

		create: function (){
			this.reLoad();
			return '';
		},

		onPageChange: function (e) {
			if (e) el = Event.element(e);
			if (el) this.options.pg = el.id.replace(/page_button_/,'');
			this.reLoad();
		},

		onDirChange: function (e) {
			if (e) el = Event.element(e);
			if (el){
				this.options.search_dir = el.id.replace(/^dir_/,'');
				this.options.pg = 1;
			}
			this.reLoad();
		},

		uploadSuccess: function(file, serverData) {
            if (serverData.match(/^Fail:/)){
                try {
                    var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
                    progress.setError();
                    progress.setStatus("Failed.");
                    progress.toggleCancel(false);

                } catch (ex) {

                }
                alert(serverData.replace(/^Fail:/, ''));
            }else{
                this.selected.push(serverData);
                if (!this.options.multiple){
                    this.isUploading = false;
                    this.onFileInsert();
                }else{
                    try {
                        var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
                        progress.setComplete();
                        progress.setStatus("Complete.");
                        progress.toggleCancel(false);

                    } catch (ex) {

                    }
                }
            }
		},

		uploadComplete: function(file) {
		},

		queueComplete: function(numFilesUploaded) {
			var status = document.getElementById("upload_divStatus");
			if (status) status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
			this.isUploading = false;
			if (this.options.multiple){
				this.onFileInsert();
			}
		},

		onClose: function(e){
			this.selected = [];
			this.modal.close();

		},

		onFileInsert: function (e){
			if (this.selected.length > 0){
				if (!this.options.multiple){
					this.options.onSelect(this.selected[0]);
				}else{
					this.options.onSelect(this.selected.uniq());
				}
				this.selected = [];
				this.modal.close();
			}else{
				alert(this.options.noneSelected);
			}
		},

		onFileSelect: function (e) {
			var el = Event.element(e);

			if (el && !el.hasClassName('selectable')) {
				el = el.up('.selectable');
			}

			if (el){
				if (!this.options.multiple){
					$$('#file_picker .selectable').invoke('removeClassName', 'selected');
					this.selected = [el.id.replace(/^(file|thumb)_/, '')];
					$$('#file_picker .' + el.id.replace(/^(file|thumb)/, '').replace(/[^a-z0-9_\-]/ig, '-')).invoke('addClassName', 'selected');
				}else{
					if (el.hasClassName('selected')){
						this.selected = this.selected.without(el.id.replace(/^(file|thumb)_/, ''));
						$$('#file_picker .' + el.id.replace(/^(file|thumb)/, '').replace(/[^a-z0-9_\-]/ig, '-')).invoke('removeClassName', 'selected');
					}else{
						this.selected.push(el.id.replace(/^(file|thumb)_/, ''));
						el.addClassName('selected');
						$$('#file_picker .' + el.id.replace(/^(file|thumb)/, '').replace(/[^a-z0-9_\-]/ig, '-')).invoke('addClassName', 'selected');
					}
				}
			}
		},

		onFilePick: function (e){
			if (e) el = Event.element(e);

			if (el && !el.hasClassName('selectable')) {
				el = el.up('.selectable');
			}

			if (el){
				$$('#file_picker .selectable').invoke('removeClassName', 'selected');
				this.selected = [el.id.replace(/^(file|thumb)_/, '')];
				el.addClassName('selected');
				this.onFileInsert();
			}
		},

		reLoad: function(){
			if (this.selected.length > 0 && this.options.multiple){
				if (confirm(this.options.confirmMessage)){
					if (!this.options.multiple){
						this.options.onSelect(this.selected[0]);
					}else{
						this.options.onSelect(this.selected);
					}
				}
			}

			listParam = {};
			listParam.file_picker = 1;
			listParam.pg = this.options.pg;
			listParam.search_ext = this.options.search_ext;
			listParam.search_dir = this.options.search_dir;
			listParam.dir_tree_root = this.options.dir_tree_root;

			var quick_search_field = $('filepicker_quick_searchbox');

			if(quick_search_field && quick_search_field.value)
				listParam.search_all_quick = quick_search_field.value;

			var file_box = $('file_view_contents');

			if (file_box) {
				file_box.update('<div id="filepicker_loading_container"><div id="filepicker_loading_indicator" /></div>');
			}

			new Ajax.Updater('modal_container', 'ajax.php', {
				method: 'get',
				parameters: listParam,
				evalScripts: true,
				onComplete: function () {
					this.initFilePicker();
					for (var i = 0; i < 3; i++) this.modal.position(); // ugly hack for ie :/
					$('filepickerQuickSearchbtn').observe('click', function(){ this.reLoad(); }.bind(this));
				}.bind(this)
			});

		}
	}
);
