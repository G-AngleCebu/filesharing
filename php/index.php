<?php

require 'vendor/autoload.php';
require_once 'helpers/helpers.php';
require_once 'middleware.php';
use App\UploadGroup;

use Aws\Common\Aws;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// Create and configure Slim app
$config = ['settings' => [
	'addContentLengthHeader' => false,
	'debug' => true,
]];

// Config
$configuration = [
	'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);
$container = $app->getContainer();

// php renderer
$container['view'] = function($container){
	return new \Slim\Views\PhpRenderer('./views/');
};

session_start();

// register flash
$container['flash'] = function() {
	return new \Slim\Flash\Messages();
};

// Index route (upload files)
$app->get('/', function ($request, $response, $args) {
	$response = $this->view->render($response, "upload.php", [
		'baseUrl' => get_base_url()
	]);
	return $response;
})->setName('upload_page');



// enter password
$app->post('/{uid}', function($request, $response, $args){
	if(isset($args['uid']) && $uid = $args['uid']){
		$uploadGroup = UploadGroup::where('download_uid', $uid)->first();
	} else if (isset($args['fileId']) && $fileId = $args['fileId']){
		$file = UploadFile::with('uploadGroup')->find($fileId);
		$uploadGroup = $file->uploadGroup;
		$uid = $uploadGroup->download_uid;
	}


	$isAuthenticated = false;

	if(!empty($uploadGroup->password)){
		$postBody = $request->getParsedBody();

		if($postBody['password'] == $uploadGroup->password){
			$isAuthenticated = true;
		} else if(!empty($postBody['password'])) {
			// incorrect password
			$this->flash->addMessage('error', 'Incorrect password. Try again.');
		}
	} else {
		// upload group doesn't have password
		$isAuthenticated = true;
	}

	if($isAuthenticated){
		$_SESSION[$uid] = true;
		$_SESSION['LAST_ACTIVITY'] = time();
		$response = $response->withStatus(302)->withHeader("Location", $uid);
	} else {
		$passwordRoute = $this->router->pathFor('password', [], ['dest' => $uid]);
		$response = $response->withStatus(302)->withHeader('Location', $passwordRoute);
		// $response = $response->withStatus(302)->withHeader('Location', '/password?dest=' . $uid);
	}

	return $response;
});

$app->get('/sessions', function($req, $res){
	echo '<pre>';
	print_r($_SESSION);
	echo '</pre>';
});

$app->get('/sessions_destroy', function($req, $res){
	session_unset();
	session_destroy();
	echo 'Sessions destroyed.<br/>';

	echo '<pre>';
	print_r($_SESSION);
	echo '</pre>';
});

$app->get('/password', function($request, $response, $args){
	$response = $this->view->render($response, "password.php", [
		'uid' => $request->getQueryParams()['dest'],
		'messages' => $this->flash->getMessages()
	]);
	return $response;
})->setName('password');

$app->get('/s3', function($req, $res, $args){
	$response = $this->view->render($response, "s3.php");
	return $response;
});

// View upload group
$app->get('/{uid}', function($request, $response, $args){
	$response = $this->view->render($response, "download.php", [
		'uid' => $args['uid'],
		'p' => '',
		'baseUrl' => get_base_url()
	]);
	return $response;
})->setName('download_page')->add($sessionMiddleware);

require_once 'routes/download.php';
require_once 'routes/api.php';

// Run app
$app->run();