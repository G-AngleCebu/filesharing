<?php

use App\UploadGroup;
use App\UploadFile;

require 'vendor/autoload.php';
require 'config/database.php';

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
$uploadHandler = new UploadHandler(['print_response' => false]);

// echo json_encode($uploadHandler->response);

$uploadGroupId = $_POST['uploadGroupId'];

// echo json_encode($uploadGroupId);

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