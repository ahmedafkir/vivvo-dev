
vivvo.admin.formBuilder = Class.create ();

Object.extend (vivvo.admin.formBuilder.prototype, vivvo.controls.list.prototype);

Object.extend (
	vivvo.admin.formBuilder.prototype,
	{
		initialize: function (formHandler, element, index){
			this.options = {
				type			: '',
				order_number	: 0,
				name			: '',
				label			: 'Label',
				required		: 0,
				size			: 0,
				max_size			: 0,
				reg_exp			: '',
				description		: '',
				options			: '',
				selected		: '',
				error_message	: ''
			};

			this.formHandler = formHandler;

			var max = index << 0;

			$$('#formHolder input, #formHolder button, #formHolder textarea').pluck('name').each(function(name){
				if (name.match(/^field_(\d+)$/) && (index = RegExp.$1 << 0) > max) {
					max = index;
				}
			});
			
			this.index = max + 1;

			if (element){
				this.element = element;
				if (this.element.hasClassName('type_text')){
					this.options.type = 'text';
					this.options.label = this.element.innerHTML.stripTags().strip();
				}else if (this.element.hasClassName('type_heading')){
					this.options.type = 'heading';
					if (this.element.down('strong')){
						this.options.label = this.element.down('strong').innerHTML.stripTags().strip();
					}
				}else if (this.element.hasClassName('type_submit')){
					this.options.type = 'submit';
					if (this.element.down('input')){
						this.options.label = this.element.down('input').value;
						this.options.name = this.element.down('input').readAttribute('name');
						if (!this.options.name){
							this.options.name = 'field_' + this.index;
							this.element.down('input').name = this.options.name;
						}
					}
				}else if (this.element.hasClassName('type_input')){
					this.options.type = 'input';
					this.analizeLabel();
					if (this.element.down('input')){
						this.options.name = this.element.down('input').readAttribute('name');
						if (!this.options.name){
							this.options.name = 'field_' + this.index;
							this.element.down('input').name = this.options.name;
						}
						this.options.size = this.element.down('input').readAttribute('size');
						this.options.max_size = this.element.down('input').readAttribute('maxlength');
						this.options.selected = this.element.down('input').value;
					}
					this.analizeDescription();
					this.analizeRegExp();
					this.analizeRequired();
					this.analizeErrorMessage();
				}else if (this.element.hasClassName('type_file_upload')){
					this.options.type = 'file_upload';
					this.analizeLabel();
					if (this.element.down('input')){
						this.options.name = this.element.down('input').readAttribute('name');
						if (!this.options.name){
							this.options.name = 'field_' + this.index;
							this.element.down('input').name = this.options.name;
						}
						this.options.selected = this.element.down('input').value;
					}
					this.analizeDescription();
					this.analizeRequired();
					this.analizeErrorMessage();
				}else if (this.element.hasClassName('type_text_area')){
					this.options.type = 'textarea';
					this.analizeLabel();
					if (this.element.down('textarea')){
						this.options.name = this.element.down('textarea').readAttribute('name');
						if (!this.options.name){
							this.options.name = 'field_' + this.index;
							this.element.down('textarea').name = this.options.name;
						}
						this.options.max_size = this.element.down('textarea').readAttribute('rows');
						this.options.selected = this.element.down('textarea').value;
					}
					this.analizeDescription();
					this.analizeRegExp();
					this.analizeRequired();
					this.analizeErrorMessage();
				}else if (this.element.hasClassName('type_drop_down')){
					this.options.type = 'drop_down';
					this.analizeLabel();
					if (this.element.down('select')){
						this.options.name = this.element.down('select').readAttribute('name');
						if (!this.options.name){
							this.options.name = 'field_' + this.index;
							this.element.down('select').name = this.options.name;
						}
						this.options.size = this.element.down('select').readAttribute('size');
						this.options.selected = [];
						this.options.options = [];
						for (var i = 0; i < this.element.down('select').options.length; i++){
							this.options.options.push(this.element.down('select').options[i].value);
							if (this.element.down('select').options[i].selected){
								this.options.selected.push(this.element.down('select').options[i].value);
							}
						}
					}
					this.analizeDescription();
					this.analizeRequired();
					this.analizeErrorMessage();
				}else if (this.element.hasClassName('type_checkbox')){
					this.options.type = 'checkbox';
					this.analizeLabel();
					if (this.element.down('input')){
						this.options.name = this.element.down('input').readAttribute('name');
						if (!this.options.name){
							this.options.name = 'field_' + this.index;
							this.element.down('input').name = this.options.name;
						}
						if (this.element.down('input').checked){
							this.options.selected = '1';
						}
					}

					this.analizeDescription();
					this.analizeRequired();
					this.analizeErrorMessage();
				}else if (this.element.hasClassName('type_radio')){
					this.options.type = 'radio';
					this.analizeLabel();
					if (this.element.down('input')){
						this.options.name = this.element.down('input').readAttribute('name');

						var inputs = $A(this.element.getElementsByTagName('input'));

						if (!this.options.name){
							this.options.name = 'field_' + this.index;
							var _ = this;
							inputs.each(
								function(inp){
									inp.name = _.options.name;
								}
							);
						}
						this.options.selected = '';
						this.options.options = [];

						for (var i = 0; i < inputs.length; i++){
							this.options.options.push(inputs[i].value);
							if (inputs[i].checked){
								this.options.selected = inputs[i].value;
							}
						}
					}
					this.analizeDescription();
					this.analizeRequired();
					this.analizeErrorMessage();
				}else if (this.element.hasClassName('type_date_time')){

				}

				this.onClickHandle = this.onClick.bindAsEventListener (this);
				Event.observe (this.element, 'click', this.onClickHandle, false);

				this.onRemoveHandle = this.onRemove.bindAsEventListener (this);
				Event.observe (this.element.down('.remove_prop'), 'click', this.onRemoveHandle, false);

				if (this.element.down('img.info_help')){
					this.tooltip = new Tooltip(this.element.down('img.info_help'), {mouseFollow: false});
				}
			}
		},

		onRemove: function(){
			this.tooltip = null;
			this.element.remove();
			this.element = null;
			$('field_properties').select('.form_line').each(Element.hide);
		},

		onClick: function(){
			if (!this.formHandler.onElementClick(this)){
				return false;
			}

			this.element.addClassName('selected');
			$('field_properties').select('.form_line').each(Element.hide);

			$('field_properties_apply').show();
			if (this.options.type == 'text'){
				$('field_properties_text').show().down('textarea').value = this.options.label;
			}else if (this.options.type == 'heading'){
				$('field_properties_label').show().down('input').value = this.options.label;
			}else if (this.options.type == 'submit'){
				this.fillName();
				$('field_properties_label').show().down('input').value = this.options.label;
			}else if (this.options.type == 'input'){
				this.fillLabel();
				this.fillName();
				this.fillDescription();
				this.fillRegExp();
				this.fillErrorMessage();
				this.fillRequired();

				if (this.options.size){
					$('field_properties_size').down('input').value = this.options.size;
				}else{
					$('field_properties_size').down('input').value = '';
				}

				if (this.options.max_size){
					$('field_properties_max_size').down('input').value = this.options.max_size;
				}else{
					$('field_properties_max_size').down('input').value = '';
				}
				$('field_properties_size').show();
				$('field_properties_max_size').show();
			}else if (this.options.type == 'file_upload'){
				this.fillLabel();
				this.fillName();
				this.fillDescription();
				this.fillErrorMessage();
				this.fillRequired();
			}else if (this.options.type == 'textarea'){
				this.fillLabel();
				this.fillName();
				this.fillDescription();
				this.fillRegExp();
				this.fillErrorMessage();
				this.fillRequired();
				this.fillRows();
				$('field_properties_rows').show();
			}else if (this.options.type == 'drop_down'){
				this.fillLabel();
				this.fillName();
				this.fillDescription();
				this.fillErrorMessage();
				this.fillRequired();
				$('field_properties_size').show();
				if (this.options.options){
					$('field_properties_options').down('textarea').value = '';
					this.options.options.each(
						function(opt){
							$('field_properties_options').down('textarea').value += opt + "\n";
						}
					)
				}else{
					$('field_properties_options').down('textarea').value = '';
				}
				$('field_properties_options').show();
				this.fillSelectSize();
			}else if (this.options.type == 'checkbox'){
				this.fillLabel();
				this.fillName();
				this.fillDescription();
				this.fillErrorMessage();
				this.fillRequired();
				$('field_properties_options').show().down('textarea').value = this.element.down('.formElement').down('label').innerHTML.stripTags().strip();
			}else if (this.options.type == 'radio'){
				this.fillLabel();
				this.fillName();
				this.fillDescription();
				this.fillErrorMessage();
				this.fillRequired();

				if (this.options.options){
					$('field_properties_options').down('textarea').value = '';
					this.options.options.each(
						function(opt){
							$('field_properties_options').down('textarea').value += opt + "\n";
						}
					)
				}else{
					$('field_properties_options').down('textarea').value = '';
				}
				$('field_properties_options').show();
			}else if (this.options.type == 'date_time'){

			}
			this.formHandler.changed = false;
		},

		applyChanges: function(){
			if (this.options.type == 'text'){
				this.options.label = $('field_properties_text').down('textarea').value;
				this.element.down('p').update(this.options.label);
			}else if (this.options.type == 'heading'){
				this.options.label = $('field_properties_label').down('input').value;
				this.element.down('strong').update(this.options.label);
			}else if (this.options.type == 'submit'){
				if ($('field_properties_name').show().down('input').value == '' || $('field_properties_name').show().down('input').value.match(/[^a-zA-Z0-9\-_]/)){
					alert('Field name can only contain a-z, A-Z, 0-9, - and _');
					return;
				}
				this.options.name = $('field_properties_name').show().down('input').value;
				this.options.label = $('field_properties_label').down('input').value;
				this.element.down('input').value = this.options.label;
				this.element.down('input').name = this.options.name;
			}else if (this.options.type == 'input'){
				this.applyName();
				this.applyLabel();
				this.applyDescription();
				this.applyRegExp();
				this.applyErrorMessage();
				this.applyRequired();

				if (parseInt($('field_properties_size').down('input').value) != 0 && !isNaN(parseInt($('field_properties_size').down('input').value))){
					this.options.size = parseInt($('field_properties_size').down('input').value);
					this.element.down('input').size = this.options.size;
					this.element.down('input').removeClassName('form_builder_text');
				}else{
					this.options.size = 0;
					this.element.down('input').size = this.options.size;
					this.element.down('input').addClassName('form_builder_text');
				}

				if (parseInt($('field_properties_max_size').down('input').value) != 0){
					this.options.max_size = parseInt($('field_properties_max_size').down('input').value);
					this.element.down('input').maxLength = this.options.max_size;
				}
			}else if (this.options.type == 'file_upload'){
				this.applyName();
				this.applyLabel();
				this.applyDescription();
				this.applyErrorMessage();
				this.applyRequired();
			}else if (this.options.type == 'textarea'){
				this.applyName();
				this.applyLabel();
				this.applyDescription();
				this.applyRegExp();
				this.applyErrorMessage();
				this.applyRequired();
				this.applyRows();
			}else if (this.options.type == 'drop_down'){
				this.options.options = $('field_properties_options').down('textarea').value.split('\n');
				var _ = this;
				this.options.options.each(
					function(opt, index){
						if (_.options.options[index].strip() == ''){
							_.options.options[index] = null;
						}
					}
				);
				var new_html = '';
				this.options.options = this.options.options.compact();

				var selectElement = this.element.down('select');
				selectElement.options.length = 0;

				this.options.options.each(
					function(opt){
						if (opt.strip() != ''){
							selectElement.options[selectElement.options.length] = new Option(opt.strip(), opt.strip());
						}
					}
				);

				//this.element.down('select').update(new_html);

				this.applyName();
				this.applyLabel();
				this.applyDescription();
				this.applyErrorMessage();
				this.applyRequired();
				this.applySelectSize();
			}else if (this.options.type == 'checkbox'){
				this.options.options = $('field_properties_options').down('textarea').value;
				this.element.down('.formElement').down('label').update('<input type="checkbox" /> ' + this.options.options);
				this.applyName();
				this.applyLabel();
				this.applyDescription();
				this.applyErrorMessage();
				this.applyRequired();
			}else if (this.options.type == 'radio'){
				this.options.options = $('field_properties_options').down('textarea').value.split('\n');
				var _ = this;
				this.options.options.each(
					function(opt, index){
						if (_.options.options[index].strip() == ''){
							_.options.options[index] = null;
						}
					}
				);
				$A(this.element.down('.formElement').getElementsByTagName('label')).each(Element.remove);
				this.options.options = this.options.options.compact();
				this.options.options.reverse();
				this.options.options.each(
					function(opt){
						if (opt.strip() != '') {
							new Insertion.Top(_.element.down('.formElement'), '<label><input type="radio" value="'+opt+'" />'+opt+'</label>');
						}
					}
				);
				this.options.options.reverse();
				this.applyName();
				this.applyLabel();
				this.applyDescription();
				this.applyErrorMessage();
				this.applyRequired();
			}else if (this.options.type == 'date_time'){

			}
			this.formHandler.changed = false;
		},

		applyName: function(){
			this.options.name = $('field_properties_name').down('input').value;
			if (this.options.type == 'textarea'){
				this.element.down('textarea').name = this.options.name;
			}else if (this.options.type == 'drop_down'){
				this.element.down('select').name = this.options.name;
			}else{
				var _ = this;
				$A(this.element.getElementsByTagName('input')).each(
					function(inp){
						inp.name = _.options.name;
					}
				);
			}
		},

		fillName: function(){
			$('field_properties_name').show().down('input').value = this.options.name;
		},

		applyRegExp: function(){
			this.options.reg_exp = $('field_properties_reg_exp').down('input').value;
		},

		fillRegExp: function(){
			$('field_properties_validation').show();
			$('field_properties_reg_exp').hide().down('input').value = this.options.reg_exp;
			if (this.options.reg_exp == '[\\w\\s]*'){
				$('field_properties_validation').down('select').selectedIndex = 1;
			}else if (this.options.reg_exp == '\\d*'){
				$('field_properties_validation').down('select').selectedIndex = 2;
			}else if (this.options.reg_exp == '[-_a-zA-Z0-9]+(\\.[-_a-zA-Z0-9]+)*@[-a-zA-Z0-9]+(\\.[-a-zA-Z0-9]+)*\\.[a-zA-Z]{2,6}'){
				$('field_properties_validation').down('select').selectedIndex = 3;
			}else if (this.options.reg_exp == '' || this.options.reg_exp == null){
				$('field_properties_validation').down('select').selectedIndex = 0;
			}else{
				$('field_properties_validation').down('select').selectedIndex = 4;
				$('field_properties_reg_exp').show();
			}
		},

		analizeRegExp: function(){
			if (this.element.down('.formElement').readAttribute('title')){
				this.options.reg_exp = this.element.down('.formElement').readAttribute('title');
			}else{
				this.options.reg_exp = '';
			}
			this.element.down('.formElement').title = '';
		},

		analizeErrorMessage: function(){
			if (this.element.readAttribute('title')){
				this.options.error_message = this.element.readAttribute('title');
			}else{
				this.options.error_message = '';
			}
			this.element.title = '';
		},

		applyErrorMessage: function(){
			this.options.error_message = $('field_properties_error_message').down('input').value;
		},

		fillErrorMessage: function(){
			$('field_properties_error_message').show().down('input').value = this.options.error_message;
		},

		applyColWidth: function(){
			if (parseInt($('field_properties_column_width').down('input').value) != 0 && !isNaN(parseInt($('field_properties_column_width').down('input').value))){
				this.options.size = parseInt($('field_properties_column_width').down('input').value);
				this.element.down('textarea').cols = this.options.size;
			}else{
				this.options.size = '';
				this.element.down('textarea').cols = '20';
			}
		},

		fillColWidth: function(){
			$('field_properties_column_width').show().down('input').value = this.options.size;
		},

		applyRows: function(){
			if (parseInt($('field_properties_rows').down('input').value) != 0 && !isNaN(parseInt($('field_properties_rows').down('input').value))){
				this.options.max_size = parseInt($('field_properties_rows').down('input').value);
				this.element.down('textarea').rows = this.options.max_size;
			}else{
				this.options.max_size = '';
				this.element.down('textarea').rows = '3';
			}
		},

		fillRows: function(){
			$('field_properties_rows').show().down('input').value = this.options.max_size;
		},

		applySelectSize: function(){
			if (parseInt($('field_properties_size').down('input').value) > 1 && !isNaN(parseInt($('field_properties_size').down('input').value))){
				this.options.size = parseInt($('field_properties_size').down('input').value);
				this.element.down('select').size = this.options.size;
				this.element.down('select').multiple = 'multiple';
			}else{
				this.options.size = '';
				this.element.down('select').size = '';
				this.element.down('select').multiple = '';
			}
		},

		fillSelectSize: function(){
			$('field_properties_size').show().down('input').value = this.options.size;
		},

		applyRequired: function(){
			if ($('field_properties_required').down('input').checked){
				this.options.required = 1;
				this.element.down('label').addClassName('required');
			}else{
				this.options.required = 0;
				this.element.down('label').removeClassName('required');
			}
		},

		fillRequired: function(){
			$('field_properties_required').show().down('input').checked = this.options.required;
		},

		analizeRequired: function(){
			if (this.element.down('label').hasClassName('required')){
				this.options.required = 1;
			}else{
				this.options.required = 0;
			}
		},

		applyLabel: function(){
			this.options.label = $('field_properties_label').down('input').value;
			this.element.down('label').update(this.options.label);

		},

		fillLabel: function(){
			$('field_properties_label').show().down('input').value = this.options.label;
		},

		analizeLabel: function(){
			if (this.element.down('label')){
				this.options.label = this.element.down('label').innerHTML.strip();
			}
		},

		applyDescription: function(){
			this.options.description = $('field_properties_description').down('textarea').value.replace(/\n/g,'\\n');
			if (this.tooltip) {
				this.tooltip.content = this.options.description.replace(/\\n/g,"<br />");
			}

			if (this.element.down('img.info_help')){
				if (this.options.description != ''){
					this.element.down('img.info_help').show();
				}else{
					this.element.down('img.info_help').hide();
				}
			}
		},

		fillDescription: function(){
			if (this.options.description){
				$('field_properties_description').down('textarea').value = this.options.description;
			}else{
				$('field_properties_description').down('textarea').value = '';
			}
			$('field_properties_description').show();
		},

		analizeDescription: function(){
			if (this.element.down('img.info_help')){
				this.options.description = this.element.down('img.info_help').readAttribute('title');
			}
		},

		serialize: function(index){
			if (!this.element) return false;
			var output = [] ;
			var _ = this;
			this.options.order_number = this.getPosition();
			$H(this.options).each(
				function(opt){
					if (_.options.type == 'drop_down'){
						if (opt.key == 'options'){
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value.join('\n')));
						}else if (opt.key == 'selected'){
							_.options.selected = [];
							for (var i = 0; i < _.element.down('select').options.length; i++){
								if (_.element.down('select').options[i].selected){
									_.options.selected.push(_.element.down('select').options[i].value);
								}
							}
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(_.options.selected.join('\n')));
						}else{
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value));
						}
					}else if (_.options.type == 'radio'){
						if (opt.key == 'options'){
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value.join('\n')));
						}else if (opt.key == 'selected'){
							var inputs = $A(_.element.getElementsByTagName('input'));
							_.options.selected = '';
							for (var i = 0; i < inputs.length; i++){
								if (inputs[i].checked){
									_.options.selected = inputs[i].value;
								}
							}
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(_.options.selected));
						}else{
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value));
						}
					}else if (_.options.type == 'input'){
						if (opt.key == 'selected'){
							_.options.selected = _.element.down('input').value;
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(_.options.selected));
						}else{
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value));
						}
					}else if (_.options.type == 'textarea'){
						if (opt.key == 'selected'){
							_.options.selected = _.element.down('textarea').value;
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(_.options.selected));
						}else{
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value));
						}
					}else if (_.options.type == 'checkbox'){
						if (opt.key == 'selected'){
							if (_.element.down('input').checked){
								_.options.selected = '1';
							}else{
								_.options.selected = '0';
							}
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(_.options.selected));
						}else{
							output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value));
						}
					}else{
						output.push('PLUGIN_FORM_BUILDER_elements[' + index + '][' + opt.key + ']=' + encodeURIComponent(opt.value));
					}
				}
			)
			return output.join('&');
		},

		getPosition : function (){
			var found_position = 0;
			var position = 0;
			var _ = this;

			$A (this.element.up().immediateDescendants()).each (
				function (child) {
					if (child.nodeType != 1) {
						return;
					}
					if (child == _.element) {
						found_position = position;
					}
					position++;
				}
			);
			return found_position;
		}
	}
);

vivvoSortable = Class.create ();

Object.extend (vivvoSortable.prototype, vivvo.controls.tree.prototype);

Object.extend (
	vivvoSortable.prototype,
	{
		formElements: [],

		formElementNumber: 0,

		selectedFormElement: false,

		changed: false,

		initialize: function (sortableElement, sourceElement){
			if ($(sortableElement)) {
				this.sortableElement = $(sortableElement);
			}
			if ($(sourceElement)) {
				this.sourceElement = $(sourceElement);
			}
			this.initSortable();
			this.initDraggable();
			this.initDroppables();
			this.initFormElements();
			Draggables.addObserver(this);

			this.onApplyHandle = this.onApply.bindAsEventListener (this);
			Event.observe ($('field_properties_apply_button'), 'click', this.onApplyHandle, false);

			this.onValidationHandle = this.onValidation.bindAsEventListener (this);
			Event.observe ($('field_properties_validation'), 'click', this.onValidationHandle, false);

			this.onSaveHandle = this.onSave.bindAsEventListener (this);
			Event.observe ($('form_submit'), 'click', this.onSaveHandle, false);

			if ($('form_delete')){
				this.onDeleteHandle = this.onDelete.bindAsEventListener (this);
				Event.observe ($('form_delete'), 'click', this.onDeleteHandle, false);
			}

			this.onPropertiesChangeHandle = this.onPropertiesChange.bindAsEventListener (this);
			Event.observe ($('field_properties_name'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_text'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_label'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_required'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_size'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_column_width'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_max_size'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_rows'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_validation'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_description'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_options'), 'change', this.onPropertiesChangeHandle, false);
			Event.observe ($('field_properties_error_message'), 'change', this.onPropertiesChangeHandle, false);

			Droppables.add('properties_info_box',
				{
					accept: 'draggable',
					overlap: '2',
					hoverclass: 'droppable_after',
					onHover: this.onHover.bind(this)
				}
			);
		},

		onDelete: function(){
			if ($('form_id')){
				this.deleteForm($('form_id').value);
			}
		},

		deleteForm: function (id){
			if (id){
				if (confirm ('Confirm delete')){
					var url = "ajax.php?ajax_dump=1&action=form_builder&cmd=delete&PLUGIN_FORM_BUILDER_id=" + id;
					var _ = this;
					new Ajax.Updater($$('.dump')[0], url, {
						evalScripts: true,
						onComplete: function () {
							_.reloadFormList();
							_.loadForm($$('.quick_launch span')[0].readAttribute('onclick').match(/\((\d+)\)/)[1]);
						},
						insertion: 'bottom'
					});
				}
			}
		},

		onSave: function(e){
			if ($('PLUGIN_FORM_BUILDER_title').value == ''){
				alert('You must enter form title!');
				return;
			}

			document.body.style.cursor = 'wait';
			var button = Event.element(e);

			if (button.hasClassName('edit')){
				var url = "ajax_dump=1&action=form_builder&cmd=edit&" + this.serialize();
			}else if(button.hasClassName('add')){
				this.addNewForm();
				return;
			}else{
				return;
			}
			var _ = this;
			new Ajax.Updater($$('.dump')[0], 'ajax.php', {
				postBody : url,
				evalScripts: true,
				onComplete: function () {
					_.reloadFormList();
				}
			});
		},

		reloadFormList: function(){
			new Ajax.Updater('form_builder_forms', 'plugins.php?plugin=form_builder&form_list', {
				evalScripts: true,
				onComplete: function () {
					document.body.style.cursor = 'default';
				}
			});
		},

		reloadFormContent: function(id){
			Droppables.remove('properties_info_box');
			this.sortableElement.select('.form_line').each(
				function (div){
					Droppables.remove(div);
				}
			);
			Draggables.removeObserver(this);
			this.drags.each(
				function(div){
					div.destroy();
				}
			);
			if (id == null){
				var url = 'plugins.php?plugin=form_builder&list_output';
			}else{
				var url = 'plugins.php?plugin=form_builder&list_output&search_fid='+id;
			}
			new Ajax.Updater('content', url, {
				evalScripts: true,
				onComplete: function () {
					document.body.style.cursor = 'default';
				}
			});
		},

		reloadFormProperties: function(id){
			if (id == null){
				var url = 'plugins.php?plugin=form_builder&form_properties';
			}else{
				var url = 'plugins.php?plugin=form_builder&form_properties&search_fid='+id;
			}
			new Ajax.Updater('form_builder_preferences', url, {
				evalScripts: true,
				onComplete: function () {
					form_builder_tabs = new Control.Tabs('form_builder_tab_list');
					form_builder_tabs.setActiveTab('form_builder_properties');
					document.body.style.cursor = 'default';
				}
			});
		},

		addNewForm: function(){
			document.body.style.cursor = 'wait';
			var url = "ajax_dump=1&action=form_builder&cmd=add&" + this.serialize();
			var _ = this;
			new Ajax.Updater($$('.dump')[0], 'ajax.php', {
				evalScripts: true,
				postBody : url,
				onComplete: function (response) {
					var id = response.getHeader('X-Vivvo-Form-Builder-FormId') || null;
					_.reloadFormList();
					if (id) {
						_.loadForm(id);
					} else {
						//_.removeListeners();
						//_.reloadFormContent();
						//_.reloadFormProperties();
					}
				}
			});

		},

		removeListeners: function(){
			Event.stopObserving ($('field_properties_apply_button'), 'click', this.onApplyHandle, false);
			Event.stopObserving ($('field_properties_validation'), 'click', this.onValidationHandle, false);
			Event.stopObserving ($('form_submit'), 'click', this.onSaveHandle, false);
			if ($('form_delete')){
				Event.stopObserving ($('form_delete'), 'click', this.onDeleteHandle, false);
			}
			Event.stopObserving ($('field_properties_name'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_text'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_label'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_required'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_size'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_column_width'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_max_size'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_rows'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_validation'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_description'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_options'), 'change', this.onPropertiesChangeHandle, false);
			Event.stopObserving ($('field_properties_error_message'), 'change', this.onPropertiesChangeHandle, false);


		},

		loadForm: function(id){
			this.removeListeners();

			document.body.style.cursor = 'wait';
			this.reloadFormContent(id);
			this.reloadFormProperties(id);
		},

		onPropertiesChange: function(){
			this.changed = true;
		},

		onValidation: function (){
			if ($('field_properties_validation').down('select').value == 'reg_exp'){
				$('field_properties_reg_exp').show();
				$('field_properties_reg_exp').down('input').value = '';
			}else{
				$('field_properties_reg_exp').hide();
				$('field_properties_reg_exp').down('input').value = $('field_properties_validation').down('select').value;
			}
		},

		initSortable: function () {
			Sortable.create(
				this.sortableElement,
				{
					tag				: 'div',
					only			: 'form_line',
					dropOnEmpty		: true,
					constraint		: 'vertical'
				}
			);
		},

		initDraggable: function () {
			var _ = this;
			_.drags = [];
			this.sourceElement.select('.draggable').each(
				function(div){
					_.drags.push(new Draggable(div,{revert:true, ghosting: true}));
				}
			);
		},

		initDroppables: function () {
			var _ = this;
			this.sortableElement.select('.form_line').each(
				function (div){
					Droppables.add(div,
						{
							accept: 'draggable',
							overlap: '2',
							hoverclass: 'droppable_hover',
							onHover: _.onHover.bind(_)
						}
					);
				}
			);
		},

		initFormElements: function () {
			var _ = this;
			this.formElements = [];
			this.sortableElement.select('.form_line').each(
				function (div, index){
					_.formElements.push(new vivvo.admin.formBuilder(_, div, index));
				}
			);
			this.formElementNumber = this.formElements.lenght;
			if (!this.formElementNumber) this.formElementNumber = 0;
		},

		onElementClick: function(elem){
			if (this.selectedFormElement && elem){
				if (this.selectedFormElement.element == elem.element){
					return false;
				}else if (this.changed){
					if (!confirm ('Discard changes')){
						return false;
					}
				}
			}else if (this.changed){
				if (!confirm ('Discard changes')){
					return false;
				}
			}

			this.selectedFormElement = elem;
			this.sortableElement.select('.form_line').each(
				function (div, index){
					div.removeClassName('selected');
				}
			);
			return true;
		},

		onApply: function (e){
			if (this.selectedFormElement){

				this.selectedFormElement.applyChanges();
			}
		},

		onEnd : function (eventName, draggable, event){
			element = draggable.element;
			element.removeClassName('draggable_dragging');
			element.removeClassName('draggable_before');
			element.removeClassName('draggable_append');
			element.removeClassName('draggable_after');
			element.removeClassName('draggable_forbidden');
			element.removeClassName('draggable_between');
			if (element.hasClassName('draggable')){
				if (this.overElement){
					this.overElement.removeClassName('droppable_before');
					this.overElement.removeClassName('droppable_append');
					this.overElement.removeClassName('droppable_after');

					if (this.overElement && (this.overElement.hasClassName('droppable_hover') || this.overElement.id == 'properties_info_box')){
						Position.relativize(element);
						element.setStyle({top:0, left:0});
						var template_element = $('template_' + element.id.replace(/type_/,''));
						new_element = template_element.cloneNode(true);
						new_element.id = '';

                        if (this.action == 'before' && this.overElement.id == 'properties_info_box'){
                            this.action = 'after';
                        }
                        
						if (this.action == 'before'){
							var new_line = this.sortableElement.insertBefore(new_element, this.overElement);
						}else if (this.action == 'after' || this.action == 'append'){
							var after_element =  this.overElement.next();
							if (after_element){
								var new_line = this.sortableElement.insertBefore(new_element, after_element);
							}else{
								var new_line = this.sortableElement.appendChild(new_element);
							}
						}

						Sortable.destroy(this.sortableElement);
						this.initSortable();
						this.initDroppables();
						this.formElements.push(new vivvo.admin.formBuilder(this, new_line, this.formElementNumber));
						this.formElementNumber++;
					}
					this.action = null;
					this.overElement = null;
				}
			}
		},

		serialize: function() {
			this.sortableElement.cleanWhitespace();
			var output = [];
			this.formElements.each(
				function(elem, index){
					output.push (elem.serialize(index));
				}
			);
			return $('form_builder_preferences_form').serialize() + '&' + output.join('&');
		}
	}
);

var formBuilderObject;
var form_builder_tabs;

document.observe("dom:loaded",
	function(){
		if ($('menu_form_builder')){
			$('menu_form_builder').addClassName('selected');
		}
		formBuilderObject = new vivvoSortable('formHolder', 'form_builder_properties');
		form_builder_tabs = new Control.Tabs('form_builder_tab_list');
		form_builder_tabs.setActiveTab('form_builder_properties');
	}
);
