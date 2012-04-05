vivvo.admin.maintenance = Class.create ();

Object.extend(vivvo.admin.maintenance.prototype, vivvo.controls.fileUpload.prototype);

Object.extend (
	vivvo.admin.maintenance.prototype,
	{
        selected_dir: 'backup',
        options: {
            search_ext: 'gz'
        },
		initialize: function (){
			this.onBackupHandle = this.onBackup.bindAsEventListener(this);
			Event.observe ($('create_backup'), 'click', this.onBackupHandle, false);

			this.onCelanCacheHandle = this.onCelanCache.bindAsEventListener(this);
			Event.observe ($('clean_cache'), 'click', this.onCelanCacheHandle, false);

			Event.observe ($('relate_articles'), 'click', function() {
				$('manual_tasks_window').src = this.baseURL + 'db_maintence.php?relate_articles';
			}.bindAsEventListener(this), false);

			this.onDbMaintenanceHandle = this.onDbMaintenance.bindAsEventListener(this);
			Event.observe($('database_maintenance'), 'click', this.onDbMaintenanceHandle, false);

			this.baseURL = this.getBaseURL();

			this.onSubmitCronFormHandle = this.onSubmitCronForm.bindAsEventListener(this);
			$$('#cron_tasks form').invoke('observe', 'submit', this.onSubmitCronFormHandle);

			new Control.Tabs('maintenance_tabs');

			$$('#content .expand').each(
				function(el){
					Event.observe(el, 'click',
						function (e){
							var el = Event.element(e);
							el.up('.form_box_header').next('.form_box_body').toggle();
							el.toggleClassName('collapse');
							if (el.hasClassName('collapse')){
								el.update(vivvo.admin.lang.get('LNG_ADMIN_HIDE_FORM'));
							}else{
								el.update(vivvo.admin.lang.get('LNG_ADMIN_SHOW_FORM'));
							}
							//$('category_save_button').makePositioned();
						}
					);
				}
			);

			$$('.cron_task_modal').each(
				function (a){
					new vivvo.controls.cronTask(a);
				}
			);

            if (vivvo.user.can('UPLOAD')) {
                this.initFileUpload('backup');
            }
		},

		getBaseURL: function () {
			var path = document.location.toString();
			path = path.replace(/maintenance\.php.*/,'');
			return path;
		},

		onDbMaintenance: function(){
			$('manual_tasks_window').src = this.baseURL + 'db_maintence.php?database_maintenance';
		},

		onCelanCache: function(){
			$('manual_tasks_window').src = this.baseURL + 'db_maintence.php?clean_cache';
		},

		onBackup: function(){
			$('report_window').src = this.baseURL + 'db_maintence.php?backup&restart=1';
		},

		onSubmitCronForm: function (e){
			var el = Event.element(e);
			var params = el.serialize(true);
			if (!params.TASK_time_mask.match(/^([-0-9,\/*]+)\s+([-0-9,\/*]+)\s+([-0-9,\/*]+)\s+([-0-9,\/*]+)\s+([-0-7,\/*]+|(-|\/|Sun|Mon|Tue|Wed|Thu|Fri|Sat)+)$/i) && params.TASK_time_mask != ''){
				alert('wrong time mask');
				return;
			}

			params.action = 'vivvoCore';
			params.cmd = 'cronTask';
			params.ajax_dump = 1;

			vivvo.admin.utils.ajaxAction(params);
		},

        queueComplete: function(numFilesUploaded) {
            var that = this;
            var status = document.getElementById("upload_divStatus");
            if (status) status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
            this.isUploading = false;
            reload_backup_files();
        }
	}
);

function backup_completed(){
	reload_backup_files();
}

function reload_backup_files(){
    new Ajax.Updater('backup_files', 'ajax.php?backup_files_list=1');
}

document.observe("dom:loaded", function() {
	vivvoMaintenanceObject = new vivvo.admin.maintenance();
});
