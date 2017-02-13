<?php

require 'vendor/autoload.php';
require_once 'password_middleware.php';
use App\UploadGroup;

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

$container['view'] = function($container){
	return new \Slim\Views\PhpRenderer('./views/');
};

// Index route (upload files)
$app->get('/', function ($request, $response, $args) {
	$response = $this->view->render($response, "upload.php");
	return $response;
})->setName('upload_page');

$app->post('/{uid}', function($request, $response, $args){
	$password = '';

	if(isset($request->getParsedBody()['password'])){
		$password = $request->getParsedBody()['password'];
	}

	$response = $this->view->render($response, "download.php", [
		'uid' => $args['uid'],
		'p' => $password
	]);

	return $response;
})->add($passwordMiddleware);

// View upload group
$app->get('/{uid}', function($request, $response, $args){
	$response = $this->view->render($response, "download.php", [
		'uid' => $args['uid'],
		'p' => ''
	]);
	return $response;
})->setName('download_page')->add($passwordMiddleware);

require_once 'routes/download.php';
require_once 'routes/api.php';

// Run app
$app->run();