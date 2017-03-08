<?php

use App\UploadGroup;
use App\UploadFile;
require 'UploadHandler.php';
require 'config/database.php';
// require 'PHPMailerAutoload.php';

$app->group('/api', function() use ($app){
	include 'middleware.php';

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

	// get list of files by upload group download_uid
	$app->get('/upload_groups/{uid}', function($request, $response, $args){
		$uploadGroup = UploadGroup::with('uploadFiles')->where('download_uid', $args['uid'])->first();

		if(!$uploadGroup) {
			http_response_code(404);
			response(['error' => 'Upload group not found.'], true);
		}

		echo $uploadGroup->toJson();
	})->add($sessionMiddleware);

	// set password of upload group
	$app->post('/upload_groups/{id}/password', function($request, $response, $args){
		$uploadGroup = UploadGroup::find($args['id']);
		$uploadGroup->password = $request->getParsedBody()['password'];

		if($uploadGroup->save()) {
			$_SESSION[$uploadGroup->download_uid] = true;
			$_SESSION['LAST_ACTIVITY'] = time();
			$response = $response->withJson($uploadGroup);
		} else {
			$response = $response->withJson(['error' => 'Failed to set new password.']);
		}

		return $response;
	});

	// email
	$app->post('/email', function($request, $response, $args){
		$mail = new PHPMailer;

		$postBody = $request->getParsedBody();
		$from = $postBody['sender'];
		$message = $postBody['message'];
		$uid = $postBody['uid'];
		$recipient = preg_split('/[\ \n\,]+/', $postBody['recipient']);
		$cc = preg_split('/[\ \n\,]+/', $postBody['cc']);
		$bcc = preg_split('/[\ \n\,]+/', $postBody['bcc']);
		$password = $postBody['password'];
		$isSeparatePassword = $postBody['isSeparatePassword'];
		$expiryDate = "12/12/20";
		$result = [];

		include 'config/smtp.php';

		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = SMTP_USERNAME;
		$mail->Password = SMTP_PASSWORD;
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;

		$mail->setFrom('ganglecebudev@gmail.com', $postBody['sender'] . ' (via G-Angle File Sharing App)');

		// add recipients
		foreach($recipient as $recipientEmail){ $mail->addAddress($recipientEmail); }
		foreach($cc as $ccEmail){ $mail->addCC($ccEmail); }
		foreach($bcc as $bccEmail){ $mail->addBCC($bccEmail); }

		// email content
		$subject = "File Sharing Notice";
		if($isSeparatePassword && !empty($password)){
			$subject .= " #1";
		}

		$mail->isHTML(true);
		$mail->Subject = $subject;
		$body = "<p>Please see the URL below for the files shared to you by {$from}. This file will be deleted on <b>{$expiryDate}</b>.</p>";

		if($isSeparatePassword && !empty($password)){
			$body .= "<p>The password will be sent out shortly!</p>";
		}

		// add upload group url
		$url = get_base_url() . "/" . $uid;
		$body .= "<p><a href='{$url}'>View file(s)</a></p>";

		// add custom message
		if(!empty($message)){
			$body .= "<p>{$from} said:</p>";
			$body .= "<p><i>{$message}</i></p>";
		}

		$mail->Body = $body; 
		$mail->AltBody = $message;

		// send main message
		if(!$mail->send()) {
			$result = [
				'error' => 'There was a problem sending the e-mail. Please try again.',
				'info' => $mail->ErrorInfo
			];
		} else {
			$result = ['success' => 'E-mail sent successfully.'];

			// if separate password URL
			if($isSeparatePassword && !empty($password)) {
				$mail->Subject = "File Sharing Notice #2";
				$mail->Body = "The password is <b>{$password}</b>";

				// add upload group url
				$url = get_base_url() . "/" . $uid;
				$body .= "<p><a href='{$url}'>View file(s)</a></p>";

				$mail->AltBody = "The password is {$password}";

				// send password email
				if(!$mail->send()) {
					$result = [
					'error' => 'There was a problem sending the e-mail. Please try again.',
					'info' => $mail->ErrorInfo
					];
				}
			}
		}

		echo json_encode($result);
	});
});