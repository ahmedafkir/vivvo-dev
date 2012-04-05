vivvo.admin.comments = Class.create();
Object.extend(vivvo.admin.comments.prototype, vivvo.controls.list.prototype);
Object.extend(vivvo.admin.comments.prototype, {

	initialize: function (){
		this.searchForm = 'comments_search_form';
		this.orderForm = 'comments_list_options_form';
		this.cleanUp = null;
		this.onComplete = function(){
			$$("#comments_list .comment_info").each(function(link) { new Tooltip(link, {mouseFollow: false}); });
		};
		this.sourceScript = 'comments.php';
		this.actionName = 'comment';

		new Control.Tabs('comments_tab_list');

		vivvo.mainNav.select('comments').pinSubNav();

		$$("#comments_list .comment_info").each(function(link) { new Tooltip(link, {mouseFollow: false}); });

		Event.observe('comments_search_form','submit', this.addSearchParams.bind(this));
		Event.observe('comments_filter_form','submit', this.saveList.bind(this));
	},
	commentEdit: function (id){
		if (id){
			if (this.sourceScript){
				if (this.cleanUp){
					this.cleanUp();
				}
				var searchOptions = this.listOptions;

				searchOptions.action = 'comment';
				searchOptions.cmd = 'edit';
				searchOptions.COMMENT_id = id;
				searchOptions.COMMENT_description = $('COMMENT_description_' + id).value;

				vivvo.admin.utils.ajaxAction(searchOptions, this.onEditComplete.bind(this,id));

				searchOptions.action = null;
				searchOptions.cmd = null;
				searchOptions.COMMENT_id = null;
				searchOptions.COMMENT_description = null;
			}
		}
		return false;
	},
	onEditComplete: function (id, transport){
		if (!transport.responseText.match(/error/)){
			$('comment_edit_form_' + id).hide();
			$('comment_list_body_footer').style.position='static';
			$('comment_list_body_footer').style.position='relative';;
		}
	}
});

var adminList = {}, adminListOptions = [];

Event.observe(window, 'load', function() {

	adminList = new vivvo.admin.comments();

	var listOptions = $A(adminListOptions);

	adminListOptions = {
		push: function(params) {
			Object.extend(adminList.listOptions, params);
		}
	};

	listOptions.each(adminListOptions.push);
});
