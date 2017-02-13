var app = new Vue({
	el: '#app',
	data: {
		uploadGroups: {},
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
				method: 'POST',
				data: {password: this.password},
				context: this,
				success: function(data){
					this.addUploadGroup(data);
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
		// Vue view methods
		addUploadGroup(uploadGroup){
			Vue.set(this.uploadGroups, uploadGroup.id, uploadGroup);
		},

		// HELPERS
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
	}
});