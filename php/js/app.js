var app = new Vue({
	el: '#app',
	data: {
		showFileInput: true,
		singleFileUpload: false,
		allProgress: 0,
		files: [],
		filesAreHovering: false,
		hoverTimeout: null,
		uploadGroups: {},
		uploadGroupId: null,
		email_message: null,
		email_from: null,
		email_to: null,
		email_cc: null,
		email_bcc: null
	},
	created: function(){
		this.uploadGroupId = downloadUid;
		// init checkboxes
		$('.ui.checkbox#separate').checkbox('set checked');
		$('.ui.checkbox#singleFileUpload').checkbox();

		if(this.uploadGroupId){
			$.ajax({
				url: 'get.php',
				dataType: 'json',
				method: 'GET',
				context: this,
				data: {
					uid: this.uploadGroupId
				},
				success: function(data){
					this.addUploadGroup(data);
				},
				error: function(error){
					this.uploadGroupId = null;
					var response = JSON.parse(error.responseText);
					// alert(response.error);
				}
			});
		}
	},
	mounted: function (){

		// init progress bar
		$('#all-progress').progress({percent: this.allProgress});

	    $('#fileupload').fileupload({
	    	dataType: 'json',
	        singleFileUploads: this.singleFileUpload,
	        add: this.fileUploadAdd,
	        done: this.fileUploadDone,
	        formData: {
	        	uploadGroupId: this.uploadGroupId
	        },
	        fail: function(e, data) {
	            // alert("Upload failed. See console.");
	            console.error(e);
	            console.error(data);
	        },
	        dragover: this.fileUploadDragOver,
	        progress: this.fileUploadProgress,
	        progressall: this.fileUploadProgressAll
	    });
	},
	watch: {
		singleFileUpload: function(val, oldVal) {
	    	$('#fileupload').fileupload('option', 'singleFileUploads', val);
		},
	},
	methods: {
		// Vue view methods
		addUploadGroup(uploadGroup){
			Vue.set(this.uploadGroups, uploadGroup.id, uploadGroup);
		},
		browseFiles(){
			$('#fileupload').click();			
		},
		generatePassword(index){
			console.log(this.uploadGroups[index]);
			Vue.set(this.uploadGroups[index], 'password', this.generateRandomString());
			console.log(this.uploadGroups[index]);
		},
		shareEmail(index){
			var uploadGroup = this.uploadGroups[index];

			$('.ui.modal').modal({
				onApprove: function(){
					var recipient = this.email_to;
					var sender = this.email_from;
					var cc = this.email_cc;
					var bcc = this.email_bcc;
					var message = this.email_message;
					var uid = uploadGroup.download_uid;
					var password = uploadGroup.password || '';
					var isSeparatePassword = $('.ui.checkbox#separate').checkbox('is checked');

					$.ajax({
						url: 'api/email',
						type: 'POST',
						dataType: 'json',
						data: {
							recipient: recipient,
							sender: sender,
							cc: cc,
							bcc: bcc,
							message: message,
							uid: uid,
							password: password,
							isSeparatePassword: isSeparatePassword ? 1 : 0
						},
						success: function(data) {
							console.log(data);
						},
						error: function(data) {
							console.error(data);
						}
					});

					// var mailto = "mailto:";
					// mailto += recipient;
					// mailto += "?";
					// mailto += "cc=" + cc;
					// mailto += "&bcc=" + bcc;
					// mailto += "&subject=" + subject;
					// mailto += "&body=" + message;

					// window.location.href = mailto;
				}.bind(this)
			}).modal('show');
		},
		setPassword(index){
			var uploadGroup = this.uploadGroups[index];
			console.log("Set pass " + uploadGroup.password);

			$.ajax({
				url: '/api/upload_groups/' + uploadGroup.id + '/password',
				method: 'POST',
				dataType: 'json',
				data: {
					password: uploadGroup.password 
				},
				success: function(data){
					// TODO
					alert("Password set successfully.");
					console.log("Set password successful.");
				},
				error: function(data){
					alert("Error setting password.");
					console.log(data);
				}
			});
		},
		deleteFile(groupIndex, fileIndex, id = null){
			this.uploadGroups[groupIndex].upload_files.splice(fileIndex, 1);

			if(id){
				$.ajax({
					url: 'api/delete',
					type: 'POST',
					data: {id: id},
					success: function(data){
						console.log(data);
					},
					error: function(data){
						console.log(data);
					}
				});
			}
		},
		
		// FILE UPLOAD CALLBACKS
		fileUploadDragOver(e, data){
			clearTimeout(this.hoverTimeout);
			this.hoverTimeout = null;

			if(!this.filesAreHovering){
				this.fileUploadDragEnter();
			}

			this.hoverTimeout = setTimeout(function(){
				this.fileUploadDragLeave();
			}.bind(this), 100);

		},
		fileUploadDragEnter(){
			console.log("ENTER");
			this.filesAreHovering = true;
		},
		fileUploadDragLeave(){
			console.log("leave");
			this.filesAreHovering = false;
			this.hoverTimeout = null;
		},
		fileUploadAdd(e, data) {
			this.showFileInput = false;

			// Append the files to the files array
			var skipUpload = false;
			$.each(data.files, function(index, file){
				if(this.hasDuplicate(file.name)){
					if(!confirm("Overwrite?")) {
						// don't overwrite, skip to next iteration
						skipUpload = true;
						return null;
					} else {
						this.deleteFile(index);
					}
				}

				// add file to files array
				this.files.push({
					id: null,
					key: this.generateRandomGuid(),
					name: file.name,
					size: file.size,
					slug: this.slugify(file.name),
					previewImageSrc: null,
					progress: 0
				});

				var fileElement = this.files[this.files.length - 1];
				this.setPreviewImageSrc(fileElement, file);
			}.bind(this));

			// if user pressed cancel on overwrite prompt
			if(skipUpload){
				return;
			}

			// Upload the files
			if (data.autoUpload || (data.autoUpload !== false && $('#fileupload').fileupload('option', 'autoUpload'))) {
				data.process().done(function () {
					data.submit();
				});
			}
		},
		fileUploadDone(e, data) {
			console.log(data.result);
			console.log('done');
			
			var newlyUploadedGroup = data.result;

			this.addUploadGroup(newlyUploadedGroup);

			$.each(newlyUploadedGroup.upload_files, function(index, file){
				this.setPreviewImageSrc(file);
			}.bind(this));

			// remove the files that finished uploading
			$.each(data.files, function(index, file){
				var fileIndex = this.getFileIndexByFilename(file.name);
				this.files.splice(fileIndex, 1);
			}.bind(this));
		},
		fileUploadProgress(e, data){
			$.each(data.files, function (index, file){
				var progress = parseInt(data.loaded / data.total * 100, 10);
				var file = this.getFileByFilename(file.name);
				file.progress = progress;

				$('#progress\\[' + file.key + '\\]').progress({percent: progress});
				// $('.ui.progress').progress({percent: $(this).data('percent')});
			}.bind(this));
		},
		fileUploadProgressAll(e, data){
			var progress = parseInt(data.loaded / data.total * 100, 10);
			this.allProgress = progress;
			$('#all-progress').progress({percent: this.allProgress});

			if(progress == 100){
				console.log("DONE ALL");
			}
		},

		// HELPERS
		setPreviewImageSrc(fileElement, file = null){
			var audioTypes = ['audio', 'mp3', 'wav', 'wma'];
			var videoTypes = ['video', 'mp4', 'mov', 'avi'];
			var imageTypes = ['image', 'jpg', 'image/jpg', 'gif', 'image/gif', 'png', 'image/png', 'jpeg', 'image/jpeg'];
			var zipTypes = ['zip', 'application/zip'];
			var pdfTypes = ['pdf'];
			var pptTypes = ['ppt', 'pptx'];
			var xlsTypes = ['xls', 'xlsx'];
			var docTypes = ['doc', 'docx'];

			var fileType = file ? file['type'] : fileElement.file_name.split('.').pop();
			// console.log(fileType);

			if(imageTypes.includes(fileType)) {
				if(file){
					var reader = new FileReader();
					reader.onload = function(e){
						fileElement.previewImageSrc = e.target.result;
					}
					reader.readAsDataURL(file);
				} else {
					fileElement.previewImageSrc = '/download/file/' + fileElement.id;
				}
			} else if(audioTypes.includes(fileType)) {
				fileElement.previewImageSrc = 'http://placehold.it/100x100?text=audio'
			} else if(videoTypes.includes(fileType)) {
				fileElement.previewImageSrc = 'http://placehold.it/100x100?text=video'
			} else if(pdfTypes.includes(fileType)) {
				fileElement.previewImageSrc = 'http://placehold.it/100x100?text=pdf'
			} else if(zipTypes.includes(fileType)) {
				fileElement.previewImageSrc = 'http://placehold.it/100x100?text=zip'
			} else if(pptTypes.includes(fileType)) {
				fileElement.previewImageSrc = 'http://placehold.it/100x100?text=ppt'
			} else if(xlsTypes.includes(fileType)) {
				fileElement.previewImageSrc = 'http://placehold.it/100x100?text=xls'
			} else if(docTypes.includes(fileType)) {
				fileElement.previewImageSrc = 'http://placehold.it/100x100?text=doc'
			} else {
				fileElement.previewImageSrc = 'http://placehold.it/100x100'
			}
		},
		hasDuplicate(filename){
			var hasDuplicate = false;
			$.each(this.files, function(index, vueFile){
				if(vueFile.name == filename){
					hasDuplicate = true;
				}
			});

			return hasDuplicate;
		},
		getFileByFilename(filename) {
			return this.files.filter(function(f){
				return f.name == filename;
			})[0] || null;
		},
		getFileIndexByFilename(filename) {
			var index = null;

			$.each(this.files, function(i, file){
				if(file.name == filename){
					index = i;
					return index;
				}
			});

			return index;
		},
		copyToClipboard(){

		},
		humanFileSize(bytes, si = true) {
			var thresh = si ? 1000 : 1024;
			if(Math.abs(bytes) < thresh) {
				return bytes + ' bytes';
			}
			var units = ['kB','MB','GB','TB','PB','EB','ZB','YB'];
			var u = -1;
			do {
				bytes /= thresh;
				++u;
			} while(Math.abs(bytes) >= thresh && u < units.length - 1);
			return bytes.toFixed(1)+' '+units[u];
		},
		humanDateTime(datetime) {
			return moment(datetime).format('MMMM Do YYYY, h:mm:ss a');
		},
		slugify: function(text) {
		  return text.toString().toLowerCase()
		    .replace(/\s+/g, '-')           // Replace spaces with -
		    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
		    .replace(/^-+/, '')             // Trim - from start of text
		    .replace(/-+$/, '');            // Trim - from end of text
		},
		generateRandomString: function(numberOfCharacters = 8){
			return Math.random().toString(36).slice(-numberOfCharacters);
		},
		generateRandomGuid: function(){
			return Date.now() + this.generateRandomString(4);
		}
	}
});