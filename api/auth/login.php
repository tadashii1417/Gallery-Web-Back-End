<?php
// required headers
header( "Access-Control-Allow-Origin: http://localhost/web/backend/" );
header( "Content-Type: application/json; charset=UTF-8" );
header( "Access-Control-Allow-Methods: POST" );
header( "Access-Control-Max-Age: 3600" );
header( "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With" );

include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db       = $database->getConnection();
$user     = new User( $db );

// input
$data = json_decode( file_get_contents( "php://input" ) );

// set product property values
$user->username = $data->username;
$user_exists    = $user->usernameExists();

// files for jwt will be here
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

// generate json web token
echo $user_exists;

if ( $user_exists && password_verify( $data->password, $user->password ) ) {
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
		],
	];

	// set response code
	http_response_code( 200 );

	// generate jwt
	$jwt = JWT::encode( $token, $key );
	echo json_encode(
		[
			"message" => "Successful login.",
			"jwt"     => $jwt,
		]
	);
} else {
	http_response_code( 401 );
	echo json_encode( [ "message" => "Login failed." ] );
}















