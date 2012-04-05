vivvoImageGallery = Class.create ();

Object.extend (vivvoImageGallery.prototype,
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
			Event.observe($('article_image_gallery_apply'), 'click', this.applyChengesObserver);
			this.initSortable();
			this.selected = '';
			this.imagePicker = new vivvo.controls.filePicker('need_help_gallery',
				{
					search_ext: 'jpg,jpeg,gif,png',
					search_dir: 'files/',
					dir_tree_root: 'files/',
					upload: true,
					onSelect: this.fileAdd.bind(this),
					multiple: true,
					butonLabel: 'Add images',
					confirmMessage: 'Add selected image(s)',
					noneSelected: 'No image(s) selected'
				}
			);
		},

		fileAdd: function (file){
			var _ = this;
			Sortable.destroy('image_holder');

			$$('#image_holder .image_sortable').each(
				function (span){
					Event.stopObserving(span, 'dblclick', _.onClickObserver);
				}
			);

			var listParam = 'ajax_dump=1&action=article_images';
			listParam += '&cmd=add';

			file.each(
				function (f, index){
					listParam += '&PLUGIN_IMAGE_GALLERY_data['+index+'][article_id]=' + _.options.article_id;
					listParam += '&PLUGIN_IMAGE_GALLERY_data['+index+'][real_path]=' + f.replace(/^files\//,'');
				}
			);

			var reloadOptions = {};
			reloadOptions.plugin = 'image_gallery';
			reloadOptions.search_id = this.options.article_id;

			new Ajax.Updater($$('.dump')[0],'ajax.php', {
				method: 'post',
				postBody: listParam,
				evalScripts: true,
				onComplete: function () {
					new Ajax.Updater('image_gallery_holder', 'plugins.php', {
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
				this.selected.title = $('article_image_gallery_description').value;
				this.selected.down('span').nextSiblings()[0].title = $('article_image_gallery_caption').value;

				var listParam = {};
				listParam.action = 'article_images';
				listParam.cmd = 'editImage';
				listParam.ajax_dump = 1;
				listParam.PLUGIN_IMAGE_GALLERY_id = this.selected.id.replace(/^article_image_gallery_/,'');
				listParam.PLUGIN_IMAGE_GALLERY_title = $('article_image_gallery_caption').value;
				listParam.PLUGIN_IMAGE_GALLERY_description = $('article_image_gallery_description').value;

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
			if (element.nodeName == 'IMG') element = element.up('span').up('span');
			if (!confirm(vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
				return false;
			}
			if (element){
				var _ = this, listParam = {};
				listParam.action = 'article_images';
				listParam.cmd = 'remove';
				listParam.ajax_dump = 1;
				listParam.PLUGIN_IMAGE_GALLERY_id = element.id.replace(/^article_image_gallery_/,'');

				var reloadOptions = {};
				reloadOptions.plugin = 'image_gallery';
				reloadOptions.search_id = this.options.article_id;

				new Ajax.Updater($$('.dump')[0],'ajax.php', {
					method: 'post',
					parameters: listParam,
					evalScripts: true,
					onComplete: function () {
						new Ajax.Updater('image_gallery_holder', 'plugins.php', {
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
			if (span.nodeName == 'IMG') span = span.up('span');
			span.siblings().invoke('removeClassName', 'selected');
			span.addClassName('selected');
			if (span && span.title){
				$('article_image_gallery_description').value = span.title;
			}else{
				$('article_image_gallery_description').value = '';
			}
			if (span.down('img') && span.down('img').title){
				$('article_image_gallery_caption').value = span.down('span').nextSiblings()[0].title;
			}else{
				$('article_image_gallery_caption').value = '';
			}
			this.selected =  span;
		},

		reorder: function () {
			var listParam = 'ajax_dump=1&action=article_images';
			listParam += '&cmd=reorder&';
			listParam += Sortable.serialize('image_holder', {tag: 'span', name: 'PLUGIN_IMAGE_GALLERY_order'});
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
			Sortable.create('image_holder',
				{
					tag:'span',
					constraint: false,
					overlap:'horizontal',
					only: 'image_sortable',
					onUpdate : this.reorder.bind(this)
				}
			);

			$$('#image_holder .image_sortable').each(
				function (span){
					Event.observe(span, 'dblclick', _.onClickObserver);
				}
			);
			$$('#image_holder .image_delete').each(function(elem){
				Event.observe(elem, 'click', this.removeImage.bindAsEventListener(this));
			}, this);
		}
	}
);