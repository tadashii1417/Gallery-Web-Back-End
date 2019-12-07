<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/web/backend/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// required to encode json web token
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;
include_once '../../config/database.php';
include_once '../../objects/collection.php';
// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
$collection = new Collection($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// authenticate
$jwt = $data->jwt;

try {
    $decoded = JWT::decode($jwt, $key, ['HS256']);
} catch(Exception $e) {
    http_response_code(403);
    echo json_encode(array("message" => "Access denied."));
}

// validate data
if (!isset($data->name)) {
    http_response_code(422);
    echo json_encode(array("message" => "Unable to create collection."));
}

$collection->name = $data->name;
$collection->description = isset($data->description) ? $data->description : "";
$collection->user_id= $decoded->data->id;
// TODO: check if username exists.


// create the user
if ($collection->create()) {
    http_response_code(200);
    echo json_encode(array("message" => "Collection was created."));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create collection."));
}
