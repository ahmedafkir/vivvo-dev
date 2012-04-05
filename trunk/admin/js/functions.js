vivvo.controls.comboBox = Class.create ();

Object.extend (vivvo.controls.comboBox.prototype,
	{
		overDropDown: false,
		initialize: function (el, zIndex){
			if (!zIndex) zIndex = 1000;
			this.element = $(el);
			this.element.makePositioned();
			this.labelElement = this.element.down('.drop_down_label');
			this.dropDownElement = this.element.down('.drop_down');
			this.selectedElement = this.element.down('.selected');
			this.inputElement = this.element.down('input');
			Position.absolutize(this.dropDownElement);
			
			//Position.absolutize(this.labelElement);
			
			this.element.setStyle({
				zIndex: zIndex
			});
			
			this.labelElement.setStyle({
				zIndex: zIndex
			});
			
			this.dropDownElement.setStyle({
				overflowY: 'scroll', 
				height: '130px', 
				borderTop: '1px solid #989DA5',
				borderRight: '2px solid #989DA5',
				borderBottom: '2px solid #989DA5',
				borderLeft: '1px solid #989DA5',
				marginTop:'18px', 
				width: '230px', 
				backgroundColor: '#ECECEC',
				zIndex: zIndex
			});
			
			Event.observe(this.labelElement, 'click', this.showDropDown.bindAsEventListener(this), true);
			//Event.observe(this.element, 'mouseout', this.hideDropDown.bind(this));
//			Event.observe(this.dropDownElement, 'mouseover', this.overDropDown.bind(this));
//			Event.observe(this.dropDownElement, 'mouseout', this.outDropDown.bind(this));

			var _ = this;
			$A(this.dropDownElement.getElementsByTagName('div')).each(
				function (div){
					Event.observe(div, 'click', _.change.bind(_));
				}
			)
			this.hideDropDownObserver = this.hideDropDown.bindAsEventListener(this);
		},
		change: function (e) {
			var el = Event.element(e);
			if (el.tagName != 'DIV') el = el.up('div');
			if (this.selectedElement){
				this.selectedElement.removeClassName('selected');
			}
			el.addClassName('selected');
			this.inputElement.value = el.id.replace(this.inputElement.name + '_', '');
			this.labelElement.update(el.down('span').innerHTML);
			
			this.dropDownElement.hide();
			Event.stopObserving(document.body, 'click', this.hideDropDownObserver);
			
			this.selectedElement = el;
		},
		overDropDown: function(){
			this.labelElement.update('over drop');
			this.overDropDown = true;
			this.dropDownElement.show();
		},
		outDropDown: function(){
			this.labelElement.update('out drop');
			this.dropDownElement.hide();
			this.overDropDown = false;
		},
		showDropDown: function(){
			this.dropDownElement.show();
			Event.observe(document.body, 'click', this.hideDropDownObserver);
		},
		hideDropDown: function(e){
			var el = Event.element(e);
			if (!Element.descendantOf(el, this.element)){
				this.dropDownElement.hide();
				Event.stopObserving(document.body, 'click', this.hideDropDownObserver);
			}
		}
	}
);

vivvo.admin.utils.deleteItemFromList = function(elem, action, idName, id){
	if (confirm (vivvo.admin.lang.get('LNG_ADMIN_CONFIRM_DELETE'))){
		if (id){
			if (action){
				var searchOptions = {};
				
				searchOptions.action = action;
				searchOptions.cmd = 'delete';
				searchOptions[idName] = id;
				searchOptions.SECURITY_TOKEN = vivvo.token;
				
				vivvo.admin.utils.ajaxAction(
					searchOptions, 
					function(transport){
						if (!transport.responseText.match(/error/)){
							if ($(elem)){
								$(elem).remove();
							}
						}
					}
				);
			}
		}
	}
}