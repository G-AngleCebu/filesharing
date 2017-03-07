var app = new Vue({
	el: '#app',
	data: {
		uploadGroup: {},
		uid: '',
		password: '',
	},
	created: function(){
		this.uid = downloadUid;
		this.password = password;

		if(this.uid){
			$.ajax({
				url: '/api/upload_groups/' + this.uid,
				dataType: 'json',
				method: 'GET',
				context: this,
				success: function(data){
					this.uploadGroup = data;

					$.each(data.upload_files, function(index, file){
						this.setPreviewImageSrc(file);
					}.bind(this));
				},
				error: function(error,data){
					this.uploadGroupId = null;
					var response = JSON.parse(error.responseText);
					if(response.error == 'incorrect_pass') {
						alert('incorrect');
					}
				}
			});
		}
	},
	methods: {
		// HELPERS
		humanFileSize(bytes, si) {
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
			return moment(datetime).format('MMMM Do YYYY, h:mm:ss A');
		},
		setPreviewImageSrc(fileElement, file = null){
			var audioTypes = ['audio', 'mp3', 'wav', 'wma'];
			var videoTypes = ['video', 'mp4', 'mov', 'avi'];
			var imageTypes = ['image', 'jpg', 'image/jpg', 'gif', 'image/gif', 'png', 'image/png', 'jpeg', 'image/jpeg'];
			var zipTypes = ['zip'];
			var pdfTypes = ['pdf'];
			var pptTypes = ['ppt', 'pptx'];
			var xlsTypes = ['xls', 'xlsx'];
			var docTypes = ['doc', 'docx'];

			var fileType = file ? file['type'] : fileElement.file_name.split('.').pop();

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
	}
});