<?php

use App\UploadFile;
use App\UploadGroup;

$sessionMiddleware = function($request, $response, $next){
	session_start();

	$args = $request->getAttribute('route')->getArguments();

	if(isset($args['uid']) && $uid = $args['uid']){
		$uploadGroup = UploadGroup::where('download_uid', $uid)->first();
	} else if (isset($args['fileId']) && $fileId = $args['fileId']){
		$file = UploadFile::with('uploadGroup')->find($fileId);
		$uploadGroup = $file->uploadGroup;
		$uid = $uploadGroup->download_uid;
	}

	$isAuthenticated = true;
	
	if(!empty($uploadGroup->password)) { // upload group has password
		if(empty($_SESSION[$uid])){ // session has not authorized the uid
			$isAuthenticated = false;
		} else if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 30)) {
			session_unset();
			session_destroy();
			$isAuthenticated = false;
		}

		if($isAuthenticated){
			// authanteicated
			$_SESSION['LAST_ACTIVITY'] = time();
			$response = $next($request, $response);
		} else {
			// not authenticated
			if($request->isXhr()){
				$response = $response->withJson(['error' => 'Unauthorized access.'], 403);
			} else {
				// redirect to password page
				// $passwordRoute = $this->router->pathFor('password', ['uid' => $uid]);
				$response = $response->withStatus(302)->withHeader('Location', '/password?dest=' . $uid);
			}
		}
	} else {
		// upload group has no password, can be viewed publicly
		$response = $next($request, $response);
	}

	return $response;
};