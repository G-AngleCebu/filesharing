<?php

use App\UploadGroup;
use App\UploadFile;

$app->group('/download', function() use ($app){
	include 'password_middleware.php';

	// download single file
	$app->post('/file/{fileId}', function($request, $response, $args){ 
		$uploadFile = UploadFile::with('uploadGroup')->find($args['fileId']);
		$uploadGroup = $uploadFile->uploadGroup;

		$filename = $uploadFile->file_name;

		$fileDownload = Apfelbox\FileDownload\FileDownload::createFromFilePath('files/'.$filename);
		$fileDownload->sendDownload($filename);
	})->add($passwordMiddleware);

	// download group as zip
	$app->post('/group/{groupId}', function($request, $response, $args){
		$uploadGroup = UploadGroup::with('uploadFiles')->find($args['groupId']);

		$files = $uploadGroup->uploadFiles->pluck('file_name');
		$files = $files->map(function($item, $key){
			return 'files/' . $item;
		});

	    # create new zip opbject
		$zip = new ZipArchive();

	    # create a temp file & open it
		$tmp_file = tempnam('.','');
		$zip->open($tmp_file, ZipArchive::CREATE);

	    # loop through each file
		foreach($files as $file){
	    # download file
			$download_file = file_get_contents($file);

	    #add it to the zip
			$zip->addFromString(basename($file),$download_file);
		}

	    # close zip
		$zip->close();

	    # send the file to the browser as a download
		header('Content-disposition: attachment; filename=download.zip');
		header('Content-type: application/zip');
		readfile($tmp_file);
		unlink($tmp_file);
	})->add($passwordMiddleware);
});