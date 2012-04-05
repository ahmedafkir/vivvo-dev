vivvo.controls.cronTask = Class.create ();

Object.extend (vivvo.controls.cronTask.prototype,
	{
		initialize: function (link, options){
			this.options = {
				onSelect: Prototype.emptyFunction,
				butonLabel: 'Add',
				confirmMessage: 'Add selected files',
				noneSelected: 'No file(s) selected.'
			}

			if (link.previous('input')){
				this.valueHolder = link.previous('input');
			}else{
				this.valueHolder = false;
			}

			Object.extend(this.options,options || {});
			this.modal = new Control.Modal(link,
				{
					afterOpen: this.openModal.bind(this),
					beforeClose: this.closeModal.bind(this),
					onSuccess: this.onDialogLoad.bind(this)
				}
			);
		},

		openModal: function(){
			//throw $break;
		},

		closeModal: function(){
			//console.log('closeModal');
		},

		fillMask: function (mask, optionName, selectId) {
			if (mask.match(/\*(\/[-0-9]+)?/)){
				try{
					$('cron_task_time_editor').getInputs('radio',optionName).find(function(radio) {return radio.value == mask}).writeAttribute("checked", "checked");
					$(selectId).disable();
				}catch (e){
					$('cron_task_time_editor').getInputs('radio',optionName).find(function(radio) {return radio.value == ''}).writeAttribute("checked", "checked");
					$(selectId).enable();
				}
				$$('#' + selectId + ' option').each(function(opt){opt.selected = false;});
			}else if (mask.match(/[-0-9,]+/)){
				$('cron_task_time_editor').getInputs('radio',optionName).find(function(radio) {return radio.value == ''}).writeAttribute("checked", "checked");
				var values = mask.split(',');
				$$('#' + selectId + ' option').each(function(opt){opt.selected = (values.indexOf(opt.value) !== -1);});
				$(selectId).enable();
			}else{
				$('cron_task_time_editor').getInputs('radio',optionName).find(function(radio) {return radio.value == '*'}).writeAttribute("checked", "checked");
				$$('#' + selectId + ' option').each(function(opt){opt.selected = false;});
				$(selectId).disable();
			}
		},

		analizeMask: function (optionName, selectId) {
			var mask = $('cron_task_time_editor').getInputs('radio',optionName).find(function(radio) {return radio.checked}).value;
			if (mask == ''){
				mask = [];
				$$('#' + selectId + ' option').each(function(opt){if (opt.selected) mask.push(opt.value);});
				if (mask.length > 0){
					mask = mask.join(',');
				}else{
					mask = '0';
				}
			}
			return mask;
		},

		onSave: function () {
			var mask = '';
			mask += this.analizeMask ('minute_select_predefiend', 'minute_select') + ' ';
			mask += this.analizeMask ('hour_select_predefiend', 'hour_select') + ' ';
			mask += this.analizeMask ('day_select_predefiend', 'day_select') + ' ';
			mask += this.analizeMask ('month_select_predefiend', 'month_select') + ' ';
			mask += this.analizeMask ('dow_select_predefiend', 'dow_select');
			if (this.valueHolder) this.valueHolder.value = mask;
			this.modal.close();
		},

		onDialogLoad: function () {
			if (this.valueHolder && this.valueHolder.value.match(/^([-0-9,\/*]+)\s+([-0-9,\/*]+)\s+([-0-9,\/*]+)\s+([-0-9,\/*]+)\s+([-0-7,\/*]+|(-|\/|Sun|Mon|Tue|Wed|Thu|Fri|Sat)+)$/i)){
				var mask = this.valueHolder.value.split(/\s+/);
			}else{
				var mask = ['*','*','*','*','*'];
			}

			this.fillMask(mask[0], 'minute_select_predefiend', 'minute_select');
			this.fillMask(mask[1], 'hour_select_predefiend', 'hour_select');
			this.fillMask(mask[2], 'day_select_predefiend', 'day_select');
			this.fillMask(mask[3], 'month_select_predefiend', 'month_select');
			this.fillMask(mask[4], 'dow_select_predefiend', 'dow_select');

			this._onSaveHandle = this.onSave.bindAsEventListener(this);
			Event.observe($('cron_task_save'), 'click', this._onSaveHandle);

			this._onChangeRadioHandle = this.onChangeRadio.bindAsEventListener(this);
			$('cron_task_time_editor').getInputs('radio').invoke('observe', 'click' , this._onChangeRadioHandle);
		},

		onChangeRadio: function (e) {
			var el = Event.element(e);

			if (el.name == 'minute_select_predefiend'){
				if (el.value == ''){
					$('minute_select').enable();
				}else{
					$('minute_select').disable();
				}
			}else if (el.name == 'hour_select_predefiend'){
				if (el.value == ''){
					$('hour_select').enable();
				}else{
					$('hour_select').disable();
				}
			}else if (el.name == 'day_select_predefiend'){
				if (el.value == ''){
					$('day_select').enable();
				}else{
					$('day_select').disable();
				}
			}else if (el.name == 'dow_select_predefiend'){
				if (el.value == ''){
					$('dow_select').enable();
				}else{
					$('dow_select').disable();
				}
			}else if (el.name == 'month_select_predefiend'){
				if (el.value == ''){
					$('month_select').enable();
				}else{
					$('month_select').disable();
				}
			}
		},

		onClose: function(e){
			this.modal.close();
		}
	}
);