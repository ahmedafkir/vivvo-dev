vivvo.controls.fileUpload = Class.create ();

Object.extend (vivvo.controls.fileUpload.prototype,
	{
		initialize: function (){

		},

		initFileUpload : function (uploadDestination, limit) {

			if (!uploadDestination){
				uploadDestination = '';
			}
			this.destination = uploadDestination;
			var cookies = document.cookie.match(vivvo.cookie_prefix + 'SessionId=(.*?)(;|$)');
            var cookieUserHash = document.cookie.match(vivvo.cookie_prefix + 'UserHash=(.*?)(;|$)');
			var settings = {
				flash_url : '../flash/swfupload.swf',
				upload_url: '../' + vivvo.fs_admin_dir + 'ajax.php', // Relative to the SWF file
                button_width: "85",
                button_height: "24",
                button_placeholder_id: "file_upload_choose",
				button_image_url : "img/button_swf_upload.gif",
                button_text: '<span class="theFont">Upload file</span>',
                button_text_style: ".theFont { font-size: 13; color: #555555; font-family: Arial, Helvetica, sans-serif; font-weight: bold;}",
                button_text_left_padding: 5,
                button_text_top_padding: 2,
				button_action: SWFUpload.BUTTON_ACTION.SELECT_FILE,

				post_params: {
					VSID: cookies[1] || '',
                    VUH: (cookieUserHash) ? cookieUserHash[1] || '' : '',
					SECURITY_TOKEN : vivvo.token,
					action: "asset",
					cmd: "upload",
					falsh_upload: 1,
					UPLOAD_destination: uploadDestination
				},
				file_types_description: "Files",
				file_post_name: 'UPLOAD_file',
				custom_settings : {
					progressTarget : "file_upload_progress",
					cancelButtonId : "file_upload_cancel"
				},
                //debug: true, debug_handler: function () { console.log.apply(console, arguments); },

				file_queued_handler : this.fileQueued.bind(this),
				file_queue_error_handler : this.fileQueueError.bind(this),
				file_dialog_complete_handler : this.fileDialogComplete.bind(this),
				upload_start_handler : this.uploadStart.bind(this),
				upload_progress_handler : this.uploadProgress.bind(this),
				upload_error_handler : this.uploadError.bind(this),
				upload_success_handler : this.uploadSuccess.bind(this),
				upload_complete_handler : this.uploadComplete.bind(this),
				queue_complete_handler : this.queueComplete.bind(this) // Queue plugin event
			};

			if (limit){
				settings.file_queue_limit = limit;
			}

			if (this.options && this.options.search_ext) {
				settings.file_types = $A(this.options.search_ext.split(','))
										.map(function(ext){return '*.' + ext;})
										.join(';');
			}

			if (this.options && this.options.multiple) {
				settings.button_action = SWFUpload.BUTTON_ACTION.SELECT_FILES;
			}

            this.swfUploader = new SWFUpload(settings);

			if (limit == 1){
				//this._swfSelectFiles = this.swfUploader.selectFile.bindAsEventListener(this.swfUploader);
			}else{
				//this._swfSelectFiles = this.swfUploader.selectFiles.bindAsEventListener(this.swfUploader);
			}
			this._cancelQueue = this.swfUploader.cancelQueue.bindAsEventListener(this.swfUploader);
			this._selectDestination = this.selectDestination.bindAsEventListener(this);
			if ($('file_upload_cancel')){
				$('file_upload_cancel').observe('click', this._cancelQueue);
			}
		},

		selectDestination: function (e) {
			var el = Event.element(e);
			if (this.swfUploader){
				this.swfUploader.addPostParam('UPLOAD_destination', el.value.replace(/^files\//,''));
				this.destination = el.value.replace(/^files\//,'');
			}
		},

		fileQueued: function(file) {
			try {
				var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
				progress.setStatus("Pending...");
				progress.toggleCancel(true, this.swfUploader);

			} catch (ex) {

			}

		},

		fileQueueError: function(file, errorCode, message) {
			try {
				if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
					alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
					return;
				}

				var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
				progress.setError();
				progress.toggleCancel(false);

				switch (errorCode) {
				case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
					progress.setStatus("File is too big.");
					break;
				case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
					progress.setStatus("Cannot upload Zero Byte files.");
					break;
				case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
					progress.setStatus("Invalid File Type.");
					break;
				default:
					if (file !== null) {
						progress.setStatus("Unhandled Error");
					}
					break;
				}
			} catch (ex) {

			}
		},

		fileDialogComplete: function(numFilesSelected, numFilesQueued) {
			try {
				if (numFilesSelected > 0) {
					if ($(this.swfUploader.customSettings.cancelButtonId)){
						document.getElementById(this.swfUploader.customSettings.cancelButtonId).disabled = false;
					}
				}
				/* I want auto start the upload and I can do that here */
				this.swfUploader.startUpload();
			} catch (ex)  {

			}
		},

		uploadStart: function(file) {
			try {
				/* I don't want to do any file validation or anything,  I'll just update the UI and
				return true to indicate that the upload should start.
				It's important to update the UI here because in Linux no uploadProgress events are called. The best
				we can do is say we are uploading.
				*/
				var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
				progress.setStatus("Uploading...");
				progress.toggleCancel(true, this.swfUploader);
			}
			catch (ex) {}
			this.isUploading = true;
			return true;
		},

		uploadProgress: function(file, bytesLoaded, bytesTotal) {
			try {
				var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

				var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
				progress.setProgress(percent);
				progress.setStatus("Uploading...");
			} catch (ex) {

			}
		},

		uploadSuccess: function(file, serverData) {
			try {
				var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
				progress.setComplete();
				progress.setStatus("Complete.");
				progress.toggleCancel(false);

			} catch (ex) {

			}
		},

		uploadError: function(file, errorCode, message) {
			try {
				var progress = new FileProgress(file, this.swfUploader.customSettings.progressTarget);
				progress.setError();
				progress.toggleCancel(false);

				switch (errorCode) {
				case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
					progress.setStatus("Upload Error: " + message);
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
					progress.setStatus("Upload Failed.");
					break;
				case SWFUpload.UPLOAD_ERROR.IO_ERROR:
					progress.setStatus("Server (IO) Error");
					break;
				case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
					progress.setStatus("Security Error");
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
					progress.setStatus("Upload limit exceeded.");
					break;
				case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
					progress.setStatus("Failed Validation.  Upload skipped.");
					break;
				case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
					// If there aren't any files left (they were all cancelled) disable the cancel button
					if (this.swfUploader.getStats().files_queued === 0) {
						document.getElementById(this.swfUploader.customSettings.cancelButtonId).disabled = true;
					}
					progress.setStatus("Cancelled");
					progress.setCancelled();
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
					progress.setStatus("Stopped");
					break;
				default:
					progress.setStatus("Unhandled Error: " + errorCode);
					break;
				}
			} catch (ex) {
			}
			this.isUploading = false;
		},

		uploadComplete: function(file) {
			if (this.swfUploader.getStats().files_queued === 0) {
				document.getElementById(this.swfUploader.customSettings.cancelButtonId).disabled = true;
			}
		},

		queueComplete: function(numFilesUploaded) {
			var status = document.getElementById("upload_divStatus");
			if (status) status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
			this.isUploading = false;
		}
	}
);
