vivvo.admin.poll = Class.create ();

Object.extend (vivvo.admin.poll.prototype, vivvo.controls.list.prototype);
		
Object.extend (
	vivvo.admin.poll.prototype,
	{
		added : 1,
		initialize: function(){
			this.sourceScript = 'plugins.php?plugin=poll';
			this.searchForm = false;
			this.orderForm = false;
			this.actionName = 'poll';
			this.cleanUp = null;
			this.onComplete = this.onPollChange.bind(this);

			if ($('menu_poll')){
				$('menu_poll').addClassName('selected');
			}
		},
		
		editMode: function(){
			$('poll_answer_list_form').show();
			$('poll_answer_list').hide();
			$('comment_list_body_footer').show();
			$('comment_list_body_footer').style.position='static';
			$('comment_list_body_footer').style.position='relative';
		},
		addNewAnswer: function (){
			new Insertion.Bottom('poll_answer_list_form',  $('new_question_template').innerHTML);
			var newEl = $('poll_answer_list_form').immediateDescendants().last()
			newEl.id = 'poll_answer_' + this.added;
			var image = $A(newEl.getElementsByTagName('img')).first();
			image.id = 'poll_answer_' + this.added + '_img';
			this.added++;
			$('comment_list_body_footer').show();
			$('comment_list_body_footer').style.position='static';
			$('comment_list_body_footer').style.position='relative';
		},
		removeAnswer: function (el){
			var elem = $(el.id.replace(/_img/, ''));
			elem.remove();
			$('comment_list_body_footer').show();
			$('comment_list_body_footer').style.position='static';
			$('comment_list_body_footer').style.position='relative';
		},
		saveList: function (){
			document.body.style.cursor = 'wait';
			var searchOptions = $('poll_form').serialize(true);
			new Ajax.Updater('content', 'plugins.php?plugin=poll', {
				parameters: searchOptions,
				evalScripts: true,
				onComplete: function () {
					document.body.style.cursor = 'default';
				}
			});
		},
		activatePoll: function (id){
			document.body.style.cursor = 'wait';
			var searchOptions = {};
			searchOptions.PLUGIN_POLL_id = id;
			searchOptions.action = 'poll';
			searchOptions.cmd = 'activate';
				
			new Ajax.Updater('content', 'plugins.php?plugin=poll&list_output=1&search_do_advanced=1&search_pid=' + id, {
				parameters: searchOptions,
				evalScripts: true,
				onComplete: function () {
					document.body.style.cursor = 'default';
				}
			});
		},
		deactivatePoll: function (id){
				document.body.style.cursor = 'wait';
				var searchOptions = {};
				searchOptions.PLUGIN_POLL_id = id;
				searchOptions.action = 'poll';
				searchOptions.cmd = 'deactivate';
				
			new Ajax.Updater('content', 'plugins.php?plugin=poll&list_output=1&search_do_advanced=1&search_pid=' + id, {
					parameters: searchOptions,
					evalScripts: true,
					onComplete: function () {
						document.body.style.cursor = 'default';
					}
				});
		},
		onPollChange: function (){
				new Ajax.Updater('navigation', 'plugins.php?plugin=poll', {
					parameters: {leftnav:1}
				});
				
		},
		goToAndEdit: function(param, value){
			this.resetParams();
	        	this.addParams({search_do_advanced: 1});
	        	this.listOptions[param] = value;
			if (this.allMatching){
				$$('.dump').first().hide()
				this.allMatching = false;
				this.listOptions.all_matching = 1;
			}else{
				this.listOptions.all_matching = 0;
			}
	   		var _ = this;
			if (this.sourceScript){
				if (this.cleanUp){
					this.cleanUp();
				}

				$$('body')[0].style.cursor = 'wait';
				var searchOptions = this.listOptions;
					searchOptions.pg = 1;
		
				if (vivvo.admin.debugMode){
					searchOptions.dump_info = 1;
				}
				
		        new Ajax.Updater('content', this.sourceScript, {
					parameters: searchOptions,
					evalScripts: true,
					onComplete: function () {
						$$('body')[0].style.cursor = 'default';
						_.editMode();
					}
				});
			}
    		}
    	}
);

var adminList = {};
document.observe("dom:loaded", function() {
	adminList = new vivvo.admin.poll();
});