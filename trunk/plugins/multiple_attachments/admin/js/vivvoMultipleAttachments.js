vivvoMultipleAttachments = Class.create ();

Object.extend (vivvoMultipleAttachments.prototype,
	{
		initialize: function (holder, options){
			this.options = {
				article_id: 0
			};
			Object.extend(this.options,options || {});
			this.element = $(holder);
			this.onClickObserver = this.onClick.bindAsEventListener(this);
			this.applyChengesObserver = this.applyChenges.bindAsEventListener(this);
			this.removeImageObserver = this.removeImage.bindAsEventListener(this);
			Event.observe($('article_multiple_attachments_apply'), 'click', this.applyChengesObserver);
			this.initSortable();
			this.selected = '';
			this.attachmentPicker = new vivvo.controls.filePicker('need_help_attachmens',
				{
					//search_ext: '',
					search_dir: 'files/',
					dir_tree_root: 'files/',
					upload: true,
					onSelect: this.fileAdd.bind(this),
					multiple: true,
					butonLabel: 'Add attachments',
					confirmMessage: 'Add selected attachment(s)',
					noneSelected: 'No attachment(s) selected'
				}
			);
		},

		fileAdd: function (file){
			var _ = this;
			Sortable.destroy('attachment_holder');

			$$('#attachment_holder .attachment_sortable').each(
				function (span){
					Event.stopObserving(span, 'dblclick', _.onClickObserver);
				}
			);

			var listParam = 'ajax_dump=1&action=article_attachments';
			listParam += '&cmd=add';

			file.each(
				function (f, index){
					listParam += '&PLUGIN_MULTIPLE_ATTACHMENTS_data['+index+'][article_id]=' + _.options.article_id;
					listParam += '&PLUGIN_MULTIPLE_ATTACHMENTS_data['+index+'][real_path]=' + f.replace(/^files\//,'');
				}
			);

			var reloadOptions = {};
			reloadOptions.plugin = 'multiple_attachments';
			reloadOptions.search_id = this.options.article_id;

			new Ajax.Updater($$('.dump')[0],'ajax.php', {
				method: 'post',
				postBody: listParam,
				evalScripts: true,
				onComplete: function () {
					new Ajax.Updater('multiple_attachments_holder', 'plugins.php', {
						method: 'get',
						parameters : reloadOptions,
						onComplete: _.initSortable.bind(_)
					});
				},
				insertion: 'bottom'
			});
		},

		applyChenges: function (e){
			if (this.selected){
				this.selected.title = $('article_multiple_attachments_description').value;
				this.selected.down('.item_data').title = $('article_multiple_attachments_caption').value;
				this.selected.down('.title_holder').update($('article_multiple_attachments_caption').value);
				var listParam = {};
				listParam.action = 'article_attachments';
				listParam.cmd = 'editAttachment';
				listParam.ajax_dump = 1;
				listParam.PLUGIN_MULTIPLE_ATTACHMENTS_id = this.selected.id.replace(/^article_multiple_attachments_/,'');
				listParam.PLUGIN_MULTIPLE_ATTACHMENTS_title = $('article_multiple_attachments_caption').value;
				listParam.PLUGIN_MULTIPLE_ATTACHMENTS_description = $('article_multiple_attachments_description').value;

				new Ajax.Updater($$('.dump')[0],'ajax.php', {
					method: 'post',
					parameters: listParam,
					evalScripts: true,
					insertion: 'bottom'
				});
			}
		},

		removeImage: function (e) {
			element = Event.element(e);
			if (element.nodeName == 'IMG') element = element.up('span').up('div').up('div');
			if (!confirm(vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
				return false;
			}
			if (element){
				var _ = this, listParam = {};
				listParam.action = 'article_attachments';
				listParam.cmd = 'remove';
				listParam.ajax_dump = 1;
				listParam.PLUGIN_MULTIPLE_ATTACHMENTS_id = element.id.replace(/^article_multiple_attachments_/,'');

				var reloadOptions = {};
				reloadOptions.plugin = 'multiple_attachments';
				reloadOptions.search_id = this.options.article_id;

				new Ajax.Updater($$('.dump')[0],'ajax.php', {
					method: 'post',
					parameters: listParam,
					evalScripts: true,
					onComplete: function () {
						new Ajax.Updater('multiple_attachments_holder', 'plugins.php', {
							method: 'get',
							parameters : reloadOptions,
							onComplete: _.initSortable.bind(_)
						});
					},
					insertion: 'bottom'
				});
			}
		},


		onClick: function (e) {
			var span = Event.element(e);
			this.selected = '';
			if (!span.hasClassName('item')) span = span.up('.item');
			span.siblings().invoke('removeClassName', 'selected');
			span.addClassName('selected');
			if (span && span.title){
				$('article_multiple_attachments_description').value = span.title;
			}else{
				$('article_multiple_attachments_description').value = '';
			}
			if (span.down('.item_data') && span.down('.item_data').title){
				$('article_multiple_attachments_caption').value = span.down('.item_data').title;
			}else{
				$('article_multiple_attachments_caption').value = '';
			}
			this.selected =  span;
		},

		reorder: function () {
			var listParam = 'ajax_dump=1&action=article_attachments';
			listParam += '&cmd=reorder&';
			listParam += Sortable.serialize('attachment_holder', {tag: 'div', name: 'PLUGIN_MULTIPLE_ATTACHMENTS_order'});
			new Ajax.Updater($$('.dump')[0],'ajax.php', {
				method: 'post',
				postBody: listParam,
				evalScripts: true,
				insertion: 'bottom'
			});
		},

		initSortable: function() {
			var _ = this;
			this.selected = '';
			Sortable.create('attachment_holder',
				{
					tag:'div',
					only: 'attachment_sortable',
					onUpdate : this.reorder.bind(this)
				}
			);

			$$('#attachment_holder .attachment_sortable').each(
				function (span){
					Event.observe(span, 'dblclick', _.onClickObserver);
				}
			);

			$$('#attachment_holder .image_delete').each(function(elem){
				Event.observe(elem, 'click', this.removeImage.bindAsEventListener(this));
			}, this);
		}
	}
);