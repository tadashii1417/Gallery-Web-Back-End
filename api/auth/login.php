<?php

include_once  '../../config/header.php';
include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db       = $database->getConnection();
$user     = new User($db);

// input
$data = json_decode(file_get_contents("php://input"));

// set product property values
$user->username = $data->username;
$user_exists    = $user->username_exists();

// files for jwt will be here
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;


if ($user_exists && password_verify($data->password, $user->password)) {
	if ($user->status == "1") {
		$token = [
			"iss"  => $iss,
			"aud"  => $aud,
			"iat"  => $iat,
			"nbf"  => $nbf,
			"data" => [
				"id"        => $user->id,
				"username"  => $user->username,
				"email"     => $user->email,
				"firstname" => $user->firstname,
				"lastname"  => $user->lastname,
				"avatarUrl" => $user->avatarUrl,
				"role" => $user->role,
				"status" => $user->status
			],
		];

		// set response code
		http_response_code(200);

		// generate jwt
		$jwt = JWT::encode($token, $key);
		echo json_encode(
			[
				"message" => "Successful login.",
				"jwt"     => $jwt,
				"user"    => $user
			]
		);
	} else {
		http_response_code(403);
		echo json_encode(["message" => "This account is being banned by admin."]);
	}
} else {
	http_response_code(401);
	echo json_encode(["message" => "Username/Password is not correct !"]);
}
