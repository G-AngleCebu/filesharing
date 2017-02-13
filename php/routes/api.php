<?php

use App\UploadGroup;
use App\UploadFile;
require('UploadHandler.php');
require 'config/database.php';

$app->group('/api', function() use ($app){
	include 'password_middleware.php';

	$app->post('/upload', function($request, $response, $args){
		error_reporting(E_ALL | E_STRICT);
		$uploadHandler = new UploadHandler(['print_response' => false]);

		$uploadGroupId = $request->getParsedBody()['uploadGroupId'];

		if($uploadGroupId){
			$uploadGroup = UploadGroup::find($uploadGroupId);
		} else {
			$uploadGroup = new UploadGroup;
			$uploadGroup->download_uid = md5(uniqid(rand(), true));
			$uploadGroup->save();
		}

		$uploadFiles = [];
		foreach($uploadHandler->response['files'] as $file){
			$uploadFiles[] = new UploadFile([
				'file_name' => $file->name,
				'file_directory' => 'files',
				'file_size' => $file->size
				]);
		}

		$uploadGroup->uploadFiles()->saveMany($uploadFiles);
		$uploadGroup->load('uploadFiles');

		echo $uploadGroup->toJson();
	});

	// delete file by file id
	$app->post('/delete', function($request, $response){
		$id = $request->getParsedBody()['id'];

		$fileToDelete = UploadFile::find($id);

		if($fileToDelete->delete()){
			echo json_encode('Success');
		} else {
			echo json_encode('Fail');
		}
	});

	// get list of files by upload group id
	$app->post('/upload_groups/{uid}', function($request, $response, $args){
		$uploadGroup = UploadGroup::with('uploadFiles')->where('download_uid', $args['uid'])->first();

		if(!$uploadGroup) {
			http_response_code(404);
			response(['error' => 'Upload group not found.'], true);
		}

		echo $uploadGroup->toJson();
	})->add($passwordMiddleware);
});