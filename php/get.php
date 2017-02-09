<?php

use App\UploadGroup;
use App\UploadFile;

require 'vendor/autoload.php';
require 'config/database.php';

$uploadGroup = UploadGroup::with('uploadFiles')->find(1);

echo $uploadGroup->toJson();