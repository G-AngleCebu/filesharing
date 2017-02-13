var app = new Vue({
	el: '#app',
	data: {
		singleFileUpload: false,
		allProgress: 0,
		files: [],
		uploadGroups: {},
		uploadGroupId: null
	},
	created: function(){
		this.uploadGroupId = downloadUid;

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
					alert(response.error);
				}
			});
		}
	},
	mounted: function (){
	    $('#fileupload').fileupload({
	    	dataType: 'json',
	        singleFileUploads: this.singleFileUpload,
	        add: this.fileUploadAdd,
	        done: this.fileUploadDone,
	        formData: {
	        	uploadGroupId: this.uploadGroupId
	        },
	        fail: function(e, data) {
	            alert("Upload failed. See console.");
	            console.error(e);
	            console.error(data);
	        },
	        processdone: function(e, data){
	        	console.log(data);
	        },
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
		generatePassword(index){
			Vue.set(this.uploadGroups[index], 'password', this.createARandomPassword());
			console.log(this.uploadGroups);
		},
		shareEmail(index){
			console.log(this.uploadGroups[index].download_uid);
		},
		setPassword(index){
			var uploadGroup = this.uploadGroups[index];
			console.log("Set pass " + uploadGroup.password);
			console.log(uploadGroup);

			$.ajax({
				url: 'update_password.php',
				method: 'POST',
				dataType: 'json',
				data: {
					id: uploadGroup.id,
					password: uploadGroup.password 
				},
				success: function(data){
					// TODO
					console.log("Set password successful.");
				},
				error: function(data){
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

		fileUploadAdd(e, data) {
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

				this.files.push({
					id: null,
					name: file.name,
					size: file.size,
					slug: this.slugify(file.name),
					progress: 0
				});
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
			var uploadGroup = data.result;
			var uploadedFiles = uploadGroup.upload_files;

			// this.uploadGroups[uploadGroup.id] = uploadGroup;
			this.addUploadGroup(uploadGroup);

			// remove the files that finished uploading
			$.each(data.files, function(index, file){
				var fileIndex = this.getFileIndexByFilename(file.name);
				this.files.splice(fileIndex, 1);
			}.bind(this));
		},
		fileUploadProgress(e, data){
			$.each(data.files, function (index, file){
				var progress = parseInt(data.loaded / data.total * 100, 10);
				// this.files[index].progress = progress;
				var file = this.getFileByFilename(file.name);
				file.progress = progress;
			// 	var slug = this.slugify(file.name);
			// 	var progress = parseInt(data.loaded / data.total * 100, 10);

			// 	$('#' + slug).html(Mustache.render(fileUploadProgressTemplate, {
			// 		file: file,
			// 		slug: slug,
			// 		progress: progress
			// 	}));
			}.bind(this));
		},
		fileUploadProgressAll(e, data){
			var progress = parseInt(data.loaded / data.total * 100, 10);
			this.allProgress = progress;
		},

		// HELPERS
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
		slugify: function(text) {
		  return text.toString().toLowerCase()
		    .replace(/\s+/g, '-')           // Replace spaces with -
		    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
		    .replace(/^-+/, '')             // Trim - from start of text
		    .replace(/-+$/, '');            // Trim - from end of text
		},
		createARandomPassword: function(numberOfCharacters = 8){
			return Math.random().toString(36).slice(-numberOfCharacters);
		}
	}
});