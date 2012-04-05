window.adminArticle = {

	article_id: 0,
	article_revisions: null,
	auto_draft_timer: null,
	content_changed: false,

	initialize: function () {

		if ($('article_main_tabs')) {
			new Control.Tabs('article_main_tabs');
		}

		this.tabs = new Control.Tabs('articles_tab_list');
		this.tabs.setActiveTab('article_edit_article');

		vivvo.mainNav.select('articles').pinSubNav();

		$$('#content .info_help').each(function(link) {
			new Tooltip(link, {
				mouseFollow: false
			});
		});

		var article_id = $('ARTICLE_id');

		if (article_id) {
			this.article_id = article_id.value;
		}

		this.article_revisions = $('article_revisions');
	},

	submit: function(action) {
		$('article_form').submit();
	},

	revertTo: function(version) {

		if (('articleRevisions' in window) && (version in articleRevisions)) {

			var msg = vivvo.admin.lang.get('LNG_ADMIN_REVERT_TO_REVISION_PROMPT');

			if (this.content_changed) {
				msg = vivvo.admin.lang.get('LNG_ADMIN_UNSAVED_CHANGES') + "\n\n" + msg;
			}

			if (confirm(msg)) {

				this.stopAutoDraft();

				$('ARTICLE_title').value = articleRevisions[version].title;
				$('ARTICLE_abstract').update(articleRevisions[version].summary);
				tinyMCE.get('article_body').setContent(articleRevisions[version].body);

				this.content_changed = false;
			}
		} else {
			alert(vivvo.admin.lang.get('LNG_ADMIN_UNKNOWN_REVISION'));
		}
	},

	startAutoDraft: function() {

		if (!('articleRevisions' in window) || this.auto_draft_timer) {
			return;
		}

		this.auto_draft_timer = setTimeout(function() {

			this.article_revisions.setStyle({cursor: 'wait'});

			new Ajax.Updater(this.article_revisions, 'article_edit.php?revisions', {

				method: 'post',

				parameters: {
					article_id: this.article_id,
					title: $('ARTICLE_title').value,
					summary: $('ARTICLE_abstract').innerHTML,
					body: tinyMCE.get('article_body').getContent()
				},

				onComplete: function() {

					this.article_revisions.setStyle({cursor: 'normal'});
					this.stopAutoDraft();

				}.bind(this)
			});

		}.bind(this), VIVVO_REVISIONS_AUTODRAFT_TIME * 60000);
	},

	stopAutoDraft: function() {

		clearTimeout(this.auto_draft_timer);
		this.auto_draft_timer = null;
	},

	onContentChange: function() {

		this.content_changed = true;
		this.startAutoDraft();
	}
};

Event.observe(window, 'load', adminArticle.initialize.bind(adminArticle));
