<?php

use App\UploadGroup;
use App\UploadFile;

require 'vendor/autoload.php';
require 'config/database.php';

$id = $_POST['id'];
$fileToDelete = UploadFile::find($id);

if($fileToDelete->delete()){
	echo json_encode('Success');
} else {
	echo json_encode('Fail');
}
