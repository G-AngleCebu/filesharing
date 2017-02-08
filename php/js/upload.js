var fileUploadProgressTemplate = $('#file-upload-progress').html();
var fileUploadTemplate = $('#file-upload').html();

$(document).ready(function () {
	// parse templates
    Mustache.parse(fileUploadProgressTemplate);
    Mustache.parse(fileUploadTemplate);

    $('#fileupload').fileupload({
        // autoUpload: false,
        // dataType: 'json',
        singleFileUploads: helpers.isSingleFileUpload(),
        add: fileUploadAdd,
        done: fileUploadDone,
        fail: function(e, data) {
            console.log(data);
        },
        progress: fileUploadProgress,
        progressall: fileUploadProgressAll
    });

    // select a file upload type setting
    $('input:radio[name="singleFileUpload"]').change(function(){
    	$('#fileupload').fileupload('option', 'singleFileUploads', helpers.isSingleFileUpload());
    });
});

// File Upload callbacks
function fileUploadAdd(e, data) {
	$files = $("#files");

	$.each(data.files, function(index, file){
		$files.append(Mustache.render(fileUploadProgressTemplate, {
			file: file,
			slug: helpers.slugify(file.name),
	    	progress: 0
	    }));
	});

	if (data.autoUpload || (data.autoUpload !== false && $(this).fileupload('option', 'autoUpload'))) {
		data.process().done(function () {
			data.submit();
		});
	}
}

function fileUploadDone(e, data) {
	$.each(data.files, function (index, file) {
		var slug = helpers.slugify(file.name);
		$('#' + slug).html(Mustache.render(fileUploadTemplate, {
			file: file
		}));
	});
}

function fileUploadProgress(e, data){
	var progress = parseInt(data.loaded / data.total * 100, 10);
	$.each(data.files, function (index, file){
		var slug = helpers.slugify(file.name);
		var progress = parseInt(data.loaded / data.total * 100, 10);

		$('#' + slug).html(Mustache.render(fileUploadProgressTemplate, {
			file: file,
			slug: slug,
			progress: progress
		}));
	});
}

function fileUploadProgressAll(e, data){
	var progress = parseInt(data.loaded / data.total * 100, 10);
	$('#progress .bar').css('width', progress + '%');
}

// HELPERS

var helpers = {
	isSingleFileUpload: function() {
		return $('input[name="singleFileUpload"]#separate').is(':checked');
	},
	slugify: function(text) {
	  return text.toString().toLowerCase()
	    .replace(/\s+/g, '-')           // Replace spaces with -
	    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
	    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
	    .replace(/^-+/, '')             // Trim - from start of text
	    .replace(/-+$/, '');            // Trim - from end of text
	}
}
