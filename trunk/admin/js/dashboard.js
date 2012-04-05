window.adminDashboard = {

	moderate_comments_holder: null,

	initialize: function() {

		$('menu_dashboard').addClassName('selected');

		this.moderate_comments_holder = $('moderate_comments_holder');

		$$('.info_help, .comment_info').each(function(link) {
			new Tooltip(link, {mouseFollow: false});
		});
	},

	commentsSelectAll: function() {
		this.moderate_comments_holder.select('div.item').each(function(item){
			item.addClassName('selected').down('input[type="checkbox"]').checked = true;
		});
	},

	commentsApprove: function() {
		var ids = this.moderate_comments_holder.select('div.item.selected').pluck('id').map(function(id) {
			return id.replace(/^moderate_comment_list_/, '');
		});

		if (ids.length) {
			new Ajax.Updater(this.moderate_comments_holder.setStyle({cursor: 'wait'}), 'ajax.php?xhr', {
				method: 'post',
				evalScripts: true,
				parameters: {
					SECURITY_TOKEN: vivvo.token,
					action: 'comment',
					cmd: 'setField',
					'ids[]': ids,
					field_name: 'status',
					field_value: 1,
					xhr_template_output: 'home/moderate_comments.xml'
				},
				onComplete: function(xhr) {
					this.moderate_comments_holder
						.setStyle({cursor: 'default'})
						.select('.comment_info')
						.each(function(link){ new Tooltip(link, {mouseFollow: false}); });
				}.bind(this)
			});
		}
	},

	commentsSelect: function(item, checkbox) {
		item[['removeClassName', 'addClassName'][checkbox.checked << 0]]('selected');
	}
};

Event.observe(window, 'load', adminDashboard.initialize.bind(adminDashboard));