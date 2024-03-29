<?php

include_once  '../../config/header.php';
// required to decode jwt
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$data = json_decode( file_get_contents( "php://input" ) );
$jwt  = isset( $data->jwt ) ? $data->jwt : "";

if ( $jwt ) {
	try {
		$decoded = JWT::decode( $jwt, $key, [ 'HS256' ] );
		http_response_code( 200 );
		echo json_encode( [
			"message" => "Access granted.",
			"data"    => $decoded->data,
		] );
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