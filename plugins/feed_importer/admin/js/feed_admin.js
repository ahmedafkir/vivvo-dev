vivvo.admin.feedImporter = Class.create ();

Object.extend (vivvo.admin.feedImporter.prototype, vivvo.controls.list.prototype);
Object.extend (vivvo.admin.feedImporter.prototype,

	{
		added: 1,
		initialize: function(){
			if ($('menu_feed_importer')){
				$('menu_feed_importer').addClassName('selected');
			}
		},
		editMode: function(el){
			var rootEl = Element.up(el, '.item');
			rootEl.select('.feed_input').each(Element.show);
			$('comment_list_body_footer').style.position='static';
			$('comment_list_body_footer').style.position='relative';
		},
		addNewAnswer: function (){
			new Insertion.Bottom('feed_list', $('new_feed_template').innerHTML);
			var elem = $$('#feed_list .item_line1').last();
			var selector = elem.getElementsByTagName('select')[0];
			var input = elem.getElementsByTagName('input')[0];
			selector.name="PLUGIN_FEED_new_feed[" + this.added + "][category_id]";
			input.name="PLUGIN_FEED_new_feed[" + this.added + "][feed]";
			var author_input = elem.getElementsByTagName('input')[1];
			author_input.name="PLUGIN_FEED_new_feed[" + this.added + "][author]";
			this.added++;
			$('comment_list_body_footer').style.position='static';
			$('comment_list_body_footer').style.position='relative';
		},
		removeAnswer: function (el){
			el.up('.item').remove();
			$('comment_list_body_footer').style.position='static';
			$('comment_list_body_footer').style.position='relative';
		},
		saveList: function (){
    	},
		forceImport: function() {
			var body = $$('body')[0].setStyle('cursor', 'wait'),
				loading = $('filelist_loading_sign').show();
			new Ajax.Request('plugins.php?plugin=feed_importer&force_import', {
				method: 'post',
				parameters: {
					SECURITY_TOKEN: vivvo.token
				},
				onComplete: function(transport) {
					loading.hide();
					body.setStyle('cursor', 'default');
					if (transport.responseJSON) {
						vivvo.admin.utils.addDump(
							feed_importer_lang.LNG_INFO_PLUGIN_FEED_FORCE_IMPORT_RESULTS
								.replace('{1}', transport.responseJSON.added)
								.replace('{2}', transport.responseJSON.updated)
								.replace('{3}', transport.responseJSON.feeds)
						);
					}
				}
			});
		}
	}
);

var feedList;

document.observe("dom:loaded",
	function(){
		var feed_tabs = new Control.Tabs('feed_tab_list');
		feedList = new vivvo.admin.feedImporter();
		// Event.observe('feed_form','submit', feedList.saveList.bind(feedList));
	}
);