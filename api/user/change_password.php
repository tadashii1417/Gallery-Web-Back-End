<?php
// required headers
header( "Access-Control-Allow-Origin: *" );
header( "Content-Type: application/json; charset=UTF-8" );
header( "Access-Control-Allow-Methods: POST" );
header( "Access-Control-Max-Age: 3600" );
header( "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With" );

// required to encode json web token
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db       = $database->getConnection();

$user = new User( $db );

$data = json_decode( file_get_contents( "php://input" ) );

// get jwt
$jwt = isset( $data->jwt ) ? $data->jwt : "";

// if jwt is not empty
if ( $jwt ) {
	try {
		$decoded = JWT::decode( $jwt, $key, [ 'HS256' ] );
		$user->id        = $decoded->data->id;
		$user->password = $data->old_password;
		if ( $user->update_password($data->new_password) ) {
			//regenerate jwt
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
					"description" => $user->description,
				],
			];
			$jwt   = JWT::encode( $token, $key );
			http_response_code( 200 );
			echo json_encode(
				[
					"message" => "User was updated.",
					"jwt"     => $jwt,
				]
			);

		} else {
			http_response_code( 401 );
			echo json_encode( [ "message" => "Unable to update user." ] );
		}
	}
	catch ( Exception $e ) {
		http_response_code( 401 );
		echo json_encode( [
			"message" => "Access denied.",
			"error"   => $e->getMessage(),
		] );
	}
} else {
	http_response_code( 401 );
	echo json_encode( [ "message" => "Access denied." ] );
}

