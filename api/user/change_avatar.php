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

// $data = json_decode( file_get_contents( "php://input" ) );

// get jwt
$jwt = isset( $_POST['jwt'] ) ? $_POST['jwt'] : "";
// if jwt is not empty
if ( $jwt ) {
	try {
		if(isset($_FILES['avatar'])){
			$errors= array();
			$file_name = $_FILES['avatar']['name'];
			$file_size = $_FILES['avatar']['size'];
			$file_tmp = $_FILES['avatar']['tmp_name'];
			$file_type = $_FILES['avatar']['type'];
			$tmp = explode('.',$_FILES['avatar']['name']);
			$file_ext=strtolower(end($tmp));
			
			$extensions= array("jpeg","jpg","png");
			
			if(in_array($file_ext,$extensions)=== false){
			   $errors[]="extension not allowed, please choose a JPEG or PNG file.";
			}
			
			if($file_size > 2097152) {
			   $errors[]='File size must be smaller than 2 MB';
			}
			if(empty($errors)==true) {
				$new_file_path = tempnam('../../upload/', 'avatar_');
				unlink($new_file_path);
			   	move_uploaded_file($file_tmp, $new_file_path);
			}else{
				return(json_encode(["errors" => $errors]));
			}
			$decoded = JWT::decode( $jwt, $key, [ 'HS256' ] );
			$user->id        = $decoded->data->id;
			if ( $user->change_avatar($new_file_path) ) {
				http_response_code( 200 );
				echo json_encode(
					[
						"message" => "User was updated.",
					]
				);
			}
			else {
				http_response_code( 401 );
				echo json_encode( [ "message" => "Unable to update user." ] );
			}
		} else {
			http_response_code(400);
			echo json_encode(['message' => 'missing avatar']);
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

