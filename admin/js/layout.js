vivvo.admin.layout = Class.create ();

Object.extend (
	vivvo.admin.layout.prototype,
	{
		initialize: function (){
            var that = this;
            
			this.tabs = new Control.Tabs('ri_main_tabs', {
                afterChange : function(el){
                    if ($(el).id.match(/topic_/) || $(el).id.match(/tag_/)){
                        that.applySelected($(el).select('select')[0]);
                    }
                }
            });
			
			$$('#ri1_main_tabs a').each(
				function (link){
					this.tabs.addTab(link);
				}.bind(this)
			)

			$$('#ri2_main_tabs a').each(
				function (link){
					this.tabs.addTab(link);
				}.bind(this)
			)

			$$('#content ul.tabs').each(
				function (ul){
					new Control.Tabs(ul.id);
				}
			);
			
			$$('.modal').each(function(link){
				new Control.Modal(link,{
					width: 400,
					height: 200
				});
			});
			
			$$('.modal_edit').each(function(link){
				new Control.Modal(link,{
					width: 900,
					height: 530,
					iframe: true,
					iframeTemplate: new Template('<img id="modal_close" onclick="Control.Modal.close()" src="img/delete_gray.gif" /><iframe src="#{href}" width="100%" height="100%" frameborder="0" id="#{id}" scrolling="no" allowtransparency="true"></iframe>')
				});
			});
			
			$$("#content .info_help").each( function(link) {
				new Tooltip(link, {
					mouseFollow: false
				});
			});
			
			this.onSelectHandle = this.onSelect.bindAsEventListener(this);
			$$('select.category_select', 'select.topic_select').invoke('observe', 'change', this.onSelectHandle);
			
			this.onFileSelectHandle = this.onFileSelect.bindAsEventListener(this);
			$$('span.file_select').invoke('observe', 'click', this.onFileSelectHandle);
			
			$('menu_layouts').addClassName('selected');
			
		},
		
		onFileSelect: function (e){
			var el = Event.element(e).up('.item');
			var fileName = el.id.split('/').last();
			var panel = el.up('.content_list_body');
			var option = panel.select('option').find(function(s) {return s.selected;});
			var selectionType = panel.id.replace('_local', '');
			
			panel.select('div.item').invoke('removeClassName', 'selected');
			el.addClassName('selected');
			
			paramList = {};
			
			if (option && option.value != 0){
				paramList.action = 'category';
				paramList.cmd = 'edit';
				paramList.CATEGORY_id = option.value;
			}else{
				paramList.action = 'vivvoCore';
				paramList.cmd = 'preferences';
			}
			
			if (selectionType == 'theme_list'){
				if (option.value != 0){
					paramList.CATEGORY_css = fileName;
				}else{
					paramList.VIVVO_DEFAULT_THEME = fileName;
					var templateSetFile = (fileName == 'default') ? 'xhtml' : fileName;
					if ($('templates/' + templateSetFile)){
						if (confirm('Do you want to change the template set as well? (Recommended: YES)')){
							paramList.VIVVO_DEFAULT_TEMPLATE_DIR = templateSetFile;
							$('template_set_list').select('div.item').invoke('removeClassName', 'selected');
							$('templates/' + templateSetFile).addClassName('selected');
						}
					}
				}
				option.writeAttribute('css', fileName);
			}else if(selectionType == 'category_layouts'){
				if (option.value != 0){
					paramList.CATEGORY_template = fileName;
				}else{
					paramList.VIVVO_CATEGORY_LAYOUT = fileName;
				}
				option.writeAttribute('template', fileName);
			}else if(selectionType == 'article_layouts'){
				if (option.value != 0){
					paramList.CATEGORY_article_template = fileName;
				}else{
					paramList.VIVVO_ARTICLE_LAYOUT = fileName;
				}
				option.writeAttribute('article_template', fileName);
				
				
				
			}else if(selectionType == 'topic_layouts'){
				paramList.action = 'tag';
                paramList.cmd = 'editGroup';
                
                paramList.TAG_GROUP_id = option.value;
                paramList.CATEGORY_id = null;
                paramList.TAG_GROUP_template = fileName;
				
				option.writeAttribute('template', fileName);
			}else if(selectionType == 'tag_layouts'){
                paramList.action = 'tag';
                paramList.cmd = 'editGroup';
                
                paramList.TAG_GROUP_id = option.value;
                paramList.CATEGORY_id = null;
                paramList.TAG_GROUP_tag_template = fileName;
				
				option.writeAttribute('label_template', fileName);
				
				
			}else if(selectionType == 'homepage_layouts'){
				paramList.VIVVO_HOMEPAGE_LAYOUT = fileName;
			//}else if(selectionType == 'tag_layouts'){
				//paramList.VIVVO_TAG_LAYOUT = fileName;
			}else if(selectionType == 'archive_layouts'){
				paramList.VIVVO_ARCHIVE_LAYOUT = fileName;
			}else if(selectionType == 'search_result_layouts'){
				paramList.VIVVO_SEARCH_RESULT_LAYOUT = fileName;
			}else if(selectionType == 'template_set_list'){
				paramList.VIVVO_DEFAULT_TEMPLATE_DIR = fileName;
				var themeFile = (fileName == 'xhtml') ? 'default' : fileName;
				if ($('themes/' + themeFile)){
					if (confirm('Change theme too?')){
						paramList.VIVVO_DEFAULT_THEME = themeFile;
						var themeOption = $('theme_list').select('option').find(function(s) {return s.value == 0;});
						themeOption.writeAttribute('css', themeFile);
						$('theme_list').select('div.item').invoke('removeClassName', 'selected');
						$('themes/' + themeFile).addClassName('selected');
					}
				}
			}else{
				return;
			}
			
			vivvo.admin.utils.ajaxAction(paramList);
		},
		
		applySelected : function (selectEl){
            selectEl = $(selectEl);
            var dir = selectEl.readAttribute('list_dir');
            var option = selectEl.select('option').find(function(s) {return s.selected;});
            if (selectEl.id.match(/^topic_layouts_/)){
                var file = option.readAttribute('template');
                $$('#topic_layouts_local div.item').invoke('removeClassName', 'selected');
                $(dir + file).addClassName('selected');
            }else if (selectEl.id.match(/^tag_layouts_topic_/)){
                var file = option.readAttribute('label_template');
                $$('#tag_layouts_local div.item').invoke('removeClassName', 'selected');
                $(dir + file).addClassName('selected');
            }else if (selectEl.id.match(/^theme_list_/)){
                var file = option.readAttribute('css');
                $$('#theme_list_local div.item').invoke('removeClassName', 'selected');
                $(dir + file).addClassName('selected');
            }else if (selectEl.id.match(/^category_layouts_/)){
                var file = option.readAttribute('template');
                $$('#category_layouts_local div.item').invoke('removeClassName', 'selected');
                $(dir + file).addClassName('selected');
            }else if (selectEl.id.match(/^article_layouts_/)){
                var file = option.readAttribute('article_template');
                $$('#article_layouts_local div.item').invoke('removeClassName', 'selected');
                $(dir + file).addClassName('selected');
            }
		},
		onSelect : function (e){
			var selectEl = Event.element(e);
			this.applySelected(selectEl);
		}
	}
);

document.observe("dom:loaded", function() {
	new vivvo.admin.layout();
});