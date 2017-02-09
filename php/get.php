<?php

use App\UploadGroup;
use App\UploadFile;

require 'vendor/autoload.php';
require 'config/database.php';

$uploadGroup = UploadGroup::with('uploadFiles')->where('download_uid', $_GET['uid'])->first();

if(!$uploadGroup) {
	http_response_code(404);
	response(['error' => 'Upload group not found.'], true);
}

echo $uploadGroup->toJson();