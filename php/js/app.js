var app = new Vue({
	el: '#app',
	data: {
		keyword: '123',
		singleFileUpload: false,
		allProgress: 0,
		files: [],
	},
	mounted: function (){
		console.log(downloadUid);

	    $('#fileupload').fileupload({
	        singleFileUploads: this.singleFileUpload,
	        add: this.fileUploadAdd,
	        done: this.fileUploadDone,
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
		}
	},
	methods: {
		deleteFile(index, id){
			this.files.splice(index,1);
			console.log(this.files);

			$.ajax({
				url: 'delete.php',
				type: 'POST',
				data: {id: id},
				success: function(data){
					console.log(data);
				},
				error: function(data){
					console.log(data);
				}
			})
		},
		
		// FILE UPLOAD CALLBACKS

		fileUploadAdd(e, data) {
			// Append the files to the files array
			$.each(data.files, function(index, file){
				this.files.push({
					id: null,
					name: file.name,
					size: file.size,
					slug: this.slugify(file.name),
					progress: 0
				});
			}.bind(this));

			// Upload the files
			if (data.autoUpload || (data.autoUpload !== false && $('#fileupload').fileupload('option', 'autoUpload'))) {
				data.process().done(function () {
					data.submit();
				});
			}
		},
		fileUploadDone(e, data) {
			var uploadedFiles = JSON.parse(data.result);
			$.each(uploadedFiles, function(i, uploadedFile){
				console.log(uploadedFile);
				var file = this.getFileByFilename(uploadedFile.file_name);
				file['id'] = uploadedFile.id;
			}.bind(this));

			$.each(data.files, function (index, file) {
				console.log(file);
				// var slug = helpers.slugify(file.name);
				// $('#' + slug).html(Mustache.render(fileUploadTemplate, {
				// 	file: file
				// }));
			});
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
		getFileByFilename(filename) {
			return this.files.filter(function(f){
				return f.name == filename;
			})[0];
		},
		slugify: function(text) {
		  return text.toString().toLowerCase()
		    .replace(/\s+/g, '-')           // Replace spaces with -
		    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
		    .replace(/^-+/, '')             // Trim - from start of text
		    .replace(/-+$/, '');            // Trim - from end of text
		},
	}
});