<?php

use App\UploadGroup;

require 'vendor/autoload.php';
require 'config/database.php';

$uploadGroup = UploadGroup::find($_POST['id']);
$uploadGroup->password = $_POST['password'];

if($uploadGroup->save()) {
	echo $uploadGroup->toJson();
} else {
	response(['error' => 'Failed to set new password.']);
}