vivvo.controls.list = Class.create ();

Object.extend (vivvo.controls.list.prototype,
	{
		searchForm: false,
		actionName: false,
		orderForm: false,
		allMatching: false,
		listOptions: {
			list_output: true
		},
		sourceScript: false,
		initialize: function (sourceScript, searchForm, orderForm, actionName, cleanUp, onComplete){
			this.searchForm = searchForm;
			this.orderForm = orderForm;
			this.cleanUp = cleanUp;
			this.onComplete = onComplete;
			this.sourceScript = sourceScript;
			this.actionName = actionName;
		},
		cleanUp: function (){

		},
		addDump: function (message, type, error, additional) {
			vivvo.admin.utils.addDump(message, type, error, additional);
		},
		addSearchParams: function (){
			var _ = this;
			if ($(this.searchForm)){
				$A($(this.searchForm).getElementsByTagName('input')).each(
					function (inp){
						_.listOptions[inp.name] = null;
					}
				)
				this.addParams($(this.searchForm).serialize(true));
				this.listUpdate();
			}
		},
		addOrderParams: function () {
			if ($(this.orderForm)){
				this.addParams($(this.orderForm).serialize(true));
				if (this.listOptions.search_sort_by){
					this.listOptions.search_order = this.listOptions.search_sort_by.split('.').last();
					this.listOptions.search_sort_by = this.listOptions.search_sort_by.split('.').first();
				}
				this.listUpdate();
			}
		},
		addParams: function(obj){
			Object.extend(this.listOptions, obj);
		},
		resetParams: function(){
			this.listOptions = {list_output: true};
		},
		goTo: function (param, value){
			this.resetParams();
	        this.addParams({search_do_advanced: 1});
	        this.listOptions[param] = value;
	        this.listUpdate();
		},
		listUpdate: function (pg){
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
				if (pg){
					searchOptions.pg = pg;
				}else{
					searchOptions.pg = 1;
				}

				if (vivvo.admin.debugMode){
					searchOptions.dump_info = 1;
				}

		        new Ajax.Updater('content', this.sourceScript, {
					parameters: searchOptions,
					evalScripts: true,
					onComplete: function () {
						_.selected_items = 0;
						if (_.onComplete){
							_.onComplete();
						}
						$$('body')[0].style.cursor = 'default';
					}
				});
			}
    	},
    	deleteItem: function(idName,id,cmd){
    		if (!cmd) cmd = 'delete';
    		var _ = this;
    		if (confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
	    		if (id){
	    			if (this.sourceScript && this.actionName){
						if (this.cleanUp){
							this.cleanUp();
						}

						$$('body')[0].style.cursor = 'wait';
						var searchOptions = Object.clone(this.listOptions);

						searchOptions.SECURITY_TOKEN = vivvo.token;
						searchOptions.action = this.actionName;
						searchOptions.cmd = cmd;
						searchOptions[idName] = id;

						if (vivvo.admin.debugMode){
							searchOptions.dump_info = 1;
						}

				        new Ajax.Updater('content', this.sourceScript, {
							parameters: searchOptions,
							evalScripts: true,
							onComplete: function () {
								if (_.onComplete){
									_.onComplete();
								}
								$$('body')[0].style.cursor = 'default';
							}
						});
					}
	    		}
    		}
    	},
    	setField: function(opt, force_ids){
    		if (this.allMatching && !force_ids){
	    		$$('.dump').first().hide()
	    		this.allMatching = false;
	    		this.listOptions.all_matching = 1;
    		}else{
    			this.listOptions.all_matching = 0;
    		}
    		var _ = this;
    		if (opt){
    			var field = opt.split('.').first();
				var value = opt.split('.').last();
    			if (field.match(/delete/i) || (field == 'status' && value == -2 && this.sourceScript == 'articles.php')){
					if (!confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
						return;
					}
    			}
    			var ids = force_ids || this.serializeList();
				if (this.sourceScript && this.actionName){
					if (this.cleanUp){
						this.cleanUp();
					}

					$$('body')[0].style.cursor = 'wait';
					var searchOptions = Object.clone(this.listOptions);

					searchOptions.SECURITY_TOKEN = vivvo.token;
					searchOptions.action = this.actionName;
					if (field.match(/delete/i)){
						searchOptions.cmd = field;
	    			}else{
						searchOptions.cmd = 'setField';
						searchOptions.field_name = field;
						searchOptions.field_value = value;
	    			}
					searchOptions['ids[]'] = ids;

					if (vivvo.admin.debugMode){
						searchOptions.dump_info = 1;
					}

			        new Ajax.Updater('content', this.sourceScript, {
						parameters: searchOptions,
						evalScripts: true,
						onComplete: function () {
							_.selected_items = 0;
							if (_.onComplete){
								_.onComplete();
							}
							$$('body')[0].style.cursor = 'default';
						}
					});
				}
    		}
    	},

		selected_items: 0,

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

    	chekBoxChange: function(input) {

			if (this.allMatching) {
	    		$$('.dump').first().hide();
	    		this.allMatching = false;
	    		this.listOptions.all_matching = 0;
    		}

   			this.onSelectionChanged($(input).toggleClassName('selected').down('input[type="checkbox"]'));
    	},

    	selectAll: function(skip) {

    		if (!skip && this.allMatching){
	    		$$('.dump').first().hide()
	    		this.allMatching = false;
	    		this.listOptions.all_matching = 0;
    		}

    		$$('#content .item').each(function(item) {
				input = item.addClassName('selected').down('input[type="checkbox"]');
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

    	invertSelection: function() {

    		if (this.allMatching){
	    		$$('.dump').first().hide()
	    		this.allMatching = false;
	    		this.listOptions.all_matching = 0;
    		}

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

    	serializeList: function(){
    		var ids = [];
    		$$('#content .content_list_items input[type="checkbox"]').each(
    			function (input){
    				if (input.checked){
	    				ids.push(input.value);
    				}
    			}
    		);
    		return ids;
    	},
    	saveList: function (e, section){
    		var filterName = $('FILTER_name').value;
    		if (filterName){
	    		var filter = 'SECURITY_TOKEN='+vivvo.token+'&user_filter=1&section=' + this.actionName + '&action=UserFilter&cmd=add&FILTER_name=' + encodeURIComponent(filterName) + '&' + 'FILTER_section=' + encodeURIComponent(this.actionName) + '&' + $(this.searchForm).serialize().replace(/search_([^=%]*)/g,'FILTER_data%5Bsearch_$1%5D');
    			$$('body')[0].style.cursor = 'wait';
		        new Ajax.Updater('user_filters_list_holder', 'ajax.php?' + filter, {
		        	method: 'get',
		        	evalScripts: true,
					onComplete: function () {
						$$('body')[0].style.cursor = 'default';
					}
				});
    		}else{
    			alert('Please enter filter name.');
    		}
    	}
	}
);