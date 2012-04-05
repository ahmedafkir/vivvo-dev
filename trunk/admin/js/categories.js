vivvo.admin.categories = Class.create();
Object.extend (vivvo.admin.categories.prototype, {

	initialize: function() {

		this.refreshTooltips();

		this.tabs = new Control.Tabs('categories_tab_list');

		vivvo.mainNav.select('categories');

		new vivvo.controls.tree('category_tree_0', true);
		$('category_tree_0').select('.draggable').each(
			function (span){
				Event.observe(span, 'click',
					function(e){
						var el = Event.element(e);
						if (el.id){
							var id = el.id.replace(/category_dd_/,''), view = 'category_form';

							$$('body')[0].setStyle({cursor: 'wait'});

							if (el.match('.link')) {
								view = 'link_form';
							}

							new Ajax.Updater('content', 'categories.php?' + view, {
								parameters: {search_id: id},
								evalScripts: true,
								onComplete: view == 'link_form' ?
									this.initLinkForm.bindAsEventListener(this) :
									function () {
										$$('body')[0].setStyle({cursor: 'default'});
									}
							});
						}
					}.bind(this)
				);
			}.bind(this)
		);

		$$(".info").each(function(link) { new Tooltip(link, {mouseFollow: true}); });

		if ($('CATEGORY_redirect')) {
			this.initLinkForm();
		}
	},

	refreshTooltips: function() {
		$$(".info_help").each( function(link) {
			new Tooltip(link, {
				mouseFollow: false
			});
		});
	},

	addNewLink: function() {
		$$('body')[0].setStyle({cursor: 'wait'});
		new Ajax.Updater('content', 'categories.php?link_form', {
			evalScripts: true,
			onComplete: this.initLinkForm.bindAsEventListener(this)
		});
	},

	initLinkForm: function() {

		this.refreshTooltips();

		new Ajax.Autocompleter('CATEGORY_redirect', 'autocomplete_choices', 'categories.php', {
			afterUpdateElement : function(text, li) {
				$('CATEGORY_name').value = li.down('.title').innerHTML.strip();
				$('CATEGORY_redirect').value = li.title;
			},
			paramName: 'name',
			parameters: 'autocomplete_links=1'
		});

		$$('body')[0].setStyle({cursor: 'default'});
	}
});

window.adminTopics = {

	topics: null,
	content: null,

	topic_list_params: {},

	url: 'categories.php',

	initialize: function() {

		this.topics = $('topics');
		this.content = $('content');

		$$(".info_help").each(function(link) {new Tooltip(link, {mouseFollow: false}); });

		/[\?&]tag_list[=&]/.test(location.href) && window.tagList.initialize();
	},

	addNewTopic: function() {
		this.editTopic(-1);
	},

	editing_topic_id: -1,

	editTopic: function(id) {

		this.content.setStyle({cursor: 'wait'});

		new Ajax.Updater(this.content, this.url + '?topic_form', {
			method: 'get',
			parameters: {
				search_id: this.editing_topic_id = id
			},
			onComplete: function(){
				this.content.setStyle({cursor: 'default'});
				$$(".info_help").each( function(link) {
					new Tooltip(link, {
						mouseFollow: false
					});
				});
			}.bind(this)
		});
	},

	deleteTopic: function(id) {

		if (confirm(vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){

			this.topics.setStyle({cursor: 'wait'});

			new Ajax.Updater(this.topics, this.url + '?topics', {
				method: 'post',
				parameters: this.topic_params({
					TAG_GROUP_id: id,
					SECURITY_TOKEN: vivvo.token,
					action: 'tag',
					cmd: 'deleteGroup'
				}),
				onComplete: function(){
					vivvo.admin.utils.addDump(vivvo.admin.lang.get('LNG_INFO_TAG_GROUP_DELETE_SUCCESS'));
					this.topics.setStyle({cursor: 'default'});

					if (this.editing_topic_id == id && id >= 0) {
						this.addNewTopic();
					}

				}.bind(this)
			});
		}
	},

	gotoTopicPage: function(pg) {

		this.topics.setStyle({cursor: 'wait'});

		this.topic_list_params.pg = pg;

		new Ajax.Updater(this.topics, this.url + '?topics', {
			method: 'get',
			parameters: this.topic_list_params,
			onComplete: function() {
				this.topics.setStyle({cursor: 'default'});
				var input = $('input_pg');
				if (input) {
					input.value = pg;
				}
			}.bind(this)
		});
	},

	loadTopicTags: function(id) {

		this.content.setStyle({cursor: 'wait'});

		new Ajax.Updater(this.content, this.url + '?tag_list', {
			method: 'get',
			parameters: {
				topic_id: id
			},
			onComplete: function(){
				this.content.setStyle({cursor: 'default'});
				tagList.initialize(id);
			}.bind(this)
		});
	},

	search: function(params) {

		if ('search_topic_id' in params) {
			if (!(params.search_topic_id instanceof Array)) {
				params.search_topic_id = (params.search_topic_id + '').split(',');
			}

			if ($A(params.search_topic_id).include(-1)) {
				delete params.search_topic_id;
			} else {
				params.search_topic_id = $A(params.search_topic_id).without(-1).join(',');
			}
		}

		params.search_form = 1;

		this.content.setStyle({cursor: 'wait'});

		new Ajax.Updater(this.content, this.url + '?tag_list', {
			method: 'get',
			parameters: params,
			onComplete: function(){
				this.content.setStyle({cursor: 'default'});
				$$(".info_help").each( function(link) {
					new Tooltip(link, {
						mouseFollow: false
					});
				});
				tagList.initialize();
			}.bind(this)
		});
	},

	topic_params: function(params) {

		Object.extend(params, this.topic_list_params);
		return params;
	}
};

window.tagList = {

	allMatching: false,
	listOptions: {},
	modal: null,
	selected_items: 0,

	initialize: function(topic_id, undefined) {

		if (topic_id !== undefined) {
			this.listOptions.topic_id = topic_id;
		}

		this.selected_items = 0;
	},

	onAction: function(action, ids, select) {

		ids = ids || this.serializeList();

		if (!ids.length) {
			if (select) {
				select.selectedIndex = 0;
			}
			return;
		}

		if (action == 'delete') {
			if (confirm(vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))) {

				this.reloadList({
					SECURITY_TOKEN: vivvo.token,
					action: 'tag',
					cmd: 'delete',
					TAG_id: ids.join(','),
					tag_list: 1
				});
			}
		}

		if (select) {
			select.selectedIndex = 0;
		}
	},

	deleteItem: function(id) {
		this.onAction('delete', [id]);
	},

	addOrderParams: function(order) {

		var param = order.split('.');

		if (param.length == 2) {
			this.listOptions.search_sort_by = param[0];
			this.listOptions.search_order = param[1];
			this.reloadList();
		}
	},

	addLimitParams: function(limit) {

		if (limit) {
			this.listOptions.search_limit = limit;
			this.reloadList();
		}
	},

	reloadList: function(params) {
		this.updateList(this.listOptions.pg, params);
	},

	updateList: function(pg, params) {

		if (!this.setAllMatching(1)) {
			this.listOptions.all_matching = 0;
		}

		if (params) {
			params.search_topic_id = this.listOptions.topic_id;
		}

		var searchOptions = {};
		Object.extend(searchOptions, this.listOptions);
		Object.extend(searchOptions, params || {});
		Object.extend(searchOptions, {pg: pg || 1});

		adminTopics.content.setStyle({cursor: 'wait'});

		new Ajax.Updater(adminTopics.content, adminTopics.url + '?tag_list', {
			method: params ? 'post' : 'get',
			evalScripts: true,
			parameters: searchOptions,
			onComplete: function() {
				tagList.selected_items = 0;
				adminTopics.content.setStyle({cursor: 'default'});
			}
		});
	},

	gotoPage: function(pg, topic_id) {

		var searchOptions = {};
		Object.extend(searchOptions, this.listOptions);
		Object.extend(searchOptions, {pg: pg || 1, topic_id: topic_id || 0});

		adminTopics.content.setStyle({cursor: 'wait'});

		new Ajax.Updater(adminTopics.content, adminTopics.url + '?tag_list', {
			method: 'get',
			evalScripts: true,
			parameters: searchOptions,
			onComplete: function() {
				tagList.selected_items = 0;
				adminTopics.content.setStyle({cursor: 'default'});
			}
		});
	},

	addDump: function (message, type, error, additional) {
		vivvo.admin.utils.addDump(message, type, error, additional);
	},

	setAllMatching: function(value) {
		if (this.allMatching) {
			$$('.dump').first().hide();
			this.allMatching = false;
			this.listOptions.all_matching = value << 0;
			return true;
		}
		return false;
	},

	onSelectionChanged: function(input) {
		if (input.checked) {
			if (!this.selected_items++) {
				$$('select.action-select').each(function(select) {
					$(select).removeClassName('none_selected').disabled = false;
				});
			}
		} else if (!--this.selected_items) {
			$$('select.action-select').each(function(select) {
				$(select).addClassName('none_selected').disabled = true;
			});
		}
	},

	chekBoxChange: function (input){
		this.setAllMatching();
		this.onSelectionChanged($(input).toggleClassName('selected').down('input[type="checkbox"]'));
	},

	selectAll: function(skip) {

		skip || this.setAllMatching();

		$$('#content .item').invoke('addClassName', 'selected');

		$$('#content .content_list_items input[type="checkbox"]').each(function(input) {
			if (!input.checked) {
				input.checked = true;
				this.onSelectionChanged(input);
			}
		}.bind(this));
	},

	selectAllMatching: function(message) {
		this.addDump(message);
		this.selectAll(true);
		this.allMatching = true;
	},

	invertSelection: function(){

		this.setAllMatching();

		$$('#content .content_list_items input[type="checkbox"]').each(function(input) {
			input.checked = !input.checked;
			if (input.checked) {
				input.up('.item').addClassName('selected');
			} else {
				input.up('.item').removeClassName('selected');
			}
			this.onSelectionChanged(input);
		}.bind(this));
	},

	serializeList: function() {

		var ids = [];

		$$('#content .content_list_items input[type="checkbox"]').each(function(input) {
			if (input.checked) {
				ids.push(input.value);
			}
		});

		return ids;
	},

	addTags: function() {

		if (!this.modal) {
			this.modal = new Control.Modal(null, {contents: $('add_tags_modal').innerHTML});
		}

		this.modal.open();
	},

	onAddTags: function(el) {

		this.reloadList({
			SECURITY_TOKEN: vivvo.token,
			action: 'tag',
			cmd: 'addTagsToTopic',
			TAG_topic_id: this.listOptions.topic_id,
			TAG_names: $(el).value
		});

		this.modal.close();
	}
};

Event.observe(window, 'load', function() {
	window.adminCategories = new vivvo.admin.categories();
	adminTopics.initialize();
});
