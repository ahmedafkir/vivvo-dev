vivvo.admin.articles = Class.create();

Object.extend(vivvo.admin.articles.prototype, vivvo.controls.list.prototype);

Object.extend(vivvo.admin.articles.prototype, {

	initialize: function (){
		this.searchForm = 'article_search_form';
		this.orderForm = 'article_list_options_form';
		this.cleanUp = this.articleCleanUp.bind(this);
		this.onComplete = this.initSortable.bind(this);
		this.sourceScript = 'articles.php';
		this.actionName = 'article';
		this.modal = null;
		this.edit_modal = null;

		vivvo.mainNav.select('articles').pinSubNav();

		this.tabs = new Control.Tabs('articles_tab_list');
		this.tabs.setActiveTab('articles_search');

		this.initSortable();

		Event.observe('article_search_form','submit', this.addSearchParams.bind(this));
		Event.observe('article_filter_form','submit', this.saveList.bind(this));

        if ($('search_tag_name')) {
            new Ajax.Autocompleter('search_tag_name', 'autocomplete_choices', 'articles.php', {
                afterUpdateElement : function(text, li) {
                    $('search_tag_name').value = li.innerHTML.strip();
                },
                paramName: 'name',
                parameters: 'autocomplete_tags=1'
            });
        }
	},

	onListAction: function(action) {
		if (action == 'apply-tags') {
			this.applyTags(this.serializeList());
		} else if (action == 'mark-headline') {
			this.setField('applyTags.1:1,1:2', this.serializeList());
		} else {
			this.setField(action);
		}
	},

	articleCleanUp: function () {
		Sortable.destroy('article_list');
	},

	initSortable: function() {

		if (vivvo.user.is('EDITOR')) {
			Sortable.create('article_list',{tag: 'div', constraint: 'vertical', only: 'item', onUpdate: this.submitArticleOrder.bind(this)});
		}

		$$("#article_list .article_info").each(function(link) {
			new Tooltip(link, {mouseFollow: false});
		});
	},

	submitArticleOrder: function(element){
		var params = {};
		params.ajax_dump = 1;
		params.action = 'article';
		params.cmd = 'reorder';
		params['article_list[]'] = Sortable.sequence(element);
		vivvo.admin.utils.ajaxAction(params, this.initSortable.bind(this));

		$$('#article_list .item').each(function(item, index) {
			item.removeClassName('row_0').removeClassName('row_1').addClassName('row_' + (1 - index % 2));
		});
	},

	applyTags: function(ids) {

		if (ids.constructor !== Array || !ids.length) {
			return;
		}

		this.selected_ids = ids;

		if (!this.modal) {
			this.modal = new Control.Modal(null, {

				contents: $('apply_tags_modal').innerHTML,

				afterOpen: function() {

					this.textboxlist = textboxlistItems.setup(
						$('modal_container').down('input[name="apply_new_tags"]'),
						'article_edit.php?tags_feed', false,
						//window.default_topic_name || false
                        //hack by utvara #1116
                        'Keywords'
					);

					this.textboxlist.data = (this.textboxlist_data || []);
					this.textboxlist.qhist = (this.textboxlist_qhist || []);
					this.textboxlist.qcache = (this.textboxlist_qcache || []);

					$('modal_container').setStyle({overflow: 'visible'}).select('.maininput').invoke('focus');

					setTimeout(function() { this.modal.position(); }.bind(this), 1);

				}.bind(this),

				beforeClose: function() {

					this.textboxlist_qhist = this.textboxlist.qhist;
					this.textboxlist_qcache = this.textboxlist.qcache;
					this.textboxlist_data = this.textboxlist.data;

					this.textboxlist.selected_data.each(function(item) {
						this.textboxlist_data.push(item);
					}.bind(this));

					$('modal_container').setStyle({overflow: 'hidden'});

				}.bind(this)
			});
		}

		this.modal.open();
	},

	onApplyTags: function() {

		var tags = this.textboxlist.bits.values();
		this.modal.close();

		this.setField('applyTags.' + tags.join(','), this.selected_ids);
	},

	editTags: function(article_id) {

		this.editing_tags_of = article_id;

		if (!this.edit_modal) {
			this.edit_modal = new Control.Modal(null, {

				contents: function() {

					new Ajax.Updater('modal_container', 'articles.php?edit_tags', {

						method: 'get',

						parameters: {article_id: this.editing_tags_of},

						onComplete: function() {

							this.textboxlist = textboxlistItems.setup(
								$('modal_container').down('input[name="edit_article_tags"]'),
								'article_edit.php?tags_feed', false,
								//window.default_topic_name || false
                                //hack by utvara #1116
                                'Keywords'
                                
							);

							this.textboxlist.data = (this.textboxlist_data || []);
							this.textboxlist.qhist = (this.textboxlist_qhist || []);
							this.textboxlist.qcache = (this.textboxlist_qcache || []);

							$('modal_container').select('.maininput').invoke('focus');

							setTimeout(function() { this.edit_modal.position(); }.bind(this), 1);

						}.bind(this)
					});

					return '<div style="position:relative;width:170px;height:50px;margin-bottom:13px"><div id="filepicker_loading_indicator" /></div>';

				}.bind(this),

				afterOpen: function() {
					$('modal_container').setStyle({overflow: 'visible'});
				},

				beforeClose: function() {

					this.textboxlist_qhist = this.textboxlist.qhist;
					this.textboxlist_qcache = this.textboxlist.qcache;
					this.textboxlist_data = this.textboxlist.data;

					this.textboxlist.selected_data.each(function(item) {
						this.textboxlist_data.push(item);
					}.bind(this));

					textboxlistItems.dispose(this.textboxlist);
					this.textboxlist = null;

					$('modal_container').setStyle({overflow: 'hidden'});

				}.bind(this)
			});
		}

		this.edit_modal.open();
	},

	onEditTags: function() {

		var tags = this.textboxlist.bits.values();
		this.edit_modal.close();

		this.setField('editTags.' + tags.join(','), this.editing_tags_of);
	},

	purgeTrashbin: function() {
		if (confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_PURGE_TRASHBIN'))){
			var params = {
				SECURITY_TOKEN: vivvo.token,
				action: 'article',
				cmd: 'purge_trashbin'
			};

			Object.extend(params, this.listOptions);

			new Ajax.Updater('content', this.sourceScript, {
			   parameters: params,
			   evalScripts: true,
			   onComplete: function () {
					this.selected_items = 0;
				   this.onComplete && this.onComplete();
				   $$('body')[0].style.cursor = 'default';
			   }.bind(this)
		   });
		}
	},

	showStats: function(article_id) {

		this.stats_modal = new Control.Modal(null, {
			contents: function() {

				new Ajax.Updater('modal_container', 'articles.php?stats', {

					method: 'get',
					evalScripts: true,

					parameters: {article_id: article_id},

					onComplete: function() {
						setTimeout(function() { this.stats_modal.position(); }.bind(this), 1);
					}.bind(this)
				});

				return '<div style="position:relative;width:170px;height:50px;margin-bottom:13px"><div id="filepicker_loading_indicator" /></div>';

			}.bind(this)
		});

		this.stats_modal.open();
	}
});

var adminList = {}, adminListOptions = [];

Event.observe(window, 'load', function() {

	adminList = new vivvo.admin.articles();

	var listOptions = $A(adminListOptions);

	adminListOptions = {
		push: function(params) {
			Object.extend(adminList.listOptions, params);
		}
	};

	listOptions.each(adminListOptions.push);
});