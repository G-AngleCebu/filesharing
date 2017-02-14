<?php

use App\UploadFile;
use App\UploadGroup;

$passwordMiddleware = function($request, $response, $next){
	$args = $request->getAttribute('route')->getArguments();

	if(isset($args['uid']) && $uid = $args['uid']){
		$uploadGroup = UploadGroup::where('download_uid', $uid)->first();
	} else if (isset($args['fileId']) && $fileId = $args['fileId']){
		$file = UploadFile::with('uploadGroup')->find($fileId);
		$uploadGroup = $file->uploadGroup;
		$uid = $uploadGroup->download_uid;
	}

	$isAuthenticated = false;
	$messages = [];

	if(!empty($uploadGroup->password)){
		$postBody = $request->getParsedBody();

		if($postBody['password'] == $uploadGroup->password){
			$isAuthenticated = true;
		} else if(!empty($postBody['password'])) {
			// incorrect password
			$messages[] = 'Incorrect password. Try again.';
		}
	} else {
		// upload group doesn't have password
		$isAuthenticated = true;
	}

	if($isAuthenticated){
		$response = $next($request, $response);
		return $response;
	} else {
		// not authenticated
		$response = $this->view->render($response, "password.php", [
			'uid' => $args['uid'],
			'messages' => $messages
		]);
		return $response;
	}
};